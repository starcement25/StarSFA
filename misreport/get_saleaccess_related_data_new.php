<?php
ob_start();
session_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

/*--------> Employee Hierarchy Condition <--------*/
if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login'] == 'emovesfa_do' || strtoupper($_SESSION['admin_login']) == 'ACCOUNTS' ){
	$emp_hierarchy = '';
	$emp_hierarchy_condition = '';
	$emp_hierarchy_condition_one='';
	$branch_condition = " WHERE branch_code != '' ";
	$sale_access_condition = " WHERE sale_access != '' ";
	$hq_condition = " WHERE hq != '' ";
	$designation_condition = " WHERE designation != '' ";
}
else{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition = " WHERE emp_code IN(".$emp_hierarchy.") ";
	$emp_hierarchy_condition_one = " AND emp_code IN(".$emp_hierarchy.") ";
	$branch_condition = " AND branch_code != '' ";
	$sale_access_condition = " AND sale_access != '' ";
	$hq_condition = " AND hq != '' ";
	$designation_condition = " AND designation != '' ";
}

$sale_access = $_REQUEST['sale_access'];
$branch = $_REQUEST['branch'];
$type = $_REQUEST['type'];
$zone = $_REQUEST['zone'];
$state = $_REQUEST['state'];
$usertype=$_REQUEST['useractive'];

if($zone != ''){
	if($zone == 'all')
		$zone_condition = "";
	else
		$zone_condition = " AND zone IN(".$zone.") ";
}
	
if($state != ''){
	$state_condition = " AND state IN(".$state.") ";
}

/*--------> Check If Sale Access Exists <--------*/
$sql_sale_access = "SELECT DISTINCT sale_access FROM employee_master".$emp_hierarchy_value_condition.$sale_access_condition." ORDER BY sale_access ASC";
$res_sale_access = mysqli_query($link,$sql_sale_access);
$sale_access_total = mysqli_num_rows($res_sale_access);

if(strtoupper($_SESSION['nick_name']) != 'STAR'){
/*--------> Check If Headquarter Exists <--------*/
$sql_hq = "SELECT DISTINCT hq FROM employee_master".$emp_hierarchy_condition.$hq_condition." ORDER BY hq ASC";
$res_hq = mysqli_query($link,$sql_hq);
$hq_total = mysqli_num_rows($res_hq);

/*--------> Check If Designation Exists <--------*/
$sql_designation = "SELECT DISTINCT designation FROM employee_master".$emp_hierarchy_condition.$designation_condition." ORDER BY designation ASC";
$res_designation = mysqli_query($link,$sql_designation);
$designation_total = mysqli_num_rows($res_designation);
}

/*--------> HQ Data Populate <--------*/
if($type == 'hq'){
	if($designation_total>0)
		$onclick = "hq_designation(this.value);";
	else
		$onclick = "hq_emp(this.value);";
				
	echo "<select name=\"hq\" id=\"hq\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
		
	$sql_hq = "SELECT DISTINCT hq FROM employee_master WHERE sale_access IN (".$sale_access.")".$emp_hierarchy_condition_one." AND hq != '' ORDER BY hq ASC";
	$res_hq = mysqli_query($link,$sql_hq);
	while($row_hq = mysqli_fetch_assoc($res_hq)){
		$hq = $row_hq['hq'];
		$hq_string .= "'".$hq."',";
		echo "<option value=\"'".$hq."'\">".$hq."</option>";
	}
	$hq_string = rtrim($hq_string,",");
	echo "<option value=\"".$hq_string."\">All</option>";
	echo "</select>";
}
/*--------> Designation Data Populate <--------*/
else if($type == 'designation'){
	$onclick = "designation_emp(this.value);";
	
	echo "<select name=\"designation\" id=\"designation\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	
	
	$sql_designation = "SELECT DISTINCT designation FROM employee_master WHERE sale_access IN (".$sale_access.")".$emp_hierarchy_condition_one." AND designation != '' ORDER BY designation ASC";
	$res_designation = mysqli_query($link,$sql_designation);
	while($row_designation = mysqli_fetch_assoc($res_designation)){
		$designation = $row_designation['designation'];
		$designation_string .= "'".$designation."',";
		echo "<option value=\"'".$designation."'\">".$designation."</option>";
	}
	$designation_string = rtrim($designation_string,",");
	echo "<option value=\"".$designation_string."\">All</option>";
	echo "</select>";
}
/*--------> Employee Data Populate <--------*/
else if($type == 'emp'){
	if(strpos($_SERVER['HTTP_REFERER'],'yellow_card_excel_report.php') > 0) $onclick = "emp_dealer(this.value);";
	else $onclick = "adhoc_function(this.value);";
	if($branch == 'all'){
		$branch_condition_one = " AND branch_code != '' ";
	}
	else{
		$branch_condition_one = " AND FIND_IN_SET('".$branch."',branch_code) ";
	}
		
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE sale_access IN (".$sale_access.")".$emp_hierarchy_condition_one.$branch_condition_one.$zone_condition.$state_condition." AND acedns = '".$usertype."' ORDER BY emp_name ASC";
	$select_control = "<select name=\"employee\" id=\"employee\" onchange=\"".$onclick."\">";
	$select_control .= "<option value=\"\">Select</option>";
	
	
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE sale_access IN (".$sale_access.")".$emp_hierarchy_condition_one.$branch_condition_one.$zone_condition.$state_condition." AND acedns = '".$usertype."' ORDER BY emp_name ASC";
	$res_emp = mysqli_query($link,$sql_emp);
	while($row_emp = mysqli_fetch_assoc($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
		$select_control_option .= "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");
	
	if(strpos($_SERVER['HTTP_REFERER'],'route_plan_approve.php') > 0)
		$select_control .= '';
	elseif($_REQUEST['showall']==0)
	$select_control .= ''; 
	else
		$select_control .= "<option value=\"".$emp_code_string."\">All</option>";
	$select_control .= $select_control_option;
	$select_control .= "</select>";
	echo $select_control;
}
mysqli_close($link);
?>