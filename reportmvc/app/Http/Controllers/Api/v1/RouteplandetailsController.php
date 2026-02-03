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



class RouteplandetailsController extends Controller{

    /**
     * [dydb this function use to connect database on the flay]
     * @param  [varcar] $dbname [database name]
     * @return [object]         [databse connection object]
     */
    public function dydb($dbname){
      $otf = new DbOnTheFly(['database' => $dbname]);
      return $otf;
    }

    public function routeplandetailsincremental(Request $request){

      $nick_name=$request->nickname;
      $emp_code=$request->emp_code;
      $db_name='acedns_'.strtoupper($request->input('nickname'));
      $dydb =$this->dydb($db_name);
      $CUTDB = $dydb->getConnection();
      $verificationcode=$request->verificationcode;
      $isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
      $contents='';
      if($isverify==1){
        $rowrouteplandetails=DB::table('route_plan_details')
                          ->where('nick_name',$nick_name)
                          ->first();
        	$cnt=1;
        	$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
          if(count($rowrouteplandetails)>0){
        		$date=date('Y-m-d');
        		$time=date('H:i:s');
        		$contentsdatetime = $date.'€'.$time;
            $contents.="<data>";
      			$contents .='<route_plan_id><![CDATA['.mb_convert_encoding($rowrouteplandetails->route_plan_id, 'UTF-8', 'UTF-8').']]></route_plan_id>
      							<user_id><![CDATA['.mb_convert_encoding($rowrouteplandetails->user_id, 'UTF-8', 'UTF-8').']]></user_id>
      							<route_plan_access_period><![CDATA['.mb_convert_encoding($rowrouteplandetails->route_plan_access_period, 'UTF-8', 'UTF-8').']]></route_plan_access_period>
      							<route_plan_deviation><![CDATA['.mb_convert_encoding($rowrouteplandetails->route_plan_deviation, 'UTF-8', 'UTF-8').']]></route_plan_deviation>
      							<route_plan_approval><![CDATA['.mb_convert_encoding($rowrouteplandetails->route_plan_approval, 'UTF-8', 'UTF-8').']]></route_plan_approval>
      							<route_plan_flow><![CDATA['.mb_convert_encoding($rowrouteplandetails->route_plan_flow, 'UTF-8', 'UTF-8').']]></route_plan_flow>
      							<route_customer_planning><![CDATA['.mb_convert_encoding($rowrouteplandetails->route_customer_planning, 'UTF-8', 'UTF-8').']]></route_customer_planning>
      							<distributor_route_planning><![CDATA['.mb_convert_encoding($rowrouteplandetails->distributor_route_planning, 'UTF-8', 'UTF-8').']]></distributor_route_planning>
      							<distributor_route_planning_multiple><![CDATA['.mb_convert_encoding($rowrouteplandetails->distributor_route_planning_multiple, 'UTF-8', 'UTF-8').']]></distributor_route_planning_multiple>
      							<last_update_time><![CDATA['.mb_convert_encoding($contentsdatetime, 'UTF-8', 'UTF-8').']]></last_update_time>
      							';
      			$contents.="</data>";
            }
            $contents .= "</recordset>";
            $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
            $url = url('/api/v1/routeplandetails?nick_name='.$nick_name);
            Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
            return $contents;

        }
        else{
          $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
          $url = url('/api/v1/routeplandetails?nick_name='.$nick_name);
          Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
          return 404;
        }
    }

}
