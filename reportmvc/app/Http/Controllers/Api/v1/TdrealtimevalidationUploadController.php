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


class TdrealtimevalidationUploadController extends Controller{

	/**
     * [dydb this function use to connect database on the flay]
     * @param  [varcar] $dbname [database name]
     * @return [object]         [databse connection object]
     */

    public function dydb($dbname){
      $otf = new DbOnTheFly(['database' => $dbname]);
      return $otf;
    }
	/*public function startTag($parser, $data){
        global $current_tag;
        $current_tag .= "*$data";
    }
    public function endTag($parser, $data){
        global $current_tag;
        $tag_key = strrpos($current_tag, '*');
        $current_tag = substr($current_tag, 0, $tag_key);
    }

    public function contents($parser, $data){
		$TD_validation_array=array();
		$TD_validation_details_array=array();

		$counterTDvalidation=0;
		$counterTDvalidationdetails=0;

		$TDval_emp_code="*ROOT*TD_VALIDATION*LOCATION*EMP_CODE";
		$TDval_trans_id = "*ROOT*TD_VALIDATION*LOCATION*TRANS_ID";
		$TDval_latt = "*ROOT*TD_VALIDATION*LOCATION*LATT";
		$TDval_longi = "*ROOT*TD_VALIDATION*LOCATION*LONGI";
		$TDval_date="*ROOT*TD_VALIDATION*LOCATION*DATE";

		$TDvaldetails_sauda_no = "*ROOT*TD_VALIDATION*TD_VALIDATION_DATA*TD_VALIDATION_DETAILS*SAUDA_NO";
		$TDvaldetails_verification_id = "*ROOT*TD_VALIDATION*TD_VALIDATION_DATA*TD_VALIDATION_DETAILS*TD_VERIFICATION_ID";
		$TDvaldetails_customer_code = "*ROOT*TD_VALIDATION*TD_VALIDATION_DATA*TD_VALIDATION_DETAILS*CUSTOMER_CODE";
		$TDvaldetails_prod_code = "*ROOT*TD_VALIDATION*TD_VALIDATION_DATA*TD_VALIDATION_DETAILS*PROD_CODE";
		$TDvaldetails_TD = "*ROOT*TD_VALIDATION*TD_VALIDATION_DATA*TD_VALIDATION_DETAILS*TD";
		$TDvaldetails_status = "*ROOT*TD_VALIDATION*TD_VALIDATION_DATA*TD_VALIDATION_DETAILS*STATUS";
		$TDvaldetails_confirmation_time = "*ROOT*TD_VALIDATION*TD_VALIDATION_DATA*TD_VALIDATION_DETAILS*CONFIRMATION_TIME";

        global $current_tag,$TD_validation_array,$TD_validation_details_array,$TDval_emp_code,$TDval_trans_id,$TDval_latt,$TDval_longi,$TDval_date,$TDvaldetails_sauda_no,$TDvaldetails_verification_id,$TDvaldetails_customer_code,$TDvaldetails_prod_code,$TDvaldetails_TD,$TDvaldetails_status,$TDvaldetails_confirmation_time,$counterTDvalidation,$counterTDvalidationdetail;
        if(substr($current_tag,0,19)=='*ROOT*TD_validation'){
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
          switch($current_tag){
            case $TDval_emp_code:
              $TD_validation_array[$counterTDvalidation] = new xml_TD_validation();
              $TD_validation_array[$counterTDvalidation]->TDval_emp_code = $data;
				break;
			case $TDval_trans_id:
				$TD_validation_array[$counterTDvalidation]->TDval_trans_id = $data;
				break;
			case $TDval_latt:
				$TD_validation_array[$counterTDvalidation]->TDval_latt = $data;
				break;
			case $TDval_longi:
				$TD_validation_array[$counterTDvalidation]->TDval_longi = $data;
				break;
			case $TDval_date:
				$TD_validation_array[$counterTDvalidation]->TDval_date = $data;
				$counterTDvalidation++;
				break;
          }
        }
		if(substr($current_tag,0,60)=='*ROOT*TD_validation*TD_VALIDATION_DATA*TD_VALIDATION_DETAILS')
		{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
			switch($current_tag){
				case $TDvaldetails_sauda_no:
					$TD_validation_details_array[$counterTDvalidationdetails] = new xml_TD_validation_details();
					$TD_validation_details_array[$counterTDvalidationdetails]->TDvaldetails_sauda_no = $data;
					break;
				case $TDvaldetails_verification_id:
					$TD_validation_details_array[$counterTDvalidationdetails]->TDvaldetails_verification_id = $data;
					break;
				case $TDvaldetails_customer_code:
					$TD_validation_details_array[$counterTDvalidationdetails]->TDvaldetails_customer_code = $data;
					break;
				case $TDvaldetails_prod_code:
					$TD_validation_details_array[$counterTDvalidationdetails]->TDvaldetails_prod_code = $data;
					break;
				case $TDvaldetails_TD:
					$TD_validation_details_array[$counterTDvalidationdetails]->TDvaldetails_TD = $data;
					break;
				case $TDvaldetails_status:
					$TD_validation_details_array[$counterTDvalidationdetails]->TDvaldetails_status = $data;
					break;
				case $TDvaldetails_confirmation_time:
					$TD_validation_details_array[$counterTDvalidationdetails]->TDvaldetails_confirmation_time = $data;
					$counterTDvalidationdetails++;
					break;
			}
		}
    }*/

    public function operationdbupdatetdvalidation(Request $request){
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
 								  echo $flag=0;
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

									$sqlupdateTDvalidationdetails=$CUTDB->table('TD_realtime_validation_details')
													   ->where('sauda_no', $sauda_no)
													   ->where('prod_code', $prod_code)
													   ->limit(1)
													   ->update(array('status'=>$status,'TD_verification_id'=>$TD_verification_id,'confirmation_time'=>$confirmation_time));
									if($sqlupdateTDvalidationdetails){
										$flag=5;
								  }
								  else {
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
        $url = url('/api/v1/tdrealtimedataupload?nick_name='.$nick_name.'&emp_code='.$emp_code.'&verificationcode='.$verificationcode);
        Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
    }
}
