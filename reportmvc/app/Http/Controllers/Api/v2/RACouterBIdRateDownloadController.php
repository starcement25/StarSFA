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

class RACouterBIdRateDownloadController extends Controller
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
    public function counterbidratedownload(Request $request){
        $curentdate=date('Ymd');
		$currentdatewindow=date('Y-m-d');
		$nick_name=Apicommonfunction::decrypt($request->nickname);
       	$emp_code=Apicommonfunction::decrypt($request->emp_code);
        $db_name='acedns_'.strtoupper($nick_name);
        $dydb =$this->dydb($db_name);
        $CUTDB = $dydb->getConnection();
		//$verificationcode=Apicommonfunction::decrypt($request->verificationcode);
        //$isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
		$linecontents='';
		//if($isverify==1){
		$timeto_lastwindow='';	
		$sqllastwindowtime=$CUTDB->select("SELECT time_to,time_from FROM RA_windowtime WHERE rate_released_date='".$currentdatewindow."' AND last_window_time='yes'");
		if(count($sqllastwindowtime) >0)
		{
		 foreach ($sqllastwindowtime as $key => $windowtimeval) {			
				$timeto_lastwindow=date('Y-m-d').' '.$windowtimeval->time_to;
			}
		}
		$condition=" AND UNIX_TIMESTAMP(NOW()) > UNIX_TIMESTAMP('".$timeto_lastwindow."')";
	
		$sqlquery=$CUTDB->select("SELECT bid_id,plant_name,prod_code,released_rate,server_indicative_rate,customer_code,qty,bid_rate,
							counter_bid,counter_bid_rate,bid_status,base_rate,app_indicative_rate,GST_percent,GST_value,primary_freight,secondary_freight,depot_cost,branch_code,	incoterms,vertical_value,sms_done FROM RA_bid_rate_details WHERE counter_bid='Y' ANd SUBSTRiNG(bid_id,3,5)='".$emp_code."' AND bid_status='' 
							AND SUBSTRING(bid_id,-14,8)='".$curentdate."'".$condition);
       // echo count($sqlquery);
		if(count($sqlquery)>0 && $timeto_lastwindow!=''){
			$username="emami";
			$password="EAL2017";
			$sender="EMAGRO";
			$contentsrowcolumn  =count($sqlquery).'##'.'21';
			$customer_code_array=array();
			$date=gmdate('d',strtotime('+330 minute'));
      		$month=gmdate('m',strtotime('+330 minute'));
      		$year=gmdate('Y',strtotime('+330 minute'));
      		$hour=gmdate('H',strtotime('+330 minute'));
      		$minute=gmdate('i',strtotime('+330 minute'));
      		$second=gmdate('s',strtotime('+330 minute'));

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

				$customer_code=$rowratedownload->customer_code;
				$sqlselectprodcode=$CUTDB->table('product_master')
							->select('product_master.prod_desc')
							->where('product_master.dns_prod_code', '=' ,$rowratedownload->prod_code)
							->first();
				$prod_desc=$sqlselectprodcode->prod_desc;	

				if(($rowratedownload->sms_done)==0)
				{
					//$customercodereplaced=str_replace('/','',$customer_code);
					if(!in_array($customer_code,$customer_code_array))
					{
						array_push($customer_code_array,$customer_code);
						${'prodstringrate'.$customer_code}='';
					}
	
					${'prodstringrate'.$customer_code}.=$prod_desc."  ".$rowratedownload->counter_bid_rate."\n";
				}
				
				$linecontents  .= $contents."\n";
			}
			$smsstring="Dear Customer,\nThank you for participating in the Emami bidding process:\nFollowing bids you placed today are in counter, with the counter rates:\nSKU                         Counter Bid Rate\n";
			foreach($customer_code_array as $customercodeval)
			{
			  $smsstringfinal=$smsstring.${'prodstringrate'.$customercodeval}."\nPlease contact your ASO to accept or reject the counter bid.\nTeam HBC";
				$sqlcustomerphone=$CUTDB->table('customer_master')
							->select('customer_master.phone_no')
							->where('customer_master.customer_code', '=' ,$customercodeval)
							->first();
				$customer_phone_no=$sqlcustomerphone->phone_no;
				$customer_phone_no='7347604544';
		
				$Url = "http://websms.codez.in:8080/bulksms/bulksms?username=coz1-".$username."&password=".$password."&type=0&dlr=1&source=".$sender."&destination=91".$customer_phone_no."&message=".rawurlencode($smsstringfinal);
			  $ch = curl_init();
			  curl_setopt($ch, CURLOPT_URL, $Url);
			  curl_setopt($ch, CURLOPT_TIMEOUT, 20);
			  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			  //$output = curl_exec($ch);
			  //print_r($output);
			  curl_close($ch);
			  /*$response = explode("|",$output);
			  if(intval($response[0]) == 1701) {
				  $sqlupdate="UPDATE RA_bid_rate_details SET sms_done='1' WHERE customer_code='".$customercodeval."' AND 
								DATE_FORMAT(SUBSTRING(bid_id,-14,8),'%Y%-%m-%d')='".$currentdate."' AND sms_done='0' AND counter_bid='Y'";
				   mysql_query($sqlupdate);
			  }
			  	//For insertion of sms log
				 $sms_date=$year.'-'.$month.'-'.$date;
				 $sms_time=$hour.':'.$minute.':'.$second;
				$CUTDB->table('sms_log')->insert(array(
				  'sms_date' => $sms_date,
				  'customer_code' => $customercodeval,
				  'sms_type' => 'COUNTER BID',
				  'sms_time' => $sms_time,
				  'response' => $output,
			   ));*/
			}
			$datacontents = Apicommonfunction::encrypt($contentsrowcolumn."\n".str_replace("\r","",$linecontents));
            $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
            $url =url('/api/v2/counterbidratedownload?nick_name='.$nick_name.'&emp_code='.$emp_code);
            Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
            return $datacontents;
        }
		else
		{
          	$datacontents = Apicommonfunction::encrypt('0'.'##'.'0');
            $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
            $url= url('/api/v2/counterbidratedownload?nick_name='.$nick_name.'&emp_code='.$emp_code);
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
