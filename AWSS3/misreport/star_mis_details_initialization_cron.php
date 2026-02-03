<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_STAR");
date_default_timezone_set("Asia/Kolkata");

mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB);

$today = date('Y-m-d');

/*----------> Today Initialize <----------*/
$sql_initialize_tdy = "UPDATE mis_data_details SET
								   `customer_code` = '', 
									 `present_tdy` = '', 
							`customer_visited_tdy` = '', 
							  `order_received_tdy` = '', 
							  `no_transaction_tdy` = '', 
								 `stock_audit_tdy` = '', 
										 `kyc_tdy` = '', 
							  `brand_activity_tdy` = '', 
							  `technical_meet_tdy` = '', 
								  `site_visit_tdy` = '', 
							 `market_feedback_tdy` = ''";
$res_initialize_tdy = mysql_query($sql_initialize_tdy);

/*----------> MTD Initialize <----------*/
if(substr($today,-2,2) == '01'){
	$sql_initialize_mtd = "UPDATE mis_data_details SET
									 `present_mtd` = '', 
							`customer_visited_mtd` = '', 
							  `order_received_mtd` = '', 
							  `no_transaction_mtd` = '', 
								 `stock_audit_mtd` = '', 
										 `kyc_mtd` = '', 
							  `brand_activity_mtd` = '', 
							  `technical_meet_mtd` = '', 
								  `site_visit_mtd` = '', 
							 `market_feedback_mtd` = ''";
	$res_initialize_mtd = mysql_query($sql_initialize_mtd);
}

/*----------> YTD Initialize <----------*/
if(substr($today,-5) == '04-01'){
	$sql_initialize_ytd = "UPDATE mis_data_details SET
									 `present_ytd` = '', 
							`customer_visited_ytd` = '', 
							  `order_received_ytd` = '', 
							  `no_transaction_ytd` = '', 
								 `stock_audit_ytd` = '', 
										 `kyc_ytd` = '', 
							  `brand_activity_ytd` = '', 
							  `technical_meet_ytd` = '', 
								  `site_visit_ytd` = '', 
							 `market_feedback_ytd` = ''";
	$res_initialize_ytd = mysql_query($sql_initialize_ytd);
}

//echo "Initialized";
?>