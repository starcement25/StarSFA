<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$dns_code = $_REQUEST['dns_code'];
$emp_name = $_REQUEST['emp_name'];
$designation = $_REQUEST['designation'];
$hq = $_REQUEST['hq'];
$state = $_REQUEST['state'];
$zone = $_REQUEST['zone'];
$phn = $_REQUEST['phn'];
$email = $_REQUEST['email'];
$branch = $_REQUEST['branch'];
$reporting_to = $_REQUEST['reporting_to'];
$vertical_fields = $_REQUEST['vertical_fields'];
$sale_access = $_REQUEST['sale_access'];
$acedns = $_REQUEST['acedns'];

foreach($branch as $branch_value){
	$branch_string .= $branch_value.",";
}
$branch_string = rtrim($branch_string,",");

foreach($vertical_fields as $vertical_fields_value){
	$vertical_fields_string .= $vertical_fields_value.",";
}
$vertical_fields_string = rtrim($vertical_fields_string,",");

$sql_max_empid = "SELECT MAX(emp_code) as maxempcode FROM employee_master";
$res_max_empid = mysql_query($sql_max_empid);
$row_max_empid = mysql_fetch_array($res_max_empid);
$max_row_id = $row_max_empid['maxempcode'];
$max_row_id++;
$new_emp_code = $max_row_id;

/*---------------------------> INSERT INTO employee_master table <-------------------------------*/
$sql_insert_empmaster = "INSERT INTO `employee_master` SET 
								  `emp_code` = '".$new_emp_code."', 
							  `dns_emp_code` = '".$dns_code."', 
								  `emp_name` = '".$emp_name."', 
									`acedns` = '".$acedns."', 
							   `branch_code` = '".$branch_string."', 
							`vertical_value` = '".$vertical_fields_string."', 
							  `reporting_to` = '".$reporting_to."', 
									 `email` = '".$email."', 
								  `phone_no` = '".$phn."', 
										`HQ` = '".$hq."', 
							   `sale_access` = '".$sale_access."', 
							   `designation` = '".$designation."', 
									 `state` = '".$state."', 
									  `zone` = '".$zone."'";
$res_insert_empmaster = mysql_query($sql_insert_empmaster);
echo "Data inserted successfully";
mysql_close($link);
?>