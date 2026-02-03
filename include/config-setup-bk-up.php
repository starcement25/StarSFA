<?php	
    //-----------------------------Definition of setup attributes--------------------------//
	
	$linksetup=mysqli_connect(SERVER,USER,PASSWORD) or die("Setup Database Connection Error.");
	mysqli_select_db("acednsproduct",$linksetup) or die("could not connect the setup database");
	$sqlproductdetails="SELECT no_of_filter,col1,col2,col3,col4 FROM product_details WHERE nick_name='".$nick_name."'";
	$rsproductdetails=mysqli_query($link,$sqlproductdetails,$linksetup);
	$rowproductdetails=mysqli_fetch_assoc($rsproductdetails);
	$no_of_filter=$rowproductdetails['no_of_filter'];
	$col1=$rowproductdetails['col1'];
	$col2=$rowproductdetails['col2'];
	$col3=$rowproductdetails['col3'];
	$col4=$rowproductdetails['col4'];
	
	$sqlorderformdetails="SELECT mrp,TD,sale_rate,TD_type,sale_rate_input_dropdown,credit_limit,cl_stk FROM order_form_details WHERE nick_name='".$nick_name."'";
	$rsorderformdetails=mysqli_query($link,$sqlorderformdetails,$linksetup);
	$roworderformdetails=mysqli_fetch_assoc($rsorderformdetails);
	$TD=$roworderformdetails['TD'];
	$TD_type=$roworderformdetails['TD_type'];
	$sale_rate=$roworderformdetails['sale_rate'];
	$sale_rate_input_dropdown=$roworderformdetails['sale_rate_input_dropdown'];
	$mrp=$roworderformdetails['mrp'];
	$credit_limit=$roworderformdetails['credit_limit'];
	$cl_stk=$roworderformdetails['cl_stk'];
	
	$sqluserdetails="SELECT no_of_licensed_users,vertical_fields,employeewise_hierarchy,vertical_branch_relation,providing_code FROM 
					user_details WHERE nick_name='".$nick_name."'";
	$rsuserdetails=mysqli_query($link,$sqluserdetails,$linksetup);
	$rowuserdetails=mysqli_fetch_assoc($rsuserdetails);
	$no_of_licensed_users=$rowuserdetails['no_of_licensed_users'];
	$vertical_fields=$rowuserdetails['vertical_fields'];
	$vertical_branch_relation=$rowuserdetails['vertical_branch_relation'];
	$employeewise_hierarchy=$rowuserdetails['employeewise_hierarchy'];
	$providing_code=$rowuserdetails['providing_code'];
	
	$sqlmenudetails="SELECT route_plan,tour_exp,loyalty FROM menu_details WHERE nick_name='".$nick_name."'";
	$rsmenudetails=mysqli_query($link,$sqlmenudetails,$linksetup);
	$rowmenudetails=mysqli_fetch_assoc($rsmenudetails);
	$route_plan=$rowmenudetails['route_plan'];
	$tour_exp=$rowmenudetails['tour_exp'];
	$loyalty=$rowmenudetails['loyalty'];
	
	
	define("col1",$col1);
	define("col2",$col2);
	define("col3",$col3);
	define("col4",$col4);
	define("no_of_filter",$no_of_filter);
	define("TD",$TD);
	define("TD_type",$TD_type);
	define("sale_rate",$sale_rate);
	define("sale_rate_input_dropdown",$sale_rate_input_dropdown);
	define("no_of_licensed_users",$no_of_licensed_users);
	define("vertical_fields",$vertical_fields);
	define("vertical_branch_relation",$vertical_branch_relation);
	define("employeewise_hierarchy",$employeewise_hierarchy);
	define("credit_limit",$credit_limit);
	define("cl_stk",$cl_stk);
	define("route_plan",$route_plan);
	define("tour_exp",$tour_exp);
	define("loyalty",$loyalty);
	define("mrp",$mrp);
	define("providing_code",$providing_code);
	mysqli_close($linksetup);
?>