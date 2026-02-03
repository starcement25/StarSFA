<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$emp_level=$_REQUEST['emp_level'];

echo "<select name=\"emp_details\" id=\"emp_details\" >";
echo "<option value=\"\">Select Employee</option>";
$sql_emp = "SELECT emp_code,emp_name,email,designation FROM employee_master WHERE level='".$emp_level."'  ORDER BY emp_name ASC";
		$res_emp = mysqli_query($link,$sql_emp);
		while($row_emp = mysqli_fetch_assoc($res_emp)){
			$emp_code = $row_emp['emp_code'];
			$emp_name = $row_emp['emp_name'];
			echo "<option value=\"".$emp_code."\">".$emp_name."</option>";
		}
echo "</select>";		
mysqli_close($link);
?>