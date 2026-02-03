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

class CompetitorGroupMasterController extends Controller{
  /**
   * [dydb this function use to connect database on the flay]
   * @param  [varcar] $dbname [database name]
   * @return [object]         [databse connection object]
   */
  public function dydb($dbname){
    $otf = new DbOnTheFly(['database' => $dbname]);
    return $otf;
  }

  public function competitorgroupmastertxt(Request $request){

    $nick_name=$request->input('nickname');
    $emp_code=$request->emp_code;
    $device_id=$request->device_id;
    $verificationcode=$request->verificationcode;
    $linecontents='';
    $datacontents='';
    $branch_code='';
    $last_update_time='';
    $data_download_time='';
    $db_name='acedns_'.strtoupper($request->input('nickname'));
    $dydb =$this->dydb($db_name);
    $CUTDB = $dydb->getConnection();
    $isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
    if($isverify==1){
      $employeewise_hierarchy=Apicommonfunction::getNameTableMainDb('user_details','employeewise_hierarchy','nick_name',$nick_name);
      if($employeewise_hierarchy=='yes'){
      		$employee_hierarchy=Apicommonfunction::return_employee_hierarchy($db_name,$emp_code);
      		$emp_hierarchy_condition='emp_code IN('.$employee_hierarchy.')';
      }
      else{
      	$emp_hierarchy_condition="emp_code='".$emp_code."'";
      }
      $sqlempbranch=$CUTDB->select("SELECT branch_code FROM employee_master WHERE ".$emp_hierarchy_condition);
      foreach($sqlempbranch as $rowempbranch){
      	$branch_value=$rowempbranch->branch_code;
      	$branch_code=$branch_code.$branch_value.',';
      }
      $branch_value_array=explode(',',$branch_code);
      $branch_value_final = "'".implode("','", $branch_value_array)."'";
      $condition_branch=" AND branch_code IN (".$branch_value_final.")";
      if($nick_name=='START'){
      	$sqlquery=$CUTDB->select("SELECT DISTINCT * FROM competitor_group_master WHERE 1 ORDER BY competitor_name ASC");
      }
      else{
      	$sqlquery=$CUTDB->select("SELECT DISTINCT * FROM competitor_group_master WHERE 1 ORDER BY competitor_name ASC");
      }
      $count=count($sqlquery);
      $cnt=1;
      $contentsrowcolumn  =$count.'¥'.'3';
      if($count>0){

      		$date=gmdate('d',strtotime('+330 minute'));
      		$month=gmdate('m',strtotime('+330 minute'));
      		$year=gmdate('Y',strtotime('+330 minute'));

      		$hour=gmdate('H',strtotime('+330 minute'));
      		$minute=gmdate('i',strtotime('+330 minute'));
      		$second=gmdate('s',strtotime('+330 minute'));
      		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

      		foreach($sqlquery as $rowcompetitorgroup){
      			$contents  = (($rowcompetitorgroup->group_name!='')?$rowcompetitorgroup->group_name: ' ')."^";
      			$contents  .= (($rowcompetitorgroup->competitor_name!='')?$rowcompetitorgroup->competitor_name: ' ')."^";
      			$contents  .= (($rowcompetitorgroup->UOM!='')?$rowcompetitorgroup->UOM: ' ');
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
        $url = url('/api/v1/competitorgroupmaster?nick_name='.$nick_name.'&emp_code='.$emp_code);
        Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
        header("Content-type: application/text");
      	header("Content-Disposition: attachment; filename=competitor_group_master.txt");
      	print "$datacontents";
    }
    else{
      $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
      $url = url('/api/v1/competitorgroupmaster?nick_name='.$nick_name.'&emp_code='.$emp_code);
      Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
      return 404;
    }

  }


}
