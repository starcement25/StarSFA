<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","acedns_STAR");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);

$sql_mf_stock_header = "SELECT mf_stk_audit_id, SUBSTRING(mf_stk_audit_id,3,5) AS emp_code,  DATE_FORMAT(SUBSTRING(mf_stk_audit_id,-14,8),'%Y-%m-%d') AS mf_stk_date, customer_code FROM mf_stk_audit_header";
$res_mf_stock_header = mysqli_query($link,$sql_mf_stock_header);
while($row_mf_stock_header = mysqli_fetch_assoc($res_mf_stock_header)){
	$mf_std_audit_id = $row_mf_stock_header['mf_stk_audit_id'];
	$mf_stk_date = $row_mf_stock_header['mf_stk_date'];
	$emp_code = $row_mf_stock_header['emp_code'];
	$customer_code = $row_mf_stock_header['customer_code'];
	
	$sql_mf_stk_details = "SELECT competitor_name, qty_mt FROM mf_stk_audit_details WHERE mf_stk_audit_id = '".$mf_std_audit_id."'";
	$res_mf_stk_details = mysqli_query($link,$sql_mf_stk_details);
	while($row_mf_stk_details = mysqli_fetch_assoc($res_mf_stk_details)){
		$competitor_name = $row_mf_stk_details['competitor_name'];
		$qty_mt = $row_mf_stk_details['qty_mt'];
		
		if($competitor_name == 'AMBUJA PPC')
			$column_name = 'ambuja';
		else if($competitor_name == 'ULTRATECH PPC')
			$column_name = 'ultratech';
		else if($competitor_name == 'LAFARGE PPC')
			$column_name = 'lafarge';
		else if($competitor_name == 'DALMIA PPC')
			$column_name = 'dalmia';
		else if($competitor_name == 'TOPCEM PPC')
			$column_name = 'topcem';
		else if($competitor_name == 'ACC PPC')
			$column_name = 'acc';
		else if($competitor_name == 'BIRLA GOLD PPC')
			$column_name = 'birla_gold';
		
		$sql_competitor_stock_check = "SELECT emp_code FROM competitor_stock WHERE emp_code = '".$emp_code."' AND  customer_code = '".$customer_code."' AND date_time = '".$mf_stk_date."'";
		$res_competitor_stock_check = mysqli_query($link,$sql_competitor_stock_check);
		$total_row_exist_check = mysqli_num_rows($res_competitor_stock_check);
		if($total_row_exist_check>0){
			$sql_update_competitor_stock = "UPDATE competitor_stock SET $column_name = '".$qty_mt."' WHERE  emp_code = '".$emp_code."' AND customer_code = '".$customer_code."' AND date_time = '".$mf_stk_date."'";
		}
		else{
			$sql_update_competitor_stock = "INSERT INTO competitor_stock SET emp_code = '".$emp_code."', customer_code = '".$customer_code."', $column_name = '".$qty_mt."', date_time = '".$mf_stk_date."'";
		}
		$res_update_competitor_stock = mysqli_query($link,$sql_update_competitor_stock);
	}
}

$sql_stock_audit = "SELECT SUBSTRING(transaction_id,2,5) AS emp_code, customer_code, DATE_FORMAT(SUBSTRING(transaction_id,-14,8),'%Y-%m-%d') AS stk_date, quantity FROM stock_audit WHERE transaction_id LIKE 'S%' AND product_code = '12001'";
$res_stock_audit = mysqli_query($link,$sql_stock_audit);
while($row_stock_audit = mysqli_fetch_assoc($res_stock_audit)){
	$emp_code = $row_stock_audit['emp_code'];
	$customer_code = $row_stock_audit['customer_code'];
	$stk_date = $row_stock_audit['stk_date'];
	$quantity = $row_stock_audit['quantity'];
	
	$sql_competitor_stock_check = "SELECT emp_code FROM competitor_stock WHERE emp_code = '".$emp_code."' AND  customer_code = '".$customer_code."' AND date_time = '".$stk_date."'";
	$res_competitor_stock_check = mysqli_query($link,$sql_competitor_stock_check);
	$total_row_exist_check = mysqli_num_rows($res_competitor_stock_check);
	if($total_row_exist_check>0){
		$sql_update_competitor_stock = "UPDATE competitor_stock SET star = '".$quantity."' WHERE  emp_code = '".$emp_code."' AND customer_code = '".$customer_code."' AND date_time = '".$stk_date."'";
	}
	else{
		$sql_update_competitor_stock = "INSERT INTO competitor_stock SET emp_code = '".$emp_code."', customer_code = '".$customer_code."', star = '".$quantity."', date_time = '".$stk_date."'";
	}
	$res_update_competitor_stock = mysqli_query($link,$sql_update_competitor_stock);
}
echo "Successfully Updated";
mysqli_close($link);
?>