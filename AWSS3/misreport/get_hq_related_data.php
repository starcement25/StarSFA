<?php
ob_start();
session_start();
require("adminUtils.php");

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

$hq = $_REQUEST['hq'];
$type = $_REQUEST['type'];
$zone = $_REQUEST['zone'];
$branch = $_REQUEST['branch'];
$state = $_REQUEST['state'];

/*--------> Designation Data Populate <--------*/
if($type == 'designation'){
	$onclick = "designation_emp(this.value);";
	
	if($hq == 'all')
		$hq_condition = ' 1 ';
	else
		$hq_condition = " HQ IN (".$hq.") ";
		
	if($zone == 'all')
		$zone_condition = '';
	else
		$zone_condition = " AND zone IN (".$zone.") ";
		
	if($branch == 'all')
		$branch_condition = '';
	else
		$branch_condition = " AND FIND_IN_SET(branch_code,'".$branch."') ";
	
	if($state == 'all')
		$state_condition = '';
	else
		$state_condition = " AND state IN (".$state.") ";
	
	echo "<select name=\"designation\" id=\"designation\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	echo "<option value=\"all\">All</option>";
		
	$sql_designation = "SELECT DISTINCT designation FROM employee_master WHERE ".$hq_condition.$zone_condition.$branch_condition.$state_condition.$emp_hierarchy_condition_one." AND designation != '' ORDER BY designation ASC";
	$res_designation = mysql_query($sql_designation);
	while($row_designation = mysql_fetch_array($res_designation)){
		$designation = $row_designation['designation'];
		$designation_string .= "'".$designation."',";
		echo "<option value=\"'".$designation."'\">".$designation."</option>";
	}
	/*$designation_string = rtrim($designation_string,",");
	echo "<option value=\"".$designation_string."\">All</option>";*/
	echo "</select>";
}
/*--------> Employee Data Populate <--------*/
else if($type == 'emp'){
	//$onclick = "designation_emp(this.value);";
	
	$emp_control = "<select name=\"employee\" id=\"employee\" onchange=\"".$onclick."\">";
	$emp_control .= "<option value=\"\">Select</option>";
	
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE hq IN (".$hq.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	$res_emp = mysql_query($sql_emp);
	while($row_emp = mysql_fetch_array($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
		$emp_control_option .= "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");
	$emp_control .= "<option value=\"".$emp_code_string."\">All</option>";
	$emp_control .= $emp_control_option;
	$emp_control .= "</select>";
	echo $emp_control;
}
mysql_close($link);
?>