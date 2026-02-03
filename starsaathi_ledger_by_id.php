<?php
// define("SERVERREMOTE","103.87.174.95");
// define("USERREMOTE","starsaat_dnsprod");
// define("PASSWORDREMOTE","dnsprod1234#");
// //define("DBREMOTE","starsaat_START");
// define("DBREMOTE","starsaathi_STARS");

define("SERVERREMOTE","starsaathi-rds-server.clcy6zb4izp8.ap-south-1.rds.amazonaws.com");
define("USERREMOTE","admin");
define("PASSWORDREMOTE","zwPB6L65ZC}p8L89");
//define("DBREMOTE","starsaat_START");
define("DBREMOTE","starsaathi_STARS");

$link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,DBREMOTE) or die("Database Connection Error.");
	
/*$link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,TRUE) or die(mysqli_error()."Database Connection Error.");
mysqli_select_db(DBREMOTE,$link) or die(mysqli_error()."could not connect the database");*/

function isJsonCk($str) {
    $json = json_decode($str);
    return $json && $str != $json;
}
function get_data_from_cserver($url_ck){
$useragent = $_SERVER['HTTP_USER_AGENT'];
$username = "MOBILEAPP";
$password = "Star@#2021";
$ch_sheader = curl_init();
curl_setopt($ch_sheader, CURLOPT_URL,$url_ck);
curl_setopt($ch_sheader, CURLOPT_RETURNTRANSFER,true);
//curl_setopt($ch_sheader, CURLOPT_USERPWD, "$username:$password");
curl_setopt($ch_sheader, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch_sheader, CURLOPT_USERAGENT, $useragent);
$body_for_mcode = curl_exec($ch_sheader);
//echo "<pre>";
$info = curl_getinfo($ch_sheader);
//print_r($info);
curl_close($ch_sheader);
return $body_for_mcode;
}
$ledger = "ledger";
$ledger_balance = "ledger_balance";
$customer_master = "customer_master";
$ledger_data = array();
$ledger_balance_data = array();
$curr_date = date("Y-m-d");
$the_id = $_REQUEST["the_id"] ? addslashes(trim($_REQUEST["the_id"])) : "";
if($the_id!=""){
$sql3 = "select `dns_customer_code`,`customer_id`,customer_code from $customer_master where `dns_customer_code`='$the_id'";
$res3 = mysqli_query($link,$sql3);
$totres3 = mysqli_num_rows($res3);
if($totres3>0){
$row3 = mysqli_fetch_assoc($res3);
$the_dealer_id = trim($row3["dns_customer_code"]);
$the_customer_id = trim($row3["customer_id"]);
$the_customer_code = trim($row3["customer_code"]);
$dlr_code_qry = "  or `dns_customer_code`='$the_dealer_id'";
$dlr_code_qry2 = "  or `dns_customer_code`='$the_dealer_id'";	
}else{
$dlr_code_qry = "";	
$dlr_code_qry2 = "";
$the_customer_id = "";
}
//echo $the_customer_code;
if($the_customer_id!=""){
	
		$url_ck="https://starsaathi.com/SAP/acedns_star_ledger_by_id.php?the_id=".$the_customer_code;
		$body_for_mcode10 = get_data_from_cserver($url_ck);
		//print_r($body_for_mcode10);die;
	if(isJsonCk($body_for_mcode10)){
	//$json_decoded21 = json_decode($body_for_mcode10,true);
		echo $body_for_mcode10;
	}
}
}
else{	
	$res_data = array("process_status"=>"NO","process_message"=>"The id is mandatory.");
}
exit();


/*$ledger = "ledger";
$ledger_balance = "ledger_balance";
$customer_master = "customer_master";
$ledger_data = array();
$ledger_balance_data = array();
$the_id = $_REQUEST["the_id"] ? addslashes(trim($_REQUEST["the_id"])) : "";
if($the_id!=""){

$sql3 = "select `customer_code` from $customer_master where `dns_customer_code`='$the_id'";
$res3 = mysqli_query($link,$sql3);
$totres3 = mysqli_num_rows($res3);
if($totres3>0){
$row3 = mysqli_fetch_assoc($res3);
$the_dealer_id = addslashes(trim($row3["customer_code"]));
$dlr_code_qry = "  or `dns_customer_code`='$the_id'";
$dlr_code_qry2 = "  or `dns_customer_code`='$the_id'";	
}else{
	$the_dealer_id='';
$dlr_code_qry = "";	
$dlr_code_qry2 = "";
}
$sqlall = "select *,DATE_FORMAT(STR_TO_DATE(`voucher_date`, '%m/%d/%Y %h:%i:%s %p'), '%Y-%m-%d %H:%i:%s') as `e_date` from $ledger where `customer_code`='$the_dealer_id' $dlr_code_qry order by `e_date` desc limit 0,50";
$resall = mysqli_query($link,$sqlall);
$totall = mysqli_num_rows($resall);
if($totall>0){
	while($row11=mysqli_fetch_assoc($resall)){
		$customer_code = $row11["customer_code"];
		$dns_customer_code = $row11["dns_customer_code"];
		$voucher_date = $row11["voucher_date"] ? trim($row11["voucher_date"]) : "";
		if($voucher_date!=""){
			$voucher_date = date("m/d/Y h:i:s A",strtotime($voucher_date));
		}
		$voucher_no = $row11["voucher_no"];
		$quantity = $row11["quantity"]." MT";
		$amount_dr = $row11["amount_dr"];
		$amount_cr = $row11["amount_cr"];
		$balance = $row11["balance"];
		$entry_date = $row11["entry_date"];
		$converted_voucher_date = "";
		$ledger_data[] = array("customer_code"=>$customer_code,"dns_customer_code"=>$dns_customer_code,"voucher_date"=>$voucher_date,"voucher_no"=>$voucher_no,"quantity"=>$quantity,"amount_dr"=>$amount_dr,"amount_cr"=>$amount_cr,"balance"=>$balance,"narration"=>$balance,"entry_date"=>$entry_date,"converted_voucher_date"=>$converted_voucher_date);
	}	
}

$sqlall2 = "select * from $ledger_balance where `customer_code`='$the_dealer_id' $dlr_code_qry2";
$resall2 = mysqli_query($link,$sqlall2);
$totall2 = mysqli_num_rows($resall2);
if($totall2>0){
$row112=mysqli_fetch_assoc($resall2);
$lb_customer_code = $row112["customer_code"];
$lb_dns_customer_code = $row112["dns_customer_code"];
$balance = $row112["balance"];
/*$date = $row112["date"] ? trim($row112["date"]) : "";
if($date!=""){
$date = date("m/d/Y h:i:s A",strtotime($date));
}*/
/*$date = date("m/d/Y");
$link = $row112["link"];
$ledger_balance_data = array("customer_code"=>$lb_customer_code,"dns_customer_code"=>$lb_dns_customer_code,"balance"=>$balance,"date"=>$date,"link"=>$link);
}

$res_data = array("process_status"=>"YES","process_message"=>"Success.","ledger_data"=>$ledger_data,"ledger_balance_data"=>$ledger_balance_data);

}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"The id is mandatory.");
}*	
echo json_encode($res_data);
mysqli_close();*/
?>