<?php
ob_start();
session_start();
require("adminUtils.php");

$zone = $_REQUEST['zone'];
$start_date = $_REQUEST['start_date'];
$start_date = date('Y-m-d',strtotime($_REQUEST['start_date']));
$end_date = $_REQUEST['end_date'];
$end_date  = date('Y-m-d',strtotime($_REQUEST['end_date']));
$type = $_REQUEST['type'];
$region = $_REQUEST['region'];

if(isset($_REQUEST['division_id']))
{
	$division_id = $_REQUEST['division_id'];
}
if($zone != ''){
	if($zone == 'all')
		$zone_condition = "";
	else
		$zone_condition = " AND zone IN(".$zone.") ";
}
if($division_id != ''){
	if($division_id == 'all')
		$division_condition = "";
	else
		$division_condition = " AND division IN(".$division_id.") ";
}
/*--------> Branch Data Populate <--------*/
if($type == 'zone'){
 if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login'] == 'emovesfa_do' || $_SESSION['admin_login']=='emovesfa_hr' ||  strtoupper($_SESSION['admin_login'])=='ACCOUNTS' ){
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
/*--------> Check If State Exists <--------*/	
	$sql_state = "SELECT DISTINCT SUBSTRING_INDEX(state, ',', 1) AS state FROM employee_master".$emp_hierarchy_value_condition.$state_condition." ORDER BY state ASC";
	$res_state = mysql_query($sql_state);
	$state_total = mysql_num_rows($res_state);
	
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
		if($state_total>0)
			$onclick = "zone_state(this.value);";
		else if($branch_total>0)
			$onclick = "zone_branch(this.value);";
		else if($sale_access_total>0)
			$onclick = "zone_saleaccess(this.value);";
		else if($hq_total>0)
			$onclick = "zone_hq(this.value);";
		else if($designation_total>0)
			$onclick = "zone_designation(this.value);";
		else
			$onclick = "zone_emp(this.value);";
		
		$sql_zone = "SELECT DISTINCT SUBSTRING_INDEX(zone, ',', 1) AS zone FROM employee_master WHERE region IN (".$region.")".$emp_hierarchy_condition_one." AND zone != '' ORDER BY zone ASC";
		$res_zone = mysql_query($sql_zone);	
		$zone_select_control = "<select name=\"zone\" id=\"zone\" onchange=\"".$onclick."\">";
		$zone_select_control .= "<option value=\"\">Select</option>";
		$zone_select_control .= "<option value=\"all\">All</option>";
		$zone_total = mysql_num_rows($res_zone);
		while($row_zone = mysql_fetch_array($res_zone)){
			$zone = $row_zone['zone'];
			$zone_select_control .= "<option value=\"'".$zone."'\">".$zone."</option>";
			$zone_string .= "'".$zone."',";
		}
		$zone_string = rtrim($zone_string,",");
		$zone_select_control .= "</select>";
		$table_data .= $zone_select_control;
		echo $table_data .= "</td></tr>";
}
else
{
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
	if(isset($_REQUEST['division_id']))
	{
		$sql_region = "SELECT DISTINCT region FROM kiosk_transaction_details WHERE type='".$type."' AND DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y%-%m-%d') <='".$end_date."' 
					AND DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y%-%m-%d') >='".$start_date."'".$emp_hierarchy_condition_one.$zone_condition.
					$division_condition;
		$res_region = mysql_query($sql_region);
		$count_region=mysql_num_rows($res_region);
		if($count_region >0)
		{
			$onclick = "region_CCC('".$type."');";
		
			$region_string='';
			echo "<select name=\"region\" id=\"region\" onchange=\"".$onclick."\">";
			echo "<option value=\"\">Select</option>";
			echo "<option value=\"all\">All</option>";
			while($row_region = mysql_fetch_array($res_region)){
				//$division_id = $row_division['division_id'];
				$region = $row_region['region'];
				
				echo "<option value=\"'".$region."'\">".$region."</option>";
				$region_string .= "'".$region."',";
			}
			echo "</select>";
		}
		else
		{
			echo "<font color='#FF0000'>No region found</font>";
		}
	}
	else
	{
		echo "<font color='#FF0000'>No region found</font>";
	}
}

mysql_close($link);
?>