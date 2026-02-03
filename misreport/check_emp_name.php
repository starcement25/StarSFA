<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$emp_name = $_REQUEST['emp_name'];
$res_check_empname = mysqli_query($link,"SELECT emp_code FROM employee_master WHERE emp_name='".$emp_name."'");
$total_rows = mysqli_num_rows($res_check_empname);
if($total_rows>=1){
	echo "Employee name exists. Please provide a different name.";
}
else{
	echo "success";
}
mysqli_close($link);
?>