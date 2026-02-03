<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","acedns_ABDOS");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);

$today_date = date('Ymd');
$yesterday = date('Ymd',strtotime('yesterday'));
$count = 1;
	$sql_order_header = "SELECT EM.emp_code, EM.district, OH.order_no, OH.customer_code, DATE_FORMAT(SUBSTRING(OH.order_no,-14),'%Y-%m-%d %H:%i:%s') AS order_date_time FROM order_header OH, employee_master EM WHERE OH.order_no LIKE 'O%' AND SUBSTRING(OH.order_no,2,5) = EM.emp_code AND SUBSTRING(OH.order_no,-14,8) != '20160517'";
	$res_order_header = mysqli_query($link,$sql_order_header);
	$total_rows = mysqli_num_rows($res_order_header);
	if($total_rows>0){
		$res_order_header = mysqli_query($link,$sql_order_header);
		while($row_order_header = mysqli_fetch_assoc($res_order_header)){
			$order_no = $row_order_header['order_no'];
			$customer_code = $row_order_header['customer_code'];
			$order_date = $row_order_header['order_date'];
			$order_date_time = $row_order_header['order_date_time'];
			$emp_code = $row_order_header['emp_code'];
			$district = $row_order_header['district'];
						
			$sql_customer_details = "SELECT cust_type, coverage_type, route_code, rds_tag FROM cust_master WHERE customer_code = '".$customer_code."'";
			$res_customer_details = mysqli_query($link,$sql_customer_details);
			$row_customer_details = mysqli_fetch_assoc($res_customer_details);
			$cust_type = $row_customer_details['cust_type'];
			$coverage_type = $row_customer_details['coverage_type'];
			$route_code = $row_customer_details['route_code'];
			$rds_tag = $row_customer_details['rds_tag'];
			
			$sql_order_details = "SELECT sku_code, qty, sale_rate, amount FROM order_details WHERE order_no = '".$order_no."'";
			$res_order_details = mysqli_query($link,$sql_order_details);
			while($row_order_details = mysqli_fetch_assoc($res_order_details)){
				$sku_code = $row_order_details['sku_code'];
				$qty = $row_order_details['qty'];
				$sale_rate = $row_order_details['sale_rate'];
				$amount = $row_order_details['amount'];
				
				$sql_product_details = "SELECT product_group_code FROM product_master WHERE prod_code = '".$sku_code."'";
				$res_product_details = mysqli_query($link,$sql_product_details);
				$row_product_details = mysqli_fetch_assoc($res_product_details);
				$product_group_code = $row_product_details['product_group_code'];
				
				$sql_insert_FMCG_sales = "INSERT INTO FMCG_sales SET 
									 `emp_code` = '".$emp_code."', 
								`customer_code` = '".$customer_code."',
									  `rds_tag` = '".$rds_tag."', 
								   `route_code` = '".$route_code."', 
									`cust_type` = '".$cust_type."', 
									 `district` = '".$district."', 
								`coverage_type` = '".$coverage_type."', 
								   `brand_code` = '".$product_group_code."', 
								    `prod_code` = '".$sku_code."', 
										 `rate` = '".$sale_rate."', 
										  `qty` = '".$qty."', 
									   `amount` = '".$amount."', 
									`date_time` = '".$order_date_time."'";
									
				$res_insert_FMCG_sales = mysqli_query($link,$sql_insert_FMCG_sales);
			}
		}
	}	
?>