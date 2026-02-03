<?php
namespace App\Http\Controllers\Api\v3;

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

class SaudamrpDownloadController extends Controller
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
    public function saudamrpdownload(Request $request){
    $nick_name=Apicommonfunction::decrypt($request->nickname);
    $emp_code=Apicommonfunction::decrypt($request->emp_code);
    $last_update_time=Apicommonfunction::decrypt($request->last_update_time);
    $last_update_time=str_replace('€',' ',$last_update_time);
    $incremental_download=Apicommonfunction::decrypt($request->incremental_download);
    $data_download_time=Apicommonfunction::decrypt($request->data_download_time);
    $data_download_time=str_replace('€',' ',$data_download_time);
    $verificationcode=Apicommonfunction::decrypt($request->verificationcode);
    $db_name='acedns_'.strtoupper($nick_name);
    $dydb =$this->dydb($db_name);
    $CUTDB = $dydb->getConnection();
    $isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
    $vertical_fields=Apicommonfunction::getNameTableMainDb('user_details','vertical_fields','nick_name',$nick_name);
    $branch_wise_mrp=Apicommonfunction::getNameTableMainDb('product_details','branch_wise_mrp','nick_name',$nick_name);
	$sauda_rate_dependent_on_despatch_point=Apicommonfunction::getNameTableMainDb('sauda_form_details','sauda_rate_dependent_on_despatch_point','nick_name',$nick_name);
	$linecontents='';
    if($isverify==1){
      if($vertical_fields=='yes'){
       $rowempvertical=$CUTDB->table('employee_master')
                            ->select('vertical_value')
                            ->where('emp_code',$emp_code)
                            ->first();
       $emp_vertical_value=$rowempvertical->vertical_value;
       $emp_vertical_value_array=explode(',',$emp_vertical_value);
       $emp_vertical_value = "'".implode("','", $emp_vertical_value_array)."'";
       $condition_one=' AND MRP.vertical_value IN ('.$emp_vertical_value.')';
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
       if($branch_wise_mrp=='yes'){
           $sqlquery=$CUTDB->select("SELECT DISTINCT MRP.* FROM sauda_mrp MRP,employee_master EM WHERE FIND_IN_SET(MRP.branch_code,EM.branch_code)
        			AND EM.emp_code='".$emp_code."' ".$condition_one." ".$login_condition."");
          //$sqlquery="SELECT MRP.* FROM mrp MRP WHERE 1 ".$condition_one." ".$login_condition."";
        }
        else{
        	$sqlquery=$CUTDB->select("SELECT MRP.* FROM sauda_mrp MRP WHERE 1 ".$condition_one." ".$login_condition."");
        }
        $count=count($sqlquery);
      	$cnt=1;
      	$contentsrowcolumn  =$count.'##'.'9';
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
				if($sauda_rate_dependent_on_despatch_point=='yes')
				{
					$sale_rate=$rowprice->sale_rate;
				}
				else $sale_rate=$rowprice->basic_rate;
				
      			$contents  = (($rowprice->product_code!='')?$rowprice->product_code: ' ')."^";
      			$contents  .= (($rowprice->mrp_code!='')?$rowprice->mrp_code: ' ')."^";
      			$contents  .= (($rowprice->mrp!='')?$rowprice->mrp: ' ')."^";
      			$contents  .= (($rowprice->sale_rate!='')?$rowprice->sale_rate: ' ')."^";
      			$contents  .= (($rowprice->UOM!='')?$rowprice->UOM: ' ')."^";
      			$contents  .= (($rowprice->branch_code!='')?$rowprice->branch_code: ' ')."^";
      			$contents  .= (($rowprice->basic_rate!='')?$rowprice->basic_rate: ' ')."^";
      			$contents  .= (($rowprice->primary_freight!='')?$rowprice->primary_freight: ' ')."^";
      			$contents  .= (($rowprice->depot_cost!='')?$rowprice->depot_cost: ' ');

      			$linecontents  .= $contents."\n";
      		}
      		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
      	}
      	else{
      		$last_update_time=str_replace('?','',$last_update_time);
      		$data_download_time=str_replace('?','',$data_download_time);
      		if(strtotime($data_download_time)>=strtotime($last_update_time)){
      			$datacontents = '0'.'##'.'0';
      		}
      		else{
      			$datacontents = '0'.'##'.'9';
      		}
      	 }
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
		$datacontents = Apicommonfunction::encrypt($datacontents);
		$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
		$url= url('/api/v3/saudamrpdownload?nick_name='.$nick_name.'&emp_code='.$emp_code.'&last_update_time='.$last_update_time.'&data_download_time='.$data_download_time.'&incremental_download='.$incremental_download);
		Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
		 header("Content-type: application/text");
		 header("Content-Disposition: attachment; filename=mrp.txt");
		 print "$datacontents";
		//return $datacontents;
    }
	else
	{
		$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
        $url = url('/api/v3/saudamrpdownload??nick_name='.$nick_name.'&emp_code='.$emp_code.'&last_update_time='.$last_update_time.'&data_download_time='.$data_download_time.'&incremental_download='.$incremental_download);
        Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
        return Apicommonfunction::encrypt('404');
	}
  }
}
