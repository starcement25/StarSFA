<?php
class Usersetup_model extends CI_Model{
	function __construct() {
		parent::__construct();
	}
	
	function product_details($nick_name){
		$query = $this->db->query("SELECT no_of_filter, col1, col2, col3, col4, branch_wise_product, branch_wise_mrp, uom_wise_mrp, branch_wise_cl_stk, sauda_allocation_basedon_filter,  destination_price_list, destination_ordertype_price_list, state_wise_mrp 
								   FROM product_details 
								   WHERE nick_name='".$nick_name."'");
		return $query->row_array();
	}
	
	function order_form_details($nick_name){
		$query = $this->db->query("SELECT mrp, TD,sale_rate,TD_type,sale_rate_input_dropdown,credit_limit,cl_stk,sale,
						  VAT,VAT_details,amount,TD_calc,TD_trans_type,VAT_calc_on,instruction,order_approval_process,TD_validation,TD_calc_basedon
						  ,premium,previous_order,order_type,freight_component,destination,tax_type,freight_cost 
						  		   FROM order_form_details 
								   WHERE nick_name='".$nick_name."'");
		return $query->row_array();
	}
	
	function user_details($nick_name){
		$query = $this->db->query("SELECT no_of_licensed_users,vertical_fields,employeewise_hierarchy,vertical_branch_relation,providing_code,
					email_hierarchywise,email_hierarchy_level,need_DCR,branch_vertical_operation_wise_email,previous_stock,stock_audit_rate,
					DCR_checkout,modified_customer_emp_route 
								   FROM user_details 
								   WHERE nick_name='".$nick_name."'");
		return $query->row_array();
	}
	
	function menu_details($nick_name){
		$query = $this->db->query("SELECT route_plan,tour_exp,loyalty,stk_audit,delete_transaction,sauda_allocation,survey,product_promotion,
					market_feedback,collection,sauda_allocation_app,pending_contract,sauda_mis,`order`,order_status,sauda_outstanding,
					sale_performance,outstanding,outstanding_ageing,notes_and_info,target_achievement 
								   FROM menu_details 
								   WHERE nick_name='".$nick_name."'");
		return $query->row_array();
	}
	
	function route_plan_details($nick_name){
		$query = $this->db->query("SELECT route_plan_flow,route_plan_access_period,route_customer_planning,distributor_route_planning 
								   FROM route_plan_details 
								   WHERE nick_name='".$nick_name."'");
		return $query->row_array();
	}
	
	function sauda_details($nick_name){
		$query = $this->db->query("SELECT sauda_allocation_carry_forward,sauda_depot_wise,sauda_rate_variable,sauda_rate_variable_value,
					 sauda_booked_through,sauda_valid_from 
					 			   FROM sauda_form_details 
								   WHERE nick_name='".$nick_name."'");
		return $query->row_array();
	}
	
	function survey_details($nick_name){
		$query = $this->db->query("SELECT survey_menu,survey_type,survey_type_details 
								   FROM survey_form_details 
								   WHERE nick_name='".$nick_name."'");
		return $query->row_array();
	}
	
	function user_details_total_users($nick_name,$last_update_time){
		$query = $this->db->query("SELECT COUNT(user_id) AS total_users 
								   FROM user_details 
								   WHERE nick_name='".$nick_name."' 
								   AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')");
		return $query->row_array();
	}
	
	function menu_details_total_menu($nick_name,$last_update_time){
		$query = $this->db->query("SELECT COUNT(menu_id) AS total_menus 
								   FROM menu_details 
								   WHERE nick_name='".$nick_name."' 
								   AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')");
		return $query->row_array();
	}
	
	function orderform_details_total_orderform($nick_name,$last_update_time){
		$query = $this->db->query("SELECT COUNT(order_form_id) AS total_order_form_details 
								   FROM order_form_details 
								   WHERE nick_name='".$nick_name."' 
								   AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')");
		return $query->row_array();
	}
	
	function product_details_total_product($nick_name,$last_update_time){
		$query = $this->db->query("SELECT COUNT(product_id) AS total_product_details 
								   FROM product_details 
								   WHERE nick_name='".$nick_name."' 
								   AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')");
		return $query->row_array();
	}
	
	function routeplan_details_total_routeplan($nick_name,$last_update_time){
		$query = $this->db->query("SELECT COUNT(route_plan_id) AS total_route_plan_details 
								   FROM route_plan_details 
								   WHERE nick_name='".$nick_name."' 
								   AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')");
		return $query->row_array();
	}
	
	function saudaform_details_total_saudaform($nick_name,$last_update_time){
		$query = $this->db->query("SELECT COUNT(sauda_form_id) AS total_sauda_form_details 
								   FROM sauda_form_details 
								   WHERE nick_name='".$nick_name."' 
								   AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')");
		return $query->row_array();
	}
	
	function surveyform_details_total_surveyform($nick_name,$last_update_time){
		$query = $this->db->query("SELECT COUNT(survey_form_id) AS total_survey_form_details 
								   FROM survey_form_details 
								   WHERE nick_name='".$nick_name."' 
								   AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')");
		return $query->row_array();
	}
	
	function marketfeed_details_total_marketfeed($nick_name,$last_update_time){
		$query = $this->db->query("SELECT COUNT(market_feedback_id) AS total_market_feedback_details 
								   FROM market_feedback_details 
								   WHERE nick_name='".$nick_name."' 
								   AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')");
		return $query->row_array();
	}
}
?>