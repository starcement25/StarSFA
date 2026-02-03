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

class MrpMasterController extends Controller
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

  public function mrpmasterincremental(Request $request){
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
    $vertical_fields=Apicommonfunction::getNameTableMainDb('user_details','vertical_fields','nick_name',$nick_name);
    $multiple_rate=Apicommonfunction::getNameTableMainDb('product_details','multiple_rate','nick_name',$nick_name);
    $branch_wise_mrp=Apicommonfunction::getNameTableMainDb('product_details','branch_wise_mrp','nick_name',$nick_name);
    if($isverify==1){
      if($vertical_fields=='yes'){
       $rowempvertical=$CUTDB->table('employee_master')
                            ->select('vertical_value')
                            ->where('emp_code',$emp_code)
                            ->first();
       $emp_vertical_value=$rowempvertical->vertical_value;
       $emp_vertical_value_array=explode(',',$emp_vertical_value);
       $emp_vertical_value = "'".implode("','", $emp_vertical_value_array)."'";
       $condition_one=' AND PGM.vertical_value IN ('.$emp_vertical_value.')';
      }
      else{
       $condition_one="";
      }
      if($incremental_download=='no'){
      	$login_condition="";
      }
      else{
      	$login_condition=" AND UNIX_TIMESTAMP(MRP.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
      }
      if($multiple_rate=='yes'){
      	$sqlstate=$CUTDB->table('employee_master')
                             ->select('state')
                             ->where('emp_code',$emp_code)
                             ->first();
      	$state_name=$sqlstate->state;
      	$sqlstatecode=$CUTDB->select("SELECT state_code FROM state_master WHERE statename LIKE '%".$state_name."%'")[0];
      	$state_code=$sqlstatecode->state_code;
      	$sqlquery=$CUTDB->select("SELECT MRP.* FROM mrp MRP WHERE 1 AND MRP.state_code='".$state_code."' ".$condition_one." ".$login_condition."");
      	$count=count($sqlquery);
      	$cnt=1;
      	$contentsrowcolumn  =$count.'¥'.'13';
      	if($count>0){
      		$date=gmdate('d',strtotime('+330 minute'));
      		$month=gmdate('m',strtotime('+330 minute'));
      		$year=gmdate('Y',strtotime('+330 minute'));

      		$hour=gmdate('H',strtotime('+330 minute'));
      		$minute=gmdate('i',strtotime('+330 minute'));
      		$second=gmdate('s',strtotime('+330 minute'));
      		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
      		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
      		foreach($sqlquery as $rowprice){
      			$contents  = (($rowprice->product_code!='')?$rowprice->product_code: ' ')."^";
      			$contents  .= (($rowprice->mrp_code!='')?$rowprice->mrp_code: ' ')."^";
      			$contents  .= (($rowprice->mrp!='')?round($rowprice->mrp,2): ' ')."^";
      			$contents  .= (($rowprice->sale_rate!='')?round($rowprice->sale_rate,2): ' ')."^";
      			$contents  .= (($rowprice->UOM!='')?$rowprice->UOM: ' ')."^";
      			$contents  .= (($rowprice->branch_code!='')?$rowprice->branch_code: ' ')."^";
      			$contents  .= (($rowprice->destination_code!='')?$rowprice->destination_code: ' ')."^";
      			$contents  .= (($rowprice->order_type!='')?$rowprice->order_type: ' ')."^";
      			$contents  .= (($rowprice->acedns!='')?$rowprice->acedns: ' ')."^";
      			$contents  .= (($rowprice->ws_rate!='')?$rowprice->ws_rate: ' ')."^";
      			$contents  .= (($rowprice->distributor_rate!='')?$rowprice->distributor_rate: ' ')."^";
      			$contents  .= (($rowprice->ss_rate!='')?$rowprice->ss_rate: ' ')."^";
      			$contents  .= (($rowprice->depot_rate!='')?$rowprice->depot_rate: ' ');
      			$linecontents  .= $contents."\n";
      		}
      		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
      	}
      	else{
      		$last_update_time=str_replace('?','',$last_update_time);
      		$data_download_time=str_replace('?','',$data_download_time);
      		if(strtotime($data_download_time)>=strtotime($last_update_time)){
      			$datacontents = '0'.'¥'.'0';
      		}
      		else{
      			$datacontents = '0'.'¥'.'13';
      		}
      	}
      }
      else{
        if($branch_wise_mrp=='yes'){
           $sqlquery=$CUTDB->select("SELECT DISTINCT MRP.* FROM mrp MRP,employee_master EM WHERE FIND_IN_SET(MRP.branch_code,EM.branch_code)
        			AND EM.emp_code='".$emp_code."' ".$condition_one." ".$login_condition."");
          //$sqlquery="SELECT MRP.* FROM mrp MRP WHERE 1 ".$condition_one." ".$login_condition."";
        }
        else{
        	$sqlquery=$CUTDB->select("SELECT MRP.* FROM mrp MRP WHERE 1 ".$condition_one." ".$login_condition."");
        }
        $count=count($sqlquery);
      	$cnt=1;
      	$contentsrowcolumn  =$count.'¥'.'13';
      	if($count>0){
      		$date=gmdate('d',strtotime('+330 minute'));
      		$month=gmdate('m',strtotime('+330 minute'));
      		$year=gmdate('Y',strtotime('+330 minute'));

      		$hour=gmdate('H',strtotime('+330 minute'));
      		$minute=gmdate('i',strtotime('+330 minute'));
      		$second=gmdate('s',strtotime('+330 minute'));
      		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
      		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
      		foreach($sqlquery as $rowprice){
      			$contents  = (($rowprice->product_code!='')?$rowprice->product_code: ' ')."^";
      			$contents  .= (($rowprice->mrp_code!='')?$rowprice->mrp_code: ' ')."^";
      			$contents  .= (($rowprice->mrp!='')?round($rowprice->mrp,2): ' ')."^";
      			$contents  .= (($rowprice->sale_rate!='')?round($rowprice->sale_rate,2): ' ')."^";
      			$contents  .= (($rowprice->UOM!='')?$rowprice->UOM: ' ')."^";
      			$contents  .= (($rowprice->branch_code!='')?$rowprice->branch_code: ' ')."^";
      			$contents  .= (($rowprice->destination_code!='')?$rowprice->destination_code: ' ')."^";
      			$contents  .= (($rowprice->order_type!='')?$rowprice->order_type: ' ')."^";
      			$contents  .= (($rowprice->acedns!='')?$rowprice->acedns: ' ')."^";
      			$contents  .= (($rowprice->ws_rate!='')?$rowprice->ws_rate: ' ')."^";
      			$contents  .= (($rowprice->distributor_rate!='')?$rowprice->distributor_rate: ' ')."^";
      			$contents  .= (($rowprice->ss_rate!='')?$rowprice->ss_rate: ' ')."^";
      			$contents  .= (($rowprice->depot_rate!='')?$rowprice->depot_rate: ' ');

      			$linecontents  .= $contents."\n";
      		}
      		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
      	}
      	else{
      		$last_update_time=str_replace('?','',$last_update_time);
      		$data_download_time=str_replace('?','',$data_download_time);
      		if(strtotime($data_download_time)>=strtotime($last_update_time)){
      			$datacontents = '0'.'¥'.'0';
      		}
      		else{
      			$datacontents = '0'.'¥'.'13';
      		}
      	}
      }
      $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
      $url = url('/api/v1/mrpmaster?nick_name='.$nick_name.'&emp_code='.$emp_code.'&last_update_time='.$last_update_time.'&data_download_time='.$data_download_time.'&incremental_download='.$incremental_download);
      Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
      header("Content-type: application/text");
    	header("Content-Disposition: attachment; filename=mrp.txt");
    	print "$datacontents";
    }
    else{
      $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
      $url = url('/api/v1/mrpmaster');
      Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
      return 404;
    }

  }
}
