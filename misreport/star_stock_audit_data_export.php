<?php
//ob_start();
session_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

$employee = $_REQUEST['employee'];
$employee_arg = str_replace("#",",",$employee);
$employee_arg = str_replace("^","'",$employee_arg);

$header = "SL"."\t"."Date"."\t"."District"."\t"."Area"."\t"."Route Name"."\t"."Customer Code"."\t"."Customer Name"."\t"."Employee Code"."\t"."Employee Name"."\t"."Sub Brand"."\t"."Prod Description"."\t"."Qty"."\t"."Sale Rate"."\t"."Amount"."\t"."Customer Type"."\t"."Remarks";

$sql_cust_data = "SELECT DATE_FORMAT(SUBSTRING(transaction_id,-14,8),'%d-%m-%Y') AS date_selected, 
				SUBSTRING(transaction_id,-19,5) AS emp_code, customer_code, product_code,quantity,remarks FROM stock_audit 
				WHERE SUBSTRING(transaction_id,-19,5) IN (".$employee_arg.") AND (DATE_FORMAT(SUBSTRING(transaction_id,-14,8),'%Y-%m-%d') 
				BETWEEN '".$start_date."' AND '".$end_date."') ORDER BY  DATE_FORMAT(SUBSTRING(transaction_id,-14,8),'%d-%m-%Y') DESC";
$res_cust_data = mysqli_query($link,$sql_cust_data);
$total_row_check = mysqli_num_rows($res_cust_data);
$count=1;
while($row_cust_data = mysqli_fetch_assoc($res_cust_data)){
	$date_selected = $row_cust_data['date_selected'];
	$emp_code = $row_cust_data['emp_code'];
	$customer_code = $row_cust_data['customer_code'];
	$product_code = $row_cust_data['product_code'];
	$visit_qty = $row_cust_data['quantity'];
	$d_instruction = $row_cust_data['remarks'];

	$sql_price = "SELECT mrp FROM mrp WHERE product_code = '".$product_code."'";
	$res_price = mysqli_query($link,$sql_price);
	$row_price = mysqli_fetch_assoc($res_price);
	$prod_price = $row_price['mrp'];
	
	$sql_emp_name = "SELECT dns_emp_code, emp_name, district FROM employee_master WHERE emp_code = '".$emp_code."'";
	$res_emp_name = mysqli_query($link,$sql_emp_name);
	$row_emp_name = mysqli_fetch_assoc($res_emp_name);
	$emp_name = $row_emp_name['emp_name'];
	$dns_emp_code = $row_emp_name['dns_emp_code'];
	//$district = $row_emp_name['district'];
	
	$sql_prod_desc = "SELECT product_group_code, prod_desc FROM product_master WHERE prod_code = '".$product_code."'";
	$res_prod_desc = mysqli_query($link,$sql_prod_desc);
	$row_prod_desc = mysqli_fetch_assoc($res_prod_desc);
	$product_group_code = $row_prod_desc['product_group_code'];
	$prod_desc = $row_prod_desc['prod_desc'];
	
	$sql_prodgroup_name = "SELECT product_group_name FROM product_group_master WHERE product_group_code = '".$product_group_code."'";
	$res_prodgroup_name = mysqli_query($link,$sql_prodgroup_name);
	$row_prodgroup_name = mysqli_fetch_assoc($res_prodgroup_name);
	$prod_group_name = $row_prodgroup_name['product_group_name'];
	
	$sql_customer_master = "SELECT dns_customer_code, customer_name, route_code, rds_tag, cust_type, coverage_type, district FROM customer_master WHERE customer_code = '".$customer_code."'";
	$res_customer_master = mysqli_query($link,$sql_customer_master);
	$customer_exist_check = mysqli_num_rows($res_customer_master);
	
	$res_customer_master = mysqli_query($link,$sql_customer_master);
	$row_customer_master = mysqli_fetch_assoc($res_customer_master);
	$dns_customer_code = $row_customer_master['dns_customer_code'];
	$customer_name = $row_customer_master['customer_name'];
	$route_code = $row_customer_master['route_code'];
	$rds_tag = $row_customer_master['rds_tag'];
	$cust_type = $row_customer_master['cust_type'];
	$coverage_type = $row_customer_master['coverage_type'];
	$district = $row_customer_master['district'];
	
	$sql_route_name = "SELECT route_name FROM route_master WHERE route_code = '".$route_code."'";
	$res_route_name = mysqli_query($link,$sql_route_name);
	$row_route_name = mysqli_fetch_assoc($res_route_name);
	$route_name = $row_route_name['route_name'];
	
	$sql_distributor_name = "SELECT customer_name FROM customer_master WHERE customer_code = '".$rds_tag."'";
	$res_distributor_name = mysqli_query($link,$sql_distributor_name);
	$row_distributor_name = mysqli_fetch_assoc($res_distributor_name);
	$distributor_name = $row_distributor_name['customer_name'];
	
	$sql_rds_details = "SELECT route_code FROM customer_master WHERE customer_code = '".$rds_tag."'";
	$res_rds_details = mysqli_query($link,$sql_rds_details);
	$row_rds_details = mysqli_fetch_assoc($res_rds_details);
	$rds_route_code = $row_rds_details['route_code'];
	
	if($cust_type == 'D'){
		$area_name = $route_name;
	}
	else{
		$sql_area_name = "SELECT route_name FROM route_master WHERE route_code = '".$rds_route_code."'";
		$res_area_name = mysqli_query($link,$sql_area_name);
		$row_area_name = mysqli_fetch_assoc($res_area_name);
		$area_name = $row_area_name['route_name'];
	}
	
	if($area_name == ''){
		$area_name = '';
	}
	
	if($visit_qty == 0)
		$visit_qty = '';
		
	if($rate == 0)
		$rate = '';
	else
		$rate = number_format((float) $rate,2);
		
	if($amount == 0)
		$amount ='';
	else
		$amount = number_format((float) $amount,2);
	
	if($customer_exist_check>0){
		
		/*$excel_body_data .= $count."\t".$date_selected."\t".$district."\t".$area_name."\t".$route_name."\t".$dns_customer_code."\t".$customer_name."\t".$dns_emp_code."\t".$emp_name."\t".$prod_group_name."\t".$prod_desc."\t".$visit_qty."\t".$rate."\t".$amount."\t".$cust_type."\t".$d_instruction."\n";*/
		
		$table_data .= $count."\t".$date_selected."\t".$district."\t".$area_name."\t".$route_name."\t".$dns_customer_code."\t".$customer_name."\t".$dns_emp_code."\t".$emp_name."\t".$prod_group_name."\t".$prod_desc."\t".$visit_qty."\t".$prod_price."\t".((float) $visit_qty* (float) $prod_price)."\t".$cust_type."\t".$d_instruction."\n";
		  
		$count++;
	 }
}
if($table_data !=''){	
		header("Content-type: application/octet-stream"); 
		header("Content-Disposition: attachment; filename=Star_Stock_Audit_Report_Details.xls"); 
		header("Pragma: no-cache"); 
		header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 
		echo ucwords($header)."\n".$table_data;
	}
	else
	{
		echo "<span style=\"font-weight:bold; color:red;\">No Records Found!</span>";
	}
mysqli_close($link);
?>
