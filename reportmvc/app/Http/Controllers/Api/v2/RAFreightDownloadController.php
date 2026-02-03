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

class RAFreightDownloadController extends Controller
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
    public function freightdownload(Request $request){
        $curentdate=date('Ymd');
		
		$nick_name=Apicommonfunction::decrypt($request->nickname);
       	$emp_code=Apicommonfunction::decrypt($request->emp_code);
        $db_name='acedns_'.strtoupper($nick_name);
        $dydb =$this->dydb($db_name);
        $CUTDB = $dydb->getConnection();
		//$verificationcode=Apicommonfunction::decrypt($request->verificationcode);
        //$isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
		$linecontents='';
		//if($isverify==1){
        $sqlquery=$CUTDB->select("SELECT * FROM RA_route_freight WHERE acedns='Y' AND vertical_value='HBC:Rasoi:BIB'");
       // echo count($sqlquery);
		if(count($sqlquery)>0){
			$contentsrowcolumn  =count($sqlquery).'##'.'7';
			foreach($sqlquery as $rowfreightdownload){
				$contents  = (($rowfreightdownload->plant_name!='')?$rowfreightdownload->plant_name: ' ')."^";
          	    $contents  .= (($rowfreightdownload->route_code!='')?$rowfreightdownload->route_code: ' ')."^";
				$contents  .= (($rowfreightdownload->zone!='')?$rowfreightdownload->zone: ' ')."^";
				$contents  .= (($rowfreightdownload->transport_mode!='')?$rowfreightdownload->transport_mode: ' ')."^";
				$contents  .= (($rowfreightdownload->capacity!='')?$rowfreightdownload->capacity: ' ')."^";
				$contents  .= (($rowfreightdownload->freight!='')?$rowfreightdownload->freight: ' ')."^";
				$contents  .= (($rowfreightdownload->vertical_value!='')?$rowfreightdownload->vertical_value: ' ');

          		$linecontents  .= $contents."\n";
			}
			$datacontents = Apicommonfunction::encrypt($contentsrowcolumn."\n".str_replace("\r","",$linecontents));
            $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
            $url =url('/api/v2/RAfreightdownload?nick_name='.$nick_name.'&emp_code='.$emp_code);
            Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
            return $datacontents;
        }
		else
		{
          	$datacontents = Apicommonfunction::encrypt('0'.'##'.'0');
            $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
            $url= url('/api/v2/RAfreightdownload?nick_name='.$nick_name.'&emp_code='.$emp_code);
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
