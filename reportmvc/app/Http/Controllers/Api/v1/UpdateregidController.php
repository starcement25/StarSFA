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


class UpdateregidController extends Controller
{

  /**
   * [dydb this function use to connect database on the flay]
   * @param  [varcar] $dbname [database name]
   * @return [object]         [databse connection object]
   */
  public function dydb($dbname)
  {
    $otf = new DbOnTheFly(['database' => $dbname]);
    return $otf;
  }
  public function Updateregid(Request $request){
      $nick_name=$request->nickname;
	  $emp_code=$request->emp_code;
	  $registrationid=$request->registrationid;
	  
      $db_name='acedns_'.strtoupper($request->input('nickname'));
      $dydb =$this->dydb($db_name);
      $CUTDB = $dydb->getConnection();
      
	  $sqlUpdate=$CUTDB->table('changepassword')
                       ->where('emp_code', $emp_code)
                       ->update(array('registrationid'=>$registrationid));
		if(count($sqlUpdate) >0)
		{
			echo "1";
		}
		else
		{
			echo "0";
		}
		$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
        $url = url('/api/v1/updateregistrationid?nick_name='.$nick_name.'&emp_code='.$emp_code.'&registrationid='.$registrationid);
        Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
  }
}
