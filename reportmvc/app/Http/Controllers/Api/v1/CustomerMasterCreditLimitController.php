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

class CustomerMasterCreditLimitController extends Controller{
  /**
   * [dydb this function use to connect database on the flay]
   * @param  [varcar] $dbname [database name]
   * @return [object]         [databse connection object]
   */
  public function dydb($dbname){
    $otf = new DbOnTheFly(['database' => $dbname]);
    return $otf;
  }

  public function customermastercreditlimitincremental(Request $request){

    $nick_name=$request->input('nickname');
    $emp_code=$request->emp_code;
    $last_update_time=$request->last_update_time;
    $last_update_time=str_replace('€',' ',$last_update_time);
    $incremental_download=$request->incremental_download;
    $data_download_time=$request->data_download_time;
    $data_download_time=str_replace('€',' ',$data_download_time);
    $device_id=$request->device_id;
    $verificationcode=$request->verificationcode;
    $db_name='acedns_'.strtoupper($request->input('nickname'));
    $dydb =$this->dydb($db_name);
    $CUTDB = $dydb->getConnection();
    $isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
    $vertical_fields=Apicommonfunction::getNameTableMainDb('user_details','vertical_fields','nick_name',$nick_name);
    if($isverify==1){
        $employeewise_hierarchy=Apicommonfunction::getNameTableMainDb('user_details','employeewise_hierarchy','nick_name',$nick_name);
        $vertical_fields=Apicommonfunction::getNameTableMainDb('user_details','vertical_fields','nick_name',$nick_name);
        if($employeewise_hierarchy=='yes'){
          $employee_hierarchy=Apicommonfunction::return_employee_hierarchy($db_name,$emp_code);
          $emp_hierarchy_condition='ERM.emp_code IN('.$employee_hierarchy.')';
        }
        else{
          $emp_hierarchy_condition="ERM.emp_code='".$emp_code."'";
        }

        if($incremental_download=='no'){
          $login_condition=" AND c1.acedns='Y'";
        }
        else{
          $login_condition=" AND UNIX_TIMESTAMP(c1.download_time_credit_limit) > UNIX_TIMESTAMP('".$last_update_time."')";
        }

        $sqlbranches=$CUTDB->table('branch_master')
                      ->select('branch_code')
                      ->get();
        $countbranches=count($sqlbranches);

        if($countbranches>1 && $vertical_fields=='yes' && $emp_code!='C0007'){
            $sqlquery=$CUTDB->select("SELECT DISTINCT c1.customer_code,c1.credit_limit
            FROM customer_master c1,emp_route_relation ERM WHERE ".$emp_hierarchy_condition."
            AND c1.route_code=ERM.route_code AND ERM.acends!='N' ".$login_condition." ORDER BY c1.customer_name ASC");
        }
        else if($countbranches>1 && $vertical_fields=='no' && $emp_code!='C0007'){
         $sqlquery=$CUTDB->select("SELECT DISTINCT c1.customer_code,c1.credit_limit FROM customer_master c1,emp_route_relation ERM
                WHERE ".$emp_hierarchy_condition." AND c1.route_code=ERM.route_code AND ERM.acends!='N' ".$login_condition." OR c1.branch_code=em.branch_code ORDER BY c1.customer_name ASC");
        }
        else if($emp_code=='C0007'){
        $sqlquery=$CUTDB->select("SELECT DISTINCT c1.customer_code,c1.credit_limit FROM customer_master c1 WHERE 1  ".$login_condition." ORDER BY c1.customer_name ASC");
        }
        else{
        $sqlquery=$CUTDB->select("SELECT DISTINCT c1.customer_code,c1.credit_limit FROM customer_master c1,emp_route_relation ERM WHERE ".$emp_hierarchy_condition."
                 AND c1.route_code=ERM.route_code AND ERM.acends!='N' ".$login_condition." ORDER BY c1.customer_name ASC");
        }
        $count=count($sqlquery);
       	$contentsrowcolumn=$count.'¥'.'2';
       	if($count>0){
       		$date=gmdate('d',strtotime('+329 minute'));
       		$month=gmdate('m',strtotime('+329 minute'));
       		$year=gmdate('Y',strtotime('+329 minute'));

       		$hour=gmdate('H',strtotime('+329 minute'));
       		$minute=gmdate('i',strtotime('+329 minute'));
       		$second=gmdate('s',strtotime('+329 minute'));

       		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
       		foreach($sqlquery as $rowsemp){
       			$contents  = (($rowsemp->customer_code!='')?$rowsemp->customer_code: ' ')."^";
       			$contents  .= (($rowsemp->credit_limit!='')?$rowsemp->credit_limit: ' ');
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
       			$datacontents = '0'.'¥'.'2';
       		}
       	}
        $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
        $url = url('/api/v1/customermastercreditlimit?nick_name='.$nick_name.'&emp_code='.$emp_code.'&device_id='.$device_id.'&last_update_time='.$last_update_time.'&incremental_download='.$incremental_download);
        Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
        header("Content-type: application/text");
      	header("Content-Disposition: attachment; filename=customer_master_credit_limit.txt");
      	print "$datacontents";

    }
    else{
      $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
      $url = url('/api/v1/customermastercreditlimit');
      Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
      return 404;
    }

  }

}

?>
