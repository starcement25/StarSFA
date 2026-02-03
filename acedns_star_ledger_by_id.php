<?php
include "star_connection.php";
$ledger = "ledger";
$ledger_balance = "ledger_balance";
$ledger_data = array();
$ledger_balance_data = array();
$the_id = $_REQUEST["the_id"] ? addslashes(trim($_REQUEST["the_id"])) : "";
if($the_id!=""){
$sqlall = "select *,DATE_FORMAT(STR_TO_DATE(`voucher_date`, '%m/%d/%Y %h:%i:%s %p'), '%Y-%m-%d %H:%i:%s') as `e_date` from $ledger where `customer_code`='$the_id' order by `e_date` desc limit 0,50";
$resall = mysqli_query($link,$sqlall);
$totall = mysqli_num_rows($resall);
if($totall>0){
	while($row11=mysqli_fetch_assoc($resall)){
		$customer_code = $row11["customer_code"];
		$dns_customer_code = $row11["dns_customer_code"];
		$voucher_date = $row11["voucher_date"];
		$voucher_no = $row11["voucher_no"];
		$quantity = $row11["quantity"]." MT";
		$amount_dr = $row11["amount_dr"];
		$amount_cr = $row11["amount_cr"];
		$balance = $row11["balance"];
		$entry_date = $row11["entry_date"];
		$converted_voucher_date = $row11["e_date"];
		$ledger_data[] = array("customer_code"=>$customer_code,"dns_customer_code"=>$dns_customer_code,"voucher_date"=>$voucher_date,"voucher_no"=>$voucher_no,"quantity"=>$quantity,"amount_dr"=>$amount_dr,"amount_cr"=>$amount_cr,"balance"=>$balance,"narration"=>$balance,"entry_date"=>$entry_date,"converted_voucher_date"=>$converted_voucher_date);
	}	
}


$sqlall2 = "select * from $ledger_balance where `customer_code`='$the_id'";
$resall2 = mysqli_query($link,$sqlall2);
$totall2 = mysqli_num_rows($resall2);
if($totall2>0){
$row112=mysqli_fetch_assoc($resall2);
$lb_customer_code = $row112["customer_code"];
$lb_dns_customer_code = $row112["dns_customer_code"];
$balance = $row112["balance"];
$date = $row112["date"];
$link = $row112["link"];
$ledger_balance_data = array("customer_code"=>$lb_customer_code,"dns_customer_code"=>$lb_dns_customer_code,"balance"=>$balance,"date"=>$date,"link"=>$link);
}

$res_data = array("process_status"=>"YES","process_message"=>"Success.","ledger_data"=>$ledger_data,"ledger_balance_data"=>$ledger_balance_data);

}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"The id is mandatory.");
}	
echo json_encode($res_data);
mysqli_close();
?>