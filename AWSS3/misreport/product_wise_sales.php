<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");
disphtml("main();");

function main(){
	if(no_of_filter == 1)
		header('location:product_wise_sales_report.php');
	else if(no_of_filter == 2)
		header('location:productgroup_wise_sales_report.php');
	else if(no_of_filter == 3)
		header('location:productsubgroup_wise_sales_report.php');
	else if(no_of_filter == 4)
		header('location:productbrand_wise_sales_report.php');
}
?>