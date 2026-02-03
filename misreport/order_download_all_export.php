<?php
ob_start();
session_start();
require("adminUtils.php");
	require("include/config.php");
    require("include/config-setup.php");
	require("include/dbcon.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");


header("Content-type: application/octet-stream"); 
header("Content-Disposition: attachment; filename=Order_download_all.xls"); 
header("Pragma: no-cache"); 
header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 
echo ucwords($_SESSION['order_download_all_csv']);

?>