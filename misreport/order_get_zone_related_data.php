<?php
ob_start();
session_start();
require("adminUtils.php");

/*--------> Employee Hierarchy Condition <--------*/
if($_SESSION['admin_login']=="admin"){
	$emp_hierarchy = '';
	$emp_hierarchy_condition = '';
	$state_condition = " WHERE state != '' ";
	$branch_condition = " WHERE branch_code != '' ";
	$sale_access_condition = " WHERE sale_access != '' ";
	$hq_condition = " WHERE hq != '' ";
	$designation_condition = " WHERE designation != '' ";
}
else{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition = " WHERE emp_code IN(".$emp_hierarchy.") ";
	$emp_hierarchy_condition_one = " AND emp_code IN(".$emp_hierarchy.") ";
	$state_condition = " AND state != '' ";
	$branch_condition = " AND branch_code != '' ";
	$sale_access_condition = " AND sale_access != '' ";
	$hq_condition = " AND hq != '' ";
	$designation_condition = " AND designation != '' ";
}

$vertical=$_REQUEST['vertical'];
$zone = $_REQUEST['zone'];
$type = $_REQUEST['type'];

if($zone != ''){
	if($zone == 'all')
		$zone_condition = "";
	else
		$zone_condition = " AND zone IN(".$zone.") ";
}
if(vertical_fields == 'yes' || $vertical!=''){
	if($vertical == 'MACROMAN')
		$vertical_condition = " AND vertical_value LIKE 'M%' ";
	else if($vertical == 'all')
		$vertical_condition = "";	
	else
		//$vertical_condition = " AND vertical_value = '".$vertical."'";
		$vertical_condition = " AND FIND_IN_SET( '".$vertical."',vertical_value)";	
}
/*--------> Check If Branch Exists <--------*/
$sql_branch = "SELECT DISTINCT SUBSTRING_INDEX(branch_code, ',', 1) AS branch_code FROM employee_master".$emp_hierarchy_condition.$branch_condition." ORDER BY branch_code ASC";
$res_branch = mysqli_query($link,$sql_branch);
$branch_total = mysqli_num_rows($res_branch);
	
/*--------> Check If Sale Access Exists <--------*/
$sql_sale_access = "SELECT DISTINCT sale_access FROM employee_master".$emp_hierarchy_condition.$sale_access_condition." ORDER BY sale_access ASC";
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
	/*--------> Check If State Exists <--------*/	
	$sql_state = "SELECT DISTINCT SUBSTRING_INDEX(state, ',', 1) AS state FROM employee_master".$emp_hierarchy_condition.$state_condition." ORDER BY state ASC";
	$res_state = mysqli_query($link,$sql_state);
	$state_total = mysqli_num_rows($res_state);


/*--------> State Data Populate <--------*/
if($type == 'state'){
	$onclick = "state_emp(this.value);";
	

	$select_control = "<select name=\"state\" id=\"state\" onchange=\"".$onclick."\">";
	$select_control .= "<option value=\"\">Select</option>";
	
	
	$sql_state = "SELECT DISTINCT SUBSTRING_INDEX(state, ',', 1) AS state FROM employee_master WHERE state != '' ".$zone_condition.$emp_hierarchy_condition_one." ORDER BY state ASC";
	$res_state = mysqli_query($link,$sql_state);
	while($row_state = mysqli_fetch_assoc($res_state)){
		$state = $row_state['state'];
		$state_string .= "'".$state."',";
		$select_control_option .= "<option value=\"'".$state."'\">".$state."</option>";
	}
	$state_string = rtrim($state_string,",");
	$select_control .= "<option value=\"all\">All</option>";
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
	$res_branch = mysqli_query($link,$sql_branch);
	while($row_branch = mysqli_fetch_assoc($res_branch)){
		$branch_code = $row_branch['branch_code'];
		$sql_branch_name = "SELECT branch_name FROM branch_master WHERE branch_code = '".$branch_code."'";
		$res_branch_name = mysqli_query($link,$sql_branch_name);
		$row_branch_name = mysqli_fetch_assoc($res_branch_name);
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
	$res_sale_access = mysqli_query($link,$sql_sale_access);
	while($row_sale_access = mysqli_fetch_assoc($res_sale_access)){
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
	
	
	$sql_designation = "SELECT DISTINCT designation FROM employee_master WHERE zone LIKE '%".$zone."%'".$emp_hierarchy_condition_one." AND designation != '' ORDER BY designation ASC";
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
	//$onclick = "designation_emp(this.value);";
	
	echo "<select name=\"employee\" id=\"employee\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	
	
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE zone LIKE '%".$zone."%'".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	$res_emp = mysqli_query($link,$sql_emp);
	while($row_emp = mysqli_fetch_assoc($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
		echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");
	echo "<option value=\"".$emp_code_string."\">All</option>";
	echo "</select>";
}
/*--------> Vertical Data Populate <--------*/
else if($type == 'vertical'){
	if($state_total > 0)
	{
		$onclick = "zone_state(this.value);";
	}
	else
		$onclick = "zone_emp(this.value);";

	$select_control = "<select name=\"zone\" id=\"zone\" onchange=\"".$onclick."\">";
	$select_control .= "<option value=\"\">Select</option>";
	
	$sql_zone = "SELECT DISTINCT zone FROM employee_master 
				WHERE zone != '' AND zone !='NULL' ".$vertical_condition.$emp_hierarchy_condition_one." ORDER BY zone ASC";
	$res_zone = mysqli_query($link,$sql_zone);
	while($row_zone = mysqli_fetch_assoc($res_zone)){
		$zone = $row_zone['zone'];
		$zone_string .= "'".$zone."',";
		$select_control_option .= "<option value=\"'".$zone."'\">".$zone."</option>";
	}
	$zone_string = rtrim($zone_string,",");
	$select_control .= "<option value=\"all\">All</option>";
	$select_control .= $select_control_option;
	$select_control .= "</select>";
	echo $select_control;
}

mysqli_close($link);
?>