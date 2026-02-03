<?php
ob_start();
session_start();
require("adminUtils.php");

/*--------> Employee Hierarchy Condition <--------*/

$dealer = $_REQUEST['dealer'];
$employee = $_REQUEST['employee'];

	$emp_hierarchy=return_employee_hierarchy(str_replace("'","",$employee));
	//$emp_hierarchy_condition = " AND SUNSTRING(YCD.yellow_card_no,2,5) IN(".$emp_hierarchy.") ";

$subdealer_select_control = "<select name=\"subdealercontrol\" id=\"subdealercontrol\" onchange=\"clear_display_div();\">";
$subdealer_select_control .= "<option value=\"\">Select</option>";

/*$sql_sub_dealer = "SELECT DISTINCT YCD.customer_code, CM.customer_name FROM customer_master CM, yellow_card_details YCD 
					WHERE YCD.customer_code = CM.customer_code AND CM.rds_tag IN(".$dealer.")  
					ORDER BY CM.customer_name ASC";*/
$sql_sub_dealer = "SELECT DISTINCT YCD.customer_code, CM.customer_name FROM customer_master CM, yellow_card_details YCD 
					WHERE YCD.customer_code = CM.customer_code AND YCD.linked_dealer_code IN(".$dealer.")  
					ORDER BY CM.customer_name ASC";					
$res_sub_dealer = mysql_query($sql_sub_dealer);
$total_rows = mysql_num_rows($res_sub_dealer);
if($total_rows>0){
	$res_sub_dealer = mysql_query($sql_sub_dealer);
	while($row_sub_dealer = mysql_fetch_array($res_sub_dealer)){
		$subdealer_code = $row_sub_dealer['customer_code'];
		$subdealer_name = $row_sub_dealer['customer_name'];
		$subdealer_code_string .= "'".$subdealer_code."',";
		$subdealer_select_control_option .= "<option value=\"'".$subdealer_code."'\">".$subdealer_name."</option>";
	}
	$subdealer_code_string = rtrim($subdealer_code_string,",");
	$subdealer_select_control .= "<option value=\"".$subdealer_code_string."\">All</option>";
	$subdealer_select_control .=$subdealer_select_control_option;
	$subdealer_select_control .= "</select>";
	echo $subdealer_select_control;
}
else{
	echo "No sub-dealer found";
}
?>
