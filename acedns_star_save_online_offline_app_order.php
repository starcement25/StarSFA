<?php
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$t_dochallan = "T_DOCHALLAN";
$customer_master = "customer_master";
$product_master = "product_master";
function show_customer_code_from_dns_customer_code($the_dns_customer_code){
$the_cust_code = "";
$customer_master = "customer_master";
if($the_dns_customer_code!=""){
$sql1 = "select `customer_code` from $customer_master where `dns_customer_code`='$the_dns_customer_code'";	
$res1 = mysqli_query($link,$sql1);
$totres1 = mysqli_num_rows($res1);
	if($totres1>0){
		$row1 = mysqli_fetch_assoc($res1);
		$the_cust_code = $row1["customer_code"] ? addslashes(trim($row1["customer_code"])) : "";
	}
}

return $the_cust_code;
}
function show_dns_customer_from_customer_code_code($the_customer_code){
$the_cust_code = "";
$customer_master = "customer_master";
if($the_customer_code!=""){
$sql1 = "select `dns_customer_code` from $customer_master where `customer_code`='$the_customer_code'";	
$res1 = mysqli_query($link,$sql1);
$totres1 = mysqli_num_rows($res1);
	if($totres1>0){
		$row1 = mysqli_fetch_assoc($res1);
		$the_cust_code = $row1["dns_customer_code"] ? addslashes(trim($row1["dns_customer_code"])) : "";
	}
}

return $the_cust_code;
}
function show_product_data_from_prod_code($the_prod_code){
$product_dtls = array("dns_prod_code"=>"","prod_desc"=>"");
$product_master = "product_master";
if($the_prod_code!=""){
$sql1 = "select `dns_prod_code`,`prod_desc` from $product_master where `prod_code`='$the_prod_code'";	
$res1 = mysqli_query($link,$sql1);
$totres1 = mysqli_num_rows($res1);
	if($totres1>0){
		$row1 = mysqli_fetch_assoc($res1);
		$dns_prod_code = $row1["dns_prod_code"] ? addslashes(trim($row1["dns_prod_code"])) : "";
		$prod_desc = $row1["prod_desc"] ? addslashes(trim($row1["prod_desc"])) : "";
		$product_dtls = array("dns_prod_code"=>$dns_prod_code,"prod_desc"=>$prod_desc);
	}
}

return $product_dtls;
}
$apporderno = $_REQUEST["apporderno"] ? addslashes(trim($_REQUEST["apporderno"])) : "";
$erporderno = $_REQUEST["erporderno"] ? addslashes(trim($_REQUEST["erporderno"])) : "";
$erporderdt = $_REQUEST["erporderdt"] ? addslashes(trim($_REQUEST["erporderdt"])) : "";
$order_for = $_REQUEST["order_for"] ? addslashes(trim($_REQUEST["order_for"])) : "";
$customer_code = $_REQUEST["customer_code"] ? addslashes(trim($_REQUEST["customer_code"])) : "";
$dns_customer_code = $_REQUEST["dns_customer_code"] ? addslashes(trim($_REQUEST["dns_customer_code"])) : "";

$prod_code = $_REQUEST["prod_code"] ? addslashes(trim($_REQUEST["prod_code"])) : "";
$dns_prod_code = $_REQUEST["dns_prod_code"] ? addslashes(trim($_REQUEST["dns_prod_code"])) : "";
$qty = $_REQUEST["qty"] ? addslashes(trim($_REQUEST["qty"])) : "";
$truckno = $_REQUEST["truckno"] ? addslashes(trim($_REQUEST["truckno"])) : "";
$driverno = $_REQUEST["driverno"] ? addslashes(trim($_REQUEST["driverno"])) : "";


if($apporderno!="" || $erporderno!=""){
	if($dns_customer_code==""){
		$dns_customer_code = show_dns_customer_from_customer_code_code($customer_code);
	}
	
	$prod_dtld = show_product_data_from_prod_code($prod_code);
	$prod_dtld_dns = $prod_dtld["dns_prod_code"];
	$prod_dtld_desc = $prod_dtld["prod_desc"];
	if($dns_prod_code==""){
		$dns_prod_code = $prod_dtld_dns;
	}
	
	$sqlin = "insert into $t_apperpdo (`APPORDERNO`,`ERPORDERNO`,`ERPORDERDT`,`order_for`,`customer_code`,`dns_customer_code`,`prod_code`,`dns_prod_code`,`prod_display_name`,`QTY`) values('$apporderno','$erporderno','$erporderdt','$order_for','$customer_code','$dns_customer_code','$prod_code','$dns_prod_code','$prod_dtld_desc','$qty')";
	$resin = mysqli_query($link,$sqlin);
	
	

	/*$sqlintodo = "insert into $t_dochallan (`APPORDERNO`,`ERPORDERNO`,`ERPORDERDT`,`prod_code`,`dns_prod_code`,`prod_display_name`,`QTY`,`customer_code`,`dns_customer_code`,`TRUCKNO`,`DRIVERNO`) values('$apporderno','$erporderno','$erporderdt','$prod_code','$dns_prod_code','$prod_dtld_desc','$qty','$customer_code','$dns_customer_code','$truckno','$driverno')";
	$resintodo = mysqli_query($link,$sqlintodo);*/
	
	$res_data = array("process_status"=>"YES","process_message"=>"The order successfully received.");
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"The id is mandatory.");
}
echo json_encode($res_data);
mysqli_close();
?>