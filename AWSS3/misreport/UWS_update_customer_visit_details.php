<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_STAR");
date_default_timezone_set("Asia/Kolkata");

mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB);

/*function get_hint_remarks($column,$table,$trans_id){
	$sql_hint_remarks = "SELECT hint_remarks FROM $table WHERE $column = '".$trans_id."'";
	$res_hint_remarks = mysql_query($sql_hint_remarks);
	$row_hint_remarks = mysql_fetch_array($res_hint_remarks);
	$hint_remarks = $row_hint_remarks['hint_remarks'];
	return $hint_remarks;
}

$sql_customer_visit = "SELECT trans_id, customer_code FROM customer_visit_details WHERE rds_tag = ''";
$res_customer_visit = mysql_query($sql_customer_visit);
while($row_customer_visit = mysql_fetch_array($res_customer_visit)){
	$trans_id = $row_customer_visit['trans_id'];
	$customer_code = $row_customer_visit['customer_code'];
	
	$sql_rds = "SELECT rds_tag FROM customer_master WHERE customer_code = '".$customer_code."'";
	$res_rds = mysql_query($sql_rds);
	$row_rds = mysql_fetch_array($res_rds);
	$rds_tag = $row_rds['rds_tag'];
	
	$sql_update_customer_visit = "UPDATE customer_visit_details SET rds_tag = '".$rds_tag."' WHERE trans_id = '".$trans_id."'";
	mysql_query($sql_update_customer_visit);
}


$sql_cust_visit = "SELECT trans_id FROM customer_visit_details WHERE (trans_id LIKE 'OE%' OR trans_id LIKE 'NO%' OR trans_id LIKE 'SE%' OR trans_id LIKE 'NS%') AND hint_remarks = '' AND SUBSTRING(trans_id,-14,8)>='20170220'";
$res_cust_visit = mysql_query($sql_cust_visit);
while($row_cust_visit = mysql_fetch_array($res_cust_visit)){
	$transaction_id = $row_cust_visit['trans_id'];
	$transid_substr = substr($transaction_id,0,2);
	if($transid_substr == "OE" || $transid_substr == "NO"){
		$hint_remarks = get_hint_remarks('order_no','order_header',$transaction_id);
	}
	else if($transid_substr == "SE" || $transid_substr == "NS"){
		$hint_remarks = get_hint_remarks('transaction_id','stock_audit',$transaction_id);
	}
	
	$sql_update_cust_visit = "UPDATE customer_visit_details SET hint_remarks = '".addslashes($hint_remarks)."' WHERE trans_id = '".$transaction_id."'";
	mysql_query($sql_update_cust_visit);
}*/

$sql_order_header = "SELECT transaction_id, hint_remarks FROM `stock_audit` WHERE hint_remarks != ''";
$res_order_header = mysql_query($sql_order_header);
while($row_order_header = mysql_fetch_array($res_order_header)){
	$transaction_id = $row_order_header['transaction_id'];
	$hint_remarks = $row_order_header['hint_remarks'];
	
	$sql_update_cv = "UPDATE customer_visit_details SET hint_remarks = '".addslashes($hint_remarks)."' WHERE trans_id = '".$transaction_id."'";
	mysql_query($sql_update_cv);
}

echo "Table Updated";
?>