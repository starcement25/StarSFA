<?php
$setup_db = "acednsproduct";
$this->load->library('dbconn');
$config = $this->dbconn->create_db_conn($setup_db);
$this->load->database($config);
$this->load->model('Usersetup_model');

/*-------------------------------------> SETUP ATTRIBUTES DEFINE <--------------------------------------------*/
/*------------------------------------------------------------------------------------------------------------*/

/*----> PRODUCT SETUP DETAILS <----*/
$product_result = $this->Usersetup_model->product_details($nick_name);
$no_of_filter = $product_result['no_of_filter'];
$col1 = $product_result['col1'];
$col2 = $product_result['col2'];
$col3 = $product_result['col3'];
$col4 = $product_result['col4'];
$branch_wise_product = $product_result['branch_wise_product'];
$branch_wise_mrp = $product_result['branch_wise_mrp'];
$uom_wise_mrp = $product_result['uom_wise_mrp'];
$branch_wise_cl_stk = $product_result['branch_wise_cl_stk'];
$sauda_allocation_basedon_filter = $product_result['sauda_allocation_basedon_filter'];
$destination_price_list = $product_result['destination_price_list'];
$destination_ordertype_price_list = $product_result['destination_ordertype_price_list'];
$state_wise_mrp = $product_result['state_wise_mrp'];

/*----> ORDERFORM DETAILS <----*/
$order_form_result = $this->Usersetup_model->order_form_details($nick_name);
$TD = $order_form_result['TD'];
$TD_type = $order_form_result['TD_type'];
$sale_rate = $order_form_result['sale_rate'];
$sale_rate_input_dropdown = $order_form_result['sale_rate_input_dropdown'];
$mrp = $order_form_result['mrp'];
$credit_limit = $order_form_result['credit_limit'];
$cl_stk = $order_form_result['cl_stk'];
$sale = $order_form_result['sale'];
$VAT = $order_form_result['VAT'];
$VAT_details = $order_form_result['VAT_details'];
$amount = $order_form_result['amount'];
$TD_calc = $order_form_result['TD_calc'];
$TD_trans_type = $order_form_result['TD_trans_type'];
$VAT_calc_on = $order_form_result['VAT_calc_on'];
$instruction = $order_form_result['instruction'];
$order_approval_process = $order_form_result['order_approval_process'];
$TD_validation = $order_form_result['TD_validation'];
$TD_calc_basedon = $order_form_result['TD_calc_basedon'];
$premium = $order_form_result['premium'];
$previous_order = $order_form_result['previous_order'];
$order_type = $order_form_result['order_type'];
$freight_component = $order_form_result['freight_component'];
$destination = $order_form_result['destination'];
$tax_type = $order_form_result['tax_type'];
$freight_cost = $order_form_result['freight_cost'];

/*----> USER DETAILS <----*/
$user_details_result = $this->Usersetup_model->user_details($nick_name);
$no_of_licensed_users = $user_details_result['no_of_licensed_users'];
$vertical_fields = $user_details_result['vertical_fields'];
$vertical_branch_relation = $user_details_result['vertical_branch_relation'];
$employeewise_hierarchy = $user_details_result['employeewise_hierarchy'];
$email_hierarchy_level = $user_details_result['email_hierarchy_level'];
$providing_code = $user_details_result['providing_code'];
$email_hierarchywise = $user_details_result['email_hierarchywise'];
$need_DCR = $user_details_result['need_DCR'];
$branch_vertical_operation_wise_email = $user_details_result['branch_vertical_operation_wise_email'];
$previous_stock = $user_details_result['previous_stock'];
$stock_audit_rate = $user_details_result['stock_audit_rate'];
$DCR_checkout = $user_details_result['DCR_checkout'];
$modified_customer_emp_route = $user_details_result['modified_customer_emp_route'];

