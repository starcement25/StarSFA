<?php
ob_start();
session_start();
require("adminUtils.php");

/*--------> Employee Hierarchy Condition <--------*/
if($_SESSION['admin_login']=="admin" ||  strtoupper($_SESSION['admin_login'])=='ACCOUNTS'){
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
$branch = $_REQUEST['branch'];

if($designation == 'all')
	$designation_condition = " 1 ";
else
	$designation_condition = " designation IN (".trim($designation).") ";
if($zone != '')
{
	if($zone == 'all'){
		$zone_condition= " AND zone !=''";
	}
	else
	{
		$zone_condition = " AND zone IN(".trim($zone).") ";
	}
}
if($state != '')
	$state_condition = " AND state IN(".trim($state).") ";
if($hq != '')
{
	if($hq == 'all'){
		$hq_condition= " AND hq !=''";
	}
	else
	{
		$hq_condition = " AND hq IN(".trim($hq).") ";
	}
}
else $hq_condition="";
if($branch != '')
{
	if($branch == 'all'){
		$branch_condition= " AND branch_code !=''";
	}
	else
	{
		$branch_condition = " AND FIND_IN_SET(branch_code,'".$branch."') ";
	}
}
else $branch_condition="";
/*--------> Employee Data Populate <--------*/
if($type == 'emp'){
	//$onclick = "table_structure_data();";
//$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$designation_condition.$zone_condition.$state_condition.$hq_condition.$branch_condition.$emp_hierarchy_condition_one." AND acedns = 'Y' ORDER BY emp_name ASC";
	if(strpos($_SERVER['HTTP_REFERER'],'yellow_card_date_validation_exceptional.php') > 0)
	{
		$control_set = "<select name=\"employee\" id=\"employee\" onchange=\"".$onclick."\" multiple>";
	}
	else
	{
		$control_set = "<select name=\"employee\" id=\"employee\" onchange=\"".$onclick."\">";
	}
	$control_set .= "<option value=\"\">Select</option>";

	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$designation_condition.$zone_condition.$state_condition.$hq_condition.$branch_condition.$emp_hierarchy_condition_one." AND acedns = 'Y' ORDER BY emp_name ASC";
	$res_emp = mysql_query($sql_emp);
	while($row_emp = mysql_fetch_array($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
		$control_set_val .= "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");
	if(strpos($_SERVER['HTTP_REFERER'],'yellow_card_date_validation_exceptional.php') > 0)
	{
		$control_set .= "";
	}
	else
	{
	$control_set .= "<option value=\"".$emp_code_string."\">All</option>";
	}
	$control_set .= $control_set_val;
	$control_set .= "</select>";
	echo $control_set;
}
mysql_close($link);
?>
