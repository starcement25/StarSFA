<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_STAR");
date_default_timezone_set("Asia/Kolkata");

mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB);

$today = date('Y-m-d');
$dateprevious = date('Y-m-d', strtotime("-1 days,$today"));

function get_record($column,$table,$trans_id){
	$sql_transaction_data = "SELECT customer_code FROM $table WHERE $column = '$trans_id'";
	$res_transaction_data = mysql_query($sql_transaction_data);
	$row_transaction_data = mysql_fetch_array($res_transaction_data);
	$customer_code = $row_transaction_data['customer_code'];
	return $customer_code;
}

$sql_location = "SELECT trans_id, emp_code FROM `location` WHERE (trans_id LIKE 'O%' OR trans_id LIKE 'NO%' OR trans_id LIKE 'S%' OR trans_id LIKE 'NS%' OR trans_id LIKE 'P%' OR trans_id LIKE 'NC%' OR trans_id LIKE 'MF%') AND (trans_id NOT LIKE 'SU%' AND trans_id NOT LIKE 'PA%') AND (SUBSTRING(date,1,10) BETWEEN '$today' AND '$today') ORDER BY date ASC";
$res_location = mysql_query($sql_location);
while($row_location = mysql_fetch_array($res_location)){
	$emp_code = $row_location['emp_code'];
	$trans_id = $row_location['trans_id'];
	$transid_substr = substr($trans_id,0,2);
	
	if($transid_substr == "OE" || $transid_substr == "NO"){
		$customer_code = get_record('order_no','order_header',$trans_id);
	}
	else if($transid_substr == "SE" || $transid_substr == "NS"){
		$customer_code = get_record('transaction_id','stock_audit',$trans_id);
	}
	else if($transid_substr == "PE" || $transid_substr == "NC"){
		$customer_code = get_record('receipt_id','collection',$trans_id);
	}
	else if($transid_substr == "MF"){
		$customer_code = get_record('market_feedback_id','market_feedback',$trans_id);
	}
	
	$sql_cust_details = "SELECT customer_name, cust_type, route_code FROM customer_master WHERE customer_code = '".$customer_code."'";
	$res_cust_details = mysql_query($sql_cust_details);
	$row_cust_details = mysql_fetch_array($res_cust_details);
	$customer_name = $row_cust_details['customer_name'];
	$cust_type = $row_cust_details['cust_type'];
	$route_code = $row_cust_details['route_code'];
	
	$sql_route = "SELECT route_name FROM route_master WHERE route_code = '".$route_code."'";
	$res_route = mysql_query($sql_route);
	$row_route = mysql_fetch_array($res_route);
	$route_name = $row_route['route_name'];
	
	$sql_transid_check = "SELECT emp_code FROM customer_visit_details WHERE trans_id = '".$trans_id."'";
	$res_transid_check = mysql_query($sql_transid_check);
	$total_row_check = mysql_num_rows($res_transid_check);
	if($total_row_check == 0){
		$sql_customer_visit_details = "INSERT INTO customer_visit_details SET 
												 `emp_code` = '".$emp_code."', 
												 `trans_id` = '".$trans_id."', 
											`customer_code` = '".$customer_code."', 
											`customer_name` = '".addslashes($customer_name)."', 
												`cust_type` = '".$cust_type."', 
											   `route_code` = '".$route_code."', 
												`route_name` = '".addslashes($route_name)."'";
		mysql_query($sql_customer_visit_details);
	}
	
}

echo "Records Inserted";
?>