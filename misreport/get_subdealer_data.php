<?php
//ob_start();
session_start();
require("adminUtils.php");
require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");
require("include/functions.php");

/*--------> Employee Hierarchy Condition <--------*/

$employee = $_REQUEST['employee'];

/*if($_SESSION['admin_login']=="admin"){
	$emp_hierarchy = '';
	$emp_hierarchy_condition = '';
	$branch_condition = " WHERE branch_code != '' ";
	$sale_access_condition = " WHERE sale_access != '' ";
	$hq_condition = " WHERE hq != '' ";
	$designation_condition = " WHERE designation != '' ";
}
else{*/
	$emp_hierarchy=return_employee_hierarchy(str_replace("'","",$employee));
	$emp_hierarchy_condition = " WHERE emp_code IN(".$emp_hierarchy.") ";
	$emp_hierarchy_condition_one = " AND emp_code IN(".$emp_hierarchy.") ";
	$branch_condition = " AND branch_code != '' ";
	$sale_access_condition = " AND sale_access != '' ";
	$hq_condition = " AND hq != '' ";
	$designation_condition = " AND designation != '' ";
//}

$employee = $_REQUEST['employee'];

$subdealer_select_control = "<select name=\"subdealercontrol\" id=\"subdealercontrol\" onchange=\"clear_display_div();\">";
$subdealer_select_control .= "<option value=\"\">Select</option>";

$sql_sub_dealer = "SELECT DISTINCT YCD.customer_code, CM.customer_name FROM customer_master CM, yellow_card_details YCD WHERE YCD.customer_code = CM.customer_code AND SUBSTRING(YCD.yellow_card_no,2,5) IN(".$employee.") ORDER BY CM.customer_name ASC";
$res_sub_dealer = mysqli_query($link,$sql_sub_dealer);
$total_rows = mysqli_num_rows($res_sub_dealer);
if($total_rows>0){
	$res_sub_dealer = mysqli_query($link,$sql_sub_dealer);
	while($row_sub_dealer = mysqli_fetch_assoc($res_sub_dealer)){
		$subdealer_code = $row_sub_dealer['customer_code'];
		$subdealer_name = $row_sub_dealer['customer_name'];
		
		$subdealer_select_control .= "<option value=\"'".$subdealer_code."'\">".$subdealer_name."</option>";
	}
	
	$subdealer_select_control .= "</select>";
	echo $subdealer_select_control;
}
else{
	echo "No sub-dealer found";
}
?>
