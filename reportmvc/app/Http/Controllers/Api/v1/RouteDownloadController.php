<?php
namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\User;
use App\Http\Requests;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Database\DbOnTheFly;
use App\Helpers\Apicommonfunction;
use Session;
use DB;


class RouteDownloadController extends Controller
{
  /**
   * [dydb this function use to connect database on the flay]
   * @param  [varcar] $dbname [database name]
   * @return [object]         [databse connection object]
   */
  public function dydb($dbname){
    $otf = new DbOnTheFly(['database' => $dbname]);
    return $otf;
  }

  public function routedownloadincremental(Request $request){
      $nick_name=$request->input('nickname');
      $emp_code=$request->emp_code;
      $last_update_time=$request->last_update_time;
      $last_update_time=str_replace('€',' ',$last_update_time);
      $incremental_download=$request->incremental_download;
      $data_download_time=$request->data_download_time;
      $data_download_time=str_replace('€',' ',$data_download_time);
      $verificationcode=$request->verificationcode;
      $db_name='acedns_'.strtoupper($request->input('nickname'));
      $dydb =$this->dydb($db_name);
      $CUTDB = $dydb->getConnection();
      $isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
      if($isverify==1){
          $employeewise_hierarchy=Apicommonfunction::getNameTableMainDb('user_details','employeewise_hierarchy','nick_name',$nick_name);
          $modified_customer_emp_route=Apicommonfunction::getNameTableMainDb('user_details','modified_customer_emp_route','nick_name',$nick_name);
          $distributor_route_planning=Apicommonfunction::getNameTableMainDb('route_plan_details','distributor_route_planning','nick_name',$nick_name);

          if($employeewise_hierarchy=='yes'){
          	$employee_hierarchy=Apicommonfunction::return_employee_hierarchy($db_name,$emp_code);
          	$emp_hierarchy_condition=' AND CM.emp_code IN('.$employee_hierarchy.')';
          	$emp_hierarchy_condition_one=' AND RM.emp_code IN('.$employee_hierarchy.')';
          }
          else{
          	$emp_hierarchy_condition=" AND CM.emp_code='".$emp_code."'";
          	$emp_hierarchy_condition_one=" AND RM.emp_code='".$emp_code."'";
          }
          if($incremental_download=='no'){
          	$login_condition="";
          	$login_condition_one="";
          }
          else{
          	$login_condition=" AND UNIX_TIMESTAMP(CM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
          	$login_condition_one=" AND UNIX_TIMESTAMP(RM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
          }
          
          if($nick_name=='EMAMI' || $nick_name=='EMAMIT'){
          	$sqlmenuaccess=$CUTDB->table('menu_access')
                                 ->select('not_accessible_menu')
                                 ->where('emp_code',$emp_code)
                                 ->get();
          	$not_accessible_menu_array=array();
          	if(count($sqlmenuaccess) >0){
          		foreach($sqlmenuaccess as $rowmenuaccess){
          			array_push($not_accessible_menu_array,$rowmenuaccess->not_accessible_menu);
          		}
          	}
          	if(in_array('order',$not_accessible_menu_array)){
          		$customer_type_condition=" AND CM.cust_type='D'";
          	}
          	else{
          		$customer_type_condition=" AND CM.cust_type IN('R','D')";
          	}
          }
          else{
          	$customer_type_condition='';
          }
          if($modified_customer_emp_route=='yes' && $nick_name!='OSHEA' && $nick_name!='HALDIRAM'){
              $sqlquerycustomerroute=$CUTDB->select("SELECT DISTINCT RM.route_code FROM customer_route_emp_relation RM WHERE
          							route_code IN(SELECT route_code FROM route_master) AND acedns='Y'
          							".$emp_hierarchy_condition_one." ".$login_condition_one."");

              if(count($sqlquerycustomerroute)>0){
            			$date=gmdate('d',strtotime('+330 minute'));
            			$month=gmdate('m',strtotime('+330 minute'));
            			$year=gmdate('Y',strtotime('+330 minute'));

            			$hour=gmdate('H',strtotime('+330 minute'));
            			$minute=gmdate('i',strtotime('+330 minute'));
            			$second=gmdate('s',strtotime('+330 minute'));
            			//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
            			$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
                  foreach($sqlquerycustomerroute as $rowscustomerroute){
                      $route_code=$rowscustomerroute->route_code;
              				$rowroute=$CUTDB->table('route_master')
                                      ->select('route_name')
                                      ->where('route_code',$route_code)
                                      ->first();
              				$dns_route_code='';
              				$route_name=preg_replace('/[\r\n]+/', '',$rowroute->route_name);
              				$contents  = (($route_code!='')?$route_code: ' ')."^";
              				$contents  .= (($route_name!='')?$route_name: ' ')."^";
              				$contents  .= (($route_code!='')?$route_code: ' '); // For dns route code forcefully given the original route code
              				$linecontents  .= $contents."\n";
                  }
                  if($nick_name=='SKIPPER' || $nick_name=='DNV'){
                    $sqlrouteextra=$CUTDB->select("SELECT route_code,route_name FROM route_master WHERE route_name IN('Office Visit','Leave Request')");
            				foreach($sqlrouteextra as $rowrouteextra){
            					$contents  = (($rowrouteextra->route_code!='')?$rowrouteextra->route_code: ' ')."^";
            					$contents  .= (($rowrouteextra->route_name!='')?$rowrouteextra->route_name: ' ')."^";
            					$contents  .= (($rowrouteextra->route_code!='')?$rowrouteextra->route_code: ' '); // For dns route code forcefully given the original route code
            					$linecontents  .= $contents."\n";
            				}
            				$countcustomerroute=$countcustomerroute+2;
                  }
                  $contentsrowcolumn=$countcustomerroute.'¥'.'3';
            			$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
              }
              else{
                  $last_update_time=str_replace('?','',$last_update_time);
            			$data_download_time=str_replace('?','',$data_download_time);
            			if(strtotime($data_download_time)>=strtotime($last_update_time)){
                    $datacontents = '0'.'¥'.'0';
                  }
                  else{
            				$datacontents = '0'.'¥'.'3';
            			}

              }
          }
          else if($nick_name=='OSHEA' || $nick_name=='HALDIRAM'){
            $route_code_array=array();
        		$sqlquerycustomerroute=$CUTDB->select("SELECT DISTINCT RM.route_code FROM customer_route_emp_relation RM WHERE
        							route_code IN(SELECT route_code FROM route_master) AND acedns='Y'
        							".$emp_hierarchy_condition_one." ".$login_condition_one."");
        		$countcustomerroute=count($sqlquerycustomerroute);

        		$sqlquerydistributorroute=$CUTDB->select("SELECT DISTINCT RM.route_code FROM distributor_route_relation RM WHERE 1
        							".$emp_hierarchy_condition_one." ".$login_condition_one."");
        		$countdistributorroute=count($sqlquerydistributorroute);
            if($countcustomerroute>0 || $countdistributorroute >0){
        			$date=gmdate('d',strtotime('+330 minute'));
        			$month=gmdate('m',strtotime('+330 minute'));
        			$year=gmdate('Y',strtotime('+330 minute'));

        			$hour=gmdate('H',strtotime('+330 minute'));
        			$minute=gmdate('i',strtotime('+330 minute'));
        			$second=gmdate('s',strtotime('+330 minute'));

        			$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
        			if($countcustomerroute>0){
          				foreach($sqlquerycustomerroute as $rowscustomerroute){
            					$route_code=$rowscustomerroute->route_code;
            					if(!in_array($route_code,$route_code_array)){
            						array_push($route_code_array,$route_code);
            					}
          				}
        		   }
        			 if($countdistributorroute>0){
        				 foreach($sqlquerydistributorroute as $rowsdistributorroute){
        						$route_code=$rowsdistributorroute->route_code;
        						if(!in_array($route_code,$route_code_array)){
        							array_push($route_code_array,$route_code);
        						}
        				  }
        			  }
        			  $routecount=0;
        			  foreach($route_code_array as $routeval){

          				$rowroute=$CUTDB->table('route_master')
                                       ->select('route_name')
                                       ->where('route_code',$routeval)
                                       ->first();

          				$dns_route_code='';
          				$route_name=preg_replace('/[\r\n]+/', '',$rowroute->route_name);
          				$contents  = (($routeval!='')?$routeval: ' ')."^";
          				$contents  .= (($route_name!='')?$route_name: ' ')."^";
          				$contents  .= (($routeval!='')?$routeval: ' '); // For dns route code forcefully given the original route code
          				$linecontents  .= $contents."\n";
          				$routecount++;
        			  }
          			if($nick_name=='HALDIRAM'){
          				$sqlrouteextra=$CUTDB->select("SELECT  route_code,route_name FROM route_master WHERE route_name IN('Office Visit','Leave Request')");
          				foreach($sqlrouteextra as $rowrouteextra){
          					$contents  = (($rowrouteextra->route_code!='')?$rowrouteextra->route_code: ' ')."^";
          					$contents  .= (($rowrouteextra->route_name!='')?$rowrouteextra->route_name: ' ')."^";
          					$contents  .= (($rowrouteextra->route_code!='')?$rowrouteextra->route_code: ' '); // For dns route code forcefully given the original route code
          					$linecontents  .= $contents."\n";
          				}
          				$routecount=$routecount+2;
          			}
          			$contentsrowcolumn=$routecount.'¥'.'3';
          			$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
        		}
          }
          else{
          	if($distributor_route_planning=='yes'){
          		if($emp_code!='C0007'){
          				$sqlquery=$CUTDB->select("select RM.* from route_master RM WHERE 1 ".$emp_hierarchy_condition_one." ".$login_condition_one." ORDER BY RM.route_name ASC");
          		}
          		else{
          				$sqlquery=$CUTDB->select("select * from route_master  ORDER BY route_name ASC");
          		}
          	}
          	else{
          		if($emp_code!='C0007'){
          				$sqlquery=$CUTDB->select("select RM.* from route_master RM,customer_master CM WHERE RM.route_code=CM.route_code AND CM.acedns='Y'
          							".$emp_hierarchy_condition." ".$login_condition." ".$customer_type_condition." GROUP BY RM.route_code ORDER BY RM.route_name ASC");
          		}
          		else{
          				$sqlquery=$CUTDB->select("select * from route_master  ORDER BY route_name ASC");
          		}
          	}
          	$count=count($sqlquery);
          	$contentsrowcolumn  =$count.'¥'.'3';
          	if($count>0){
          		$date=gmdate('d',strtotime('+330 minute'));
          		$month=gmdate('m',strtotime('+330 minute'));
          		$year=gmdate('Y',strtotime('+330 minute'));
          		$hour=gmdate('H',strtotime('+330 minute'));
          		$minute=gmdate('i',strtotime('+330 minute'));
          		$second=gmdate('s',strtotime('+330 minute'));
          		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
          		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
          		$dns_route_code='';
          		foreach($sqlquery as $rowroute){
          			$route_name=preg_replace('/[\r\n]+/', '',$rowroute->route_name);
          			$contents  = (($rowroute->route_code!='')?$rowroute->route_code: ' ')."^";
          			$contents  .= (($route_name!='')?$route_name: ' ')."^";
          			$contents  .= (($rowroute->route_code!='')?$rowroute->route_code: ' '); // For dns route code forcefully given the original route code
          			$linecontents  .= $contents."\n";
          		}
          		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
          	}
          	else{
          		$last_update_time=str_replace('?','',$last_update_time);
          		$data_download_time=str_replace('?','',$data_download_time);
          		if(strtotime($data_download_time)>=strtotime($last_update_time)){
          			$datacontents = '0'.'¥'.'0';
          		}
          		else{
          			$datacontents = '0'.'¥'.'3';
          		}
          	}
          }
          $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
          $url = url('/api/v1/routedownload?nick_name='.$nick_name.'&emp_code='.$emp_code.'&last_update_time='.$last_update_time.'&data_download_time='.$data_download_time.'&incremental_download='.$incremental_download);
          Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
          header("Content-type: application/text");
        	header("Content-Disposition: attachment; filename=route_master.txt");
        	print "$datacontents";
      }
      else{
        $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
        $url = url('/api/v1/routedownload');
        Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
        return 404;
      }

  }


}
