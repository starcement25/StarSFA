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

/*$sqlquerycustomerroute="SELECT DISTINCT route_code FROM customer_route_emp_relation  WHERE 
					route_code IN(SELECT route_code FROM route_master) AND acedns='Y' 
					AND emp_code='".$emp_code."'";*/
if(strtoupper($_SESSION['nick_name'])=='GOLDSTONE')
{
						
$sqlquerycustomerroute="SELECT DISTINCT route_code FROM customer_route_emp_relation  WHERE 
					route_code IN(SELECT route_code FROM route_master) AND emp_code='".$emp_code."' AND acedns='Y'";					
$resultcustomerroute = mysqli_query($link,$sqlquerycustomerroute);
}
else
{
	$sqlquerycustomerroute="SELECT DISTINCT route_code FROM distributor_route_relation  WHERE 
					route_code IN(SELECT route_code FROM route_master) AND emp_code='".$emp_code."' AND acedns='Y'";					
$resultcustomerroute = mysqli_query($link,$sqlquerycustomerroute);
}
echo "<select name=\"route_swap[]\" id=\"route_swap\" multiple=\"multiple\" style=\"height: 150px;width: 200px;\">";
echo "<option value=\"\" selected>Select</option>";
while($rowscustomerroute = mysqli_fetch_assoc($resultcustomerroute)){
	$route_code=$rowscustomerroute['route_code'];
	$sqlroute="SELECT  route_name FROM route_master WHERE route_code='".$route_code."'";
	$rsroute=mysqli_query($link,$sqlroute);
	$rowroute=mysqli_fetch_assoc($rsroute);
	$route_name=preg_replace('/[\r\n]+/', '',$rowroute['route_name']);
	echo "<option value=\"'".$route_code."'\" ".$selected.">".$route_name."</option>";
}
echo "</select>";

?>	