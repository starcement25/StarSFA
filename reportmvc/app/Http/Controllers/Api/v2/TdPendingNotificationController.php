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


class TdPendingNotificationController extends Controller{
  /**
   * [dydb this function use to connect database on the flay]
   * @param  [varcar] $dbname [database name]
   * @return [object]         [databse connection object]
   */
  public function dydb($dbname){
    $otf = new DbOnTheFly(['database' => $dbname]);
    return $otf;
  }

  public function tdpendingnotificationtxt(Request $request){
      $nick_name=Apicommonfunction::decrypt($request->input('nickname'));
      $emp_code=Apicommonfunction::decrypt($request->emp_code);
      $device_id=Apicommonfunction::decrypt($request->deviceid);
      $verificationcode=Apicommonfunction::decrypt($request->verificationcode);
      $linecontents='';
      $db_name='acedns_'.strtoupper($nick_name);
      $dydb =$this->dydb($db_name);
      $CUTDB = $dydb->getConnection();
      $isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
      if($isverify==1){
        $employeewise_hierarchy=Apicommonfunction::getNameTableMainDb('user_details','employeewise_hierarchy','nick_name',$nick_name);
        if($employeewise_hierarchy=='yes'){
        	$employee_hierarchy=Apicommonfunction::return_employee_hierarchy($db_name,$emp_code);
        	$emp_hierarchy_condition=' AND NAR.receiver_id IN('.$employee_hierarchy.')';
        }
        else{
        	$emp_hierarchy_condition=" AND NAR.receiver_id='".$emp_code."'";
        }
        if($emp_code!='C0007'){
        	$sqlquery=$CUTDB->select("SELECT NAR.notification_id,NM.message,NM.sender_id FROM notification_master NM,notification_ack_relation NAR
        			  WHERE NM.notification_id=NAR.notification_id AND NAR.ack_id='' ".$emp_hierarchy_condition."");
        }
        else{
        	$sqlquery=$CUTDB->select("SELECT NAR.notification_id,NM.message,NM.sender_id FROM notification_master NM,notification_ack_relation NAR
        			  WHERE NM.notification_id=NAR.notification_id AND NAR.ack_id=''");
        }
        $count=count($sqlquery);
        if($count>0){
      		foreach($sqlquery as $rownotification){
      			$contents  = (($rownotification->notification_id!='')?$rownotification->notification_id: ' ')."^";
      			$contents  .= (($rownotification->message!='')?$rownotification->message: ' ')."^";
      			$contents  .= (($rownotification->sender_id!='')?$rownotification->sender_id: ' ');
      			$linecontents  .= $contents."\n";
      		}
      		$datacontents = Apicommonfunction::encrypt(str_replace("\r","",$linecontents));
      	}
      	else{
      		$datacontents = Apicommonfunction::encrypt('0'.'¥'.'0');
      	}
        $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
        $url = url('/api/v1/tdpendingnotification?nick_name='.$nick_name.'&emp_code='.$emp_code);
        Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
        header("Content-type: application/text");
      	header("Content-Disposition: attachment; filename=pending_notification.txt");
      	print "$datacontents";
      }
      else{
        $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
        $url = url('/api/v1/tdpendingnotification');
        Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
        return Apicommonfunction::encrypt('404');
      }

  }


}
