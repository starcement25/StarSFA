<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

$emp_code = $_REQUEST['emp_code'];
$link=mysqli_connect(SERVER,USER,PASSWORD,DB);
echo "<option value=''>Select Route</option>";
$sql_route = "SELECT route_code, route_name FROM route_master WHERE emp_code = '".$emp_code."'";
$res_route = mysqli_query($link,$sql_route);
while($row_route = mysqli_fetch_assoc($res_route))
{
	echo "<option value='".$row_route['route_code']."'>".$row_route['route_name']."</option>";
}
mysqli_close($link);
?>