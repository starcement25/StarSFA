<?php
ob_start();
session_start();
require("adminUtils.php");

/*--------> Employee Hierarchy Condition <--------*/
if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login'] == 'emovesfa_do' || $_SESSION['admin_login']=='emovesfa_hr' ||  strtoupper($_SESSION['admin_login'])=='ACCOUNTS' ){
	$emp_hierarchy = '';
	$emp_hierarchy_condition = '';
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

$zone = $_REQUEST['zone'];
$type = $_REQUEST['type'];
$region = $_REQUEST['region'];

if($zone != ''){
	if($zone == 'all')
		$zone_condition = "";
	else
		$zone_condition = " AND zone IN(".$zone.") ";
}

/*--------> Check If Branch Exists <--------*/
$sql_branch = "SELECT DISTINCT SUBSTRING_INDEX(branch_code, ',', 1) AS branch_code FROM employee_master".$emp_hierarchy_condition.$branch_condition." ORDER BY branch_code ASC";
$res_branch = mysql_query($sql_branch);
$branch_total = mysql_num_rows($res_branch);
	
/*--------> Check If Sale Access Exists <--------*/
$sql_sale_access = "SELECT DISTINCT sale_access FROM employee_master".$emp_hierarchy_condition.$sale_access_condition." ORDER BY sale_access ASC";
$res_sale_access = mysql_query($sql_sale_access);
$sale_access_total = mysql_num_rows($res_sale_access);

if(strtoupper($_SESSION['nick_name']) != 'STAR'){
/*--------> Check If Headquarter Exists <--------*/
$sql_hq = "SELECT DISTINCT hq FROM employee_master".$emp_hierarchy_condition.$hq_condition." ORDER BY hq ASC";
$res_hq = mysql_query($sql_hq);
$hq_total = mysql_num_rows($res_hq);

/*--------> Check If Designation Exists <--------*/
$sql_designation = "SELECT DISTINCT designation FROM employee_master".$emp_hierarchy_condition.$designation_condition." ORDER BY designation ASC";
$res_designation = mysql_query($sql_designation);
$designation_total = mysql_num_rows($res_designation);
}

