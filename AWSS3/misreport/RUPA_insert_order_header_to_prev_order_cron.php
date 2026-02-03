<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","acedns_RUPA");

mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB);

$sql_order_header = "SELECT order_no, customer_code, d_instruction, DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y-%m-%d') AS date_selected, DATE_FORMAT(SUBSTRING(order_no,-14),'%H:%i:%s') AS time_selected, vertical_value
FROM `order_header`
WHERE DATE_FORMAT( SUBSTRING( order_no , -14, 8 ) , '%Y-%m-%d' ) >= '2016-04-01' AND DATE_FORMAT( 					SUBSTRING( order_no , -14, 8 ) , '%Y-%m-%d' ) <= '2016-07-29' ORDER BY DATE_FORMAT( SUBSTRING( order_no , -14, 8 ) , '%Y-%m-%d' ) DESC";
$res_order_header = mysql_query($sql_order_header);
while($row_order_header = mysql_fetch_array($res_order_header)){
	
	$order_no = $row_order_header['order_no'];
	$d_instruction = $row_order_header['d_instruction'];
	$customer_code = $row_order_header['customer_code'];
	$date_selected = $row_order_header['date_selected'];
	$time_selected = $row_order_header['time_selected'];
	$vertical_value = $row_order_header['vertical_value'];
	$dwnld_time = $date_selected." ".$time_selected;
	
	$order_check = substr($order_no,0,1);
	
	$sql_cust_type = "SELECT cust_type FROM customer_master WHERE customer_code = '".$customer_code."'";
	$res_cust_type = mysql_query($sql_cust_type);
	$row_cust_type = mysql_fetch_array($res_cust_type);
	$cust_type = $row_cust_type['cust_type'];
	
	//echo $order_no." ".$order_check."<br>";
	if($order_check == 'O'){
		$sql_order_details = "SELECT sku_code, qty, sale_rate, amount FROM order_details WHERE order_no = '".$order_no."'";
		$res_order_details = mysql_query($sql_order_details);
		while($row_order_details = mysql_fetch_array($res_order_details)){
			$sku_code = $row_order_details['sku_code'];
			$qty = $row_order_details['qty'];
			$sale_rate = $row_order_details['sale_rate'];
			$amount = $row_order_details['amount'];
			
			/*$sql_check_prev_order = "SELECT order_no FROM prev_order_counting_master WHERE order_no = '".$order_no."' AND product_code = '".$sku_code."'";
			$res_check_prev_order = mysql_query($sql_check_prev_order);
			$total_rows = mysql_num_rows($res_check_prev_order);
			
			if($total_rows>0){
				$sql_update_prev_order = "UPDATE prev_order_counting_master SET `rate` = '".$sale_rate."', `amount` = '".$amount."' WHERE order_no = '".$order_no."' AND product_code = '".$sku_code."'";
				$res_update_prev_order = mysql_query($sql_update_prev_order);
			}
			else if($total_rows == 0){*/
				$sql_insert_prev_order = "INSERT INTO prev_order_counting_master SET 
											`customer_code` = '".$customer_code."',
												`cust_type` = '".$cust_type."',
											 `product_code` = '".$sku_code."', 
												`visit_qty` = '".$qty."', 
											   `visit_date` = '".$dwnld_time."', 
												 `order_no` = '".$order_no."',
										   `vertical_value` = '".$vertical_value."',
											 `delivery_qty` = '', 
												   `status` = 'pending', 
												  `remarks` = '', 
											`d_instruction` = '".$d_instruction."', 
													 `rate` = '".$sale_rate."', 
												   `amount` = '".$amount."', 
											`download_time` = '".$dwnld_time."'";
				$res_insert_prev_order = mysql_query($sql_insert_prev_order);
			//}
		}
	}
	else if($order_check == 'N'){
		$sql_prev_order_insert = "INSERT INTO prev_order_counting_master SET 
											`customer_code` = '".$customer_code."',
												`cust_type` = '', 
											 `product_code` = '', 
												`visit_qty` = '', 
											   `visit_date` = '".$dwnld_time."', 
												 `order_no` = '".$order_no."', 
										   `vertical_value` = '',
											 `delivery_qty` = '', 
												   `status` = 'pending', 
												  `remarks` = '', 
											`d_instruction` = '".$d_instruction."', 
													 `rate` = '', 
												   `amount` = '', 
											`download_time` = '".$dwnld_time."'";
		$res_prev_order_insert = mysql_query($sql_prev_order_insert);
	}
}