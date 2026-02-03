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


class LogodownloadController extends Controller
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
  public function logodownload(Request $request){
      $nick_name=$request->nickname;
      $db_name='acedns_'.strtoupper($request->input('nickname'));
      $dydb =$this->dydb($db_name);
      $CUTDB = $dydb->getConnection();
      $rowlogodetails=DB::table('user_details')
	  					->select('user_details.logo')
                        ->where('nick_name',$nick_name)
                        ->first();
      if(count($rowlogodetails)>0){
	    $logo=$rowlogodetails->logo;
		//echo $logourl=url('/logo/'.$logo);
		$logourl = "http://".$_SERVER['SERVER_NAME'].'/logo/'.$logo;
		//echo '<img src="'.$logourl.'">';
		header("Content-type: image/png"); 
		header("Content-Disposition: attachment; filename=$logo");
		//imagepng($im);
		//imagedestroy($im);
		readfile("$logourl");
    	}
    	else
    	{
    		echo '0';
    	}
  }
}
