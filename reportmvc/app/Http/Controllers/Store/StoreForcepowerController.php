<?php
namespace App\Http\Controllers\Store;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use App\Http\Requests;
use DB;
use Session;
use App\Helpers\StoreDashboard;

class StoreForcepowerController extends Controller

	{
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(){
		$userdetails = StoreDashboard::getAllUserTableData();
		return view('store/frocepower.create', compact('userdetails'));
	}

	public function Createuser(Request $request){
		$user_id = strtoupper($request->input('nick_name')) . date("YmdHis");
		if ($request->input('vertical_fields') == 'Yes'){
			$no_of_verticals = $request->input('no_of_verticals');
			$vertical_fields_value = $request->input('vertical_fields_value');
		}
		else{
			$no_of_verticals = '';
			$vertical_fields_value = '';
		}

		$this->validate($request, ['name' => 'required', 'address' => 'required', 'phone_no' => 'required', 'email' => 'required|email', 'license_key' => 'required', 'no_users' => 'required', 'no_of_licensed_users' => 'required', 'nick_name' => 'required', 'previous_baseurl_app' => 'required|active_url', 'current_baseurl_app' => 'required|active_url', 'last_amc_date' => 'required', ]);
		$nicknamecheck = DB::table('user_details')->where('nick_name', '=', strtoupper($request->input('nick_name')))->first();
    $license_key =DB::table('user_details')
		              ->select('license_key')
		              ->where('license_key', DB::raw("(select max(`license_key`) from user_details)"))->get();
		$newlicense_key=$license_key->license_key+1;
		if (count($nicknamecheck) == 0){
			$file = $request->file('logo');
			$rand = time();
			$name = $rand . '_' . $file->getClientOriginalName();
			$destinationPath = 'uploads/companylogo';
			$file->move($destinationPath, $name);
			$query = DB::table('user_details')->insert(array(
				'user_id' => $user_id,
				'name' => $request->input('name') ,
				'address' => $request->input('address') ,
				'phone_no' => $request->input('phone_no') ,
				'email' => $request->input('email') ,
				'license_key' => $newlicense_key ,
				'no_users' => $request->input('no_users') ,
				'no_of_licensed_users' => $request->input('no_of_licensed_users') ,
				'nick_name' => $request->input('nick_name') ,
				'no_of_branches' => $request->input('nick_name') ,
				'employeewise_hierarchy' => $request->input('employeewise_hierarchy') ,
				'vertical_fields' => $request->input('vertical_fields') ,
				'no_of_verticals' => $no_of_verticals,
				'vertical_fields_value' => $vertical_fields_value,
				'vertical_branch_relation' => $request->input('vertical_branch_relation') ,
				'email_hierarchywise' => $request->input('email_hierarchywise') ,
				'email_hierarchy_level' => $request->input('email_hierarchy_level') ,
				'branch_vertical_operation_wise_email' => $request->input('branch_vertical_operation_wise_email') ,
				'ace_integration' => $request->input('ace_integration') ,
				'ace_backward_integration' => $request->input('ace_backward_integration') ,
				'providing_code' => $request->input('providing_code') ,
				'previous_stock' => $request->input('previous_stock') ,
				'need_DCR' => $request->input('need_DCR') ,
				'DCR_time' => $request->input('DCR_time') ,
				'DCR_checkout' => $request->input('DCR_checkout') ,
				'multiple_prospect' => $request->input('multiple_prospect') ,
				'multiple_prospect_value' => $request->input('multiple_prospect_value') ,
				'stock_audit_scan' => $request->input('stock_audit_scan') ,
				'logo' => $name,
				'previous_baseurl_app' => $request->input('previous_baseurl_app') ,
				'current_baseurl_app' => $request->input('current_baseurl_app') ,
				'customer_employee_mapping' => $request->input('customer_employee_mapping') ,
				'modified_customer_emp_route' => $request->input('modified_customer_emp_route') ,
				'working_mode' => $request->input('working_mode') ,
				'last_amc_date' => $request->input('last_amc_date') ,
				'stock_audit_rate' => $request->input('stock_audit_rate') ,
				'remote_db_access' => $request->input('remote_db_access') ,
				'location_drag_drop' => $request->input('location_drag_drop') ,
				'tour_plan_daywise' => $request->input('tour_plan_daywise') ,
				'check_in_out_typeval' => $request->input('check_in_out_typeval') ,
				'user_vertical'=>$request->input('user_vertical') ,
				'FCM' => $request->input('FCM') ,
				'key' => '',
				'downloadapk_val' => '',
				'download_time' => date('Y-m-d H:i:s')
			));
			if ($query){
				session(['nickname' => strtoupper($request->input('nick_name')) ]);
				session(['userid' => $user_id]);
				return redirect('store/createmenu');
			}
			}
		  else{
			return redirect('store/createstore')->withErrors(['Nickname allready exists.Please use any other nickname']);
			}
		}
  public function showuser($user_id) {
          $storeuser =DB::table('user_details')
                 ->where('user_id',$user_id)
                 ->first();
         $userdetails = StoreDashboard::getAllUserTableData();
         return view('store/frocepower.edituser', compact('userdetails','storeuser'));
  }
	public function do_edituser($id){
		if (Input::get('vertical_fields') == 'Yes'){
			$no_of_verticals = Input::get('no_of_verticals');
			$vertical_fields_value = Input::get('vertical_fields_value');
		}
		else{
			$no_of_verticals = '';
			$vertical_fields_value = '';
		}

		$query=DB::table('user_details')
		->where('user_id', $id)
		->limit(1)
		->update(array('name' => Input::get('name') ,
		'address' => Input::get('address') ,
		'phone_no' => Input::get('phone_no') ,
		'email' => Input::get('email') ,
		'no_users' => Input::get('no_users') ,
		'no_of_licensed_users' => Input::get('no_of_licensed_users') ,
		'nick_name' => Input::get('nick_name') ,
		'no_of_branches' => Input::get('nick_name') ,
		'employeewise_hierarchy' => Input::get('employeewise_hierarchy') ,
		'vertical_fields' => Input::get('vertical_fields') ,
		'no_of_verticals' => $no_of_verticals,
		'vertical_fields_value' => $vertical_fields_value,
		'vertical_branch_relation' => Input::get('vertical_branch_relation') ,
		'email_hierarchywise' => Input::get('email_hierarchywise') ,
		'email_hierarchy_level' => Input::get('email_hierarchy_level') ,
		'branch_vertical_operation_wise_email' => Input::get('branch_vertical_operation_wise_email') ,
		'ace_integration' => Input::get('ace_integration') ,
		'ace_backward_integration' => Input::get('ace_backward_integration') ,
		'providing_code' => Input::get('providing_code') ,
		'previous_stock' => Input::get('previous_stock') ,
		'need_DCR' => Input::get('need_DCR') ,
		'DCR_time' => Input::get('DCR_time') ,
		'DCR_checkout' => Input::get('DCR_checkout') ,
		'multiple_prospect' => Input::get('multiple_prospect') ,
		'multiple_prospect_value' => Input::get('multiple_prospect_value') ,
		'stock_audit_scan' => Input::get('stock_audit_scan') ,
		'previous_baseurl_app' => Input::get('previous_baseurl_app') ,
		'current_baseurl_app' => Input::get('current_baseurl_app') ,
		'customer_employee_mapping' => Input::get('customer_employee_mapping') ,
		'modified_customer_emp_route' => Input::get('modified_customer_emp_route') ,
		'working_mode' => Input::get('working_mode') ,
		'last_amc_date' => Input::get('last_amc_date') ,
		'stock_audit_rate' => Input::get('stock_audit_rate') ,
		'remote_db_access' => Input::get('remote_db_access') ,
		'location_drag_drop' => Input::get('location_drag_drop') ,
		'tour_plan_daywise' => Input::get('tour_plan_daywise') ,
		'check_in_out_typeval' => Input::get('check_in_out_typeval') ,
		'user_vertical'=>Input::get('user_vertical') ,
		'FCM' => Input::get('FCM')
	  ));

			 return redirect('/store/createmenu/'.$id);

	}

	public function createmenu(){
		$menudetails = StoreDashboard::getAllMenuTableData();
		return view('store/frocepower.menucreate', compact('menudetails'));
	}



  public function showmenu($user_id) {
            $storemenu =DB::table('menu_details')
                   ->where('user_id',$user_id)
                   ->first();
           $menudetails = StoreDashboard::getAllMenuTableData();
           return view('store/frocepower.editmenu', compact('menudetails','storemenu'));
  }

	public function do_addmenu(Request $request){
		$userid = Session::get('userid');
		$nickname = Session::get('nickname');
		$marketfeedbackid = 'M' . $nickname . date('ymdhmi');
		$query = DB::table('menu_details')->insert(array(
			'menu_id' => $marketfeedbackid,
			'user_id' => $userid,
			'nick_name' => $nickname,
			'attendance' => $request->input('attendance') ,
			'route_plan' => $request->input('route_plan') ,
			'order' => $request->input('order') ,
			'collection' => $request->input('collection') ,
			'stk_audit' => $request->input('stk_audit') ,
			'business_prospect' => $request->input('business_prospect') ,
			'tour_exp' => $request->input('tour_exp') ,
			'loading_freight' => $request->input('loading_freight') ,
			'capture_image' => $request->input('capture_image') ,
			'notes_and_info' => $request->input('notes_and_info') ,
			'activity_report' => $request->input('activity_report') ,
			'mis_report' => $request->input('mis_report') ,
			'loyalty' => $request->input('loyalty') ,
			'self_appraisal' => $request->input('self_appraisal') ,
			'schemes' => $request->input('schemes') ,
			'delete_transaction' => $request->input('delete_transaction') ,
			'sauda_allocation' => $request->input('sauda_allocation') ,
			'sale_performance' => $request->input('sale_performance') ,
			'survey' => $request->input('survey') ,
			'product_promotion' => $request->input('product_promotion') ,
			'replacement' => $request->input('replacement') ,
			'market_feedback' => $request->input('market_feedback') ,
			'sauda_allocation_app' => $request->input('sauda_allocation_app') ,
			'pending_contract' => $request->input('pending_contract') ,
			'sauda_mis' => $request->input('sauda_mis') ,
			'order_status' => $request->input('order_status') ,
			'checkout' => $request->input('checkout') ,
			'check_in_out' => $request->input('check_in_out') ,
			'sauda_outstanding' => $request->input('sauda_outstanding') ,
			'outstanding' => $request->input('outstanding') ,
			'outstanding_ageing' => $request->input('outstanding_ageing') ,
			'target_achievement' => $request->input('target_achievement') ,
			'wholesaler_info' => $request->input('wholesaler_info') ,
			'yellow_card' => $request->input('yellow_card') ,
			'catalogue' => $request->input('catalogue') ,
			'catalogue_url' => $request->input('catalogue_url') ,
			'download_time' => date('Y-m-d H:i:s')
		));
		if ($query)
			{
			return redirect('store/createproduct');
			}
		}
	public function do_editmenu($id){
		$query=DB::table('menu_details')
					 ->where('user_id', $id)
					 ->limit(1)
					 ->update(array(
						 'attendance' => Input::get('attendance') ,
						 'route_plan' => Input::get('route_plan') ,
						 'order' => Input::get('order') ,
						 'collection' => Input::get('collection') ,
						 'stk_audit' => Input::get('stk_audit') ,
						 'business_prospect' => Input::get('business_prospect') ,
						 'tour_exp' => Input::get('tour_exp') ,
						 'loading_freight' => Input::get('loading_freight') ,
						 'capture_image' => Input::get('capture_image') ,
						 'notes_and_info' => Input::get('notes_and_info') ,
						 'activity_report' => Input::get('activity_report') ,
						 'mis_report' => Input::get('mis_report') ,
						 'loyalty' => Input::get('loyalty') ,
						 'self_appraisal' => Input::get('self_appraisal') ,
						 'schemes' => Input::get('schemes') ,
						 'delete_transaction' => Input::get('delete_transaction') ,
						 'sauda_allocation' => Input::get('sauda_allocation') ,
						 'sale_performance' => Input::get('sale_performance') ,
						 'survey' => Input::get('survey') ,
						 'product_promotion' => Input::get('product_promotion') ,
						 'replacement' => Input::get('replacement') ,
						 'market_feedback' => Input::get('market_feedback') ,
						 'sauda_allocation_app' => Input::get('sauda_allocation_app') ,
						 'pending_contract' => Input::get('pending_contract') ,
						 'sauda_mis' => Input::get('sauda_mis') ,
						 'order_status' => Input::get('order_status') ,
						 'checkout' => Input::get('checkout') ,
						 'check_in_out' => Input::get('check_in_out') ,
						 'sauda_outstanding' => Input::get('sauda_outstanding') ,
						 'outstanding' => Input::get('outstanding') ,
						 'outstanding_ageing' => Input::get('outstanding_ageing') ,
						 'target_achievement' => Input::get('target_achievement') ,
						 'wholesaler_info' => Input::get('wholesaler_info') ,
						 'yellow_card' => Input::get('yellow_card') ,
						 'catalogue' => Input::get('catalogue') ,
						 'catalogue_url' => Input::get('catalogue_url') ,
						 'download_time' => date('Y-m-d H:i:s')
					 ));
					 if ($query)
			 			{
			 				return redirect('store/createproduct/'.$id);
			 			}
	}


	public function createproduct(){
		$productdetails = StoreDashboard::getAllProductTableData();
		return view('store/frocepower.productcreate', compact('productdetails'));
	}
  public function showproduct($user_id) {
            $storeproduct =DB::table('product_details')
                   ->where('user_id',$user_id)
                   ->first();
           $productdetails = StoreDashboard::getAllProductTableData();
           return view('store/frocepower.editproduct', compact('productdetails','storeproduct'));
  }

	public function do_product(Request $request){
		$userid = Session::get('userid');
		$nickname = Session::get('nickname');
		$marketfeedbackid = 'P' . $nickname . date('ymdhmi');
		$this->validate($request, ['no_of_filter' => 'required', ]);
		$query = DB::table('product_details')->insert(array(
			'product_id' => $marketfeedbackid,
			'user_id' => $userid,
			'nick_name' => $nickname,
			'no_of_filter' => $request->input('no_of_filter') ,
			'col1' => $request->input('col1') ,
			'col2' => $request->input('col2') ,
			'col3' => $request->input('col3') ,
			'col4' => $request->input('col4') ,
			'branch_wise_product' => $request->input('branch_wise_product') ,
			'uom_wise_mrp' => $request->input('uom_wise_mrp') ,
			'branch_wise_mrp' => $request->input('branch_wise_mrp') ,
			'state_wise_mrp' => $request->input('state_wise_mrp') ,
			'multiple_rate' => $request->input('multiple_rate') ,
			'branch_wise_cl_stk' => $request->input('branch_wise_cl_stk') ,
			'sauda_allocation_basedon_filter' => $request->input('sauda_allocation_basedon_filter') ,
			'product_in_business_prospect' => $request->input('product_in_business_prospect') ,
			'secondary_unit' => $request->input('secondary_unit') ,
			'destination_price_list' => $request->input('destination_price_list') ,
			'destination_ordertype_price_list' => $request->input('destination_ordertype_price_list') ,
			'product_qty_wise_TD' => $request->input('product_qty_wise_TD') ,
			'focus_product' => $request->input('focus_product') ,
			'download_time' => date('Y-m-d H:i:s')
		));
			if ($query){
				return redirect('store/createorderdetails');
			}
		}
		public function do_editproduct($id){
  		$query=DB::table('product_details')
  					 ->where('user_id', $id)
  					 ->limit(1)
  					 ->update(array(
							 'no_of_filter' => Input::get('no_of_filter') ,
							 'col1' => Input::get('col1') ,
							 'col2' => Input::get('col2') ,
							 'col3' => Input::get('col3') ,
							 'col4' => Input::get('col4') ,
							 'branch_wise_product' => Input::get('branch_wise_product') ,
							 'uom_wise_mrp' => Input::get('uom_wise_mrp') ,
							 'branch_wise_mrp' => Input::get('branch_wise_mrp') ,
							 'state_wise_mrp' => Input::get('state_wise_mrp') ,
							 'multiple_rate' => Input::get('multiple_rate') ,
							 'branch_wise_cl_stk' => Input::get('branch_wise_cl_stk') ,
							 'sauda_allocation_basedon_filter' => Input::get('sauda_allocation_basedon_filter') ,
							 'product_in_business_prospect' => Input::get('product_in_business_prospect') ,
							 'secondary_unit' => Input::get('secondary_unit') ,
							 'destination_price_list' => Input::get('destination_price_list') ,
							 'destination_ordertype_price_list' => Input::get('destination_ordertype_price_list') ,
							 'product_qty_wise_TD' => Input::get('product_qty_wise_TD') ,
							 'focus_product' => Input::get('focus_product') ,
							 'download_time' => date('Y-m-d H:i:s')
						 ));
						 if ($query){
							return redirect('store/createorderdetails/'.$id);
			 			}
		}

	public function createorder(){
		$orderdetails = StoreDashboard::getAllOrderDetailsTableData();
		return view('store/frocepower.orderdetailscreate', compact('orderdetails'));
	}
  public function showorderdetails($user_id) {
              $storeorderdetails =DB::table('order_form_details')
                     ->where('user_id',$user_id)
                     ->first();
             $orderdetails = StoreDashboard::getAllOrderDetailsTableData();
             return view('store/frocepower.editorderdetails', compact('orderdetails','storeorderdetails'));
  }

	public function do_orderdetails(Request $request){
		$userid = Session::get('userid');
		$nickname = Session::get('nickname');
		$marketfeedbackid = 'O' . $nickname . date('ymdhmi');
		$query = DB::table('order_form_details')->insert(array(
			'order_form_id' => $marketfeedbackid,
			'user_id' => $userid,
			'nick_name' => $nickname,
			'credit_limit' => $request->input('credit_limit') ,
			'cl_stk' => $request->input('cl_stk') ,
			'mrp_input_dropdown' => $request->input('mrp_input_dropdown') ,
			'mrp' => $request->input('mrp') ,
			'TD' => $request->input('TD') ,
			'TD_type' => $request->input('TD_type') ,
			'TD_type_input_dropdown' => $request->input('TD_type_input_dropdown') ,
			'TD_calc' => $request->input('TD_calc') ,
			'TD_trans_type' => $request->input('TD_trans_type') ,
			'TD_validation' => $request->input('TD_validation') ,
			'TD_calc_basedon' => $request->input('TD_calc_basedon') ,
			'sale_rate' => $request->input('sale_rate') ,
			'amount' => $request->input('amount') ,
			'sale' => $request->input('sale') ,
			'branch_rds_transfer' => $request->input('branch_rds_transfer') ,
			'instruction' => $request->input('instruction') ,
			'hint_remarks' => $request->input('hint_remarks') ,
			'hint_remarks_val' => $request->input('hint_remarks_val') ,
			'sale_rate_input_dropdown' => $request->input('sale_rate_input_dropdown') ,
			'VAT' => $request->input('VAT') ,
			'VAT_details' => $request->input('VAT_details') ,
			'VAT_type' => $request->input('VAT_type') ,
			'VAT_calc_on' => $request->input('VAT_calc_on') ,
			'add_customer' => $request->input('add_customer') ,
			'add_customer_branch' => $request->input('add_customer_branch') ,
			'add_customer_OTP' => $request->input('add_customer_OTP') ,
			'add_customer_route_creation' => $request->input('add_customer_route_creation') ,
			'add_customer_details' => $request->input('add_customer_details') ,
			'add_customer_trade_nontrade' => $request->input('add_customer_trade_nontrade') ,
			'add_customer_image_creation' => $request->input('add_customer_image_creation') ,
			'distributor_route_emp_relation' => $request->input('distributor_route_emp_relation') ,
			'customer_information_check' => $request->input('customer_information_check') ,
			'tagged_customer_for_business_prospect' => $request->input('tagged_customer_for_business_prospect') ,
			'tagged_distributor_for_order' => $request->input('tagged_distributor_for_order') ,
			'attached_printer' => $request->input('attached_printer') ,
			'printer_mandatory' => $request->input('printer_mandatory') ,
			'SMSLIVE' => $request->input('SMSLIVE') ,
			'payment_type' => $request->input('payment_type') ,
			'order_approval_process' => $request->input('order_approval_process') ,
			'premium' => $request->input('premium') ,
			'previous_order' => $request->input('previous_order') ,
			'order_type' => $request->input('order_type') ,
			'freight_component' => $request->input('freight_component') ,
			'freight_cost' => $request->input('freight_cost') ,
			'tax_type' => $request->input('tax_type') ,
			'destination' => $request->input('destination') ,
			'branch_wise_destination' => $request->input('branch_wise_destination') ,
			'input_screen_normal' => $request->input('input_screen_normal') ,
			'input_screen_special' => $request->input('input_screen_special') ,
			'printer_type' => $request->input('printer_type') ,
			'printer_menu' => $request->input('printer_menu') ,
			'multiple_UOM' => $request->input('multiple_UOM') ,
			'input_screen_planwise' => $request->input('input_screen_planwise') ,
			'input_screen_planwise_filter1wise' => $request->input('input_screen_planwise_filter1wise') ,
			'download_time' => date('Y-m-d H:i:s')
		));
		if ($query)
		{
           $menu= DB::table('menu_details')->where('user_id', $userid)->first();
           if($menu->survey=='yes')
           {
			          return redirect('store/createsurvayformdetails');
           }
           elseif($menu->sauda_allocation=='yes')
           {
			          return redirect('store/createsaudaformdetails');
           }
           elseif($menu->route_plan=='yes')
           {
              return redirect('store/createrouteplandetails');
           }
           elseif($menu->market_feedback=='yes')
           {
              return redirect('store/createmarketfeedback');
           }
           else{
             return redirect('store/createreportconfig');
           }
			}
		}
		public function do_editorderdetails($id){
			$query=DB::table('order_form_details')
						 ->where('user_id', $id)
						 ->limit(1)
						 ->update(array(
							 'credit_limit' => Input::get('credit_limit') ,
							 'cl_stk' => Input::get('cl_stk') ,
							 'mrp_input_dropdown' => Input::get('mrp_input_dropdown') ,
							 'mrp' => Input::get('mrp') ,
							 'TD' => Input::get('TD') ,
							 'TD_type' => Input::get('TD_type') ,
							 'TD_type_input_dropdown' => Input::get('TD_type_input_dropdown') ,
							 'TD_calc' => Input::get('TD_calc') ,
							 'TD_trans_type' => Input::get('TD_trans_type') ,
							 'TD_validation' => Input::get('TD_validation') ,
							 'TD_calc_basedon' => Input::get('TD_calc_basedon') ,
							 'sale_rate' => Input::get('sale_rate') ,
							 'amount' => Input::get('amount') ,
							 'sale' => Input::get('sale') ,
							 'branch_rds_transfer' => Input::get('branch_rds_transfer') ,
							 'instruction' => Input::get('instruction') ,
							 'hint_remarks' => Input::get('hint_remarks') ,
							 'hint_remarks_val' => Input::get('hint_remarks_val') ,
							 'sale_rate_input_dropdown' => Input::get('sale_rate_input_dropdown') ,
							 'VAT' => Input::get('VAT') ,
							 'VAT_details' => Input::get('VAT_details') ,
							 'VAT_type' => Input::get('VAT_type') ,
							 'VAT_calc_on' => Input::get('VAT_calc_on') ,
							 'add_customer' => Input::get('add_customer') ,
							 'add_customer_branch' => Input::get('add_customer_branch') ,
							 'add_customer_OTP' => Input::get('add_customer_OTP') ,
							 'add_customer_route_creation' => Input::get('add_customer_route_creation') ,
							 'add_customer_details' => Input::get('add_customer_details') ,
							 'add_customer_trade_nontrade' => Input::get('add_customer_trade_nontrade') ,
							 'add_customer_image_creation' => Input::get('add_customer_image_creation') ,
							 'distributor_route_emp_relation' => Input::get('distributor_route_emp_relation') ,
							 'customer_information_check' => Input::get('customer_information_check') ,
							 'tagged_customer_for_business_prospect' => Input::get('tagged_customer_for_business_prospect') ,
							 'tagged_distributor_for_order' => Input::get('tagged_distributor_for_order') ,
							 'attached_printer' => Input::get('attached_printer') ,
							 'printer_mandatory' => Input::get('printer_mandatory') ,
							 'SMSLIVE' => Input::get('SMSLIVE') ,
							 'payment_type' => Input::get('payment_type') ,
							 'order_approval_process' => Input::get('order_approval_process') ,
							 'premium' => Input::get('premium') ,
							 'previous_order' => Input::get('previous_order') ,
							 'order_type' => Input::get('order_type') ,
							 'freight_component' => Input::get('freight_component') ,
							 'freight_cost' => Input::get('freight_cost') ,
							 'tax_type' => Input::get('tax_type') ,
							 'destination' => Input::get('destination') ,
							 'branch_wise_destination' => Input::get('branch_wise_destination') ,
							 'input_screen_normal' => Input::get('input_screen_normal') ,
							 'input_screen_special' => Input::get('input_screen_special') ,
							 'printer_type' => Input::get('printer_type') ,
							 'printer_menu' => Input::get('printer_menu') ,
							 'multiple_UOM' => Input::get('multiple_UOM') ,
							 'input_screen_planwise' => Input::get('input_screen_planwise') ,
							 'input_screen_planwise_filter1wise' => Input::get('input_screen_planwise_filter1wise') ,
							 'download_time' => date('Y-m-d H:i:s')
						 ));
						 if ($query)
				 		 {
				            $menu= DB::table('menu_details')->where('user_id', $id)->first();
				            if($menu->survey=='yes')
				            {
				 			          return redirect('store/createsurvayformdetails/'.$id);
				            }
				            elseif($menu->sauda_allocation=='yes')
				            {
				 			          return redirect('store/createsaudaformdetails/'.$id);
				            }
				            elseif($menu->route_plan=='yes')
				            {
				               return redirect('store/createrouteplandetails/'.$id);
				            }
				            elseif($menu->market_feedback=='yes')
				            {
				               return redirect('store/createmarketfeedback/'.$id);
				            }
				            else{
											$reportconfig=DB::table('configuration_filter')->where('user_id', '=', $id)->get();
										 if(count($reportconfig)>0)
										 {
											 return redirect('store/createreportconfig/'.$id);
										 }
										 else{
											 return redirect('store/dashboard/');
										 }
				            }
				 			}
		}

	public function createsurvayform(){
		$survayformdetails = StoreDashboard::getAllSurvayFormDetailsTableData();
		return view('store/frocepower.survayformdetailscreate', compact('survayformdetails'));
	}

  public function showsurvayform($user_id) {
              $storesurvayform =DB::table('survey_form_details')
                     ->where('user_id',$user_id)
                     ->first();
             $survayformdetails = StoreDashboard::getAllSurvayFormDetailsTableData();
             return view('store/frocepower.editsurvayform', compact('survayformdetails','storesurvayform'));
  }

	public function do_survayformdetails(Request $request){
		$userid = Session::get('userid');
		$nickname = Session::get('nickname');
		$marketfeedbackid = 'S' . $nickname . date('ymdhmi');

    $this->validate($request, ['survey_type_details' => 'required',
        'survey_sub_type_details'=>'required',
    ]);

		$query = DB::table('survey_form_details')->insert(array(
			'survey_form_id' => $marketfeedbackid,
			'user_id' => $userid,
			'nick_name' => $nickname,
			'survey_menu' => $request->input('survey_menu') ,
			'survey_layer' => $request->input('survey_layer') ,
			'survey_type' => $request->input('survey_type') ,
			'survey_type_details' => $request->input('survey_type_details') ,
			'survey_sub_type_details' => $request->input('survey_sub_type_details') ,
			'survey_submenu' => $request->input('survey_submenu') ,
			'survey_submenu_details' => $request->input('survey_submenu_details') ,
			'mall_survey_relation' => $request->input('mall_survey_relation') ,
			'OTP' => $request->input('OTP') ,
			'outlet_menu' => $request->input('outlet_menu') ,
			'survey_route_plan' => $request->input('survey_route_plan') ,
			'survey_report_row_id' => $request->input('survey_report_row_id') ,
			'other_text' => $request->input('other_text') ,
			'download_time' => date('Y-m-d H:i:s')
		));
		if ($query)
		{
        $menu= DB::table('menu_details')->where('user_id', $userid)->first();
        if($menu->sauda_allocation=='yes')
        {
             return redirect('store/createsaudaformdetails');
        }
        elseif($menu->route_plan=='yes')
        {
           return redirect('store/createrouteplandetails');
        }
        elseif($menu->market_feedback=='yes')
        {
           return redirect('store/createmarketfeedback');
        }
        else{
          return redirect('store/createreportconfig');
        }
			}
		}

	public function do_editsurvayformdetails($id){
			$query=DB::table('survey_form_details')
						 ->where('user_id', $id)
						 ->limit(1)
						 ->update(array(
							 'survey_menu' => Input::get('survey_menu') ,
							 'survey_layer' => Input::get('survey_layer') ,
							 'survey_type' => Input::get('survey_type') ,
							 'survey_type_details' => Input::get('survey_type_details') ,
							 'survey_sub_type_details' => Input::get('survey_sub_type_details') ,
							 'survey_submenu' => Input::get('survey_submenu') ,
							 'survey_submenu_details' => Input::get('survey_submenu_details') ,
							 'mall_survey_relation' => Input::get('mall_survey_relation') ,
							 'OTP' => Input::get('OTP') ,
							 'outlet_menu' => Input::get('outlet_menu') ,
							 'survey_route_plan' => Input::get('survey_route_plan') ,
							 'survey_report_row_id' => Input::get('survey_report_row_id') ,
							 'other_text' => Input::get('other_text') ,
							 'download_time' => date('Y-m-d H:i:s')
						 ));
						 if ($query)
				 		 {
				         $menu= DB::table('menu_details')->where('user_id', $id)->first();
				         if($menu->sauda_allocation=='yes')
				         {
				              return redirect('store/createsaudaformdetails/'.$id);
				         }
				         elseif($menu->route_plan=='yes')
				         {
				            return redirect('store/createrouteplandetails/'.$id);
				         }
				         elseif($menu->market_feedback=='yes')
				         {
				            return redirect('store/createmarketfeedback/'.$id);
				         }
				         else{
									 $reportconfig=DB::table('configuration_filter')->where('user_id', '=', $id)->get();
									 if(count($reportconfig)>0)
									 {
										 return redirect('store/createreportconfig/'.$id);
									 }
									 else{
										 return redirect('store/dashboard/');
									 }
				         }
				 		}
	}

	public function createsaudaform(){
		$saudaformdetails = StoreDashboard::getAllSaudaFormDetailsTableData();
		return view('store/frocepower.saudaformdetailscreate', compact('saudaformdetails'));
	}

  public function showsaudaform($user_id) {
              $storesauda =DB::table('sauda_form_details')
                     ->where('user_id',$user_id)
                     ->first();
             $saudaformdetails = StoreDashboard::getAllSaudaFormDetailsTableData();
             return view('store/frocepower.editsaudaform', compact('saudaformdetails','storesauda'));
  }

	public function do_saudaformdetails(Request $request){
		$userid = Session::get('userid');
		$nickname = Session::get('nickname');
		$marketfeedbackid = 'S' . $nickname . date('ymdhmi');
		$query = DB::table('sauda_form_details')->insert(array(
			'sauda_form_id' => $marketfeedbackid,
			'user_id' => $userid,
			'nick_name' => $nickname,
			'sauda_allocation_carry_forward' => $request->input('sauda_allocation_carry_forward') ,
			'sauda_depot_wise' => $request->input('sauda_depot_wise') ,
			'sauda_rate_variable' => $request->input('sauda_rate_variable') ,
			'sauda_rate_variable_value' => $request->input('sauda_rate_variable_value') ,
			'sauda_booked_through' => $request->input('sauda_booked_through') ,
			'sauda_valid_from' => $request->input('sauda_valid_from') ,
			'sauda_rate_dependent_on_despatch_point' => $request->input('sauda_rate_dependent_on_despatch_point') ,
			'sauda_rate_dependent_on_despatch_point_val' => $request->input('sauda_rate_dependent_on_despatch_point_val') ,
			'sauda_rate_dependent_on_despatch_point_verticlewise' => $request->input('sauda_rate_dependent_on_despatch_point_verticlewise') ,
			'sauda_rate_dependent_on_despatch_point_verticle_val' => $request->input('sauda_rate_dependent_on_despatch_point_verticle_val') ,
			'state_branch_wise_TD' => $request->input('state_branch_wise_TD') ,
			'download_time' => date('Y-m-d H:i:s')
		));
		  if ($query)
			{
        $menu= DB::table('menu_details')->where('user_id', $id)->first();
        if($menu->route_plan=='yes')
        {
           return redirect('store/createrouteplandetails');
        }
        elseif($menu->market_feedback=='yes')
        {
           return redirect('store/createmarketfeedback');
        }
        else{
          return redirect('store/createreportconfig');
        }
			}
		}
		public function do_editsaudaformdetails($id){
			$query=DB::table('sauda_form_details')
						 ->where('user_id', $id)
						 ->limit(1)
						 ->update(array(
							 'sauda_allocation_carry_forward' => Input::get('sauda_allocation_carry_forward') ,
							 'sauda_depot_wise' => Input::get('sauda_depot_wise') ,
							 'sauda_rate_variable' => Input::get('sauda_rate_variable') ,
							 'sauda_rate_variable_value' => Input::get('sauda_rate_variable_value') ,
							 'sauda_booked_through' => Input::get('sauda_booked_through') ,
							 'sauda_valid_from' => Input::get('sauda_valid_from') ,
							 'sauda_rate_dependent_on_despatch_point' => Input::get('sauda_rate_dependent_on_despatch_point') ,
							 'sauda_rate_dependent_on_despatch_point_val' => Input::get('sauda_rate_dependent_on_despatch_point_val') ,
							 'sauda_rate_dependent_on_despatch_point_verticlewise' => Input::get('sauda_rate_dependent_on_despatch_point_verticlewise') ,
							 'sauda_rate_dependent_on_despatch_point_verticle_val' => Input::get('sauda_rate_dependent_on_despatch_point_verticle_val') ,
							 'state_branch_wise_TD' => Input::get('state_branch_wise_TD') ,
							 'download_time' => date('Y-m-d H:i:s')
						 ));
						 if ($query)
			 			 {
			         $menu= DB::table('menu_details')->where('user_id', $id)->first();
			         if($menu->route_plan=='yes')
			         {
			            return redirect('store/createrouteplandetails/'.$id);
			         }
			         elseif($menu->market_feedback=='yes')
			         {
			            return redirect('store/createmarketfeedback/'.$id);
			         }
			         else{
								  $reportconfig=DB::table('configuration_filter')->where('user_id', '=', $id)->get();
	 								if(count($reportconfig)>0)
	 								{
			           		return redirect('store/createreportconfig/'.$id);
									}
									else{
										return redirect('store/dashboard/');
									}
			         }
			 			}

		}

	public function createrouteplan(){
		$routeplanedetails = StoreDashboard::getAllRoutePlanDetailsTableData();
		return view('store/frocepower.routeplandetailscreate', compact('routeplanedetails'));
	}

  public function showrouteplan($user_id) {
            $storerouteplan =DB::table('route_plan_details')
                      ->where('user_id',$user_id)
                      ->first();
            $routeplanedetails = StoreDashboard::getAllRoutePlanDetailsTableData();
            return view('store/frocepower.editrouteplane', compact('routeplanedetails','storerouteplan'));
    }

	public function do_routeplandetails(Request $request){
		$userid = Session::get('userid');
		$nickname = Session::get('nickname');
		$marketfeedbackid = 'R' . $nickname . date('ymdhmi');
		$query = DB::table('route_plan_details')->insert(array(
			'route_plan_id' => $marketfeedbackid,
			'user_id' => $userid,
			'nick_name' => $nickname,
			'route_plan_flow' => $request->input('route_plan_flow') ,
			'route_plan_access_period' => $request->input('route_plan_access_period') ,
			'route_plan_deviation' => $request->input('route_plan_deviation') ,
			'route_plan_approval' => $request->input('route_plan_approval') ,
			'route_customer_planning' => $request->input('route_customer_planning') ,
			'distributor_route_planning' => $request->input('distributor_route_planning') ,
			'distributor_route_planning_multiple' => $request->input('distributor_route_planning_multiple') ,
			'download_time' => date('Y-m-d H:i:s')
		));
		if ($query)
			{
        $menu= DB::table('menu_details')->where('user_id', $id)->first();
				if($menu->market_feedback=='yes')
        {
           return redirect('store/createmarketfeedback');
        }
        else{
          return redirect('store/createreportconfig');
        }
			}
		}
	public function do_editrouteplandetails($id){
		$query=DB::table('route_plan_details')
					 ->where('user_id', $id)
					 ->limit(1)
					 ->update(array(
						 'route_plan_flow' => Input::get('route_plan_flow') ,
						 'route_plan_access_period' => Input::get('route_plan_access_period') ,
						 'route_plan_deviation' => Input::get('route_plan_deviation') ,
						 'route_plan_approval' => Input::get('route_plan_approval') ,
						 'route_customer_planning' => Input::get('route_customer_planning') ,
						 'distributor_route_planning' => Input::get('distributor_route_planning') ,
						 'distributor_route_planning_multiple' => Input::get('distributor_route_planning_multiple') ,
						 'download_time' => date('Y-m-d H:i:s')
					 ));
					 if ($query)
 					 {
             $menu= DB::table('menu_details')->where('user_id', $id)->first();
							if($menu->market_feedback=='yes')
 			        {
 			           return redirect('store/createmarketfeedback/'.$id);
 			        }
 			        else{
								$reportconfig=DB::table('configuration_filter')->where('user_id', '=', $id)->get();
								if(count($reportconfig)>0)
								{
 			           return redirect('store/createreportconfig/'.$id);
							  }
							  else{
									return redirect('store/dashboard/');
								}
 			        }
 					}
	}

	public function createmarketfeedback(){
		$marketfeedbackdetails = StoreDashboard::getAllMarketFeedbackDetailsTableData();
		return view('store/frocepower.marketfeedbackdetailscreate', compact('marketfeedbackdetails'));
	}
  public function storemarketfeedback($user_id) {
            $storemarketfeedback =DB::table('market_feedback_details')
                      ->where('user_id',$user_id)
                      ->first();
            $marketfeedbackdetails = StoreDashboard::getAllMarketFeedbackDetailsTableData();
            return view('store/frocepower.editmarketfeedback', compact('marketfeedbackdetails','storemarketfeedback'));
  }

	public function do_addmarketfeedback(Request $request){
  		$userid = Session::get('userid');
  		$nickname = Session::get('nickname');
  		$marketfeedbackid = 'M' . $nickname . date('ymdhmi');
  		$query = DB::table('market_feedback_details')->insert(array(
  			'market_feedback_id' => $marketfeedbackid,
  			'user_id' => $userid,
  			'nick_name' => $nickname,
  			'mf_group_enable' => $request->input('mf_group_enable') ,
  			'mf_col1' => $request->input('mf_col1') ,
  			'mf_col2' => $request->input('mf_col2') ,
  			'mf_col3' => $request->input('mf_col3') ,
  			'mf_col4' => $request->input('mf_col4') ,
  			'mf_sub_menu_details' => $request->input('mf_sub_menu_details') ,
  			'mf_sub_menu_image' => $request->input('mf_sub_menu_image') ,
  			'download_time' => date('Y-m-d H:i:s')
  		));
		  if ($query){
        $dbname='acedns_'.$nickname;
        $sql =DB::select("CREATE DATABASE $dbname");
			  return redirect('store/createmarketfeedback');
			}
	}
	public function do_editmarketfeedback(){

		$query=DB::table('market_feedback_details')
					 ->where('user_id', $id)
					 ->limit(1)
					 ->update(array(
						  'mf_group_enable' => Input::get('mf_group_enable') ,
	 						'mf_col1' => Input::get('mf_col1') ,
	 						'mf_col2' => Input::get('mf_col2') ,
	 						'mf_col3' => Input::get('mf_col3') ,
	 						'mf_col4' => Input::get('mf_col4') ,
	 						'mf_sub_menu_details' => Input::get('mf_sub_menu_details') ,
	 						'mf_sub_menu_image' => Input::get('mf_sub_menu_image') ,
	 						'download_time' => date('Y-m-d H:i:s')
					 ));

	}

	public function createreport(){
		return view('store/frocepower.reportconfiguration');
	}

	public function do_addreportconfig(Request $request){
		$userid = Session::get('userid');
		$nickname = Session::get('nickname');
		$marketfeedbackid = 'R' . $nickname . date('ymdhmi');
		$filtervalue = implode(',', $request->input('filter_value'));
		$standerreport = implode(',', $request->input('stander_report'));

		$query = DB::table('configuration_filter')->insert(array(
			'report_id' => $marketfeedbackid,
			'user_id' => $userid,
			'nick_name' => $nickname,
			'filter_value' => $filtervalue,
			'stander_report'=>$standerreport,
			'download_time' => date('Y-m-d H:i:s')
		));

	}

	public function storereportconfig($user_id){
		$storereportconfig =DB::table('configuration_filter')
							->where('user_id',$user_id)
							->first();
		return view('store/frocepower.editreportconfig', compact('storereportconfig'));
	}

	public function do_editreportconfig($id){

		$filtervalue = implode(',', Input::get('filter_value'));
		$standerreport = implode(',', Input::get('stander_report'));
		$query=DB::table('configuration_filter')
					 ->where('user_id', $id)
					 ->limit(1)
					 ->update(array(
						 'filter_value' => $filtervalue,
						 'stander_report'=>$standerreport,
						 'download_time' => date('Y-m-d H:i:s')
					 ));

			$storereportconfig =DB::table('configuration_filter')
			 							->where('user_id',$id)
			 							->first();
			return view('store/frocepower.editreportconfig', compact('storereportconfig'));


	}


	}
