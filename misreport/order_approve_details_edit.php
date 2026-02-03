<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$select_control_val = $_REQUEST['select_control_val'];
$select_val = $_REQUEST['select_val'];
$split_val=explode('-',$select_control_val);
$sqlupdate="UPDATE order_header SET status='".$select_val."' WHERE customer_code='".$split_val[1]."' AND 
			DATE_FORMAT(SUBSTRING(order_no,-14,8),'%d%m%Y')='".$split_val[0]."'";
mysqli_query($link,$sqlupdate);
mysqli_close($link);
?>
