<?php
ob_start();
session_start();
require("adminUtils.php");

$employee = $_REQUEST['employee'];

$dealer_select_control = "<select name=\"dealercontrol\" id=\"dealercontrol\" onchange=\"dealer_subdealer(this.value);\">";
$dealer_select_control .= "<option value=\"\">Select</option>";

$sql_dealer = "SELECT DISTINCT rds_tag  FROM customer_master  WHERE emp_code IN(".$employee.")";
$res_dealer = mysql_query($sql_dealer);
$total_rows = mysql_num_rows($res_dealer);
if($total_rows>0){
	$res_dealer = mysql_query($sql_dealer);
	while($row_dealer = mysql_fetch_array($res_dealer)){
		$dealer_code = $row_dealer['rds_tag'];
		$sqldealer="SELECT customer_name FROM customer_master WHERE customer_code='".$dealer_code."'";
		$rsdealer=mysql_query($sqldealer);
		$rowdealer=mysql_fetch_array($rsdealer);
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
