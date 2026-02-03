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



class EmployeeMasterController extends Controller{

    /**
     * [dydb this function use to connect database on the flay]
     * @param  [varcar] $dbname [database name]
     * @return [object]         [databse connection object]
     */
    public function dydb($dbname){
      $otf = new DbOnTheFly(['database' => $dbname]);
      return $otf;
    }

    public function downloadempmaster(Request $request){
      $nick_name=$request->input('nickname');
      $emp_code=$request->emp_code;
      $last_update_time=$request->last_update_time;
      $last_update_time=str_replace('€',' ',$last_update_time);
      $incremental_download=$request->incremental_download;
      $verificationcode=$request->verificationcode;
      $db_name='acedns_'.strtoupper($request->input('nickname'));
      $dydb =$this->dydb($db_name);
      $CUTDB = $dydb->getConnection();
      $isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
      $branch_code_list='';
      $linecontents='';
      $employeewise_hierarchy=Apicommonfunction::getNameTableMainDb('user_details','email_hierarchywise','nick_name',$nick_name);
      //$sale="yes";
      if($isverify==1){
            if($employeewise_hierarchy=='yes'){
             $employee_hierarchy=Apicommonfunction::return_employee_hierarchy($db_name,$emp_code);
             $emp_hierarchy_condition='emp_code IN('.$employee_hierarchy.')';
            }
            else{
             $emp_hierarchy_condition="emp_code='".$emp_code."'";
            }
            if($incremental_download=='no'){
             $login_condition=" AND acedns!='N'";
            }
            else{
             $login_condition=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
            }

            /*if($sale=='yes'){
            $sqlbranch=$CUTDB->select("SELECT branch_code FROM employee_master WHERE ".$emp_hierarchy_condition."");
            $branc_code_array=array();
            foreach($sqlbranch as $rowbranch)
            {
            	$branch_code=$rowbranch->branch_code;
            	if(!in_array($branch_code,$branc_code_array))
            	{
            		$branch_code_list=$branch_code_list."'".$branch_code."'".',';
            		array_push($branc_code_array,$branch_code);
            	}
            }
            $branch_code_list=substr($branch_code_list,0,-1);

            $sqlquery=$CUTDB->select("SELECT emp_code,emp_name,sale_access,reporting_to,designation,vertical_value,branch_code,state,zone,acedns,lower_leaves FROM employee_master WHERE
            			branch_code IN($branch_code_list) ORDER BY emp_name ASC");
            }
            else*/
            if($nick_name='MAITHAN')
            {
            	$sqlquery=$CUTDB->select("SELECT emp_code,emp_name,sale_access,reporting_to,designation,vertical_value,branch_code,state,zone,acedns,lower_leaves FROM employee_master WHERE 1 ".$login_condition." ORDER BY emp_name ASC");
            }
            else
            {
            $sqlquery=$CUTDB->select("SELECT emp_code,emp_name,sale_access,reporting_to,designation,vertical_value,branch_code,state,zone,acedns,lower_leaves FROM employee_master WHERE
            			".$emp_hierarchy_condition.$login_condition." ORDER BY emp_name ASC");
            }
            $cnt=1;
          	$contentsrowcolumn  =count($sqlquery).'¥'.'12';
          	if(count($sqlquery)>0){
              $date=gmdate('d',strtotime('+330 minute'));
          		$month=gmdate('m',strtotime('+330 minute'));
          		$year=gmdate('Y',strtotime('+330 minute'));

          		$hour=gmdate('H',strtotime('+330 minute'));
          		$minute=gmdate('i',strtotime('+330 minute'));
          		$second=gmdate('s',strtotime('+330 minute'));
          		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

              foreach($sqlquery as $rowemp){
                  $sqlemphierarchy=$CUTDB->select("SELECT emp_code FROM employee_master WHERE FIND_IN_SET('$rowemp->emp_code', reporting_to)");
                  if(count($sqlemphierarchy)>0){
                     $level='2';
                  }
                  else{
                    $level='1';
                  }
                  $contents  = (($rowemp->emp_code!='')?$rowemp->emp_code: ' ')."^";
          				$contents  .= (($rowemp->emp_name!='')?$rowemp->emp_name: ' ')."^";
          				$contents  .= (($rowemp->sale_access!='')?$rowemp->sale_access: ' ')."^";
          				$contents  .= (($rowemp->reporting_to!='')?$rowemp->reporting_to: ' ')."^";
          				$contents  .= (($level!='')?$level: ' ')."^";
          				$contents  .= (($rowemp->designation!='')?$rowemp->designation: ' ')."^";
          				$contents  .= (($rowemp->vertical_value!='')?$rowemp->vertical_value: ' ')."^";
          				$contents  .= (($rowemp->branch_code!='')?$rowemp->branch_code: ' ')."^";
          				$contents  .= (($rowemp->state!='')?$rowemp->state: ' ')."^";
          				$contents  .= (($rowemp->zone!='')?$rowemp->zone: ' ')."^";
          				$contents  .= (($rowemp->acedns!='')?$rowemp->acedns: ' ')."^";
          				$contents  .= (($rowemp->lower_leaves!='')?$rowemp->lower_leaves: ' ');

          				$linecontents  .= $contents."\n";
              }
              $datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
              $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
            	$url =url('/api/v1/empmasaterdownload?nick_name='.$nick_name.'&emp_code='.$emp_code.'&last_update_time='.$last_update_time.'&incremental_download='.$incremental_download);
            	Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url,$nick_name);
              header("Content-type: application/text");
              header("Content-Disposition: attachment; filename=emp_master.txt");
              return $datacontents;
            }
            else{
              $last_update_time=str_replace('?','',$last_update_time);
          		$data_download_time=str_replace('?','',$data_download_time);
          		if(strtotime($data_download_time)>=strtotime($last_update_time)){
          			$datacontents = '0'.'¥'.'0';
          		}
          		else{
          			$datacontents = '0'.'¥'.'12';
          		}
              $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
            	$url= url('/api/v1/empmasaterdownload?nick_name='.$nick_name.'&emp_code='.$emp_code.'&last_update_time='.$last_update_time.'&incremental_download='.$incremental_download);
            	Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url,$nick_name);
              header("Content-type: application/text");
              header("Content-Disposition: attachment; filename=emp_master.txt");
              return $datacontents;
           }

      }
      else{
        return "404";
        $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
      	$url = url('/api/v1/empmasaterdownload?nick_name='.$nick_name.'&emp_code='.$emp_code.'&last_update_time='.$last_update_time.'&incremental_download='.$incremental_download);
      	Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url,$nick_name);
      }




    }







}
