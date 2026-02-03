<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

/*if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
		$emp_hierarchy_condition_one='';
		$emp_upper_hierarchy='';
}
else
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition_one=' AND EM.emp_code IN('.$emp_hierarchy.')';
	$emp_upper_hierarchy=return_employee_upper_hierarchy($_SESSION['admin_login']);
}*/

$branch_code = $_REQUEST['branch_code'];
$sql_route = "SELECT route_code, route_name FROM route_master ORDER BY route_name ASC";
$res_route = mysql_query($sql_route);
while($row_route = mysql_fetch_array($res_route)){
	$route_code = $row_route['route_code'];
	$route_name = $row_route['route_name'];
	echo "<option value=\"".$route_code."\">".$route_name."</option>";
}
mysql_close($link);
?>