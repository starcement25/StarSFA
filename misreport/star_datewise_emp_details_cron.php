<?php
define("SERVERREMOTE","52.66.101.239");
define("USERREMOTE","root");
define("PASSWORDREMOTE","cmcl@123");
define("DB","acedns_STAR");

mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE);
mysqli_select_db(DB);

$today = date('Y-m-d');
$curdateone = date('Ymd');

/*----> SELECT DISTINCT DATE FROM LOCATION ACCORDING TO FINANCIAL YEAR (1ST APRIL)<----*/
/*$sql_location_date = "SELECT DISTINCT SUBSTRING(date,1,10) AS distinct_date FROM `location` WHERE SUBSTRING(date,1,10)>='2016-04-01' 
AND SUBSTRING(date,1,10) != '".$today."'";*/
$sql_location_date = "SELECT DISTINCT SUBSTRING(date,1,10) AS distinct_date FROM `location` WHERE SUBSTRING(date,1,10)='2016-10-07'";
$res_location_date = mysqli_query($link,$sql_location_date);
while($row_location_date = mysqli_fetch_assoc($res_location_date)){
	$location_date = $row_location_date['distinct_date'];
	
	/*----> SELECT EMPLOYEE DETAILS <----*/
	$sql_emp_code = "SELECT emp_code, reporting_to, sale_access FROM employee_master ORDER BY emp_code";
	$res_emp_code = mysqli_query($link,$sql_emp_code);
	while($row_emp_code = mysqli_fetch_assoc($res_emp_code)){
		$emp_code = $row_emp_code['emp_code'];
		$reporting_to = $row_emp_code['reporting_to'];
		$sale_access = $row_emp_code['sale_access'];
		
		/*----> CHECKS FOR EMPLOYEE ATTENDANCE <----*/
		$attend =0;
		$sql_present = "SELECT emp_code FROM location WHERE emp_code = '".$emp_code."' AND trans_id LIKE 'A%' AND SUBSTRING(date,1,10) = '".$location_date."'";
		$res_present = mysqli_query($link,$sql_present);
		$present_check = mysqli_num_rows($res_present);
		if($present_check>0)
			$attend = 1;
		/*----------> CUSTOMER VISIT COUNT <----------*/
		//$customer_code_array = array();
		$customer_string = '';
		$sql_order_header_customer = "SELECT customer_code, SUBSTRING(order_no,-14,8) AS oh_date FROM order_header WHERE SUBSTRING(order_no,-19,5) = '".$emp_code."' AND SUBSTRING(order_no,-14,8) = '".str_replace("-","",$location_date)."' GROUP BY customer_code, SUBSTRING(order_no,-14,8)";
		$res_order_header_customer = mysqli_query($link,$sql_order_header_customer);
		while($row_order_header_customer = mysqli_fetch_assoc($res_order_header_customer)){
			$order_header_customer_code = $row_order_header_customer['customer_code'];
			//$location_date = $row_order_header_customer['oh_date'];
			//$customer_concat_date = $order_header_customer_code."^".$location_date;
			//$customer_code_array[$customer_concat_date] = '1';
			$customer_string .= $order_header_customer_code.",";
		}
		
		$sql_stock_audit_customer = "SELECT customer_code, SUBSTRING(transaction_id,-14,8) AS sa_date FROM stock_audit WHERE SUBSTRING(transaction_id,-19,5) = '".$emp_code."' AND SUBSTRING(transaction_id,-14,8) = '".str_replace("-","",$location_date)."' GROUP BY customer_code, SUBSTRING(transaction_id,-14,8)";
		$res_stock_audit_customer = mysqli_query($link,$sql_stock_audit_customer);
		while($row_stock_audit_customer = mysqli_fetch_assoc($res_stock_audit_customer)){
			$stock_audit_customer = $row_stock_audit_customer['customer_code'];
			//$location_date = $row_stock_audit_customer['sa_date'];
			//$customer_concat_date = $stock_audit_customer."^".$location_date;
			//$customer_code_array[$customer_concat_date] = '1';
			$customer_string .= $stock_audit_customer.",";
		}
							
		$sql_market_feedback = "SELECT customer_code, SUBSTRING(market_feedback_id,-14,8) AS mf_date FROM market_feedback WHERE SUBSTRING(market_feedback_id,3,5) = '".$emp_code."' AND SUBSTRING(market_feedback_id,-14,8) = '".str_replace("-","",$location_date)."' GROUP BY customer_code, SUBSTRING(market_feedback_id,-14,8)";
		$res_market_feedback = mysqli_query($link,$sql_market_feedback);
		while($row_market_feedback = mysqli_fetch_assoc($res_market_feedback)){
			$market_feedback_customer = $row_market_feedback['customer_code'];
			//$market_feedback_date = $row_market_feedback['mf_date'];
			//$customer_concat_date = $market_feedback_customer."^".$market_feedback_date;
			//$customer_code_array[$customer_concat_date] = '1';
			$customer_string .= $market_feedback_customer.",";
		}
		//$no_customer_visit = count($customer_code_array);
		$customer_string = rtrim($customer_string,",");
		
		/*----------> TOTAL ORDER <----------*/
		$sqltotalorder="SELECT SUM(OD.qty) AS total_order_received
						FROM order_details OD,location LO
						WHERE  LO.trans_id LIKE 'O%' AND LO.trans_id=OD.order_no AND SUBSTRING(LO.emp_code,1,1)!='C'
						AND LO.emp_code = '".$emp_code."' AND SUBSTRING(LO.date,1,10) = '".$location_date."'";
		$rstotalorder=mysqli_query($link,$sqltotalorder) or die(mysqli_error()." Error in total order received: ".$sqltotalorder);
		$rowtotalorder=mysqli_fetch_assoc($rstotalorder);
		$total_order_qty=$rowtotalorder['total_order_received'];
		
		/*----------> TOTAL COLLECTION <----------*/
		$sqltotalcollection="SELECT SUM(PD.amount) AS total_collection_received
							FROM payment_details PD,location LO
							WHERE LO.trans_id LIKE 'P%' AND LO.trans_id=PD.receipt_id 
							AND SUBSTRING(LO.emp_code,1,1)!='C' AND LO.emp_code = '".$emp_code."' AND SUBSTRING(LO.date,1,10) = '".$location_date."'";
		$rstotalcollection=mysqli_query($link,$sqltotalcollection) or die(mysqli_error()." Error in total collection received: ".$sqltotalcollection);
		$rowtotalcollection=mysqli_fetch_assoc($rstotalcollection);
		$collection_received=$rowtotalcollection['total_collection_received'];
		
		/*----------> TOTAL NO TRANSACTION <----------*/
		$sqlnotransaction="SELECT COUNT(LO.trans_id)AS total_no_transaction
							FROM location LO WHERE (SUBSTRING(LO.trans_id,1,2) = 'NO' OR SUBSTRING(LO.trans_id,1,2) = 'NC' OR SUBSTRING(LO.trans_id,1,2) = 'NS' OR SUBSTRING(LO.trans_id,1,3) = 'NSU') 
							AND SUBSTRING(LO.emp_code,1,1)!='C' AND LO.emp_code = '".$emp_code."' AND SUBSTRING(LO.date,1,10) = '".$location_date."'";
		$rsnotransaction=mysqli_query($link,$sqlnotransaction) or die(mysqli_error()." Error in total no transaction: ".$sqlnotransaction);
		$rownotransaction=mysqli_fetch_assoc($rsnotransaction);
		$no_transaction=$rownotransaction['total_no_transaction'];
		
		/*----------> TOTAL STOCK AUDIT <----------*/
		$sqlnostkaudit="SELECT SUM(SA.quantity)AS total_stk_audit FROM location LO,stock_audit SA 
								WHERE SA.transaction_id=LO.trans_id AND (LO.trans_id LIKE 'S%') 
								AND SUBSTRING(LO.emp_code,1,1)!='C' AND LO.emp_code = '".$emp_code."' AND SUBSTRING(LO.date,1,10) = '".$location_date."'";
		$rsnostkaudit=mysqli_query($link,$sqlnostkaudit) or die(mysqli_error()." Error in total no stk audit: ".$sqlnostkaudit);
		$rownostkaudit=mysqli_fetch_assoc($rsnostkaudit);
		$no_stk_audit=$rownostkaudit['total_stk_audit'];
		
		/*----------> TOTAL KYC <----------*/
		$sql_KYC_count = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type = 'KYC' AND SUBSTRING(survey_id,3,5) = '".$emp_code."' AND SUBSTRING(survey_id,-14,8) = '".str_replace("-","",$location_date)."'";
		$res_KYC_count = mysqli_query($link,$sql_KYC_count);
		$row_KYC_count = mysqli_fetch_assoc($res_KYC_count);
		$KYC_count = $row_KYC_count['COUNT(DISTINCT survey_id)'];
		
		/*----------> TOTAL BRANDING <----------*/
		$sql_brand_count = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type = 'Branding' AND SUBSTRING(survey_id,3,5) = '".$emp_code."' AND SUBSTRING(survey_id,-14,8) = '".str_replace("-","",$location_date)."'";
		$res_brand_count = mysqli_query($link,$sql_brand_count);
		$row_brand_count = mysqli_fetch_assoc($res_brand_count);
		$brand_count = $row_brand_count['COUNT(DISTINCT survey_id)'];
		
		/*----------> TOTAL TECHNICAL MEETS <----------*/
		$sql_technical_count = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type = 'Technical Meets' AND SUBSTRING(survey_id,3,5) = '".$emp_code."' AND SUBSTRING(survey_id,-14,8) = '".str_replace("-","",$location_date)."'";
		$res_technical_count = mysqli_query($link,$sql_technical_count);
		$row_technical_count = mysqli_fetch_assoc($res_technical_count);
		$technical_count = $row_technical_count['COUNT(DISTINCT survey_id)'];
		
		/*----------> TOTAL SITE VISIT <----------*/
		$sql_site_visit = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type = 'Site Visit' AND SUBSTRING(survey_id,3,5) = '".$emp_code."' AND SUBSTRING(survey_id,-14,8) = '".str_replace("-","",$location_date)."'";
		$res_site_visit = mysqli_query($link,$sql_site_visit);
		$row_site_visit = mysqli_fetch_assoc($res_site_visit);
		$site_visit_count = $row_site_visit['COUNT(DISTINCT survey_id)'];
		
		/*----------> TOTAL MARKET FEEDBACK <----------*/
		$sql_market_feedback = "SELECT COUNT(DISTINCT market_feedback_id) FROM market_feedback WHERE SUBSTRING(market_feedback_id,3,5) = '".$emp_code."' AND SUBSTRING(market_feedback_id,-14,8) = '".str_replace("-","",$location_date)."'";
		$res_market_feedback = mysqli_query($link,$sql_market_feedback);
		$row_market_feedback = mysqli_fetch_assoc($res_market_feedback);
		$market_feedback_count = $row_market_feedback['COUNT(DISTINCT market_feedback_id)'];
		
		
		$sql_insert_emp_details = "INSERT INTO mis_details_emp_datewise SET 
										  `operation_date` = '".$location_date."', 
											    `emp_code` = '".$emp_code."', 
											     `present` = '".$attend."', 
										`customer_visited` = '".$customer_string."', 
										  `order_received` = '".$total_order_qty."', 
										  `no_transaction` = '".$no_transaction."', 
										     `stock_audit` = '".$no_stk_audit."', 
													 `kyc` = '".$KYC_count."', 
										  `brand_activity` = '".$brand_count."', 
										  `technical_meet` = '".$technical_count."', 
											  `site_visit` = '".$site_visit_count."', 
										 `market_feedback` = '".$market_feedback_count."'";
		$res_insert_emp_details = mysqli_query($link,$sql_insert_emp_details);
	}
}
?>