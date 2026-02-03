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


class TransportModeCategoryController extends Controller{
  /**
   * [dydb this function use to connect database on the flay]
   * @param  [varcar] $dbname [database name]
   * @return [object]         [databse connection object]
   */
  public function dydb($dbname){
    $otf = new DbOnTheFly(['database' => $dbname]);
    return $otf;
  }

  public function transportmodecategoryincremental(Request $request){
    $nick_name=$request->input('nickname');
    $last_update_time=$request->last_update_time;
    $last_update_time=str_replace('€',' ',$last_update_time);
    $incremental_download=$request->incremental_download;
    $data_download_time=$request->data_download_time;
    $data_download_time=str_replace('€',' ',$data_download_time);
    $device_id=$request->device_id;
    $verificationcode=$request->verificationcode;
    $db_name='acedns_'.strtoupper($request->input('nickname'));
    $dydb =$this->dydb($db_name);
    $emp_code=$request->emp_code;
    $linecontents='';
    $CUTDB = $dydb->getConnection();
    $isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
    if($isverify==1){
      if($incremental_download=='no'){
      	$login_condition="";
      }
      else{
      	$login_condition=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
      }
      $sqlquery=$CUTDB->select("SELECT * FROM transport_mode_category WHERE 1 ".$login_condition." ORDER BY transport_mode_cat_name ASC");
      $count=count($sqlquery);
      $cnt=1;
      $contentsrowcolumn=$count.'¥'.'2';
      if($count>0){
      		foreach($sqlquery as $rowptransportmodecat){
      			$contents  = (($rowptransportmodecat->transport_mode_cat_id!='')?$rowptransportmodecat->transport_mode_cat_id: ' ')."^";
      			$contents  .= (($rowptransportmodecat->transport_mode_cat_name!='')?$rowptransportmodecat->transport_mode_cat_name: ' ');
      			$linecontents .= $contents."\n";
      		}
      		$datacontents = $contentsrowcolumn."\n".str_replace("\r","",$linecontents);
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
      $url = url('/api/v1/banklistmaster?nick_name='.$nick_name.'&device_id='.$device_id.'&last_update_time='.$last_update_time.'&incremental_download='.$incremental_download);
      Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
      header("Content-type: application/text");
    	header("Content-Disposition: attachment; filename=transport_mode_category.txt");
    	print "$datacontents";
    }
    else{
      $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
      $url = url('/api/v1/banklistmaster');
      Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
      return 404;
    }

  }
}
?>
