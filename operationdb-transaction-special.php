<?php
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");
/*if($nick_name=='ABDOS')
{
	echo $flag=0;
	exit();
}*/
function getReverseGeo($latitude,$longitude)
{
	// format this string with the appropriate latitude longitude
	$url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true";
	// make the HTTP request
	$data = @file_get_contents($url);
	// parse the json response
	$jsondata = json_decode($data,true);

	//print_r($jsondata);
	// if we get a formatted_address array and the status was OK, get the addres
	if(is_array($jsondata )&& $jsondata['status']=='OK')
	{
		  $addr = $jsondata['results']['0']['formatted_address'];
	}
	return  $addr;
}

$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
//For authorized employee fetching
$sqlauthemp="SELECT emp_code,phone_no FROM employee_master WHERE SUBSTRING(designation,1,4)='auth' ORDER BY SUBSTRING(designation,5,1) ASC";
$rsauthemp=mysqli_query($link,$sqlauthemp);
while($rowauthemp=mysqli_fetch_assoc($rsauthemp))
{
	$emp_code_auth=$rowauthemp['emp_code'];
	$emp_code_phone=$rowauthemp['phone_no'];

	$sqlloggedindatetime="SELECT loggedin_date_time,registrationid FROM changepassword WHERE emp_code='".$emp_code_auth."'";
	$rsloggedindatetime=mysqli_query($link,$sqlloggedindatetime);
	$rowloggedindatetime=mysqli_fetch_assoc($rsloggedindatetime);
	$logged_in_date=date('d-m-Y',strtotime(substr($rowloggedindatetime['loggedin_date_time'],0,10)));
	$registrationid=$rowloggedindatetime['registrationid'];
	if($logged_in_date==date('d-m-Y'))
	{
		$authorized_emp_code=$emp_code_auth;
		$authorized_emp_phone=$emp_code_phone;
		$authorized_emp_registrationid=$registrationid;
		break;
	}
}
if($authorized_emp_code==''){
	$sqlauthempone="SELECT EM.emp_code,EM.phone_no,CH.registrationid FROM employee_master EM,changepassword CH WHERE
					SUBSTRING(EM.designation,1,4)='auth' AND EM.emp_code=CH.emp_code ORDER BY SUBSTRING(EM.designation,5,1) ASC LIMIT 0,1";
	$rsauthempone=mysqli_query($link,$sqlauthempone);
	$rowauthempone=mysqli_fetch_assoc($rsauthempone);

	$authorized_emp_code=$rowauthempone['emp_code'];
	$authorized_emp_phone=$rowauthempone['phone_no'];
	$authorized_emp_registrationid=$rowauthempone['registrationid'];
}


if($nick_name=='AMPL' || $nick_name=='TT')
{
  $spam_filter='-facedns@coral.in';
}
else
{
  $spam_filter='-facedns@acedns.in';
}

$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$result = mysqli_query($link,$sqlquery);
$countdatarefresh=mysqli_num_rows($result);
$body=file_get_contents('php://input');


	$body_xml=str_replace("'",'"',$body);
	$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
	mysqli_query($link,$sqlinsert_xml_data);



/*$body="<?xml version='1.0' encoding='UTF-8'?><root><order><location><emp_code><![CDATA[E0029]]></emp_code><trans_id><![CDATA[OE002920171208144256]]></trans_id><latt><![CDATA[22.5644535]]></latt><longi><![CDATA[88.3567542]]></longi><date><![CDATA[2017-12-08 14:42:56]]></date></location><orderdata><order_header><order_no><![CDATA[OE002920171208144256]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[5300]]></order_value><d_instruction><![CDATA[test]]></d_instruction><tag_distributor_code><![CDATA[]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0000323]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[SPA]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002920171208144256]]></Order_no><Sku_code><![CDATA[12026]]></Sku_code><qty><![CDATA[10.0]]></qty><TD><![CDATA[1]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[30]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[300]]></amount><UOM><![CDATA[null]]></UOM><mrp_code><![CDATA[0]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002920171208144256]]></Order_no><Sku_code><![CDATA[12089]]></Sku_code><qty><![CDATA[50.0]]></qty><TD><![CDATA[1]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[36]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1800]]></amount><UOM><![CDATA[null]]></UOM><mrp_code><![CDATA[0]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002920171208144256]]></Order_no><Sku_code><![CDATA[12199]]></Sku_code><qty><![CDATA[40.0]]></qty><TD><![CDATA[0]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[5]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[200]]></amount><UOM><![CDATA[null]]></UOM><mrp_code><![CDATA[0]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002920171208144256]]></Order_no><Sku_code><![CDATA[12019]]></Sku_code><qty><![CDATA[30.0]]></qty><TD><![CDATA[0]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[100]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3000]]></amount><UOM><![CDATA[null]]></UOM><mrp_code><![CDATA[0]]></mrp_code></order_details></orderdata></order></root>";*/

$order_emp_code="*ROOT*ORDER*LOCATION*EMP_CODE";
$order_trans_id = "*ROOT*ORDER*LOCATION*TRANS_ID";
$order_latt = "*ROOT*ORDER*LOCATION*LATT";
$order_longi = "*ROOT*ORDER*LOCATION*LONGI";
$order_date="*ROOT*ORDER*LOCATION*DATE";
$orderdataheader_order_no = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*ORDER_NO";
$orderdataheader_customer_code = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*CUSTOMER_CODE";
$orderdataheader_customer_name = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*CUSTOMER_NAME";
$orderdataheader_phone_no = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*PHONE_NO";
$orderdataheader_address = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*ADDRESS";
$orderdataheader_pin_code = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*PIN_CODE";
$orderdataheader_area = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*AREA";
$orderdataheader_area_name = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*AREA_NAME";
$orderdataheader_TD = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*TD";
$orderdataheader_sale_type = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*SALE_TYPE";
$orderdataheader_d_instruction = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*D_INSTRUCTION";
$orderdataheader_tag_distributor_code = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*TAG_DISTRIBUTOR_CODE";
$orderdataheader_cust_type = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*CUST_TYPE";
$orderdataheader_customer_flag = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*CUSTOMER_FLAG";
$orderdataheader_transaction_type = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*TRANSACTION_TYPE";
$orderdataheader_VAT= "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*VAT";
$orderdataheader_grn_no = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*GRN_NO";
$orderdataheader_vertical_value = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*VERTICAL_VALUE";
$orderdataheader_destination_code = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*DESTINATION_CODE";
$orderdataheader_order_type = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*ORDER_TYPE";
$orderdataheader_freight_component = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*FREIGHT_COMPONENT";
$orderdataheader_hint_remarks = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*HINT_REMARKS";
$orderdataheader_price_validation_type="*ROOT*ORDER*ORDERDATA*ORDER_HEADER*PRICE_VALIDATION_TYPE";
$orderdataheader_GST_type = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*GST_TYPE";

$orderdatadetails_order_no = "*ROOT*ORDER*ORDERDATA*ORDER_DETAILS*ORDER_NO";
$orderdatadetails_sku_code = "*ROOT*ORDER*ORDERDATA*ORDER_DETAILS*SKU_CODE";
$orderdatadetails_qty = "*ROOT*ORDER*ORDERDATA*ORDER_DETAILS*QTY";
$orderdatadetails_TD = "*ROOT*ORDER*ORDERDATA*ORDER_DETAILS*TD";
$orderdatadetails_premium = "*ROOT*ORDER*ORDERDATA*ORDER_DETAILS*PREMIUM";
$orderdatadetails_sale_rate = "*ROOT*ORDER*ORDERDATA*ORDER_DETAILS*SALE_RATE";
$orderdatadetails_VAT = "*ROOT*ORDER*ORDERDATA*ORDER_DETAILS*VAT";
$orderdatadetails_amount = "*ROOT*ORDER*ORDERDATA*ORDER_DETAILS*AMOUNT";
$orderdatadetails_UOM = "*ROOT*ORDER*ORDERDATA*ORDER_DETAILS*UOM";
$orderdatadetails_mrp_code = "*ROOT*ORDER*ORDERDATA*ORDER_DETAILS*MRP_CODE";



$attendance_array = array();
$order_array=array();
$order_details_array=array();
$stockist_array=array();
$mt_array=array();

$counter = 0;
$counterorder=0;
$counterorderdetails=0;


class xml_attendance{
    var $emp_code, $trans_id,$latt,$longi,$attendance_date,$attendancedata_emp_code,$attendancedata_date;
}
class xml_order{
	var $order_emp_code,$order_trans_id,$order_latt,$order_longi,$order_date,$orderdataheader_order_no,$orderdataheader_customer_code,$orderdataheader_customer_name,
	$orderdataheader_phone_no,$orderdataheader_address,$orderdataheader_pin_code,$orderdataheader_area,$orderdataheader_area_name,$orderdataheader_TD,$orderdataheader_sale_type,$orderdataheader_d_instruction,$orderdataheader_tag_distributor_code,$orderdataheader_cust_type,$orderdataheader_transaction_type,$orderdataheader_VAT,$orderdataheader_customer_flag,$orderdataheader_grn_no,$orderdataheader_vertical_value,$orderdataheader_destination_code,$orderdataheader_order_type,$orderdataheader_freight_component,$orderdataheader_hint_remarks,$orderdataheader_GST_type,$orderdataheader_price_validation_type;
}
class xml_order_details{
	var $orderdatadetails_order_no,$orderdatadetails_sku_code,$orderdatadetails_qty,$orderdatadetails_TD,$orderdatadetails_premium,$orderdatadetails_sale_rate,$orderdatadetails_VAT,$orderdatadetails_amount,$orderdatadetails_UOM,$orderdatadetails_mrp_code;
}


function startTag($parser, $data){
    global $current_tag;
    $current_tag .= "*$data";
}

function endTag($parser, $data){
    global $current_tag;
    $tag_key = strrpos($current_tag, '*');
    $current_tag = substr($current_tag, 0, $tag_key);
}

