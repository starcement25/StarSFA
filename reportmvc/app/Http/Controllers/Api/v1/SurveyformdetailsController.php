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

class SurveyformdetailsController extends Controller
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
      public function productdetailsincremental(Request $request){
          $nick_name=$request->nickname;
          $emp_code=$request->emp_code;
          $last_update_time=$request->last_update_time;
          $last_update_time=str_replace('€',' ',$last_update_time);
          $incremental_download=$request->incremental_download;
          $db_name='acedns_'.strtoupper($request->input('nickname'));
          $dydb =$this->dydb($db_name);
          $CUTDB = $dydb->getConnection();
          $contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
          $rowssurveyformdetails=DB::table('survey_form_details')
                            ->where('nick_name',$nick_name)
                            ->first();
          if(count($rowssurveyformdetails)>0){
            $contents.="<data>";
      			$contents .='<survey_form_id><![CDATA['.mb_convert_encoding($rowssurveyformdetails->survey_form_id, 'UTF-8', 'UTF-8').']]></survey_form_id>
      						<user_id><![CDATA['.mb_convert_encoding($rowssurveyformdetails->user_id, 'UTF-8', 'UTF-8').']]></user_id>
      						<survey_menu><![CDATA['.mb_convert_encoding($rowssurveyformdetails->survey_menu, 'UTF-8', 'UTF-8').']]></survey_menu>
      						<survey_type><![CDATA['.mb_convert_encoding($rowssurveyformdetails->survey_type, 'UTF-8', 'UTF-8').']]></survey_type>
      						<survey_type_details><![CDATA['.mb_convert_encoding($rowssurveyformdetails->survey_type_details, 'UTF-8', 'UTF-8').']]></survey_type_details>
      						<mall_survey_relation><![CDATA['.mb_convert_encoding($rowssurveyformdetails->mall_survey_relation, 'UTF-8', 'UTF-8').']]></mall_survey_relation>
      						<survey_sub_type_details><![CDATA['.mb_convert_encoding($rowssurveyformdetails->survey_sub_type_details, 'UTF-8', 'UTF-8').']]></survey_sub_type_details>
      						<OTP><![CDATA['.mb_convert_encoding($rowssurveyformdetails->OTP, 'UTF-8', 'UTF-8').']]></OTP>
      						<survey_layer><![CDATA['.mb_convert_encoding($rowssurveyformdetails->survey_layer, 'UTF-8', 'UTF-8').']]></survey_layer>
      						<survey_submenu><![CDATA['.mb_convert_encoding($rowssurveyformdetails->survey_submenu, 'UTF-8', 'UTF-8').']]></survey_submenu>
      						<survey_submenu_details><![CDATA['.mb_convert_encoding($rowssurveyformdetails->survey_submenu_details, 'UTF-8', 'UTF-8').']]></survey_submenu_details>
      						<outlet_menu><![CDATA['.mb_convert_encoding($rowssurveyformdetails->outlet_menu, 'UTF-8', 'UTF-8').']]></outlet_menu>
      						<survey_route_plan><![CDATA['.mb_convert_encoding($rowssurveyformdetails->survey_route_plan, 'UTF-8', 'UTF-8').']]></survey_route_plan>
      						<other_text><![CDATA['.mb_convert_encoding($rowssurveyformdetails->other_text, 'UTF-8', 'UTF-8').']]></other_text>
      						<survey_report_row_id><![CDATA['.mb_convert_encoding($rowssurveyformdetails->survey_report_row_id, 'UTF-8', 'UTF-8').']]></survey_report_row_id>
      						<customer_email_update><![CDATA['.mb_convert_encoding($rowssurveyformdetails->customer_email_update, 'UTF-8', 'UTF-8').']]></customer_email_update>
      						';
      			$contents.="</data>";
          }
          $contents .= "</recordset>";
        	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
        	$url = url('/api/v1/surveyformdetails?nick_name='.$nick_name.'&last_update_time='.$last_update_time.'&incremental_download='.$incremental_download.'&mode='.$mode);
        	Apicommonfunction::insertapilog($datetime,$emp_code,$url,$nick_name);
        	return $contents;
      }

}
