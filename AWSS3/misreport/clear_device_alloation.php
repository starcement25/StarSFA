<?php
ob_start();
session_start();
require("adminUtils.php");
require ("attribute_selection.php");
if($_SESSION['admin_login']=="")  		header("product:index.php");

$emp_code = $_REQUEST['emp_code'];

$sql_clear_allocation = "UPDATE changepassword SET deviceid = '', registrationid = '' WHERE emp_code = '".$emp_code."'";
$res_clear_allocation = mysql_query($sql_clear_allocation);

echo "Device Cleared";

mysql_close($link);
?>