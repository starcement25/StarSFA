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

mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB);*/

$emp_code = $_REQUEST['emp_code'];
$flag = $_REQUEST['flag'];

$sql_check_TD_allocation = "SELECT * FROM TD_allocation_access WHERE emp_code = '".$emp_code."'";
$res_check_TD_allocation = mysql_query($sql_check_TD_allocation);
$row_exist = mysql_num_rows($res_check_TD_allocation);

if($row_exist > 0){
	$sql_update = "UPDATE TD_allocation_access SET flag = '".$flag."' WHERE emp_code = '".$emp_code."'";
	if(mysql_query($sql_update))
		echo "Data updated successfully";}
else{
	$sql_get_desig = "SELECT designation FROM employee_master WHERE emp_code = '".$emp_code."'";
	$res_get_desig = mysql_query($sql_get_desig);
	$row_get_desig = mysql_fetch_array($res_get_desig);
	$designation = $row_get_desig['designation'];
	
	$sql_insert = "INSERT INTO TD_allocation_access SET emp_code = '".$emp_code."', flag = '".$flag."', designation = '".$designation."', get_allocation = 'no'";
	if(mysql_query($sql_insert))
		echo "Data inserted successfully";}

mysql_close($link);
?>