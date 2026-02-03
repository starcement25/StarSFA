<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	$mall_id = $_REQUEST['mall_id'];
	$set_status = $_REQUEST['set_status'];
	
	$sql_update_mall_status = "UPDATE mall_master SET status = '".$set_status."' WHERE mall_id = '".$mall_id."'";
	$res_update_mall_status = mysqli_query($link,$sql_update_mall_status);
	
	echo "Status changed";
?>