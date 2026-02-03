<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");


$link=mysqli_connect(SERVER,USER,PASSWORD,DB);
if($_SESSION['admin_login']=="admin")
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition="";
	
}
else
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	//$emp_hierarchy_condition=" AND EM.reporting_to LIKE '%$_SESSION[admin_login]%";
	$emp_hierarchy_condition=" AND EM.emp_code IN (".$emp_hierarchy.") ";
}

$state_name = $_REQUEST['state_name'];
$sql_emp_name = "SELECT EM.emp_code, EM.emp_name FROM employee_master EM, customer_master CM WHERE EM.state LIKE '%".$state_name."%'".$emp_hierarchy_condition." AND CM.customer_code LIKE 'N%' AND CM.emp_code=EM.emp_code GROUP BY EM.emp_code ORDER BY EM.emp_name ASC";
$res_emp_name = mysqli_query($link,$sql_emp_name);
echo "<option value=''>Select Employee</option>";
echo "<option value='all'>All</option>";
while($row_emp_name = mysqli_fetch_assoc($res_emp_name))
{
	echo "<option value='".$row_emp_name['emp_code']."'>".$row_emp_name['emp_name']."</option>";
}
mysqli_close($link);
?>