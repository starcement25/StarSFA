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



class TdrealtimevalidationController extends Controller{

    /**
     * [dydb this function use to connect database on the flay]
     * @param  [varcar] $dbname [database name]
     * @return [object]         [databse connection object]
     */
    public function dydb($dbname){
      $otf = new DbOnTheFly(['database' => $dbname]);
      return $otf;
    }

    public function tdrealtimevalidation(Request $request){
      $nick_name=$request->nickname;
      $emp_code=$request->emp_code;
      $last_update_time=$request->last_update_time;
      $last_update_time=str_replace('€',' ',$last_update_time);
      $linecontents='';

      $db_name='acedns_'.strtoupper($request->input('nickname'));
      $dydb =$this->dydb($db_name);
      $CUTDB = $dydb->getConnection();
      $verificationcode=$request->verificationcode;
      $isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
      if($isverify==1){

        $sqldatadownload=$CUTDB->select("SELECT * FROM TD_realtime_validation_details WHERE 
								UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('$last_update_time') AND authorized_emp_code='".$emp_code."'");
        $contentsrowcolumn=count($sqldatadownload).'¥'.'16';
        if(count($sqldatadownload)>0){
          $date=date('Y-m-d');
      		$time=date('H:i:s');
      		$contentsdatetime = $date.'€'.$time."\n";
          //echo "<pre>";print_r($sqldatadownload);
          foreach ($sqldatadownload as $rowsdatadownload) {
            $contents  = (($rowsdatadownload->sauda_no!='')?$rowsdatadownload->sauda_no : ' ')."^";
            $contents  .= (($rowsdatadownload->TD_verification_id!='')?$rowsdatadownload->TD_verification_id : ' ')."^";
            $contents  .= (($rowsdatadownload->customer_code!='')?$rowsdatadownload->customer_code: ' ')."^";
            $contents  .= (($rowsdatadownload->customer_name!='')?$rowsdatadownload->customer_name: ' ')."^";
            $contents  .= (($rowsdatadownload->depot_code!='')?$rowsdatadownload->depot_code: ' ')."^";
            $contents  .= (($rowsdatadownload->depot_name!='')?$rowsdatadownload->depot_name: ' ')."^";
            $contents  .= (($rowsdatadownload->prod_code!='')?$rowsdatadownload->prod_code: ' ')."^";
            $contents  .= (($rowsdatadownload->prod_desc!='')?$rowsdatadownload->prod_desc: ' ')."^";
            $contents  .= (($rowsdatadownload->product_group_code!='')?$rowsdatadownload->product_group_code: ' ')."^";
            $contents  .= (($rowsdatadownload->product_group_name!='')?$rowsdatadownload->product_group_name : '0')."^";
            $contents  .= (($rowsdatadownload->qty!='')?$rowsdatadownload->qty : ' ')."^";
            $contents  .= (($rowsdatadownload->sale_rate!='')?$rowsdatadownload->sale_rate : ' ')."^";
            $contents  .= (($rowsdatadownload->TD!='')?$rowsdatadownload->TD: ' ')."^";
            $contents  .= (($rowsdatadownload->authorized_emp_code!='')?$rowsdatadownload->authorized_emp_code : ' ')."^";
            $contents  .= (($rowsdatadownload->status!='')?$rowsdatadownload->status : ' ')."^";
            $contents  .= (($rowsdatadownload->confirmation_time!="0000-00-00 00:00:00")?$rowsdatadownload->confirmation_time : ' ');
            $linecontents  .= $contents."\n";
          }
          $datacontents = $contentsrowcolumn."\n".$contentsdatetime.$linecontents;
          $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
          $url = url('/api/v1/tdrealtimedata?nick_name='.$nick_name.'&emp_code='.$emp_code.'&verificationcode='.$verificationcode);
          Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
          header("Content-type: application/text");
        	header("Content-Disposition: attachment; filename=TD_realtime_validation_details.txt");
          return $datacontents;
        }
      }
      else{
        $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
        $url = url('/api/v1/tdrealtimedata?nick_name='.$nick_name.'&emp_code='.$emp_code.'&verificationcode='.$verificationcode);
        Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
         return 404;
      }


    }













}