function contents($parser, $data){
  global $current_tag, $attendance_emp_code, $attendance_trans_id,$attendance_latt,$attendance_longi,$attendance_date,$attendancedata_emp_code,$attendancedata_date, $counter, $counterorder,$counterorderdetails,$counterpayment,$counterpaymentdetails,$attendance_array,$order_array,$order_details_array,$payment_array,$payment_details_array,$stockist_array,$mt_array,
	$order_emp_code,$order_trans_id,$order_latt,$order_longi,$order_date,$orderdataheader_order_no,$orderdataheader_customer_code,$orderdataheader_customer_name,
	$orderdataheader_phone_no,$orderdataheader_address,$orderdataheader_pin_code,$orderdataheader_area,$orderdataheader_area_name,$orderdataheader_TD,$orderdataheader_sale_type,
	$orderdataheader_d_instruction,$orderdataheader_tag_distributor_code,$orderdataheader_cust_type,$orderdataheader_transaction_type,$orderdataheader_VAT,$orderdataheader_grn_no,$orderdataheader_customer_flag,$orderdataheader_vertical_value,$orderdataheader_destination_code,$orderdataheader_order_type,$orderdataheader_freight_component,$orderdataheader_hint_remarks,$orderdataheader_GST_type,$orderdataheader_price_validation_type,$orderdatadetails_order_no,$orderdatadetails_sku_code,$orderdatadetails_qty,$orderdatadetails_TD,$orderdatadetails_premium,$orderdatadetails_sale_rate,$orderdatadetails_VAT,$orderdatadetails_amount,$orderdatadetails_UOM,$orderdatadetails_mrp_code,
	$payment_emp_code,$payment_trans_id,$payment_latt,$payment_longi,$payment_date,$paymentdataheader_receipt_id,$paymentdataheader_customer_code,$paymentdataheader_amount,
	$paymentdataheader_cash_cheque,$paymentdataheader_cheque_no,$paymentdataheader_date,$paymentdataheader_bank,$paymentdataheader_sale_type,$paymentdataheader_p_remark,$paymentdatadetails_receipt_id, $paymentdatadetails_invoice_id,$paymentdatadetails_recid,$paymentdatadetails_amount,$paymentdatadetails_discount;
	//echo $current_tag.'<br />';
	//echo $data;exit;

	if(substr($current_tag,0,11)=='*ROOT*ORDER')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $order_emp_code:
				$order_array[$counterorder] = new xml_order();
				$order_array[$counterorder]->order_emp_code = $data;
				break;
			case $order_trans_id:
				$order_array[$counterorder]->order_trans_id = $data;
				break;
			case $order_latt:
				$order_array[$counterorder]->order_latt = $data;
				break;
			case $order_longi:
				$order_array[$counterorder]->order_longi = $data;
				break;
			case $order_date:
				$order_array[$counterorder]->order_date = $data;
				break;
			case $orderdataheader_order_no:
				$order_array[$counterorder]->orderdataheader_order_no = $data;
				break;
			case $orderdataheader_customer_name:
				$order_array[$counterorder]->orderdataheader_customer_name = $data;
				break;
			case $orderdataheader_phone_no:
				$order_array[$counterorder]->orderdataheader_phone_no = $data;
				break;
			case $orderdataheader_address:
				$order_array[$counterorder]->orderdataheader_address = $data;
				break;
			case $orderdataheader_pin_code:
				$order_array[$counterorder]->orderdataheader_pin_code = $data;
				break;
			case $orderdataheader_area:
				$order_array[$counterorder]->orderdataheader_area = $data;
				break;
			case $orderdataheader_area_name:
				$order_array[$counterorder]->orderdataheader_area_name = $data;
				break;
			case $orderdataheader_TD:
				$order_array[$counterorder]->orderdataheader_TD = $data;
				break;
			case $orderdataheader_sale_type:
				$order_array[$counterorder]->orderdataheader_sale_type = $data;
				break;
			case $orderdataheader_d_instruction:
				$order_array[$counterorder]->orderdataheader_d_instruction = $data;
				break;
			case $orderdataheader_tag_distributor_code:
				$order_array[$counterorder]->orderdataheader_tag_distributor_code = $data;
				break;
			case $orderdataheader_cust_type:
				$order_array[$counterorder]->orderdataheader_cust_type = $data;
				break;
			case $orderdataheader_customer_flag:
				$order_array[$counterorder]->orderdataheader_customer_flag = $data;
				break;
			case $orderdataheader_transaction_type:
				$order_array[$counterorder]->orderdataheader_transaction_type = $data;
				break;
			case $orderdataheader_VAT:
				$order_array[$counterorder]->orderdataheader_VAT = $data;
				break;
			case $orderdataheader_grn_no:
				$order_array[$counterorder]->orderdataheader_grn_no = $data;
				break;
			case $orderdataheader_vertical_value:
				$order_array[$counterorder]->orderdataheader_vertical_value = $data;
				break;
			case $orderdataheader_destination_code:
				$order_array[$counterorder]->orderdataheader_destination_code = $data;
				break;
			case $orderdataheader_order_type:
				$order_array[$counterorder]->orderdataheader_order_type = $data;
				break;
			case $orderdataheader_freight_component:
				$order_array[$counterorder]->orderdataheader_freight_component = $data;
				break;
			case $orderdataheader_hint_remarks:
				$order_array[$counterorder]->orderdataheader_hint_remarks = $data;
				break;
			case $orderdataheader_GST_type:
				$order_array[$counterorder]->orderdataheader_GST_type = $data;
				break;
			case $orderdataheader_customer_code:
				$order_array[$counterorder]->orderdataheader_customer_code = $data;
				break;
			case $orderdataheader_price_validation_type:
				$order_array[$counterorder]->orderdataheader_price_validation_type = $data;
				$counterorder++;
					break;
		}
	}
	if(substr($current_tag,0,35)=='*ROOT*ORDER*ORDERDATA*ORDER_DETAILS')
		{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
			switch($current_tag){
				case $orderdatadetails_order_no:
					$order_details_array[$counterorderdetails] = new xml_order_details();

					$order_details_array[$counterorderdetails]->orderdatadetails_order_no = $data;
					break;
				case $orderdatadetails_sku_code:
					$order_details_array[$counterorderdetails]->orderdatadetails_sku_code = $data;
					break;
				case $orderdatadetails_qty:
					$order_details_array[$counterorderdetails]->orderdatadetails_qty = $data;
					break;
				case $orderdatadetails_TD:
					$order_details_array[$counterorderdetails]->orderdatadetails_TD = $data;
					break;
				case $orderdatadetails_premium:
					$order_details_array[$counterorderdetails]->orderdatadetails_premium = $data;
					break;
				case $orderdatadetails_sale_rate:
					$order_details_array[$counterorderdetails]->orderdatadetails_sale_rate = $data;
					break;
				case $orderdatadetails_VAT:
					$order_details_array[$counterorderdetails]->orderdatadetails_VAT = $data;
					break;
				case $orderdatadetails_amount:
					$order_details_array[$counterorderdetails]->orderdatadetails_amount = $data;
					break;
				case $orderdatadetails_UOM:
					$order_details_array[$counterorderdetails]->orderdatadetails_UOM = $data;
					break;
				case $orderdatadetails_mrp_code:
					$order_details_array[$counterorderdetails]->orderdatadetails_mrp_code = $data;
					$counterorderdetails++;
					break;
		}
	}

}

$xml_parser = xml_parser_create();
xml_set_element_handler($xml_parser, "startTag", "endTag");
xml_set_character_data_handler($xml_parser, "contents");
$data = $body;

if(!(xml_parse($xml_parser, $data, LIBXML_PARSEHUGE))){
    die("Error on line " . xml_get_current_line_number($xml_parser));
}
xml_parser_free($xml_parser);
mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");

$flag=1;

