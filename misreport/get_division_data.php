<?php
ob_start();
session_start();
require("adminUtils.php");

/*--------> Employee Hierarchy Condition <--------*/
if($_SESSION['admin_login']=="admin"){
	$emp_hierarchy = '';
	$emp_hierarchy_condition = '';
	$emp_hierarchy_condition_one='';
}
else{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition = " WHERE emp_code IN(".$emp_hierarchy.") ";
	$emp_hierarchy_condition_one = " AND SUBSTRING(survey_id,3,5) IN(".$emp_hierarchy.") ";
}

$zone = $_REQUEST['zone'];
$start_date = $_REQUEST['start_date'];
$start_date = date('Y-m-d',strtotime($_REQUEST['start_date']));
$end_date = $_REQUEST['end_date'];
$end_date  = date('Y-m-d',strtotime($_REQUEST['end_date']));
$type = $_REQUEST['type'];

if($zone != ''){
	if($zone == 'all')
		$zone_condition = "";
	else
		$zone_condition = " AND zone IN(".$zone.") ";
}

	
	/*$sql_emp_zone = "SELECT emp_code FROM employee_master WHERE ".$zone_condition.$emp_hierarchy_condition_one;
	$res_emp_zone = mysqli_query($link,$sql_emp_zone);
	while($row_emp_zone = mysqli_fetch_assoc($res_emp_zone)){
		$emp_code = $row_emp_zone['emp_code'];
		$emp_code_string .= "'".$emp_code."',";
	}
	$emp_code_string = rtrim($emp_code_string,",");	*/	
	$sql_division = "SELECT DISTINCT division FROM kiosk_transaction_details WHERE type='".$type."' AND DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y%-%m-%d') <='".$end_date."' 
				AND DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y%-%m-%d') >='".$start_date."'".$emp_hierarchy_condition_one.$zone_condition;
	$res_division = mysqli_query($link,$sql_division);
	$count_division=mysqli_num_rows($res_division);
	if($count_division >0)
	{
		$onclick = "division_region('".$type."');region_CCC('".$type."');";
		$division_string='';
		echo "<select name=\"division_id\" id=\"division_id\" onchange=\"".$onclick."\">";
		echo "<option value=\"\">Select</option>";
		echo "<option value=\"all\">All</option>";
		while($row_division = mysqli_fetch_assoc($res_division)){
			//$division_id = $row_division['division_id'];
			$division = $row_division['division'];
			
			echo "<option value=\"'".$division."'\">".$division."</option>";
			$division_string .= "'".$division."',";
		}
		echo "</select>";
	}
	else
	{
		echo "<font color='#FF0000'>No division found</font>";
	}
