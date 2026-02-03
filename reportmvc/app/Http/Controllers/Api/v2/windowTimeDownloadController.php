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

class windowTimeDownloadController extends Controller
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
    public function windowtimedownload(Request $request){
        $nick_name=Apicommonfunction::decrypt($request->nickname);
       	//$emp_code=Apicommonfunction::decrypt($request->emp_code);
		$emp_code='';
        $db_name='acedns_'.strtoupper($nick_name);
        $dydb =$this->dydb($db_name);
        $CUTDB = $dydb->getConnection();
		//$verificationcode=Apicommonfunction::decrypt($request->verificationcode);
        //$isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
		$linecontents='';
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));

		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$currentdate=$year.'-'.$month.'-'.$date;
		//if($isverify==1){ 
        $sqlquery=$CUTDB->select("SELECT time_from,time_to,last_window_time FROM RA_windowtime WHERE acedns='Y' AND rate_released_date='".$currentdate."'");
       // echo count($sqlquery);
		if(count($sqlquery)>0){
			$contentsrowcolumn  =count($sqlquery).'##'.'3';
			foreach($sqlquery as $rowtimedownload){
				$contents  = (($rowtimedownload->time_from!='')?$rowtimedownload->time_from: ' ')."^";
          	    $contents  .= (($rowtimedownload->time_to!='')?$rowtimedownload->time_to: ' ')."^";
			    $contents  .= (($rowtimedownload->last_window_time!='')?$rowtimedownload->last_window_time: ' ');

				$linecontents  .= $contents."\n";
			}
			$datacontents = Apicommonfunction::encrypt($contentsrowcolumn."\n".str_replace("\r","",$linecontents));
            $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
            $url =url('/api/v2/windowtimedownload?nick_name='.$nick_name);
            Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
            return $datacontents;
        }
		else
		{
          	$datacontents = Apicommonfunction::encrypt('0'.'##'.'0');
            $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
            $url= url('/api/v2/windowtimedownload?nick_name='.$nick_name);
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
