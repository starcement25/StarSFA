<?php
ob_start();
session_start();
require("adminUtils.php");
	if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=' AND emp_code IN('.$emp_hierarchy.')';
	}

$emp_code = $_REQUEST['emp_code'];

echo "<select name=\"emp_swap\" id=\"emp_swap\">";
	echo "<option value=\"\">Select</option>";
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE acedns='Y' AND emp_code!='".$emp_code."' ".$emp_hierarchy_condition." ORDER BY emp_name ASC";
	$res_emp = mysqli_query($link,$sql_emp);
	while($row_emp = mysqli_fetch_assoc($res_emp)){
		$emp_code_swap = $row_emp['emp_code'];
		$emp_name_swap = $row_emp['emp_name'];
		echo "<option value=\"'".$emp_code_swap."'\">".$emp_name_swap."</option>";
	}
	echo "</select>";
?>	