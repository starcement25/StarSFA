<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$dns_code = $_REQUEST['dns_code'];
$customer_name = $_REQUEST['customer_name'];
$phn = $_REQUEST['phn'];
$address = $_REQUEST['address'];
$pin = $_REQUEST['pin'];
$emp_code = $_REQUEST['emp_code'];
$route = $_REQUEST['route'];
$branch = $_REQUEST['branch'];
$current_balance = $_REQUEST['current_balance'];
$credit_limit = $_REQUEST['credit_limit'];
$credit_days = $_REQUEST['credit_days'];
$acedns = $_REQUEST['acedns'];
$blacklist = $_REQUEST['blacklist'];
$td = $_REQUEST['td'];
$customer_type = $_REQUEST['customer_type'];
$rds_tag = $_REQUEST['rds_tag'];
$sauda_valid_period = $_REQUEST['sauda_valid_period'];

$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE customer_code NOT LIKE '%N%'";
$rsmaxcustomercode=mysql_query($sqlmaxcustomercode);
$rowmaxcustomercode=mysql_fetch_array($rsmaxcustomercode);
$max_customer_code=$rowmaxcustomercode['max_customer_code'];
$max_customer_code++;

$sql_insert = "INSERT INTO customer_master SET 
										 `customer_code` = '".$max_customer_code."', 
									 `dns_customer_code` = '".$dns_code."', 
										 `customer_name` = '".$customer_name."', 
											   `address` = '".$address."', 
												   `pin` = '".$pin."', 
											  `phone_no` = '".$phn."', 
											`route_code` = '".$route."', 
											  `emp_code` = '".$emp_code."', 
										   `branch_code` = '".$branch."', 
									   `current_balance` = '".$current_balance."', 
										  `credit_limit` = '".$credit_limit."', 
										   `credit_days` = '".$credit_days."', 
												`acedns` = '".$acedns."', 
											`black_list` = '".$blacklist."', 
													`TD` = '".$td."', 
											 `cust_type` = '".$customer_type."', 
											   `rds_tag` = '".$rds_tag."', 
								 `sauda_validity_period` = '".$sauda_valid_period."', 
										 `download_time` = current_timestamp, 
							`download_time_credit_limit` = current_timestamp";
$res_insert = mysql_query($sql_insert);
echo "Data successfully inserted";

mysql_close($link);
?>