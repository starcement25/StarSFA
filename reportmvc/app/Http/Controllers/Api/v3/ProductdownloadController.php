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

class ProductdownloadController extends Controller{
  /**
   * [dydb this function use to connect database on the flay]
   * @param  [varcar] $dbname [database name]
   * @return [object]         [databse connection object]
   */
  public function dydb($dbname){
    $otf = new DbOnTheFly(['database' => $dbname]);
    return $otf;
  }

  public function productdownloadincremental(Request $request){
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
    $branch_wise_product=Apicommonfunction::getNameTableMainDb('product_details','branch_wise_product','nick_name',$nick_name);
    $state_branch_wise_TD=Apicommonfunction::getNameTableMainDb('sauda_form_details','state_branch_wise_TD','nick_name',$nick_name);
	$linecontents='';
    if($isverify==1){
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
      		$condition_two.=" FIND_IN_SET( '".$emp_vertical_values."',PM.vertical_value) OR";
      	}
      	$condition_two=substr($condition_two,0,-2);
      	$condition_one.=$condition_two.")";
      }
      else{
        $condition_one="";
      }
      if($incremental_download=='no'){
      		$login_condition="AND PM.acedns='Y' AND PM.black_list='N'";
      }
      else{
      	$login_condition=" AND UNIX_TIMESTAMP(PM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
      }
      $sqlbranches=$CUTDB->table('branch_master')
                           ->select('branch_code')
                           ->get();
      $countbranches=count($sqlbranches);
      if($countbranches>1){
      	if($branch_wise_product=='yes' || $nick_name=='DNV'){
      		$sqlempbranch=$CUTDB->table('employee_master')
                               ->select('branch_code','state')
                               ->where('emp_code',$emp_code)
                               ->first();

      		$branch_value=$sqlempbranch->branch_code;
      		$state=$sqlempbranch->state;
      		if($state_branch_wise_TD=='yes'){
      			$sqlstatecode=$CUTDB->table('state_master')
                                 ->select('state_code')
                                 ->where('state',$state)
                                 ->first();
      			$state_code=$sqlstatecode->state_code;
      		}
      		$branch_value_array=explode(',',$branch_value);
      		$branch_value = "'".implode("','", $branch_value_array)."'";
      		$condition_branch=' AND PM.branch_code IN ('.$branch_value.')';

      		$sqlquery=$CUTDB->select("SELECT DISTINCT PM.*
      					FROM product_master PM,employee_master EM
      					WHERE PM.prod_desc <>''
      					AND EM.emp_code ='".$emp_code."' ".$condition_branch.$condition_one." ".$login_condition." ORDER BY PM.prod_desc ASC");
      	}
      	else{
      		$sqlquery=$CUTDB->select("SELECT DISTINCT PM.* FROM product_master PM WHERE PM.prod_desc <>'' ".$condition_one." ".$login_condition." ORDER BY PM.prod_desc ASC");
      	}
      }
      else{
      	$sqlquery=$CUTDB->select("SELECT DISTINCT PM.* FROM product_master PM WHERE PM.prod_desc <>'' ".$condition_one." ".$login_condition." ORDER BY PM.prod_desc ASC");
      }
      $count=count($sqlquery);
      	$cnt=1;
      	$contentsrowcolumn  =$count.'¥'.'26';
      	if($count>0){
      		$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
	
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
      	 	foreach($sqlquery as $rowproduct){
      			if($state_branch_wise_TD=='yes'){
      				$sqlselTD=$CUTDB->select("SELECT TD FROM state_branch_product_wise_TD WHERE state_code='".$state_code."' AND
      							branch_code='".$rowproduct['branch_code']."' AND prod_code='".$rowproduct['prod_code']."'
      							AND SUBSTRING(download_time,1,10)='".$date."' ORDER BY download_time DESC LIMIT 0,1");

      				$TD=$sqlselTD->TD;
      			}
      			else{
      				$TD=$rowproduct->TD;
      			}
      			$contents  = (($rowproduct->prod_code!='')?$rowproduct->prod_code: ' ')."^";
      			$contents  .= (($rowproduct->product_group_code!='')?$rowproduct->product_group_code: ' ')."^";
      			$contents  .= ' '."^";
      			$contents  .= (($rowproduct->product_sub_group_code!='')?$rowproduct->product_sub_group_code: ' ')."^";
      			$contents  .= ' '."^";
      			$contents  .= (($rowproduct->product_brand_code!='')?$rowproduct->product_brand_code: ' ')."^";
      			$contents  .= ' '."^";
      			$contents  .= (($rowproduct->prod_desc!='')?$rowproduct->prod_desc: ' ')."^";
      			$contents  .= (($rowproduct->black_list!='')?$rowproduct->black_list: ' ')."^";
      			$contents  .= (($rowproduct->acedns!='')?$rowproduct->acedns: ' ')."^";
      			$contents  .= (($rowproduct->UOM1!='')?$rowproduct->UOM1: ' ')."^";
      			$contents  .= (($rowproduct->UOM2!='')?$rowproduct->UOM2: ' ')."^";
      			$contents  .= (($rowproduct->conversion_factor!='')?$rowproduct->conversion_factor: ' ')."^";
      			$contents  .= (($rowproduct->pack_size!='')?$rowproduct->pack_size: ' ')."^";
      			$contents  .= (($rowproduct->UOM3!='')?$rowproduct->UOM3: ' ')."^";
      			$contents  .= (($rowproduct->conversion_factor_two!='')?$rowproduct->conversion_factor_two: ' ')."^";
      			$contents  .= (($TD!='')?$TD: 0)."^";
      			$contents  .= (($rowproduct->branch_code!='')?$rowproduct->branch_code: ' ')."^";
      			$contents  .= (($rowproduct->vertical_value!='')?$rowproduct->vertical_value: ' ')."^";
      			$contents  .= (($rowproduct->secondary_unit!='')?$rowproduct->secondary_unit: ' ')."^";
      			$contents  .= (($rowproduct->dns_prod_code!='')?$rowproduct->dns_prod_code: ' ')."^";
      			$contents  .= (($rowproduct->focus!='')?$rowproduct->focus: ' ')."^";
      			$contents  .= (($rowproduct->weightage!='')?$rowproduct->weightage: ' ')."^";
      			$contents  .= (($rowproduct->vat!='')?$rowproduct->vat: ' ')."^";
      			$contents  .= (($rowproduct->addl_vat!='')?$rowproduct->addl_vat: ' ')."^";
      			$contents  .= (($rowproduct->freight_cost!='')?$rowproduct->freight_cost: ' ');

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

      			$datacontents = '0'.'¥'.'26';
      		}
      	}
		$datacontents = Apicommonfunction::encrypt($datacontents);
        $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
        $url = url('/api/v3/productdownloadincremental?nick_name='.$nick_name.'&emp_code='.$emp_code.'&last_update_time='.$last_update_time.'&data_download_time='.$data_download_time.'&incremental_download='.$incremental_download);
        Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
        header("Content-type: application/text");
      	header("Content-Disposition: attachment; filename=product_master.txt");
      	print "$datacontents";
    }
    else{
      $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
      $url = url('/api/v3/productdownloadincremental?nick_name='.$nick_name.'&emp_code='.$emp_code.'&last_update_time='.$last_update_time.'&data_download_time='.$data_download_time.'&incremental_download='.$incremental_download);
      Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);

    }
  }

}
