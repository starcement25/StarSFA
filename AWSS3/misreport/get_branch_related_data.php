<?php
ob_start();
session_start();
require("adminUtils.php");

/*--------> Employee Hierarchy Condition <--------*/
if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login'] == 'emovesfa_do' || $_SESSION['admin_login']=='emovesfa_hr' || strtoupper($_SESSION['admin_login'])=='ACCOUNTS'){
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

$branch = $_REQUEST['branch'];
$type = $_REQUEST['type'];
$zone = $_REQUEST['zone'];
$state = $_REQUEST['state'];
$region = $_REQUEST['region'];

if($zone != ''){
	if($zone == 'all')
		$zone_condition = "";
	else
		$zone_condition = " AND zone IN(".$zone.") ";
}
	
if($state != ''){
	$state_condition = " AND state IN(".$state.") ";
}

if(strtoupper($_SESSION['nick_name']) == 'STAR' || strtoupper($_SESSION['nick_name']) == 'START'){
/*--------> Check If Sale Access Exists <--------*/
$sql_sale_access = "SELECT DISTINCT sale_access FROM employee_master".$emp_hierarchy_value_condition.$sale_access_condition." ORDER BY sale_access ASC";
$res_sale_access = mysql_query($sql_sale_access);
$sale_access_total = mysql_num_rows($res_sale_access);
}

if(strtoupper($_SESSION['nick_name']) != 'STAR' && strtoupper($_SESSION['nick_name']) != 'START'){
/*--------> Check If Headquarter Exists <--------*/
$sql_hq = "SELECT DISTINCT hq FROM employee_master".$emp_hierarchy_condition.$hq_condition." ORDER BY hq ASC";
$res_hq = mysql_query($sql_hq);
$hq_total = mysql_num_rows($res_hq);

/*--------> Check If Designation Exists <--------*/
$sql_designation = "SELECT DISTINCT designation FROM employee_master".$emp_hierarchy_condition.$designation_condition." ORDER BY designation ASC";
$res_designation = mysql_query($sql_designation);
$designation_total = mysql_num_rows($res_designation);
}

/*--------> Sale Access Data Populate <--------*/
if($type == 'sale_access'){
	if($hq_total>0)
		$onclick = "saleaccess_hq(this.value);";
	else if($designation_total>0)
		$onclick = "saleaccess_designation(this.value);";
	else if(strpos($_SERVER['HTTP_REFERER'],'menu_access_notaccessible.php') > 0)
		$onclick = "saleaccess_level(this.value);";	
	else
		$onclick = "saleaccess_emp(this.value);";
	
	$select_control = "<select name=\"sale_access\" id=\"sale_access\" onchange=\"".$onclick."\">";
	$select_control .= "<option value=\"\">Select</option>";
	
	if($branch == 'all'){
		$sale_access_condition = " branch_code != '' ";
	}
	else{
		$sale_access_condition = " FIND_IN_SET('".$branch."',branch_code) ";
	}
	
	if(strpos($_SERVER['HTTP_REFERER'],'SIS_report_ROE.php') > 0 || strpos($_SERVER['HTTP_REFERER'],'SIS_report_NE.php') > 0){
	$sql_sale_access = "SELECT DISTINCT sale_access FROM employee_master WHERE ".$sale_access_condition.$emp_hierarchy_condition_one." AND sale_access != '' AND sale_access = 'primary' AND region=".$region." ".$zone_condition.$state_condition." ORDER BY sale_access ASC";
	}
	else
	{
	  $sql_sale_access = "SELECT DISTINCT sale_access FROM employee_master WHERE ".$sale_access_condition.$emp_hierarchy_condition_one." AND sale_access != '' ".$zone_condition.$state_condition." ORDER BY sale_access ASC";

	}
	$res_sale_access = mysql_query($sql_sale_access);
	while($row_sale_access = mysql_fetch_array($res_sale_access)){
		$sale_access = $row_sale_access['sale_access']; 
		$sale_access_string .= "'".$sale_access."',";
		$select_control_option .= "<option value=\"'".$sale_access."'\">".$sale_access."</option>";
	}
	$sale_access_string = rtrim($sale_access_string,",");
	$select_control .= "<option value=\"".$sale_access_string."\">All</option>";
	$select_control .= $select_control_option;
	$select_control .= "</select>";
	echo $select_control;
}
/*--------> HQ Data Populate <--------*/
else if($type == 'hq'){
	if($designation_total>0)
		$onclick = "hq_designation(this.value);";
	else
		$onclick = "hq_emp(this.value);";
		
	if($branch == 'all'){
		$branch_condition = ' 1 ';
	}
	else{
		$branch_condition = " FIND_IN_SET(branch_code,'".$branch."') ";
	}
				
	echo "<select name=\"hq\" id=\"hq\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	echo "<option value=\"all\">All</option>";
		
	$sql_hq = "SELECT DISTINCT hq FROM employee_master WHERE ".$branch_condition.$emp_hierarchy_condition_one." AND hq != '' ORDER BY hq ASC";
	$res_hq = mysql_query($sql_hq);
	while($row_hq = mysql_fetch_array($res_hq)){
		$hq = $row_hq['hq'];
		$hq_string .= "'".$hq."',";
		echo "<option value=\"'".$hq."'\">".$hq."</option>";
	}
	/*$hq_string = rtrim($hq_string,",");
	echo "<option value=\"".$hq_string."\">All</option>";*/
	echo "</select>";
}
/*--------> Designation Data Populate <--------*/
else if($type == 'designation'){
	$onclick = "designation_emp(this.value);";
	
	echo "<select name=\"designation\" id=\"designation\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	
	
	$sql_designation = "SELECT DISTINCT designation FROM employee_master WHERE branch_code IN (".$branch.")".$emp_hierarchy_condition_one." AND designation != '' ORDER BY designation ASC";
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
	if(strpos($_SERVER['HTTP_REFERER'],'branding_verification_report.php') > 0 || strpos($_SERVER['HTTP_REFERER'],'branding_verification_with_location.php') > 0 || strpos($_SERVER['HTTP_REFERER'],'branding_verification_with_location_modified.php') > 0){
		if($branch=='all')
		{
			$branch_condition="";	
		}
		else
		{
			$branch_condition=" branch_code IN ('".$branch."')";	
		}
		$department_condition=" sale_access IN('Branding Verification','Branding')";
		$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$department_condition.$branch_condition.$emp_hierarchy_condition_one." ORDER BY emp_name ASC";	
	}
	else if(strpos($_SERVER['HTTP_REFERER'],'logistics_checkin_checkout.php') > 0){
		if($branch=='all')
		{
			$branch_condition="";	
		}
		else
		{
			$branch_condition=" branch_code IN ('".$branch."')";	
		}
		$department_condition=" sale_access='logistics'";
		$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$department_condition.$branch_condition.$emp_hierarchy_condition_one." ORDER BY emp_name ASC";	
	}
	else
	{
		$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE branch_code IN (".$branch.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	}

	echo "<select name=\"employee\" id=\"employee\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	
	
	/*$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE branch_code IN (".$branch.")".$departmaent_condition.$emp_hierarchy_condition_one." ORDER BY emp_name ASC";*/
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