<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$order_no = $_REQUEST['order_no'];
$reason_text = $_REQUEST['reason_text'];
$reason_type = $_REQUEST['reason_type'];
$billed_qty_array = $_REQUEST['billed_qty_array'];
$invoice_no = $_REQUEST['invoice_no'];
$invoice_amt = $_REQUEST['invoice_amt'];

if($reason_type == 'hold' || $reason_type == 'spl permission'){
	$sql_update_order_header = "UPDATE order_header SET status = '".$reason_type."', reason = '".addslashes($reason_text)."' WHERE order_no = '".$order_no."'";
	mysql_query($sql_update_order_header);
	
	$set_status = $reason_type;
	$order_track_user_id = "OT".strtoupper($_SESSION['admin_login']).date('YmdHis');
	$sql_order_details = "SELECT sku_code, qty FROM order_details WHERE order_no = '".$order_no."'";
	$res_order_details = mysql_query($sql_order_details);
	while($row_order_details = mysql_fetch_array($res_order_details)){
		$sku_code = $row_order_details['sku_code'];
		$order_qty = $row_order_details['qty'];
		$sql_insert_order_track = "INSERT INTO order_tracking SET 
											  `order_no` = '".$order_no."', 
											`entry_date` = '".date('Y-m-d H:i:s')."', 
											   `user_id` = '".$order_track_user_id."', 
													`IP` = '".$_SERVER['REMOTE_ADDR']."', 
											 `prod_code` = '".$sku_code."', 
											 `order_qty` = '".$order_qty."',
												`status` = '".$set_status."',
												`reason` = '".addslashes($reason_text)."'";
		mysql_query($sql_insert_order_track);
	}
	echo "Updated Successfully";
}
else if($reason_type == 'send_to_pending'){
	$sql_update_order_header = "UPDATE order_header SET status = 'pending', reason = '' WHERE order_no = '".$order_no."'";
	mysql_query($sql_update_order_header);
	
	$order_track_user_id = "OT".strtoupper($_SESSION['admin_login']).date('YmdHis');
	$sql_order_details = "SELECT sku_code, qty FROM order_details WHERE order_no = '".$order_no."'";
	$res_order_details = mysql_query($sql_order_details);
	while($row_order_details = mysql_fetch_array($res_order_details)){
		$sku_code = $row_order_details['sku_code'];
		$order_qty = $row_order_details['qty'];
		$sql_insert_order_track = "INSERT INTO order_tracking SET 
											  `order_no` = '".$order_no."', 
											`entry_date` = '".date('Y-m-d H:i:s')."', 
											   `user_id` = '".$order_track_user_id."', 
													`IP` = '".$_SERVER['REMOTE_ADDR']."', 
											 `prod_code` = '".$sku_code."', 
											 `order_qty` = '".$order_qty."',
												`status` = 'pending'";
		mysql_query($sql_insert_order_track);
	}
	
	echo "Updated Successfully";
}
else if($reason_type == 'billed'){
	$order_track_user_id = "OT".strtoupper($_SESSION['admin_login']).date('YmdHis');
	$billed_flag = 0;
	foreach($billed_qty_array as $billed_qty_val){
		$billed_qty_explode = explode("_",$billed_qty_val);
		
		$order_no_get = $billed_qty_explode[0];
		$sku_code = $billed_qty_explode[1];
		$billed_qty = $billed_qty_explode[2];
		
		$sql_update_order_details = "UPDATE order_details SET billed_qty = (billed_qty+".$billed_qty.") WHERE order_no = '".$order_no_get."' AND sku_code = '".$sku_code."'";
		$res_update_order_details = mysql_query($sql_update_order_details);
		
		$sql_order_qty = "SELECT qty, billed_qty FROM order_details WHERE order_no = '".$order_no_get."' AND sku_code = '".$sku_code."'";
		$res_order_qty = mysql_query($sql_order_qty);
		$row_order_qty = mysql_fetch_array($res_order_qty);
		$order_qty = $row_order_qty['qty'];
		$billed_qty_check = $row_order_qty['billed_qty'];
		
		if($billed_qty_check != $order_qty){
			$billed_flag = 1;
			$set_status = 'pending';
		}
		else{
			$set_status = 'billed';
		}
		
		$sql_insert_order_track = "INSERT INTO order_tracking SET 
											  `order_no` = '".$order_no_get."', 
											`entry_date` = '".date('Y-m-d H:i:s')."', 
											   `user_id` = '".$order_track_user_id."', 
													`IP` = '".$_SERVER['REMOTE_ADDR']."', 
											 `prod_code` = '".$sku_code."', 
											 `order_qty` = '".$order_qty."', 
											`billed_qty` = '".$billed_qty."', 
											`invoice_no` = '".$invoice_no."', 
										`invoice_amount` = '".$invoice_amt."',
												`status` = '".$set_status."'";
		$res_insert_order_track = mysql_query($sql_insert_order_track);
	}
	
	if($billed_flag == 0){
		$status = 'billed';
	}
	else if($billed_flag == 1){
		$status = 'pending';
	}
	
	$sql_update_order_header = "UPDATE order_header SET status = '".$status."' WHERE order_no = '".$order_no_get."'";
	$res_update_order_header = mysql_query($sql_update_order_header);
	
	echo "Updated Successfully";
}
mysql_close($link);
?>