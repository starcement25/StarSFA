<?php
header('Content-type: text/html; charset=utf-8');
include "star_connection.php";
mysqli_set_charset("UTF8");
$T_APPERPDO = "T_APPERPDO";
$T_DOCHALLAN = "T_DOCHALLAN";
$res_data = array();
$order_data = array();
$order_challan_data = array();
$the_id = $_REQUEST["the_id"] ? addslashes(trim($_REQUEST["the_id"])) : "";
if($the_id!=""){
$sqlall = "select * from $T_APPERPDO where `customer_code`='$the_id'";
$resall = mysqli_query($link,$sqlall);
$totall = mysqli_num_rows($resall);
if($totall>0){
	while($row11=mysqli_fetch_assoc($resall)){
		$apporderno = $row11["APPORDERNO"] ? trim($row11["APPORDERNO"]) : "";
		$erporderno = $row11["ERPORDERNO"] ? trim($row11["ERPORDERNO"]) : "";
		$erporderdt = $row11["ERPORDERDT"];
		$isdoimp = "";
		$order_for = $row11["order_for"] ? trim($row11["order_for"]) : "";
		if($order_for!=""){
			if( strpos($order_for,",") !== false ) {
				$ofrarr = array();
				$ofrarr = explode(",",$order_for);
				if(count($ofrarr)>0){
					$order_for = $ofrarr[0];
				}
			}
		}
		$customer_code = $row11["customer_code"];
		$dns_customer_code = $row11["dns_customer_code"];
		$status = $row11["STATUS"];
		
		$prod_code = $row11["prod_code"];
		$dns_prod_code = $row11["dns_prod_code"];
		$prod_display_name = $row11["prod_display_name"];
		$qty = $row11["QTY"];
		
		$the_actual_ord_id = $apporderno ? addslashes($apporderno) : addslashes($erporderno);
		$order_challan_data = array();
		if($the_actual_ord_id!=""){
			$sqlall2 = "select * from $T_DOCHALLAN where (`APPORDERNO`='$the_actual_ord_id' or `ERPORDERNO`='$the_actual_ord_id')";
			$resall2 = mysqli_query($link,$sqlall2);
			$totall2 = mysqli_num_rows($resall2);			
			if($totall2>0){
				while($row112=mysqli_fetch_assoc($resall2)){
				$ch_apporderno = $row112["APPORDERNO"] ? trim($row112["APPORDERNO"]) : "";
				$ch_erporderno = $row112["ERPORDERNO"] ? trim($row112["ERPORDERNO"]) : "";
				$ch_erporderdt = $row112["ERPORDERDT"] ? trim($row112["ERPORDERDT"]) : "";
				$ch_challanno = $row112["CHALLANNO"] ? trim($row112["CHALLANNO"]) : "";
				$ch_challandt = $row112["CHALLANDT"] ? trim($row112["CHALLANDT"]) : "";
				$ch_prod_code = $row112["prod_code"] ? trim($row112["prod_code"]) : "";
				$ch_dns_prod_code = $row112["dns_prod_code"] ? trim($row112["dns_prod_code"]) : "";
				$ch_prod_display_name = $row112["prod_display_name"] ? trim($row112["prod_display_name"]) : "";
				$ch_qty = $row112["QTY"] ? trim($row112["QTY"]) : "";
				$ch_challanqty = $row112["CHALLANQTY"] ? trim($row112["CHALLANQTY"]) : "";
				$ch_customer_code = $row112["customer_code"];
				$ch_dns_customer_code = $row112["dns_customer_code"];
				$ch_truckno = $row112["TRUCKNO"] ? trim($row112["TRUCKNO"]) : "";
				$ch_driverno = $row112["DRIVERNO"] ? trim($row112["DRIVERNO"]) : "";
				$ch_ischlnimp = "";
				$order_challan_data[] = array("apporderno"=>$ch_apporderno,"erporderno"=>$ch_erporderno,"erporderdt"=>$ch_erporderdt,"challanno"=>$ch_challanno,"challandt"=>$ch_challandt,"prod_code"=>$ch_prod_code,"dns_prod_code"=>$ch_dns_prod_code,"prod_display_name"=>$ch_prod_display_name,"qty"=>$ch_qty,"challanqty"=>$ch_challanqty,"customer_code"=>$ch_customer_code,"dns_customer_code"=>$ch_dns_customer_code,"truckno"=>$ch_truckno,"driverno"=>$ch_driverno,"ischlnimp"=>$ch_ischlnimp);
				
				}
			}			
		}
$order_data[] = array("apporderno"=>$apporderno,"erporderno"=>$erporderno,"erporderdt"=>$erporderdt,"isdoimp"=>$isdoimp,"order_for"=>$order_for,"customer_code"=>$customer_code,"dns_customer_code"=>$dns_customer_code,"status"=>$status,"prod_code"=>$prod_code,"dns_prod_code"=>$dns_prod_code,"prod_display_name"=>$prod_display_name,"qty"=>$qty,"order_challan_data"=>$order_challan_data);

	}

$res_data = array("process_status"=>"YES","process_message"=>"Success.","order_data"=>$order_data);
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"No order data found.");
}
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"The id is mandatory.");
}	
echo json_encode($res_data);
mysqli_close();
?>