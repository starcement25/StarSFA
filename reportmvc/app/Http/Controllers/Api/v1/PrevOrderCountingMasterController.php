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

class PrevOrderCountingMasterController extends Controller{
  /**
   * [dydb this function use to connect database on the flay]
   * @param  [varcar] $dbname [database name]
   * @return [object]         [databse connection object]
   */
  public function dydb($dbname){
    $otf = new DbOnTheFly(['database' => $dbname]);
    return $otf;
  }

  public function prevordercountingmasterincremental(Request $request){
      $nick_name=$request->input('nickname');
      $emp_code=$request->emp_code;
      $last_update_time=$request->last_update_time;
      $last_update_time=str_replace('€',' ',$last_update_time);
      $incremental_download=$request->incremental_download;
      $data_download_time=$request->data_download_time;
      $data_download_time=str_replace('€',' ',$data_download_time);
      $device_id=$request->device_id;
      $verificationcode=$request->verificationcode;
      $linecontents='';
      $datacontents='';
      $db_name='acedns_'.strtoupper($request->input('nickname'));
      $dydb =$this->dydb($db_name);
      $CUTDB = $dydb->getConnection();
      $isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
      if($isverify==1){
        $employeewise_hierarchy=Apicommonfunction::getNameTableMainDb('user_details','employeewise_hierarchy','nick_name',$nick_name);
        if($employeewise_hierarchy=='yes'){
         $employee_hierarchy=Apicommonfunction::return_employee_hierarchy($db_name,$emp_code);
         $emp_hierarchy_condition=' (SUBSTRING(order_no,2,5) IN('.$employee_hierarchy.'))';
        }
        else{
         $emp_hierarchy_condition=" SUBSTRING(order_no,2,5)='".$emp_code."'";
        }
        if($incremental_download=='no'){
         $login_condition="";
        }
        else{
         $login_condition=" AND UNIX_TIMESTAMP(POCM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
        }
        $sqlquery=$CUTDB->select("SELECT DISTINCT POCM.customer_code,POCM.product_code FROM prev_order_counting_master POCM,customer_master CM
             WHERE ".$emp_hierarchy_condition." AND CM.customer_code=POCM.customer_code");
        $count=count($sqlquery);
        if($count>0){
           $date=gmdate('d',strtotime('+330 minute'));
           $month=gmdate('m',strtotime('+330 minute'));
           $year=gmdate('Y',strtotime('+330 minute'));

           $hour=gmdate('H',strtotime('+330 minute'));
           $minute=gmdate('i',strtotime('+330 minute'));
           $second=gmdate('s',strtotime('+330 minute'));
           $contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
           $countprevorder=0;
           foreach($sqlquery as $rowsprevorder){
             $customer_code=$rowsprevorder->customer_code;
             $product_code=$rowsprevorder->product_code;
             $contents  = (($customer_code!='')?$customer_code: ' ')."^";
             $contents  .= (($product_code!='')?$product_code: ' ')."^";
             $sqlqueryprevorderdetails=$CUTDB->select("SELECT POCM.visit_qty
                       FROM prev_order_counting_master POCM WHERE  POCM.customer_code='".$customer_code."'
                       AND POCM.product_code='".$product_code."' ORDER BY POCM.visit_date DESC LIMIT 0,3");
             $countprevorderdetails=count($sqlqueryprevorderdetails);
             $visit_qty='';
             foreach($sqlqueryprevorderdetails as $rowsprevorderdetails){
              $visit_qty=$visit_qty.$rowsprevorderdetails->visit_qty.',';
             }
             $visit_qty=substr($visit_qty,0,-1);
             $contents  .= (($visit_qty!='')?$visit_qty: ' ');
             $linecontents  .= $contents."\n";
             if($countprevorderdetails >0){
               $countprevorder++;
             }
           }
           $contentsrowcolumn=$countprevorder.'¥'.'3';
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
         $url = url('/api/v1/prevordercountingmaster?nick_name='.$nick_name.'&emp_code='.$emp_code.'&device_id='.$device_id.'&last_update_time='.$last_update_time.'&incremental_download='.$incremental_download);
         Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
         header("Content-type: application/text");
       	 header("Content-Disposition: attachment; filename=prev_order_counting_master.txt");
       	 print "$datacontents";
      }
      else{
        $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
        $url = url('/api/v1/prevordercountingmaster?nick_name='.$nick_name.'&emp_code='.$emp_code.'&device_id='.$device_id.'&last_update_time='.$last_update_time.'&incremental_download='.$incremental_download);
        Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
        return 404;
      }

  }

}
