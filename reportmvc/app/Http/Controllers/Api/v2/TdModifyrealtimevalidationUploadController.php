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


class TdModifyrealtimevalidationUploadController extends Controller{

	/**
     * [dydb this function use to connect database on the flay]
     * @param  [varcar] $dbname [database name]
     * @return [object]         [databse connection object]
     */

    public function dydb($dbname){
      $otf = new DbOnTheFly(['database' => $dbname]);
      return $otf;
    }


    public function operationdbupdatetdvalidation(Request $request){
        $nick_name=Apicommonfunction::decrypt($request->nickname);
	    $emp_code=Apicommonfunction::decrypt($request->emp_code);
        $db_name='acedns_'.strtoupper($nick_name);
        $dydb =$this->dydb($db_name);
        $CUTDB = $dydb->getConnection();
        $flag='';
        $auth3email='';
        $messageauth3='';
        $auth2email='';
        $messageauth2='';
        $TD_verification_id_array=array();
        $device_id=Apicommonfunction::decrypt($request->deviceid);
        $verificationcode=Apicommonfunction::decrypt($request->verificationcode);
		    $isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
        if($isverify==1){
			//$body=file_get_contents('php://input');
					$body=Apicommonfunction::decrypt($request->xmldata);
					$date=gmdate('d',strtotime('+330 minute'));
					$month=gmdate('m',strtotime('+330 minute'));
					$year=gmdate('Y',strtotime('+330 minute'));

					$hour=gmdate('H',strtotime('+330 minute'));
					$minute=gmdate('i',strtotime('+330 minute'));
					$second=gmdate('s',strtotime('+330 minute'));
					$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
					$CUTDB->beginTransaction();
					$xml = simplexml_load_string($body, 'SimpleXMLElement', LIBXML_NOCDATA);
					foreach ($xml as $TD_validation) {
						foreach ($TD_validation->location as $location) {
							$emp_code=$location->emp_code;
							$trans_id=$location->trans_id;
							$latt=$location->latt;
							$longi=$location->longi;
							$date=$location->date;

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
 								  echo $flag=Apicommonfunction::encrypt('fail');
 								  return;
							  }
						   }
						}
						foreach ($TD_validation->TD_validation_data as $TD_validation_data) {
							foreach($TD_validation_data->TD_validation_details as $TD_validation_details){
									$sauda_no=$TD_validation_details->sauda_no;
									$TD_verification_id=$TD_validation_details->TD_verification_id;
									$customer_code=$TD_validation_details->customer_code;
									$prod_code=$TD_validation_details->prod_code;
									$TD=$TD_validation_details->TD;
									$status=$TD_validation_details->status;
									$confirmation_time=$TD_validation_details->confirmation_time;
                  $app_status=$TD_validation_details->app_status;
                  $margin=$TD_validation_details->margin;
                  $remarks=(($TD_validation_details->remarks)?$TD_validation_details->remarks:'');

                  if(strtoupper($status)=='EMAIL'){
                    $diffmartd=$margin-$TD;
                    //echo "sud====".$sauda_no;
                    //echo "prod====".$prod_code;
                    $sqlTDauthcode=$CUTDB->table('TD_realtime_validation_details')
                        ->select('TD_realtime_validation_details.auth_one_limit')
                        ->where('sauda_no', $sauda_no)
                        ->where('prod_code', $prod_code)
                        ->first();
                    if(count($sqlTDauthcode)>0){
                      if($diffmartd<$sqlTDauthcode->auth_one_limit && $diffmartd>=0){
                        $sqlempcode=$CUTDB->table('employee_master')
                            ->select('employee_master.emp_code','employee_master.email')
                              ->where('employee_master.designation', '=' ,'auth2')
                              ->first();
                          $authemail2=$sqlempcode->email;
                          $authorized_emp_code=$sqlempcode->emp_code;
                          $authorized_emp_code_done=$emp_code;
                          $app_status="DECLINE,APPROVE";
                          if(!in_array($TD_verification_id,$TD_verification_id_array)){
                    				array_push($TD_verification_id_array,$TD_verification_id);
                    			}
                      }
                    }
                    if($diffmartd<0){
                      $sqlempcode=$CUTDB->table('employee_master')
                            ->select('employee_master.emp_code','employee_master.email')
                            ->where('employee_master.designation', '=' ,'auth3')
                            ->first();
                      $authemail3=$sqlempcode->email;
                      $authorized_emp_code=$sqlempcode->emp_code;
                      $authorized_emp_code_done=$emp_code;
                      $app_status="DECLINE,APPROVE";
                      if(!in_array($TD_verification_id,$TD_verification_id_array)){
                        array_push($TD_verification_id_array,$TD_verification_id);
                      }
                    }
                  }
                  else{
                    $authorized_emp_code=$emp_code;
                    $authorized_emp_code_done="";
                    $app_status=$app_status;
                  }

					$sqlupdateTDvalidationdetails=$CUTDB->table('TD_realtime_validation_details')
													   ->where('sauda_no', $sauda_no)
													   ->where('prod_code', $prod_code)
													   ->update(array('status'=>$status,'TD_verification_id'=>$TD_verification_id,'confirmation_time'=>$confirmation_time,
                               'authorized_emp_code'=>$authorized_emp_code,'authorized_emp_code_done'=>$authorized_emp_code_done,'app_status'=>$app_status,'remarks'=>$remarks));
									if($sqlupdateTDvalidationdetails){
										$flag=5;
								  }
								  else {
										$CUTDB->rollBack();
										echo $flag=Apicommonfunction::encrypt('fail');
										return;
								  }
							}
						}
					}// End of foreach
					if($flag==5){
						$CUTDB->commit();
						//Email part
            /*$today=date('d M Y');
            $msg_header="<html>
                  <body>Dear Sir,</b><br /><br />Please find the below mentioned sales contract booked at discount on ".$today.".<br /><br /><br />
                  <table border=1 style=background-color:AliceBlue>
                  <tr>
                  <th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial
                  CE'>State</span></strong></th>
                  <th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial
                  CE'>Customer Name</span></strong></th>
                  <th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial
                  CE'>Material Description</span></strong></th>
                  <th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial
                  CE'>Material Qty. (Case)</span></strong></th>
                  <th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial
                  CE'>Margin / CASE</span></strong></th>
                  <th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial
                  CE'>Total Margin</span></strong></th>
                  <th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial
                  CE'>Business Justification</span></strong></th>
                  </tr>";

            foreach($TD_verification_id_array as $TD_verification_id_new){
              $sqlverifications=$CUTDB->table('TD_realtime_validation_details')
                    ->where('TD_realtime_validation_details.TD_verification_id',$TD_verification_id_new)
                    ->get();
              foreach ($sqlverifications as $sqlverification) {
                $statename=$CUTDB->table('customer_master')
                            ->join('state_master', 'customer_master.state_code', '=', 'state_master.dns_state_code')
                            ->where('customer_master.customer_code',$sqlverification->customer_code)
                            ->first();
                $diffmartdemail=$sqlverification->margin-$sqlverification->TD;
                if($diffmartdemail<$sqlverification->auth_one_limit && $diffmartdemail>=0){*/
                  /*$sqlempcode=$CUTDB->table('employee_master')
                          ->select('employee_master.email')
                            ->where('employee_master.designation', '=' ,'auth2')
                            ->first();
                  $auth2email=$sqlempcode->email;*/
                  /*$auth2email=$authemail2;
                  $messageauth2.="<tr>
                              <td style='width:165px;text-align:right;min-height:21px;background-color:white'>
                              <span style='font-family:Arial CE;font-size:10pt'>".$statename->state."</span>&nbsp;</td>
                              <td style='width:165px;text-align:right;min-height:21px;background-color:white'>
                              <span style='font-family:Arial CE;font-size:10pt'>".$sqlverification->customer_name."</span>&nbsp;</td>
                              <td style='width:165px;text-align:right;min-height:21px;background-color:white'>
                              <span style='font-family:Arial CE;font-size:10pt'>".$sqlverification->prod_desc."</span>&nbsp;</td>
                              <td style='width:165px;text-align:right;min-height:21px;background-color:white'>
                              <span style='font-family:Arial CE;font-size:10pt'>".$sqlverification->qty."</span>&nbsp;</td>
                              <td style='width:165px;text-align:right;min-height:21px;background-color:white'>
                              <span style='font-family:Arial CE;font-size:10pt'>".$sqlverification->margin."</span>&nbsp;</td>
                              <td style='width:165px;text-align:right;min-height:21px;background-color:white'>
                              <span style='font-family:Arial CE;font-size:10pt'>".($sqlverification->margin*$sqlverification->qty)."</span>&nbsp;</td>
                              <td style='width:165px;text-align:right;min-height:21px;background-color:white'>
                              <span style='font-family:Arial CE;font-size:10pt'>".$sqlverification->remarks."</span>&nbsp;</td>
                              </tr>";
                  }
                  if($diffmartdemail<0){*/
                    /*$sqlempcode=$CUTDB->table('employee_master')
                            ->select('employee_master.email')
                              ->where('employee_master.designation', '=' ,'auth3')
                              ->first();
                    $auth3email=$sqlempcode->email;*/
                    /*$auth3email=$authemail3;
                    $messageauth3.="<tr>
                                        <td style='width:165px;text-align:right;min-height:21px;background-color:white'>
                                        <span style='font-family:Arial CE;font-size:10pt'>".$statename->state."</span>&nbsp;</td>
                                        <td style='width:165px;text-align:right;min-height:21px;background-color:white'>
                                        <span style='font-family:Arial CE;font-size:10pt'>".$sqlverification->customer_name."</span>&nbsp;</td>
                                        <td style='width:165px;text-align:right;min-height:21px;background-color:white'>
                                        <span style='font-family:Arial CE;font-size:10pt'>".$sqlverification->prod_desc."</span>&nbsp;</td>
                                        <td style='width:165px;text-align:right;min-height:21px;background-color:white'>
                                        <span style='font-family:Arial CE;font-size:10pt'>".$sqlverification->qty."</span>&nbsp;</td>
                                        <td style='width:165px;text-align:right;min-height:21px;background-color:white'>
                                        <span style='font-family:Arial CE;font-size:10pt'>".$sqlverification->margin."</span>&nbsp;</td>
                                        <td style='width:165px;text-align:right;min-height:21px;background-color:white'>
                                        <span style='font-family:Arial CE;font-size:10pt'>".($sqlverification->margin*$sqlverification->qty)."</span>&nbsp;</td>
                                        <td style='width:165px;text-align:right;min-height:21px;background-color:white'>
                                        <span style='font-family:Arial CE;font-size:10pt'>".$sqlverification->remarks."</span>&nbsp;</td>
                                        </tr>";
                  }
              }
            }
            $msg_footer="</table><p>Request you to please accord your approval for the same.</p><p>Thanks & regards,</p><p>Vivek Batra</p>";
            if($auth2email && $messageauth2){
              $to = $auth2email; // note the comma
              // Subject
              $subject = 'EMAIL FOR APPROVAL';
              // Message
              $message=$msg_header.$messageauth2.$msg_footer;
            }
            if($auth3email && $messageauth3){
              $to = $auth3email; // note the comma
              // Subject
              $subject = 'EMAIL FOR APPROVAL';
              // Message
              $message=$msg_header.$messageauth3.$msg_footer;
            }
            // To send HTML mail, the Content-type header must be set
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset=iso-8859-1';

            $headers[] = 'From: acednspro<acedns@acedns.in>';
      		$headers[] = 'Bcc: acedns@coral.in';
            		// Mail it
            		mail($to, $subject, $message, implode("\r\n", $headers));*/
						echo Apicommonfunction::encrypt('success');
					}
				}// End of verify if
				else{
					echo Apicommonfunction::encrypt('404');
				}
		    $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
        $url = url('/api/v2/tdmodifyrealtimedataupload?nick_name='.$nick_name.'&emp_code='.$emp_code.'&verificationcode='.$verificationcode);
        Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
    }
}
