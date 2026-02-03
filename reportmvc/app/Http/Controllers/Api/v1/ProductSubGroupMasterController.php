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


class ProductSubGroupMasterController extends Controller{

  /**
   * [dydb this function use to connect database on the flay]
   * @param  [varcar] $dbname [database name]
   * @return [object]         [databse connection object]
   */
  public function dydb($dbname){
    $otf = new DbOnTheFly(['database' => $dbname]);
    return $otf;
  }

  public function productsubgroupmasterincremental(Request $request){
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
      		$condition_two.=" FIND_IN_SET( '".$emp_vertical_values."',PSGM.vertical_value) OR";
      	}
      	$condition_two=substr($condition_two,0,-2);
      	$condition_one.=$condition_two.")";
      }
      else{
        $condition_one="";
      }
      if($incremental_download=='no'){
      		$login_condition='';
      }
      else{
      	$login_condition=" AND UNIX_TIMESTAMP(PSGM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
      }
      $sqlbranches=$CUTDB->table('branch_master')
                         ->first();
      if(count($sqlbranches)>1){
        $sqlquery=$CUTDB->select("SELECT DISTINCT PSGM.* FROM product_sub_group_master PSGM WHERE 1 ".$condition_one." ".$login_condition." ORDER
      				BY PSGM.product_sub_group_name ASC");
      }
      else{
      	$sqlquery=$CUTDB->select("SELECT DISTINCT PSGM.* FROM product_sub_group_master PSGM WHERE 1 ".$condition_one." ".$login_condition."
      				ORDER BY PSGM.product_sub_group_name ASC");
      }
      $count=count($sqlquery);
      $cnt=1;
    	$contentsrowcolumn  =$count.'¥'.'3';
    	if($count>0){
        $date=gmdate('d',strtotime('+329 minute'));
    		$month=gmdate('m',strtotime('+329 minute'));
    		$year=gmdate('Y',strtotime('+329 minute'));

    		$hour=gmdate('H',strtotime('+329 minute'));
    		$minute=gmdate('i',strtotime('+329 minute'));
    		$second=gmdate('s',strtotime('+329 minute'));
    		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
        foreach($sqlquery as $rowproductsubgroup){
    			$contents  = (($rowproductsubgroup->product_sub_group_code!='')?$rowproductsubgroup->product_sub_group_code: ' ')."^";
    			$contents  .= (($rowproductsubgroup->product_group_code!='')?$rowproductsubgroup->product_group_code: ' ')."^";
    			$contents  .= (($rowproductsubgroup->product_sub_group_name!='')?$rowproductsubgroup->product_sub_group_name: ' ');
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
    			$datacontents = '0'.'¥'.'3';
    		}
    	}
      $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
      $url = url('/api/v1/productsubgroupmaster?nick_name='.$nick_name.'&emp_code='.$emp_code.'&last_update_time='.$last_update_time.'&data_download_time='.$data_download_time.'&incremental_download='.$incremental_download);
      Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
      header("Content-type: application/text");
      header("Content-Disposition: attachment; filename=product_sub_group_master.txt");
      print "$datacontents";
    }
    else{
      $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
      $url = url('/api/v1/productsubgroupmaster');
      Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
      return 404;
    }

  }




}
