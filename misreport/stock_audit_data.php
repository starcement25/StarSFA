<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");
	
$start_date = $_REQUEST['start_date'];	
$end_date = $_REQUEST['end_date'];
$emp_code = $_REQUEST['emp_code'];
$customer_code = $_REQUEST['customer_code'];

$emp_array = array();

if($emp_code == 'all'){
	if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=' AND EM.emp_code IN('.$emp_hierarchy.')';
	}
	
	$sql_select_employee = "SELECT EM.emp_code, EM.emp_name FROM employee_master EM, stock_audit SU WHERE EM.emp_code = SUBSTRING(SU.transaction_id,2,5) ".$emp_hierarchy_condition."AND SUBSTRING(SU.transaction_id,-14,8) BETWEEN ".date('Ymd',strtotime($start_date))." AND ".date('Ymd',strtotime($end_date))." GROUP BY SUBSTRING(SU.transaction_id,2,5) ORDER BY EM.emp_name ASC";
	$res_select_employee = mysqli_query($link,$sql_select_employee);
	while($row_select_employee = mysqli_fetch_assoc($res_select_employee)){
		array_push($emp_array,$row_select_employee['emp_code']);
	}
}
else{
	$emp_array[] = $emp_code;
}

$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code='$emp_code'";
$res_emp_name = mysqli_query($link,$sql_emp_name);
$row_emp_name = mysqli_fetch_assoc($res_emp_name);
$emp_name = $row_emp_name['emp_name'];
if($customer_code == 'all')
{
	$report_head = $emp_name." - "."ALL";
}
else
{
	$sql_customer_name = "SELECT customer_name FROM customer_master WHERE customer_code='$customer_code'";
	$res_customer_name = mysqli_query($link,$sql_customer_name);
	$row_customer_name = mysqli_fetch_assoc($res_customer_name);
	$customer_name = $row_customer_name['customer_name'];
	$report_head = $emp_name;
}

if($emp_code == 'all'){
	foreach($emp_array as $emp_code){
		$emp_string .= "'".$emp_code."',";	
	}
	$emp_string = rtrim($emp_string,",");
}
else{
	$emp_string = "'".$emp_code."'";
}


if($customer_code == 'all')
{
	$sql_select_product = "SELECT CM.customer_name, CM.dns_customer_code, CM.cust_type, DATE_FORMAT(SUBSTRING(SU.transaction_id,-14,8),'%d-%m-%Y') as date_selected, PM.prod_desc, SUM(SU.quantity) as tot_qty, SU.remarks FROM product_master PM, stock_audit SU, customer_master CM WHERE SUBSTRING(SU.transaction_id,2,5) IN($emp_string) AND SUBSTRING(SU.transaction_id,-14,8) BETWEEN ".date('Ymd',strtotime($start_date))." AND ".date('Ymd',strtotime($end_date))." AND PM.prod_code=SU.product_code AND SU.customer_code=CM.customer_code GROUP BY date_selected, SU.customer_code, SU.product_code ORDER BY date_selected,CM.customer_name,PM.prod_desc ASC";
}
else
{
	$sql_select_product = "SELECT CM.customer_name, CM.dns_customer_code, CM.cust_type, DATE_FORMAT(SUBSTRING(SU.transaction_id,-14,8),'%d-%m-%Y') as date_selected, PM.prod_desc, SUM(SU.quantity) as tot_qty FROM product_master PM, stock_audit SU, customer_master CM WHERE SUBSTRING(SU.transaction_id,2,5)IN($emp_string) AND CM.customer_code = SU.customer_code AND SU.customer_code='$customer_code' AND SUBSTRING(SU.transaction_id,-14,8) BETWEEN ".date('Ymd',strtotime($start_date))." AND ".date('Ymd',strtotime($end_date))." AND PM.prod_code=SU.product_code GROUP BY date_selected, SU.product_code ORDER BY date_selected, PM.prod_desc ASC";
}
	$res_select_product = mysqli_query($link,$sql_select_product);
	$total_rows = mysqli_num_rows($res_select_product);
	$date_array = array();
	$customer_name_array = array();
	
	if($total_rows>0)
	{
		$count = 1;
		echo "<table border='1' width=\"60%\" class=\"border\" style=\"border-collapse:collapse;\">
				<tr  class='TDHEAD'><td align='center' colspan='5'>$report_head</td></tr>
				<tr class=\"TDHEAD_SUB\" align='center'>
					<td>SI</td>
					<td>Date</td>
					<td>Product Name</td>
					<td>Quantity</td>
					<td>Remarks</td>
				</tr>";
		$res_select_product = mysqli_query($link,$sql_select_product);
		while($row_select_product = mysqli_fetch_assoc($res_select_product))
		{
			$dns_customer_code = $row_select_product['dns_customer_code'];
			$customer_name = $row_select_product['customer_name'];
			$cust_type = $row_select_product['cust_type'];
			$remarks = $row_select_product['remarks'];
			
			if(!in_array($customer_name,$customer_name_array))
			{
				array_push($customer_name_array,$customer_name);
				echo "<tr class='TDHEAD_SUB'><td align='center' colspan='5'>".$dns_customer_code." - ".$customer_name." - ".$cust_type."</td></tr>";
			}
			echo "<tr>
					<td>".$count."</td>";
			if(!in_array($row_select_product['date_selected'],$date_array))
			{
				array_push($date_array,$row_select_product['date_selected']);
				echo "<td bgcolor=\"#CCCCCC\"><b>".$row_select_product['date_selected']."</b></td>";
			}
			else
			{
				echo "<td></td>";
			}
					
			echo "<td>".$row_select_product['prod_desc']."</td>
					<td align=\"right\">".$row_select_product['tot_qty']."</td>
					<td>".$remarks."</td>
				  </tr>";	
			$count++;
		}
		echo "</table>";
	}
	else
	{
		echo "No records found";
	}
	
mysqli_close($link);
?>