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

class ProductdetailsController extends Controller
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
          $rowsproductdetails=DB::table('menu_details')
                            ->where('nick_name',$nick_name)
                            ->first();
        	if(count($rowsproductdetails)>0){
        		$date=date('Y-m-d');
        		$time=date('H:i:s');
        		$contentsdatetime = $date.'€'.$time;
        		$contents.="<data>";
        		$contents .='<product_id><![CDATA['.mb_convert_encoding($rowsproductdetails->product_id, 'UTF-8', 'UTF-8').']]></product_id>
        							<user_id><![CDATA['.mb_convert_encoding($rowsproductdetails->user_id, 'UTF-8', 'UTF-8').']]></user_id>
        							<no_of_filter><![CDATA['.mb_convert_encoding($rowsproductdetails->no_of_filter, 'UTF-8', 'UTF-8').']]></no_of_filter>
        							<col1><![CDATA['.mb_convert_encoding($rowsproductdetails->col1, 'UTF-8', 'UTF-8').']]></col1>
        							<col2><![CDATA['.mb_convert_encoding($rowsproductdetails->col2, 'UTF-8', 'UTF-8').']]></col2>
        							<col3><![CDATA['.mb_convert_encoding($rowsproductdetails->col3, 'UTF-8', 'UTF-8').']]></col3>
        							<col4><![CDATA['.mb_convert_encoding($rowsproductdetails->col4, 'UTF-8', 'UTF-8').']]></col4>
        							<uom_wise_mrp><![CDATA['.mb_convert_encoding($rowsproductdetails->uom_wise_mrp, 'UTF-8', 'UTF-8').']]></uom_wise_mrp>
        							<branch_wise_mrp><![CDATA['.mb_convert_encoding($rowsproductdetails->branch_wise_mrp, 'UTF-8', 'UTF-8').']]></branch_wise_mrp>
        							<sauda_allocation_basedon_filter><![CDATA['.mb_convert_encoding($rowsproductdetails->sauda_allocation_basedon_filter, 'UTF-8', 'UTF-8').']]></sauda_allocation_basedon_filter>
        							<product_in_business_prospect><![CDATA['.mb_convert_encoding($rowsproductdetails->product_in_business_prospect, 'UTF-8', 'UTF-8').']]></product_in_business_prospect>
        							<branch_wise_product><![CDATA['.mb_convert_encoding($rowsproductdetails->branch_wise_product, 'UTF-8', 'UTF-8').']]></branch_wise_product>
        							<secondary_unit><![CDATA['.mb_convert_encoding($rowsproductdetails->secondary_unit, 'UTF-8', 'UTF-8').']]></secondary_unit>
        							<destination_price_list><![CDATA['.mb_convert_encoding($rowsproductdetails->destination_price_list, 'UTF-8', 'UTF-8').']]></destination_price_list>
        							<destination_ordertype_price_list><![CDATA['.mb_convert_encoding($rowsproductdetails->destination_ordertype_price_list, 'UTF-8', 'UTF-8').']]></destination_ordertype_price_list>
        							<state_wise_mrp><![CDATA['.mb_convert_encoding($rowsproductdetails->state_wise_mrp, 'UTF-8', 'UTF-8').']]></state_wise_mrp>
        							<multiple_rate><![CDATA['.mb_convert_encoding($rowsproductdetails->multiple_rate, 'UTF-8', 'UTF-8').']]></multiple_rate>
            					<product_qty_wise_TD><![CDATA['.mb_convert_encoding($rowsproductdetails->product_qty_wise_TD, 'UTF-8', 'UTF-8').']]></product_qty_wise_TD>
            					<focus_product><![CDATA['.mb_convert_encoding($rowsproductdetails->focus_product, 'UTF-8', 'UTF-8').']]></focus_product>
        							<last_update_time><![CDATA['.mb_convert_encoding($contentsdatetime, 'UTF-8', 'UTF-8').']]></last_update_time>';
        				$contents.="</data>";
        	}
        	$contents .= "</recordset>";
        	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
        	$url = url('/api/v1/menudetails?nick_name='.$nick_name.'&last_update_time='.$last_update_time.'&incremental_download='.$incremental_download.'&mode='.$mode);
        	Apicommonfunction::insertapilog($datetime,$emp_code,$url,$nick_name);
        	return $contents;
      }

}
