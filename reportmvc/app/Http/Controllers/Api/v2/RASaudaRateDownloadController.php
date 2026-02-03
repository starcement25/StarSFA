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

class RASaudaRateDownloadController extends Controller
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
    public function saudaratedownload(Request $request){
        $nick_name=Apicommonfunction::decrypt($request->nickname);
       	$emp_code=Apicommonfunction::decrypt($request->emp_code);
        $db_name='acedns_'.strtoupper($nick_name);
        $dydb =$this->dydb($db_name);
        $CUTDB = $dydb->getConnection();
		//$verificationcode=Apicommonfunction::decrypt($request->verificationcode);
        //$isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
		  $date=gmdate('d',strtotime('+330 minute'));
		  $month=gmdate('m',strtotime('+330 minute'));
		  $year=gmdate('Y',strtotime('+330 minute'));
		  $hour=gmdate('H',strtotime('+330 minute'));
		  $minute=gmdate('i',strtotime('+330 minute'));
		  $second=gmdate('s',strtotime('+330 minute'));
		  //$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		  $contentsdatetime =$year.'-'.$month.'-'.$date;
		  $linecontents='';
		//if($isverify==1){ 
        $sqlquery=$CUTDB->select("SELECT plant_name,prod_code,release_rate,acedns,base_rate,indicative_rate,GST_percent FROM plant_product_wise_RA_rate WHERE acedns='Y' AND SUBSTRING(download_time,1,10)='".$contentsdatetime."'");
       // echo count($sqlquery);
		if(count($sqlquery)>0){
			$contentsrowcolumn  =count($sqlquery).'##'.'7';
			foreach($sqlquery as $rowratedownload){
				$contents  = (($rowratedownload->plant_name!='')?$rowratedownload->plant_name: ' ')."^";
          	    $contents  .= (($rowratedownload->prod_code!='')?$rowratedownload->prod_code: ' ')."^";
				$contents  .= (($rowratedownload->release_rate!='')?$rowratedownload->release_rate: ' ')."^";
				$contents  .= (($rowratedownload->acedns!='')?$rowratedownload->acedns: ' ')."^";
				$contents  .= (($rowratedownload->base_rate!='')?$rowratedownload->base_rate: ' ')."^";
				$contents  .= (($rowratedownload->indicative_rate!='')?$rowratedownload->indicative_rate: ' ')."^";
				$contents  .= (($rowratedownload->GST_percent!='')?$rowratedownload->GST_percent: ' ');

          		$linecontents  .= $contents."\n";
			}
			$datacontents = Apicommonfunction::encrypt($contentsrowcolumn."\n".str_replace("\r","",$linecontents));
            $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
            $url =url('/api/v2/RAsaudaratedownload?nick_name='.$nick_name.'&emp_code='.$emp_code);
            Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
            return $datacontents;
        }
		else
		{
          	$datacontents = Apicommonfunction::encrypt('0'.'##'.'0');
            $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
            $url= url('/api/v2/RAsaudaratedownload?nick_name='.$nick_name.'&emp_code='.$emp_code);
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
