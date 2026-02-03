<?php
// define("SERVER","localhost");
// define("USER","root");
// define("PASSWORD","Passw0rd123#$");
// define("DB","acedns_STAR");
date_default_timezone_set("Asia/Kolkata");
require_once("../sfa_connection.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);
$localDB = new sfa_connection();
$link = $localDB->conn;
// mysqli_connect(SERVER,USER,PASSWORD);
// mysqli_select_db(DB);

$today = date('Y-m-d');

/*----------> Today Initialize <----------*/
// $sql_initialize_tdy = "UPDATE mis_data_details SET
// 								   `customer_code` = '',
// 									 `present_tdy` = '',
// 							`customer_visited_tdy` = '',
// 							  `order_received_tdy` = '',
// 							  `no_transaction_tdy` = '',
// 								 `stock_audit_tdy` = '',
// 										 `kyc_tdy` = '',
// 							  `brand_activity_tdy` = '',
// 							  `technical_meet_tdy` = '',
// 								  `site_visit_tdy` = '',
// 							 `market_feedback_tdy` = ''";
$sql_initialize_tdy = "UPDATE mis_data_details SET
    `customer_code` = '',
    `present_tdy` = 0,
    `customer_visited_tdy` = 0,
    `order_received_tdy` = 0,
    `no_transaction_tdy` = 0,
    `stock_audit_tdy` = 0,
    `kyc_tdy` = 0,
    `brand_activity_tdy` = 0,
    `technical_meet_tdy` = 0,
    `site_visit_tdy` = 0,
    `market_feedback_tdy` = 0";
							
$res_initialize_tdy = mysqli_query($link,$sql_initialize_tdy);
//  print_r($today) ;
// die;
/*----------> MTD Initialize <----------*/
// if(substr($today,-2,2) == '01'){
// 	$sql_initialize_mtd = "UPDATE mis_data_details SET
// 									 `present_mtd` = '',
// 							`customer_visited_mtd` = '',
// 							  `order_received_mtd` = '',
// 							  `no_transaction_mtd` = '',
// 								 `stock_audit_mtd` = '',
// 										 `kyc_mtd` = '',
// 							  `brand_activity_mtd` = '',
// 							  `technical_meet_mtd` = '',
// 								  `site_visit_mtd` = '',
// 							 `market_feedback_mtd` = ''";
// 	$res_initialize_mtd = mysqli_query($link,$sql_initialize_mtd);
// }

if (substr($today, -2, 2) == '01') {
	$sql_initialize_mtd = "UPDATE mis_data_details SET
		`present_mtd` = 0,
		`customer_visited_mtd` = 0,
		`order_received_mtd` = 0,
		`no_transaction_mtd` = 0,
		`stock_audit_mtd` = 0,
		`kyc_mtd` = 0,
		`brand_activity_mtd` = 0,
		`technical_meet_mtd` = 0,
		`site_visit_mtd` = 0,
		`market_feedback_mtd` = 0";
	$res_initialize_mtd = mysqli_query($link, $sql_initialize_mtd);
}

/*----------> YTD Initialize <----------*/
// if(substr($today,-5) == '04-01'){
// 	$sql_initialize_ytd = "UPDATE mis_data_details SET
// 									 `present_ytd` = '',
// 							`customer_visited_ytd` = '',
// 							  `order_received_ytd` = '',
// 							  `no_transaction_ytd` = '',
// 								 `stock_audit_ytd` = '',
// 										 `kyc_ytd` = '',
// 							  `brand_activity_ytd` = '',
// 							  `technical_meet_ytd` = '',
// 								  `site_visit_ytd` = '',
// 							 `market_feedback_ytd` = ''";
// 	$res_initialize_ytd = mysqli_query($link,$sql_initialize_ytd);
// }

if (substr($today, -5) == '04-01') {
	$sql_initialize_ytd = "UPDATE mis_data_details SET
		`present_ytd` = 0,
		`customer_visited_ytd` = 0,
		`order_received_ytd` = 0,
		`no_transaction_ytd` = 0,
		`stock_audit_ytd` = 0,
		`kyc_ytd` = 0,
		`brand_activity_ytd` = 0,
		`technical_meet_ytd` = 0,
		`site_visit_ytd` = 0,
		`market_feedback_ytd` = 0";
	$res_initialize_ytd = mysqli_query($link, $sql_initialize_ytd);
}

//echo "Initialized";


echo "SUCCESS";

mysqli_close($link);
?>
