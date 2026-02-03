<?php	
    //-----------------------------Definition of setup attributes--------------------------//
	
	/*$linksetup=mysqli_connect(SERVER,USER,PASSWORD) or die("Setup Database Connection Error.");
	mysqli_select_db("acedns_acednsproduct",$linksetup) or die("could not connect the setup database");*/
	
		$linksetup=mysqli_connect("localhost","acedns_dnsprod","dnsprod1234#","acedns_acednsproduct") or die("Setup Database Connection Error.");
	
	$sqlproductdetails="SELECT no_of_filter,col1,col2,col3,col4,branch_wise_product,branch_wise_mrp,uom_wise_mrp,
						branch_wise_cl_stk,sauda_allocation_basedon_filter,destination_price_list,destination_ordertype_price_list,state_wise_mrp,multiple_rate,
						product_qty_wise_TD,state_wise_product FROM product_details WHERE nick_name='".$nick_name."'";
	$rsproductdetails=mysqli_query($linksetup,$sqlproductdetails);
	$rowproductdetails=mysqli_fetch_assoc($rsproductdetails);
	$no_of_filter=$rowproductdetails['no_of_filter'];
	$col1=$rowproductdetails['col1'];
	$col2=$rowproductdetails['col2'];
	$col3=$rowproductdetails['col3'];
	$col4=$rowproductdetails['col4'];
	$branch_wise_product=$rowproductdetails['branch_wise_product'];
	$branch_wise_mrp=$rowproductdetails['branch_wise_mrp'];
	$uom_wise_mrp=$rowproductdetails['uom_wise_mrp'];
	$branch_wise_cl_stk=$rowproductdetails['branch_wise_cl_stk'];
	$sauda_allocation_basedon_filter=$rowproductdetails['sauda_allocation_basedon_filter'];
	$destination_price_list=$rowproductdetails['destination_price_list'];
	$destination_ordertype_price_list=$rowproductdetails['destination_ordertype_price_list'];
	$state_wise_mrp=$rowproductdetails['state_wise_mrp'];
	$multiple_rate=$rowproductdetails['multiple_rate'];
	$product_qty_wise_TD=$rowproductdetails['product_qty_wise_TD'];
	$state_wise_product=$rowproductdetails['state_wise_product'];
	
	$sqlorderformdetails="SELECT mrp,TD,sale_rate,TD_type,sale_rate_input_dropdown,credit_limit,cl_stk,sale,
						  VAT,VAT_details,amount,TD_calc,TD_trans_type,VAT_calc_on,instruction,order_approval_process,TD_validation,TD_calc_basedon
						  ,premium,previous_order,order_type,freight_component,destination,tax_type,freight_cost,tagged_distributor_for_order,distributor_route_emp_relation,branch_wise_destination,multiple_UOM,
						  input_screen_planwise,sauda_sale_rate_input_dropdown,add_customer_activation,multiple_distributor,cust_type_wise_UOM_val 
						  FROM order_form_details WHERE nick_name='".$nick_name."'";
	$rsorderformdetails=mysqli_query($linksetup,$sqlorderformdetails);
	$roworderformdetails=mysqli_fetch_assoc($rsorderformdetails);
	$TD=$roworderformdetails['TD'];
	$TD_type=$roworderformdetails['TD_type'];
	$sale_rate=$roworderformdetails['sale_rate'];
	$sale_rate_input_dropdown=$roworderformdetails['sale_rate_input_dropdown'];
	$mrp=$roworderformdetails['mrp'];
	$credit_limit=$roworderformdetails['credit_limit'];
	$cl_stk=$roworderformdetails['cl_stk'];
	$sale=$roworderformdetails['sale'];
	$VAT=$roworderformdetails['VAT'];
	$VAT_details=$roworderformdetails['VAT_details'];
	$amount=$roworderformdetails['amount'];
	$TD_calc=$roworderformdetails['TD_calc'];
	$TD_trans_type=$roworderformdetails['TD_trans_type'];
	$VAT_calc_on=$roworderformdetails['VAT_calc_on'];
	$instruction=$roworderformdetails['instruction'];
	$order_approval_process=$roworderformdetails['order_approval_process'];
	$TD_validation=$roworderformdetails['TD_validation'];
	$TD_calc_basedon=$roworderformdetails['TD_calc_basedon'];
	$premium=$roworderformdetails['premium'];
	$previous_order=$roworderformdetails['previous_order'];
	$order_type=$roworderformdetails['order_type'];
	$freight_component=$roworderformdetails['freight_component'];
	$destination=$roworderformdetails['destination'];
	$tax_type=$roworderformdetails['tax_type'];
	$freight_cost=$roworderformdetails['freight_cost'];
	$tagged_distributor_for_order=$roworderformdetails['tagged_distributor_for_order'];
	$distributor_route_emp_relation=$roworderformdetails['distributor_route_emp_relation'];
	$branch_wise_destination=$roworderformdetails['branch_wise_destination'];
	$multiple_UOM=$roworderformdetails['multiple_UOM'];
	$input_screen_planwise=$roworderformdetails['input_screen_planwise'];
	$sauda_sale_rate_input_dropdown=$roworderformdetails['sauda_sale_rate_input_dropdown'];
	$add_customer_activation=$roworderformdetails['add_customer_activation'];
	$multiple_distributor=$roworderformdetails['multiple_distributor'];
	$cust_type_wise_UOM_val=$roworderformdetails['cust_type_wise_UOM_val'];
	
	$sqluserdetails="SELECT no_of_licensed_users,vertical_fields,employeewise_hierarchy,vertical_branch_relation,providing_code,
					email_hierarchywise,email_hierarchy_level,need_DCR,branch_vertical_operation_wise_email,previous_stock,stock_audit_rate,
					DCR_checkout,modified_customer_emp_route,employeewise_upperhierarchy,y_card_date_val_ineffictive_emp,stk_audit_msl,
					DCR_map,trans_download,trans_download_params,app_phoneno_login,customer_product_relation,stk_audit_mfd_date,
					departmentwise_geo_fencing_variance FROM user_details WHERE nick_name='".$nick_name."'";
	$rsuserdetails=mysqli_query($linksetup,$sqluserdetails);
	$rowuserdetails=mysqli_fetch_assoc($rsuserdetails);
	$no_of_licensed_users=$rowuserdetails['no_of_licensed_users'];
	$vertical_fields=$rowuserdetails['vertical_fields'];
	$vertical_branch_relation=$rowuserdetails['vertical_branch_relation'];
	$employeewise_hierarchy=$rowuserdetails['employeewise_hierarchy'];
	$email_hierarchy_level=$rowuserdetails['email_hierarchy_level'];
	$providing_code=$rowuserdetails['providing_code'];
	$email_hierarchywise=$rowuserdetails['email_hierarchywise'];
	$need_DCR=$rowuserdetails['need_DCR'];
	$branch_vertical_operation_wise_email=$rowuserdetails['branch_vertical_operation_wise_email'];
	$previous_stock=$rowuserdetails['previous_stock'];
	$stock_audit_rate=$rowuserdetails['stock_audit_rate'];
	$DCR_checkout=$rowuserdetails['DCR_checkout'];
	$modified_customer_emp_route=$rowuserdetails['modified_customer_emp_route'];
	$employeewise_upperhierarchy=$rowuserdetails['employeewise_upperhierarchy'];
	$y_card_date_val_ineffictive_emp=$rowuserdetails['y_card_date_val_ineffictive_emp'];
	$stk_audit_msl=$rowuserdetails['stk_audit_msl'];
	$DCR_map=$rowuserdetails['DCR_map'];
	$trans_download=$rowuserdetails['trans_download'];
	$trans_download_params=$rowuserdetails['trans_download_params'];
	$app_phoneno_login=$rowuserdetails['app_phoneno_login'];
	$customer_product_relation=$rowuserdetails['customer_product_relation'];
	$stk_audit_mfd_date=$rowuserdetails['stk_audit_mfd_date'];
	$departmentwise_geo_fencing_variance=$rowuserdetails['departmentwise_geo_fencing_variance'];
	
	$sqlmenudetails="SELECT route_plan,tour_exp,loyalty,stk_audit,delete_transaction,sauda_allocation,survey,product_promotion,
					market_feedback,collection,sauda_allocation_app,pending_contract,sauda_mis,`order`,order_status,
					sauda_outstanding,sale_performance,outstanding,outstanding_ageing,notes_and_info,target_achievement,self_appraisal,catalogue,
					TD_allocation_app,TD_allocation_vertical,run_time_TD_approval_vertical,business_prospect,CRM_app,yellow_card,schemes,
					retailer_app,branchwise_scheme_PDF,collection_forecast,retailer_care,golden_rules,van_sales,tour_exp_DA,bargain,DO,geo_fencing_menu,
					joint_work,app_order_approval FROM menu_details WHERE nick_name='".$nick_name."'";
	$rsmenudetails=mysqli_query($linksetup,$sqlmenudetails);
	$rowmenudetails=mysqli_fetch_assoc($rsmenudetails);
	$route_plan=$rowmenudetails['route_plan'];
	$tour_exp=$rowmenudetails['tour_exp'];
	$loyalty=$rowmenudetails['loyalty'];
	$stk_audit=$rowmenudetails['stk_audit'];
	$delete_transaction=$rowmenudetails['delete_transaction'];
	$sauda_allocation=$rowmenudetails['sauda_allocation'];
	$survey=$rowmenudetails['survey'];
	$product_promotion=$rowmenudetails['product_promotion'];
	$market_feedback=$rowmenudetails['market_feedback'];
	$collection=$rowmenudetails['collection'];
	$sauda_allocation_app=$rowmenudetails['sauda_allocation_app'];
	$pending_contract=$rowmenudetails['pending_contract'];
	$sauda_mis=$rowmenudetails['sauda_mis'];
	$order=$rowmenudetails['order'];
	$order_status=$rowmenudetails['order_status'];
	$sauda_outstanding=$rowmenudetails['sauda_outstanding'];
	$sale_performance=$rowmenudetails['sale_performance'];
	$outstanding=$rowmenudetails['outstanding'];
	$outstanding_ageing=$rowmenudetails['outstanding_ageing'];
	$notes_and_info=$rowmenudetails['notes_and_info'];
	$target_achievement=$rowmenudetails['target_achievement'];
	$self_appraisal=$rowmenudetails['self_appraisal'];
	$catalogue=$rowmenudetails['catalogue'];
	$TD_allocation_app=$rowmenudetails['TD_allocation_app'];
	$TD_allocation_vertical=$rowmenudetails['TD_allocation_vertical'];
	$run_time_TD_approval_vertical=$rowmenudetails['run_time_TD_approval_vertical'];
	$business_prospect=$rowmenudetails['business_prospect'];
	$CRM_app=$rowmenudetails['CRM_app'];
	$yellow_card=$rowmenudetails['yellow_card'];
	$schemes=$rowmenudetails['schemes'];
	$retailer_app=$rowmenudetails['retailer_app'];
	$branchwise_scheme_PDF=$rowmenudetails['branchwise_scheme_PDF'];
	$collection_forecast=$rowmenudetails['collection_forecast'];
	$retailer_care=$rowmenudetails['retailer_care'];
	$golden_rules=$rowmenudetails['golden_rules'];
	$van_sales=$rowmenudetails['van_sales'];
	$tour_exp_DA=$rowmenudetails['tour_exp_DA'];
	$bargain=$rowmenudetails['bargain'];
	$DO=$rowmenudetails['DO'];
	$geo_fencing_menu=$rowmenudetails['geo_fencing_menu'];
	$joint_work=$rowmenudetails['joint_work'];
	$app_order_approval=$rowmenudetails['app_order_approval'];
	
	$sqlrouteplandetails="SELECT route_plan_flow,route_plan_access_period,route_customer_planning,distributor_route_planning FROM 
						route_plan_details WHERE nick_name='".$nick_name."'";
	$rsrouteplandetails=mysqli_query($linksetup,$sqlrouteplandetails);
	$rowrouteplandetails=mysqli_fetch_assoc($rsrouteplandetails);
	$route_plan_flow=$rowrouteplandetails['route_plan_flow'];
	$route_plan_access_period=$rowrouteplandetails['route_plan_access_period'];
	$route_customer_planning=$rowrouteplandetails['route_customer_planning'];
	$distributor_route_planning=$rowrouteplandetails['distributor_route_planning'];
	
	$sqlsaudadetails="SELECT sauda_allocation_carry_forward,sauda_depot_wise,sauda_rate_variable,sauda_rate_variable_value,
					 sauda_booked_through,sauda_valid_from,sauda_rate_dependent_on_despatch_point,state_branch_wise_TD,incoterms_vertical FROM sauda_form_details WHERE nick_name='".$nick_name."'";
	$rssaudadetails=mysqli_query($linksetup,$sqlsaudadetails);
	$rowsaudadetails=mysqli_fetch_assoc($rssaudadetails);
	$sauda_allocation_carry_forward=$rowsaudadetails['sauda_allocation_carry_forward'];
	$sauda_depot_wise=$rowsaudadetails['sauda_depot_wise'];
	$sauda_rate_variable=$rowsaudadetails['sauda_rate_variable'];
	$sauda_rate_variable_value=$rowsaudadetails['sauda_rate_variable_value'];
	$sauda_booked_through=$rowsaudadetails['sauda_booked_through'];
	$sauda_valid_from=$rowsaudadetails['sauda_valid_from'];
	$sauda_rate_dependent_on_despatch_point=$rowsaudadetails['sauda_rate_dependent_on_despatch_point'];
	$state_branch_wise_TD=$rowsaudadetails['state_branch_wise_TD'];
	$incoterms_vertical=$rowsaudadetails['incoterms_vertical'];
	
	$sqlsurveydetails="SELECT survey_menu,survey_type,survey_type_details FROM survey_form_details WHERE nick_name='".$nick_name."'";
	$rssurveydetails=mysqli_query($linksetup,$sqlsurveydetails);
	$rowsurveydetails=mysqli_fetch_assoc($rssurveydetails);
	$survey_menu=$rowsurveydetails['survey_menu'];
	$survey_type=$rowsurveydetails['survey_type'];
	$survey_type_details=$rowsurveydetails['survey_type_details'];
	
	$sqlselfappraisaldetails="SELECT multiple_target_achievement,multiple_target_achievement_val,product_group_wise,customer_wise,previous_year,product_wise,
							achievement_sale_value_wise,week_wise
								FROM 
							  self_appraisal_details WHERE nick_name='".$nick_name."'";
	$rsselfappraisaldetails=mysqli_query($linksetup,$sqlselfappraisaldetails);
	$rowselfappraisaldetails=mysqli_fetch_assoc($rsselfappraisaldetails);
	$multiple_target_achievement=$rowselfappraisaldetails['multiple_target_achievement'];
	$multiple_target_achievement_val=$rowselfappraisaldetails['multiple_target_achievement_val'];
	$product_group_wise=$rowselfappraisaldetails['product_group_wise'];
	$customer_wise=$rowselfappraisaldetails['customer_wise'];
	$previous_year=$rowselfappraisaldetails['previous_year'];
	$product_wise=$rowselfappraisaldetails['product_wise'];
	$achievement_sale_value_wise=$rowselfappraisaldetails['achievement_sale_value_wise'];
	$week_wise=$rowselfappraisaldetails['week_wise'];

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
	define("multiple_rate",$multiple_rate);
	define("self_appraisal",$self_appraisal);
	define("multiple_target_achievement",$multiple_target_achievement);
	define("product_group_wise",$product_group_wise);
	define("catalogue",$catalogue);
	define("tagged_distributor_for_order",$tagged_distributor_for_order);
	define("distributor_route_emp_relation",$distributor_route_emp_relation);
	define("branch_wise_destination",$branch_wise_destination);
	define("multiple_UOM",$multiple_UOM);
	define("input_screen_planwise",$input_screen_planwise);
	define("product_qty_wise_TD",$product_qty_wise_TD);
	define("customer_wise_self_appraisal",$customer_wise);
	define("sauda_rate_dependent_on_despatch_point",$sauda_rate_dependent_on_despatch_point);
	define("state_branch_wise_TD",$state_branch_wise_TD);
	define("TD_allocation_app",$TD_allocation_app);
	define("TD_allocation_vertical",$TD_allocation_vertical);
	define("run_time_TD_approval_vertical",$run_time_TD_approval_vertical);
	define("business_prospect",$business_prospect);
	define("employeewise_upperhierarchy",$employeewise_upperhierarchy);
	define("sauda_sale_rate_input_dropdown",$sauda_sale_rate_input_dropdown);
	define("CRM_app",$CRM_app);
	define("y_card_date_val_ineffictive_emp",$y_card_date_val_ineffictive_emp);
	define("yellow_card",$yellow_card);
	define("schemes",$schemes);
	define("incoterms_vertical",$incoterms_vertical);
	define("stk_audit_msl",$stk_audit_msl);
	define("retailer_app",$retailer_app);
	define("previous_year",$previous_year);
	define("product_wise",$product_wise);
	define("add_customer_activation",$add_customer_activation);
	define("DCR_map",$DCR_map);
	define("trans_download",$trans_download);
	define("trans_download_params",$trans_download_params);
	define("multiple_distributor",$multiple_distributor);
	define("app_phoneno_login",$app_phoneno_login);
	define("branchwise_scheme_PDF",$branchwise_scheme_PDF);
	define("collection_forecast",$collection_forecast);
	define("retailer_care",$retailer_care);
	define("golden_rules",$golden_rules);
	define("van_sales",$van_sales);
	define("cust_type_wise_UOM_val",$cust_type_wise_UOM_val);
	define("achievement_sale_value_wise",$achievement_sale_value_wise);
	define("tour_exp_DA",$tour_exp_DA);
	define("bargain",$bargain);
	define("customer_product_relation",$customer_product_relation);
	define("delivery_order",$DO);
	define("week_wise",$week_wise);
	define("geo_fencing_menu",$geo_fencing_menu);
	define("stk_audit_mfd_date",$stk_audit_mfd_date);
	define("joint_work",$joint_work);
	define("departmentwise_geo_fencing_variance",$departmentwise_geo_fencing_variance);
	define("state_wise_product",$state_wise_product);
	define("app_order_approval",$app_order_approval);
	
	//------------------------------------------------Define setup------------------------------------------------
	
	$incremental_download=$_REQUEST['incremental_download'];
	//$last_update_time='2014-06-06 13:40:25';
	$last_update_time=$_REQUEST['last_update_time'];
	$last_update_time=str_replace('€',' ',$last_update_time);
	
	if($incremental_download=='yes')
	{
		$sqluserdetails="SELECT COUNT(user_id) AS total_users FROM user_details 
						WHERE nick_name='".$nick_name."' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
						
		$rsuserdetails=mysqli_query($linksetup,$sqluserdetails);
		$rowuserdetails=mysqli_fetch_assoc($rsuserdetails);
		$usersetupcnt=$rowuserdetails['total_users'];
						
		if($usersetupcnt >0)
		{
			define("user_details_download","yes");
		}
		else
		{
			define("user_details_download","no");
		}
		
		$sqlmenudetails="SELECT COUNT(menu_id) AS total_menus FROM menu_details 
						WHERE nick_name='".$nick_name."' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
		$rsmenudetails=mysqli_query($linksetup,$sqlmenudetails);
		$rowmenudetails=mysqli_fetch_assoc($rsmenudetails);
		$menusetupcnt=$rowmenudetails['total_menus'];
						
		if($menusetupcnt >0)
		{
			define("menu_details_download","yes");
		}
		else
		{
			define("menu_details_download","no");
		}
		
	    $sqlOTPmenudetails="SELECT COUNT(menu_id) AS total_menus FROM OTP_menu_details 
						WHERE nick_name='".$nick_name."' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
		$rsOTPmenudetails=mysqli_query($linksetup,$sqlOTPmenudetails);
		$rowOTPmenudetails=mysqli_fetch_assoc($rsOTPmenudetails);
		$menusetupcnt=$rowmenudetails['total_menus'];
		if($menusetupcnt >0)
		{
			define("OTP_menu_details_download","yes");
		}
		else
		{
			define("OTP_menu_details_download","no");
		}
		$sqlorderformdetails="SELECT COUNT(order_form_id) AS total_order_form_details FROM order_form_details 
						WHERE nick_name='".$nick_name."' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
		$rsorderformdetails=mysqli_query($linksetup,$sqlorderformdetails);
		$roworderformdetails=mysqli_fetch_assoc($rsorderformdetails);
		$orderformsetupcnt=$roworderformdetails['total_order_form_details'];
						
		if($orderformsetupcnt >0)
		{
			define("order_form_details_download","yes");
		}
		else
		{
			define("order_form_details_download","no");
		}
		
		$sqlproductdetails="SELECT COUNT(product_id) AS total_product_details FROM product_details 
						WHERE nick_name='".$nick_name."' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
		$rsproductdetails=mysqli_query($linksetup,$sqlproductdetails);
		$rowproductdetails=mysqli_fetch_assoc($rsproductdetails);
		$productsetupcnt=$rowproductdetails['total_product_details'];
						
		if($productsetupcnt >0)
		{
			define("product_details_download","yes");
		}
		else
		{
			define("product_details_download","no");
		}
		
		$sqlrouteplandetails="SELECT COUNT(route_plan_id) AS total_route_plan_details FROM route_plan_details 
						WHERE nick_name='".$nick_name."' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
		$rsrouteplandetails=mysqli_query($linksetup,$sqlrouteplandetails);
		$rowrouteplandetails=mysqli_fetch_assoc($rsrouteplandetails);
		$routeplansetupcnt=$rowrouteplandetails['total_route_plan_details'];
		if($routeplansetupcnt >0)
		{
			define("route_plan_details_download","yes");
		}
		else
		{
			define("route_plan_details_download","no");
		}
		
		$sqlsaudaformdetails="SELECT COUNT(sauda_form_id) AS total_sauda_form_details FROM sauda_form_details 
						WHERE nick_name='".$nick_name."' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
		$rssaudaformdetails=mysqli_query($linksetup,$sqlsaudaformdetails);
		$rowsaudaformdetails=mysqli_fetch_assoc($rssaudaformdetails);
		$saudaformsetupcnt=$rowsaudaformdetails['total_sauda_form_details'];
		if($saudaformsetupcnt >0)
		{
			define("sauda_form_details_download","yes");
		}
		else
		{
			define("sauda_form_details_download","no");
		}
		
		$sqlsurveyformdetails="SELECT COUNT(survey_form_id) AS total_survey_form_details FROM survey_form_details 
						WHERE nick_name='".$nick_name."' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
		$rssurveyformdetails=mysqli_query($linksetup,$sqlsurveyformdetails);
		$rowsurveyformdetails=mysqli_fetch_assoc($rssurveyformdetails);
		$surveyformsetupcnt=$rowsurveyformdetails['total_survey_form_details'];
		if($surveyformsetupcnt >0)
		{
			define("survey_form_details_download","yes");
		}
		else
		{
			define("survey_form_details_download","no");
		}
		$sqlmarketfeeddetails="SELECT COUNT(market_feedback_id) AS total_market_feedback_details FROM market_feedback_details 
						WHERE nick_name='".$nick_name."' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
		$rsmarketfeeddetails=mysqli_query($linksetup,$sqlmarketfeeddetails);
		$rowmarketfeeddetails=mysqli_fetch_assoc($rsmarketfeeddetails);
		$marketfeedbacksetupcnt=$rowmarketfeeddetails['total_market_feedback_details'];
		if($marketfeedbacksetupcnt >0)
		{
			define("market_feedback_details_download","yes");
		}
		else
		{
			define("market_feedback_details_download","no");
		}
		$sqlselfappdetails="SELECT COUNT(self_appraisal_id) AS total_selfapp_details FROM self_appraisal_details
 							WHERE nick_name='".$nick_name."' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
		$rsselfappdetails=mysqli_query($linksetup,$sqlselfappdetails);
		$rowselfappdetails=mysqli_fetch_assoc($rsselfappdetails);
		$selfappsetupcnt=$rowselfappdetails['total_selfapp_details'];
		if($selfappsetupcnt >0)
		{
			define("self_appraisal_details_download","yes");
		}
		else
		{
			define("self_appraisal_details_download","no");
		}					
	}
	//------------------------------------------------End of Define stup-------------------------------------------
	mysqli_close($linksetup);
?>