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



class RoutePlanMasterController extends Controller{
  /**
   * [dydb this function use to connect database on the flay]
   * @param  [varcar] $dbname [database name]
   * @return [object]         [databse connection object]
   */
  public function dydb($dbname){
    $otf = new DbOnTheFly(['database' => $dbname]);
    return $otf;
  }

  public function routeplanmasterincremental(Request $request){
    $nick_name=$request->input('nickname');
    $emp_code=$request->emp_code;
    $last_update_time=$request->last_update_time;
    $last_update_time=str_replace('€',' ',$last_update_time);
    $incremental_download=$request->incremental_download;
    $data_download_time=$request->data_download_time;
    $data_download_time=str_replace('€',' ',$data_download_time);
    $device_id=$request->device_id;
    $verificationcode=$request->verificationcode;
    $db_name='acedns_'.strtoupper($request->input('nickname'));
    $dydb =$this->dydb($db_name);
    $CUTDB = $dydb->getConnection();
    $isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
    if($isverify==1){
      $sqlaccessperiod=$CUTDB->table('route_plan_access_period')
                             ->select('access_start_date','access_end_date','period')
                             ->where('emp_code',$emp_code)
                             ->first();
      $countaccessperiod=count($sqlaccessperiod);
      if($countaccessperiod>0){
      	$contentsaccessperiod=date('d-m-Y',strtotime($sqlaccessperiod->access_start_date)).'µ'.date('d-m-Y',strtotime($sqlaccessperiod->access_end_date)).'µ'.$sqlaccessperiod->period;
      }
      else{
      	$contentsaccessperiod='';
      }
      if($incremental_download=='no'){
      	$login_condition="";
      }
      else{
      	$login_condition=" AND UNIX_TIMESTAMP(create_date) > UNIX_TIMESTAMP('".$last_update_time."')";
      }
      $sqlquery=$CUTDB->select("SELECT *,DATE_FORMAT(visit_date,'%d-%m-%Y') AS visit_date FROM route_plan WHERE emp_code='".$emp_code."'".$login_condition);
      $count=count($sqlquery);
      	$cnt=1;
      	$contentsrowcolumn=$count.'¥'.'8';
      	if($count>0){
      		$date=gmdate('d',strtotime('+330 minute'));
      		$month=gmdate('m',strtotime('+330 minute'));
      		$year=gmdate('Y',strtotime('+330 minute'));

      		$hour=gmdate('H',strtotime('+330 minute'));
      		$minute=gmdate('i',strtotime('+330 minute'));
      		$second=gmdate('s',strtotime('+330 minute'));

      		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

      		foreach($sqlquery as $rowrouteplan){
      				$visit_date=$rowrouteplan->visit_date;
      				$visit_date_final=date('Y-m-d',strtotime($visit_date));
      				$trans_id=$rowrouteplan->route_plan_trans_id;
      				$current_route_code=$rowrouteplan->route_code;
      				$sqlroutename=$CUTDB->table('route_master')
                            ->select('route_name')
                            ->where('route_code',$current_route_code)
                            ->first();

      				$route_name=$sqlroutename->route_name;
      				$contents  = (($rowrouteplan->route_plan_trans_id!='')?$rowrouteplan->route_plan_trans_id: ' ')."^";
      				$contents  .= (($rowrouteplan->emp_code!='')?$rowrouteplan->emp_code: ' ')."^";
      				$contents  .= (($rowrouteplan->route_code!='')?$rowrouteplan->route_code: ' ')."^";
      				$contents  .= (($rowrouteplan->visit_date!='')?$rowrouteplan->visit_date: ' ')."^";
      				$contents  .= (($rowrouteplan->create_date!='')?$rowrouteplan->create_date: ' ')."^";
      				$contents  .= (($rowrouteplan->status!='')?$rowrouteplan->status: ' ')."^";
      				$contents  .= (($rowrouteplan->distributor_code!='')?$rowrouteplan->distributor_code: ' ')."^";
      				$contents  .= (($route_name!='')?$route_name: ' ');
      				$linecontents  .= $contents."\n";
      		}
      		$datacontents = $contentsrowcolumn."\n".$contentsdatetime."\n".$contentsaccessperiod."\n".str_replace("\r","",$linecontents);
      	}
      	else{
      		$datacontents = $contentsaccessperiod."\n".'0'.'¥'.'0';
      	}
        $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
        $url = url('/api/v1/routeplanmaster?nick_name='.$nick_name.'&emp_code='.$emp_code.'&device_id='.$device_id.'&last_update_time='.$last_update_time.'&incremental_download='.$incremental_download);
        Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
        header("Content-type: application/text");
      	header("Content-Disposition: attachment; filename=route_plan_master.txt");
      	print "$datacontents";
    }
    else{
      $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
      $url = url('/api/v1/routeplanmaster');
      Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
      return 404;
    }
  }

}
