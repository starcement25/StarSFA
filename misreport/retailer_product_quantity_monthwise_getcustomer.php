<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");
	
$emp_code = $_REQUEST['emp_code'];
$emp_hierarchy = return_employee_hierarchy($emp_code);
$emp_hierarchy_condition = " EM.emp_code IN (".$emp_hierarchy.") ";
$customer_namecode_array = array();

echo "<option value=''>Select</option>";
echo $sql_select_customer = "SELECT CM.customer_code, CM.customer_name FROM customer_master CM, employee_master EM, prev_order_counting_master POCM WHERE ".$emp_hierarchy_condition." AND CM.emp_code=EM.emp_code AND POCM.customer_code=CM.customer_code AND CM.cust_type = 'R' ORDER BY CM.customer_name ASC";
$res_select_customer = mysqli_query($link,$sql_select_customer);
while($row_select_customer = mysqli_fetch_assoc($res_select_customer)){
	$customer_code = $row_select_customer['customer_code'];
	$customer_name = $row_select_customer['customer_name'];
	if($customer_name != ''){
		if(!in_array($customer_name,$customer_namecode_array))
			array_push($customer_namecode_array,$customer_name);
	}
	
}
foreach($customer_namecode_array as $val){
	echo "<option>".$val."</option>";
}

mysqli_close($link);
?>