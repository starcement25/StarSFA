<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>

<?php
$db = "acedns_".strtoupper($_SESSION['nick_name']);
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","$db");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);
?>

<?php
$emp_code = $_REQUEST['emp_code'];
$flag = $_REQUEST['flag'];

$sql_check_sauda_allocation = "SELECT * FROM sauda_allocation_access WHERE emp_code = '".$emp_code."'";
$res_check_sauda_allocation = mysqli_query($link,$sql_check_sauda_allocation);

$row_exist = mysqli_num_rows($res_check_sauda_allocation);

if($row_exist > 0)
{
	$sql_update = "UPDATE sauda_allocation_access SET flag = '".$flag."' WHERE emp_code = '".$emp_code."'";
	if(mysqli_query($link,$sql_update))
		echo "Data updated successfully";
}
else
{
	$sql_insert = "INSERT INTO sauda_allocation_access SET emp_code = '".$emp_code."', flag = '".$flag."'";
	if(mysqli_query($link,$sql_insert))
		echo "Data inserted successfully";
}
	
?>