<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>

<?php

$emp_code = $_GET['emp_code'];
$product_code = $_GET['product_code'];

$sql_quantity = "SELECT qty FROM sauda_allocation WHERE emp_code = '$emp_code' AND product_filter_code='$product_code'";
$res_quantity = mysqli_query($link,$sql_quantity);
$row_quantity = mysqli_fetch_assoc($res_quantity);

echo $quantity =(($row_quantity['qty']!='')?$row_quantity['qty']: 0);
?>