/*----> USER DETAILS <----*/
$menu_details_result = $this->Usersetup_model->menu_details($nick_name);
$route_plan = $menu_details_result['route_plan'];
$tour_exp = $menu_details_result['tour_exp'];
$loyalty = $menu_details_result['loyalty'];
$stk_audit = $menu_details_result['stk_audit'];
$delete_transaction = $menu_details_result['delete_transaction'];
$sauda_allocation = $menu_details_result['sauda_allocation'];
$survey = $menu_details_result['survey'];
$product_promotion = $menu_details_result['product_promotion'];
$market_feedback = $menu_details_result['market_feedback'];
$collection = $menu_details_result['collection'];
$sauda_allocation_app = $menu_details_result['sauda_allocation_app'];
$pending_contract = $menu_details_result['pending_contract'];
$sauda_mis = $menu_details_result['sauda_mis'];
$order = $menu_details_result['order'];
$order_status = $menu_details_result['order_status'];
$sauda_outstanding = $menu_details_result['sauda_outstanding'];
$sale_performance = $menu_details_result['sale_performance'];
$outstanding = $menu_details_result['outstanding'];
$outstanding_ageing = $menu_details_result['outstanding_ageing'];
$notes_and_info = $menu_details_result['notes_and_info'];
$target_achievement = $menu_details_result['target_achievement'];

/*----> ROUTE PLAN DETAILS <----*/
$route_plan_details_result = $this->Usersetup_model->route_plan_details($nick_name);
$route_plan_flow = $route_plan_details_result['route_plan_flow'];
$route_plan_access_period = $route_plan_details_result['route_plan_access_period'];
$route_customer_planning = $route_plan_details_result['route_customer_planning'];
$distributor_route_planning = $route_plan_details_result['distributor_route_planning'];

/*----> SAUDA DETAILS <----*/
$sauda_details_result = $this->Usersetup_model->sauda_details($nick_name);
$sauda_allocation_carry_forward = $sauda_details_result['sauda_allocation_carry_forward'];
$sauda_depot_wise = $sauda_details_result['sauda_depot_wise'];
$sauda_rate_variable = $sauda_details_result['sauda_rate_variable'];
$sauda_rate_variable_value = $sauda_details_result['sauda_rate_variable_value'];
$sauda_booked_through = $sauda_details_result['sauda_booked_through'];
$sauda_valid_from = $sauda_details_result['sauda_valid_from'];

/*----> SURVEY DETAILS <----*/
$survey_details_result = $this->Usersetup_model->survey_details($nick_name);
$survey_menu = $survey_details_result['survey_menu'];
$survey_type = $survey_details_result['survey_type'];
$survey_type_details = $survey_details_result['survey_type_details'];

define("col1",$col1);
define("col2",$col2);
define("col3",$col3);
define("col4",$col4);
define("branch_wise_product",$branch_wise_product);
define("no_of_filter",$no_of_filter);
define("TD",$TD);
define("TD_type",$TD_type);
define("sale_rate",$sale_rate);
define("sale_rate_input_dropdown",$sale_rate_input_dropdown);
define("no_of_licensed_users",$no_of_licensed_users);
define("vertical_fields",$vertical_fields);
define("vertical_branch_relation",$vertical_branch_relation);
define("branch_vertical_operation_wise_email",$branch_vertical_operation_wise_email);
define("employeewise_hierarchy",$employeewise_hierarchy);
define("email_hierarchy_level",$email_hierarchy_level);
define("email_hierarchywise",$email_hierarchywise);
define("need_DCR",$need_DCR);
define("DCR_checkout",$DCR_checkout);
define("previous_stock",$previous_stock);
define("credit_limit",$credit_limit);
define("cl_stk",$cl_stk);
define("sale",$sale);
define("route_plan",$route_plan);
define("tour_exp",$tour_exp);
define("loyalty",$loyalty);
define("stk_audit",$stk_audit);
define("delete_transaction",$delete_transaction);
define("sauda_allocation",$sauda_allocation);
define("mrp",$mrp);
define("providing_code",$providing_code);
define("VAT",$VAT);
define("VAT_details",$VAT_details);
define("amount",$amount);
define("TD_calc",$TD_calc);
define("TD_trans_type",$TD_trans_type);
define("TD_validation",$TD_validation);
define("TD_calc_basedon",$TD_calc_basedon);
define("premium",$premium);
define("VAT_calc_on",$VAT_calc_on);
define("instruction",$instruction);
define("order_approval_process",$order_approval_process);
define("sauda_allocation_carry_forward",$sauda_allocation_carry_forward);
define("sauda_depot_wise",$sauda_depot_wise);
define("sauda_rate_variable",$sauda_rate_variable);
define("sauda_rate_variable_value",$sauda_rate_variable_value);
define("sauda_booked_through",$sauda_booked_through);
define("sauda_valid_from",$sauda_valid_from);
define("route_plan_flow",$route_plan_flow);
define("route_plan_access_period",$route_plan_access_period);	
define("branch_wise_mrp",$branch_wise_mrp);
define("uom_wise_mrp",$uom_wise_mrp);
define("branch_wise_cl_stk",$branch_wise_cl_stk);
define("sauda_allocation_basedon_filter",$sauda_allocation_basedon_filter);
define("survey",$survey);
define("survey_menu",$survey_menu);
define("survey_type",$survey_type);
define("survey_type_details",$survey_type_details);
define("product_promotion",$product_promotion);
define("market_feedback",$market_feedback);
define("collection",$collection);
define("sauda_allocation_app",$sauda_allocation_app);
define("pending_contract",$pending_contract);
define("sauda_mis",$sauda_mis);
define("previous_order",$previous_order);
define("order",$order);
define("order_status",$order_status);
define("sauda_outstanding",$sauda_outstanding);
define("sale_performance",$sale_performance);
define("destination_price_list",$destination_price_list);
define("stock_audit_rate",$stock_audit_rate);
define("destination_ordertype_price_list",$destination_ordertype_price_list);
define("order_type",$order_type);
define("freight_component",$freight_component);
define("route_customer_planning",$route_customer_planning);
define("destination",$destination);
define("tax_type",$tax_type);
define("outstanding",$outstanding);
define("outstanding_ageing",$outstanding_ageing);
define("target_achievement",$target_achievement);
define("distributor_route_planning",$distributor_route_planning);
define("modified_customer_emp_route",$modified_customer_emp_route);
define("freight_cost",$freight_cost);
define("state_wise_mrp",$state_wise_mrp);

