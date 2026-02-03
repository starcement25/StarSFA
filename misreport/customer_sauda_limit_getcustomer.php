<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");
	
$emp_code = $_REQUEST['emp_code'];
if(strpos($emp_code,",")!=false)
{
	$emp_hierarchy_condition=" emp_code IN(".$emp_code.") ";
}
else
{
	/*$emp_hierarchy = return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition = " emp_code IN (".$emp_hierarchy.") ";*/
	$emp_hierarchy_condition=" emp_code IN(".$emp_code.") ";
}
$customer_namecode_array = array();

echo "<option value=''>Select</option>";
//echo "<option value='all' >All</option>";

$sql_select_customer = "SELECT dns_customer_code, customer_name FROM customer_master  WHERE ".$emp_hierarchy_condition." AND acedns='Y' 
						ORDER BY customer_name ASC";
$res_select_customer = mysqli_query($link,$sql_select_customer);
while($row_select_customer = mysqli_fetch_assoc($res_select_customer)){
	$customer_code = $row_select_customer['dns_customer_code'];
	$customer_name = $row_select_customer['customer_name'];
	if($customer_name != ''){
		/*if(!in_array($customer_name,$customer_namecode_array))
			array_push($customer_namecode_array,$customer_name);*/
			echo "<option value='".$customer_code."'>".$customer_name."</option>";
			$customer_code_string .= "'".$customer_code."',";
	}
}
$customer_code_string = rtrim($customer_code_string,",");
echo "<option value=\"".$customer_code_string."\">All</option>";
?>