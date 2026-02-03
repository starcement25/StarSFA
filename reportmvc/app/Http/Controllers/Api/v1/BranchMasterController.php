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

class BranchMasterController extends Controller{
  /**
   * [dydb this function use to connect database on the flay]
   * @param  [varcar] $dbname [database name]
   * @return [object]         [databse connection object]
   */
  public function dydb($dbname){
    $otf = new DbOnTheFly(['database' => $dbname]);
    return $otf;
  }

  public function branchmastertxt(Request $request){

    $nick_name=$request->input('nickname');
    $emp_code=$request->emp_code;
    $device_id=$request->device_id;
    $verificationcode=$request->verificationcode;
    $db_name='acedns_'.strtoupper($request->input('nickname'));
    $linecontents='';
    $dydb =$this->dydb($db_name);
    $CUTDB = $dydb->getConnection();
    $isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
    if($isverify==1){
      $employeewise_hierarchy=Apicommonfunction::getNameTableMainDb('user_details','employeewise_hierarchy','nick_name',$nick_name);
      $survey=Apicommonfunction::getNameTableMainDb('menu_details','survey','nick_name',$nick_name);
      if($employeewise_hierarchy=='yes'){
      	$employee_hierarchy=Apicommonfunction::return_employee_hierarchy($db_name,$emp_code);
      	$emp_hierarchy_condition='EM.emp_code IN('.$employee_hierarchy.')';
      }
      else{
      	$emp_hierarchy_condition="EM.emp_code='".$emp_code."'";
      }
      if($nick_name=='EMAMI' || $nick_name=='EMAMIT'){
      	$branch_condition=" AND acedns='Y'";
      }
      else{
      	$branch_condition="";
      }
      if($survey=='yes' && $nick_name!='EMAMI'){
      	$sqlquery=$CUTDB->select("SELECT DISTINCT BM.branch_code,BM.branch_name,BM.comp_code,BM.HQ,BM.plant_name FROM
      				branch_master BM,employee_master EM WHERE FIND_IN_SET(BM.branch_code,EM.branch_code) AND ".$emp_hierarchy_condition." ORDER BY BM.branch_name ASC");
      }
      else{
      	$sqlquery=$CUTDB->select("SELECT BM.branch_code,BM.branch_name,BM.comp_code,BM.HQ,BM.plant_name FROM branch_master BM WHERE
      				1 ".$branch_condition." ORDER BY BM.branch_name ASC");
      }

    	$count=count($sqlquery);
    	$cnt=1;
    	$contentsrowcolumn  =$count.'¥'.'5';
    	if($count>0){
    		$date=gmdate('d',strtotime('+330 minute'));
    		$month=gmdate('m',strtotime('+330 minute'));
    		$year=gmdate('Y',strtotime('+330 minute'));

    		$hour=gmdate('H',strtotime('+330 minute'));
    		$minute=gmdate('i',strtotime('+330 minute'));
    		$second=gmdate('s',strtotime('+330 minute'));
    		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

    		foreach($sqlquery as $rowbranch){
    			$contents  = (($rowbranch->comp_code!='')?$rowbranch->comp_code: ' ')."^";
    			$contents  .= (($rowbranch->branch_code!='')?$rowbranch->branch_code: ' ')."^";
    			$contents  .= (($rowbranch->branch_name!='')?$rowbranch->branch_name: ' ')."^";
    			$contents  .= (($rowbranch->HQ!='')?$rowbranch->HQ: ' ')."^";
    			$contents  .= (($rowbranch->plant_name!='')?$rowbranch->plant_name: ' ');

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
    			$datacontents = '0'.'¥'.'5';
    		}
    	}
      $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
      $url = url('/api/v1/customermastercreditlimit?nick_name='.$nick_name.'&emp_code='.$emp_code);
      Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
      header("Content-type: application/text");
    	header("Content-Disposition: attachment; filename=branch_master.txt");
    	print "$datacontents";
    }
    else{
      $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
      $url = url('/api/v1/customermastercreditlimit');
      Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
      return 404;
    }

  }



}