$incremental_download = $this->get('incremental_download');
$last_update_time = $this->get('last_update_time');
$last_update_time = str_replace('€',' ',$last_update_time);

if($incremental_download=='yes'){
	$rowuserdetails = $this->Usersetup_model->user_details_total_users($nick_name,$last_update_time);
	$usersetupcnt = $rowuserdetails['total_users'];
	if($usersetupcnt >0){
		define("user_details_download","yes");
	}
	else{
		define("user_details_download","no");
	}
	
	$rowmenudetails = $this->Usersetup_model->menu_details_total_menu($nick_name,$last_update_time);
	$menusetupcnt = $rowmenudetails['total_menus'];
	if($menusetupcnt >0){
		define("menu_details_download","yes");
	}
	else{
		define("menu_details_download","no");
	}
	
	$roworderformdetails = $this->Usersetup_model->orderform_details_total_orderform($nick_name,$last_update_time);
	$orderformsetupcnt = $roworderformdetails['total_order_form_details'];
	if($orderformsetupcnt >0){
		define("order_form_details_download","yes");
	}
	else{
		define("order_form_details_download","no");
	}
	
	$rowproductdetails = $this->Usersetup_model->product_details_total_product($nick_name,$last_update_time);
	$productsetupcnt = $rowproductdetails['total_product_details'];
	if($productsetupcnt >0){
		define("product_details_download","yes");
	}
	else{
		define("product_details_download","no");
	}
	
	$rowrouteplandetails = $this->Usersetup_model->routeplan_details_total_routeplan($nick_name,$last_update_time);
	$routeplansetupcnt = $rowrouteplandetails['total_route_plan_details'];
	if($routeplansetupcnt >0){
		define("route_plan_details_download","yes");
	}
	else{
		define("route_plan_details_download","no");
	}
	
	$rowsaudaformdetails = $this->Usersetup_model->saudaform_details_total_saudaform($nick_name,$last_update_time);
	$saudaformsetupcnt = $rowsaudaformdetails['total_sauda_form_details'];
	if($saudaformsetupcnt >0){
		define("sauda_form_details_download","yes");
	}
	else{
		define("sauda_form_details_download","no");
	}
	
	$rowsurveyformdetails = $this->Usersetup_model->surveyform_details_total_surveyform($nick_name,$last_update_time);
	$surveyformsetupcnt = $rowsurveyformdetails['total_survey_form_details'];
	if($surveyformsetupcnt >0){
		define("survey_form_details_download","yes");
	}
	else{
		define("survey_form_details_download","no");
	}
	
	$rowmarketfeeddetails = $this->Usersetup_model->marketfeed_details_total_marketfeed($nick_name,$last_update_time);
	$marketfeedbacksetupcnt = $rowmarketfeeddetails['total_market_feedback_details'];
	if($marketfeedbacksetupcnt >0){
		define("market_feedback_details_download","yes");
	}
	else{
		define("market_feedback_details_download","no");
	}
	
}

$this->db->close();
?>