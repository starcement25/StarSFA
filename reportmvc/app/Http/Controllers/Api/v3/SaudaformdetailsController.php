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

class SaudaformdetailsController extends Controller
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
    public function saudaformdetailsincremental(Request $request){
        $nick_name=Apicommonfunction::decrypt($request->nickname);
        $emp_code=Apicommonfunction::decrypt($request->emp_code);
        $db_name='acedns_'.strtoupper($nick_name);
        $dydb =$this->dydb($db_name);
        $CUTDB = $dydb->getConnection();
		$verificationcode=Apicommonfunction::decrypt($request->verificationcode);
        $isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
		if($isverify==1){ 
        $contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
        $rowssaudaformdetails=DB::table('sauda_form_details')
                          ->where('nick_name',$nick_name)
                          ->first();
        if(count($rowssaudaformdetails)>0){
          $date=date('Y-m-d');
      		$time=date('H:i:s');
      		$contentsdatetime = $date.'€'.$time;
          $contents.="<data>";
  				$contents .='<sauda_form_id><![CDATA['.mb_convert_encoding($rowssaudaformdetails->sauda_form_id, 'UTF-8', 'UTF-8').']]></sauda_form_id>
  							<user_id><![CDATA['.mb_convert_encoding($rowssaudaformdetails->user_id, 'UTF-8', 'UTF-8').']]></user_id>
  							<sauda_allocation_carry_forward><![CDATA['.mb_convert_encoding($rowssaudaformdetails->sauda_allocation_carry_forward, 'UTF-8', 'UTF-8').']]></sauda_allocation_carry_forward>
  							<sauda_depot_wise><![CDATA['.mb_convert_encoding($rowssaudaformdetails->sauda_depot_wise, 'UTF-8', 'UTF-8').']]></sauda_depot_wise>
  							<sauda_rate_variable><![CDATA['.mb_convert_encoding($rowssaudaformdetails->sauda_rate_variable, 'UTF-8', 'UTF-8').']]></sauda_rate_variable>
  							<sauda_rate_variable_value><![CDATA['.mb_convert_encoding($rowssaudaformdetails->sauda_rate_variable_value, 'UTF-8', 'UTF-8').']]></sauda_rate_variable_value>
  							<sauda_booked_through><![CDATA['.mb_convert_encoding($rowssaudaformdetails->sauda_booked_through, 'UTF-8', 'UTF-8').']]></sauda_booked_through>
  							<sauda_valid_from><![CDATA['.mb_convert_encoding($rowssaudaformdetails->sauda_valid_from, 'UTF-8', 'UTF-8').']]></sauda_valid_from>
  							<sauda_rate_dependent_on_despatch_point><![CDATA['.mb_convert_encoding($rowssaudaformdetails->sauda_rate_dependent_on_despatch_point, 'UTF-8', 'UTF-8').']]></sauda_rate_dependent_on_despatch_point>
  							<sauda_rate_dependent_on_despatch_point_val><![CDATA['.mb_convert_encoding($rowssaudaformdetails->sauda_rate_dependent_on_despatch_point_val, 'UTF-8', 'UTF-8').']]></sauda_rate_dependent_on_despatch_point_val>
  							<sauda_rate_dependent_on_despatch_point_verticlewise><![CDATA['.mb_convert_encoding($rowssaudaformdetails->sauda_rate_dependent_on_despatch_point_verticlewise, 'UTF-8', 'UTF-8').']]></sauda_rate_dependent_on_despatch_point_verticlewise>
  							<sauda_rate_dependent_on_despatch_point_verticle_val><![CDATA['.mb_convert_encoding($rowssaudaformdetails->sauda_rate_dependent_on_despatch_point_verticle_val, 'UTF-8', 'UTF-8').']]></sauda_rate_dependent_on_despatch_point_verticle_val>
  							<secondary_freight_vertical><![CDATA['.mb_convert_encoding($rowssaudaformdetails->secondary_freight_vertical, 'UTF-8', 'UTF-8').']]></secondary_freight_vertical>
  							<special_discount_vertical><![CDATA['.mb_convert_encoding($rowssaudaformdetails->special_discount_vertical, 'UTF-8', 'UTF-8').']]></special_discount_vertical>
  							<customer_email_check_vertical><![CDATA['.mb_convert_encoding($rowssaudaformdetails->customer_email_check_vertical, 'UTF-8', 'UTF-8').']]></customer_email_check_vertical>
					<incoterms_vertical><![CDATA['.mb_convert_encoding($rowssaudaformdetails->incoterms_vertical, 'UTF-8', 'UTF-8').']]></incoterms_vertical>
  							';
  				$contents.="</data>";
        }
       $contents .= "</recordset>";
       $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
       $url = url('/api/v3/saudaformdetails?nick_name='.$nick_name);
       Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
       return Apicommonfunction::encrypt($contents);
    }
	else
	{
		$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
        $url = url('/api/v3/saudaformdetails?nick_name='.$nick_name);
        Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
        return Apicommonfunction::encrypt('404');
	}
  }
}
