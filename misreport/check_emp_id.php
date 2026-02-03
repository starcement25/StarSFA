<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$emp_id = $_REQUEST['emp_id'];
$res_check_empname = mysqli_query($link,"SELECT emp_code FROM employee_master WHERE dns_emp_code='".$emp_id."'");
$total_rows = mysqli_num_rows($res_check_empname);
if($total_rows>=1){
	echo "Employee id exists. Please provide a different id.";
}
else{
	echo "success";
}
mysqli_close($link);
?>