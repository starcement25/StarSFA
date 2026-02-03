<?php
ob_start();
/* ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);*/
session_start();

require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require ("attribute_selection.php");
require("include/dbcon.php");

if($_SESSION['admin_login']=="")  		header("product:index.php");

$emp_code = $_REQUEST['emp_code'];

$sql_clear_allocation = "UPDATE changepassword SET deviceid = '', registrationid = '' WHERE emp_code = '".$emp_code."'";
$res_clear_allocation = mysqli_query($link,$sql_clear_allocation);

echo "Device Cleared";

mysqli_close($link);
?>