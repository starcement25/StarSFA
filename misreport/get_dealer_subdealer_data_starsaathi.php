<?php
//ob_start();
session_start();
define("SERVERREMOTE","103.87.174.95");
	define("USERREMOTE","starsaat_dnsprod");
	define("PASSWORDREMOTE","dnsprod1234#");
	define("DBREMOTE","starsaathi_STARS");
		
	$link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,DBREMOTE) or die("Database Connection Error.");

/*--------> Employee Hierarchy Condition <--------*/

$dealer = $_REQUEST['dealer'];
$employee = $_REQUEST['employee'];

	//$emp_hierarchy=return_employee_hierarchy(str_replace("'","",$employee));
	//$emp_hierarchy_condition = " AND SUNSTRING(YCD.yellow_card_no,2,5) IN(".$emp_hierarchy.") ";
$subdealer_select_control = "<select name=\"subdealercontrol\" id=\"subdealercontrol\" onchange=\"clear_display_div();\">";
$subdealer_select_control .= "<option value=\"\">Select</option>";

/*$sql_sub_dealer = "SELECT DISTINCT YCD.customer_code, CM.customer_name FROM customer_master CM, yellow_card_details YCD 
					WHERE YCD.customer_code = CM.customer_code AND CM.rds_tag IN(".$dealer.")  
					ORDER BY CM.customer_name ASC";*/
if(strpos($_SERVER['HTTP_REFERER'],'allocation_details_summary_report.php') > 0)
{
	$sql_sub_dealer = "SELECT DISTINCT CM.customer_id AS customer_code, CM.customer_name FROM customer_master CM, allocation_details AD 
					WHERE AD.sub_dealer_id = CM.customer_id AND AD.customer_id IN(".$dealer.")  
					ORDER BY CM.customer_name ASC";
}
else{
$sql_sub_dealer = "SELECT DISTINCT YCD.customer_code, CM.customer_name FROM customer_master CM, yellow_card_details YCD 
					WHERE YCD.customer_code = CM.customer_code AND YCD.linked_dealer_code IN(".$dealer.")  
					ORDER BY CM.customer_name ASC";
}
$res_sub_dealer = mysqli_query($link,$sql_sub_dealer);
$total_rows = mysqli_num_rows($res_sub_dealer);
if($total_rows>0){
	$res_sub_dealer = mysqli_query($link,$sql_sub_dealer);
	while($row_sub_dealer = mysqli_fetch_assoc($res_sub_dealer)){
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
