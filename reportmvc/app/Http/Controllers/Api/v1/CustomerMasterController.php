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

class CustomerMasterController extends Controller
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

  public function customermasterincremental(Request $request){
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
        $vertical_fields=Apicommonfunction::getNameTableMainDb('user_details','vertical_fields','nick_name',$nick_name);
              if($employeewise_hierarchy=='yes'){
              	$employee_hierarchy=Apicommonfunction::return_employee_hierarchy($db_name,$emp_code);
              	$emp_hierarchy_condition='c1.emp_code IN('.$employee_hierarchy.')';
              }
              else{
              	$emp_hierarchy_condition="c1.emp_code='".$emp_code."'";
              }
              if($incremental_download=='no'){
              	$login_condition=" AND c1.acedns='Y'";
              }
              else{
              	$login_condition=" AND UNIX_TIMESTAMP(c1.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
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
                		$customer_type_condition=" AND c1.cust_type='D'";
                	}
                	else{
                		$customer_type_condition=" AND c1.cust_type IN('R','D')";
                	}
              }
              else{
              	$customer_type_condition='';
              }
              $sqlbranches=$CUTDB->table('branch_master')
                                   ->select('branch_code')
                                   ->get();
              $countbranches=count($sqlbranches);
              if($modified_customer_emp_route=='yes'){
            		$sqlquerycustomerroute=$CUTDB->select("SELECT DISTINCT c1.customer_code,c1.route_code,c1.acedns FROM customer_route_emp_relation c1 WHERE
            							".$emp_hierarchy_condition." ".$login_condition." AND customer_code IN(SELECT customer_code FROM customer_master)
            							ORDER BY customer_code ASC,acedns DESC");
                  if(count($sqlquerycustomerroute)>0){
              			$date=gmdate('d',strtotime('+330 minute'));
              			$month=gmdate('m',strtotime('+330 minute'));
              			$year=gmdate('Y',strtotime('+330 minute'));

              			$hour=gmdate('H',strtotime('+330 minute'));
              			$minute=gmdate('i',strtotime('+330 minute'));
              			$second=gmdate('s',strtotime('+330 minute'));
              			//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
              			$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

              			$dns_customer_code='';
              			$customebr_code_array=array();
              			$customer_count=0;
              			foreach($sqlquerycustomerroute as $rowscustomerroute){
              				$customer_code=$rowscustomerroute->customer_code;
              				$route_code=$rowscustomerroute->route_code;
              				$acedns=$rowscustomerroute->acedns;

              				if(!in_array($customer_code,$customebr_code_array)){
              					$rowcustomer=$CUTDB->table('customer_master')
                                             ->select('customer_name','emp_code','current_balance','credit_limit','black_list','acedns',
                                   							'TD','cust_type','rds_tag','sauda_validity_period','address','phone_no','pin','landline_no','owner_name','owner_phone',
                                   							'cust_class','weekly_closing_day','coverage_type','TIN','PAN','minimum_stock','branch_code','visit_day','email')
                                             ->where('customer_code',$customer_code)
                                             ->first();
              					$contents  = (($customer_code!='')?$customer_code: ' ')."^";
              					$contents  .= (($rowcustomer->customer_name!='')?trim(preg_replace('/[\r\n]+/', '',$rowcustomer->customer_name)): ' ')."^";
              					$contents  .= (($route_code!='')?$route_code: ' ')."^";
              					$contents  .= (($rowcustomer->emp_code!='')?$rowcustomer->emp_code: ' ')."^";
              					$contents  .= (($rowcustomer->current_balance!='')?$rowcustomer->current_balance: ' ')."^";
              					$contents  .= (($rowcustomer->credit_limit !='')?$rowcustomer->credit_limit: ' ')."^";
              					$contents  .= (($acedns!='')?$acedns: ' ')."^";
              					$contents  .= (($rowcustomer->black_list!='')?$rowcustomer->black_list: ' ')."^";
              					$contents  .= (($rowcustomer->TD!='')?$rowcustomer->TD: '0')."^";
              					$contents  .= (($rowcustomer->cust_type!='')?$rowcustomer->cust_type: ' ')."^";
              					$contents  .= (($rowcustomer->rds_tag!='')?$rowcustomer->rds_tag: ' ')."^";
              					$contents  .= (($rowcustomer->sauda_validity_period!='')?$rowcustomer->sauda_validity_period: ' ')."^";
              					$contents  .= (($rowcustomer->address!='')?trim(preg_replace('/[\r\n]+/', '',$rowcustomer->address)): ' ')."^";
              					$contents  .= (($rowcustomer->pin!='')?$rowcustomer->pin: ' ')."^";
              					$contents  .= (($rowcustomer->phone_no!='')?$rowcustomer->phone_no: ' ')."^";
              					$contents  .= (($customer_code!='')?$customer_code: ' ')."^";// For dns customer code forcefully given the original customer code
              					$contents  .= (($rowcustomer->landline_no!='')?$rowcustomer->landline_no: ' ')."^";
              					$contents  .= (($rowcustomer->owner_name!='')?$rowcustomer->owner_name: ' ')."^";
              					$contents  .= (($rowcustomer->owner_phone!='')?$rowcustomer->owner_phone: ' ')."^";
              					$contents  .= (($rowcustomer->cust_class!='')?$rowcustomer->cust_class: ' ')."^";
              					$contents  .= (($rowcustomer->weekly_closing_day!='')?$rowcustomer->weekly_closing_day: ' ')."^";
              					$contents  .= (($rowcustomer->coverage_type!='')?$rowcustomer->coverage_type: ' ')."^";
              					$contents  .= (($rowcustomer->TIN!='')?$rowcustomer->TIN: ' ')."^";
              					$contents  .= (($rowcustomer->PAN!='')?$rowcustomer->PAN: ' ')."^";
              					$contents  .= (($rowcustomer->minimum_stock!='')?$rowcustomer->minimum_stock: ' ')."^";
              					$contents  .= (($rowcustomer->branch_code!='')?$rowcustomer->branch_code: ' ')."^";
              					$contents  .= (($rowcustomer->visit_day!='')?$rowcustomer->visit_day: ' ')."^";
              					$contents  .= (($rowcustomer->email!='')?$rowcustomer->email: ' ');
              					$linecontents  .= $contents."\n";
              					array_push($customebr_code_array,$customer_code);
              					$customer_count++;
              				}
              			}
              			$contentsrowcolumn=$customer_count.'¥'.'28';
              			$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
              		}
                  else{
              			$last_update_time=str_replace('?','',$last_update_time);
              			$data_download_time=str_replace('?','',$data_download_time);
              			if(strtotime($data_download_time)>=strtotime($last_update_time)){
              				$datacontents = '0'.'¥'.'0';
              			}
              			else{
              				$datacontents = '0'.'¥'.'28';
              			}
              		}
               }
               else{
                   if($nick_name=='SELVEL'){
              			 $sqlquery=$CUTDB->select("SELECT DISTINCT c1.* FROM customer_master c1 WHERE 1  ".$login_condition." ORDER BY c1.customer_name ASC");
              		 }
                   else{
                       if($countbranches>1 && $vertical_fields=='yes' && $emp_code!='C0007'){
                			   $sqlquery=$CUTDB->select("SELECT DISTINCT c1.customer_code, c1.customer_name, c1.route_code,c1.emp_code,c1.current_balance,c1.credit_limit,c1.black_list,c1.acedns,
                						c1.TD,c1.cust_type,c1.rds_tag,c1.sauda_validity_period,c1.address,c1.phone_no,c1.pin,c1.landline_no,c1.owner_name,c1.owner_phone,
                						 c1.cust_class,c1.weekly_closing_day,c1.coverage_type,c1.TIN,c1.PAN,c1.minimum_stock,c1.branch_code,c1.visit_day,c1.email
                						 FROM customer_master c1 WHERE ".$emp_hierarchy_condition." ".$login_condition." ".$customer_type_condition."
                						 ORDER BY c1.customer_name ASC");
                			 }
                       else if($emp_code=='C0007'){
                         $sqlquery=$CUTDB->select("SELECT DISTINCT c1.* FROM customer_master c1 WHERE 1  ".$login_condition." ORDER BY c1.customer_name ASC");
                       }
                       else{
                         $sqlquery=$CUTDB->select("SELECT DISTINCT c1.customer_code, c1.customer_name, c1.route_code,c1.emp_code,c1.current_balance,c1.credit_limit,c1.black_list,c1.acedns,
                 						   c1.TD,c1.cust_type,c1.rds_tag,c1.sauda_validity_period,c1.address,c1.phone_no,c1.pin,c1.landline_no,c1.owner_name,c1.owner_phone,
                 						 c1.cust_class,c1.weekly_closing_day,c1.coverage_type,c1.TIN,c1.PAN,c1.minimum_stock,c1.branch_code,c1.visit_day,c1.email FROM customer_master c1 WHERE ".$emp_hierarchy_condition." ".$login_condition." ".$customer_type_condition." ORDER BY c1.customer_name ASC");
                       }
                   }

                   $count=count($sqlquery);
                   $contentsrowcolumn=$count.'¥'.'28';
                      if($count>0){
                     		$date=gmdate('d',strtotime('+330 minute'));
                     		$month=gmdate('m',strtotime('+330 minute'));
                     		$year=gmdate('Y',strtotime('+330 minute'));

                     		$hour=gmdate('H',strtotime('+330 minute'));
                     		$minute=gmdate('i',strtotime('+330 minute'));
                     		$second=gmdate('s',strtotime('+330 minute'));
                     		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
                     		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

                     		$dns_customer_code='';
                     		foreach($sqlquery as $rowsemp){
                     			$contents  = (($rowsemp->customer_code!='')?$rowsemp->customer_code: ' ')."^";
                     			$contents  .= (($rowsemp->customer_name!='')?trim(preg_replace('/[\r\n]+/', '',$rowsemp->customer_name)): ' ')."^";
                     			$contents  .= (($rowsemp->route_code!='')?$rowsemp->route_code: ' ')."^";
                     			$contents  .= (($rowsemp->emp_code!='')?$rowsemp->emp_code: ' ')."^";
                     			$contents  .= (($rowsemp->current_balance!='')?$rowsemp->current_balance: ' ')."^";
                     			$contents  .= (($rowsemp->credit_limit!='')?$rowsemp->credit_limit: ' ')."^";
                     			$contents  .= (($rowsemp->acedns!='')?$rowsemp->acedns: ' ')."^";
                     			$contents  .= (($rowsemp->black_list!='')?$rowsemp->black_list: ' ')."^";
                     			$contents  .= (($rowsemp->TD!='')?$rowsemp->TD: '0')."^";
                     			$contents  .= (($rowsemp->cust_type!='')?$rowsemp->cust_type: ' ')."^";
                     			$contents  .= (($rowsemp->rds_tag!='')?$rowsemp->rds_tag: ' ')."^";
                     			$contents  .= (($rowsemp->sauda_validity_period!='')?$rowsemp->sauda_validity_period: ' ')."^";
                     			$contents  .= (($rowsemp->address!='')?trim(preg_replace('/[\r\n]+/', '',$rowsemp->address)): ' ')."^";
                     			$contents  .= (($rowsemp->pin!='')?$rowsemp->pin: ' ')."^";
                     			$contents  .= (($rowsemp->phone_no!='')?$rowsemp->phone_no: ' ')."^";
                     			$contents  .= (($rowsemp->customer_code!='')?$rowsemp->customer_code: ' ')."^";// For dns customer code forcefully given the original customer code
                     			$contents  .= (($rowsemp->landline_no!='')?$rowsemp->landline_no: ' ')."^";
                     			$contents  .= (($rowsemp->owner_name!='')?$rowsemp->owner_name: ' ')."^";
                     			$contents  .= (($rowsemp->owner_phone!='')?$rowsemp->owner_phone: ' ')."^";
                     			$contents  .= (($rowsemp->cust_class!='')?$rowsemp->cust_class: ' ')."^";
                     			$contents  .= (($rowsemp->weekly_closing_day!='')?$rowsemp->weekly_closing_day: ' ')."^";
                     			$contents  .= (($rowsemp->coverage_type!='')?$rowsemp->coverage_type: ' ')."^";
                     			$contents  .= (($rowsemp->TIN!='')?$rowsemp->TIN: ' ')."^";
                     			$contents  .= (($rowsemp->PAN!='')?$rowsemp->PAN: ' ')."^";
                     			$contents  .= (($rowsemp->minimum_stock!='')?$rowsemp->minimum_stock: ' ')."^";
                     			$contents  .= (($rowsemp->branch_code!='')?$rowsemp->branch_code: ' ')."^";
                     			$contents  .= (($rowsemp->visit_day!='')?$rowsemp->visit_day: ' ')."^";
                     			$contents  .= (($rowsemp->email!='')?$rowsemp->email: ' ');
                     			$linecontents  .= $contents."\n";
                     		 }
                   		   $datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
                         $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
                         $url = url('/api/v1/customermasteraudit?nick_name='.$nick_name.'&emp_code='.$emp_code.'&last_update_time='.$last_update_time.'&data_download_time='.$data_download_time.'&incremental_download='.$incremental_download);
                         Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
                         header("Content-type: application/text");
                       	 header("Content-Disposition: attachment; filename=customer_master.txt");
                       	 print "$datacontents";
                      }
                 	    else{
                     		$last_update_time=str_replace('?','',$last_update_time);
                     		$data_download_time=str_replace('?','',$data_download_time);
                     		if(strtotime($data_download_time)>=strtotime($last_update_time)){
                     			$datacontents = '0'.'¥'.'0';
                     		}
                     		else{
                     			$datacontents = '0'.'¥'.'28';
                     		}
                        $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
                        $url = url('/api/v1/customermasteraudit?nick_name='.$nick_name.'&emp_code='.$emp_code.'&last_update_time='.$last_update_time.'&data_download_time='.$data_download_time.'&incremental_download='.$incremental_download);
                        Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
                        header("Content-type: application/text");
                        header("Content-Disposition: attachment; filename=customer_master.txt");
                        print "$datacontents";
                     	}
                   }

               }
               else{
                 $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
                 $url = url('/api/v1/customermasteraudit');
                 Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
                 return 404;
               }

    }

}