/*--------> State Data Populate <--------*/
if($type == 'state'){
	if(strtoupper($_SESSION['nick_name']) == 'SKIPPER' || strtoupper($_SESSION['nick_name']) == 'ASL' || strtoupper($_SESSION['nick_name']) == 'NIMBUS'){
		if($designation_total>0)
			$onclick = "state_designation(this.value);";
		else
			$onclick = "state_emp(this.value);";
	}
	else if(strtoupper($_SESSION['nick_name']) == 'AJANTA'){
		if($designation_total>0)
			$onclick = "state_designation(this.value);";
		else
			$onclick = "state_emp(this.value);";
	}
	else
	{
		if($branch_total>0)
		{
			$onclick = "state_branch(this.value);";
		}
		else if($hq_total>0)
			$onclick = "state_hq(this.value);";
		else if($designation_total>0)
			$onclick = "state_designation(this.value);";
		else
			$onclick = "state_emp(this.value);";
	}
	if(strpos($_SERVER['HTTP_REFERER'],'SIS_report_ROE.php') > 0 || strpos($_SERVER['HTTP_REFERER'],'SIS_report_NE.php') > 0)
	{
		$region_condition=" AND region=".$region."";
	}
	else $region_condition='';
	$sql_state = "SELECT DISTINCT SUBSTRING_INDEX(state, ',', 1) AS state 
				FROM employee_master WHERE state != '' ".$zone_condition.$emp_hierarchy_condition_one.$region_condition." ORDER BY state ASC";
	$select_control = "<select name=\"state\" id=\"state\" onchange=\"".$onclick."\">";
	$select_control .= "<option value=\"\">Select</option>";
	$sql_state = "SELECT DISTINCT SUBSTRING_INDEX(state, ',', 1) AS state 
				FROM employee_master WHERE state != '' ".$zone_condition.$emp_hierarchy_condition_one.$region_condition." ORDER BY state ASC";
	$res_state = mysql_query($sql_state);
	while($row_state = mysql_fetch_array($res_state)){
		$state = $row_state['state'];
		$state_string .= "'".$state."',";
		$select_control_option .= "<option value=\"'".$state."'\">".$state."</option>";
	}
	$state_string = rtrim($state_string,",");
	$select_control .= "<option value=\"".$state_string."\">All</option>";
	$select_control .= $select_control_option;
	$select_control .= "</select>";
	echo $select_control;
}
/*--------> Branch Data Populate <--------*/
else if($type == 'branch'){
	if($sale_access_total>0)
		$onclick = "branch_saleaccess(this.value);";
	else if($hq_total>0)
		$onclick = "branch_hq(this.value);";
	else if($designation_total>0)
		$onclick = "branch_designation(this.value);";
	else
		$onclick = "branch_emp(this.value);";
	
	echo "<select name=\"branch\" id=\"branch\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	
	$sql_branch = "SELECT DISTINCT SUBSTRING_INDEX(branch_code, ',', 1) AS branch_code FROM employee_master WHERE zone IN (".$zone.")".$emp_hierarchy_condition_one." AND branch_code != '' ORDER BY branch_code ASC";
	$res_branch = mysql_query($sql_branch);
	while($row_branch = mysql_fetch_array($res_branch)){
		$branch_code = $row_branch['branch_code'];
		$sql_branch_name = "SELECT branch_name FROM branch_master WHERE branch_code = '".$branch_code."'";
		$res_branch_name = mysql_query($sql_branch_name);
		$row_branch_name = mysql_fetch_array($res_branch_name);
		$branch_name = $row_branch_name['branch_name'];
		$branch_string .= "'".$branch_code."',";
		echo "<option value=\"'".$branch_code."'\">".$branch_name."</option>";
	}
	$branch_string = rtrim($branch_string,",");
	echo "<option value=\"".$branch_string."\">All</option>";
	echo "</select>";
}
/*--------> Sale Access Data Populate <--------*/
else if($type == 'sale_access'){
	if($hq_total>0)
		$onclick = "saleaccess_hq(this.value);";
	else if($designation_total>0)
		$onclick = "saleaccess_designation(this.value);";
	else
		$onclick = "saleaccess_emp(this.value);";
	
	echo "<select name=\"sale_access\" id=\"sale_access\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	
	$sql_sale_access = "SELECT DISTINCT sale_access FROM employee_master WHERE zone IN (".$zone.")".$emp_hierarchy_condition_one." AND sale_access != '' ORDER BY sale_access ASC";
	$res_sale_access = mysql_query($sql_sale_access);
	while($row_sale_access = mysql_fetch_array($res_sale_access)){
		$sale_access = $row_sale_access['sale_access']; 
		$sale_access_string .= "'".$sale_access."',";
		echo "<option value=\"'".$sale_access."'\">".$sale_access."</option>";
	}
	$sale_access_string = rtrim($sale_access_string,",");
	echo "<option value=\"".$sale_access_string."\">All</option>";
	echo "</select>";
}

/*--------> Headquarter Data Populate <--------*/
else if($type == 'hq'){
	if($designation_total>0)
		$onclick = "hq_designation(this.value);";
	else
		$onclick = "hq_emp(this.value);";
				
	echo "<select name=\"hq\" id=\"hq\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
		
	$sql_hq = "SELECT DISTINCT hq FROM employee_master WHERE zone IN (".$zone.")".$emp_hierarchy_condition_one." AND hq != '' ORDER BY hq ASC";
	$res_hq = mysql_query($sql_hq);
	while($row_hq = mysql_fetch_array($res_hq)){
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
	
	
	$sql_designation = "SELECT DISTINCT designation FROM employee_master WHERE zone LIKE '%".$zone."%'".$emp_hierarchy_condition_one." AND designation != '' ORDER BY designation ASC";
	$res_designation = mysql_query($sql_designation);
	while($row_designation = mysql_fetch_array($res_designation)){
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
	//$onclick = "designation_emp(this.value);";
	
	echo "<select name=\"employee\" id=\"employee\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	
	
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE zone LIKE '%".$zone."%'".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
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
mysql_close($link);
?>