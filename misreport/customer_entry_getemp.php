<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

/*if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
		$emp_hierarchy_condition_one='';
		$emp_upper_hierarchy='';
}
else
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition_one=' AND EM.emp_code IN('.$emp_hierarchy.')';
	$emp_upper_hierarchy=return_employee_upper_hierarchy($_SESSION['admin_login']);
}*/
$branch_code = $_REQUEST['branch_code'];
$sql_get_emp = "SELECT EM.emp_code, EM.emp_name FROM employee_master EM WHERE FIND_IN_SET('$branch_code',EM.branch_code) ORDER BY EM.emp_name ASC";
$res_get_emp = mysqli_query($link,$sql_get_emp);
while($row_get = mysqli_fetch_assoc($res_get_emp)){
	$emp_code = $row_get['emp_code'];
	$emp_name = $row_get['emp_name'];
	echo "<option value=\"".$emp_code."\">".$emp_name."</option>";
}

mysqli_close($link);
?>