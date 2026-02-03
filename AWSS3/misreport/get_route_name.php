<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$emp_code = $_REQUEST['emp_code'];
$emp_hierarchy=return_employee_hierarchy($emp_code);

$sql_get_route = "SELECT DISTINCT RM.route_name, ERR.route_code FROM emp_route_relation ERR, route_master RM WHERE ERR.emp_code IN(".$emp_hierarchy.") AND ERR.route_code=RM.route_code";
$res_get_route = mysql_query($sql_get_route);
while($row_get_route = mysql_fetch_array($res_get_route)){
	echo "<option value=\"".$row_get_route['route_code']."\">".$row_get_route['route_name']."</option>";
}
mysql_close($link);
?>