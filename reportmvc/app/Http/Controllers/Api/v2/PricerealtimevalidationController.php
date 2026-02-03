<?php
namespace App\Http\Controllers\Api\v2;

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


class PricerealtimevalidationController extends Controller{

      /**
       * [dydb this function use to connect database on the flay]
       * @param  [varcar] $dbname [database name]
       * @return [object]         [databse connection object]
       */
      public function dydb($dbname){
        $otf = new DbOnTheFly(['database' => $dbname]);
        return $otf;
      }

      public function pricerealtimevalidation(Request $request){

        $nick_name=Apicommonfunction::decrypt($request->nickname);
        $emp_code=Apicommonfunction::decrypt($request->emp_code);
        $last_update_time=Apicommonfunction::decrypt($request->last_update_time);
        $last_update_time=str_replace('@@',' ',$last_update_time);
        $linecontents='';

        $db_name='acedns_'.strtoupper($nick_name);
        $dydb =$this->dydb($db_name);
        $CUTDB = $dydb->getConnection();
        $device_id=Apicommonfunction::decrypt($request->deviceid);
        $verificationcode=Apicommonfunction::decrypt($request->verificationcode);
        $isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
        if($isverify==1){
          $sqldatadownload=$CUTDB->select("SELECT * FROM price_validation_details WHERE
  								UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('$last_update_time') AND authorized_emp_code='".$emp_code."'");
          $contentsrowcolumn=count($sqldatadownload).'##'.'16';
          if(count($sqldatadownload)>0){
            $date=date('Y-m-d');
        		$time=date('H:i:s');
            $date=gmdate('d',strtotime('+330 minute'));
  					$month=gmdate('m',strtotime('+330 minute'));
  					$year=gmdate('Y',strtotime('+330 minute'));

  					$hour=gmdate('H',strtotime('+330 minute'));
  					$minute=gmdate('i',strtotime('+330 minute'));
  					$second=gmdate('s',strtotime('+330 minute'));
  					$contentsdatetime=$year.'-'.$month.'-'.$date.'@@'.$hour.':'.$minute.':'.$second."\n";
            //echo "<pre>";print_r($sqldatadownload);
            foreach ($sqldatadownload as $rowsdatadownload) {
              $contents  = (($rowsdatadownload->order_no!='')?$rowsdatadownload->order_no : ' ')."^";
              $contents  .= (($rowsdatadownload->price_verification_id!='')?$rowsdatadownload->price_verification_id : ' ')."^";
              $contents  .= (($rowsdatadownload->customer_code!='')?$rowsdatadownload->customer_code: ' ')."^";
              $contents  .= (($rowsdatadownload->customer_name!='')?$rowsdatadownload->customer_name: ' ')."^";
              $contents  .= (($rowsdatadownload->depot_code!='')?$rowsdatadownload->depot_code: ' ')."^";
              $contents  .= (($rowsdatadownload->depot_name!='')?$rowsdatadownload->depot_name: ' ')."^";
              $contents  .= (($rowsdatadownload->prod_code!='')?$rowsdatadownload->prod_code: ' ')."^";
              $contents  .= (($rowsdatadownload->prod_desc!='')?$rowsdatadownload->prod_desc: ' ')."^";
              $contents  .= (($rowsdatadownload->product_group_code!='')?$rowsdatadownload->product_group_code: ' ')."^";
              $contents  .= (($rowsdatadownload->product_group_name!='')?$rowsdatadownload->product_group_name : '0')."^";
              $contents  .= (($rowsdatadownload->qty!='')?$rowsdatadownload->qty : ' ')."^";
              $contents  .= (($rowsdatadownload->existing_price!='')?$rowsdatadownload->existing_price : ' ')."^";
              $contents  .= (($rowsdatadownload->input_price!='')?$rowsdatadownload->input_price: ' ')."^";
              $contents  .= (($rowsdatadownload->authorized_emp_code!='')?$rowsdatadownload->authorized_emp_code : ' ')."^";
              $contents  .= (($rowsdatadownload->status!='')?$rowsdatadownload->status : ' ')."^";
              $contents  .= (($rowsdatadownload->confirmation_time!="0000-00-00 00:00:00")?$rowsdatadownload->confirmation_time : ' ');
              $linecontents  .= $contents."\n";
            }
            $datacontents = Apicommonfunction::encrypt($contentsrowcolumn."\n".$contentsdatetime.$linecontents);
            $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
            $url = url('/api/v2/pricerealtimedata?nick_name='.$nick_name.'&emp_code='.$emp_code.'&verificationcode='.$verificationcode.'&last_update_time='.$last_update_time);
            Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
            return $datacontents;
          }
          else{
        		$datacontent = '0'.'##'.'0';
            $datacontents = Apicommonfunction::encrypt($datacontent);
            $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
            $url = url('/api/v2/pricerealtimedata?nick_name='.$nick_name.'&emp_code='.$emp_code.'&verificationcode='.$verificationcode.'&last_update_time='.$last_update_time);
            Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
            return $datacontents;
          }
        }
        else{
          $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
          $url = url('/api/v2/pricerealtimedata?nick_name='.$nick_name.'&emp_code='.$emp_code.'&verificationcode='.$verificationcode.'&last_update_time='.$last_update_time);
          Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
          return Apicommonfunction::encrypt("404");

        }

      }







}
