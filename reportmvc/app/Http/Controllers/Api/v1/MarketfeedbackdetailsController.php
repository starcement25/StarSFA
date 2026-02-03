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

class MarketfeedbackdetailsController extends Controller{
  /**
   * [dydb this function use to connect database on the flay]
   * @param  [varcar] $dbname [database name]
   * @return [object]         [databse connection object]
   */
  public function dydb($dbname){
    $otf = new DbOnTheFly(['database' => $dbname]);
    return $otf;
  }

  public function marketfeedbackdetailsincremental(Request $request){
    $nick_name=$request->nickname;
    $emp_code=$request->emp_code;
    $db_name='acedns_'.strtoupper($request->input('nickname'));
    $dydb =$this->dydb($db_name);
    $CUTDB = $dydb->getConnection();
    $verificationcode=$request->verificationcode;
    $isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
    $contents='';
    if($isverify==1){
        $rowsmarketfeedbackdetails=DB::table('market_feedback_details')
                          ->where('nick_name',$nick_name)
                          ->first();
      	$cnt=1;
      	$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
        if(count($rowsmarketfeedbackdetails)>0){
      		$date=date('Y-m-d');
      		$time=date('H:i:s');
      		$contentsdatetime = $date.'€'.$time;
          $contents.="<data>";
    			$contents .='<market_feedback_id><![CDATA['.mb_convert_encoding($rowsmarketfeedbackdetails->market_feedback_id, 'UTF-8', 'UTF-8').']]></market_feedback_id>
    						<user_id><![CDATA['.mb_convert_encoding($rowsmarketfeedbackdetails->user_id, 'UTF-8', 'UTF-8').']]></user_id>
    						<mf_group_enable><![CDATA['.mb_convert_encoding($rowsmarketfeedbackdetails->mf_group_enable, 'UTF-8', 'UTF-8').']]></mf_group_enable>
    						<mf_col1><![CDATA['.mb_convert_encoding($rowsmarketfeedbackdetails->mf_col1, 'UTF-8', 'UTF-8').']]></mf_col1>
    						<mf_col2><![CDATA['.mb_convert_encoding($rowsmarketfeedbackdetails->mf_col2, 'UTF-8', 'UTF-8').']]></mf_col2>
    						<mf_col3><![CDATA['.mb_convert_encoding($rowsmarketfeedbackdetails->mf_col3, 'UTF-8', 'UTF-8').']]></mf_col3>
    						<mf_col4><![CDATA['.mb_convert_encoding($rowsmarketfeedbackdetails->mf_col4, 'UTF-8', 'UTF-8').']]></mf_col4>
    						<mf_sub_menu_details><![CDATA['.mb_convert_encoding($rowsmarketfeedbackdetails->mf_sub_menu_details, 'UTF-8', 'UTF-8').']]></mf_sub_menu_details>
    						<mf_sub_menu_image><![CDATA['.mb_convert_encoding($rowsmarketfeedbackdetails->mf_sub_menu_image, 'UTF-8', 'UTF-8').']]></mf_sub_menu_image>';
    			$contents.="</data>";
        }
        $contents .= "</recordset>";
        $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
        $url = url('/api/v1/marketfeedbackdetails?nick_name='.$nick_name);
        Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
        return $contents;
    }
    else{
      $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
      $url = url('/api/v1/marketfeedbackdetails?nick_name='.$nick_name);
      Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
      return 404;

    }
  }
}
