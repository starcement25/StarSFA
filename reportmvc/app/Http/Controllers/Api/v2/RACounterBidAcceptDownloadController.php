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

class RACounterBidAcceptDownloadController extends Controller
{
    /**
     * [dydb this function use to connect database on the flay]
     * @param  [varcar] $dbname [database name]
     * @return [object]         [databse connection object]
     */
    public function dydb($dbname){
      $otf = new DbOnTheFly(['database' => $dbname]);
      return $otf;
    }
    public function counterbidacceptdownload(Request $request){
        $curentdate=date('Ymd');
		
		$nick_name=Apicommonfunction::decrypt($request->nickname);
       	$emp_code=Apicommonfunction::decrypt($request->emp_code);
	    $last_update_time=Apicommonfunction::decrypt($request->last_update_time);
    	$last_update_time=str_replace('€',' ',$last_update_time);
		//$final_downlod_time=date('Y-m-d H:i:s',strtotime('+15 minutes',));
        $db_name='acedns_'.strtoupper($nick_name);
        $dydb =$this->dydb($db_name);
        $CUTDB = $dydb->getConnection();
		//$verificationcode=Apicommonfunction::decrypt($request->verificationcode);
        //$isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
		$linecontents='';
		//if($isverify==1){
		/*$login_condition=" AND UNIX_TIMESTAMP(DATE_FORMAT(SUBSTRING(RAD.counter_bid_id,-14,14),'%Y%-%m-%d %H:%i:%s')) > UNIX_TIMESTAMP('".$last_update_time."') 
						 AND UNIX_TIMESTAMP(NOW()) > UNIX_TIMESTAMP(DATE_FORMAT(SUBSTRING(RAD.counter_bid_id,-14,14),'%Y%-%m-%d %H:%i:%s') + INTERVAL 15 MINUTE)";*/
		$login_condition="  AND UNIX_TIMESTAMP(NOW()) > UNIX_TIMESTAMP(DATE_FORMAT(SUBSTRING(RAD.counter_bid_id,-14,14),'%Y%-%m-%d %H:%i:%s') + INTERVAL 15 MINUTE)";				 
        
		$sqlquery=$CUTDB->select("SELECT DISTINCT RAD.bid_id,RAD.plant_name,RAD.prod_code,RAD.released_rate,RAD.server_indicative_rate,RAD.customer_code,RAD.qty,RAD.bid_rate,
							RAD.counter_bid,RAD.counter_bid_rate,RAD.bid_status,RAD.base_rate,RAD.app_indicative_rate,RAD.GST_percent,RAD.GST_value,RAD.primary_freight,RAD.secondary_freight,RAD.depot_cost,RAD.branch_code,RAD.incoterms,RAD.vertical_value,CM.customer_name,PM.prod_desc,CM.phone_no,RAD.sms_done FROM RA_bid_rate_details RAD,customer_master CM,product_master PM WHERE SUBSTRiNG(RAD.bid_id,3,5)='".$emp_code."' AND RAD.bid_status='ACCEPT' AND RAD.counter_bid_id!='' AND RAD.prod_code=PM.dns_prod_code AND RAD.customer_code=CM.customer_code AND SUBSTRING(RAD.counter_bid_id,-14,8)='".$curentdate."' ".$login_condition);
       // echo count($sqlquery);
		if(count($sqlquery)>0){
      		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			$username="emami";
			$password="EAL2017";
			$sender="EMAGRO";
			$date=gmdate('d',strtotime('+330 minute'));
      		$month=gmdate('m',strtotime('+330 minute'));
      		$year=gmdate('Y',strtotime('+330 minute'));
      		$hour=gmdate('H',strtotime('+330 minute'));
      		$minute=gmdate('i',strtotime('+330 minute'));
      		$second=gmdate('s',strtotime('+330 minute'));
      		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
			$contentsrowcolumn  =count($sqlquery).'##'.'21';
			foreach($sqlquery as $rowratedownload){
				$contents  = (($rowratedownload->bid_id!='')?$rowratedownload->bid_id: ' ')."^";
          	    $contents  .= (($rowratedownload->plant_name!='')?$rowratedownload->plant_name: ' ')."^";
				$contents  .= (($rowratedownload->prod_code!='')?$rowratedownload->prod_code: ' ')."^";
				$contents  .= (($rowratedownload->released_rate!='')?$rowratedownload->released_rate: ' ')."^";
				$contents  .= (($rowratedownload->base_rate!='')?$rowratedownload->base_rate: ' ')."^";
				$contents  .= (($rowratedownload->server_indicative_rate!='')?$rowratedownload->server_indicative_rate: ' ')."^";
				$contents  .= (($rowratedownload->app_indicative_rate!='')?$rowratedownload->app_indicative_rate: ' ')."^";
				$contents  .= (($rowratedownload->customer_code!='')?$rowratedownload->customer_code: ' ')."^";
				$contents  .= (($rowratedownload->qty!='')?$rowratedownload->qty: ' ')."^";
				$contents  .= (($rowratedownload->bid_rate!='')?$rowratedownload->bid_rate: ' ')."^";
				$contents  .= (($rowratedownload->counter_bid!='')?$rowratedownload->counter_bid: ' ')."^";
				$contents  .= (($rowratedownload->counter_bid_rate!='')?$rowratedownload->counter_bid_rate: ' ')."^";
				$contents  .= (($rowratedownload->bid_status!='')?$rowratedownload->bid_status: ' ')."^";
				$contents  .= (($rowratedownload->primary_freight!='')?$rowratedownload->primary_freight: ' ')."^";
				$contents  .= (($rowratedownload->secondary_freight!='')?$rowratedownload->secondary_freight: ' ')."^";
				$contents  .= (($rowratedownload->depot_cost!='')?$rowratedownload->depot_cost: ' ')."^";
				$contents  .= (($rowratedownload->GST_percent!='')?$rowratedownload->GST_percent: ' ')."^";
				$contents  .= (($rowratedownload->GST_value!='')?$rowratedownload->GST_value: ' ')."^";
				$contents  .= (($rowratedownload->branch_code!='')?$rowratedownload->branch_code: ' ')."^";
				$contents  .= (($rowratedownload->incoterms!='')?$rowratedownload->incoterms: ' ')."^";
				$contents  .= (($rowratedownload->vertical_value!='')?$rowratedownload->vertical_value: ' ');

          		$linecontents  .= $contents."\n";
			  $bid_id=$rowratedownload->bid_id;
			  $smsstring='';
			  if(substr($bid_id,-14,8)==date('Ymd')){
			  $bid_rate=$rowratedownload->bid_rate;
			  $qty=$rowratedownload->qty;
			  $prod_desc=$rowratedownload->prod_desc;
			  $customer_name=$rowratedownload->customer_name;
			  $customer_phone_no=$rowratedownload->phone_no;
			  $sms_done=$rowratedownload->sms_done;
			  $customer_phone_no='7347604544';
			  /*if($sms_done==0){
			  //$customer_phone_no='9874450813';
			  $bid_time=substr($bid_id,-6,2).':'.substr($bid_id,-4,2).':'.substr($bid_id,-2,2);
			  //$smsstring="Dear Customer,\n\nYour Bid @$bid_time of $prod_desc $qty $bid_rate is Accepted.\n\nRegards\nTeam Himani Best Choice";
			  $skustring="SKU-Rate\n$prod_desc-$rowratedownload->counter_bid_rate";
$smsstring="Dear Customer,\nThank you for participating in the Emami bidding process\nCongratulations! Your bid has been accepted\n".$skustring."\nTeam HBC";
			  
			  $Url = "http://websms.codez.in:8080/bulksms/bulksms?username=coz1-".$username."&password=".$password."&type=0&dlr=1&source=".$sender."&destination=91".$customer_phone_no."&message=".rawurlencode($smsstring);
			  $ch = curl_init();
			  curl_setopt($ch, CURLOPT_URL, $Url);
			  curl_setopt($ch, CURLOPT_TIMEOUT, 20);
			  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			  $output = curl_exec($ch);
			  //print_r($output);
			  curl_close($ch);
			  $response = explode("|",$output);
				if(intval($response[0]) == 1701) {
				$sqlupdate=$CUTDB->table('RA_bid_rate_details')
							   ->where('bid_id', $bid_id)
								->where('prod_code', $rowratedownload->prod_code)
								->where("sms_done", '0')
								->where("bid_status", 'ACCEPT')
							   ->limit(1)
							   ->update(array('sms_done'=>'1'));
				}
			  //For insertion of sms log
			     $sms_date=$year.'-'.$month.'-'.$date;
			     $sms_time=$hour.':'.$minute.':'.$second;
				$CUTDB->table('sms_log')->insert(array(
				  'sms_date' => $sms_date,
				  'customer_code' => $rowratedownload->customer_code,
				  'sms_type' => 'ACCEPT BID',
				  'sms_time' => $sms_time,
				  'sms_body' => $smsstring,
				  'response' => $output,
			   ));
			  }*/
			  
			 }
			}
			$datacontents = Apicommonfunction::encrypt($contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents));
            $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
            $url =url('/api/v2/counterbidacceptdownload?nick_name='.$nick_name.'&emp_code='.$emp_code.'&last_update_time='.$last_update_time);
            Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
            return $datacontents;
        }
		else
		{
          	$datacontents = Apicommonfunction::encrypt('0'.'##'.'0');
            $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
            $url= url('/api/v2/counterbidacceptdownload?nick_name='.$nick_name.'&emp_code='.$emp_code.'&last_update_time='.$last_update_time);
            Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
            return $datacontents;
		}
    //}
	/*else
	{
		$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
        $url = url('/api/v2/RAsaudaratedownload?nick_name='.$nick_name.'&emp_code='.$emp_code);
        Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
        return Apicommonfunction::encrypt('404');
	}*/
  }
}
