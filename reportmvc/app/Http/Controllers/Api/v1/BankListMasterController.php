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


class BankListMasterController extends Controller{
  /**
   * [dydb this function use to connect database on the flay]
   * @param  [varcar] $dbname [database name]
   * @return [object]         [databse connection object]
   */
  public function dydb($dbname){
    $otf = new DbOnTheFly(['database' => $dbname]);
    return $otf;
  }

  public function banklistmasterincremental(Request $request){

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
      if($incremental_download=='no'){
       $login_condition="";
      }
      else{
       $login_condition=" AND UNIX_TIMESTAMP(BM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
      }
      if($emp_code!='C0007'){
       $sqlquery=$CUTDB->select("SELECT BM.* FROM bank_master BM WHERE 1 ".$login_condition." ORDER BY BM.bank_name ASC");
      }
      else{
       $sqlquery=$CUTDB->table('bank_master')
                       ->orderBy('bank_name','ASC')
                       ->get();
      }
      $count=count($sqlquery);
      $cnt=1;
      $contentsrowcolumn  =$count.'¥'.'2';
      if($count>0){
      		$date=gmdate('d',strtotime('+330 minute'));
      		$month=gmdate('m',strtotime('+330 minute'));
      		$year=gmdate('Y',strtotime('+330 minute'));
      		$hour=gmdate('H',strtotime('+330 minute'));
      		$minute=gmdate('i',strtotime('+330 minute'));
      		$second=gmdate('s',strtotime('+330 minute'));
      		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
      		foreach($sqlquery as $rowbank){
      			$contents  = (($rowbank->bank_id!='')?$rowbank->bank_id: ' ')."^";
      			$contents  .= (($rowbank->bank_name!='')?$rowbank->bank_name: ' ');
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
        $url = url('/api/v1/banklistmaster?nick_name='.$nick_name.'&emp_code='.$emp_code.'&device_id='.$device_id.'&last_update_time='.$last_update_time.'&incremental_download='.$incremental_download);
        Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
        header("Content-type: application/text");
      	header("Content-Disposition: attachment; filename=bank_master.txt");
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
