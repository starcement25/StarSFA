<?php
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");
	require("include/functions.php");
	
$emp_code = $_GET['emp_code'];
$login_emp_code = $_GET['login_emp_code'];
$product_group_code = $_GET['product_group_code'];
$TD=$_GET['TD'];

$sql_allocated_TD="SELECT TD FROM TD_allocation WHERE product_filter_code ='".$product_group_code."' AND emp_code='".$emp_code."'";
$rs_allocated_TD=mysqli_query($link,$sql_allocated_TD);
$row_allocated_TD=mysqli_fetch_assoc($rs_allocated_TD);
$allocated_TD=$row_allocated_TD['TD'];
if($allocated_TD !='' && $allocated_TD >0)
{
	$emp_hierarchy_leaves=return_employee_hierarchy($emp_code);
	$emp_code_val="'".$emp_code."'";
	$emp_hierarchy_leaves=str_replace($emp_code_val,'',$emp_hierarchy_leaves);
	$emp_hierarchy_leaves=substr($emp_hierarchy_leaves,0,-1);
	
	$emp_hierarchy_condition='emp_code IN('.$emp_hierarchy_leaves.')';
	

	$sqlselmaxallocation="SELECT MAX(TD) AS max_td FROM TD_allocation WHERE product_filter_code ='".$product_group_code."' AND ".$emp_hierarchy_condition;
	$rsselmaxallocation=mysqli_query($link,$sqlselmaxallocation);
	$rowselmaxallocation=mysqli_fetch_assoc($rsselmaxallocation);
	$max_td=$rowselmaxallocation['max_td'];
	if($max_td =='')  $max_td=0;

	if($TD < $max_td)
	{
		echo 0;
	}
	else
	{
		echo 1;
	}
}
else
{
	echo 1;
}

mysqli_close($link);
?>