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

class SaudaallocationAccessController extends Controller
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
    public function saudaallocationaccess(Request $request){
        $nick_name=Apicommonfunction::decrypt($request->nickname);
       	$emp_code=Apicommonfunction::decrypt($request->emp_code);
        $db_name='acedns_'.strtoupper($nick_name);
        $dydb =$this->dydb($db_name);
        $CUTDB = $dydb->getConnection();
		$verificationcode=Apicommonfunction::decrypt($request->verificationcode);
        $isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
		$linecontents='';
		$employeewise_hierarchy=Apicommonfunction::getNameTableMainDb('user_details','employeewise_hierarchy','nick_name',$nick_name);
		if($isverify==1){ 
		if($employeewise_hierarchy=='yes'){
             $employee_hierarchy=Apicommonfunction::return_employee_hierarchy($db_name,$emp_code);
             $emp_hierarchy_condition='emp_code IN('.$employee_hierarchy.')';
        }
		else{
		 $emp_hierarchy_condition="emp_code='".$emp_code."'";
		}
        $sqlquery=$CUTDB->select("SELECT emp_code,designation,flag,get_allocation FROM sauda_allocation_access WHERE ".$emp_hierarchy_condition);
       // echo count($sqlquery);
		if(count($sqlquery)>0){
			$contentsrowcolumn  =count($sqlquery).'##'.'4';
			foreach($sqlquery as $rowallocationaccess){
				$contents  = (($rowallocationaccess->emp_code!='')?$rowallocationaccess->emp_code: ' ')."^";
          	    $contents  .= (($rowallocationaccess->designation!='')?$rowallocationaccess->designation: ' ')."^";
				$contents  .= (($rowallocationaccess->flag!='')?$rowallocationaccess->flag: ' ')."^";
				$contents  .= (($rowallocationaccess->get_allocation!='')?$rowallocationaccess->get_allocation: ' ');

          		$linecontents  .= $contents."\n";
			}
			$datacontents = Apicommonfunction::encrypt($contentsrowcolumn."\n".str_replace("\r","",$linecontents));
            $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
            $url =url('/api/v3/saudaallocationaccess?nick_name='.$nick_name.'&emp_code='.$emp_code);
            Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
            return $datacontents;
        }
		else
		{
          	$datacontents = Apicommonfunction::encrypt('0'.'##'.'0');
            $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
            $url= url('/api/v1/saudaallocationaccess?nick_name='.$nick_name.'&emp_code='.$emp_code);
            Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
            return $datacontents;
		}
    }
	else
	{
		$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
        $url = url('/api/v3/saudaallocationaccess?nick_name='.$nick_name.'&emp_code='.$emp_code);
        Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
        return Apicommonfunction::encrypt('404');
	}
  }
}
