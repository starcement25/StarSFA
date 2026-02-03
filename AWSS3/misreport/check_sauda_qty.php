<?php
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");
	
	
$emp_code = $_GET['emp_code'];
$login_emp_code = $_GET['login_emp_code'];
$product_group_code = $_GET['product_group_code'];
$quantity=$_GET['quantity'];
if($login_emp_code =='SFATS')
{
	$login_emp_code='E0076';
}
if($login_emp_code !="E0076"){
	$sql_allocated_qty="SELECT qty FROM sauda_allocation WHERE product_filter_code ='".$product_group_code."' AND emp_code='".$login_emp_code."'";
	$rs_allocated_qty=mysql_query($sql_allocated_qty);
	$row_allocated_qty=mysql_fetch_array($rs_allocated_qty);
	$allocated_qty=$row_allocated_qty['qty'];
	
	$sql_total_qty="SELECT SUM(qty) AS total_qty FROM sauda_allocation WHERE product_filter_code ='".$product_group_code."' 
					AND emp_code IN(SELECT emp_code FROM employee_master WHERE reporting_to='".$login_emp_code."' AND emp_code <>'".$emp_code."')";
	$rs_total_qty=mysql_query($sql_total_qty);
	$row_total_qty=mysql_fetch_array($rs_total_qty);
	$total_qty=$row_total_qty['total_qty'];
	$total_qty=$total_qty+$quantity;
	
	if($total_qty > $allocated_qty)
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
	$sqlchildcount="SELECT COUNT(emp_code) as tot_emp FROM employee_master WHERE reporting_to='".$emp_code."'";
	$rschildcount=mysql_query($sqlchildcount);
	$rowqchildcount=mysql_fetch_array($rschildcount);
	$tot_emp=$rowqchildcount['tot_emp'];
	if($tot_emp > 1)
	{
		$sqlimmediatebosschildallocation="SELECT SUM(qty) AS child_qty FROM sauda_allocation WHERE 
										product_filter_code = '".$product_group_code."' AND 
										emp_code IN (SELECT emp_code FROM employee_master WHERE reporting_to='".$emp_code."')";
		$rsimmediatebosschildallocation=mysql_query($sqlimmediatebosschildallocation);
		$rowimmediatebosschildallocation=mysql_fetch_array($rsimmediatebosschildallocation);
		$immediate_boss_child_allocation=$rowimmediatebosschildallocation['child_qty'];
		if($immediate_boss_child_allocation =='')   $immediate_boss_child_allocation=0;
		
		if($quantity < $immediate_boss_child_allocation)
		{
			echo 2;
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
}
mysql_close($link);
?>