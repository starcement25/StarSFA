<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

/*$db = "acedns_".strtoupper($_SESSION['nick_name']);
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","$db");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);*/

$emp_code = $_REQUEST['emp_code'];
$flag = $_REQUEST['flag'];

$sql_check_TD_allocation = "SELECT * FROM TD_allocation_access WHERE emp_code = '".$emp_code."'";
$res_check_TD_allocation = mysqli_query($link,$sql_check_TD_allocation);
$row_exist = mysqli_num_rows($res_check_TD_allocation);

if($row_exist > 0){
	$sql_update = "UPDATE TD_allocation_access SET flag = '".$flag."' WHERE emp_code = '".$emp_code."'";
	if(mysqli_query($link,$sql_update))
		echo "Data updated successfully";}
else{
	$sql_get_desig = "SELECT designation FROM employee_master WHERE emp_code = '".$emp_code."'";
	$res_get_desig = mysqli_query($link,$sql_get_desig);
	$row_get_desig = mysqli_fetch_assoc($res_get_desig);
	$designation = $row_get_desig['designation'];
	
	$sql_insert = "INSERT INTO TD_allocation_access SET emp_code = '".$emp_code."', flag = '".$flag."', designation = '".$designation."', get_allocation = 'no'";
	if(mysqli_query($link,$sql_insert))
		echo "Data inserted successfully";}

mysqli_close($link);
?>