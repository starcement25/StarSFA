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

class RARejectBIdRateDownloadController extends Controller
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
    public function rejectbidratedownload(Request $request){
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
				$timeto_lastwindow=date('Y-m-d').' '.$windowtimeval->time_from;
			}
		}
		$condition=" AND UNIX_TIMESTAMP(NOW()) > UNIX_TIMESTAMP('".$timeto_lastwindow."' + INTERVAL 60 MINUTE)";
	
        $sqlquery=$CUTDB->select("SELECT bid_id,plant_name,prod_code,released_rate,server_indicative_rate,customer_code,qty,bid_rate,
							counter_bid,counter_bid_rate,bid_status,base_rate,app_indicative_rate,GST_percent,GST_value,primary_freight,secondary_freight,depot_cost,branch_code,	incoterms,vertical_value FROM RA_bid_rate_details WHERE SUBSTRiNG(bid_id,3,5)='".$emp_code."' AND bid_status IN('REJECT') 
							AND SUBSTRING(bid_id,-14,8)='".$curentdate."'".$condition);
       // echo count($sqlquery);
		if(count($sqlquery)>0 && $timeto_lastwindow!=''){
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
			}
			$datacontents = Apicommonfunction::encrypt($contentsrowcolumn."\n".str_replace("\r","",$linecontents));
            $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
            $url =url('/api/v2/rejectbidratedownload?nick_name='.$nick_name.'&emp_code='.$emp_code);
            Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
            return $datacontents;
        }
		else
		{
          	$datacontents = Apicommonfunction::encrypt('0'.'##'.'0');
            $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
            $url= url('/api/v2/rejectbidratedownload?nick_name='.$nick_name.'&emp_code='.$emp_code);
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
