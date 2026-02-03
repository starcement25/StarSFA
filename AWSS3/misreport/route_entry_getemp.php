<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

if($_SESSION['admin_login']=="admin"){
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
}
$branch_code = $_REQUEST['branch_code'];
$sql_get_emp = "SELECT EM.emp_code, EM.emp_name FROM employee_master EM WHERE 1".$emp_hierarchy_condition_one." AND FIND_IN_SET('".$branch_code."',EM.branch_code) AND EM.acedns!='N' AND EM.emp_code NOT IN (SELECT reporting_to FROM employee_master)";
$res_get_emp = mysql_query($sql_get_emp);
while($row_get_emp = mysql_fetch_array($res_get_emp)){
	$emp_code = $row_get_emp['emp_code'];
	$emp_name = $row_get_emp['emp_name'];
	
	echo "<input name=\"menu_checked[]\" type=\"checkbox\" value=\"".$emp_code."\" class=\"input_chk\" />:".$emp_name."<br>";
}
mysql_close($link);
?>