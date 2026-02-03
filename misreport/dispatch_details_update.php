<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");
date_default_timezone_set("Asia/Kolkata");

$invoice_no = $_REQUEST['invoice_no'];
$invoice_amount = $_REQUEST['invoice_amount'];
$order_no = $_REQUEST['order_no'];
$dispatch_through = $_REQUEST['dispatch_through'];

$dispatch_qty_array = $_REQUEST['dispatch_qty_array'];
$remarks_array = $_REQUEST['remarks_array'];
$remarks_array_new = array();

foreach($remarks_array as $remarks_val){
	$remarks_val_explode = explode("_",$remarks_val);
	$remarks_prod_code = $remarks_val_explode[1];
	$remarks_value = $remarks_val_explode[2];
	$remarks_key = $order_no."_".$remarks_prod_code;
	$remarks_array_new[$remarks_key] = $remarks_value;
}

$entry_date_time = date('Y-m-d H:i:s');
$dispatch_id = "DP".strtoupper($_SESSION['admin_login']).date('YmdHis');
$ip = $_SERVER['REMOTE_ADDR'];
$dispatch_flag = 0;

foreach($dispatch_qty_array as $dispatch_qty){
	$dispatch_qty_explode = explode("_",$dispatch_qty);
	$sku_code = $dispatch_qty_explode[1];
	$dispatch_quantity = $dispatch_qty_explode[2];
	$remarks_key_one = $order_no."_".$sku_code;
	$remarks_insert = $remarks_array_new[$remarks_key_one];
	
		
	$sql_billed_qty = "SELECT billed_qty FROM order_details WHERE order_no = '".$order_no."' AND sku_code = '".$sku_code."'";
	$res_billed_qty = mysqli_query($link,$sql_billed_qty);
	$row_billed_qty = mysqli_fetch_assoc($res_billed_qty);
	$billed_qty = $row_billed_qty['billed_qty'];
	
	$sql_insert_dispatch = "INSERT INTO dispatch_tracking SET 
								  `order_no` = '".$order_no."', 
								`entry_date` = '".$entry_date_time."', 
								   `user_id` = '".$dispatch_id."', 
										`IP` = '".$ip."', 
								 `prod_code` = '".$sku_code."', 
								`billed_qty` = '".$billed_qty."',
							  `dispatch_qty` = '".$dispatch_quantity."',
								`invoice_no` = '".$invoice_no."',
							`invoice_amount` = '".$invoice_amount."',
						  `dispatch_through` = '".$dispatch_through."',
								   `remarks` = '".$remarks_insert."'";
	mysqli_query($link,$sql_insert_dispatch);
}

$sql_check_billed_dispatch_qty = "SELECT prod_code, billed_qty, SUM(dispatch_qty) FROM dispatch_tracking WHERE order_no = '".$order_no."' AND invoice_no = '".$invoice_no."' GROUP BY prod_code";
$res_check_billed_dispatch_qty = mysqli_query($link,$sql_check_billed_dispatch_qty);
while($row_check_billed_dispatch_qty = mysqli_fetch_assoc($res_check_billed_dispatch_qty)){
	$prod_code = $row_check_billed_dispatch_qty['prod_code'];
	$billed_qty = $row_check_billed_dispatch_qty['billed_qty'];
	$dispatch_qty = $row_check_billed_dispatch_qty['SUM(dispatch_qty)'];
	
	if($dispatch_qty != $billed_qty)
		$dispatch_flag = 1;
}

if($dispatch_flag == 0){
	$sql_order_header = "UPDATE order_header SET status = 'dispatch' WHERE order_no = '".$order_no."'";
	mysqli_query($link,$sql_order_header);
}

echo "Order Dispatched";

mysqli_close($link);
?>