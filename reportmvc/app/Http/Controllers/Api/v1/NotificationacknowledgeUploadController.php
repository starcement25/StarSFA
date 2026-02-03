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


class NotificationacknowledgeUploadController extends Controller{

	/**
     * [dydb this function use to connect database on the flay]
     * @param  [varcar] $dbname [database name]
     * @return [object]         [databse connection object]
     */

    public function dydb($dbname){
      $otf = new DbOnTheFly(['database' => $dbname]);
      return $otf;
    }

    public function operationdbnotification(Request $request){
        $nick_name=$request->nickname;
		$emp_code=$request->emp_code;
        $db_name='acedns_'.strtoupper($request->input('nickname'));
        $dydb =$this->dydb($db_name);
        $CUTDB = $dydb->getConnection();
        $flag='';
        $verificationcode=$request->verificationcode;
		$isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
        if($isverify==1){
			//$body=file_get_contents('php://input');
					$body=$request->xmldata;

					$date=gmdate('d',strtotime('+330 minute'));
					$month=gmdate('m',strtotime('+330 minute'));
					$year=gmdate('Y',strtotime('+330 minute'));

					$hour=gmdate('H',strtotime('+330 minute'));
					$minute=gmdate('i',strtotime('+330 minute'));
					$second=gmdate('s',strtotime('+330 minute'));
					$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
					$CUTDB->beginTransaction();
					$xml = simplexml_load_string($body, 'SimpleXMLElement', LIBXML_NOCDATA);
					foreach ($xml as $notification) {
						foreach ($notification->location as $location) {
							$emp_code=$location->emp_code;
							$trans_id=$location->trans_id;
							$latt=$location->latt;
							$longi=$location->longi;
							$date=$location->date;
							$notification_id=$location->notification_id;

							$sqlselectlocation=$CUTDB->table('location')
									->select('location.trans_id')
									  ->where('location.trans_id', '=' ,$trans_id)
									  ->first();
						   if(count($sqlselectlocation) >0){
							   $sqlupdatelocation=$CUTDB->table('location')
												   ->where('trans_id', $trans_id)
												   ->limit(1)
												   ->update(array('emp_code'=>$emp_code,'latt'=>$latt,'longi'=>$longi));
						   }
						   else{
							   $sqlInsertlocation=$CUTDB->table('location')
											->insert(array('emp_code'=>$emp_code,
											'trans_id'=>$trans_id,
											'latt'=>$latt,
											'longi'=>$longi,
											'date'=>$date,
											'updatetime'=>$location_date,
											));
							  if($sqlInsertlocation){
									$flag=5;
							  }
							  else{
									$CUTDB->rollBack();
 								  echo $flag=0;
 								  return;
							  }
							  
							  $sqlupdatenotificationack=$CUTDB->table('notification_ack_relation')
												   ->where('notification_id', $notification_id)
												   ->where('receiver_id', $emp_code)
												   ->limit(1)
												   ->update(array('ack_id'=>$trans_id));
							 if($sqlupdatenotificationack){
									$flag=5;
							  }
							  else{
									$CUTDB->rollBack();
 								  echo $flag=0;
 								  return;
							  }					   
						   }
						}
					}// End of foreach
					if($flag==5){
						$CUTDB->commit();
						echo '1';
					}
				}// End of verify if
				else{
					echo 404;
				}
		    $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
        $url = url('/api/v1/operationdbnotification?nick_name='.$nick_name.'&emp_code='.$emp_code.'&verificationcode='.$verificationcode);
        Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
    }
}
