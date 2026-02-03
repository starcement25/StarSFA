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

class RADetentionDownloadController extends Controller{
  /**
   * [dydb this function use to connect database on the flay]
   * @param  [varcar] $dbname [database name]
   * @return [object]         [databse connection object]
   */
  public function dydb($dbname){
    $otf = new DbOnTheFly(['database' => $dbname]);
    return $otf;
  }
  public function detentiondownloadincremental(Request $request){
    $nick_name=Apicommonfunction::decrypt($request->nickname);
    $emp_code=Apicommonfunction::decrypt($request->emp_code);
    $last_update_time=Apicommonfunction::decrypt($request->last_update_time);
    $last_update_time=str_replace('€',' ',$last_update_time);
    //$verificationcode=Apicommonfunction::decrypt($request->verificationcode);
    $db_name='acedns_'.strtoupper($nick_name);
    $dydb =$this->dydb($db_name);
    $CUTDB = $dydb->getConnection();
    //$isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
    $vertical_fields=Apicommonfunction::getNameTableMainDb('user_details','vertical_fields','nick_name',$nick_name);
	$linecontents='';
      if($vertical_fields=='yes'){
        $sqlempvertical=$CUTDB->table('employee_master')
                             ->select('vertical_value')
                             ->where('emp_code',$emp_code)
                             ->first();
      	$emp_vertical_value=$sqlempvertical->vertical_value;
      	$emp_vertical_value_array=explode(',',$emp_vertical_value);
      	$condition_one=" AND (";
      	$condition_two='';
      	foreach($emp_vertical_value_array as $emp_vertical_values){
      		$condition_two.=" FIND_IN_SET( '".$emp_vertical_values."',DC.vertical_value) OR";
      	}
      	$condition_two=substr($condition_two,0,-2);
      	$condition_one.=$condition_two.")";
      }
      else{
        $condition_one="";
      }
      	$login_condition=" AND UNIX_TIMESTAMP(DC.datetime) > UNIX_TIMESTAMP('".$last_update_time."')";
	/*echo "SELECT * FROM (SELECT MC.dns_prod_code,DATE_FORMAT(SUBSTRING(MC.datetime,1,10),'%d-%m-%Y') As last_updated_date,
				MC.margin_cost,MC.state_code,MC.zone FROM margin_cost_RA MC WHERE 1 ".$login_condition.$condition_one." ORDER BY MC.datetime DESC) AS SAT GROUP BY 1,4 ORDER BY 2 DESC";	*/
      
	  $sqlquery=$CUTDB->select("SELECT * FROM (SELECT DC.prod_code,DATE_FORMAT(SUBSTRING(DC.datetime,1,10),'%d-%m-%Y') As last_updated_date,
				DC.detention_cost,DC.branch_code FROM detention_cost DC WHERE 1 ".$login_condition.$condition_one." ORDER BY DC.datetime DESC) AS SAT GROUP BY 1,4 ORDER BY 2 DESC");
      $count=count($sqlquery);
      	$cnt=1;
      	$contentsrowcolumn  =$count.'##'.'3';
      	if($count>0){
      		$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
	
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
      	 	foreach($sqlquery as $rowdetention){
				
				$contents  = (($rowdetention->branch_code!='')?$rowdetention->branch_code: ' ')."^";
      			$contents  .= (($rowdetention->prod_code!='')?$rowdetention->prod_code: ' ')."^";
      			$contents  .= (($rowdetention->detention_cost!='')?$rowdetention->detention_cost: ' ');
      			
      			$linecontents  .= $contents."\n";
      		}
      		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
			$datacontents = Apicommonfunction::encrypt($datacontents);
			$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
			$url = url('/api/v2/detentiondownload?nick_name='.$nick_name.'&emp_code='.$emp_code.'&last_update_time='.$last_update_time);
			Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
			return $datacontents;
      	}
      	else{
      		$datacontents = Apicommonfunction::encrypt('0'.'##'.'0');
            $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
            $url= url('/api/v2/detentiondownload?nick_name='.$nick_name.'&emp_code='.$emp_code.'&last_update_time='.$last_update_time);
            Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
            return $datacontents;
      	}
	}
}
