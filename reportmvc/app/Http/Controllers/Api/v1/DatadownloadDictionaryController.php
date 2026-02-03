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

class DatadownloadDictionaryController extends Controller{
  /**
   * [dydb this function use to connect database on the flay]
   * @param  [varcar] $dbname [database name]
   * @return [object]         [databse connection object]
   */
  public function dydb($dbname){
    $otf = new DbOnTheFly(['database' => $dbname]);
    return $otf;
  }

  public function datadownloaddictionaryincremental(Request $request){
    $nick_name=$request->input('nickname');
    $emp_code=$request->emp_code;
    $device_id=$request->device_id;
    $incremental_download=$request->incremental_download;
    $last_update_time=$request->last_update_time;
    $last_update_time=str_replace('€',' ',$last_update_time);
    $verificationcode=$request->verificationcode;
    $db_name='acedns_'.strtoupper($request->input('nickname'));
    $dydb =$this->dydb($db_name);
    $CUTDB = $dydb->getConnection();
    $currenttimestamp=date('Y-m-d H:m:s');
    $emp_code_list='';
    $isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
      if($isverify==1){
        $date=gmdate('d',strtotime('+330 minute'));
        $month=gmdate('m',strtotime('+330 minute'));
        $year=gmdate('Y',strtotime('+330 minute'));

        $hour=gmdate('H',strtotime('+330 minute'));
        $minute=gmdate('i',strtotime('+330 minute'));
        $second=gmdate('s',strtotime('+330 minute'));
        $query_validate_datetime=$year.$month.$date;
        $contents='';
        $contents =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
        $sqlselectversion=$CUTDB->table('db_version')
                                ->select('version_code')
                                ->first();
        $versionCodecurrent=$sqlselectversion->version_code;
        $employeewise_hierarchy=Apicommonfunction::getNameTableMainDb('user_details','employeewise_hierarchy','nick_name',$nick_name);
        $vertical_fields=Apicommonfunction::getNameTableMainDb('user_details','vertical_fields','nick_name',$nick_name);
        $sale=Apicommonfunction::getNameTableMainDb('order_form_details','sale','nick_name',$nick_name);
        $route_plan=Apicommonfunction::getNameTableMainDb('menu_details','route_plan','nick_name',$nick_name);
        $sauda_allocation=Apicommonfunction::getNameTableMainDb('menu_details','sauda_allocation','nick_name',$nick_name);
        $survey=Apicommonfunction::getNameTableMainDb('menu_details','survey','nick_name',$nick_name);
        $sqlsurveyformdetails=DB::select("SELECT COUNT(survey_form_id) AS total_survey_form_details FROM survey_form_details
    						WHERE nick_name='".$nick_name."' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')")[0];
        $surveyformsetupcnt=$sqlsurveyformdetails->total_survey_form_details;
        if($surveyformsetupcnt>0){
          $survey_form_details_download='yes';
        }
        else{
          $survey_form_details_download='no';
        }
        $market_feedback=Apicommonfunction::getNameTableMainDb('menu_details','market_feedback','nick_name',$nick_name);

        if($employeewise_hierarchy=='yes'){
        	$sqlreportinglevel=$CUTDB->select("SELECT COUNT(emp_code) AS total_emp_code FROM employee_master WHERE FIND_IN_SET('".$emp_code."', reporting_to)")[0];
        	$reporting_level=$sqlreportinglevel->total_emp_code;
        }
        $sauda_booked_through=Apicommonfunction::getNameTableMainDb('sauda_form_details','sauda_booked_through','nick_name',$nick_name);
        $collection=Apicommonfunction::getNameTableMainDb('menu_details','collection','nick_name',$nick_name);
        $credit_limit=Apicommonfunction::getNameTableMainDb('order_form_details','credit_limit','nick_name',$nick_name);
        $outstanding=Apicommonfunction::getNameTableMainDb('menu_details','outstanding','nick_name',$nick_name);
        $outstanding_ageing=Apicommonfunction::getNameTableMainDb('menu_details','outstanding_ageing','nick_name',$nick_name);
        $no_of_filter=Apicommonfunction::getNameTableMainDb('product_details','no_of_filter','nick_name',$nick_name);
        $branch_wise_product=Apicommonfunction::getNameTableMainDb('product_details','branch_wise_product','nick_name',$nick_name);
        $branch_wise_mrp=Apicommonfunction::getNameTableMainDb('product_details','branch_wise_mrp','nick_name',$nick_name);
        $cl_stk=Apicommonfunction::getNameTableMainDb('order_form_details','cl_stk','nick_name',$nick_name);
        $mrp=Apicommonfunction::getNameTableMainDb('order_form_details','mrp','nick_name',$nick_name);
        $sale_rate=Apicommonfunction::getNameTableMainDb('order_form_details','sale_rate','nick_name',$nick_name);
        $sale_rate_input_dropdown=Apicommonfunction::getNameTableMainDb('order_form_details','sale_rate_input_dropdown','nick_name',$nick_name);
        $order=Apicommonfunction::getNameTableMainDb('menu_details','order','nick_name',$nick_name);
        $stk_audit=Apicommonfunction::getNameTableMainDb('menu_details','stk_audit','nick_name',$nick_name);
        $previous_stock=Apicommonfunction::getNameTableMainDb('user_details','previous_stock','nick_name',$nick_name);
        $route_customer_planning=Apicommonfunction::getNameTableMainDb('route_plan_details','route_customer_planning','nick_name',$nick_name);
        $tour_exp=Apicommonfunction::getNameTableMainDb('menu_details','tour_exp','nick_name',$nick_name);
        $loyalty=Apicommonfunction::getNameTableMainDb('menu_details','loyalty','nick_name',$nick_name);
        $sauda_depot_wise=Apicommonfunction::getNameTableMainDb('sauda_form_details','sauda_depot_wise','nick_name',$nick_name);
        $sauda_allocation_app=Apicommonfunction::getNameTableMainDb('menu_details','sauda_allocation_app','nick_name',$nick_name);
        $sauda_mis=Apicommonfunction::getNameTableMainDb('menu_details','sauda_mis','nick_name',$nick_name);
        $product_promotion=Apicommonfunction::getNameTableMainDb('menu_details','product_promotion','nick_name',$nick_name);
        $pending_contract=Apicommonfunction::getNameTableMainDb('menu_details','pending_contract','nick_name',$nick_name);
        $previous_order=Apicommonfunction::getNameTableMainDb('order_form_details','previous_order','nick_name',$nick_name);
        $order_status=Apicommonfunction::getNameTableMainDb('menu_details','order_status','nick_name',$nick_name);
        $sauda_outstanding=Apicommonfunction::getNameTableMainDb('menu_details','sauda_outstanding','nick_name',$nick_name);
        $sale_performance=Apicommonfunction::getNameTableMainDb('menu_details','sale_performance','nick_name',$nick_name);
        $destination_price_list=Apicommonfunction::getNameTableMainDb('product_details','destination_price_list','nick_name',$nick_name);
        $destination_ordertype_price_list=Apicommonfunction::getNameTableMainDb('product_details','destination_ordertype_price_list','nick_name',$nick_name);
        $destination=Apicommonfunction::getNameTableMainDb('order_form_details','destination','nick_name',$nick_name);
        $target_achievement=Apicommonfunction::getNameTableMainDb('menu_details','target_achievement','nick_name',$nick_name);
        $distributor_route_planning=Apicommonfunction::getNameTableMainDb('route_plan_details','distributor_route_planning','nick_name',$nick_name);
        $sqlmenudetails=DB::select("SELECT COUNT(menu_id) AS total_menus FROM menu_details
    						WHERE nick_name='".$nick_name."' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')")[0];
    		$menusetupcnt=$sqlmenudetails->total_menus;
    		if($menusetupcnt >0){
    			$menu_details_download="yes";
    		}
    		else{
    			$menu_details_download="no";
    		}
        if($incremental_download=='yes')
      	{
      		$sqluserdetails=DB::select("SELECT COUNT(user_id) AS total_users FROM user_details
      						WHERE nick_name='".$nick_name."' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')")[0];

      		$usersetupcnt=$sqluserdetails->total_users;

      		if($usersetupcnt >0){
      			$user_details_download="yes";
      		}
      		else{
      			$user_details_download="no";
      		}

      		$sqlmenudetails=DB::select("SELECT COUNT(menu_id) AS total_menus FROM menu_details
      						WHERE nick_name='".$nick_name."' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')")[0];
      		$menusetupcnt=$sqlmenudetails->total_menus;
      		if($menusetupcnt >0)
      		{
      			$menu_details_download="yes";
      		}
      		else{
      			$menu_details_download="no";
      		}

      		$sqlorderformdetails=DB::select("SELECT COUNT(order_form_id) AS total_order_form_details FROM order_form_details
      						WHERE nick_name='".$nick_name."' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')")[0];

      		$orderformsetupcnt=$sqlorderformdetails->total_order_form_details;

      		if($orderformsetupcnt >0){
      			$order_form_details_download="yes";
      		}
      		else{
      			$order_form_details_download="no";
      		}

      		$sqlproductdetails=DB::select("SELECT COUNT(product_id) AS total_product_details FROM product_details
      						WHERE nick_name='".$nick_name."' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')")[0];
      		$productsetupcnt=$sqlproductdetails->total_product_details;

      		if($productsetupcnt >0){
      			$product_details_download="yes";
      		}
      		else{
      			$product_details_download="no";
      		}

      		$sqlrouteplandetails=DB::select("SELECT COUNT(route_plan_id) AS total_route_plan_details FROM route_plan_details
      						WHERE nick_name='".$nick_name."' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')")[0];
      		$routeplansetupcnt=$sqlrouteplandetails->total_route_plan_details;
      		if($routeplansetupcnt >0){
      			$route_plan_details_download="yes";
      		}
      		else{
      			$route_plan_details_download="no";
      		}

      		$sqlsaudaformdetails=DB::select("SELECT COUNT(sauda_form_id) AS total_sauda_form_details FROM sauda_form_details
      						WHERE nick_name='".$nick_name."' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')")[0];

      		$saudaformsetupcnt=$sqlsaudaformdetails->total_sauda_form_details;
      		if($saudaformsetupcnt >0){
      			$sauda_form_details_download="yes";
      		}
      		else{
      			$sauda_form_details_download="no";
      		}

      		$sqlsurveyformdetails=DB::select("SELECT COUNT(survey_form_id) AS total_survey_form_details FROM survey_form_details
      						WHERE nick_name='".$nick_name."' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')")[0];

      		$surveyformsetupcnt=$sqlsurveyformdetails->total_survey_form_details;
      		if($surveyformsetupcnt >0){
      			$survey_form_details_download="yes";
      		}
      		else{
      			$survey_form_details_download="no";
      		}
      		$sqlmarketfeeddetails=DB::select("SELECT COUNT(market_feedback_id) AS total_market_feedback_details FROM market_feedback_details
      						WHERE nick_name='".$nick_name."' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')")[0];

      		$marketfeedbacksetupcnt=$sqlmarketfeeddetails->total_market_feedback_details;
      		if($marketfeedbacksetupcnt >0){
      			$market_feedback_details_download="yes";
      		}
      		else{
      			$market_feedback_details_download="no";
      		}
      		$sqlselfappdetails=DB::select("SELECT COUNT(self_appraisal_id) AS total_selfapp_details FROM self_appraisal_details
       							WHERE nick_name='".$nick_name."' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')")[0];
      		$selfappsetupcnt=$sqlselfappdetails->total_selfapp_details;
      		if($selfappsetupcnt >0){
      			$self_appraisal_details_download="yes";
      		}
      		else{
      			$self_appraisal_details_download="no";
      		}
      	}


        $server_current_date=$year.'-'.$month.'-'.$date;
        if($emp_code=='C0007'){
        		$emp_val_condition_audit="";
        		$emp_val_rds="";
        }
        else{
        	$emp_val_condition_audit=" AND CM.emp_code='".$emp_code."'";
        	if($employeewise_hierarchy=='yes'){
        		$employee_hierarchy=Apicommonfunction::return_employee_hierarchy($db_name,$emp_code);
        		$emp_val_rds=' AND (emp_code IN('.$employee_hierarchy.')';
        	}
        	else{
        		$emp_val_rds=" AND emp_code='".$emp_code."'";
        	}
        	if($sale=='yes'){
        		$sqlemp=$CUTDB->table('employee_master')
                          ->select('employee_master.emp_code')
                          ->join('branch_master', 'employee_master.branch_code', '=', 'branch_master.branch_code')
                          ->where('employee_master.emp_code',$emp_code)
                          ->get();
        		foreach($sqlemp as $rowemp){
        			$emp_code_list=$emp_code_list."'".$rowemp->emp_code."'".',';
        		}
        		$emp_code_list=substr($emp_code_list,0,-1);
        		if($emp_code_list!=''){
        			$emp_val_rds.=' OR emp_code IN('.$emp_code_list.'))';
        		}
        		else{
        			$emp_val_rds.=')';
        		}
        	}
        	else{
        		$emp_val_rds.=')';
        	}
        }
        $rowselect=$CUTDB->table('emp_data_update_log')
                         ->select('emp_code')
                         ->where('emp_code',$emp_code)
                         ->first();
          if(count($rowselect)>0){
            $sqlUpdate=$CUTDB->table('emp_data_update_log')
                        ->where('emp_code', $emp_code)
                        ->limit(1)
                        ->update(array('update_time'=>$currenttimestamp));
            if(count($sqlUpdate)>0){
              $successval="1";
            }
            else{
              $successval="0";
            }
          }
          else{
            $sqlInsert=$CUTDB->table('emp_data_update_log')->insert(array(
              'emp_code'=>$emp_code,
              'update_time'=>$currenttimestamp
            ));
            if(count($sqlInsert)>0){
              $successval="1";
            }
            else{
              $successval="0";
            }
          }
          $sqlmenuaccess=$CUTDB->table('menu_access')
                               ->select('not_accessible_menu')
                               ->where('emp_code',$emp_code)
                               ->get();
          $menu_access_array=array();
          if(count($sqlmenuaccess) >0){
          	foreach($sqlmenuaccess as $rowmenuaccess){
          		array_push($menu_access_array,$rowmenuaccess->not_accessible_menu);
          	}
          }
          $sqlselectuserdbversion=$CUTDB->table('table_structure_updation')
                                        ->select('is_update','db_version_code')
                                        ->where('device_id',$device_id)
                                        ->where('emp_code',$emp_code)
                                        ->first();
          if(count($sqlselectuserdbversion)>0){
            	$is_update=$sqlselectuserdbversion->is_update;
            	$user_db_version_code=$sqlselectuserdbversion->db_version_code;
            	if((($versionCodecurrent-$user_db_version_code)*10) > '1' && $is_update==1 && $incremental_download=='yes'){
            			$need_download_table_array=array();
            			$sqlquery=DB::table('app_db_update_execution')
                               ->select('table_name')
                               ->where('db_version','>',$user_db_version_code)
                               ->where('db_version','<=',$versionCodecurrent)
                               ->orderBy('app_db_u_exe_id','ASC')
                               ->get();
            			foreach($sqlquery as $rowstructuredetails){
            				array_push($need_download_table_array,$rowstructuredetails->table_name);
            			}

            		if(in_array("menu_details",$need_download_table_array)){
            			$contents  .= 'menu_details'."\n";
            		}
            		if(in_array("user_details",$need_download_table_array)){
            			$contents  .= 'user_details'."\n";
            		}
            		if(in_array("order_form_details",$need_download_table_array)){
            			$contents  .= 'order_details'."\n";
            		}
            		if(in_array("product_details",$need_download_table_array)){
            			$contents  .= 'product_details'."\n";
            		}
            		if($route_plan=='yes' && in_array("route_plan_details",$need_download_table_array)){
            			$contents  .= 'route_plan_details'."\n";
            		}
            		if($sauda_allocation=='yes' && in_array("sauda_form_details",$need_download_table_array)){
            			$contents  .= 'sauda_form_details'."\n";
            		}
            		if($survey=='yes' && in_array("survey_form_details",$need_download_table_array)){
            			$contents  .= 'survey_form_details'."\n";
            		}
            		else if($survey=='yes' && $survey_form_details_download=='yes'){
            				$contents  .= 'survey_form_details'."\n";
            		}
            		if($market_feedback=='yes' && in_array("market_feedback_details",$need_download_table_array)){
            			$contents  .= 'market_feedback_details'."\n";
            		}
            		if($sauda_allocation=='yes' && in_array("broker_master",$need_download_table_array)){
            			if(!in_array('sauda',$menu_access_array))
            			{
            				if($sauda_booked_through =='BROKER' || $sauda_booked_through =='BOTH'){
            					$contents  .= 'broker_master'."\n";
            				}
            			}
            		}
            		if(in_array("route_master",$need_download_table_array)){
            			$contents  .= 'route_master'."\n";
            		}
            		if($collection=='yes' && in_array("bank_master",$need_download_table_array)){
            			$contents  .= 'bank_master'."\n";
            		}
            		if(in_array("customer_master",$need_download_table_array)){
            			$contents  .= 'customer_master'."\n";
            			if($credit_limit=='yes'){
            				$contents  .= 'credit_limit'."\n";
            			}
            		}
            		if($collection=='yes' || $outstanding=='yes' || $outstanding_ageing=='yes'){
            			$contents  .= 'outstanding_master'."\n";
            		}
            		if($no_of_filter > 1 && in_array("product_group_master",$need_download_table_array)){
            			$contents  .= 'product_group_master'."\n";
            		}
            		if($no_of_filter > 2 && in_array("product_sub_group_master",$need_download_table_array)){
            			$contents  .= 'product_sub_group_master'."\n";
            		}
            		if($no_of_filter > 3 && in_array("product_brand_master",$need_download_table_array)){
            			$contents  .= 'product_brand_master'."\n";
            		}
            		if(in_array("product_master",$need_download_table_array)){
            			$contents  .= 'product_master'."\n";
            		}
            		if(($cl_stk=='yes' || $sale=='yes') && in_array("closing_stock",$need_download_table_array)) {
            			$contents  .= 'closing_stock'."\n";
            		}
            		if(($mrp=='yes' || ($sale_rate=='yes' && $sale_rate_input_dropdown=='dropdown')) && in_array("mrp",$need_download_table_array) && $order=='yes') {
            			$contents  .= 'mrp_master'."\n";
            		}
            		if(($mrp=='yes' || ($sale_rate=='yes' && $sale_rate_input_dropdown=='dropdown')) && in_array("sauda_mrp",$need_download_table_array) && $sauda_allocation=='yes') {
            			$contents  .= 'sauda_mrp'."\n";
            		}

            		if($stk_audit=='yes' && $previous_stock=='yes'){
            			$sqlprevstkcnt=$CUTDB->select("SELECT COUNT(PSCM.customer_code) AS total_prev_stk FROM prev_stock_counting_master PSCM,customer_master CM
            							WHERE CM.customer_code=PSCM.customer_code  ".$emp_val_rds."")[0];
            			$prevstkcnt=$sqlprevstkcnt->total_prev_stk;

            			if($prevstkcnt >0 && in_array("prev_stock_counting_master",$need_download_table_array))
            			{
            				$contents  .= 'prev_stock_counting_master'."\n";
            			}
            		}
            		if($route_plan=='yes'){
            			if(in_array('route_plan_transaction',$need_download_table_array)){
            				$contents  .= 'route_plan'."\n";
            			}
            			else{
            				$sqlqueryrouteplan=$CUTDB->select("SELECT COUNT(route_plan_trans_id) AS total_route_plan FROM route_plan WHERE
            				   emp_code='".$emp_code."' AND UNIX_TIMESTAMP(create_date) > UNIX_TIMESTAMP('".$last_update_time."')")[0];
            				$routeplancnt=$sqlqueryrouteplan->total_route_plan;
            				if($routeplancnt >0){
            					$contents  .= 'route_plan'."\n";
            				}
            			}
            			if($route_customer_planning=='yes'){
            				$sqlroutecustomerplancnt=$CUTDB->select("SELECT COUNT(route_plan_trans_id) AS total_route_customer_plan FROM route_customer_plan
            									WHERE SUBSTRING(route_plan_trans_id,3,5)='".$emp_code."'
            									AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')")[0];
            				$routecustomerplancnt=$rowroutecustomerplancnt->total_route_customer_plan;
            				if($routecustomerplancnt >0 && in_array("route_customer_plan_transaction",$need_download_table_array)){
            					$contents  .= 'route_customer_plan'."\n";
            				}
            			}
            		}
            		if($tour_exp=='yes'){
            			if(in_array("transport_mode_category",$need_download_table_array)){
            				$contents  .= 'travel_category'."\n";
            			}
            			if(in_array("transport_mode_sub_category",$need_download_table_array)){
            				$contents  .= 'travel_sub_category'."\n";
            			}
            		}
            		if($loyalty=='yes'){
            			if(in_array("loyalty_card_holder_master",$need_download_table_array)){
            				$contents  .= 'loyalty_customer'."\n";
            			}
            			$sqlqueryscheme=$CUTDB->select("SELECT COUNT(scheme_id) AS total_scheme_id FROM scheme_details")[0];
            			$total_scheme_cnt=$sqlqueryscheme->total_scheme_id;
            			if($total_scheme_cnt >0){
            					$contents  .= 'scheme_details'."\n";
            			}
            			$sqlqueryloyaltypurchase=$CUTDB->table('card_transaction')
                                                 ->select('loyalty_card_no')
                                                 ->first();

            			if(count($sqlqueryloyaltypurchase) >0){
            				if(in_array("loyalty_purchase_details",$need_download_table_array)){
            					$contents  .= 'loyalty_purchase_details'."\n";
            				}
            			}
            			$sqlqueryredeeme=$CUTDB->table('redeem_details')
                                         ->first();
            			if(count($sqlqueryredeeme) >0){
            					$contents  .= 'redeeme_details'."\n";
            			}
            		}
            		$sqlrds=$CUTDB->select("SELECT COUNT(rds_code) AS total_rds FROM rds_master WHERE 1  ".$emp_val_rds."")[0];
            		$rdscnt=$sqlrds->total_rds;

            		if($rdscnt >0){
            			if(in_array("rds_master",$need_download_table_array)){
            					$contents  .= 'rds_master'."\n";
            				}
            		}
            		if(in_array('emp_master',$need_download_table_array)){
            				$contents  .= 'emp_master'."\n";
            		}
            		else{
            			$sqlqueryemp=$CUTDB->select("SELECT COUNT(emp_code) AS total_emp FROM employee_master WHERE
            								UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('$last_update_time') $emp_val_rds");
            			$cntemp=$sqlqueryemp->total_emp;
            			if($cntemp >0){
            					$contents  .= 'emp_master'."\n";
            				}
            		}
            		if($survey=='yes'){
            			$contents  .= 'branch_master'."\n";
            		}
            		if($sale=='yes'){
            			$contents  .= 'branch_master'."\n";
            			$contents  .= 'vendor_master'."\n";

            			$sqlquerygit=$CUTDB->select("SELECT COUNT(GIT.grn_no) AS total_git FROM goods_in_transit GIT  WHERE GIT.receiver_code='".$emp_code."'")[0];
            			$gitcnt=$sqlquerygit->total_git;
            			if($gitcnt >0 && in_array("goods_in_transit",$need_download_table_array)){
            				$contents  .= 'git_master'."\n";
            			}
            			if($reporting_level >0){
            				if(in_array("mis_transaction_log",$need_download_table_array)){
            					$contents  .= 'mis_transaction_log'."\n";
            				}
            				$sqlrdslist=$CUTDB->select("SELECT rds_code FROM rds_master WHERE 1  ".$emp_val_rds."");
            				foreach($sqlrdslist as $rowrdslist){
            					$rds_list=$rds_list."'".$rowrdslist->rds_code."'".',';
            				}
            				$rds_list=substr($rds_list,0,-1);
            				$sqlchkdelete=$CUTDB->select("SELECT COUNT(transaction_id) AS no_of_trans_id FROM activity_log WHERE mis_updated_flag_app='0' AND (rds_code IN($rds_list) OR SUBSTRING(transaction_id,2,5) IN ($employee_hierarchy))");
            				$no_of_trans_id=$sqlchkdelete->no_of_trans_id;
            				if($no_of_trans_id >0){
            					$contents  .= 'mis_transaction_delete'."\n";
            				}
            			}
            			$contents  .= 'user_access'."\n";
            		}
            		if($sauda_allocation=='yes'){
            			if(!in_array('sauda',$menu_access_array)){
            				$contents  .= 'sauda_allocation'."\n";
            				if(in_array('order',$menu_access_array)){
            					$contents  .= 'branch_master'."\n";
            					if($sauda_depot_wise=='yes'){
            						if(in_array('customer_branch_relation',$need_download_table_array)){
            							$contents  .= 'customer_branch_relation'."\n";
            						}
            						else{
            							$sqlquerycustomerrds=$CUTDB->select("SELECT COUNT(CBR.customer_code) AS total_customer_depot FROM customer_branch_relation CBR,customer_master CM WHERE CM.customer_code=CBR.customer_code ".$emp_val_rds."
            										AND UNIX_TIMESTAMP(CBR.download_time) > UNIX_TIMESTAMP('".$last_update_time."')");
            							$customer_depot_cnt=$sqlquerycustomerrds->total_customer_depot;
            							if($customer_depot_cnt >0){
            								$contents  .= 'customer_branch_relation'."\n";
            							}
            						}
            					}
            				}
            			}
            			if(!in_array('sauda_allocation_app',$menu_access_array)){
            				$contents  .= 'sauda_allocation_access'."\n";
            				if($sauda_allocation_app=='yes'){
            					if(in_array('sauda_allocation_log',$need_download_table_array)){
            						$contents  .= 'sauda_allocation_log'."\n";
            					}
            					else{
            						$sqlsaudaallocation=$CUTDB->select("SELECT count(allocation_id) AS total_allocation FROM sauda_allocation_log
            										WHERE allocation_id<>'' AND UNIX_TIMESTAMP(allocation_date) > UNIX_TIMESTAMP('$last_update_time') ".$emp_val_rds."")[0];
            						$total_allocation_cnt=$sqlsaudaallocation->total_allocation;
            						if($total_allocation_cnt >0){
            							$contents  .='sauda_allocation_log'."\n";
            						}
            					}
            				}
            			}
            			if(!in_array('sauda_mis',$menu_access_array)){
            				if($sauda_mis=='yes'){
            					if(in_array('sauda_transaction_log',$need_download_table_array)){
            						$contents  .= 'sauda_transaction_log'."\n";
            					}
            					else{
            						$sqlsaudatransaction=$CUTDB->select("SELECT count(sauda_no) AS total_sauda_transaction FROM sauda_transaction_log
            										WHERE  UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('$last_update_time') ".$emp_val_rds."")[0];
            						$total_sauda_transaction=$sqlsaudatransaction->total_sauda_transaction;

            						if($total_sauda_transaction >0){
            							$contents  .='sauda_transaction_log'."\n";
            						}
            					}
            				}
            			}
            		}
            		if($survey=='yes'){
            		  if(in_array('survey_category_master',$need_download_table_array)){
            				$contents  .= 'survey_category_master'."\n";
            			}
            			else{
            				$sqlquerysurveycat=$CUTDB->select("SELECT COUNT(SCM.sub_cat_id) AS total_sub_cat_id FROM survey_category_master SCM WHERE
            									UNIX_TIMESTAMP(SCM.download_time) > UNIX_TIMESTAMP('$last_update_time')");
            				$sub_cat_id_cnt=$sqlquerysurveycat->total_sub_cat_id;
            				if($sub_cat_id_cnt >0){
            					$contents  .= 'survey_category_master'."\n";
            				}
            			 }

            			 if(in_array('table_view',$need_download_table_array)){
            				$contents  .= 'survey_table_view'."\n";
            			 }
            			else{
            				$sqlquerysurveytableview=$CUTDB->select("SELECT COUNT(row_id) AS total_survey_table_view FROM table_view WHERE
            									UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('$last_update_time')")[0];
            				$survey_table_view_cnt=$rowquerysurveytableview->total_survey_table_view;
            				if($survey_table_view_cnt >0){
            					$contents  .= 'survey_table_view'."\n";
            				}
            			 }
            			 if(in_array('survey_input',$need_download_table_array)){
            				$contents  .= 'survey_input_details'."\n";
            			 }
            			else{
            				$sqlquerysurveyinput=$CUTDB->select("SELECT COUNT(SI.row_id) AS total_survey_input_id FROM survey_input SI WHERE
            									UNIX_TIMESTAMP(SI.download_time) > UNIX_TIMESTAMP('$last_update_time')")[0];
            				$survey_input_cnt=$sqlquerysurveyinput->total_survey_input_id;
            				if($survey_input_cnt >0){
            					$contents  .= 'survey_input_details'."\n";
            				}
            			 }
            			 if(in_array('mall_master',$need_download_table_array) || $survey_type=='yes'){
            				$contents  .= 'mall_master'."\n";
            			 }
            			 if(in_array('mall_survey_relation',$need_download_table_array) || $survey_type=='yes'){
            				$contents  .= 'mall_survey_relation'."\n";
            			 }
            			 if(in_array('survey_publish',$need_download_table_array)){
            				 $contents  .= 'survey_publish'."\n";
            			 }
            			 else{
            				 $sqlsurveyidfetch=$CUTDB->select("SELECT SH.survey_id FROM survey_header SH,mall_emp_audit_relation MEAR WHERE
            							SH.mall_id=MEAR.mall_id AND SH.status='ready to publish' AND
            							UNIX_TIMESTAMP(SH.download_time) > UNIX_TIMESTAMP('$last_update_time') AND MEAR.emp_code='".$emp_code."'")[0];
            				 if(count($sqlsurveyidfetch) >0){
            					 $contents  .= 'survey_publish'."\n";
            				 }
            			 }
            			 if(in_array('fs_survey_publish',$need_download_table_array)){
            				 $contents  .= 'fs_survey_publish'."\n";
            			 }
            			 else{
            				 $sqlmallidfetch=$CUTDB->select("SELECT MER.mall_id,MM.mall_name FROM mall_emp_relation MER,mall_master MM
            								 WHERE MM.mall_id=MER.mall_id AND MER.status='assigned' AND MER.emp_code='".$emp_code."' ");
            				 if(count($sqlmallidfetch) >0){
            					 $countdata=0;
            					 foreach($sqlmallidfetch as $rowmallidfetch){
            						 $mall_id_fetch=$rowmallidfetch->mall_id;
            						 $mall_name_fetch=$rowmallidfetch->mall_name;
            						 $sqlquery=$CUTDB->select("SELECT * from foot_soldier  WHERE mall_id='".$mall_id_fetch."' AND DCE_status='NOT DONE' AND
            								UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')")[0];

            						 if(count($sqlquery) >0){
            							 $contents  .= 'fs_survey_publish'."\n";
            							 break;
            						 }
            					  }
            				 }
            			 }
            		}
            		if($product_promotion=='yes' || $market_feedback=='yes'){
            			$contents  .= 'generic_oil_master'."\n";
            		}
            		if($market_feedback=='yes'){
            			$contents  .= 'competitor_group_master'."\n";
            		}
            		$contents  .= 'menu_access'."\n";
            		if(!in_array('pending_contract',$menu_access_array)){
            			if($pending_contract=='yes' && in_array("pending_contract",$need_download_table_array)){
            				$sqlpendingcontractcnt=$CUTDB->select("SELECT COUNT(PC.customer_code) AS total_pending_contract FROM pending_contract_ageing PC,customer_master CM
            									 WHERE CM.customer_code=PC.customer_code ".$emp_val_rds."")[0];
            				$pendingcontractcnt=$sqlpendingcontractcnt->total_pending_contract;
            				if($pendingcontractcnt >0){
            				  $contents  .= 'pending_contract'."\n";
            				}
            			}
            		}
            		if(!in_array('order',$menu_access_array)){
            			if($order=='yes' && $previous_order=='yes'){
            				$sqlprevordercnt=$CUTDB->select("SELECT COUNT(POCM.customer_code) AS total_prev_order FROM prev_order_counting_master POCM,customer_master CM
            								WHERE CM.customer_code=POCM.customer_code  ".$emp_val_rds."")[0];
            				$prevordercnt=$sqlprevordercnt->total_prev_order;

            				if($prevordercnt >0 && in_array("prev_order_counting_master",$need_download_table_array))
            				{
            					$contents  .= 'prev_order_counting_master'."\n";
            				}
            			}
            		}
            		if(!in_array('order',$menu_access_array)){
            			if($order=='yes' && $order_status=='yes'){
            				$sqlorderstatus=$CUTDB->select("SELECT COUNT(POCM.customer_code) AS total_order_status FROM prev_order_counting_master POCM,customer_master CM
            								WHERE CM.customer_code=POCM.customer_code  ".$emp_val_rds."");
            				$total_order_status=$sqlorderstatus->total_order_status;
            				if($total_order_status >0 && in_array("order_status",$need_download_table_array)){
            					$contents  .= 'order_status'."\n";
            				}
            			}
            		}
            		if(!in_array('outstanding_ageing',$menu_access_array)){
            			if($sauda_outstanding=='yes'){
            				$sqloutstandingcnt=$CUTDB->select("SELECT COUNT(OA.customer_code) AS total_outstanding FROM outstanding_ageing OA,customer_master CM
            									 WHERE CM.customer_code=OA.customer_code ".$emp_val_rds."")[0];
            				$outstandingcnt=$sqloutstandingcnt->total_outstanding;
            				if(in_array('outstanding_ageing',$need_download_table_array)){
            					if($outstandingcnt >0){
            						$contents  .= 'outstanding_ageing'."\n";
            					}
            				}
            			}
            		}
            		if($sale_performance=='yes' && in_array("sale_performance_details",$need_download_table_array)){
            			$contents  .= 'sale_performance'."\n";
            		}
            		if($destination_price_list=='yes' || $destination_ordertype_price_list=='yes' || $destination=='yes'){
            			if(in_array('destination_master',$need_download_table_array)){
            				$contents  .= 'destination_master'."\n";
            			}
            			else{
            				$sqlquerydestination=$CUTDB->select("SELECT COUNT(destination_code) AS total_destination FROM destination_master WHERE
            								UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('$last_update_time')")[0];
            				$destinationcnt=$sqlquerydestination->total_destination;
            				if($destinationcnt >0){
            					$contents  .= 'destination_master'."\n";
            				}
            			}
            		}
            		if($target_achievement=='yes'){
            			$contents  .= 'target_achievement'."\n";
            		}
            		if($distributor_route_planning=='yes'){
            			if(in_array('distributor_route_relation',$need_download_table_array)){
            				$contents  .= 'distributor_route_relation'."\n";
            			}
            			else{
            				$sqlquerydistributorroute=$CUTDB->select("SELECT COUNT(distributor_code) AS total_distributor FROM distributor_route_relation WHERE 1 ".$emp_val_rds."
            				AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')")[0];
            				$distributorroutecnt=$sqlquerydistributorroute->total_distributor;
            				if($distributorroutecnt >0){
            					$contents  .= 'distributor_route_relation'."\n";
            				}
            			}
            		}
                $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
                $url = url('/api/v1/datadownloaddictionary?nick_name='.$nick_name);
                Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);

              	header("Content-type: application/text");
              	header("Content-Disposition: attachment; filename=datadownloaddictionary.txt");
              	print "$contents";
              	exit();
            	}
            }
            if($successval=="1"){
            	if($incremental_download=='no'){
            		$contents  .= 'menu_details'."\n";
            		$contents  .= 'user_details'."\n";
            		$contents  .= 'order_details'."\n";
            		$contents  .= 'product_details'."\n";
            		if($route_plan=='yes'){
            			$contents  .= 'route_plan_details'."\n";
            		}
            		if($sauda_allocation=='yes'){
            			$contents  .= 'sauda_form_details'."\n";
            		}
            		if($survey=='yes'){
            			$contents  .= 'survey_form_details'."\n";
            			$contents  .= 'survey_table_view'."\n";
            		}
            		if($market_feedback=='yes'){
            			$contents  .= 'market_feedback_details'."\n";
            		}
            		if($sauda_allocation=='yes'){
            			if(!in_array('sauda',$menu_access_array)){
            				if($sauda_booked_through =='BROKER' || $sauda_booked_through =='BOTH'){
            					$contents  .= 'broker_master'."\n";
            				}
            			}
            		}
            		$contents  .= 'route_master'."\n";
            		if($collection=='yes'){
            			$contents  .= 'bank_master'."\n";
            		}
            		$contents  .= 'customer_master'."\n";
            		if($credit_limit=='yes'){
            			$contents  .= 'credit_limit'."\n";
            		}
            		if($collection=='yes' || $outstanding=='yes' || $outstanding_ageing=='yes'){
            			$contents  .= 'outstanding_master'."\n";
            		}
            		if($no_of_filter > 1){
            			$contents  .= 'product_group_master'."\n";
            		}
            		if($no_of_filter > 2){
            			$contents  .= 'product_sub_group_master'."\n";
            		}
            		if($no_of_filter > 3){
            			$contents  .= 'product_brand_master'."\n";
            		}
            		$contents  .= 'product_master'."\n";
            		if($cl_stk=='yes' || $sale=='yes'){
            			$contents  .= 'closing_stock'."\n";
            		}
            		if(($mrp=='yes' || ($sale_rate=='yes' && $sale_rate_input_dropdown=='dropdown')) && $order=='yes'){
            		 $contents  .= 'mrp_master'."\n";
            		}
            		if(($mrp=='yes' || ($sale_rate=='yes' && $sale_rate_input_dropdown=='dropdown')) && $sauda_allocation=='yes') {
            			$contents  .= 'sauda_mrp'."\n";
            		}
            		if($stk_audit=='yes' && $previous_stock=='yes'){
            			$sqlprevstkcnt=$CUTDB->select("SELECT COUNT(PSCM.customer_code) AS total_prev_stk FROM prev_stock_counting_master PSCM,customer_master CM
            							WHERE CM.customer_code=PSCM.customer_code  ".$emp_val_rds."")[0];

            			$prevstkcnt=$sqlprevstkcnt->total_prev_stk;

            			if($prevstkcnt >0)
            			{
            				$contents  .= 'prev_stock_counting_master'."\n";
            			}
            		}
            		if($route_plan=='yes'){
            			//$contents  .= 'route_plan'."\n";
            			$sqlqueryrouteplan=$CUTDB->select("SELECT COUNT(route_plan_trans_id) AS total_route_plan FROM route_plan WHERE emp_code='".$emp_code."'")[0];
            			$routeplancnt=$rowqueryrouteplan->total_route_plan;

            			if($routeplancnt >0){
            				$contents  .= 'route_plan'."\n";
            			}

            			if($route_customer_planning=='yes'){
            				$sqlroutecustomerplancnt=$CUTDB->select("SELECT COUNT(route_plan_trans_id) AS total_route_customer_plan FROM route_customer_plan
            									WHERE SUBSTRING(route_plan_trans_id,3,5)='".$emp_code."'")[0];
            				$routecustomerplancnt=$sqlroutecustomerplancnt->total_route_customer_plan;
            				if($routecustomerplancnt >0){
            					$contents  .= 'route_customer_plan'."\n";
            				}
            			}
            		}
            		if($tour_exp=='yes'){
            			$contents  .= 'travel_category'."\n";
            			$contents  .= 'travel_sub_category'."\n";
            		}
            		if($loyalty=='yes'){
            			$contents  .= 'loyalty_customer'."\n";
            			$sqlqueryscheme=$CUTDB->select("SELECT COUNT(scheme_id) AS total_scheme_id FROM scheme_details")[0];
            			$total_scheme_cnt=$sqlqueryscheme->total_scheme_id;
            			if($total_scheme_cnt >0){
            				$contents  .= 'scheme_details'."\n";
            			}
            			$sqlqueryloyaltypurchase=$CUTDB->table('card_transaction')
                                                 ->select('loyalty_card_no')
                                                 ->first();
            			if(count($sqlqueryloyaltypurchase) >0){
            				$contents  .= 'loyalty_purchase_details'."\n";
            			}
            			$sqlqueryredeeme=$CUTDB->table('redeem_details')
                                                 ->select('sn')
                                                 ->first();
            			if(count($sqlqueryredeeme) >0){
            				$contents  .= 'redeeme_details'."\n";
            			}
            		}
            		$sqlrds=$CUTDB->select("SELECT COUNT(rds_code) AS total_rds FROM rds_master WHERE 1  ".$emp_val_rds."")[0];
            		$rdscnt=$sqlrds->total_rds;
            		if($rdscnt >0){
            			$contents  .= 'rds_master'."\n";
            		}
            		$sqlqueryemp=$CUTDB->select("SELECT COUNT(emp_code) AS total_emp FROM employee_master WHERE 1 ".$emp_val_rds."")[0];
            		$cntemp=$sqlqueryemp->total_emp;
            		if($cntemp >0){
            			$contents  .= 'emp_master'."\n";
            		}
            		if($survey=='yes'){
            			$sqlquerybranch=$CUTDB->select("SELECT COUNT(branch_code) AS total_branch FROM branch_master ")[0];
            			$branchcnt=$sqlquerybranch->total_branch;
            			if($branchcnt >0){
            				$contents  .= 'branch_master'."\n";
            			}
            		}
            		if($sale=='yes'){
            			$contents  .= 'branch_master'."\n";
            			$contents  .= 'vendor_master'."\n";
            			$sqlclstksales=$CUTDB->select("SELECT COUNT(OH.order_no) AS total_stk FROM order_header OH,location LO
            							WHERE LO.trans_id=OH.order_no AND
            							OH.transaction_type='CN' AND OH.customer_code='".$emp_code."'")[0];
            			$clstksalescnt=$sqlclstksales->total_stk;
            			if($clstksalescnt >0){
            				$contents  .= 'cl_stk_sales'."\n";
            			}
            			$sqlquerygit=$CUTDB->select("SELECT COUNT(GIT.grn_no) AS total_git FROM goods_in_transit GIT  WHERE GIT.receiver_code='".$emp_code."'")[0];
            			$gitcnt=$sqlquerygit->total_git;
            			if($gitcnt >0){
            				$contents  .= 'git_master'."\n";
            			}
            			if($reporting_level >0){
            				$contents  .= 'mis_transaction_log'."\n";

            				$sqlrdslist=$CUTDB->select("SELECT rds_code FROM rds_master WHERE 1  ".$emp_val_rds."");
            				foreach($sqlrdslist as $rowrdslist){
            					$rds_list=$rds_list."'".$rowrdslist->rds_code."'".',';
            				}
            				$rds_list=substr($rds_list,0,-1);
            				$sqlchkdelete=$CUTDB->select("SELECT COUNT(transaction_id) AS no_of_trans_id FROM activity_log WHERE mis_updated_flag_app='0' AND (rds_code IN($rds_list) OR SUBSTRING(transaction_id,2,5) IN ($employee_hierarchy))")[0];
            				$no_of_trans_id=$sqlchkdelete->no_of_trans_id;
            				if($no_of_trans_id >0){
            					$contents  .= 'mis_transaction_delete'."\n";
            				}
            			}
            			$contents  .= 'user_access'."\n";
            		}
            		if($sauda_allocation=='yes'){
            			if(!in_array('sauda',$menu_access_array)){
            				$contents  .= 'sauda_allocation'."\n";
            				if(in_array('order',$menu_access_array)){
            					$sqlquerybranch=$CUTDB->select("SELECT COUNT(branch_code) AS total_branch FROM branch_master ")[0];
            					$branchcnt=$sqlquerybranch->total_branch;
            					if($branchcnt >0){
            						$contents  .= 'branch_master'."\n";
            					}
            					if($sauda_depot_wise=='yes'){
            						$contents  .= 'customer_branch_relation'."\n";
            					}
            				}
            			}
            			if(!in_array('order',$menu_access_array)){
            				$sqlquerybranch=$CUTDB->select("SELECT COUNT(branch_code) AS total_branch FROM branch_master ")[0];
            				$branchcnt=$sqlquerybranch->total_branch;

            				if($branchcnt >0){
            					$contents  .= 'branch_master'."\n";
            				}
            				if($sauda_depot_wise=='yes'){
            					$contents  .= 'customer_branch_relation'."\n";
            				}
            			}
            			if(!in_array('sauda_allocation_app',$menu_access_array)){
            				$contents  .= 'sauda_allocation_access'."\n";
            				if($sauda_allocation_app=='yes'){
            					$sqlsaudaallocation=$CUTDB->select("SELECT count(allocation_id) AS total_allocation FROM sauda_allocation_log WHERE allocation_id<>'' ".$emp_val_rds."")[0];
            					$total_allocation_cnt=$sqlsaudaallocation->total_allocation;
            					if($total_allocation_cnt >0){
            						$contents  .='sauda_allocation_log'."\n";
            					}
            				}
            			}
            			if(!in_array('sauda_mis',$menu_access_array)){
            				if($sauda_mis=='yes'){
            					$sqlsaudatransaction=$CUTDB->select("SELECT count(sauda_no) AS total_sauda_transaction FROM sauda_transaction_log
            									WHERE 1 ".$emp_val_rds."")[0];
            					$total_sauda_transaction=$sqlsaudatransaction->total_sauda_transaction;
            					if($total_sauda_transaction >0){
            						$contents  .='sauda_transaction_log'."\n";
            					}
            				}
            			}
            		}
            		if($survey=='yes'){
            		  $sqlquerysurveycat=$CUTDB->select("SELECT COUNT(sub_cat_id) AS total_sub_cat_id FROM survey_category_master")[0];
            			$sub_cat_id_cnt=$sqlquerysurveycat->total_sub_cat_id;
            			if($sub_cat_id_cnt >0){
            		 		 $contents  .= 'survey_category_master'."\n";
            			}
            			$contents  .= 'survey_input_details'."\n";
            			if($survey_type=='yes'){
            				$contents  .= 'mall_master'."\n";
            				$contents  .= 'mall_survey_relation'."\n";
            			 }
            			 $sqlsurveyidfetch=$CUTDB->select("SELECT SH.survey_id FROM survey_header SH,mall_emp_audit_relation MEAR WHERE
            						SH.mall_id=MEAR.mall_id AND SH.status='ready to publish' AND
            						UNIX_TIMESTAMP(SH.download_time) > UNIX_TIMESTAMP('$last_update_time') AND MEAR.emp_code='".$emp_code."'");

            			 if(count($sqlsurveyidfetch) >0){
            				 $contents  .= 'survey_publish'."\n";
            			 }

            			 $sqlmallidfetch=$CUTDB->select("SELECT MER.mall_id,MM.mall_name FROM mall_emp_relation MER,mall_master MM
            								 WHERE MM.mall_id=MER.mall_id AND MER.status='assigned' AND MER.emp_code='".$emp_code."' ");
            			 if(count($sqlmallidfetch) >0){
            				 foreach($sqlmallidfetch as $rowmallidfetch){
            					 $mall_name_fetch=$rowmallidfetch->mall_name;
            					 $mall_id_fetch=$rowmallidfetch->mall_id;
            					 $sqlquery=$CUTDB->select("SELECT * from foot_soldier  WHERE mall_id='".$mall_id_fetch."' AND DCE_status='NOT DONE' AND
            							UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')");

            					 if(count($sqlquery) >0){
            						 $contents  .= 'fs_survey_publish'."\n";
            						 break;
            					 }
            				  }
            			 }

            		}
            		if($product_promotion=='yes'  || $market_feedback=='yes'){
            			$contents  .= 'generic_oil_master'."\n";
            		}
            		if($market_feedback=='yes'){
            			$contents  .= 'competitor_group_master'."\n";
            		}
            		$contents  .= 'menu_access'."\n";
            		if(!in_array('pending_contract',$menu_access_array)){
            			if($pending_contract=='yes'){
            				$sqlpendingcontractcnt=$CUTDB->select("SELECT COUNT(PC.customer_code) AS total_pending_contract FROM pending_contract_ageing PC,customer_master CM
            									 WHERE CM.customer_code=PC.customer_code ".$emp_val_rds."")[0];
            				$pendingcontractcnt=$sqlpendingcontractcnt->total_pending_contract;

            				if($pendingcontractcnt >0){
            					$contents  .= 'pending_contract'."\n";
            				}
            			}
            		}
            		if(!in_array('order',$menu_access_array))
            		{
            			if($order=='yes' && $previous_order=='yes'){
            				$sqlprevordercnt=$CUTDB->select("SELECT COUNT(POCM.customer_code) AS total_prev_order FROM prev_order_counting_master POCM,customer_master CM
            								WHERE CM.customer_code=POCM.customer_code  ".$emp_val_rds."");
            				$prevordercnt=$sqlprevordercnt->total_prev_order;

            				if($prevordercnt >0){
            					$contents  .= 'prev_order_counting_master'."\n";
            				}
            			}
            		}
            		if(!in_array('order',$menu_access_array)){
            			if($order=='yes' && $order_status=='yes'){
            				$sqlorderstatus=$CUTDB->select("SELECT COUNT(POCM.customer_code) AS total_order_status FROM prev_order_counting_master POCM,customer_master CM
            								WHERE CM.customer_code=POCM.customer_code ".$emp_val_rds."");
            				$total_order_status=$sqlorderstatus->total_order_status;
            				if($total_order_status >0){
            					$contents  .= 'order_status'."\n";
            				}
            			}
            		}
            		if(!in_array('outstanding_ageing',$menu_access_array)){
            			if($sauda_outstanding=='yes'){
            			    $sqloutstandingcnt=$CUTDB->select("SELECT COUNT(OA.customer_code) AS total_outstanding FROM outstanding_ageing OA,customer_master CM
            									 WHERE CM.customer_code=OA.customer_code ".$emp_val_rds."")[0];
            				  $outstandingcnt=$sqloutstandingcnt->total_outstanding;
              				if($outstandingcnt >0){
              					$contents  .= 'outstanding_ageing'."\n";
              				}
            			}
            		}
            		if($sale_performance=='yes'){
            			$contents  .= 'sale_performance'."\n";
            		}
            		if($destination_price_list=='yes' || $destination_ordertype_price_list=='yes' || $destination=='yes'){
            			$sqlquerydestination=$CUTDB->select("SELECT COUNT(destination_code) AS total_destination FROM destination_master")[0];
            			$destinationcnt=$sqlquerydestination->total_destination;

            			if($destinationcnt >0){
            				$contents  .= 'destination_master'."\n";
            			}
            		}
            		if($target_achievement=='yes'){
            			$contents  .= 'target_achievement'."\n";
            		}
            		if($distributor_route_planning=='yes'){
            			$sqlquerydistributorroute=$CUTDB->select("SELECT COUNT(distributor_code) AS total_distributor FROM distributor_route_relation WHERE 1 ".$emp_val_rds."")[0];
            			$distributorroutecnt=$sqlquerydistributorroute->total_distributor;

            			if($distributorroutecnt >0){
            				$contents  .= 'distributor_route_relation'."\n";
            			}
            		}
            	}
            	else{
            		$sqlselect=$CUTDB->select("SELECT is_update,db_version_code FROM table_structure_updation  WHERE device_id='".$device_id."' and emp_code='".$emp_code."'")[0];
            		$need_update_table_array=array();
            		if(COUNT($sqlselect)>0){

            			$is_update=$sqlselect->is_update;
            			$db_version_code=$sqlselect->db_version_code;
            			if($is_update==1){
            				$sqlquery=$CUTDB->select("SELECT table_name FROM table_structure_master WHERE need_update='Y' ORDER BY t_structure_id");
            				foreach($sqlquery as $rowquery){
            					array_push($need_update_table_array,$rowquery->table_name);
            				}
            			}
            		}
            		if($vertical_fields=='yes'){
            			$sqlempvertical=$CUTDB->table('employee_master')
                                        ->select('vertical_value')
                                        ->where('emp_code',$emp_code)
                                        ->first();
            			$emp_vertical_value=$sqlempvertical->vertical_value;
            			$emp_vertical_value_array=explode(',',$emp_vertical_value);
            			$condition_verticle=" AND (";
            			$condition_verticle_one=" AND (";
            			$condition_verticle_two=" AND (";
            			$condition_two='';
            			$condition_three='';
            			$condition_four='';
            			foreach($emp_vertical_value_array as $emp_vertical_values){
            				$condition_two.=" FIND_IN_SET( '".$emp_vertical_values."',vertical_value) OR";
            				$condition_three.=" FIND_IN_SET( '".$emp_vertical_values."',PM.vertical_value) OR";
            				$condition_four.=" FIND_IN_SET( '".$emp_vertical_values."',MRP.vertical_value) OR";
            			}
            			$condition_two=substr($condition_two,0,-2);
            			$condition_verticle.=$condition_two.")";
            			$condition_three=substr($condition_three,0,-2);
            			$condition_verticle_one.=$condition_three.")";
            			$condition_four=substr($condition_four,0,-2);
            			$condition_verticle_two.=$condition_four.")";
            		}
            		else{
            			$condition_verticle="";
            			$emp_vertical_value="";
            		}
            		//Set up tables download checking
            		if(in_array('menu_details',$need_update_table_array)){
            			$contents  .= 'menu_details'."\n";
            		}
            		else{
            			if($menu_details_download=='yes'){
            				$contents  .= 'menu_details'."\n";
            			}
            		}
            		if(in_array('user_details',$need_update_table_array)){
            			$contents  .= 'user_details'."\n";
            		}
            		else{
            			if($user_details_download=='yes'){
            				$contents  .= 'user_details'."\n";
            			}
            		}
            		if(in_array('order_form_details',$need_update_table_array)){
            			$contents  .= 'order_details'."\n";
            		}
            		else{
            			if($order_form_details_download=='yes'){
            				$contents  .= 'order_details'."\n";
            			}
            		}
            		if(in_array('product_details',$need_update_table_array)){
            			$contents  .= 'product_details'."\n";
            		}
            		else{
            			if($product_details_download=='yes'){

            				$contents  .= 'product_details'."\n";
            			}
            		}
            		if($route_plan=='yes'){
            			if(in_array('route_plan_details',$need_update_table_array)){
            				$contents  .= 'route_plan_details'."\n";
            			}
            			else{
            				if($route_plan_details_download=='yes'){
            					$contents  .= 'route_plan_details'."\n";
            				}
            			}
            		}
            		if($sauda_allocation=='yes'){
            			if(in_array('sauda_form_details',$need_update_table_array)){
            				$contents  .= 'sauda_form_details'."\n";
            			}
            			else{
            				if($sauda_form_details_download=='yes'){
            					$contents  .= 'sauda_form_details'."\n";
            				}
            			}
            		}
            		if($survey=='yes'){
            			if(in_array('survey_form_details',$need_update_table_array)){
            				$contents  .= 'survey_form_details'."\n";
            			}
            			else{
            				if($survey_form_details_download=='yes'){
            					$contents  .= 'survey_form_details'."\n";
            				}
            			}
            		}
            		if($market_feedback=='yes'){
            			if(in_array('market_feedback_details',$need_update_table_array)){
            				$contents  .= 'market_feedback_details'."\n";
            			}
            			else{
            				if($market_feedback_details_download=='yes'){
            					$contents  .= 'market_feedback_details'."\n";
            				}
            			}
            		}
            		if($sauda_allocation=='yes'){
            			if(!in_array('sauda',$menu_access_array)){
            				if($sauda_booked_through =='BROKER' || $sauda_booked_through =='BOTH'){
            					if(in_array('broker_master',$need_update_table_array)){
            						$contents  .= 'broker_master'."\n";
            					}
            					else{
            						$sqlbrokercnt=$CUTDB->select("SELECT COUNT(broker_id) AS total_broker FROM broker_master
            									WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')")[0];
            						$brokercnt=$sqlbrokercnt->total_broker;
            						if($brokercnt >0){
            							$contents  .= 'broker_master'."\n";
            						}
            					}
            				}
            			}
            		}
            		if($emp_code=='C0007'){
            			$emp_val_condition="";
            		}
            		else{
            			if($employeewise_hierarchy=='yes'){
            				$employee_hierarchy=Apicommonfunction::return_employee_hierarchy($db_name,$emp_code);
            				$emp_val_condition=' AND emp_code IN('.$employee_hierarchy.')';
            				$emp_val_condition_route=' AND CM.emp_code IN('.$employee_hierarchy.')';
            				$emp_val_condition_distributor_route=' AND DRR.emp_code IN('.$employee_hierarchy.')';
            			}
            			else{
            				$emp_val_condition=" AND emp_code='".$emp_code."'";
            				$emp_val_condition_route=" AND CM.emp_code='".$emp_code."'";
            				$emp_val_condition_distributor_route=" AND DRR.emp_code='".$emp_code."'";
            			}
            		}
            		if($nick_name=='EMAMI' || $nick_name=='EMAMIT'){
            			if(in_array('order',$menu_access_array)){
            				$customer_type_condition=" AND CM.cust_type='D'";
            			}
            			else{
            				$customer_type_condition=" AND CM.cust_type IN('R','D')";
            			}
            		}
            		else{
            			$customer_type_condition='';
            		}
            		//route download checking
            		if(in_array('route_master',$need_update_table_array)){
            			$contents  .= 'route_master'."\n";
            		}
            		else{
            			if($distributor_route_planning=='no'){
            				$sqlroutecnt=$CUTDB->select("select RM.route_code from route_master RM,customer_master CM WHERE RM.route_code=CM.route_code AND
            							  UNIX_TIMESTAMP(CM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')
            							  ".$customer_type_condition.$emp_val_condition_route." GROUP BY RM.route_code");
            				if(count($sqlroutecnt) >0){
            					$contents  .= 'route_master'."\n";
            				}
            			}
            			else{
            				$sqlroutecnt=$CUTDB->select("select RM.route_code from route_master RM,distributor_route_relation DRR WHERE RM.route_code=DRR.route_code AND
            							  UNIX_TIMESTAMP(RM.download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_condition_distributor_route."
            							  GROUP BY RM.route_code");
            				if(count($sqlroutecnt) >0){
            					$contents  .= 'route_master'."\n";
            				}
            			}
            		}
            		//bank download checking
            		if($collection=='yes'){
            			if(in_array('bank_master',$need_update_table_array)){
            				$contents  .= 'bank_master'."\n";
            			}
            			else{
            				$sqlbankcnt=$CUTDB->select("SELECT COUNT(bank_id) AS total_bank FROM bank_master
            								WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('$last_update_time')")[0];
            				$bankcnt=$sqlbankcnt->total_bank;
            				if($bankcnt >0){
            					$contents  .= 'bank_master'."\n";
            				}
            			}
            		}
            		//customer download checking
            		if(in_array('customer_master',$need_update_table_array)){
            			$contents  .= 'customer_master'."\n";
            		}
            		else{
            			$sqlcustomercnt=$CUTDB->select("SELECT COUNT(CM.customer_code) AS total_customer FROM customer_master CM
            							WHERE UNIX_TIMESTAMP(CM.download_time) > UNIX_TIMESTAMP('$last_update_time') ".$customer_type_condition.$emp_val_condition."")[0];
            			$customercnt=$sqlcustomercnt->total_customer;

            			if($customercnt >0){
            				$contents  .= 'customer_master'."\n";
            			}
            		}
            		// customer credit limit download checking
            		if($credit_limit=='yes'){
            			$sqlcustomercreditcnt=$CUTDB->select("SELECT COUNT(customer_code) AS total_customer_credit FROM customer_master
            										WHERE UNIX_TIMESTAMP(download_time_credit_limit) > UNIX_TIMESTAMP('$last_update_time') ".$emp_val_condition."")[0];

            			$customercreditcnt=$sqlcustomercreditcnt->total_customer_credit;

            			if($customercreditcnt >0){
            				$contents  .= 'credit_limit'."\n";
            			}
            		}
            		if($collection=='yes' || $outstanding=='yes' || $outstanding_ageing=='yes'){
            			$contents  .= 'outstanding_master'."\n";
            		}
            		//product download checking
            		if($no_of_filter > 1){
            			if(in_array('product_group_master',$need_update_table_array)){
            				$contents  .= 'product_group_master'."\n";
            			}
            			else{
            				if($nick_name=='RUPA' && substr($emp_vertical_value,0,1)=='M'){  //For RUPA M'SERIES
            					$sqlprodgroupcnt=$CUTDB->select("SELECT COUNT(product_group_code) AS total_product_group  FROM product_group_master WHERE vertical_value
            								LIKE 'M%' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('$last_update_time')")[0];
            				}
            				else{
            					$sqlprodgroupcnt=$CUTDB->select("SELECT COUNT(product_group_code) AS total_product_group FROM product_group_master
            									WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('$last_update_time') ".$condition_verticle."")[0];
            				}

            				$prodgroupcnt=$sqlprodgroupcnt->total_product_group;

            				if($prodgroupcnt >0){
            					$contents  .= 'product_group_master'."\n";
            				}
            			}
            		}
            		if($no_of_filter > 2){
            			if(in_array('product_sub_group_master',$need_update_table_array)){
            				$contents  .= 'product_sub_group_master'."\n";
            			}
            			else{
            				$sqlprodsubgroupcnt=$CUTDB->select("SELECT COUNT(product_sub_group_code) AS total_product_sub_group FROM product_sub_group_master
            								WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('$last_update_time') ".$condition_verticle."")[0];
            				$prodsubgroupcnt=$sqlprodsubgroupcnt->total_product_sub_group;

            				if($prodsubgroupcnt >0){
            					$contents  .= 'product_sub_group_master'."\n";
            				}
            			}
            		}
            		if($no_of_filter > 3){
            			if(in_array('product_brand_master',$need_update_table_array)){
            				$contents  .= 'product_brand_master'."\n";
            			}
            			else{
            				$sqlprodbrandcnt=$CUTDB->select("SELECT COUNT(product_brand_code) AS total_product_brand FROM product_brand_master
            								WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('$last_update_time') ".$condition_verticle."")[0];
            				$prodbrandcnt=$sqlprodbrandcnt->total_product_brand;
            				if($prodbrandcnt >0){
            					$contents  .= 'product_brand_master'."\n";
            				}
            			}
            		}

            		if(in_array('product_master',$need_update_table_array)){
            				$contents  .= 'product_master'."\n";
            			}
            		else{
            			if($branch_wise_product=='yes'){
            			    $sqlprodcnt=$CUTDB->select("SELECT COUNT(PM.prod_code) AS total_product FROM product_master PM,employee_master EM
            							WHERE FIND_IN_SET(PM.branch_code,EM.branch_code) AND EM.emp_code='".$emp_code."' AND
            							UNIX_TIMESTAMP(PM.download_time) > UNIX_TIMESTAMP('$last_update_time') ".$condition_verticle_one."")[0];

            				  $prodcnt=$sqlprodcnt->total_product;
            			}
            			else{
            				$sqlprodcnt=$CUTDB->select("SELECT COUNT(prod_code) AS total_product FROM product_master
            							WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('$last_update_time') $condition_verticle")[0];

            				$prodcnt=$sqlprodcnt->total_product;
            			}
            			if($prodcnt >0){
            				$contents  .= 'product_master'."\n";
            			}
            		}
            		//product closing stock download checking
            		if($cl_stk=='yes' || $sale=='yes'){
            			if(in_array('closing_stock',$need_update_table_array)){
            				$contents  .= 'closing_stock'."\n";
            			}
            		  else{
            			if($cl_stk=='yes'){
            				if($branch_wise_cl_stk=='yes'){
            					$sqlprodstkcnt=$CUTDB->select("SELECT COUNT(product_code) AS total_product_stk FROM branch_product_wise_stock
            								WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('$last_update_time') AND product_code!=''")[0];

            					$prodstkcnt=$sqlprodstkcnt->total_product_stk;
            					if($prodstkcnt >0){
            						$contents  .= 'closing_stock'."\n";
            					}
            				}
            				else{
            					$sqlprodstkcnt=$CUTDB->select("SELECT COUNT(prod_code) AS total_product_stk FROM product_master
            								WHERE UNIX_TIMESTAMP(download_time_cl_stk) > UNIX_TIMESTAMP('$last_update_time')")[0];
            					$prodstkcnt=$sqlprodstkcnt->total_product_stk;
            					if($prodstkcnt >0){
            						$contents  .= 'closing_stock'."\n";
            					}
            				}
            			}
            			if($sale=='yes'){

            					$contents  .= 'closing_stock'."\n";

            			}
            		  }
            		}
            		//mrp download checking
            		if(($mrp=='yes' || ($sale_rate=='yes' && $sale_rate_input_dropdown=='dropdown')) && $order=='yes'){
            			if(in_array('mrp',$need_update_table_array)){
            				$contents  .= 'mrp_master'."\n";
            			}
            			else{
            				if($branch_wise_mrp=='yes'){
            					$sqlmrpcnt=$CUTDB->select("SELECT COUNT(MRP.mrp_code) AS total_mrp FROM mrp MRP,employee_master EM WHERE
            								FIND_IN_SET(MRP.branch_code,EM.branch_code) AND EM.emp_code='".$emp_code."' AND
            								UNIX_TIMESTAMP(MRP.download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$condition_verticle_two."")[0];

            					$mrpcnt=$sqlmrpcnt->total_mrp;
            				}
            				else{
            					$sqlmrpcnt=$CUTDB->select("SELECT COUNT(mrp_code) AS total_mrp FROM mrp
            								WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('$last_update_time') ".$condition_verticle."")[0];

            					$mrpcnt=$sqlmrpcnt->total_mrp;
            				}

            				if($mrpcnt >0){
            					$contents  .= 'mrp_master'."\n";
            				}
            			}
            		}
            		if(($mrp=='yes' || ($sale_rate=='yes' && $sale_rate_input_dropdown=='dropdown')) && $sauda_allocation=='yes'){
            			if(in_array('sauda_mrp',$need_update_table_array)){
            				$contents  .= 'sauda_mrp'."\n";
            			}
            			else{
            				if($branch_wise_mrp=='yes'){
            					$sqlmrpcnt=$CUTDB->select("SELECT COUNT(MRP.mrp_code) AS total_mrp FROM sauda_mrp MRP,employee_master EM WHERE
            								FIND_IN_SET(MRP.branch_code,EM.branch_code) AND EM.emp_code='".$emp_code."' AND
            								UNIX_TIMESTAMP(MRP.download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$condition_verticle_two."")[0];

            					$mrpcnt=$sqlmrpcnt->total_mrp;
            				}
            				else{
            					$sqlmrpcnt=$CUTDB->select("SELECT COUNT(mrp_code) AS total_mrp FROM sauda_mrp
            								WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('$last_update_time') ".$condition_verticle."")[0];

            					$mrpcnt=$sqlmrpcnt->total_mrp;
            				}

            				if($mrpcnt >0){
            					$contents  .= 'sauda_mrp'."\n";
            				}
            			}
            		}
            		if($stk_audit=='yes' && $previous_stock=='yes'){
            			if(in_array('prev_stock_counting_master',$need_update_table_array)){
            				$contents  .= 'prev_stock_counting_master'."\n";
            			}
            		else{
            			$sqlprevstkcnt=$CUTDB->select("SELECT COUNT(PSCM.customer_code) AS total_prev_stk FROM prev_stock_counting_master PSCM,customer_master CM
            							WHERE CM.customer_code=PSCM.customer_code
            							AND UNIX_TIMESTAMP(PSCM.download_time) > UNIX_TIMESTAMP('$last_update_time') ".$emp_val_rds."")[0];

            			$prevstkcnt=$sqlprevstkcnt->total_prev_stk;

            			if($prevstkcnt >0)
            			{
            				$contents  .= 'prev_stock_counting_master'."\n";
            			}
            		  }
            		}
            		if($route_plan=='yes'){
            			//$contents  .= 'route_plan'."\n";
            			if(in_array('route_plan_transaction',$need_update_table_array))
            			{
            				$contents  .= 'route_plan'."\n";
            			}
            			else
            			{
            				$sqlqueryrouteplan=$CUTDB->select("SELECT COUNT(route_plan_trans_id) AS total_route_plan FROM route_plan WHERE
            				   emp_code='".$emp_code."' AND UNIX_TIMESTAMP(create_date) > UNIX_TIMESTAMP('".$last_update_time."')")[0];

            				$routeplancnt=$sqlqueryrouteplan->total_route_plan;

            				if($routeplancnt >0){
            					$contents  .= 'route_plan'."\n";
            				}
            			}

            			if($route_customer_planning=='yes')
            			{
            				if(in_array('route_customer_plan_transaction',$need_update_table_array))
            				{
            					$contents  .= 'route_customer_plan'."\n";
            				}
            				$sqlroutecustomerplancnt=$CUTDB->select("SELECT COUNT(route_plan_trans_id) AS total_route_customer_plan FROM route_customer_plan
            									WHERE SUBSTRING(route_plan_trans_id,3,5)='".$emp_code."'
            									AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')")[0];

            				$routecustomerplancnt=$sqlroutecustomerplancnt->total_route_customer_plan;

            				if($routecustomerplancnt >0){
            					$contents  .= 'route_customer_plan'."\n";
            				}
            			}
            		}
            		if($tour_exp=='yes'){
            			$sqltravelcatcnt=$CUTDB->select("SELECT COUNT(transport_mode_cat_id) AS total_travel_cat FROM transport_mode_category
            						WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('$last_update_time')")[0];

            			$travelcatcnt=$sqltravelcatcnt->total_travel_cat;
            			if($travelcatcnt >0){
            				$contents  .= 'travel_category'."\n";
            			}
            			$sqltravelsubcatcnt=$CUTDB->select("SELECT COUNT(transport_mode_sub_cat_id) AS total_travel_sub_cat FROM transport_mode_sub_category
            						WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('$last_update_time')")[0];

            			$travelsubcatcnt=$sqltravelsubcatcnt->total_travel_sub_cat;
            			if($travelsubcatcnt >0){
            				$contents  .= 'travel_sub_category'."\n";
            			}
            		}
            		if($loyalty=='yes'){
            			$contents  .= 'loyalty_customer'."\n";
            			$sqlqueryscheme=$CUTDB->select("SELECT COUNT(scheme_id) AS total_scheme_id FROM scheme_details")[0];
            			$total_scheme_cnt=$sqlqueryscheme->total_scheme_id;
            			if($total_scheme_cnt >0){
            				$contents  .= 'scheme_details'."\n";
            			}
            			if(in_array('loyalty_purchase_details',$need_update_table_array)){
            				$contents  .= 'loyalty_purchase_details'."\n";
            			}
            			else{
            				$sqlqueryloyaltypurchase="SELECT loyalty_card_no AS total_purchase_value FROM `card_transaction` WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
            				$resultloyaltypurchase = mysql_query($sqlqueryloyaltypurchase);
            				$countloyaltypurchase=mysql_num_rows($resultloyaltypurchase);
            				if($countloyaltypurchase >0)
            				{
            					$contents  .= 'loyalty_purchase_details'."\n";
            				}
            			}
            			$sqlqueryredeeme=$CUTDB->select("SELECT COUNT(sn) AS total_redeeme FROM redeem_details")[0];
            			if(count($sqlqueryredeeme) >0){
            				$contents  .= 'redeeme_details'."\n";
            			}
            			if($db_version_code <'7.0'){
            				$contents  .= 'card_transaction'."\n";
            			}
            		}
            		//rds download checking
            		if(in_array('rds_master',$need_update_table_array))
            			{
            				$sqlrds=$CUTDB->select("SELECT COUNT(rds_code) AS total_rds FROM rds_master WHERE 1")[0];
            				$rdscnt=$sqlrds->total_rds;
            				if($rdscnt >0){
            					$contents  .= 'rds_master'."\n";
            				}
            			}
            		else{
            			$sqlrds=$CUTDB->select("SELECT COUNT(rds_code) AS total_rds FROM rds_master WHERE
            					UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('$last_update_time') ".$emp_val_rds."")[0];
            			$rdscnt=$sqlrds->total_rds;

            			if($rdscnt >0){
            				$contents  .= 'rds_master'."\n";
            			}
            		}

            		//$contents  .= 'emp_master'."\n";
              		if(in_array('emp_master',$need_update_table_array)){
              				$contents  .= 'emp_master'."\n";
              		}
            			else{
            				$sqlqueryemp=$CUTDB->select("SELECT COUNT(emp_code) AS total_emp FROM employee_master WHERE
            								UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('$last_update_time') ".$emp_val_rds."")[0];
            				$cntemp=$sqlqueryemp->total_emp;
            				if($cntemp >0){
            					$contents  .= 'emp_master'."\n";
            				}
            			}

            		if($survey=='yes'){

            			$contents  .= 'branch_master'."\n";
            		}
            		//Checking of sale related download
            		if($sale=='yes'){
            			$contents  .= 'branch_master'."\n";
            			$contents  .= 'vendor_master'."\n";
            			$sqlclstksales=$CUTDB->select("SELECT COUNT(OH.order_no) AS total_stk FROM order_header OH,location LO
            							WHERE LO.trans_id=OH.order_no AND
            							OH.transaction_type='CN' AND OH.customer_code='".$emp_code."' AND
            							UNIX_TIMESTAMP(LO.date) > UNIX_TIMESTAMP('".$last_update_time."')")[0];

            			$clstksalescnt=$sqlclstksales->total_stk;

            			if($clstksalescnt >0){
            				$contents  .= 'cl_stk_sales'."\n";
            			}
            			if(in_array('goods_in_transit',$need_update_table_array)){
            				$sqlquerygit=$CUTDB->select("SELECT COUNT(GIT.grn_no) AS total_git FROM goods_in_transit GIT  WHERE GIT.receiver_code=$emp_code")[0];
            				$gitcnt=$sqlquerygit->total_git;
            				if($gitcnt >0){
            					$contents  .= 'git_master'."\n";
            				}
            			}
            			else{
            				$sqlquerygit=$CUTDB->select("SELECT COUNT(GIT.grn_no) AS total_git FROM goods_in_transit GIT  WHERE
            							GIT.transaction_type='ST' AND GIT.receiver_code='".$emp_code."' AND UNIX_TIMESTAMP(GIT.download_time) >
            							UNIX_TIMESTAMP('".$last_update_time."')")[0];

            				$gitcnt=$sqlquerygit->total_git;
            				if($gitcnt >0){
            					$contents  .= 'git_master'."\n";
            				}
            			}

            			if($reporting_level >0){
            				if(in_array('mis_transaction_log',$need_update_table_array)){
            					$sqlquerymis=$CUTDB->select("SELECT COUNT(trans_id) AS total_trans_id FROM mis_transaction_log WHERE 1 ".$emp_val_rds."")[0];
            					$mis_trans_id_cnt=$sqlquerymis->total_trans_id;
            					if($mis_trans_id_cnt >0){
            						$contents  .= 'mis_transaction_log'."\n";
            					}
            				}
            				else{
            					$sqlquerymis=$CUTDB->select("SELECT COUNT(trans_id) AS total_trans_id FROM mis_transaction_log WHERE 1 ".$emp_val_rds."
            								AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')")[0];

            					$mis_trans_id_cnt=$sqlquerymis->total_trans_id;
            					if($mis_trans_id_cnt >0){
            						$contents  .= 'mis_transaction_log'."\n";
            					}
            				}
            			}
            			if($db_version_code <='5.5'){
            				$contents  .= 'transaction_log'."\n";
            			}
            			if($reporting_level >0){
            				$sqlrdslist=$CUTDB->select("SELECT rds_code FROM rds_master WHERE 1  ".$emp_val_rds."");
            				foreach($sqlrdslist as $rowrdslist){
            					$rds_list=$rds_list."'".$rowrdslist->rds_code."'".',';
            				}
            				$rds_list=substr($rds_list,0,-1);
            				$sqlchkdelete=$CUTDB->select("SELECT COUNT(transaction_id) AS no_of_trans_id FROM activity_log WHERE mis_updated_flag_app='0' AND (rds_code IN($rds_list) OR SUBSTRING(transaction_id,2,5) IN ($employee_hierarchy))")[0];
            				$no_of_trans_id=$sqlchkdelete->no_of_trans_id;
            				if($no_of_trans_id >0){
            					$contents  .= 'mis_transaction_delete'."\n";
            				}
            			}
            			$contents  .= 'user_access'."\n";
            		}
            		if($sauda_allocation=='yes'){
            			if(!in_array('sauda',$menu_access_array)){
            				$contents  .= 'sauda_allocation'."\n";
            				if(in_array('order',$menu_access_array)){
            					$contents  .= 'branch_master'."\n";
            					if($sauda_depot_wise=='yes'){
            						if(in_array('customer_branch_relation',$need_update_table_array)){
            							$contents  .= 'customer_branch_relation'."\n";
            						}
            						else{
            							$sqlquerycustomerrds=$CUTDB->select("SELECT COUNT(CBR.customer_code) AS total_customer_depot FROM customer_branch_relation CBR,customer_master CM WHERE CM.customer_code=CBR.customer_code ".$emp_val_rds."
            										AND UNIX_TIMESTAMP(CBR.download_time) > UNIX_TIMESTAMP('".$last_update_time."')")[0];
            							$customer_depot_cnt=$sqlquerycustomerrds->total_customer_depot;
            							if($customer_depot_cnt >0){
            								$contents  .= 'customer_branch_relation'."\n";
            							}
            						}
            					}
            				}
            			}
            			if(!in_array('sauda_allocation_app',$menu_access_array)){
            				$contents  .= 'sauda_allocation_access'."\n";
            				if($sauda_allocation_app=='yes'){
            					if(in_array('sauda_allocation_log',$need_update_table_array)){
            						$contents  .= 'sauda_allocation_log'."\n";
            					}
            					else{
            						$sqlsaudaallocation=$CUTDB->select("SELECT count(allocation_id) AS total_allocation FROM sauda_allocation_log
            										WHERE allocation_id<>'' AND UNIX_TIMESTAMP(allocation_date) > UNIX_TIMESTAMP('$last_update_time') ".$emp_val_rds."")[0];
            						$total_allocation_cnt=$sqlsaudaallocation->total_allocation;
            						if($total_allocation_cnt >0){
            							$contents  .='sauda_allocation_log'."\n";
            						}
            					}
            				}
            			}
            			if(!in_array('sauda_mis',$menu_access_array)){
            				if($sauda_mis=='yes'){
            					if(in_array('sauda_transaction_log',$need_update_table_array)){
            						$contents  .= 'sauda_transaction_log'."\n";
            					}
            					else{
            						$sqlsaudatransaction=$CUTDB->select("SELECT count(sauda_no) AS total_sauda_transaction FROM sauda_transaction_log
            										WHERE  UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('$last_update_time') ".$emp_val_rds."")[0];

            						$total_sauda_transaction=$sqlsaudatransaction->total_sauda_transaction;

            						if($total_sauda_transaction >0){
            							$contents  .='sauda_transaction_log'."\n";
            						}
            					}
            				}
            			}
            		}
            		if($survey=='yes'){
            		  if(in_array('survey_category_master',$need_update_table_array)){
            				$contents  .= 'survey_category_master'."\n";
            			}
            			else{
            				$sqlquerysurveycat=$CUTDB->select("SELECT COUNT(SCM.sub_cat_id) AS total_sub_cat_id FROM survey_category_master SCM WHERE
            									UNIX_TIMESTAMP(SCM.download_time) > UNIX_TIMESTAMP('$last_update_time')")[0];
            				$sub_cat_id_cnt=$sqlquerysurveycat->total_sub_cat_id;
            				if($sub_cat_id_cnt >0){
            					$contents  .= 'survey_category_master'."\n";
            				}
            			 }

            			 if(in_array('table_view',$need_update_table_array)){
            				$contents  .= 'survey_table_view'."\n";
            			 }
            			 else{
            				$sqlquerysurveytableview=$CUTDB->select("SELECT COUNT(row_id) AS total_survey_table_view FROM table_view WHERE
            									UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('$last_update_time')")[0];
            				$survey_table_view_cnt=$sqlquerysurveytableview->total_survey_table_view;
            				if($survey_table_view_cnt >0){
            					$contents  .= 'survey_table_view'."\n";
            				}
            			 }

            			 if(in_array('survey_input',$need_update_table_array)) {
            				$contents  .= 'survey_input_details'."\n";
            			 }
            			else{
            				$sqlquerysurveyinput=$CUTDB->select("SELECT COUNT(SI.row_id) AS total_survey_input_id FROM survey_input SI WHERE
            									UNIX_TIMESTAMP(SI.download_time) > UNIX_TIMESTAMP('$last_update_time')")[0];
            				$survey_input_cnt=$sqlquerysurveyinput->total_survey_input_id;
            				if($survey_input_cnt >0){
            					$contents  .= 'survey_input_details'."\n";
            				}
            			 }
            			 if(in_array('mall_master',$need_update_table_array) || $survey_type=='yes'){
            				$contents  .= 'mall_master'."\n";
            			 }
            			 if(in_array('mall_survey_relation',$need_update_table_array) || $survey_type=='yes'){
            				$contents  .= 'mall_survey_relation'."\n";
            			 }
            			 if(in_array('survey_publish',$need_update_table_array)){
            				 $contents  .= 'survey_publish'."\n";
            			 }
            			 else{
            				 $sqlsurveyidfetch=$CUTDB->select("SELECT SH.survey_id FROM survey_header SH,mall_emp_audit_relation MEAR WHERE
            							SH.mall_id=MEAR.mall_id AND SH.status='ready to publish' AND
            							UNIX_TIMESTAMP(SH.download_time) > UNIX_TIMESTAMP('$last_update_time') AND MEAR.emp_code='".$emp_code."'")[0];

            				 if(count($sqlsurveyidfetch) >0){
            					 $contents  .= 'survey_publish'."\n";
            				 }

            			 }
            			 if(in_array('fs_survey_publish',$need_update_table_array)) {
            				 $contents  .= 'fs_survey_publish'."\n";
            			 }
            			 else{
            				 $sqlmallidfetch=$CUTDB->select("SELECT MER.mall_id,MM.mall_name FROM mall_emp_relation MER,mall_master MM
            								 WHERE MM.mall_id=MER.mall_id AND MER.status='assigned' AND MER.emp_code='".$emp_code."' ");

            				 if(count($sqlmallidfetch) >0){
            					 $countdata=0;
            					 foreach($sqlmallidfetch as $rowmallidfetch){
            						 $mall_id_fetch=$rowmallidfetch->mall_id;
            						 $mall_name_fetch=$rowmallidfetch->mall_name;
            						 $sqlquery=$CUTDB->select("SELECT * from foot_soldier  WHERE mall_id='".$mall_id_fetch."' AND DCE_status='NOT DONE' AND
            								UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')")[0];

            						 if(count($sqlquery) >0){
            							 $contents  .= 'fs_survey_publish'."\n";
            							 break;
            						 }
            					  }
            				 }
            			 }

            		}
            	  if($product_promotion=='yes' || $market_feedback=='yes'){
            			     $contents  .= 'generic_oil_master'."\n";

            		}
            		if($market_feedback=='yes'){
            			$contents  .= 'competitor_group_master'."\n";
            		}
            		$contents  .= 'menu_access'."\n";
            		if(!in_array('pending_contract',$menu_access_array)){
            			if($pending_contract=='yes'){
            				if(in_array('pending_contract_ageing',$need_update_table_array)){
            					$contents  .= 'pending_contract'."\n";
            				}
            				else{
            					$sqlpendingcontractcnt=$CUTDB->select("SELECT COUNT(PC.customer_code) AS total_pending_contract FROM pending_contract_ageing PC,customer_master CM
            									 WHERE CM.customer_code=PC.customer_code ".$emp_val_rds."")[0];

            					$pendingcontractcnt=$sqlpendingcontractcnt->total_pending_contract;

            					if($pendingcontractcnt >0){
            						$contents  .= 'pending_contract'."\n";
            					}
            				 }
            			}
            		}
            		if(!in_array('order',$menu_access_array)){
            		    if($order=='yes' && $previous_order=='yes'){
            				  $contents  .= 'branch_master'."\n";
            			  }
            				if($sauda_depot_wise=='yes'){
            					if(in_array('customer_branch_relation',$need_update_table_array)){
            						$contents  .= 'customer_branch_relation'."\n";
            					}
            					else{
            						$sqlquerycustomerrds=$CUTDB->select("SELECT COUNT(CBR.customer_code) AS total_customer_depot FROM customer_branch_relation CBR,customer_master CM WHERE CM.customer_code=CBR.customer_code ".$emp_val_rds."
            									AND UNIX_TIMESTAMP(CBR.download_time) > UNIX_TIMESTAMP('".$last_update_time."')")[0];

            						$customer_depot_cnt=$sqlquerycustomerrds->total_customer_depot;
            						if($customer_depot_cnt >0){
            							$contents  .= 'customer_branch_relation'."\n";
            						}
            					}
            				}


            			if($order=='yes' && $previous_order=='yes'){
            				if(in_array('prev_order_counting_master',$need_update_table_array)){
            					$contents  .= 'prev_order_counting_master'."\n";
            				}
            				else{
            					$sqlprevordercnt=$CUTDB->select("SELECT COUNT(POCM.customer_code) AS total_prev_order FROM prev_order_counting_master POCM,customer_master CM
            									WHERE CM.customer_code=POCM.customer_code AND UNIX_TIMESTAMP(POCM.download_time) > UNIX_TIMESTAMP('$last_update_time') ".$emp_val_rds."")[0];

            					$prevordercnt=$sqlprevordercnt->total_prev_order;

            					if($prevordercnt >0){
            						$contents  .= 'prev_order_counting_master'."\n";
            					}
            				 }
            			}
            		}
            		if(!in_array('order',$menu_access_array)){
            			if($order=='yes' && $order_status=='yes'){
            				if(in_array('order_status',$need_update_table_array)){
            					$contents  .= 'order_status'."\n";
            				}
            				else{
            					$sqlorderstatus=$CUTDB->select("SELECT COUNT(POCM.customer_code) AS total_order_status FROM prev_order_counting_master POCM,customer_master CM
            									WHERE CM.customer_code=POCM.customer_code AND UNIX_TIMESTAMP(POCM.download_time) > UNIX_TIMESTAMP('$last_update_time') ".$emp_val_rds."")[0];

            					$total_order_status=$sqlorderstatus->total_order_status;

            					if($total_order_status >0){
            						$contents  .= 'order_status'."\n";
            					}
            				 }
            			}
            		}
            		if(!in_array('outstanding_ageing',$menu_access_array)){
            			if($sauda_outstanding=='yes'){
            			    if(in_array('outstanding_ageing',$need_update_table_array)){
            					$contents  .= 'outstanding_ageing'."\n";
            				  }
            				  else{
            					$sqloutstandingcnt=$CUTDB->select("SELECT COUNT(OA.customer_code) AS total_outstanding FROM outstanding_ageing OA,customer_master CM
            										 WHERE CM.customer_code=OA.customer_code ".$emp_val_rds."")[0];

            					$outstandingcnt=$sqloutstandingcnt->total_outstanding;

            					if($outstandingcnt >0){
            						$contents  .= 'outstanding_ageing'."\n";
            					}
            				 }
            			}
            		}
            		if($sale_performance=='yes'){
            			$contents  .= 'sale_performance'."\n";
            		}
            		if($destination_price_list=='yes' || $destination_ordertype_price_list=='yes' || $destination=='yes'){
            			if(in_array('destination_master',$need_update_table_array)){
            				$contents  .= 'destination_master'."\n";
            			}
            			else{
            				$sqlquerydestination=$CUTDB->select("SELECT COUNT(destination_code) AS total_destination FROM destination_master WHERE
            								UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('$last_update_time')")[0];

            				$destinationcnt=$sqlquerydestination->total_destination;

            				if($destinationcnt >0){
            					$contents  .= 'destination_master'."\n";
            				}
            			}
            		}
            		if($target_achievement=='yes'){
            			$contents  .= 'target_achievement'."\n";
            		}
            		if($distributor_route_planning=='yes'){
            			if(in_array('distributor_route_relation',$need_update_table_array)){
            				$contents  .= 'distributor_route_relation'."\n";
            			}
            			else{
            				$sqlquerydistributorroute=$CUTDB->select("SELECT COUNT(distributor_code) AS total_distributor FROM distributor_route_relation WHERE 1 ".$emp_val_rds."
            				AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')")[0];

            				$distributorroutecnt=$sqlquerydistributorroute->total_distributor;

            				if($distributorroutecnt >0){
            					$contents  .= 'distributor_route_relation'."\n";
            				}
            			}
            		}
            	}
            	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
            	$url = url('/api/v1/datadownloaddictionary?nick_name='.$nick_name.'&emp_code='.$emp_code.'&device_id='.$device_id.'&last_update_time='.$last_update_time.'&incremental_download='.$incremental_download);
            	Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);

              header("Content-type: application/text");
              header("Content-Disposition: attachment; filename=datadownloaddictionary.txt");
              print "$contents";
            }

        }
        else{
          $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
          $url = url('/api/v1/datadownloaddictionary?nick_name='.$nick_name);
          Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
          return 404;
        }


  }


}
