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

class ProductClStockController extends Controller{

  /**
   * [dydb this function use to connect database on the flay]
   * @param  [varcar] $dbname [database name]
   * @return [object]         [databse connection object]
   */
  public function dydb($dbname){
    $otf = new DbOnTheFly(['database' => $dbname]);
    return $otf;
  }

  public function productclstkmasterincremental(Request $request){
    $nick_name=$request->input('nickname');
    $emp_code=$request->emp_code;
    $last_update_time=$request->last_update_time;
    $last_update_time=str_replace('€',' ',$last_update_time);
    $incremental_download=$request->incremental_download;
    $data_download_time=$request->data_download_time;
    $data_download_time=str_replace('€',' ',$data_download_time);
    $verificationcode=$request->verificationcode;
    $db_name='acedns_'.strtoupper($request->input('nickname'));
    $dydb =$this->dydb($db_name);
    $CUTDB = $dydb->getConnection();
    $isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
    $vertical_fields=Apicommonfunction::getNameTableMainDb('user_details','vertical_fields','nick_name',$nick_name);
    $sale=Apicommonfunction::getNameTableMainDb('order_form_details','sale','nick_name',$nick_name);
    $branch_wise_cl_stk=Apicommonfunction::getNameTableMainDb('product_details','branch_wise_cl_stk','nick_name',$nick_name);
    if($isverify==1){
      if($vertical_fields=='yes'){
        $sqlempvertical=$CUTDB->table('employee_master')
                             ->select('vertical_value')
                             ->where('emp_code',$emp_code)
                             ->first();
      	$emp_vertical_value=$sqlempvertical->vertical_value;
      	$emp_vertical_value_array=explode(',',$emp_vertical_value);
      	foreach($emp_vertical_value_array as $emp_vertical_array_val){
      		$final_emp_vertical_value_array[]=ltrim($emp_vertical_array_val);
      	}
      	$emp_vertical_value = "'".implode("','", $final_emp_vertical_value_array)."'";
      	$condition_one=' AND PM.vertical_value IN ('.$emp_vertical_value.')';
      }
      else{
      	$condition_one="";
      }
      if($sale=='no'){
      	if($incremental_download=='no'){
      		$login_condition="AND PM.acedns='Y' AND PM.black_list='N'";
      		$login_condition_one="AND PM.acedns='Y' AND PM.black_list='N'";
      	}
      	else{
      		$login_condition="AND UNIX_TIMESTAMP(PM.download_time_cl_stk) > UNIX_TIMESTAMP('".$last_update_time."')";
      		$login_condition_one="AND UNIX_TIMESTAMP(BPWS.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
      	}
      	$sqlbranches=$CUTDB->table('branch_master')
                           ->get();
      	$countbranches=count($sqlbranches);

      	if($countbranches>1){
      		if($branch_wise_cl_stk=='yes'){
      			$sqlquery=$CUTDB->select("SELECT DISTINCT PM.prod_code,BPWS.closing_stk AS cl_stk FROM product_master PM,branch_product_wise_stock BPWS,employee_master EM
      						WHERE PM.prod_desc <>'' AND EM.branch_code=BPWS.branch_code AND PM.prod_code=BPWS.product_code
      						".$condition_one.$login_condition_one." AND EM.emp_code ='".$emp_code."'  ORDER BY PM.prod_desc ASC");
      		}
      		else{
      		  $sqlquery=$CUTDB->select("SELECT DISTINCT PM.prod_code,PM.prod_desc,PM.cl_stk FROM product_master PM WHERE PM.prod_desc <>''
      					".$condition_one." ".$login_condition." ORDER BY PM.prod_desc ASC");
      		}
      	}
      	else{
      		$sqlquery=$CUTDB->select("SELECT DISTINCT PM.prod_code,PM.prod_desc,PM.cl_stk FROM product_master PM WHERE PM.prod_desc <>''
      					 ".$condition_one." ".$login_condition." ORDER BY PM.prod_desc ASC");
      	}

      	$count=count($sqlquery);
      	$contentsrowcolumn  =$count.'¥'.'2';
      	if($count>0){
      		$date=gmdate('d',strtotime('+329 minute'));
      		$month=gmdate('m',strtotime('+329 minute'));
      		$year=gmdate('Y',strtotime('+329 minute'));

      		$hour=gmdate('H',strtotime('+329 minute'));
      		$minute=gmdate('i',strtotime('+329 minute'));
      		$second=gmdate('s',strtotime('+329 minute'));

      		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
      		foreach($sqlquery as $rowproduct){
      			$contents  = (($rowproduct->prod_code!='')?$rowproduct->prod_code: ' ')."^";
      			$contents  .= (($rowproduct->cl_stk!='')?$rowproduct->cl_stk: ' ');
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
      			$datacontents = '0'.'¥'.'2';
      		}
      	}
      }
      else{
      	$sqlempbranch=$CUTDB->table('employee_master')
                             ->select('branch_code')
                             ->where('emp_code',$emp_code)
                             ->first();
      	$branch_code=$sqlempbranch->branch_code;
      	if($branch_code!==''){
      		$sqlemprds=$CUTDB->table('rds_master')
                               ->select('rds_code')
                               ->where('emp_code',$emp_code)
                               ->first();
      		$rds_code=$sqlemprds->rds_code;

      		$sqlquery=$CUTDB->select("SELECT BRPWS.product_code,BRPWS.closing_stk FROM product_master PM,branch_rds_product_wise_stock BRPWS
      				WHERE PM.prod_code=BRPWS.product_code AND BRPWS.branch_code='".$branch_code."' AND BRPWS.rds_code='".$rds_code."'
      				".$condition_one." ORDER BY PM.prod_code ASC");
      	}
      	else{
      		$employee_hierarchy=Apicommonfunction::return_employee_hierarchy($db_name,$emp_code);
      		$sql_rds_details=$CUTDB->select('SELECT rds_code FROM rds_master WHERE emp_code IN($employee_hierarchy)');

      		foreach($sql_rds_details as $row_rds_details){
      			$rds_list=$rds_list."'".$row_rds_details->rds_code."'".',';
      		}
      		$rds_list=substr($rds_list,0,-1);
      		$sqlquery=$CUTDB->select("SELECT BRPWS.product_code,SUM(BRPWS.closing_stk) AS closing_stk FROM product_master PM,branch_rds_product_wise_stock BRPWS
      				WHERE PM.prod_code=BRPWS.product_code ".$condition_one." AND BRPWS.rds_code IN($rds_list) GROUP BY
      				BRPWS.product_code ORDER BY PM.prod_code ASC");
      	}
      	$count=count($sqlquery);
      	$contentsrowcolumn  =$count.'¥'.'2';
      	if($count>0){
      		$date=gmdate('d',strtotime('+329 minute'));
      		$month=gmdate('m',strtotime('+329 minute'));
      		$year=gmdate('Y',strtotime('+329 minute'));

      		$hour=gmdate('H',strtotime('+329 minute'));
      		$minute=gmdate('i',strtotime('+329 minute'));
      		$second=gmdate('s',strtotime('+329 minute'));
      		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
      		foreach($sqlquery as $rowproduct){
      			$contents  = (($rowproduct->product_code!='')?$rowproduct->product_code: ' ')."^";
      			$contents  .= (($rowproduct->closing_stk!='')?$rowproduct->closing_stk: ' ');
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
      			$datacontents = '0'.'¥'.'2';
      		}
      	}
      }
      $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
      $url = url('/api/v1/productclstkmaster?nick_name='.$nick_name.'&emp_code='.$emp_code.'&last_update_time='.$last_update_time.'&data_download_time='.$data_download_time.'&incremental_download='.$incremental_download);
      Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
      header("Content-type: application/text");
    	header("Content-Disposition: attachment; filename=product_cl_stk.txt");
    	print "$datacontents";

    }
    else{
      $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
      $url = url('/api/v1/productclstkmaster?nick_name='.$nick_name.'&emp_code='.$emp_code.'&last_update_time='.$last_update_time.'&data_download_time='.$data_download_time.'&incremental_download='.$incremental_download);
      Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
      return 404;

    }

  }

}
