<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");
	
$emp_code = $_REQUEST['emp_code'];
$emp_hierarchy = return_employee_hierarchy($emp_code);
$emp_hierarchy_condition = " EM.emp_code IN (".$emp_hierarchy.") ";
$customer_namecode_array = array();
if(modified_customer_emp_route == 'yes'){
	$sql_getroute_employeewise = "SELECT DISTINCT ERR.route_code as route_code FROM emp_route_relation ERR WHERE ERR.emp_code IN(".$emp_hierarchy.")";
	$res_getroute_employeewise = mysql_query($sql_getroute_employeewise);
	while($row_getroute_employeewise = mysql_fetch_array($res_getroute_employeewise)){
		$get_route .= "'".$row_getroute_employeewise['route_code']."',";
	}
	$get_route = rtrim($get_route,',');
	$sql_select_customer = "SELECT CM.customer_code, CM.customer_name FROM customer_master CM, prev_order_counting_master POCM WHERE CM.route_code IN(".$get_route.") AND POCM.customer_code=CM.customer_code ORDER BY CM.customer_name ASC";
}
else{
	$sql_select_customer = "SELECT CM.customer_code, CM.customer_name FROM customer_master CM, employee_master EM, prev_order_counting_master POCM WHERE ".$emp_hierarchy_condition." AND CM.emp_code=EM.emp_code AND POCM.customer_code=CM.customer_code ORDER BY CM.customer_name ASC";
}

echo "<option value=''>Select</option>";

$res_select_customer = mysql_query($sql_select_customer);
while($row_select_customer = mysql_fetch_array($res_select_customer)){
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