<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
$state_name = $_REQUEST['state_name'];
if($_SESSION['admin_login']=="admin")
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition="";
	
}
else
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	//$emp_hierarchy_condition=" AND EM.reporting_to LIKE '%$_SESSION[admin_login]%";
	$emp_hierarchy_condition=" EM.emp_code IN (".$emp_hierarchy.") ";
}
if(modified_customer_emp_route == 'yes'){
	if($_SESSION['admin_login'] == 'admin'){
		$emp_get_condition = "";
	}
	else{
		$emp_get_condition = " AND ERR.emp_code IN(".$emp_hierarchy.")";
	}
	$sql_get_emp = "SELECT DISTINCT ERR.emp_code as emp_code FROM emp_route_relation, customer_master CM WHERE CM.route_code=ERR.route_code AND CM.customer_code LIKE 'N%'".$emp_get_condition;
	$res_get_emp = mysqli_query($link,$sql_get_emp);
	while($row_get_emp = mysqli_fetch_assoc($res_get_emp)){
		$get_emp_code .= "'".$row_get_emp['emp_code']."',";
	}
	$get_emp_code = rtrim($get_emp_code,",");
	$sql_emp_name = "SELECT EM.emp_code, EM.emp_name FROM employee_master EM WHERE EM.state LIKE '%".$state_name."%' AND EM.emp_code IN(".$get_emp_code.") GROUP BY EM.emp_code ORDER BY EM.emp_name ASC";
}
else{
	$sql_emp_name = "SELECT EM.emp_code, EM.emp_name FROM employee_master EM, customer_master CM WHERE EM.state LIKE '%".$state_name."%'".$emp_hierarchy_condition." AND CM.customer_code LIKE 'N%' AND CM.emp_code=EM.emp_code GROUP BY EM.emp_code ORDER BY EM.emp_name ASC";
}

//$state_name = $_REQUEST['state_name'];
/*$sql_emp_name = "SELECT EM.emp_code, EM.emp_name FROM employee_master EM, customer_master CM WHERE".$emp_hierarchy_condition." EM.state LIKE '%".$state_name."%' AND CM.customer_code LIKE 'N%' AND CM.emp_code=EM.emp_code GROUP BY EM.emp_code ORDER BY EM.emp_name ASC";*/
$res_emp_name = mysqli_query($link,$sql_emp_name);
echo "<option value=''>Select Employee</option>";
while($row_emp_name = mysqli_fetch_assoc($res_emp_name))
{
	echo "<option value='".$row_emp_name['emp_code']."'>".$row_emp_name['emp_name']."</option>";
}
?>