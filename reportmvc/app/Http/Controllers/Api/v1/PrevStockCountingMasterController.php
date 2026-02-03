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



class PrevStockCountingMasterController extends Controller{
  /**
   * [dydb this function use to connect database on the flay]
   * @param  [varcar] $dbname [database name]
   * @return [object]         [databse connection object]
   */
  public function dydb($dbname){
    $otf = new DbOnTheFly(['database' => $dbname]);
    return $otf;
  }

  public function prevstockcountingmasterincremental(Request $request){

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
    if($isverify==1){
       $employeewise_hierarchy=Apicommonfunction::getNameTableMainDb('user_details','employeewise_hierarchy','nick_name',$nick_name);
       $vertical_fields=Apicommonfunction::getNameTableMainDb('user_details','vertical_fields','nick_name',$nick_name);
       if($employeewise_hierarchy=='yes'){
         $employee_hierarchy=Apicommonfunction::return_employee_hierarchy($db_name,$emp_code);
       	 $emp_hierarchy_condition='CM.emp_code IN('.$employee_hierarchy.')';
       }
       else{
       	 $emp_hierarchy_condition="CM.emp_code='".$emp_code."'";
       }
       if($incremental_download=='no'){
       	$login_condition="";
       }
       else{
       	$login_condition=" AND UNIX_TIMESTAMP(PSCM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
       }
       $sqlbranches=$sqlbranches=$CUTDB->table('branch_master')
                     ->select('branch_code')
                     ->get();
       $countbranches=count($sqlbranches);
       if($countbranches>1 && $vertical_fields=='yes' && $emp_code!='C0007'){
    	 $sqlquery=$CUTDB->select("SELECT PSCM.customer_code,PSCM.product_code,PSCM.visit_1,PSCM.visit_2,PSCM.visit_3
    	 			FROM prev_stock_counting_master PSCM,customer_master CM WHERE ".$emp_hierarchy_condition." ".$login_condition."
    				AND CM.customer_code=PSCM.customer_code");
    	 }
    	 else if($emp_code=='C0007'){
    			$sqlquery=$CUTDB->select("SELECT DISTINCT PSCM.* FROM prev_stock_counting_master PSCM WHERE 1 ".$login_condition."");
    	 }
    	 else{
    	  $sqlquery=$CUTDB->select("SELECT PSCM.customer_code,PSCM.product_code,PSCM.visit_1,PSCM.visit_2,PSCM.visit_3
    	 			FROM prev_stock_counting_master PSCM,customer_master CM WHERE ".$emp_hierarchy_condition." ".$login_condition."
    				AND CM.customer_code=PSCM.customer_code");
    	 }
       $count=count($sqlquery);
       $contentsrowcolumn=$count.'¥'.'3';
     	 if($count>0){
       		$date=date('Y-m-d');
       		$time=date('H:i:s');
       		$contentsdatetime = $date.'€'.$time."\n";
     		  foreach($sqlquery as $rowsemp){
       			$visit_1=$rowsemp->visit_1;
       			$visit_2=$rowsemp->visit_2;
       			$visit_3=$rowsemp->visit_3;
       			$contents  = (($rowsemp->customer_code!='')?$rowsemp->customer_code: ' ')."^";
       			$contents  .= (($rowsemp->product_code!='')?$rowsemp->product_code: ' ')."^";

       			if($visit_1 >0 && $visit_2 >0 && $visit_3 >0){
       				$contents  .=$visit_1.','.$visit_2.','.$visit_3;
       			}
       			else{
       				if($visit_3 >0 && $visit_1==0 && $visit_2==0){
       					$contents  .=$visit_3.','.$visit_1.','.$visit_2;
       				}
       				else if($visit_3 >0 && $visit_1==0 && $visit_2 >0){
       					$contents  .=$visit_2.','.$visit_3.','.$visit_1;
       				}
       			}
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
      $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
      $url = url('/api/v1/prevstockcountingmaster?nick_name='.$nick_name.'&emp_code='.$emp_code.'&device_id='.$device_id.'&last_update_time='.$last_update_time.'&incremental_download='.$incremental_download);
      Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
      header("Content-type: application/text");
      header("Content-Disposition: attachment; filename=prev_stock_counting_master.txt");
      print "$datacontents";

    }
    else{
      $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
      $url = url('/api/v1/prevstockcountingmaster');
      Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
      return 404;
    }

  }

}
