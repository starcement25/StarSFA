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


class UserdetailsController extends Controller
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
  public function userdetailsincremental(Request $request){
      $nick_name=$request->nickname;
      $db_name='acedns_'.strtoupper($request->input('nickname'));
      $dydb =$this->dydb($db_name);
      $CUTDB = $dydb->getConnection();
      $rowsuserdetails=DB::table('user_details')
                        ->where('nick_name',$nick_name)
                        ->first();
      if(count($rowsuserdetails)>0){
        $date=date('Y-m-d');
    		$time=date('H:i:s');
    		$contentsdatetime = $date.'€'.$time;
    		$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
          $contents.="<data>";
          $contents .='<user_id><![CDATA['.mb_convert_encoding($rowsuserdetails->user_id, 'UTF-8', 'UTF-8').']]></user_id>
                <name><![CDATA['.mb_convert_encoding($rowsuserdetails->name, 'UTF-8', 'UTF-8').']]></name>
                <address><![CDATA['.mb_convert_encoding($rowsuserdetails->address, 'UTF-8', 'UTF-8').']]></address>
                <phone_no><![CDATA['.mb_convert_encoding($rowsuserdetails->phone_no, 'UTF-8', 'UTF-8').']]></phone_no>
                <email><![CDATA['.mb_convert_encoding($rowsuserdetails->email, 'UTF-8', 'UTF-8').']]></email>
                <license_key><![CDATA['.mb_convert_encoding($rowsuserdetails->license_key, 'UTF-8', 'UTF-8').']]></license_key>
                <no_users><![CDATA['.mb_convert_encoding($rowsuserdetails->no_users, 'UTF-8', 'UTF-8').']]></no_users>
                <nick_name><![CDATA['.mb_convert_encoding($rowsuserdetails->nick_name, 'UTF-8', 'UTF-8').']]></nick_name>
                <no_of_branches><![CDATA['.mb_convert_encoding($rowsuserdetails->no_of_branches, 'UTF-8', 'UTF-8').']]></no_of_branches>
                <email_hierarchywise><![CDATA['.mb_convert_encoding($rowsuserdetails->email_hierarchywise, 'UTF-8', 'UTF-8').']]></email_hierarchywise>
                <vertical_fields><![CDATA['.mb_convert_encoding($rowsuserdetails->vertical_fields, 'UTF-8', 'UTF-8').']]></vertical_fields>
                <vertical_fields_value><![CDATA['.mb_convert_encoding($rowsuserdetails->vertical_fields_value, 'UTF-8', 'UTF-8').']]></vertical_fields_value>
                <previous_stock><![CDATA['.mb_convert_encoding($rowsuserdetails->previous_stock, 'UTF-8', 'UTF-8').']]></previous_stock>
                <last_update_time><![CDATA['.mb_convert_encoding($contentsdatetime, 'UTF-8', 'UTF-8').']]></last_update_time>
                <multiple_prospect><![CDATA['.mb_convert_encoding($rowsuserdetails->multiple_prospect, 'UTF-8', 'UTF-8').']]></multiple_prospect>
                <multiple_prospect_value><![CDATA['.mb_convert_encoding($rowsuserdetails->multiple_prospect_value, 'UTF-8', 'UTF-8').']]></multiple_prospect_value>
                <stock_audit_scan><![CDATA['.mb_convert_encoding($rowsuserdetails->stock_audit_scan, 'UTF-8', 'UTF-8').']]></stock_audit_scan>
                <stock_audit_rate><![CDATA['.mb_convert_encoding($rowsuserdetails->stock_audit_rate, 'UTF-8', 'UTF-8').']]></stock_audit_rate>
                <location_drag_drop ><![CDATA['.mb_convert_encoding($rowsuserdetails->location_drag_drop, 'UTF-8', 'UTF-8').']]></location_drag_drop>
                <tour_plan_daywise><![CDATA['.mb_convert_encoding($rowsuserdetails->tour_plan_daywise, 'UTF-8', 'UTF-8').']]></tour_plan_daywise>
                <check_in_out_typeval><![CDATA['.mb_convert_encoding($rowsuserdetails->check_in_out_typeval, 'UTF-8', 'UTF-8').']]></check_in_out_typeval>
                <FCM><![CDATA['.mb_convert_encoding($rowsuserdetails->FCM, 'UTF-8', 'UTF-8').']]></FCM>
                <minimum_stock><![CDATA['.mb_convert_encoding($rowsuserdetails->minimum_stock, 'UTF-8', 'UTF-8').']]></minimum_stock>
                <stk_audit_unit><![CDATA['.mb_convert_encoding($rowsuserdetails->stk_audit_unit, 'UTF-8', 'UTF-8').']]></stk_audit_unit>
                <stk_audit_irrespective_routeplan><![CDATA['.mb_convert_encoding($rowsuserdetails->stk_audit_irrespective_routeplan, 'UTF-8', 'UTF-8').']]></stk_audit_irrespective_routeplan>
                <stk_audit_cust_type><![CDATA['.mb_convert_encoding($rowsuserdetails->stk_audit_cust_type, 'UTF-8', 'UTF-8').']]></stk_audit_cust_type>
                <notes_info_hint_remarks><![CDATA['.mb_convert_encoding($rowsuserdetails->notes_info_hint_remarks, 'UTF-8', 'UTF-8').']]></notes_info_hint_remarks>
                <notes_info_upload_photo><![CDATA['.mb_convert_encoding($rowsuserdetails->notes_info_upload_photo, 'UTF-8', 'UTF-8').']]></notes_info_upload_photo>
                ';
          $contents.="</data>";
        $contents .= "</recordset>";
        $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
    	  $url = url('/api/v1/userdetails?nick_name='.$nick_name);
        $emp_code='';
    		Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url,$nick_name);
    		return $contents;
    	}
    	else{
    		return '0';
    	}

  }


}
