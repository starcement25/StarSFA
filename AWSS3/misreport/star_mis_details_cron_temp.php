<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","acedns_STAR");

mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB);

$today = date('Y-m-d');
$curdateone = date('Ymd');

$sql_truncate = "TRUNCATE mis_data_details";
$res_truncate = mysql_query($sql_truncate);

$sql_select_emp_details = "SELECT emp_code, sale_access, reporting_to FROM employee_master";
$res_select_emp_details = mysql_query($sql_select_emp_details);
while($row_select_emp_details = mysql_fetch_array($res_select_emp_details)){
	$emp_code = $row_select_emp_details['emp_code'];
	$sale_access = $row_select_emp_details['sale_access'];
	$reporting_to = $row_select_emp_details['reporting_to'];
	
	for($i=1;$i<=3;$i++){
		/*if($i == 1){
			$present_col = 'present_tdy';
			$customer_visited_col = 'customer_visited_tdy';
			$order_received_col = 'order_received_tdy';
			$no_transaction_col = 'no_transaction_tdy';
			$stock_audit_col = 'stock_audit_tdy';
			$kyc_col = 'kyc_tdy';
			$brand_activity_col = 'brand_activity_tdy';
			$technical_meet_col = 'technical_meet_tdy';
			$site_visit_col = 'site_visit_tdy';
			$market_feedback_col = 'market_feedback_tdy';
		}*/
		
		if($i == 2){
			$present_col = 'present_mtd';
			$customer_visited_col = 'customer_visited_mtd';
			$order_received_col = 'order_received_mtd';
			$no_transaction_col = 'no_transaction_mtd';
			$stock_audit_col = 'stock_audit_mtd';
			$kyc_col = 'kyc_mtd';
			$brand_activity_col = 'brand_activity_mtd';
			$technical_meet_col = 'technical_meet_mtd';
			$site_visit_col = 'site_visit_mtd';
			$market_feedback_col = 'market_feedback_mtd';
		}
		
		if($i == 3){
			$present_col = 'present_ytd';
			$customer_visited_col = 'customer_visited_ytd';
			$order_received_col = 'order_received_ytd';
			$no_transaction_col = 'no_transaction_ytd';
			$stock_audit_col = 'stock_audit_ytd';
			$kyc_col = 'kyc_ytd';
			$brand_activity_col = 'brand_activity_ytd';
			$technical_meet_col = 'technical_meet_ytd';
			$site_visit_col = 'site_visit_ytd';
			$market_feedback_col = 'market_feedback_ytd';
		}
		
		/*if($i==1){
			$date=date('Y-m-d');
			if($date!='' && sale=='no' && instruction=='yes')
			{
				$date_condition ="  AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$date."%'";
			}
			else
			{
				$date_condition ="  AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d') LIKE '%".$date."%'";
			}
			
			$survey_output_date_condition = " AND SUBSTRING(survey_id,-14,8) = '".str_replace("-","",$date)."' ";
			$market_feedback_condition = " AND SUBSTRING(market_feedback_id,-14,8) = '".str_replace("-","",$date)."' ";
			
			$sl_value='Today';
			$val='T';
		}*/
		//For MTD OR Month Today
		if($i==2){
			$current_month = date('Ym');
			if(sale=='no' && instruction=='yes')
			{
				$date_condition=" AND YEAR(LO.date) = YEAR(CURDATE()) AND MONTH(LO.date) = MONTH(CURDATE()) ";
			}
			else
			{
				$date_condition=" AND YEAR(DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d')) = YEAR(CURDATE()) AND MONTH(DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d')) = MONTH(CURDATE()) ";
			}
			
			$survey_output_date_condition = " AND SUBSTRING(survey_id,-14,6) = '".$current_month."' ";
			$market_feedback_condition = " AND SUBSTRING(market_feedback_id,-14,6) = '".$current_month."' ";
			$sl_value='MTD';
			$val='MTD';
		}
		//For YTD OR Year Today
		if($i==3){
			$end_date = date('Y-m-d');
			
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
			
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			
			if($month>='04'){
				$fiinancial_year=$year.'-04-01';
			}
			else
			{
				$fiinancial_year=($year-1).'-04-01';
			}

			if(sale=='no' && instruction=='yes')
			{
				//$date_condition=" AND YEAR(LO.date) = YEAR(CURDATE())";
				$date_condition=" AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%-%m-%d') <=DATE_FORMAT(NOW(),'%Y%-%m-%d') 
							AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%-%m-%d') >='".$fiinancial_year."'";
			}
			else
			{
				//$date_condition=" AND YEAR(DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d')) = YEAR(CURDATE())";
				$date_condition=" AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%-%m-%d') <=DATE_FORMAT(NOW(),'%Y%-%m-%d') 
							AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%-%m-%d') >='".$fiinancial_year."'";
			}
			
			$survey_output_date_condition = "  AND (SUBSTRING(survey_id,-14,8) BETWEEN '".str_replace("-","",$fiinancial_year)."' AND '".str_replace("-","",$end_date)."') ";
			
			$market_feedback_condition = "  AND (SUBSTRING(market_feedback_id,-14,8) BETWEEN '".str_replace("-","",$fiinancial_year)."' AND '".str_replace("-","",$end_date)."') ";
			
			
			$sl_value='YTD';
			$val='YTD';
		}
							
		/*$sqlpresent="SELECT COUNT(DISTINCT LO.trans_id) AS no_present  FROM location LO WHERE LO.trans_id LIKE 'A%' 
					AND SUBSTRING(LO.emp_code,1,1)!='C' ".$emp_hierarchy_condition_one.$date_condition;
		$respresent=mysql_query($sqlpresent) or die(mysql_error()." Error in select present information: ".$sqlpresent);
		$rowpresent=mysql_fetch_array($respresent);
		$present=$rowpresent['no_present'];*/
		//For MTD OR Month Today
		/*if($i == 1){
			$sql_get_tdy_present = "SELECT DISTINCT LO.emp_code AS present FROM location LO WHERE SUBSTRING(LO.trans_id,-14,8) ='".$curdateone."' AND LO.emp_code = '".$emp_code."' AND LO.trans_id LIKE 'A%'";
			$res_get_tdy_present = mysql_query($sql_get_tdy_present);
			$emp_present = mysql_num_rows($res_get_tdy_present);
		}*/
		if($i == 2)
		{
			//$days=return_no_days('','M');
			//$present=ceil($present/$days);
			$sql_get_mtd_present = "SELECT DISTINCT LO.emp_code AS present FROM location LO WHERE SUBSTRING(LO.trans_id,-14,6) ='".$current_month."' AND LO.emp_code = '".$emp_code."' AND LO.trans_id LIKE 'A%'";
			$res_get_mtd_present = mysql_query($sql_get_mtd_present);
			$emp_present = mysql_num_rows($res_get_mtd_present);
		}
		//For YTD OR Year Today
		if($i == 3)
		{
			 $sql_get_ytd_present = "SELECT DISTINCT LO.emp_code AS present FROM location LO WHERE (SUBSTRING(LO.date,1,10) BETWEEN '".$fiinancial_year."' AND '".$end_date."') AND LO.emp_code = '".$emp_code."' AND LO.trans_id LIKE 'A%'";
			 $res_get_ytd_present = mysql_query($sql_get_ytd_present);
			 $emp_present = mysql_num_rows($res_get_ytd_present);
		}
		
		if($i == 2 || $i == 3){
		/*----------> Total Customer Visit <----------*/
							
		/*$customer_code_array = array();
		$sql_order_header_customer = "SELECT OH.customer_code, SUBSTRING(LO.date,1,10) AS lo_date FROM order_header OH, location LO WHERE SUBSTRING(OH.order_no,-19,5) = '".$emp_code."' AND OH.order_no = LO.trans_id ".$date_condition." GROUP BY OH.customer_code, SUBSTRING(LO.date,1,10)";
		$res_order_header_customer = mysql_query($sql_order_header_customer);
		while($row_order_header_customer = mysql_fetch_array($res_order_header_customer)){
			$order_header_customer_code = $row_order_header_customer['customer_code'];
			$location_date = $row_order_header_customer['lo_date'];
			$customer_concat_date = $order_header_customer_code."^".$location_date;
			if(!in_array($customer_concat_date,$customer_code_array))
				array_push($customer_code_array,$customer_concat_date);
		}
		
		$sql_collection_customer = "SELECT PH.customer_code, SUBSTRING(LO.date,1,10) AS lo_date FROM payment_header PH, location LO WHERE SUBSTRING(PH.receipt_id,-19,5) = '".$emp_code."' AND PH.receipt_id = LO.trans_id ".$date_condition."  GROUP BY PH.customer_code, SUBSTRING(LO.date,1,10)";
		$res_collection_customer = mysql_query($sql_collection_customer);
		while($row_collection_customer = mysql_fetch_array($res_collection_customer)){
			$collection_customer_code = $row_collection_customer['customer_code'];
			$location_date = $row_collection_customer['lo_date'];
			$customer_concat_date = $collection_customer_code."^".$location_date;
			if(!in_array($customer_concat_date,$customer_code_array))
				array_push($customer_code_array,$customer_concat_date);
		}
		
		$sql_stock_audit_customer = "SELECT SA.customer_code, SUBSTRING(LO.date,1,10) AS lo_date FROM stock_audit SA, location LO WHERE SUBSTRING(SA.transaction_id,-19,5) = '".$emp_code."' AND SA.transaction_id = LO.trans_id ".$date_condition."   GROUP BY SA.customer_code, SUBSTRING(LO.date,1,10)";
		$res_stock_audit_customer = mysql_query($sql_stock_audit_customer);
		while($row_stock_audit_customer = mysql_fetch_array($res_stock_audit_customer)){
			$stock_audit_customer = $row_stock_audit_customer['customer_code'];
			$location_date = $row_stock_audit_customer['lo_date'];
			$customer_concat_date = $stock_audit_customer."^".$location_date;
			if(!in_array($customer_concat_date,$customer_code_array))
				array_push($customer_code_array,$customer_concat_date);
		}
		
		$sql_market_feedback = "SELECT MF.customer_code, SUBSTRING(LO.date,1,10) AS lo_date FROM market_feedback MF, location LO WHERE SUBSTRING(market_feedback_id,3,5) = '".$emp_code."' AND  MF.market_feedback_id = LO.trans_id ".$date_condition." GROUP BY MF.customer_code, SUBSTRING(LO.date,1,10)";
		$res_market_feedback = mysql_query($sql_market_feedback);
		while($row_market_feedback = mysql_fetch_array($res_market_feedback)){
			$market_feedback_customer = $row_market_feedback['customer_code'];
			$market_feedback_date = $row_market_feedback['lo_date'];
			$market_concat_date = $market_feedback_customer."^".$market_feedback_date;
			if(!in_array($market_concat_date,$customer_code_array))
				array_push($customer_code_array,$market_concat_date);
		}*/
		
		$no_customer_visit = '';
		
		/*----------> Total Collection <----------*/
		$sqltotalcollection="SELECT SUM(PD.amount) AS total_collection_received
							FROM payment_details PD,location LO
							WHERE LO.trans_id LIKE 'P%' AND LO.trans_id=PD.receipt_id 
							AND SUBSTRING(LO.emp_code,1,1)!='C' AND LO.emp_code = '".$emp_code."'".$date_condition;
		$rstotalcollection=mysql_query($sqltotalcollection) or die(mysql_error()." Error in total collection received: ".$sqltotalcollection);
		$rowtotalcollection=mysql_fetch_array($rstotalcollection);
		$collection_received=$rowtotalcollection['total_collection_received'];
		
		/*----------> Total Order <----------*/
		$sqltotalorder="SELECT SUM(OD.qty) AS total_order_received
						FROM order_details OD,location LO
						WHERE  LO.trans_id LIKE 'O%' AND LO.trans_id=OD.order_no AND SUBSTRING(LO.emp_code,1,1)!='C'
						AND LO.emp_code = '".$emp_code."'".$date_condition;
		$rstotalorder=mysql_query($sqltotalorder) or die(mysql_error()." Error in total order received: ".$sqltotalorder);
		$rowtotalorder=mysql_fetch_array($rstotalorder);
		$total_order_qty=$rowtotalorder['total_order_received'];
		
		/*----------> Total No Transaction <----------*/
		$sqlnotransaction="SELECT COUNT(LO.trans_id)AS total_no_transaction
							FROM location LO WHERE (SUBSTRING(LO.trans_id,1,2) = 'NO' OR SUBSTRING(LO.trans_id,1,2) = 'NC' OR SUBSTRING(LO.trans_id,1,2) = 'NS' OR SUBSTRING(LO.trans_id,1,3) = 'NSU') 
							AND SUBSTRING(LO.emp_code,1,1)!='C' AND LO.emp_code = '".$emp_code."'".$date_condition;
		$rsnotransaction=mysql_query($sqlnotransaction) or die(mysql_error()." Error in total no transaction: ".$sqlnotransaction);
		$rownotransaction=mysql_fetch_array($rsnotransaction);
		$no_transaction=$rownotransaction['total_no_transaction'];
		
		/*----------> Total Stock Audit <----------*/
		$sqlnostkaudit="SELECT SUM(SA.quantity)AS total_stk_audit FROM location LO,stock_audit SA 
								WHERE SA.transaction_id=LO.trans_id AND (LO.trans_id LIKE 'S%') 
								AND SUBSTRING(LO.emp_code,1,1)!='C' AND LO.emp_code = '".$emp_code."' ".$date_condition;
		$rsnostkaudit=mysql_query($sqlnostkaudit) or die(mysql_error()." Error in total no stk audit: ".$sqlnostkaudit);
		$rownostkaudit=mysql_fetch_array($rsnostkaudit);
		$no_stk_audit=$rownostkaudit['total_stk_audit'];
		
		/*----------> Total KYC <----------*/
		$sql_KYC_count = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type = 'KYC' AND SUBSTRING(survey_id,3,5) = '".$emp_code."' ".$survey_output_date_condition;
		$res_KYC_count = mysql_query($sql_KYC_count);
		$row_KYC_count = mysql_fetch_array($res_KYC_count);
		$KYC_count = $row_KYC_count['COUNT(DISTINCT survey_id)'];
		
		/*----------> Total Branding <----------*/
		$sql_brand_count = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type = 'Branding' AND SUBSTRING(survey_id,3,5) = '".$emp_code."' ".$survey_output_date_condition;
		$res_brand_count = mysql_query($sql_brand_count);
		$row_brand_count = mysql_fetch_array($res_brand_count);
		$brand_count = $row_brand_count['COUNT(DISTINCT survey_id)'];
		
		/*----------> Total Technical Meets <----------*/
		$sql_technical_count = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type = 'Technical Meets' AND SUBSTRING(survey_id,3,5) = '".$emp_code."' ".$survey_output_date_condition;
		$res_technical_count = mysql_query($sql_technical_count);
		$row_technical_count = mysql_fetch_array($res_technical_count);
		$technical_count = $row_technical_count['COUNT(DISTINCT survey_id)'];
		
		/*----------> Total Site Visit <----------*/
		$sql_site_visit = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type = 'Site Visit' AND SUBSTRING(survey_id,3,5) = '".$emp_code."' ".$survey_output_date_condition;
		$res_site_visit = mysql_query($sql_site_visit);
		$row_site_visit = mysql_fetch_array($res_site_visit);
		$site_visit_count = $row_site_visit['COUNT(DISTINCT survey_id)'];
		
		/*----------> Total Market Feedback <----------*/
		$sql_market_feedback = "SELECT COUNT(DISTINCT market_feedback_id) FROM market_feedback WHERE SUBSTRING(market_feedback_id,3,5) = '".$emp_code."' ".$market_feedback_condition;
		$res_market_feedback = mysql_query($sql_market_feedback);
		$row_market_feedback = mysql_fetch_array($res_market_feedback);
		$market_feedback_count = $row_market_feedback['COUNT(DISTINCT market_feedback_id)'];
		
		$total_activity = ($no_customer_visit + $KYC_count + $brand_count + $technical_count + $site_visit_count + $market_feedback_count);
		
		$sql_emp_exist_check = "SELECT emp_code FROM mis_data_details WHERE emp_code = '".$emp_code."'";
		$res_emp_exist_check = mysql_query($sql_emp_exist_check);
		$emp_row_check = mysql_num_rows($res_emp_exist_check);
		if($emp_row_check>0){
			
			$sql_update = "UPDATE mis_data_details SET 
											`emp_code` = '".$emp_code."', 
										 `sale_access` = '".$sale_access."', 
										`reporting_to` = '".$reporting_to."', 
									   `customer_code` = '', 
										  $present_col = '".$emp_present."', 
								 $customer_visited_col = '".$no_customer_visit."', 
								   $order_received_col = '".$total_order_qty."', 
								   $no_transaction_col = '".$no_transaction."', 
									  $stock_audit_col = '".$no_stk_audit."', 
											  $kyc_col = '".$KYC_count."', 
								   $brand_activity_col = '".$brand_count."', 
								   $technical_meet_col = '".$technical_count."', 
									   $site_visit_col = '".$site_visit_count."', 
								  $market_feedback_col = '".$market_feedback_count."' WHERE emp_code = '".$emp_code."'";
			$res_update = mysql_query($sql_update);
		}
		else{
			$sql_insert = "INSERT INTO mis_data_details SET 
											`emp_code` = '".$emp_code."', 
										 `sale_access` = '".$sale_access."', 
										`reporting_to` = '".$reporting_to."', 
									   `customer_code` = '', 
										  $present_col = '".$emp_present."', 
								 $customer_visited_col = '".$no_customer_visit."', 
								   $order_received_col = '".$total_order_qty."', 
								   $no_transaction_col = '".$no_transaction."', 
									  $stock_audit_col = '".$no_stk_audit."', 
											  $kyc_col = '".$KYC_count."', 
								   $brand_activity_col = '".$brand_count."', 
								   $technical_meet_col = '".$technical_count."', 
									   $site_visit_col = '".$site_visit_count."', 
								  $market_feedback_col = '".$market_feedback_count."'";
			$res_insert = mysql_query($sql_insert);
		}
		}
	}
}