/* --------------------START QUERY FOR ORDER----------------------------------------------------------------------------------------------------------------*/
$order_array_trans_id=array();
$orderheader_array_mailbody=array();
$orderinstruction_array_mailbody=array();
$orderdetails_array_mailbody=array();
$orderemp_array_mailbody=array();
$ordertansid_array_mailbody=array();
$ordercashreceived_array_mailbody=array();
$orderdataheader_sale_type_array=array();
$orderdate_array_mailbody=array();
$orderdataheader_TD_array=array();
$orderdataheader_transaction_type_array=array();
$orderdataheader_entity_name_array=array();
$orderpricevalidation_array_mailbody=array();
//print_r($order_array);
if(count($order_array)>0)
{
	for($x=0;$x<count($order_array);$x++){
		$order_emp_code=$order_array[$x]->order_emp_code;
		$order_trans_id=$order_array[$x]->order_trans_id;
		$order_latt=$order_array[$x]->order_latt;
		$order_longi=$order_array[$x]->order_longi;
		$order_date=$order_array[$x]->order_date;
		$orderdataheader_order_no=$order_array[$x]->orderdataheader_order_no;
		$orderdataheader_customer_code=$order_array[$x]->orderdataheader_customer_code;
		$orderdataheader_customer_name=$order_array[$x]->orderdataheader_customer_name;
		$orderdataheader_phone_no=$order_array[$x]->orderdataheader_phone_no;
		$orderdataheader_address=$order_array[$x]->orderdataheader_address;
		$orderdataheader_pin_code=$order_array[$x]->orderdataheader_pin_code;
		$orderdataheader_area=$order_array[$x]->orderdataheader_area;
		$orderdataheader_area_name=$order_array[$x]->orderdataheader_area_name;
		$orderdataheader_sale_type=$order_array[$x]->orderdataheader_sale_type;
		$orderdataheader_TD=$order_array[$x]->orderdataheader_TD;
		$orderdataheader_cust_type=$order_array[$x]->orderdataheader_cust_type;
		$orderdataheader_customer_flag=$order_array[$x]->orderdataheader_customer_flag;
		$orderdataheader_transaction_type=$order_array[$x]->orderdataheader_transaction_type;
		$orderdataheader_d_instruction=$order_array[$x]->orderdataheader_d_instruction;
		$orderdataheader_VAT=$order_array[$x]->orderdataheader_VAT;
		$orderdataheader_grn_no=$order_array[$x]->orderdataheader_grn_no;
		$orderdataheader_vertical_value=$order_array[$x]->orderdataheader_vertical_value;
		$orderdataheader_tag_distributor_code=$order_array[$x]->orderdataheader_tag_distributor_code;
		$orderdataheader_destination_code=$order_array[$x]->orderdataheader_destination_code;
		$orderdataheader_order_type=$order_array[$x]->orderdataheader_order_type;
		$orderdataheader_freight_component=$order_array[$x]->orderdataheader_freight_component;
		$orderdataheader_hint_remarks=$order_array[$x]->orderdataheader_hint_remarks;
		$orderdataheader_GST_type=$order_array[$x]->orderdataheader_GST_type;
    $orderdataheader_price_validation_type=$order_array[$x]->orderdataheader_price_validation_type;
		if($orderdataheader_sale_type=='CASH'){
		$paymentdataheader_amount=$payment_array[$x]->paymentdataheader_amount;
		}
		${orderdataheader_transaction_type.$orderdataheader_order_no}=$orderdataheader_transaction_type;
		${orderdataheader_grn_no.$orderdataheader_order_no}=$orderdataheader_grn_no;

		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if($order_latt>0 && $order_longi>0)
		{
			$sqlupdatelatlongzero="UPDATE location SET latt='".$order_latt."',longi='".$order_longi."' WHERE
									emp_code='".$order_emp_code."' AND latt='0' AND longi='0'";
			$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero);
		}

		//For checking that trans id exist or not for order
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$order_trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check order location: ".$sqlchkorlocation);
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);

		//For update the location table for existing trans id for order
		if($countchkorlocation>0)
		{
			//$order_trans_id_chk=substr($order_trans_id,1,19);
			if(!in_array($order_trans_id,$order_array_trans_id))
			{
				array_push($order_array_trans_id,$order_trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$order_emp_code."',
									latt='".$order_latt."',
									longi='".$order_longi."'
									WHERE trans_id='".$order_trans_id."'";
			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update order location: ".$sqlupdateorlocation);
			if($rsupdateorlocation)
			{
				$flag=6;
			}
			else
			{
				echo $flag=0;
			}
		}
		else
		{
			$sqlempname="SELECT emp_name,SUBSTRING_INDEX( branch_code, ',', 1 ) AS branch_code,vertical_value,reporting_to,dns_emp_code FROM employee_master WHERE emp_code='".$order_emp_code."'";
			$rsempname=mysqli_query($link,$sqlempname);
			$rowempname=mysqli_fetch_assoc($rsempname);
			$emp_name=title_case_emp($rowempname['emp_name']);
			$branch_code=$rowempname['branch_code'];
			$dns_emp_code=$rowempname['dns_emp_code'];
			$vertical_value=$rowempname['vertical_value'];
			$reporting_to=$rowempname['reporting_to'];

			$sqlnewcustomerrds="SELECT rds_code FROM rds_master WHERE emp_code='".$order_emp_code."'";
			$rsnewcustomerrds=mysqli_query($link,$sqlnewcustomerrds);
			$rownewcustomerrds=mysqli_fetch_assoc($rsnewcustomerrds);
			$rds_codenewcustomerrds=$rownewcustomerrds['rds_code'];

			// create the data for location table date field , by checking the current date and time and the actual date and time of order
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));

			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));

			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			//For Insert into the location table for new trans id regarding order
			$sqlinsertorlocation="INSERT INTO location SET emp_code='".$order_emp_code."',
									trans_id='".$order_trans_id."',
									latt='".$order_latt."',
									longi='".$order_longi."',
									date='".$order_date."',
									updatetime='".$location_date."'";

			if(strtoupper($nick_name)=='STAR')
			{
				$branch_condiion=" branch_code	='".$branch_code."',";
			}
			else
			{
				$branch_condiion='';
			}
			//For Insert into the Order Header table for new trans id
			$sqlinsertorderheader="INSERT INTO order_header SET order_no='".$orderdataheader_order_no."',
								  customer_code 	='".$orderdataheader_customer_code."',
								  sale_type 		='".$orderdataheader_sale_type."',
								  TD				='".$orderdataheader_TD."',
								  tag_distributor_code='".$orderdataheader_tag_distributor_code."',
								  VAT          		='".$orderdataheader_VAT."',
								  transaction_type  ='".$orderdataheader_transaction_type."',
								  vertical_value    ='".$orderdataheader_vertical_value."',
								  destination_code  ='".$orderdataheader_destination_code."',
								  order_type    	='".$orderdataheader_order_type."',
								  hint_remarks    	='".$orderdataheader_hint_remarks."',
								  freight_component ='".$orderdataheader_freight_component."',".$branch_condiion."
								  GST_type 			='".$orderdataheader_GST_type."',
									price_validation_type ='".$orderdataheader_price_validation_type."',
								  d_instruction		='".addslashes($orderdataheader_d_instruction)."'";

						if(mysqli_query($link,$sqlinsertorlocation) && mysqli_query($link,$sqlinsertorderheader))
						{
							$flag=5;
						}
						else
						{
							mysqli_query($link,"ROLLBACK");
							echo $flag=0;
							return;
						}

				//Regarding Email Sending
				if(branch_vertical_operation_wise_email=='yes')
				{
					$operation_type='Order';
					$order_email=fetch_corresponding_emails($operation_type,$vertical_value,$branch_code);
				}
				else
				{
					if($nick_name=='RUPA' || $nick_name=='RUPAT')
					{
						$order_email='';
					}
					else if($nick_name=='STAR')
					{
						$sqlbranchmail="SELECT branch_email_id FROM branch_master WHERE branch_code='".$branch_code."'";
						$rsbranchmail=mysqli_query($link,$sqlbranchmail);
						$rowbranchmail=mysqli_fetch_assoc($rsbranchmail);
						$branch_email_id=$rowbranchmail['branch_email_id'];
						if($branch_email_id !='')
						{
							$order_email=ORDEREMAILRECIPENTS.','.$branch_email_id;
						}
						else
						{
							$order_email=ORDEREMAILRECIPENTS;
						}
					}
					else
					{
						$order_email=ORDEREMAILRECIPENTS;
					}
				}
				if(substr($order_trans_id,0,1)!='N')
				{
					$addressorder=getReverseGeo($order_latt,$order_longi);
				}
				if($orderdataheader_customer_flag=='N'){
				$customer_name=$orderdataheader_customer_name;
				$orderheader_TR="<th style='width:200px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>New Customer Added</span></strong></th>
								<th style='width:150px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Route</span></strong></th>
								<th style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Phone no</span></strong></th>";

				$orderheader_TD="<td style='width:200px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>
								".$customer_name."</span>&nbsp;</td>
								<td style='width:150px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>
								".$orderdataheader_area_name."</span>&nbsp;</td>
								<td style='width:100px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>
								+91-".$orderdataheader_phone_no."</span>&nbsp;</td>";
				}
				else
				{
					$sqlcustomername="SELECT dns_customer_code,customer_name,route_code,cust_type,cust_class FROM customer_master
									WHERE customer_code='".$orderdataheader_customer_code."'";
					$rscustomername=mysqli_query($link,$sqlcustomername);
					$rowcustomername=mysqli_fetch_assoc($rscustomername);
					$customer_name=$rowcustomername['customer_name'];
					$dns_customer_code=$rowcustomername['dns_customer_code'];
					$route_code=$rowcustomername['route_code'];
					$cust_type=$rowcustomername['cust_type'];

					$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$route_code."'";
					$rsroutename=mysqli_query($link,$sqlroutename);
					$rowroutename=mysqli_fetch_assoc($rsroutename);
					$route_name=$rowroutename['route_name'];

					if(providing_code=='yes'){
						$orderheader_dns_customer_code_TR="<th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Customer Code</span></strong></th>";
						$orderheader_dns_customer_code_TD="<td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$dns_customer_code."</span>&nbsp;</td>";
					}
					else
					{
						$orderheader_dns_customer_code_TR='';
						$orderheader_dns_customer_code_TD=='';
					}

					$orderheader_TR=$orderheader_dns_customer_code_TR."<th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Customer Name</span></strong></th><th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Route</span></strong></th>";
					$orderheader_TD=$orderheader_dns_customer_code_TD."<td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$customer_name."</span>&nbsp;</td><td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$route_name."</span>&nbsp;</td>";
				}

				//-------------------------------------------For Sale Feature-----------------------------------------------------------------------------
				if($orderdataheader_transaction_type=='PB')
					{
						$transaction_type_details='Purchase Bill';
						//Vendor
						$sqlvendor="SELECT vendor_name FROM vendor_master WHERE vendor_code='".$orderdataheader_customer_code."'";
						$rsvendor=mysqli_query($link,$sqlvendor);
						$rowvendor=mysqli_fetch_assoc($rsvendor);
						$vendor_name=$rowvendor['vendor_name'];
						$orderheader_TR="<th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Vendor Name</span></strong></th>";
					$orderheader_TD="<td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$vendor_name."</span>&nbsp;</td>";
					$entity_name=$vendor_name;
					}
					if($orderdataheader_transaction_type=='BT' || $orderdataheader_transaction_type=='ST' || $orderdataheader_transaction_type=='SR')
					{
						if($orderdataheader_transaction_type=='BT') $transaction_type_details='Depot Transfer';
						if($orderdataheader_transaction_type=='ST') $transaction_type_details='Stock Transfer';
						if($orderdataheader_transaction_type=='SR') $transaction_type_details='Stock Return';

						if($orderdataheader_transaction_type=='BT' || $orderdataheader_transaction_type=='ST')
						{
							//RDS
							$sqlrds="SELECT rds_name,emp_code FROM rds_master WHERE rds_code='".$orderdataheader_customer_code."'";
							$rsrds=mysqli_query($link,$sqlrds);
							$rowrds=mysqli_fetch_assoc($rsrds);
							$rds_name=$rowrds['rds_name'];
							${stock_receive_emp_code.$orderdataheader_order_no}=$rowrds['emp_code'];
							if($orderdataheader_transaction_type=='BT'){
							$orderheader_TR="<th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Stock Received From</span></strong></th>";
								$sqlrdsnamestockreceived="SELECT rds_name FROM rds_master WHERE emp_code='".substr($orderdataheader_grn_no,1,5)."'";
								$rsrdsnamestockreceived=mysqli_query($link,$sqlrdsnamestockreceived);
								$rowrdsnamestockreceived=mysqli_fetch_assoc($rsrdsnamestockreceived);
								$rds_name=$rowrdsnamestockreceived['rds_name'];
							}
							else
							{
							$orderheader_TR="<th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Stock Transfer To</span></strong></th>";
							}

							$orderheader_TD="<td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$rds_name."</span>&nbsp;</td>";
							$entity_name=$rds_name;

							if($orderdataheader_transaction_type=='ST')
							{
								// updating data refresh log for stock receiving employee
								$sqlInsertstockrefresh="INSERT INTO stock_refresh_log SET emp_code='".${stock_receive_emp_code.$orderdataheader_order_no}."',refresh_date_time=CURRENT_TIMESTAMP(),stock_transferred_by='".$emp_name."'";
								mysqli_query($link,$sqlInsertstockrefresh);


							}
						}
						else
						{
							//Branch
							$sqlbranch="SELECT branch_name FROM branch_master WHERE branch_code='".$orderdataheader_customer_code."'";
							$rsbranch=mysqli_query($link,$sqlbranch);
							$rowbranch=mysqli_fetch_assoc($rsbranch);
							$branch_name=$rowbranch['branch_name'];
							$orderheader_TR="<th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Branch Name</span></strong></th>";
							$orderheader_TD="<td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$branch_name."</span>&nbsp;</td>";
							$entity_name=$branch_name;
						}
					}
					if($orderdataheader_transaction_type=='CN')
					{
						if($orderdataheader_transaction_type=='CN') $transaction_type_details='Carry In Stock Out';
						//Employee
						$sqlempname="SELECT emp_name FROM employee_master WHERE emp_code='".$orderdataheader_customer_code."'";
						$rsempname=mysqli_query($link,$sqlempname);
						$rowempname=mysqli_fetch_assoc($rsempname);
						$stock_hadnded_over_to=$rowempname['emp_name'];
						$orderheader_TR="<th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Employee Name</span></strong></th>";
					  $orderheader_TD="<td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$stock_hadnded_over_to."</span>&nbsp;</td>";
					  $entity_name=$stock_hadnded_over_to;
						// updating data refresh log for receiving the stock
						/*$sqlInsertdatarefresh="INSERT INTO data_refresh_log SET refresh_date_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlInsertdatarefresh);*/
					}
					if($orderdataheader_transaction_type=='SB' || $orderdataheader_transaction_type=='SO' || $orderdataheader_transaction_type=='CR' || $orderdataheader_transaction_type=='TO'){
						if($orderdataheader_transaction_type=='SB') $transaction_type_details='Sale Bill';
						if($orderdataheader_transaction_type=='SO') $transaction_type_details='Sale Order';
						if($orderdataheader_transaction_type=='CR') $transaction_type_details='Carry In Return';
						if($orderdataheader_transaction_type=='TO') $transaction_type_details='Telephonic Order';
						//customer
						$sqlcustomername="SELECT dns_customer_code,customer_name,route_code FROM customer_master WHERE
											customer_code='".$orderdataheader_customer_code."'";
						$rscustomername=mysqli_query($link,$sqlcustomername);
						$rowcustomername=mysqli_fetch_assoc($rscustomername);
						$customer_name=$rowcustomername['customer_name'];
						$dns_customer_code=$rowcustomername['dns_customer_code'];
						$route_code=$rowcustomername['route_code'];

						$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$route_code."'";
						$rsroutename=mysqli_query($link,$sqlroutename);
						$rowroutename=mysqli_fetch_assoc($rsroutename);
						$route_name=$rowroutename['route_name'];

						if(providing_code=='yes'){
							$orderheader_dns_customer_code_TR="<th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Customer Code</span></strong></th>";
							$orderheader_dns_customer_code_TD="<td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$dns_customer_code."</span>&nbsp;</td>";
						}
						else
						{
							$orderheader_dns_customer_code_TR='';
							$orderheader_dns_customer_code_TD=='';
						}

						$orderheader_TR=$orderheader_dns_customer_code_TR."<th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Customer Name</span></strong></th><th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Route</span></strong></th>";
						$orderheader_TD=$orderheader_dns_customer_code_TD."<td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$customer_name."</span>&nbsp;</td><td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$route_name."</span>&nbsp;</td>";
						$entity_name=$customer_name;
					}
					if($orderdataheader_transaction_type=='SA' || $orderdataheader_transaction_type=='SH')
					{
						$transaction_type_details='Material Shortage Booking';
						$orderheader_TR="";
						$orderheader_TD="";
						$entity_name="";
					}
					if($orderdataheader_transaction_type=='RP')
					{
						$transaction_type_details='Replacement';
						$entity_name="";
					}
					//-------------------------------------------End For Sale Feature-------------------------------------------------------------------------

				if($orderdataheader_tag_distributor_code!='')
				{
					if($nick_name=='RKBK')
					{
						$sqldistributor="SELECT rds_name FROM rds_master WHERE rds_code='".$orderdataheader_tag_distributor_code."'";
					}
					else
					{
						$sqldistributor="SELECT customer_name FROM customer_master WHERE customer_code='".$orderdataheader_tag_distributor_code."'";
					}
					$rsdistributor=mysqli_query($link,$sqldistributor);
					$rowdistributor=mysqli_fetch_assoc($rsdistributor);
					if($nick_name=='RKBK')
					{
						$distributor_name=$rowdistributor['rds_name'];
					}
					else
					{
						$distributor_name=$rowdistributor['customer_name'];
					}

					$orderheader_TR_distributor="<th style='width:200px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Tagged To Distributor</span></strong></th>";
					$orderheader_TD_distributor="<td style='width:200px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$distributor_name."</span>&nbsp;</td>";
				}
				else
				{
					$orderheader_TR_distributor="";
					$orderheader_TD_distributor="";
				}

				//For sending email to recipents in case of No Order transaction happened
				if($nick_name=='STAR' || $nick_name=='START')
				{
					if($reporting_to !='')
					{
						if($orderdataheader_hint_remarks=='Branding Requirement' || $orderdataheader_hint_remarks=='Technical Requirement')
						{
							$visit_date=date('d-m-Y H:i:s',strtotime($order_date));
							send_hint_remarks_email_sms($orderdataheader_hint_remarks,$reporting_to,$visit_date,$emp_name,$orderdataheader_customer_code,$orderdataheader_d_instruction,$nick_name,$orderdataheader_order_no);
						}
					}
				}
				$last_operation_datetime=$order_date;
				if(substr($order_trans_id,0,1)=='N')
				{
			      //Start for STAR mis data details No order data updation
				  if($nick_name=='STAR')
					{
						$qty='';
						$trans_type='NO';
						$trans_sub_type='';
						update_transaction_STAR($order_emp_code,$orderdataheader_order_no,$qty,$trans_type,$trans_sub_type);
					}
				  //End for STAR mis data details No order data updation
					$noorderemailsubj="$nick_name - Activiy without Transaction ".$emp_name. " on ".date('d-m-Y',strtotime($order_date))." @".date('H:i:s',strtotime($order_date)).' hrs.';

					$noorderemailbody = "<html><head><title>No Order</title></head>
										<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
										.$emp_name. "</b><br><br>".$emp_name." visited ".$customer_name.". No order happened. </table><br><br>
										<b>Remarks: </b> ".strtoupper($orderdataheader_d_instruction)."
										<br><br><br>Powered By aceDNS<br></body></html>";
					$headers  = "MIME-Version: 1.0\r\n";
					$headers .= "Content-type: text/html; charset=UTF-8\n";
					$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
								"Bcc: ".BCCEMAIL." \r\n".
								'X-Mailer: PHP/' . phpversion();
					if(mail($order_email,$noorderemailsubj, $noorderemailbody, $headers,$spam_filter))
					{
						$flag=5;
					}
					else
					{
						mysqli_query($link,"ROLLBACK");
						echo $flag=0;
						return;
					}
				}// End of if block for No Order transaction checking
				else{
					if(vertical_fields=='yes')
					{
						$orderheader_TR_vertical="<th style='width:200px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Vertical Value</span></strong></th>";
						$orderheader_TD_vertical="<td style='width:200px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$orderdataheader_vertical_value."</span>&nbsp;</td>";
					}
					if(destination_price_list=='yes' || destination_ordertype_price_list=='yes' || destination=='yes')
					{
						$sqldestination="SELECT destination_name FROM destination_master WHERE destination_code='".$orderdataheader_destination_code."'";
						$rsdestination=mysqli_query($link,$sqldestination);
						$rowdestination=mysqli_fetch_assoc($rsdestination);
						$destination_name=$rowdestination['destination_name'];

						$orderheader_TR_destination="<th style='width:200px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Destination</span></strong></th>";
						$orderheader_TD_destination="<td style='width:200px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$destination_name."</span>&nbsp;</td>";
          }
					//For constructing the email body for ORDER HEADER if ORDER has performed
					$orderemailbody = "<html><head><title>Order Details</title></head>
								<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
								.$emp_name. "</b><br /><br />Refference no: <b>".$orderdataheader_order_no."</b><br /><br /><table border=1 style=background-color:AliceBlue>
								<tr>".$orderheader_TR.$orderheader_TR_distributor.$orderheader_TR_vertical."
								<th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial
								CE'>Date & Time</span></strong></th>
								<th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial
								CE'>Transaction Type</span></strong></th>".$orderheader_TR_destination.$orderheader_TR_ordertype.$orderheader_TR_freight_component."
								</tr><tr>".$orderheader_TD.$orderheader_TD_distributor.$orderheader_TD_vertical."
								<td style='width:165px;text-align:right;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".date('d-m-Y H:i:s',strtotime($order_date))."</span>&nbsp;</td>
								<td style='width:165px;text-align:right;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$transaction_type_details."</span>&nbsp;</td>".$orderheader_TD_destination.$orderheader_TD_ordertype.$orderheader_TD_freight_component."
								</tr></table>";

							array_push($orderheader_array_mailbody,$orderemailbody);
							if($nick_name=='KARMA')
							{
								$sqldns_branch_code="SELECT dns_branch_code,costcenter FROM branch_master WHERE branch_code='".$branch_code."'";
								$rsdns_branch_code=mysqli_query($link,$sqldns_branch_code);
								$rowdns_branch_code=mysqli_fetch_assoc($rsdns_branch_code);
								${dns_branch_code.$order_trans_id}=$rowdns_branch_code['dns_branch_code'];
								${cost_center.$order_trans_id}=$rowdns_branch_code['costcenter'];
								${branch_code_GST.$order_trans_id}=$branch_code;
								$guid=getGUID();

								//$SO_order_date=date('Y-m-dTH:i:sZ',strtotime($order_date));
								$SO_date=gmdate('d',strtotime($order_date));
								$SO_month=gmdate('m',strtotime($order_date));
								$SO_year=gmdate('Y',strtotime($order_date));

								$SO_hour=gmdate('H',strtotime($order_date));
								$SO_minute=gmdate('i',strtotime($order_date));
								$SO_second=gmdate('s',strtotime($order_date));

								$SO_order_date=$SO_year.'-'.$SO_month.'-'.$SO_date.'T'.$SO_hour.':'.$SO_minute.':'.$SO_second.'Z';

								${xml_order_header.$order_trans_id}='<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" 	xmlns:ent="http://www.ramco.com/iris/EnterpriseServices/"><soap:Header/><soap:Body><ent:sOCreate_Authorize_OP><ent:contextparam><ent:CustomerCode>'.$dns_customer_code.'</ent:CustomerCode><ent:Ouinstance>'.${dns_branch_code.$order_trans_id}.'</ent:Ouinstance><ent:Sessionid>'.$guid.'</ent:Sessionid><ent:Transactionid>'.$guid.'</ent:Transactionid><ent:User>'.$dns_emp_code.'</ent:User></ent:contextparam><ent:dms_socreateauthorize_inout><ent:Add_co1></ent:Add_co1><ent:Advancetype></ent:Advancetype><ent:Bank_cashcode></ent:Bank_cashcode><ent:Card_number></ent:Card_number><ent:Card_type></ent:Card_type><ent:Cardholdername></ent:Cardholdername><ent:Countrycode></ent:Countrycode><ent:Currencycode>INR</ent:Currencycode><ent:Customer_bankname></ent:Customer_bankname><ent:Excha_rate></ent:Excha_rate><ent:Expirymonthyear></ent:Expirymonthyear><ent:Fax></ent:Fax><ent:Mobile></ent:Mobile><ent:Organizationid></ent:Organizationid><ent:Phone></ent:Phone><ent:Receipt_amount>0.00</ent:Receipt_amount><ent:Receiptdate1>1900-01-01T00:00:00Z</ent:Receiptdate1><ent:Saleorder_no>'.$order_trans_id.'</ent:Saleorder_no><ent:So_address1></ent:So_address1><ent:So_countryname></ent:So_countryname><ent:So_createddate>1900-01-01T00:00:00Z</ent:So_createddate><ent:So_customercode>'.$dns_customer_code.'</ent:So_customercode><ent:So_customername></ent:So_customername><ent:So_modifieddate>1900-01-01T00:00:00Z</ent:So_modifieddate><ent:So_orderdate>'.$SO_order_date.'</ent:So_orderdate> <ent:So_receiptdate>1900-01-01T00:00:00Z</ent:So_receiptdate><ent:So_receiptmode></ent:So_receiptmode><ent:So_receiptno>NO</ent:So_receiptno><ent:So_receiptroute></ent:So_receiptroute><ent:So_salespromotionno></ent:So_salespromotionno><ent:So_userid>'.$dns_emp_code.'</ent:So_userid><ent:Soc_payterm></ent:Soc_payterm><ent:Soemail></ent:Soemail><ent:Transactionnumber></ent:Transactionnumber><ent:Zip_code></ent:Zip_code></ent:dms_socreateauthorize_inout>';
							${order_GST_type.$order_trans_id}=$orderdataheader_GST_type;
							${So_lineno.$order_trans_id}=1;
							${So_orderdate.$order_trans_id}=$SO_order_date;
							}
							$orderinstructionemailbody="Delivery Instruction: <b>".strtoupper($orderdataheader_d_instruction)."</b>";
							/*if($orderdataheader_sale_type=='CASH'){
								//$ordercashreceivedemailbody="<br /><table>Received Cash: <b>Rs.".number_format($paymentdataheader_amount,2)."/-</b></table><br />";
								//array_push($ordercashreceived_array_mailbody,$ordercashreceivedemailbody);
							}*/
							array_push($orderdataheader_sale_type_array,$orderdataheader_sale_type);
							array_push($orderdataheader_TD_array,$orderdataheader_TD);
							array_push($orderinstruction_array_mailbody,$orderinstructionemailbody);
							array_push($orderemp_array_mailbody,$emp_name);
							array_push($orderdate_array_mailbody,$order_date);
							array_push($orderpricevalidation_array_mailbody,$orderdataheader_price_validation_type);
							array_push($ordertansid_array_mailbody,$order_trans_id);
							array_push($orderdataheader_transaction_type_array,$orderdataheader_transaction_type);
							array_push($orderdataheader_entity_name_array,$entity_name);
							if($nick_name=='DNV' || $nick_name=='HALDIRAM')
							{
								${cust_type.$orderdataheader_order_no}=$cust_type;
							}
					}
		 }//End of else
	}// End for loop
	//For Insert into the Order Details table for new trans id
	//print_r($order_details_array);
	//exit();
		if(count($order_details_array)>0)
		{
			for($i=0;$i<count($order_details_array);$i++){
				$orderdatadetails_order_no=$order_details_array[$i]->orderdatadetails_order_no;
				$orderdatadetails_sku_code=$order_details_array[$i]->orderdatadetails_sku_code;
				$orderdatadetails_qty=$order_details_array[$i]->orderdatadetails_qty;
				$orderdatadetails_TD=$order_details_array[$i]->orderdatadetails_TD;
				$orderdatadetails_premium=$order_details_array[$i]->orderdatadetails_premium;
				$orderdatadetails_VAT=$order_details_array[$i]->orderdatadetails_VAT;
				$orderdatadetails_grn_no=$order_details_array[$i]->orderdatadetails_grn_no;
				$orderdatadetails_mrp_code=$order_details_array[$i]->orderdatadetails_mrp_code;
				$orderdatadetails_amount=$order_details_array[$i]->orderdatadetails_amount;
				$orderdatadetails_UOM=$order_details_array[$i]->orderdatadetails_UOM;
				$orderdatadetails_sale_rate=$order_details_array[$i]->orderdatadetails_sale_rate;

				$sqlrdscode="SELECT rds_code FROM rds_master WHERE emp_code='".substr($orderdatadetails_order_no,1,5)."'";
				$rsrdscode=mysqli_query($link,$sqlrdscode);
				$rowrdscode=mysqli_fetch_assoc($rsrdscode);
				$rds_code_stock_update=$rowrdscode['rds_code'];

				$sqlmrpdetails="SELECT mrp,sale_rate,UOM,ws_rate,distributor_rate,ss_rate FROM mrp WHERE product_code='".$orderdatadetails_sku_code."'
								AND mrp_code='".$orderdatadetails_mrp_code."'";
				$rsmrpdetails=mysqli_query($link,$sqlmrpdetails);
				$recmrpdetails=mysqli_fetch_assoc($rsmrpdetails);
				if(stock_audit_rate=='no')
				{
					$mrp=$recmrpdetails['mrp'];
					$sale_rate_db=$recmrpdetails['sale_rate'];
				}
				else
				{
					$mrp=$orderdatadetails_sale_rate;
					$sale_rate_db=$orderdatadetails_sale_rate;
				}
				$UOM=$recmrpdetails['UOM'];

				if($nick_name=='DNV' || $nick_name=='HALDIRAM')
				{
					if(${cust_type.$orderdatadetails_order_no}=='R')  $orderdatadetails_sale_rate=$orderdatadetails_sale_rate;
					if(${cust_type.$orderdatadetails_order_no}=='D')  $orderdatadetails_sale_rate=$recmrpdetails['distributor_rate'];
					if(${cust_type.$orderdatadetails_order_no}=='SS') $orderdatadetails_sale_rate=$recmrpdetails['ss_rate'];
					if(${cust_type.$orderdatadetails_order_no}=='WS') $orderdatadetails_sale_rate=$recmrpdetails['ws_rate'];
				}
					if(no_of_filter==1){
						$sqlproductdetails="SELECT prod_desc,product_group_code,UOM1,UOM2,conversion_factor,dns_prod_code FROM product_master WHERE prod_code='".$orderdatadetails_sku_code."'";
					}
					if(no_of_filter==2){
						$sqlproductdetails="SELECT PGM.product_group_name,PM.product_group_code,PM.prod_desc,PM.conversion_factor,PM.UOM1,PM.UOM2,PM.dns_prod_code
											FROM product_master PM,product_group_master PGM
											WHERE PM.product_group_code=PGM.product_group_code AND PM.prod_code='".$orderdatadetails_sku_code."'";
					}
					if(no_of_filter==3){
						$sqlproductdetails="SELECT PGM.product_group_name,PM.product_group_code,PSGM.product_sub_group_name,PM.prod_desc,PM.dns_prod_code FROM
											product_master PM,product_group_master PGM,product_sub_group_master PSGM
											WHERE PM.product_group_code=PGM.product_group_code AND PM.product_sub_group_code=PSGM.product_sub_group_code
											AND PM.prod_code='".$orderdatadetails_sku_code."'";
					}
					if(no_of_filter==4){
						$sqlproductdetails="SELECT PGM.product_group_name,PM.product_group_code,PSGM.product_sub_group_name,PBM.product_brand_name,PM.prod_desc,PM.dns_prod_code
											FROM  product_master PM,product_group_master PGM,product_sub_group_master PSGM,product_brand_master PBM
											WHERE PM.product_group_code=PGM.product_group_code AND PM.product_sub_group_code=PSGM.product_sub_group_code
											AND PM.product_brand_code=PBM.product_brand_code AND PM.prod_code='".$orderdatadetails_sku_code."'";
					}
					$rsproductdetails=mysqli_query($link,$sqlproductdetails);
					$rowproductdetails=mysqli_fetch_assoc($rsproductdetails);
					$prod_desc=$rowproductdetails['prod_desc'];
					$product_group_name=$rowproductdetails['product_group_name'];
					$product_group_code=$rowproductdetails['product_group_code'];
					$product_brand_name=$rowproductdetails['product_brand_name'];
					$product_sub_group_name=$rowproductdetails['product_sub_group_name'];
					$UOM1=$rowproductdetails['UOM1'];
					$UOM2=$rowproductdetails['UOM2'];
					$conversion_factor=$rowproductdetails['conversion_factor'];
					$dns_prod_code=$rowproductdetails['dns_prod_code'];

					if(sale=='yes' && delete_transaction=='yes')
					{
						$sqlinsert_condition=" transaction_type ='".${orderdataheader_transaction_type.$orderdatadetails_order_no}."',";
					}
					else
					{
						$sqlinsert_condition="";
					}

					//print_r($order_array_trans_id);
					if(!in_array($orderdatadetails_order_no,$order_array_trans_id))
					{

						$sqlinsertorderdetails="INSERT INTO order_details SET order_no='".$orderdatadetails_order_no."',
												sku_code 	='".$orderdatadetails_sku_code."',
												qty			='".$orderdatadetails_qty."',
												TD			='".$orderdatadetails_TD."',
												premium		='".$orderdatadetails_premium."',
												sale_rate	='".$orderdatadetails_sale_rate."',
												VAT			='".$orderdatadetails_VAT."',
												UOM			='".$orderdatadetails_UOM."',
												amount		='".$orderdatadetails_amount."', ".$sqlinsert_condition."
												mrp_code	='".$orderdatadetails_mrp_code."'";
						if(mysqli_query($link,$sqlinsertorderdetails))
						{
							$flag=5;
							$customer_name=mysqli_fetch_assoc(mysqli_query($link,"select customer_name from customer_master where customer_code='".$orderdataheader_customer_code."'"));
							$branch_name=mysqli_fetch_assoc(mysqli_query($link,"select branch_name from branch_master where branch_code='".$branch_code."'"));
              if(mrp=='yes'){
							$sale_rate_db=mysqli_fetch_assoc(mysqli_query($link,"select mrp from mrp where branch_code='".$branch_code."' and product_code='".$orderdatadetails_sku_code."'"));
              $exprice=$sale_rate_db['mrp'];
							}
							else{
								$sale_rate_db=mysqli_fetch_assoc(mysqli_query($link,"select sale_rate from mrp where branch_code='".$branch_code."' and product_code='".$orderdatadetails_sku_code."'"));
                $exprice=$sale_rate_db['sale_rate'];
							}
							$sql_pricevalidation="INSERT INTO price_validation_details SET order_no='".$orderdatadetails_order_no."',
											customer_code			='".$orderdataheader_customer_code."',
											customer_name			='".addslashes($customer_name['customer_name'])."',
											depot_code		='".$branch_code."',
											depot_name	='".$branch_name['branch_name']."',
											prod_code		='".$orderdatadetails_sku_code."',
											prod_desc			='".$prod_desc."',
											product_group_code		='".$product_group_code."',
				  						product_group_name='".$product_group_name."',
											qty='".$orderdatadetails_qty."',
											existing_price='".$exprice."',
											input_price='".$orderdatadetails_sale_rate."',
											authorized_emp_code='".$authorized_emp_code."',
											download_time	='".$location_date."'";
							 mysqli_query($link,$sql_pricevalidation);

							// For stock transfer and stock rceive push the value in goods_in_transit table
							if(${orderdataheader_transaction_type.$orderdatadetails_order_no}=='BT' || ${orderdataheader_transaction_type.$orderdatadetails_order_no}=='ST' || ${orderdataheader_transaction_type.$orderdatadetails_order_no}=='SA')
							{
								if(${orderdataheader_transaction_type.$orderdatadetails_order_no}=='ST')
								{
									$grn_no_goods_in_transit=$orderdatadetails_order_no;
									$order_no_goods_in_transit='';
									$despatch_qty=$orderdatadetails_qty;
									$rec_qty=0;
									$orderdatadetails_sale_rate=$orderdatadetails_sale_rate;
									$despatcher_code=$order_emp_code;
									$receiver_code=${stock_receive_emp_code.$orderdatadetails_order_no};
								}

								if(${orderdataheader_transaction_type.$orderdatadetails_order_no}=='BT' || ${orderdataheader_transaction_type.$orderdatadetails_order_no}=='SA')
								{
									$grn_no_goods_in_transit=${orderdataheader_grn_no.$orderdatadetails_order_no};
									$order_no_goods_in_transit=$orderdatadetails_order_no;

									//For selection of despatch qty for the grn_no
									$sqlquerygit="SELECT despatch_qty,sale_rate,despatcher_code,receiver_code FROM goods_in_transit WHERE
												grn_no='".$grn_no_goods_in_transit."' AND prod_code='".$orderdatadetails_sku_code."' AND
												transaction_type='ST'";
									$rsquerygit=mysqli_query($link,$sqlquerygit);
									$rowquerygit=mysqli_fetch_assoc($rsquerygit);
									$despatch_qty=$rowquerygit['despatch_qty'];
									$sale_rate=$rowquerygit['sale_rate'];
									$rec_qty=$orderdatadetails_qty;
									$orderdatadetails_sale_rate=$sale_rate;
									$despatcher_code=$rowquerygit['despatcher_code'];
									$receiver_code=$rowquerygit['receiver_code'];
								}
								//Insertion of goods_in_transit table
								$sqlinsertgoodsintransit="INSERT INTO goods_in_transit SET grn_no='".$grn_no_goods_in_transit."',
														despatcher_code ='".$despatcher_code."',
														receiver_code	='".$receiver_code."',
														prod_code		='".$orderdatadetails_sku_code."',
														despatch_qty	='".$despatch_qty."',
														rec_qty			='".$rec_qty."',
														sale_rate		='".$orderdatadetails_sale_rate."',
														status			='0',
														transaction_type ='".${orderdataheader_transaction_type.$orderdatadetails_order_no}."',
														download_time=CURRENT_TIMESTAMP(),
														order_no='".$order_no_goods_in_transit."'";
								if(mysqli_query($link,$sqlinsertgoodsintransit))
								{
									$flag=5;
									//Updating status to complete of all the transactions of the particular grn_no if despatch qty=Sum of all the receive qty
									$sqlqueryrecgit="SELECT SUM(rec_qty) AS total_rec_qty FROM goods_in_transit WHERE
												grn_no='".$grn_no_goods_in_transit."' AND prod_code='".$orderdatadetails_sku_code."' AND
												transaction_type='BT'";
									$rsqueryrecgit=mysqli_query($link,$sqlqueryrecgit);
									$rowqueryrecgit=mysqli_fetch_assoc($rsqueryrecgit);
									$total_rec_qty=$rowqueryrecgit['total_rec_qty'];
									if($despatch_qty==$total_rec_qty)
									{
										$sqlupdategitstatus="UPDATE goods_in_transit SET status='1'
										WHERE grn_no='".$grn_no_goods_in_transit."' AND prod_code='".$orderdatadetails_sku_code."'";
										if(mysqli_query($link,$sqlupdategitstatus))
										{
											$flag=5;
										}
										else
										{
											mysqli_query($link,"ROLLBACK");
											echo $flag=0;
											return;
										}
									}
									//End of updating status
								 }
								else
								{
									mysqli_query($link,"ROLLBACK");
									echo $flag=0;
									return;
								}
							}
							//Start for STAR mis data details order qty updation
							if($nick_name=='STAR')
							{
								$qty=$orderdatadetails_qty;
								$trans_type='O';
								$trans_sub_type='';
								update_transaction_STAR(substr($orderdatadetails_order_no,1,5),$orderdatadetails_order_no,$qty,$trans_type,$trans_sub_type);
							}
							if($nick_name=='RUPA')
							{
								$qty=$orderdatadetails_qty;
								$order_UOM=$orderdatadetails_UOM;
							 update_ach_RUPA(substr($orderdatadetails_order_no,1,5),$orderdatadetails_order_no,$order_UOM,$qty,$UOM1,$UOM2,$conversion_factor);
							}


					  }

						else
						{
							mysqli_query($link,"ROLLBACK");
							echo $flag=0;
							return;
						}
						//For constructing the email body for ORDER DETAILS if ORDER has performed

						if(no_of_filter==1){
							$product_details_TD="<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$prod_desc."</span>&nbsp;</td>	";
						}
						if(no_of_filter==2){
							$product_details_TD="<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$product_group_name."</span>&nbsp;</td>
											<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$prod_desc."</span>&nbsp;</td>	";
						}
						if(no_of_filter==3){
							$product_details_TD="<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$product_group_name."</span>&nbsp;</td>
											<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$product_sub_group_name."</span>&nbsp;</td>
											<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$prod_desc."</span>&nbsp;</td>	";
						}
						if(no_of_filter==4){
							$product_details_TD="<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$product_group_name."</span>&nbsp;</td>
											<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$product_sub_group_name."</span>&nbsp;</td>
											<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$product_brand_name."</span>&nbsp;</td>
											<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$prod_desc."</span>&nbsp;</td>	";
						}
						if(providing_code=='yes'){
							$product_details_TD.="<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$dns_prod_code."</span>&nbsp;</td>";
						}
						if(premium=='yes' )
						{
							${premiun.$orderdatadetails_sku_code}=$orderdatadetails_premium*$orderdatadetails_qty;
						}
						else
						{
							${premiun.$orderdatadetails_sku_code}=0;
						}
						if(mrp=='yes' && TD=='yes' && TD_type=='sku wise'){
							if(TD_calc=='percentage')
							{
								$totalmrp=($orderdatadetails_qty*$mrp)-((($orderdatadetails_qty*$mrp)*$orderdatadetails_TD)/100);
							}
							else
							{
								//$totalmrp=($orderdatadetails_qty*$mrp)-$orderdatadetails_TD;
								$totalmrp=$orderdatadetails_qty*($mrp-$orderdatadetails_TD);
							}
							${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$totalmrp+${premiun.$orderdatadetails_sku_code};
							${totalamount.$orderdatadetails_sku_code}=$totalmrp+${premiun.$orderdatadetails_sku_code};
						 }
						 if(mrp=='yes' && TD=='no'){
							$totalmrp=($orderdatadetails_qty*$mrp);
							${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$totalmrp+${premiun.$orderdatadetails_sku_code};
							${totalamount.$orderdatadetails_sku_code}=$totalmrp+${premiun.$orderdatadetails_sku_code};
						 }
						 if(sale_rate=='yes' && TD=='yes' && TD_type=='sku wise'){
							 if(TD_calc=='percentage')
							 {
							 	$totalrate=($orderdatadetails_qty*$orderdatadetails_sale_rate)-((($orderdatadetails_qty*$orderdatadetails_sale_rate)*$orderdatadetails_TD)/100);
							 }
							 else
							 {
								//$totalrate=($orderdatadetails_qty*$orderdatadetails_sale_rate)-$orderdatadetails_TD;
								$totalrate=$orderdatadetails_qty*($orderdatadetails_sale_rate-$orderdatadetails_TD);
							 }
							${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$totalrate+${premiun.$orderdatadetails_sku_code};
							${totalamount.$orderdatadetails_sku_code}=$totalrate+${premiun.$orderdatadetails_sku_code};
						 }
						 if(sale_rate=='yes' && TD=='no'){
							$totalrate=($orderdatadetails_qty*$orderdatadetails_sale_rate);
							${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$totalrate+${premiun.$orderdatadetails_sku_code};
							${totalamount.$orderdatadetails_sku_code}=$totalrate+${premiun.$orderdatadetails_sku_code};
						 }
						 if(mrp=='yes' && TD=='yes' && (TD_type=='order value wise' || TD_type=='customer wise')){
							//if($nick_name=='SKIPPER')
							if(multiple_UOM=='yes')
							{
								$totalmrp=$orderdatadetails_amount;
							}
							else
							{
								$totalmrp=($orderdatadetails_qty*$mrp);
							}
							${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$totalmrp+${premiun.$orderdatadetails_sku_code};
							${totalamount.$orderdatadetails_sku_code}=$totalmrp+${premiun.$orderdatadetails_sku_code};
						 }
						 if(sale_rate=='yes' && TD=='yes' && (TD_type=='order value wise' || TD_type=='customer wise')){
							$totalrate=($orderdatadetails_qty*$orderdatadetails_sale_rate);
							${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$totalrate+${premiun.$orderdatadetails_sku_code};
							${totalamount.$orderdatadetails_sku_code}=$totalrate+${premiun.$orderdatadetails_sku_code};
						 }

						 if(VAT=='yes' && VAT_details=='amount')
						 {
							 ${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$orderdatadetails_VAT;
						 }
						 else if(VAT=='yes' && VAT_details=='percentage')
						 {
							 $vat_total=(${grandtotal.$orderdatadetails_order_no}*$orderdatadetails_VAT/100);
							 ${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$vat_total;
						 }

						 if(TD=='yes' && TD_type=='sku wise')
						 {
							$orderemailbody_TD_VAL="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".number_format($orderdatadetails_TD,2)."</span>&nbsp;</td>";
						 }
						 else
						 {
							 $orderemailbody_TD_VAL="";
						 }
						 if(premium=='yes' )
						 {
							 $orderemailbody_premium_VAL="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".number_format($orderdatadetails_premium,2)."</span>&nbsp;</td>";
						 }
						 else
						 {
							$orderemailbody_premium_VAL=="";
						 }
						 if(VAT=='yes')
						 {
							$orderemailbody_VAT_VAL="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>".number_format(round($orderdatadetails_VAT,2),2)."</span>&nbsp;</td>";
						 }
						 else
						 {
							 $orderemailbody_VAT_VAL="";
						 }

						 if($orderdatadetails_sale_rate=='')
						 {
							$orderdatadetails_sale_rate=0;
						 }
						 if(${orderdataheader_transaction_type.$orderdatadetails_order_no}=='BT')
						 {
							$orderdatadetails_despatch_qty="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".number_format($despatch_qty,2)."</span>&nbsp;</td>";
						 }
						 else
						 {
							$orderdatadetails_despatch_qty='';
						 }
						 if(amount=='yes')
						 {
							 if($orderdatadetails_amount >0)
							 {
							 	$amount_mail=number_format(round($orderdatadetails_amount,2),2);
							 }
							 else
							 {
								 $amount_mail=number_format(round(${totalamount.$orderdatadetails_sku_code},2),2);
							 }
							 if($orderdatadetails_sale_rate >0)
							 {
								 $sale_rate_mail=number_format(round($orderdatadetails_sale_rate,4),2);
							 }
							 else
							 {
								 $sale_rate_mail=number_format(round($orderdatadetails_sale_rate,4),2);
							 }
						 }
						 else
						 {
							 $amount_mail=number_format(${totalamount.$orderdatadetails_sku_code},2);
							 $sale_rate_mail=number_format(round($orderdatadetails_sale_rate,4),2);
						 }
						 if(strpos($orderdatadetails_qty,'.')!=false){
							 $orderdatadetails_qty_mail=$orderdatadetails_qty;
						 }
						 else
						 {
							 $orderdatadetails_qty_mail=number_format($orderdatadetails_qty,2);
						 }

						//For total value
						 ${orderdatadetails_VAT_total.$orderdatadetails_order_no}=${orderdatadetails_VAT_total.$orderdatadetails_order_no}+$orderdatadetails_VAT;
						 ${orderdatadetails_qty_total.$orderdatadetails_order_no}=${orderdatadetails_qty_total.$orderdatadetails_order_no}+$orderdatadetails_qty;
						 if(${orderdataheader_transaction_type.$orderdatadetails_order_no}=='BT')
						 {
							 ${orderdatadetails_despatchqty_total.$orderdatadetails_order_no}=${orderdatadetails_despatchqty_total.$orderdatadetails_order_no}+$despatch_qty;
						 }
						/*if($orderdatadetails_amount >0)
						 {
							${orderdatadetails_amount_total.$orderdatadetails_order_no}=${orderdatadetails_amount_total.$orderdatadetails_order_no}+${totalamount.$orderdatadetails_sku_code};
						 }
						 else
						 {*/
							${orderdatadetails_amount_total.$orderdatadetails_order_no}=${orderdatadetails_amount_total.$orderdatadetails_order_no}+${totalamount.$orderdatadetails_sku_code};
						 //}
							 if(mrp=='yes')
							 {
								//if($nick_name=='SKIPPER')
								if(multiple_UOM=='yes')
								{
									$mrp_UOM_wise=$orderdatadetails_amount/$orderdatadetails_qty;
									$amount_mail=number_format($orderdatadetails_amount,2);
									$mrp_sale_rate_TD="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".number_format($mrp_UOM_wise,2)."</span>&nbsp;</td>";
								}
								else
								{
								$mrp_sale_rate_TD="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".number_format($mrp,2)."</span>&nbsp;</td>";
								}
							 }
							 else if(sale_rate=='yes' && sale_rate_input_dropdown=='input')
							 {
								 $mrp_sale_rate_TD="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$sale_rate_mail."</span>&nbsp;</td>";
							 }
							 else if(sale_rate=='yes' && sale_rate_input_dropdown=='dropdown')
							 {
								if($nick_name=='DNV' || $nick_name=='HALDIRAM')
								{
									$mrp_sale_rate_TD="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$orderdatadetails_sale_rate."</span>&nbsp;</td>";
								}
								else
								{
								 $mrp_sale_rate_TD="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".number_format($sale_rate_db,2)."</span>&nbsp;</td>";
								}
							 }

						${a.$orderdatadetails_order_no} .="
								<tr>".$product_details_TD.$orderdatadetails_despatch_qty."
								<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".number_format($orderdatadetails_qty_mail,2)."</span>&nbsp;</td>
								<td style='width:60px;text-align:left;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".strtoupper($orderdatadetails_UOM)."</span>&nbsp;</td>
								".$mrp_sale_rate_TD.$orderemailbody_TD_VAL.$orderemailbody_premium_VAL."
								<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$amount_mail."</span>&nbsp;</td>".$orderemailbody_VAT_VAL."
								</tr>";
						if($nick_name=='KARMA')
						{
							$sqlfetchGST="SELECT SGST,CGST,discount_code,tax_code,IGST FROM prodqty_custclass_wise_TD WHERE
											branch_code='".${branch_code_GST.$orderdatadetails_order_no}."' AND prod_code='".$orderdatadetails_sku_code."'";
							$rsfetchGST=mysqli_query($link,$sqlfetchGST);
							$rowfetchGST=mysqli_fetch_assoc($rsfetchGST);
							$SGST=$rowfetchGST['SGST'];
							$CGST=$rowfetchGST['CGST'];
							$IGST=$rowfetchGST['IGST'];

							$rate_discount=($orderdatadetails_sale_rate-(($orderdatadetails_sale_rate*$orderdatadetails_TD)/100));
							${xml_order_details.$orderdatadetails_order_no}.='<ent:DMS_SOCreateAuthorize_ML_input><ent:So_costcenter>'.${cost_center.$orderdatadetails_order_no}.'</ent:So_costcenter><ent:So_deliverydate>2016-06-02T00:00:00Z</ent:So_deliverydate><ent:So_itemcode>'.$dns_prod_code.'</ent:So_itemcode><ent:So_itemdesc></ent:So_itemdesc><ent:So_lineno>'.${So_lineno.$orderdatadetails_order_no}.'</ent:So_lineno><ent:So_quantity>'.$orderdatadetails_qty.'</ent:So_quantity><ent:So_rate>'.$rate_discount.'</ent:So_rate><ent:So_requireddate>'.${So_orderdate.$orderdatadetails_order_no}.'</ent:So_requireddate><ent:So_shippingat></ent:So_shippingat><ent:So_warehouse></ent:So_warehouse><ent:Soca_uom></ent:Soca_uom></ent:DMS_SOCreateAuthorize_ML_input>';
							if(${order_GST_type.$orderdatadetails_order_no}=='CGST/SGST')
							{
								$GST_val_CGST=$CGST;
								$GST_val_SGST=$SGST;
							${xml_order_details_TD.$orderdatadetails_order_no}.='<ent:DMS_SOCreateAuthorize_TCDML_input><ent:So_itemlineno>'.${So_lineno.$orderdatadetails_order_no}.'</ent:So_itemlineno><ent:So_tcdcharge>0.00</ent:So_tcdcharge><ent:So_tcdcode>'.$GST_val_SGST.'</ent:So_tcdcode> <ent:So_tcdcostcentre>'.${cost_center.$orderdatadetails_order_no}.'</ent:So_tcdcostcentre><ent:So_tcdtype>Line</ent:So_tcdtype><ent:So_tcdvalue>'.$orderdatadetails_TD.'</ent:So_tcdvalue></ent:DMS_SOCreateAuthorize_TCDML_input>';
							${xml_order_details_TD.$orderdatadetails_order_no}.='<ent:DMS_SOCreateAuthorize_TCDML_input><ent:So_itemlineno>'.${So_lineno.$orderdatadetails_order_no}.'</ent:So_itemlineno><ent:So_tcdcharge>0.00</ent:So_tcdcharge><ent:So_tcdcode>'.$GST_val_CGST.'</ent:So_tcdcode> <ent:So_tcdcostcentre>'.${cost_center.$orderdatadetails_order_no}.'</ent:So_tcdcostcentre><ent:So_tcdtype>Line</ent:So_tcdtype><ent:So_tcdvalue>'.$orderdatadetails_TD.'</ent:So_tcdvalue></ent:DMS_SOCreateAuthorize_TCDML_input>';
							}
							if(${order_GST_type.$orderdatadetails_order_no}=='IGST')
							{
							 $GST_val_IGST=$IGST;
							${xml_order_details_TD.$orderdatadetails_order_no}.='<ent:DMS_SOCreateAuthorize_TCDML_input><ent:So_itemlineno>'.${So_lineno.$orderdatadetails_order_no}.'</ent:So_itemlineno><ent:So_tcdcharge>0.00</ent:So_tcdcharge><ent:So_tcdcode>'.$GST_val_IGST.'</ent:So_tcdcode><ent:So_tcdcostcentre>'.${cost_center.$orderdatadetails_order_no}.'</ent:So_tcdcostcentre><ent:So_tcdtype>Line</ent:So_tcdtype><ent:So_tcdvalue>'.$orderdatadetails_TD.'</ent:So_tcdvalue></ent:DMS_SOCreateAuthorize_TCDML_input>';
							}
							${So_lineno.$orderdatadetails_order_no}=${So_lineno.$orderdatadetails_order_no}+1;
						}
					}

			}//End for loop
		}
		//End Insert into the Order Details table for new trans id
		//For sending email for order
		//echo $orderdatadetails_order_no;
		if(count($orderheader_array_mailbody)>0)
		{
			for($countarr=0;$countarr<count($orderheader_array_mailbody);$countarr++)
			{
				if($orderpricevalidation_array_mailbody[$countarr]=='SPA'){
						$notification_id='PN'.$emp_code.'KARMA'.$location_date;
						$notification_type='Individual';

						$apiKey='AAAAcwFa5lA:APA91bFLXdUw4ZQCrPcLce7xsI33eVlv2C9I1qgARtQMfESSvdEHd0wJ2vmSi63YVZQsTYCaJBDT1wK5j3y8OUATVaaSqL6Q6OpyYeKhIz9z-F25Rq-4qm9KlQ26f4WCteFcjwXaEAkS';
						$collapseKey=rand();
						 $title = "";
						$message='Order booked by '.$emp_name.'. Please validate the Price.';

						//This array contains, the token and the notification. The 'to' attribute stores the token.
						$data= array('sauda_no' =>$ordertansid_array_mailbody[$countarr],'notification_id' =>$notification_id, 'notification_type' => $notification_type, 'sender_id' => 'KARMA', 'body' => $message);
						//$arrayToSend = array('to' => $registrationid, 'notification' => $notification, 'data'=>$data);
						$arrayToSend = array('to' => $authorized_emp_registrationid, 'data'=>$data);

						// Set POST variables
						$url = 'https://fcm.googleapis.com/fcm/send';
						$headers = array(
							'Authorization: key=' . $apiKey,
							'Content-Type: application/json'
						);
						// Open connection
						$ch = curl_init();

						// Set the url, number of POST vars, POST data
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

						// Disabling SSL Certificate support temporarly
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

						curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayToSend));

						// Execute post
						$result = curl_exec($ch);
						$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
						if($httpCode==200)
						{
							$sqlnotificationmaster  = "INSERT INTO notification_master ";
							$sqlnotificationmaster .= " SET notification_id='".$notification_id."'";
							$sqlnotificationmaster .= " ,type_of_notification='".$notification_type."'";
							$sqlnotificationmaster .= " ,sender_id='KARMA'";
							$sqlnotificationmaster .= " ,message='".$message."'";
							$sqlnotificationmaster .= " ,transferred='YES'";
							if(mysqli_query($link,$sqlnotificationmaster))
							{
								$sqlnotification  = "INSERT INTO notification_ack_relation ";
								$sqlnotification .= " SET notification_id='".$notification_id."'";
								$sqlnotification .= " ,receiver_id='".$authorized_emp_code."'";
								mysqli_query($link,$sqlnotification);
							}
						}
				 }

				if(TD=='yes' && TD_type=='order value wise')
				{
					if(TD_calc=='percentage')
					{
						$TD_order_val="<br /><table>TD on order value: <b>".number_format($orderdataheader_TD_array[$countarr],2)."%</b></table>";
					}
					else
					{
						$TD_order_val="<br /><table>TD on order value: <b>".number_format($orderdataheader_TD_array[$countarr],2)."</b></table>";
					}
				}
				if(TD=='yes' && TD_type=='customer wise')
				{
					if(TD_calc=='percentage')
					{
						$TD_order_val="<br /><table>TD applicable for this customer: <b>".$orderdataheader_TD_array[$countarr]."%</b></table>";
					}
					else
					{
						$TD_order_val="<br /><table>TD applicable for this customer: <b>".$orderdataheader_TD_array[$countarr]."</b></table>";
					}
				}
				if(TD=='yes' && (TD_type=='order value wise' || TD_type=='customer wise')){

					if(TD_calc=='percentage')
					{
						$total_order_amount=(${grandtotal.$ordertansid_array_mailbody[$countarr]}-(${grandtotal.$ordertansid_array_mailbody[$countarr]}*$orderdataheader_TD_array[$countarr])/100);
					}
					else
					{
						$total_order_amount=(${grandtotal.$ordertansid_array_mailbody[$countarr]}- $orderdataheader_TD_array[$countarr]);
					}
				}
				else
				{
					$total_order_amount=${grandtotal.$ordertansid_array_mailbody[$countarr]};
				}
				if($orderdataheader_sale_type_array[$countarr]=='CREDIT' || $orderdataheader_sale_type_array[$countarr]=='COD'){
					if(($orderdataheader_sale_type_array[$countarr]=='CREDIT' || $orderdataheader_sale_type_array[$countarr]=='COD') && $orderdataheader_transaction_type_array[$countarr]!='SB'){
					$orderemailsubj="$nick_name - Order received by ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';
					}
					else if(($orderdataheader_sale_type_array[$countarr]=='CREDIT' || $orderdataheader_sale_type_array[$countarr]=='COD') && $orderdataheader_transaction_type_array[$countarr]=='SB')
					{
					$orderemailsubj="Sale bill generated by ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';

					}
				$ordercashreceivedemailbody='';
				}
				else if($orderdataheader_sale_type_array[$countarr]=='CASH')
				{
					if($orderdataheader_sale_type_array[$countarr]=='CASH'  && $orderdataheader_transaction_type_array[$countarr]!='SB'){
					$orderemailsubj="$nick_name - Cash sale by ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';
					$ordercashreceivedemailbody="<br /><table>Received Cash: <b>Rs.".number_format($total_order_amount,2)."/-</b></table><br />";
					}
					if($orderdataheader_sale_type_array[$countarr]=='CASH'  && $orderdataheader_transaction_type_array[$countarr]=='SB'){
					$orderemailsubj="Sale bill generated by ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';
					$ordercashreceivedemailbody="";
					}
				}
				//For sales feature
				else if($orderdataheader_transaction_type_array[$countarr]=='BT' && $orderdataheader_sale_type_array[$countarr]==''){
				$orderemailsubj="Stock received by ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';
				$total_order_val="<br /><table>Stock received value: <b>Rs. ".number_format($total_order_amount,2)."/-</b></table>";

				$ordercashreceivedemailbody='';
				}
				else if($orderdataheader_transaction_type_array[$countarr]=='PB' && $orderdataheader_sale_type_array[$countarr]==''){
				$orderemailsubj="Purchase bill received by ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';
				$total_order_val="<br /><table>Purchase value: <b>Rs. ".number_format($total_order_amount,2)."/-</b></table>";
				$ordercashreceivedemailbody='';
				}
				else if($orderdataheader_transaction_type_array[$countarr]=='ST' && $orderdataheader_sale_type_array[$countarr]==''){
				$orderemailsubj="Stock transfer to ".$orderdataheader_entity_name_array[$countarr]." by ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';
				$total_order_val="<br /><table>Stock  transfer value: <b>Rs. ".number_format($total_order_amount,2)."/-</b></table>";
				$ordercashreceivedemailbody='';
				}
				else if($orderdataheader_transaction_type_array[$countarr]=='CN' && $orderdataheader_sale_type_array[$countarr]==''){
				$orderemailsubj="Stock handed over to ".$orderdataheader_entity_name_array[$countarr]." by ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';
				$total_order_val="<br /><table>Carry in stock out value: <b>Rs. ".number_format($total_order_amount,2)."/-</b></table>";
				$ordercashreceivedemailbody='';

				}
				else if($orderdataheader_transaction_type_array[$countarr]=='SR' && $orderdataheader_sale_type_array[$countarr]==''){
				$orderemailsubj="Stock return to ".$orderdataheader_entity_name_array[$countarr]." by ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';
				$total_order_val="<br /><table>Stock return value: <b>Rs. ".number_format($total_order_amount,2)."/-</b></table>";
				$ordercashreceivedemailbody='';
				}
				else if($orderdataheader_transaction_type_array[$countarr]=='CR' && $orderdataheader_sale_type_array[$countarr]==''){
				$orderemailsubj="Stock return from ".$orderdataheader_entity_name_array[$countarr]." by ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';
				$total_order_val="<br /><table>Carry in return value: <b>Rs. ".number_format($total_order_amount,2)."/-</b></table>";
				$ordercashreceivedemailbody='';
				}
				else if(($orderdataheader_transaction_type_array[$countarr]=='SA' || $orderdataheader_transaction_type_array[$countarr]=='SH') && $orderdataheader_sale_type_array[$countarr]==''){
				$orderemailsubj="Material Shortage booked by ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';
				$total_order_val="<br /><table>Shortage value: <b>Rs. ".number_format($total_order_amount,2)."/-</b></table>";
				$ordercashreceivedemailbody='';
				}
				else if($orderdataheader_transaction_type_array[$countarr]=='RP' && $orderdataheader_sale_type_array[$countarr]==''){
				$orderemailsubj="$nick_name - Replacement request receipt from ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';
				$total_order_val="<br /><table>Replacement value: <b>Rs. ".number_format($total_order_amount,2)."/-</b></table>";
				$ordercashreceivedemailbody='';
				}
				//End for sales feature
				if($orderdataheader_sale_type_array[$countarr]=='CREDIT' || $orderdataheader_sale_type_array[$countarr]=='COD' || $orderdataheader_transaction_type_array[$countarr]=='SB'){
					if($orderdataheader_sale_type_array[$countarr]=='COD')
					{
						$COD_val='(CASH ON DELIVERY)';
					}
					else
					{
						$COD_val='';
					}
					if(($orderdataheader_sale_type_array[$countarr]=='CREDIT' || $orderdataheader_sale_type_array[$countarr]=='COD') && $orderdataheader_transaction_type_array[$countarr]!='SB'){
					$total_order_val="<br /><table>Total order value: <b>Rs. ".number_format($total_order_amount,2)."/- ".$COD_val."</b></table>";
					}
					else if(($orderdataheader_sale_type_array[$countarr]=='CREDIT' || $orderdataheader_sale_type_array[$countarr]=='COD' || $orderdataheader_sale_type_array[$countarr]=='CASH' ) && $orderdataheader_transaction_type_array[$countarr]=='SB')
					{
						$total_order_val="<br /><table>Total sale value: <b>Rs. ".number_format($total_order_amount,2)."/- ".$COD_val."</b></table>";
					}
					else if($orderdataheader_sale_type_array[$countarr]=='CASH' && $orderdataheader_transaction_type_array[$countarr]!='SB')
					{
						$total_order_val='';
					}
				}
				if(no_of_filter==1){
					$product_details_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col4."</span></strong></th>";

				}
				if(no_of_filter==2){
					$product_details_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col1."</span></strong></th>
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col4."</span></strong></th>";
				}
				if(no_of_filter==3){
					$product_details_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col1."</span></strong></th>
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col2."</span></strong></th>
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col4."</span></strong></th>";
				}
				if(no_of_filter==4){
					$product_details_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col1."</span></strong></th>
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col2."</span></strong></th>
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col3."</span></strong></th>
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col4."</span></strong></th>";
				}
				if(providing_code=='yes')
				{
					$product_details_TH.="<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>SKU CODE</span></strong></th>";
					$product_details_colspan=no_of_filter+1;
				}
				else
				{
					$product_details_colspan=no_of_filter;
				}

				if(TD=='yes' && TD_type=='sku wise')
				{
					$orderemailbody_TD_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>TD</span></strong></th>";
					$orderemailbody_TD_total="<td style='width:60px;min-height:21px;text-align:right'><strong>
							<span style='font-size:10pt;font-family:Arial CE'></span></strong></td>";
				}
				else
				{
					$orderemailbody_TD_TH="";
					$orderemailbody_TD_total="";
				}
				if(premium=='yes')
				{
					$order_premiun_TR="<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>Premium</span></strong></th>";
					$order_premium_total="<td style='width:60px;min-height:21px;text-align:right'><strong>
							<span style='font-size:10pt;font-family:Arial CE'></span></strong></td>";
				}
				else
				{
					$order_premiun_TR='';
					$order_premium_total='';
				}

				if(VAT=='yes')
				{
					$orderemailbody_VAT_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>VAT</span></strong></th>";
					$orderemailbody_VAT_total="<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>".number_format(round(${orderdatadetails_VAT_total.$ordertansid_array_mailbody[$countarr]},2),2)."</span></strong></td>";
				}
				else
				{
					$orderemailbody_VAT_TH="";
					$orderemailbody_VAT_total="";
				}

				if($orderdataheader_transaction_type_array[$countarr]=='PB' && sale_rate=='yes')
				{
					$mrp_sale_rate_TR="<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>Purchase Rate</span></strong></th>";
				}
				else if($orderdataheader_transaction_type_array[$countarr]!='PB' && sale_rate=='yes')
				{
					$mrp_sale_rate_TR="<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>Sale Rate</span></strong></th>";
				}
				else if(mrp=='yes')
				{
					$mrp_sale_rate_TR="<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>MRP</span></strong></th>";
				}
				else
				{
					$mrp_sale_rate_TR='';
				}
				if($orderdataheader_transaction_type_array[$countarr]=='BT')
				{
					$qty_TR="<th style='width:60px;min-height:21px;text-align:left'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>Despatch Qty</span></strong></th>
							<th style='width:60px;min-height:21px;text-align:left'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>Received Qty</span></strong></th>
							";
					$despatch_qty_total_TD="<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>".number_format(${orderdatadetails_despatchqty_total.$ordertansid_array_mailbody[$countarr]},2)."</span></strong></td>";
				}
				else if($orderdataheader_transaction_type_array[$countarr]=='ST')
				{
					$qty_TR="<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>Despatch Qty</span></strong></th>";
					$despatch_qty_total_TD='';
				}
				else
				{
					$qty_TR="<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>Qty</span></strong></th>";
					$despatch_qty_total_TD='';
				}
				 if(strpos(${orderdatadetails_qty_total.$ordertansid_array_mailbody[$countarr]},'.')!=false){
					 ${orderdatadetails_qty_total.$ordertansid_array_mailbody[$countarr]}=${orderdatadetails_qty_total.$ordertansid_array_mailbody[$countarr]};
				 }
				 else
				 {
					 ${orderdatadetails_qty_total.$ordertansid_array_mailbody[$countarr]}=number_format(${orderdatadetails_qty_total.$ordertansid_array_mailbody[$countarr]},2);
				 }

				 //KARMA soap calling
				 if($nick_name=='KARMA')
				 {
						  $xml_post_string = ${xml_order_header.$ordertansid_array_mailbody[$countarr]}.'<ent:dms_socreateauthorize_ml>'.${xml_order_details.$ordertansid_array_mailbody[$countarr]}.'</ent:dms_socreateauthorize_ml><ent:dms_socreateauthorize_tcdml>'.${xml_order_details_TD.$ordertansid_array_mailbody[$countarr]}.'</ent:dms_socreateauthorize_tcdml></ent:sOCreate_Authorize_OP></soap:Body></soap:Envelope>';
						  $sqlinsertsoapresponse="INSERT INTO soap_request_response_details SET
						  							request_xml='".$xml_post_string."',
													emp_code='".$emp_code."',
													order_no='".$ordertansid_array_mailbody[$countarr]."',
													download_time=CURRENT_TIMESTAMP()";
							if(mysqli_query($link,$sqlinsertsoapresponse))
								{
									$flag=5;
								}
								else
								{
									mysqli_query($link,"ROLLBACK");
									echo $flag=0;
									return;
								}
					}

				 //Order approval process checking
				$headers  = "MIME-Version: 1.0\r\n";
				$headers .= "Content-type: text/html; charset=UTF-8\n";
				$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
							"Bcc: ".BCCEMAIL." \r\n" .
							'X-Mailer: PHP/' . phpversion();
				//$order_email=ORDEREMAILRECIPENTS;

				 if(order_approval_process=='no')
				 {
					$orderemailbody = $orderheader_array_mailbody[$countarr]."<br><table border=1 style=background-color:AliceBlue>
								<tr>".$product_details_TH.$qty_TR."
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>UOM</span></strong></th>
								".$mrp_sale_rate_TR.$orderemailbody_TD_TH.$order_premiun_TR."
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>Amount</span></strong></th>".$orderemailbody_VAT_TH."
								</tr>
					".${a.$ordertansid_array_mailbody[$countarr]}."<tr>
								<td style='min-height:21px;text-align:center' colspan='".$product_details_colspan."'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>Total</span></strong></td>".$despatch_qty_total_TD."
								<td style='width:50px;min-height:21px;text-align:right'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".${orderdatadetails_qty_total.$ordertansid_array_mailbody[$countarr]}."</span></strong></td>
								<td style='width:50px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'></span></strong></td>
								<td style='width:50px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'></span></strong></td>".$orderemailbody_TD_total.$order_premium_total."<th style='width:50px;min-height:21px;text-align:right'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".number_format(round(${orderdatadetails_amount_total.$ordertansid_array_mailbody[$countarr]},2),2)."</span></strong></th>".$orderemailbody_VAT_total."
								</tr></table>
					".$TD_order_val.$ordercashreceivedemailbody.$total_order_val."
					<br /><br /><table>".$orderinstruction_array_mailbody[$countarr]."</table><br /><br />".$order_approval_mail_body."
					<br /><br /><br />Powered By aceDNS<br /></body></html>";
					if(mail($order_email, $orderemailsubj, $orderemailbody, $headers,$spam_filter))
					{
						$flag=5;
					}
					else
					{
						mysqli_query($link,"ROLLBACK");
						echo $flag=0;
						return;
					}
				 }
				 else
				 {
					 $order_approval_emailsARR=ORDERAPPROVALEMAIL;
					 $order_emailARR=explode(',',$order_email);
					 foreach($order_emailARR as $order_email_values)
					 {
						 if(strstr($order_approval_emailsARR,$order_email_values)!=FALSE)
						 {
							${orderapprovalemailbody.$order_email_values}="<form name='orderapproval' action=".APICALLLOGURL."/update-order-approval.php?nick_name=".$nick_name."' method='POST'>
						<table wiidth='100%' border=1 style=background-color:AliceBlue>
						<tr>
						<th style='width:350px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial
						CE'>Order Approval</span></strong></th>
						</tr>
						<tr>
							<td style='width:350px;text-align:center;min-height:91px;background-color:white;padding-left:10 px;'>
								<input type='radio' name='approval' value='approved' /> Approved
								<input type='radio' name='approval' value='notapproved' /> Not Approved

								<input type='submit' name='Submit' value='save' />
								<input type='hidden' name='order_no' value='".$ordertansid_array_mailbody[$countarr]."' />
							</td>
						</tr>
						</table>
					</form>";
						 }
						 else
						 {
							 ${orderapprovalemailbody.$order_email_values}='';
						 }

						 ${orderemailbody.$order_email_values} = $orderheader_array_mailbody[$countarr]."<br><table border=1 style=background-color:AliceBlue>
								<tr>".$product_details_TH.$qty_TR."
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>UOM</span></strong></th>
								".$mrp_sale_rate_TR.$orderemailbody_TD_TH."
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>Amount</span></strong></th>".$orderemailbody_VAT_TH."
								</tr>
					".${a.$ordertansid_array_mailbody[$countarr]}."<tr>
								<td style='min-height:21px;text-align:center' colspan='".$product_details_colspan."'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>Total</span></strong></td>".$despatch_qty_total_TD."
								<td style='width:50px;min-height:21px;text-align:right'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".${orderdatadetails_qty_total.$ordertansid_array_mailbody[$countarr]}."</span></strong></td>
								<td style='width:50px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'></span></strong></td>
								<td style='width:50px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'></span></strong></td>".$orderemailbody_TD_total."<th style='width:50px;min-height:21px;text-align:right'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".number_format(round(${orderdatadetails_amount_total.$ordertansid_array_mailbody[$countarr]},2),2)."</span></strong></th>".$orderemailbody_VAT_total."
								</tr></table>
					".$TD_order_val.$ordercashreceivedemailbody.$total_order_val."
					<br /><br /><table>".$orderinstruction_array_mailbody[$countarr]."</table><br /><br />".${orderapprovalemailbody.$order_email_values}."
					<br /><br /><br />Powered By aceDNS<br /></body></html>";
						if(mail($order_email_values, $orderemailsubj,${orderemailbody.$order_email_values}, $headers,$spam_filter))
						{
							$flag=5;
						}
						else
						{
							mysqli_query($link,"ROLLBACK");
							echo $flag=0;
							return;
						}
					 }
				 }
			}
		}
}
 /* --------------------END QUERY FOR ORDER---------------------------------------------------------------------------------------------------------------*/

if($flag==5)
{
	 $sqlupdatelastoperationtime="UPDATE changepassword SET last_operation_datetime='".$last_operation_datetime."' WHERE emp_code='".$emp_code."'";
	 $rsupdatelastoperationtime=mysqli_query($link,$sqlupdatelastoperationtime);
	 mysqli_query($link,"COMMIT");
	 if($rsupdatelastoperationtime){

	 echo $flag=1;
   }
	 else{
		 echo $flag=0;
	 }

}
if($flag==6)
{
	$sqlupdatelastoperationtime="UPDATE changepassword SET last_operation_datetime='".$last_operation_datetime."' WHERE emp_code='".$emp_code."'";
	$rsupdatelastoperationtime=mysqli_query($link,$sqlupdatelastoperationtime);
	mysqli_query($link,"COMMIT");
  if($rsupdatelastoperationtime){

	echo $flag=1;
  }
	else{
		echo $flag=0;
	}
}
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = APICALLLOGURL."/operationdb-transaction-6.0.8.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&last_git_master_update_time=$last_git_master_update_time&last_loyalty_purchase_update_time=$last_loyalty_purchase_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
mysqli_close($link);
?>
