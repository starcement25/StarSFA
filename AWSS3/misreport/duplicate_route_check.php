<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$route_name = $_REQUEST['route_name'];
$sql_check = "SELECT route_name FROM route_master WHERE route_name LIKE '%$route_name%'";
$res_check = mysql_query($sql_check);
$total_rows = mysql_num_rows($res_check);
if($total_rows>0)
 echo "EXIST";
 
mysql_close($link);
?>