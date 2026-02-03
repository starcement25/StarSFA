<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

$emp_code = $_REQUEST['emp_code'];

echo "<option value=''>Select Route</option>";
$sql_route = "SELECT route_code, route_name FROM route_master WHERE emp_code = '".$emp_code."'";
$res_route = mysql_query($sql_route);
while($row_route = mysql_fetch_array($res_route))
{
	echo "<option value='".$row_route['route_code']."'>".$row_route['route_name']."</option>";
}
mysql_close($link);
?>