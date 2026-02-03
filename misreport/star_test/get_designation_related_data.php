<?php
ob_start();
session_start();
require("../adminUtils.php");

/*--------> Employee Hierarchy Condition <--------*/
if($_SESSION['admin_login']=="admin"){
	$emp_hierarchy = '';
	$emp_hierarchy_condition = '';
}
else{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition = " WHERE emp_code IN(".$emp_hierarchy.") ";
	$emp_hierarchy_condition_one = " AND emp_code IN(".$emp_hierarchy.") ";
}

$zone = $_REQUEST['zone'];
$state = $_REQUEST['state'];
$hq = $_REQUEST['hq'];
$designation = $_REQUEST['designation'];
$type = $_REQUEST['type'];

if($zone != '')
	$zone_condition = " AND zone IN(".$zone.") ";
if($state != '')
	$state_condition = " AND state IN(".$state.") ";
if($hq != '')
	$hq_condition = " AND hq IN(".$hq.") ";

/*--------> Employee Data Populate <--------*/
if($type == 'emp'){
	//$onclick = "table_structure_data();";
	
	echo "<select name=\"employee\" id=\"employee\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
		
	echo $sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE designation IN (".$designation.")".$zone_condition.$state_condition.$hq_condition.$emp_hierarchy_condition_one." AND acedns = 'Y' AND app_access = 'Y' ORDER BY emp_name ASC";
	$res_emp = mysql_query($sql_emp);
	while($row_emp = mysql_fetch_array($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
		echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");
	echo "<option value=\"".$emp_code_string."\">All</option>";
	echo "</select>";
}