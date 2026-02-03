<?php
//b_start();
session_start();
define("SERVERREMOTE","103.87.174.95");
	define("USERREMOTE","starsaat_dnsprod");
	define("PASSWORDREMOTE","dnsprod1234#");
	define("DBREMOTE","starsaathi_STARS");
		
	$link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,DBREMOTE) or die("Database Connection Error.");

/*--------> Employee Hierarchy Condition <--------*/

$employee = $_REQUEST['employee'];

if($_SESSION['admin_login']=="admin"){
	$emp_hierarchy = '';
	$emp_hierarchy_condition = '';
	$branch_condition = " WHERE branch_code != '' ";
	$sale_access_condition = " WHERE sale_access != '' ";
	$hq_condition = " WHERE hq != '' ";
	$designation_condition = " WHERE designation != '' ";
}
else{
	//$emp_hierarchy=return_employee_hierarchy(str_replace("'","",$employee));
	$emp_hierarchy_condition = " WHERE emp_code IN(".$emp_hierarchy.") ";
	$emp_hierarchy_condition_one = " AND emp_code IN(".$emp_hierarchy.") ";
	$branch_condition = " AND branch_code != '' ";
	$sale_access_condition = " AND sale_access != '' ";
	$hq_condition = " AND hq != '' ";
	$designation_condition = " AND designation != '' ";
}

$employee = $_REQUEST['employee'];
$dealer_select_control = "<select name=\"dealercontrol\" id=\"dealercontrol\" onchange=\"dealer_subdealer(this.value);\">";
$dealer_select_control .= "<option value=\"\">Select</option>";

/*$sql_dealer = "SELECT DISTINCT CM.rds_tag  FROM customer_master CM, yellow_card_details YCD WHERE YCD.customer_code = CM.customer_code 
		AND SUBSTRING(YCD.yellow_card_no,2,5) IN(".$employee.")";//comment of on 12-11-2019*/
//$sql_dealer = "SELECT DISTINCT CM.rds_tag  FROM customer_master CM, yellow_card_details YCD WHERE YCD.customer_code = CM.customer_code ";
$sql_dealer = "SELECT DISTINCT customer_code FROM customer_route_emp_relation WHERE emp_code IN(".$employee.") 
			AND customer_code IN(SELECT customer_id FROM allocation_details)";
$res_dealer = mysqli_query($link,$sql_dealer);
$total_rows = mysqli_num_rows($res_dealer);
if($total_rows>0){
	$res_dealer = mysqli_query($link,$sql_dealer);
	while($row_dealer = mysqli_fetch_assoc($res_dealer)){
		$dealer_code = $row_dealer['customer_code'];
		$sqldealer="SELECT customer_name FROM customer_master WHERE customer_id='".$dealer_code."'";
		$rsdealer=mysqli_query($link,$sqldealer);
		$rowdealer=mysqli_fetch_assoc($rsdealer);
		$dealername=$rowdealer['customer_name'];
		if($dealername !=''){
			$dealer_code_string .= "'".$dealer_code."',";
			$dealer_select_control_option .= "<option value=\"'".$dealer_code."'\">".$dealername."</option>";
		}
	}
	$dealer_code_string = rtrim($dealer_code_string,",");
	$dealer_select_control .= "<option value=\"".$dealer_code_string."\">All</option>";
	$dealer_select_control .= $dealer_select_control_option;
	$dealer_select_control .= "</select>";
	echo $dealer_select_control;
}
else{
	echo "No dealer found";
}
?>
