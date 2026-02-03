<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_STAR");
date_default_timezone_set("Asia/Kolkata");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);

$table_data = "<table border=\"1\" width=\"40%\" style=\"border-collapse:collapse;\">
				<tr>
					<td>Date</td>
					<td>Emp Code</td>
					<td width=\"10%\">Master Data</td>
					<td width=\"10%\">Mis Datawise</td>
					<td>Master COUNT</td>
					<td>Datewise COUNT</td>
				</tr>";

$sql_emp_code = "SELECT DISTINCT emp_code FROM `mis_details_emp_datewise` WHERE operation_date BETWEEN '2017-01-21' AND '2017-01-24' AND emp_code = 'E0460' ORDER BY emp_code ASC";
$res_emp_code = mysqli_query($link,$sql_emp_code);
while($row_emp_code = mysqli_fetch_assoc($res_emp_code)){
	$emp_code = $row_emp_code['emp_code'];
	
	$sql_location_date = "SELECT DISTINCT SUBSTRING(date,1,10) AS location_date FROM location WHERE (SUBSTRING(date,1,10) BETWEEN '2017-01-21' AND '2017-01-24') ORDER BY SUBSTRING(date,1,10) ASC";
	$res_location_date = mysqli_query($link,$sql_location_date);
	while($row_location_date = mysqli_fetch_assoc($res_location_date)){
		$location_date = $row_location_date['location_date'];
		
		$customer_string = '';
		$customer_string_order_header = '';
		$customer_string_stock_audit = '';
		$customer_string_market_feedback = '';
		
		$order_header_count = 0;
		$stock_audit_count = 0;
		$market_feedback_count = 0;
		$total_count = 0;
		
		if($order_header_count == '')
			$order_header_count= 0;
		if($stock_audit_count == '')
			$stock_audit_count = 0;
		if($market_feedback_count == '')
			$market_feedback_count = 0;
		if($total_count == '')
			$total_count = 0;
		
		$customer_code_array = array();
		
		$sql_order_header_customer = "SELECT customer_code, SUBSTRING(order_no,-14,8) AS oh_date FROM order_header WHERE SUBSTRING(order_no,-19,5) = '".$emp_code."' AND SUBSTRING(order_no,-14,8) = '".str_replace("-","",$location_date)."' GROUP BY customer_code, SUBSTRING(order_no,-14,8)";
		$res_order_header_customer = mysqli_query($link,$sql_order_header_customer);
		$order_header_count = mysqli_num_rows($res_order_header_customer);
		$res_order_header_customer = mysqli_query($link,$sql_order_header_customer);
		while($row_order_header_customer = mysqli_fetch_assoc($res_order_header_customer)){
			$order_header_customer_code = $row_order_header_customer['customer_code'];
			$location_date = $row_order_header_customer['oh_date'];
			
			$customer_concat_date = $order_header_customer_code."^".$location_date;
			$customer_code_array[$customer_concat_date] = '1';
			$customer_string_order_header .= $order_header_customer_code.",";
			//$customer_string .= $order_header_customer_code.",";
		}
		
		$sql_stock_audit_customer = "SELECT customer_code, SUBSTRING(transaction_id,-14,8) AS sa_date FROM stock_audit WHERE SUBSTRING(transaction_id,-19,5) = '".$emp_code."' AND SUBSTRING(transaction_id,-14,8) = '".str_replace("-","",$location_date)."' GROUP BY customer_code, SUBSTRING(transaction_id,-14,8)";
		$res_stock_audit_customer = mysqli_query($link,$sql_stock_audit_customer);
		$stock_audit_count = mysqli_num_rows($res_stock_audit_customer);
		$res_stock_audit_customer = mysqli_query($link,$sql_stock_audit_customer);
		while($row_stock_audit_customer = mysqli_fetch_assoc($res_stock_audit_customer)){
			$stock_audit_customer = $row_stock_audit_customer['customer_code'];
			$location_date = $row_stock_audit_customer['sa_date'];
			
			$customer_concat_date = $stock_audit_customer."^".$location_date;
			$customer_code_array[$customer_concat_date] = '1';
			$customer_string_stock_audit .= $stock_audit_customer.",";
			//$customer_string .= $stock_audit_customer.",";
		}
							
		$sql_market_feedback = "SELECT customer_code, SUBSTRING(market_feedback_id,-14,8) AS mf_date FROM market_feedback WHERE SUBSTRING(market_feedback_id,3,5) = '".$emp_code."' AND SUBSTRING(market_feedback_id,-14,8) = '".str_replace("-","",$location_date)."' GROUP BY customer_code, SUBSTRING(market_feedback_id,-14,8)";
		$res_market_feedback = mysqli_query($link,$sql_market_feedback);
		$market_feedback_count = mysqli_num_rows($res_market_feedback);
		$res_market_feedback = mysqli_query($link,$sql_market_feedback);
		while($row_market_feedback = mysqli_fetch_assoc($res_market_feedback)){
			$market_feedback_customer = $row_market_feedback['customer_code'];
			$market_feedback_date = $row_market_feedback['mf_date'];
			
			$customer_concat_date = $market_feedback_customer."^".$market_feedback_date;
			$customer_code_array[$customer_concat_date] = '1';
			$customer_string_market_feedback .= $market_feedback_customer.",";
			//$customer_string .= $market_feedback_customer.",";
		}
		
		if($order_header_count == '')
			$order_header_count= 0;
		if($stock_audit_count == '')
			$stock_audit_count = 0;
		if($market_feedback_count == '')
			$market_feedback_count = 0;
				
		$total_count = $order_header_count+$stock_audit_count+$market_feedback_count;
		
		if($total_count == '')
			$total_count = 0;
		
		$no_customer_visited = count($customer_code_array);
		
		/*$customer_string_order_header = rtrim($customer_string_order_header,",");
		$customer_string_stock_audit = rtrim($customer_string_stock_audit,",");
		$customer_string_market_feedback = rtrim($customer_string_market_feedback,",");
		$customer_string = rtrim($customer_string,",");
		if(strpos($customer_string,"," == TRUE))
			$customer_string_array = explode(",",$customer_string);
		else{
			$customer_string_array = array();
			array_push($customer_string_array,$customer_string);
		}*/
			
		
		$sql_mis_details_datewise = "SELECT customer_visited FROM mis_details_emp_datewise WHERE operation_date = '".$location_date."' AND emp_code = '".$emp_code."'";
		$res_mis_details_datewise = mysqli_query($link,$sql_mis_details_datewise);
		$row_mis_details_datewise = mysqli_fetch_assoc($res_mis_details_datewise);
		$customer_visited = $row_mis_details_datewise['customer_visited'];
		$customer_visited = rtrim($customer_visited,",");
		
		/*if(strpos(",",$customer_visited_array) == TRUE)
			$customer_visited_array = explode(",",$customer_visited);
		else{
			$customer_visited_array = array();
			array_push($customer_visited_array,$customer_visited);
		}
		
		$customer_visited_array = explode(",",$customer_visited);
		$count_customer = count($customer_visited_array);*/
		
		$customer_array = array();
		$count_customer_array = array();
		if($customer_visited != ''){
			if(strpos($customer_visited,",") == FALSE)
				$customer_array[] = $customer_visited;
			else{
				$customer_array = explode(",",$customer_visited);
			}
			
			foreach($customer_array  as $customer_value){
				if($customer_value != ''){
					$customer_index = $operation_date."^".$emp_code."^".$customer_value;
					$count_customer_array[$customer_index] = '1';
				}
				
			}
		}
		
		$count_customer = count($count_customer_array);
				
		if($total_count>0){
			if($total_count != count($customer_visited_array))
				$style=" style=\"background:#FF0; font-weight:bold;\" ";
			else
				$style="";
			
			$table_data .= "<tr $style>
								<td>".$location_date."</td>
								<td>".$emp_code."</td>
								<td>Order:".$customer_string_order_header."<br>Stock:".$customer_string_stock_audit."<br>Market:".$customer_string_market_feedback."</td>
								<td>".$customer_visited."</td>
								<td>".$no_customer_visited."</td>
								<td>".$count_customer."</td>
							</tr>";
		}
		
		
	}
}
$table_data .= "</table>";

echo $table_data;

/*$to = 'kaustavjk@coral.in';
$subject = 'Emp Customer Details';

$header = "Content-type: application/octet-stream" . "\r\n" ;
$header .= "Content-Disposition: attachment; filename=Report.xls" . "\r\n";
$header .= "From: <webmaster@example.com>" . "\r\n";

mail($to,$subject,$table_data,$header);*/

echo "mail Sent";
?>
