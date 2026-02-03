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

class OutstandingAgainMasterController extends Controller{
  /**
   * [dydb this function use to connect database on the flay]
   * @param  [varcar] $dbname [database name]
   * @return [object]         [databse connection object]
   */
  public function dydb($dbname){
    $otf = new DbOnTheFly(['database' => $dbname]);
    return $otf;
  }

  public function outstandingmasterincremental(Request $request){
      $nick_name=$request->input('nickname');
      $emp_code=$_REQUEST['emp_code'];
      $device_id=$request->device_id;
      $verificationcode=$request->verificationcode;
      $db_name='acedns_'.strtoupper($request->input('nickname'));
      $dydb =$this->dydb($db_name);
      $CUTDB = $dydb->getConnection();
      $isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
      if($isverify==1){
         $employeewise_hierarchy=Apicommonfunction::getNameTableMainDb('user_details','employeewise_hierarchy','nick_name',$nick_name);
         if($employeewise_hierarchy=='yes'){
         	$employee_hierarchy=Apicommonfunction::return_employee_hierarchy($db_name,$emp_code);
         	$emp_hierarchy_condition=' AND CM.emp_code IN('.$employee_hierarchy.')';
         }
         else{
         	$emp_hierarchy_condition=" AND CM.emp_code='".$emp_code."'";
         }
         if($emp_code!='C0007'){
         	$sqlquery=$CUTDB->select("SELECT OA.customer_code,OA.customer_name,OA.outstanding_amount,OA.amount_0_15_days,OA.amount_16_30_days,OA.amount_31_45_days,OA.amount_46_90_days,
         			   OA.amount_greater_90_days FROM outstanding_ageing OA,customer_master CM WHERE OA.customer_code=CM.customer_code");
         }
         else{
         	$sqlquery=$CUTDB->select("SELECT OA.customer_code,OA.customer_name,OA.outstanding_amount,OA.amount_0_15_days,OA.amount_16_30_days,OA.amount_31_45_days,OA.amount_46_90_days,
         			   OA.amount_greater_90_days FROM outstanding_ageing OA,customer_master CM WHERE OA.customer_code=CM.customer_code ".$emp_hierarchy_condition."");
         }
         $count=count($sqlquery);
         $contentsrowcolumn=$count.'¥'.'8';
         	if($count>0){
         		$date=date('Y-m-d');
         		$time=date('H:i:s');
         		$contentsdatetime = $date.'€'.$time."\n";
         		foreach($sqlquery as $rowoutstanding){
         			$contents   = (($rowoutstanding->customer_code!='')?$rowoutstanding->customer_code: ' ')."^";
         			$contents  .= (($rowoutstanding->customer_name!='')?$rowoutstanding->customer_name: ' ')."^";
         			$contents  .= (($rowoutstanding->outstanding_amount!='')?intval($rowoutstanding->outstanding_amount): ' ')."^";
         			$contents  .= (($rowoutstanding->amount_0_15_days!='')?intval($rowoutstanding->amount_0_15_days): ' ')."^";
         			$contents  .= (($rowoutstanding->amount_16_30_days>0)?intval($rowoutstanding->amount_16_30_days): 0)."^";
         			$contents  .= (($rowoutstanding->amount_31_45_days>0)?intval($rowoutstanding->amount_31_45_days): 0)."^";
         			$contents  .= (($rowoutstanding->amount_46_90_days>0)?intval($rowoutstanding->amount_46_90_days): 0)."^";
         			$contents  .= (($rowoutstanding->amount_greater_90_days>0)?intval($rowoutstanding->amount_greater_90_days): 0);

         			$linecontents  .= $contents."\n";
         		}
         		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
         	}
         	else{
         		$datacontents = '0'.'¥'.'0';
         	}
          $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
          $url = url('/api/v1/outstandingageing?nick_name='.$nick_name.'&emp_code='.$emp_code);
          Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
          header("Content-type: application/text");
        	header("Content-Disposition: attachment; filename=outstanding_ageing.txt");
        	print "$datacontents";
      }
      else{
        $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
        $url = url('/api/v1/outstandingageing');
        Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
        return 404;
      }
  }

}
