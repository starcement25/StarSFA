<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>

<?php

$emp_code = $_REQUEST['emp_code'];
$product_code = $_REQUEST['product_code'];
$quantity = $_REQUEST['quantity'];

$sql_insert_sauda_allocation = "INSERT INTO TD_allocation SET 
															   emp_code = '$emp_code', 
													product_filter_code = '$product_code', 
																	 TD = '$quantity'";
$res_insert_sauda_allocation = mysql_query($sql_insert_sauda_allocation);
	
$sql_insert_sauda_log = "INSERT into TD_allocation_log SET 
															   emp_code = '$emp_code', 
													product_filter_code = '$product_code', 
																	TD = '$quantity'";
$res_insert_sauda_log = mysql_query($sql_insert_sauda_log);

echo "Data successfully inserted";

mysql_close($link);
//echo "$emp_code $product_code $quantity";
?>