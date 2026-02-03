<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>

<?php
$sql_select_employee = "SELECT EM.emp_code, EM.emp_name, CM.customer_code, CM.customer_name, SH.sauda_no FROM employee_master EM, sauda_header SH, customer_master CM WHERE substring(SH.sauda_no,3,5) = EM.emp_code AND date_format(substring(SH.sauda_no,-14,8),'%d-%m-%Y')='05-08-2015' AND SH.customer_code = CM.customer_code ORDER BY EM.emp_name ASC";
$res_select_employee = mysql_query($sql_select_employee);
while($row_select_employee = mysql_fetch_array($res_select_employee))
{
	$emp_code = $row_select_employee['emp_code'];
	$emp_name = $row_select_employee['emp_name'];
	$customer_code = $row_select_employee['customer_code'];
	$customer_name = $row_select_employee['customer_name'];
	$sauda_no = $row_select_employee['sauda_no'];
	$emp_array[$emp_code][$customer_code] = $sauda_no;
}

echo "<pre>";
print_r($emp_array);
echo "</pre>";

echo $emp_hi = return_employee_hierarchy('E0027');
?>