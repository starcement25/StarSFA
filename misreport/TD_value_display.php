<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>

<?php

$emp_code = $_GET['emp_code'];
$product_code = $_GET['product_code'];

$sql_TD = "SELECT TD FROM TD_allocation WHERE emp_code = '$emp_code' AND product_filter_code='$product_code'";
$res_TD = mysqli_query($link,$sql_TD);
$row_TD = mysqli_fetch_assoc($res_TD);

echo $TD =(($row_TD['TD']!='')?$row_TD['TD']: 0);
?>