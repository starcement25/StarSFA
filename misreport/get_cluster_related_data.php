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
	$cluster_condition = " WHERE cluster != '' ";
	$designation_condition = " WHERE designation != '' ";
}
else{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition = " WHERE emp_code IN(".$emp_hierarchy.") ";
	$emp_hierarchy_condition_one = " AND emp_code IN(".$emp_hierarchy.") ";
	$branch_condition = " AND branch_code != '' ";
	$sale_access_condition = " AND sale_access != '' ";
	$hq_condition = " AND hq != '' ";
	$cluster_condition = " AND cluster != '' ";
	$designation_condition = " AND designation != '' ";
}

$branch = $_REQUEST['branch'];
$type = $_REQUEST['type'];
$zone = $_REQUEST['zone'];
$state = $_REQUEST['state'];
$region = $_REQUEST['region'];
$cluster = $_REQUEST['cluster'];

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
$res_sale_access = mysqli_query($link,$sql_sale_access);
$sale_access_total = mysqli_num_rows($res_sale_access);
}

if(strtoupper($_SESSION['nick_name']) != 'STAR' && strtoupper($_SESSION['nick_name']) != 'START'){
/*--------> Check If Headquarter Exists <--------*/
$sql_hq = "SELECT DISTINCT hq FROM employee_master".$emp_hierarchy_condition.$hq_condition." ORDER BY hq ASC";
$res_hq = mysqli_query($link,$sql_hq);
$hq_total = mysqli_num_rows($res_hq);

/*--------> Check If Designation Exists <--------*/
$sql_designation = "SELECT DISTINCT designation FROM employee_master".$emp_hierarchy_condition.$designation_condition." ORDER BY designation ASC";
$res_designation = mysqli_query($link,$sql_designation);
$designation_total = mysqli_num_rows($res_designation);
}

/*--------> Sale Access Data Populate <--------*/
if($type == 'route'){
	/*echo $sql_route = "SELECT route_code,dns_route_code,route_name FROM route_master WHERE 
	route_code IN(SELECT DISTINCT route_code FROM customer_master WHERE cluster IN (".$cluster.") AND acedns='Y') AND route_name != '' ORDER BY route_name ASC";*/
	echo "<select name=\"route\" id=\"route\" >";
	echo "<option value=\"\">Select</option>";
	$route_code_array = array();
	$route_name_array = array();
	$sql_route = "SELECT route_code,dns_route_code,route_name FROM route_master WHERE 
	route_code IN(SELECT DISTINCT route_code FROM customer_master WHERE cluster IN (".$cluster.") AND acedns='Y') AND route_name != '' ORDER BY route_name ASC";
	$res_route = mysqli_query($link,$sql_route);
	while($row_route = mysqli_fetch_assoc($res_route)){
		$route_code = $row_route['route_code'];
		$dns_route_code = $row_route['dns_route_code'];
		$route_name = $row_route['route_name'];
		array_push($route_code_array,$route_code);
		array_push($route_name_array,$route_name);

		$route_string .= "'".$route_code."',";
		//echo "<option value=\"'".$route_code."'\">".$route_name." - (".$dns_route_code.")</option>";
	}
	$route_string = rtrim($route_string,",");
	echo "<option value=\"".$route_string."\">All</option>";
	for($i=0;$i<count($route_code_array);$i++)
	{
		echo "<option value=\"'".$route_code_array[$i]."'\">".$route_name_array[$i]."</option>";
	}
	echo "</select>";
}
else if($type == 'cluster'){
	//echo $sql_route = "SELECT route_code,route_name FROM route_master WHERE branch_code IN (".$branch.") AND route_name != '' ORDER BY route_name ASC";
	$onclick = "cluster_route(this.value);";
	echo "<select name=\"cluster\" id=\"cluster\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	$route_code_array = array();
	$cluster_name_array = array();
	$sql_cluster = "SELECT DISTINCT cluster FROM customer_master WHERE branch_code IN (".$branch.") AND cluster != '' ORDER BY cluster ASC";
	$res_cluster = mysqli_query($link,$sql_cluster);
	while($row_cluster = mysqli_fetch_assoc($res_cluster)){
		$cluster = $row_cluster['cluster'];
		array_push($cluster_name_array,$cluster);

		$cluster_string .= "'".$cluster."',";
		//echo "<option value=\"'".$route_code."'\">".$route_name." - (".$dns_route_code.")</option>";
	}
	$cluster_string = rtrim($cluster_string,",");
	echo "<option value=\"".$cluster_string."\">All</option>";
	for($i=0;$i<count($cluster_name_array);$i++)
	{
		echo "<option value=\"'".$cluster_name_array[$i]."'\">".$cluster_name_array[$i]."</option>";
	}
	echo "</select>";
}
mysqli_close($link);
?>