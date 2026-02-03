<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");
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

$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$result = mysqli_query($link,$sqlquery);
$countdatarefresh=mysqli_num_rows($result);
$body=file_get_contents('php://input');

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><attendance><location><emp_code>E0233</emp_code><trans_id>324343434343434344</trans_id>
<latt>25.00</latt><longi>25.00</longi></location><attendancedata><emp_code>E0233</emp_code></attendancedata></attendance></root>";

$body="<?xml version='1.0' encoding='UTF-8'?><root><order><location><emp_code>E0001</emp_code><trans_id>OE000120130119125128</trans_id><latt>22.5641</latt><longi>88.3569</longi><date>2013-01-19 13:11:28</date></location><orderdata><order_header><order_no>E000120130119125128</order_no><d_instruction>order</d_instruction><customer_code>C/0000080</customer_code></order_header><order_details><Order_no>E000120130119125128</Order_no><Sku_code>12194</Sku_code><qty>6.0</qty><mrp_code>0</mrp_code></order_details></orderdata></order></root>";*/
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><order><location><emp_code>E0002</emp_code><trans_id>OE000220140131130503</trans_id><latt>22.5643</latt><longi>88.3568</longi><date>2014-01-31 13:05:14</date></location><orderdata><order_header><order_no>OE000220140131130503</order_no><customer_name>ram das</customer_name><Phone_no>number</Phone_no><address>9876543210</address><pin_code>878787</pin_code><area></area><sale_type>CASH</sale_type><d_instruction><![CDATA[add customer check]]></d_instruction><customer_flag>N</customer_flag><customer_code>NE0002201401311320</customer_code></order_header><order_details><Order_no>OE000220140131130503</Order_no><Sku_code>12024</Sku_code><qty>55.0</qty><TD>10</TD><mrp_code>m24</mrp_code></order_details><order_details><Order_no>OE000220140131130503</Order_no><Sku_code>12005</Sku_code><qty>7.0</qty><TD>10</TD><mrp_code>m5</mrp_code></order_details></orderdata></order><payment><location><emp_code>E0002</emp_code><trans_id>PE000220140131130503</trans_id><latt>22.5643</latt><longi>88.3568</longi><date>2014-01-31 13:06:02</date></location><paymentdata><payment_header><receipt_id>PE000220140131130503</receipt_id><amount>14215.0</amount><cash_cheque>CASH</cash_cheque><cheque_no></cheque_no><date>2014-01-31</date><bank></bank><sale_type>CASH</sale_type><p_remark><![CDATA[add customer check]]></p_remark><customer_code>	</customer_code></payment_header><payment_details><Receipt_id>PE000220140131130503</Receipt_id><Invoice_id>RKBK/CS0602/13-14</Invoice_id><Recid></Recid><Amount>14215.0</Amount><Discount>0.0</Discount></payment_details></paymentdata></payment></root>";
$body="<?xml version='1.0' encoding='UTF-8'?><root><order><location><emp_code><![CDATA[C0007]]></emp_code><trans_id><![CDATA[OC000720140822161904]]></trans_id><latt><![CDATA[0.0]]></latt><longi><![CDATA[0.0]]></longi><date><![CDATA[2014-08-22 16:19:04]]></date></location><orderdata><order_header><order_no><![CDATA[OC000720140822161904]]></order_no><customer_name><![CDATA[New Cust]]></customer_name><Phone_no><![CDATA[2588744557]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[258842]]></pin_code><area><![CDATA[NRT/C000720140822161817]]></area><area_name><![CDATA[new route]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[COD]]></sale_type><order_value><![CDATA[0.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0000401]]></tag_distributor_code><cust_type><![CDATA[D]]></cust_type><customer_flag><![CDATA[N]]></customer_flag><customer_code><![CDATA[NC000720140822161854]]></customer_code></order_header><order_details><Order_no><![CDATA[OC000720140822161904]]></Order_no><Sku_code><![CDATA[12010]]></Sku_code><qty><![CDATA[25.0]]></qty><TD><![CDATA[0]]></TD><sale_rate><![CDATA[]]></sale_rate><mrp_code><![CDATA[m010]]></mrp_code></order_details></orderdata></order></root>";*/

$attendance_emp_code = "*ROOT*ATTENDANCE*LOCATION*EMP_CODE";
$attendance_trans_id = "*ROOT*ATTENDANCE*LOCATION*TRANS_ID";
$attendance_latt = "*ROOT*ATTENDANCE*LOCATION*LATT";
$attendance_longi = "*ROOT*ATTENDANCE*LOCATION*LONGI";
$attendance_date = "*ROOT*ATTENDANCE*LOCATION*DATE";
$attendancedata_emp_code = "*ROOT*ATTENDANCE*ATTENDANCEDATA*EMP_CODE";
$attendancedata_date = "*ROOT*ATTENDANCE*ATTENDANCEDATA*DATE";

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
$orderdatadetails_order_no = "*ROOT*ORDER*ORDERDATA*ORDER_DETAILS*ORDER_NO";
$orderdatadetails_sku_code = "*ROOT*ORDER*ORDERDATA*ORDER_DETAILS*SKU_CODE";
$orderdatadetails_qty = "*ROOT*ORDER*ORDERDATA*ORDER_DETAILS*QTY";
$orderdatadetails_TD = "*ROOT*ORDER*ORDERDATA*ORDER_DETAILS*TD";
$orderdatadetails_sale_rate = "*ROOT*ORDER*ORDERDATA*ORDER_DETAILS*SALE_RATE";
$orderdatadetails_mrp_code = "*ROOT*ORDER*ORDERDATA*ORDER_DETAILS*MRP_CODE";

$payment_emp_code="*ROOT*PAYMENT*LOCATION*EMP_CODE";
$payment_trans_id = "*ROOT*PAYMENT*LOCATION*TRANS_ID";
$payment_latt = "*ROOT*PAYMENT*LOCATION*LATT";
$payment_longi = "*ROOT*PAYMENT*LOCATION*LONGI";
$payment_date="*ROOT*PAYMENT*LOCATION*DATE";
$paymentdataheader_receipt_id = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_HEADER*RECEIPT_ID";
$paymentdataheader_customer_code = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_HEADER*CUSTOMER_CODE";
$paymentdataheader_amount = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_HEADER*AMOUNT";
$paymentdataheader_cash_cheque = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_HEADER*CASH_CHEQUE";
$paymentdataheader_cheque_no = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_HEADER*CHEQUE_NO";
$paymentdataheader_date = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_HEADER*DATE";
$paymentdataheader_bank = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_HEADER*BANK";
$paymentdataheader_sale_type = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_HEADER*SALE_TYPE";
$paymentdataheader_p_remark = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_HEADER*P_REMARK";
$paymentdatadetails_receipt_id = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_DETAILS*RECEIPT_ID";
$paymentdatadetails_invoice_id = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_DETAILS*INVOICE_ID";
$paymentdatadetails_recid = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_DETAILS*RECID";
$paymentdatadetails_amount = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_DETAILS*AMOUNT";
$paymentdatadetails_discount = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_DETAILS*DISCOUNT";

$attendance_array = array();
$order_array=array();
$order_details_array=array();
$payment_array=array();
$payment_details_array=array();
$stockist_array=array();
$mt_array=array();

$counter = 0;
$counterorder=0;
$counterorderdetails=0;
$counterpayment=0;
$counterpaymentdetails=0;

class xml_attendance{
    var $emp_code, $trans_id,$latt,$longi,$attendance_date,$attendancedata_emp_code,$attendancedata_date;
}
class xml_order{
	var $order_emp_code,$order_trans_id,$order_latt,$order_longi,$order_date,$orderdataheader_order_no,$orderdataheader_customer_code,$orderdataheader_customer_name,
	$orderdataheader_phone_no,$orderdataheader_address,$orderdataheader_pin_code,$orderdataheader_area,$orderdataheader_area_name,$orderdataheader_TD,$orderdataheader_sale_type,$orderdataheader_d_instruction,$orderdataheader_tag_distributor_code,$orderdataheader_cust_type,$orderdataheader_transaction_type,$orderdataheader_customer_flag;	
}
class xml_order_details{
	var $orderdatadetails_order_no,$orderdatadetails_sku_code,$orderdatadetails_qty,$orderdatadetails_TD,$orderdatadetails_sale_rate,$orderdatadetails_mrp_code;
}
class xml_payment{
	var $payment_emp_code,$payment_trans_id,$payment_latt,$payment_longi,$payment_date,$paymentdataheader_receipt_id,$paymentdataheader_customer_code,$paymentdataheader_amount,$paymentdataheader_cash_cheque,$paymentdataheader_cheque_no,$paymentdataheader_date,$paymentdataheader_bank,$paymentdataheader_sale_type,$paymentdataheader_p_remark;	
}
class xml_payment_details{
	var $paymentdatadetails_receipt_id,$paymentdatadetails_invoice_id,$paymentdatadetails_recid,$paymentdatadetails_amount,$paymentdatadetails_discount;
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
	$orderdataheader_d_instruction,$orderdataheader_tag_distributor_code,$orderdataheader_cust_type,$orderdataheader_transaction_type,$orderdataheader_customer_flag,$orderdatadetails_order_no,$orderdatadetails_sku_code,$orderdatadetails_qty,$orderdatadetails_TD,$orderdatadetails_sale_rate,$orderdatadetails_mrp_code,
	$payment_emp_code,$payment_trans_id,$payment_latt,$payment_longi,$payment_date,$paymentdataheader_receipt_id,$paymentdataheader_customer_code,$paymentdataheader_amount,
	$paymentdataheader_cash_cheque,$paymentdataheader_cheque_no,$paymentdataheader_date,$paymentdataheader_bank,$paymentdataheader_sale_type,$paymentdataheader_p_remark,$paymentdatadetails_receipt_id, $paymentdatadetails_invoice_id,$paymentdatadetails_recid,$paymentdatadetails_amount,$paymentdatadetails_discount;
	//echo $current_tag.'<br />';
	//echo $data;
	if(substr($current_tag,0,16)=='*ROOT*ATTENDANCE')
	{
		switch($current_tag){
			case $attendance_emp_code:
				$attendance_array[$counter] = new xml_attendance();
				$attendance_array[$counter]->emp_code = $data;
				break;
			case $attendance_trans_id:
				$attendance_array[$counter]->trans_id = $data;
				break;
			case $attendance_latt:
				$attendance_array[$counter]->latt = $data;
				break;
			case $attendance_longi:
				$attendance_array[$counter]->longi = $data;
				break;
			case $attendance_date:
				$attendance_array[$counter]->attendance_date = $data;
				break;
			case $attendancedata_emp_code:
				$attendance_array[$counter]->attendancedata_emp_code = $data;
				break;
			case $attendancedata_date:
				$attendance_array[$counter]->attendancedata_date = $data;
				$counter++;
				break;
		}
	}
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
			case $orderdataheader_customer_code:
				$order_array[$counterorder]->orderdataheader_customer_code = $data;
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
				case $orderdatadetails_sale_rate:
					$order_details_array[$counterorderdetails]->orderdatadetails_sale_rate = $data;
					break;			
				case $orderdatadetails_mrp_code:
					$order_details_array[$counterorderdetails]->orderdatadetails_mrp_code = $data;
					$counterorderdetails++;
					break;
		}
	}
	if(substr($current_tag,0,13)=='*ROOT*PAYMENT')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';			
		switch($current_tag){
			case $payment_emp_code:
				$payment_array[$counterpayment] = new xml_payment();
				$payment_array[$counterpayment]->payment_emp_code = $data;
				break;
			case $payment_trans_id:
				$payment_array[$counterpayment]->payment_trans_id = $data;
				break;
			case $payment_latt:
				$payment_array[$counterpayment]->payment_latt = $data;
				break;
			case $payment_longi:
				$payment_array[$counterpayment]->payment_longi = $data;
				break;
			case $payment_date:
				$payment_array[$counterpayment]->payment_date = $data;
				break;
			case $paymentdataheader_receipt_id:
				$payment_array[$counterpayment]->paymentdataheader_receipt_id = $data;
				break;
			case $paymentdataheader_amount:
				$payment_array[$counterpayment]->paymentdataheader_amount = $data;
				break;
			case $paymentdataheader_cash_cheque:
				$payment_array[$counterpayment]->paymentdataheader_cash_cheque = $data;
				break;
			case $paymentdataheader_cheque_no:
				$payment_array[$counterpayment]->paymentdataheader_cheque_no = $data;
				break;
			case $paymentdataheader_date:
				$payment_array[$counterpayment]->paymentdataheader_date = $data;
				break;
			case $paymentdataheader_bank:
				$payment_array[$counterpayment]->paymentdataheader_bank = $data;
				break;
			case $paymentdataheader_sale_type:
				$payment_array[$counterpayment]->paymentdataheader_sale_type = $data;
				break;		
			case $paymentdataheader_p_remark:
				$payment_array[$counterpayment]->paymentdataheader_p_remark = $data;
				break;			
			case $paymentdataheader_customer_code:
				$payment_array[$counterpayment]->paymentdataheader_customer_code = $data;
				$counterpayment++;
				break;
		}
	}
	if(substr($current_tag,0,41)=='*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_DETAILS')
		{
		switch($current_tag){
			case $paymentdatadetails_receipt_id:
				$payment_details_array[$counterpaymentdetails] = new xml_payment_details();
				$payment_details_array[$counterpaymentdetails]->paymentdatadetails_receipt_id = $data;
				break;
			case $paymentdatadetails_invoice_id:
				$payment_details_array[$counterpaymentdetails]->paymentdatadetails_invoice_id = $data;
				break;
			case $paymentdatadetails_recid:
				$payment_details_array[$counterpaymentdetails]->paymentdatadetails_recid = $data;
				break;
			case $paymentdatadetails_amount:
				$payment_details_array[$counterpaymentdetails]->paymentdatadetails_amount = $data;
				break;
			case $paymentdatadetails_discount:
				$payment_details_array[$counterpaymentdetails]->paymentdatadetails_discount = $data;
				$counterpaymentdetails++;
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
//print_r($attendance_array);
//echo count($attendance_array);
//print_r($order_array);
//print_r($order_details_array);
//print_r($payment_array);
mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");

$flag=1;
/* ------------------------------------------------START QUERY FOR ATTENDANCE---------------------------------------------------------------------*/
if(count($attendance_array)>0)
{
	for($x=0;$x<count($attendance_array);$x++){
		$emp_code=$attendance_array[$x]->emp_code;
		$trans_id=$attendance_array[$x]->trans_id;
		$latt=$attendance_array[$x]->latt;
		$longi=$attendance_array[$x]->longi;
		$attendance_date=$attendance_array[$x]->attendance_date;
		$attendance_emp_code=$attendance_array[$x]->attendancedata_emp_code;
		$attendancedata_date=$attendance_array[$x]->attendancedata_date;
		
		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if($latt>0 && $longi>0)
		{
			$sqlupdatelatlongzero="UPDATE location SET latt='".$latt."',longi='".$longi."' WHERE emp_code='".$emp_code."' AND latt='0' AND longi='0'";
			$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
		}
		
		//For checking that trans id exist or not
		$sqlchkattlocation="SELECT * FROM location WHERE trans_id='".$trans_id."'";
		$reschkattlocation = mysqli_query($link,$sqlchkattlocation) or die(mysqli_error()." Error in check attendance location: ".$sqlchkattlocation); 
		$rowchkattlocation = mysqli_fetch_assoc($reschkattlocation);
		$countchkattlocation=mysqli_num_rows($reschkattlocation);
		
		//For update the location table for existing trans id
		if($countchkattlocation>0)
		{
			$sqlupdateattlocation="UPDATE location SET emp_code='".$emp_code."',
									latt='".$latt."',
									longi='".$longi."'
									WHERE trans_id='".$trans_id."'";
			$rsupdateattlocation=mysqli_query($link,$sqlupdateattlocation) or die(mysqli_error()." Error in update attendance location: ".$sqlupdateattlocation);
			if($rsupdateattlocation)
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
			// create the data for location table date field , by checking the current date and time and the actual date and time of attendance
			$date=gmdate('d',strtotime('+329 minute'));
			$month=gmdate('m',strtotime('+329 minute'));
			$year=gmdate('Y',strtotime('+329 minute'));
			
			$hour=gmdate('H',strtotime('+329 minute'));
			$minute=gmdate('i',strtotime('+329 minute'));
			$second=gmdate('s',strtotime('+329 minute'));
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;

			//For Insert into the location table for new trans id			
			$sqlinsertattlocation="INSERT INTO location SET emp_code='".$emp_code."',
									trans_id='".$trans_id."',
									latt='".$latt."',
									longi='".$longi."',
									date='".$attendance_date."',
									updatetime='".$location_date."'";
			
			//For Insert into the attendance table for new trans id
			$sqlinsertattendance="INSERT INTO attendence SET emp_code='".$attendance_emp_code."',
								 date='".$attendancedata_date."'";	
			if(mysqli_query($link,$sqlinsertattlocation) && mysqli_query($link,$sqlinsertattendance))
				{
					$flag=5;
					
					$last_operation_datetime=$attendance_date;
					// For Sending email to recipents for attendance
				 	$sqlempname="SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
					$rsempname=mysqli_query($link,$sqlempname);
					$rowempname=mysqli_fetch_assoc($rsempname);
					$emp_name=$rowempname['emp_name'];
					$address=getReverseGeo($latt,$longi);
					$attendanceemailsubj="Attendance - ".$emp_name." on ".date('d-m-Y',strtotime($attendance_date))." @".date('H:i:s',strtotime($attendance_date)).' hrs.';
					$attendancemailbody = "<html><head><title>Attendance</title></head>
										<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
										.$emp_name. "</b><br><br>".$emp_name." marked as present on <b>".date('d-m-Y H:i:s',strtotime($attendance_date))."</b> 
										at <b>".$address."</b></table><br><br>Powered By aceDNS</body></html>";
					$headers  = "MIME-Version: 1.0\r\n";
					$headers .= "Content-type: text/html; charset=UTF-8\n";
					$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
								"Reply-To:".FROMEMAIL." \r\n" .
								"Bcc: ".BCCEMAIL." \r\n".
								'X-Mailer: PHP/' . phpversion();
					if(mail(ATTENDANCEEMAILRECIPENTS, $attendanceemailsubj, $attendancemailbody, $headers,'-facedns@coral.in'))
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
					mysqli_query($link,"ROLLBACK");
					echo $flag=0;
					return;
				}
			
		}// End of else
	}// End for loop
}// End attendance array if 

 /* --------------------END QUERY FOR ATTENDANCE------------------------------------------------------------------------------------------------------------*/
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
		$orderdataheader_tag_distributor_code=$order_array[$x]->orderdataheader_tag_distributor_code;
		if($orderdataheader_sale_type=='CASH'){
		$paymentdataheader_amount=$payment_array[$x]->paymentdataheader_amount;
		}
		
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
			// create the data for location table date field , by checking the current date and time and the actual date and time of order
			$date=gmdate('d',strtotime('+329 minute'));
			$month=gmdate('m',strtotime('+329 minute'));
			$year=gmdate('Y',strtotime('+329 minute'));

			$hour=gmdate('H',strtotime('+329 minute'));
			$minute=gmdate('i',strtotime('+329 minute'));
			$second=gmdate('s',strtotime('+329 minute'));
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			//For Insert into the location table for new trans id regarding order
			$sqlinsertorlocation="INSERT INTO location SET emp_code='".$order_emp_code."',
									trans_id='".$order_trans_id."',
									latt='".$order_latt."',
									longi='".$order_longi."',
									date='".$order_date."',
									updatetime='".$location_date."'"; 
			
			//For Insert into the Order Header table for new trans id
			$sqlinsertorderheader="INSERT INTO order_header SET order_no='".$orderdataheader_order_no."',
								  customer_code 	='".$orderdataheader_customer_code."',
								  sale_type 		='".$orderdataheader_sale_type."',
								  TD				='".$orderdataheader_TD."',
								  tag_distributor_code='".$orderdataheader_tag_distributor_code."',
								  transaction_type='".$orderdataheader_transaction_type."',
								  d_instruction		='".addslashes($orderdataheader_d_instruction)."'";
			if($orderdataheader_customer_flag=='N'){
			$sqlinsertprospectcustomer="INSERT INTO customer_master SET emp_code='".$order_emp_code."',
									   customer_code				='".$orderdataheader_customer_code."',
									   customer_name				='".$orderdataheader_customer_name."',
									   address						='".$orderdataheader_address."',
									   pin							='".$orderdataheader_pin_code."',
									   phone_no						='".$orderdataheader_phone_no."',
									   cust_type					='".$orderdataheader_cust_type."',
									   download_time				=CURRENT_TIMESTAMP(),
									   download_time_credit_limit	=CURRENT_TIMESTAMP(),
									   acedns						='Y',
									   black_list					='N',
									   route_code					='".$orderdataheader_area."'";						  	
			if(mysqli_query($link,$sqlinsertorlocation) && mysqli_query($link,$sqlinsertorderheader) && mysqli_query($link,$sqlinsertprospectcustomer))
				{
					$flag=5;
				}
				else
				{
					mysqli_query($link,"ROLLBACK");
					//ON 14-07-2014
					echo $flag=0;
					//echo $flag=2;
					return;
				}
			}
			else
			{
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
			}
			// Add new route to route master
			if(substr($orderdataheader_area,0,1)=='N'){
				$sqlroute="SELECT route_name FROM route_master WHERE route_code='".$orderdataheader_area."'";
				$rsroute=mysqli_query($link,$sqlroute);
				$countroute=mysqli_num_rows($rsroute);
				if($countroute<1)
				{
					$sqlroute  = "insert into route_master ";
					$sqlroute .= " SET route_code='".$orderdataheader_area."'";
					$sqlroute .= " ,dns_route_code=''";
					$sqlroute .= " ,route_name='".$orderdataheader_area_name."'";
					$sqlroute .= " , emp_code='".$order_emp_code."'";
					$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";
					if(mysqli_query($link,$sqlroute))
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
			//Add new rds to rds master
				if($orderdataheader_cust_type=='D'){
					$sqlrds  = "insert into rds_master ";
					$sqlrds .= " SET rds_code='".$orderdataheader_customer_code."'";
					$sqlrds .= " ,dns_rds_code=''";
					$sqlrds .= " ,rds_name='".$orderdataheader_customer_name."'";
					$sqlrds .= " , emp_code='".$order_emp_code."'";
					$sqlrds .= " , rds_type='".$orderdataheader_cust_type."'";
					$sqlrds .= " , download_time=CURRENT_TIMESTAMP()";
					if(mysqli_query($link,$sqlrds))
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
				//Regarding Email Sending
				$sqlempname="SELECT emp_name FROM employee_master WHERE emp_code='".$order_emp_code."'";
				$rsempname=mysqli_query($link,$sqlempname);
				$rowempname=mysqli_fetch_assoc($rsempname);
				$emp_name=$rowempname['emp_name'];
				
				$addressorder=getReverseGeo($order_latt,$order_longi);
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
					$sqlcustomername="SELECT customer_name FROM customer_master WHERE customer_code='".$orderdataheader_customer_code."'";
					$rscustomername=mysqli_query($link,$sqlcustomername);
					$rowcustomername=mysqli_fetch_assoc($rscustomername);
					$customer_name=$rowcustomername['customer_name'];
					
					$orderheader_TR="<th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Customer Name</span></strong></th>";
					$orderheader_TD="<td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>
									".$customer_name."</span>&nbsp;</td>";
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
						if($orderdataheader_transaction_type=='BT') $transaction_type_details='Branch Transfer';
						if($orderdataheader_transaction_type=='ST') $transaction_type_details='Stock Transfer';
						if($orderdataheader_transaction_type=='SR') $transaction_type_details='Stock Return';
						//Branch
						$sqlbranch="SELECT branch_name FROM branch_master WHERE branch_code='".$orderdataheader_customer_code."'";
						$rsbranch=mysqli_query($link,$sqlbranch);
						$rowbranch=mysqli_fetch_assoc($rsbranch);
						$branch_name=$rowbranch['branch_name'];
						$orderheader_TR="<th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Branch Name</span></strong></th>";
					$orderheader_TD="<td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$branch_name."</span>&nbsp;</td>";
					$entity_name=$branch_name;
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
						$sqlInsertdatarefresh="INSERT INTO data_refresh_log SET refresh_date_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlInsertdatarefresh);
					}
					if($orderdataheader_transaction_type=='SB' || $orderdataheader_transaction_type=='SO' || $orderdataheader_transaction_type=='CR'){
						if($orderdataheader_transaction_type=='SB') $transaction_type_details='Sale Bill';
						if($orderdataheader_transaction_type=='SO') $transaction_type_details='Sale Order';
						if($orderdataheader_transaction_type=='CR') $transaction_type_details='Carry In Return';
						//customer
						$sqlcustomername="SELECT customer_name FROM customer_master WHERE customer_code='".$orderdataheader_customer_code."'";
						$rscustomername=mysqli_query($link,$sqlcustomername);
						$rowcustomername=mysqli_fetch_assoc($rscustomername);
						$customer_name=$rowcustomername['customer_name'];
						$orderheader_TR="<th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Customer Name</span></strong></th>";
					$orderheader_TD="<td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$customer_name."</span>&nbsp;</td>";
					$entity_name=$customer_name;
					}
					//-------------------------------------------End For Sale Feature-------------------------------------------------------------------------
				
				if($orderdataheader_tag_distributor_code!='')
				{
					$sqldistributor="SELECT rds_name FROM rds_master WHERE rds_code='".$orderdataheader_tag_distributor_code."'";
					$rsdistributor=mysqli_query($link,$sqldistributor);
					$rowdistributor=mysqli_fetch_assoc($rsdistributor);
					$distributor_name=$rowdistributor['rds_name'];

					$orderheader_TR_distributor="<th style='width:200px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Tagged To Distributor</span></strong></th>";
					$orderheader_TD_distributor="<td style='width:200px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$distributor_name."</span>&nbsp;</td>";
				}
				else
				{
					$orderheader_TR_distributor="";
					$orderheader_TD_distributor="";
				}
				
				//For sending email to recipents in case of No Order transaction happened
				$last_operation_datetime=$order_date;
				if(substr($order_trans_id,0,1)=='N')
				{
					$noorderemailsubj="Activiy without Transaction ".$emp_name. " on ".date('d-m-Y',strtotime($order_date))." @".date('H:i:s',strtotime($order_date)).' hrs.';

					$noorderemailbody = "<html><head><title>No Order</title></head>
										<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
										.$emp_name. "</b><br><br>".$emp_name." visited ".$customer_name." at ".$addressorder.". 
										No order happened. </table><br><br>
										<b>Remarks: </b> ".strtoupper($orderdataheader_d_instruction)."
										<br><br><br>Powered By aceDNS<br></body></html>";
					$headers  = "MIME-Version: 1.0\r\n";
					$headers .= "Content-type: text/html; charset=UTF-8\n";
					$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
								"Reply-To:".FROMEMAIL." \r\n" .
								"Bcc: ".BCCEMAIL." \r\n".
								'X-Mailer: PHP/' . phpversion();
					if(mail(ORDEREMAILRECIPENTS, $noorderemailsubj, $noorderemailbody, $headers,'-facedns@coral.in'))
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
				//For constructing the email body for ORDER HEADER if ORDER has performed
				$orderemailbody = "<html><head><title>Order Details</title></head>
							<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
							.$emp_name. "</b><br><br><br><table border=1 style=background-color:AliceBlue>
							<tr>".$orderheader_TR.$orderheader_TR_distributor."
							<th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
							CE'>Date & Time</span></strong></th>
							<th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
							CE'>Transaction Type</span></strong></th>
							</tr><tr>".$orderheader_TD.$orderheader_TD_distributor."
							<td style='width:165px;text-align:right;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>".date('d-m-Y H:i:s',strtotime($order_date))."</span>&nbsp;</td>
							<td style='width:165px;text-align:right;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>".$transaction_type_details."</span>&nbsp;</td>
							</tr></table>";
							
							array_push($orderheader_array_mailbody,$orderemailbody);
							
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
							array_push($ordertansid_array_mailbody,$order_trans_id);
							array_push($orderdataheader_transaction_type_array,$orderdataheader_transaction_type);
							array_push($orderdataheader_entity_name_array,$entity_name);
					}
		 }//End of else
	}// End for loop
	//For Insert into the Order Details table for new trans id
	//print_r($order_details_array);
		if(count($order_details_array)>0)
		{
			for($i=0;$i<count($order_details_array);$i++){
				$orderdatadetails_order_no=$order_details_array[$i]->orderdatadetails_order_no;
				$orderdatadetails_sku_code=$order_details_array[$i]->orderdatadetails_sku_code;
				$orderdatadetails_qty=$order_details_array[$i]->orderdatadetails_qty;
				$orderdatadetails_TD=$order_details_array[$i]->orderdatadetails_TD;
				$orderdatadetails_mrp_code=$order_details_array[$i]->orderdatadetails_mrp_code;
				$orderdatadetails_sale_rate=$order_details_array[$i]->orderdatadetails_sale_rate;
				
				$sqlmrpdetails="SELECT mrp FROM mrp WHERE product_code='".$orderdatadetails_sku_code."' AND mrp_code='".$orderdatadetails_mrp_code."'";
				$rsmrpdetails=mysqli_query($link,$sqlmrpdetails);
				$recmrpdetails=mysqli_fetch_assoc($rsmrpdetails);
				$mrp=$recmrpdetails['mrp'];
				
					if(no_of_filter==1){
						$sqlproductdetails="SELECT prod_desc FROM product_master WHERE prod_code='".$orderdatadetails_sku_code."'";
					}
					if(no_of_filter==2){
						$sqlproductdetails="SELECT PGM.product_group_name,PM.prod_desc FROM product_master PM,product_group_master PGM 
											WHERE PM.product_group_code=PGM.product_group_code AND PM.prod_code='".$orderdatadetails_sku_code."'";
					}
					if(no_of_filter==3){
						$sqlproductdetails="SELECT PGM.product_group_name,PSGM.product_sub_group_name,PM.prod_desc FROM 
											product_master PM,product_group_master PGM,product_sub_group_master PSGM
											WHERE PM.product_group_code=PGM.product_group_code AND PM.product_sub_group_code=PSGM.product_sub_group_code 
											AND PM.prod_code='".$orderdatadetails_sku_code."'";
					}
					if(no_of_filter==4){
						$sqlproductdetails="SELECT PGM.product_group_name,PSGM.product_sub_group_name,PBM.product_brand_name,PM.prod_desc
											FROM  product_master PM,product_group_master PGM,product_sub_group_master PSGM,product_brand_master PBM
											WHERE PM.product_group_code=PGM.product_group_code AND PM.product_sub_group_code=PSGM.product_sub_group_code
											AND PM.product_brand_code=PBM.product_brand_code AND PM.prod_code='".$orderdatadetails_sku_code."'";
					}
					$rsproductdetails=mysqli_query($link,$sqlproductdetails);
					$rowproductdetails=mysqli_fetch_assoc($rsproductdetails);
					$prod_desc=$rowproductdetails['prod_desc'];
					$product_group_name=$rowproductdetails['product_group_name'];
					$product_brand_name=$rowproductdetails['product_brand_name'];
					$product_sub_group_name=$rowproductdetails['product_sub_group_name'];
					
					//print_r($order_array_trans_id);
					if(!in_array($orderdatadetails_order_no,$order_array_trans_id))
					{
						$sqlinsertorderdetails="INSERT INTO order_details SET order_no='".$orderdatadetails_order_no."',
												sku_code 	='".$orderdatadetails_sku_code."',
												qty			='".$orderdatadetails_qty."',
												TD			='".$orderdatadetails_TD."',
												sale_rate	='".$orderdatadetails_sale_rate."',
												mrp_code	='".$orderdatadetails_mrp_code."'";
						if(mysqli_query($link,$sqlinsertorderdetails))
						{
							$flag=5;
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
						if(mrp=='yes' && TD=='yes' && TD_type=='sku wise'){
							//$totalmrp=($orderdatadetails_qty*$mrp)-((($orderdatadetails_qty*$mrp)*$orderdatadetails_TD)/100);
							$totalmrp=($orderdatadetails_qty*$mrp)+$orderdatadetails_TD;
							${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$totalmrp;
							${totalamount.$orderdatadetails_sku_code}=($orderdatadetails_qty*$mrp);
						 }
						 if(mrp=='yes' && TD=='no'){
							$totalmrp=($orderdatadetails_qty*$mrp);
							${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$totalmrp;
							${totalamount.$orderdatadetails_sku_code}=$totalmrp;
						 }
						 if(sale_rate=='yes' && TD=='yes' && TD_type=='sku wise'){
							// $totalrate=($orderdatadetails_qty*$orderdatadetails_sale_rate)-((($orderdatadetails_qty*$orderdatadetails_sale_rate)*$orderdatadetails_TD)/100);
							$totalrate=($orderdatadetails_qty*$orderdatadetails_sale_rate)+$orderdatadetails_TD;
							${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$totalrate;
							${totalamount.$orderdatadetails_sku_code}=($orderdatadetails_qty*$orderdatadetails_sale_rate);
						 }
						 if(sale_rate=='yes' && TD=='no'){
							$totalrate=($orderdatadetails_qty*$orderdatadetails_sale_rate);
							${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$totalrate;
							${totalamount.$orderdatadetails_sku_code}=$totalrate;
						 }
						 if(mrp=='yes' && TD=='yes' && (TD_type=='order value wise' || TD_type=='customer wise')){
							$totalmrp=($orderdatadetails_qty*$mrp);
							${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$totalmrp;
							${totalamount.$orderdatadetails_sku_code}=$totalmrp;
						 }
						 if(sale_rate=='yes' && TD=='yes' && (TD_type=='order value wise' || TD_type=='customer wise')){
							$totalrate=($orderdatadetails_qty*$orderdatadetails_sale_rate);
							${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$totalrate;
							${totalamount.$orderdatadetails_sku_code}=$totalrate;
						 }
						 
						 if(TD_type=='sku wise')
						 {
							$orderemailbody_TD_VAL="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".number_format($orderdatadetails_TD,2)."</span>&nbsp;</td>";
						 }
						 else
						 {
							 $orderemailbody_TD_VAL="";
						 }
						${a.$orderdatadetails_order_no} .="
								<tr>".$product_details_TD."
								<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".number_format($orderdatadetails_qty,2)."</span>&nbsp;</td>
								<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".number_format($mrp,2)."</span>&nbsp;</td>
								<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".number_format($orderdatadetails_sale_rate,2)."</span>&nbsp;</td>".$orderemailbody_TD_VAL."
								<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".number_format(${totalamount.$orderdatadetails_sku_code},2)."</span>&nbsp;</td>
								</tr>";
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
				if(TD_type=='order value wise')
				{
				$TD_order_val="<br /><table>TD on order value: <b>".number_format($orderdataheader_TD_array[$countarr],2)."%</b></table>";
				}
				if(TD_type=='customer wise')
				{
				$TD_order_val="<br /><table>TD applicable for this customer: <b>".$orderdataheader_TD_array[$countarr]."%</b></table>";
				}
				if((TD_type=='order value wise' || TD_type=='customer wise')){
					$total_order_amount=(${grandtotal.$ordertansid_array_mailbody[$countarr]}-(${grandtotal.$ordertansid_array_mailbody[$countarr]}*$orderdataheader_TD_array[$countarr])/100);
				}
				else
				{
					$total_order_amount=${grandtotal.$ordertansid_array_mailbody[$countarr]};
				}
				if($orderdataheader_sale_type_array[$countarr]=='CREDIT' || $orderdataheader_sale_type_array[$countarr]=='COD' || $orderdataheader_transaction_type_array[$countarr]=='SB'){
					if(($orderdataheader_sale_type_array[$countarr]=='CREDIT' || $orderdataheader_sale_type_array[$countarr]=='COD') && $orderdataheader_transaction_type_array[$countarr]!='SB'){
					$orderemailsubj="Order received by ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';
					}
					else if(($orderdataheader_sale_type_array[$countarr]=='CREDIT' || $orderdataheader_sale_type_array[$countarr]=='COD') && $orderdataheader_transaction_type_array[$countarr]=='SB')
					{
					$orderemailsubj="Sale bill generated by ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';

					}
				$ordercashreceivedemailbody='';
				}
				else if($orderdataheader_sale_type_array[$countarr]=='CASH' || $orderdataheader_transaction_type_array[$countarr]=='SB')
				{
					if($orderdataheader_sale_type_array[$countarr]=='CASH'  && $orderdataheader_transaction_type_array[$countarr]!='SB'){
					$orderemailsubj="Cash sale by ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';
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
					else if(($orderdataheader_sale_type_array[$countarr]=='CREDIT' || $orderdataheader_sale_type_array[$countarr]=='COD') && $orderdataheader_transaction_type_array[$countarr]=='SB')
					{
						$total_order_val="<br /><table>Total sale value: <b>Rs. ".number_format($total_order_amount,2)."/- ".$COD_val."</b></table>";
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
				if(TD_type=='sku wise' && ($orderdataheader_transaction_type_array[$countarr]=='SO' || $orderdataheader_transaction_type_array[$countarr]=='SB' || $orderdataheader_transaction_type_array[$countarr]=='PB' ))
				{
					$orderemailbody_TD_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>VAT</span></strong></th>";
				}
				else if(TD_type=='sku wise' && ($orderdataheader_transaction_type_array[$countarr]=='BT' || $orderdataheader_transaction_type_array[$countarr]=='ST' || $orderdataheader_transaction_type_array[$countarr]=='CN' || $orderdataheader_transaction_type_array[$countarr]=='SR' || $orderdataheader_transaction_type_array[$countarr]=='CR'))
				{
					$orderemailbody_TD_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>TD</span></strong></th>";
				}
				else if(TD_type=='order value wise')
				{
					$orderemailbody_TD_TH="";
				}
				
				if($orderdataheader_transaction_type_array[$countarr]=='PB')
				{
					$sale_rate_TR='Purchase Rate';
				}
				else
				{
					$sale_rate_TR='Sale Rate';
				}
				
				$orderemailbody = $orderheader_array_mailbody[$countarr]."<br><table border=1 style=background-color:AliceBlue>
							<tr>".$product_details_TH."
							<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>Qty</span></strong></th>
							<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>MRP</span></strong></th>
							<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>".$sale_rate_TR."</span></strong></th>".$orderemailbody_TD_TH."
							<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>Amount</span></strong></th>
							</tr>
				".${a.$ordertansid_array_mailbody[$countarr]}."</table>
				".$TD_order_val.$ordercashreceivedemailbody.$total_order_val."
				<br /><br /><table>".$orderinstruction_array_mailbody[$countarr]."</table><br /><br /><br />Powered By aceDNS<br /></body></html>";
				$headers  = "MIME-Version: 1.0\r\n";
				$headers .= "Content-type: text/html; charset=UTF-8\n";
				$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
							"Reply-To:".FROMEMAIL." \r\n" .
							"Bcc: ".BCCEMAIL." \r\n" .
							'X-Mailer: PHP/' . phpversion();
				$order_email=ORDEREMAILRECIPENTS;			
				if(mail($order_email, $orderemailsubj, $orderemailbody, $headers,'-facedns@coral.in'))
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
 /* --------------------END QUERY FOR ORDER---------------------------------------------------------------------------------------------------------------*/
 /* --------------------START QUERY FOR PAYMENT-----------------------------------------------------------------------------------------------------------*/
$payment_array_trans_id=array();
$paymentheader_array_mailbody=array();
$paymentinstruction_array_mailbody=array();
$paymentdetails_array_mailbody=array();
$paymentemp_array_mailbody=array();
$paymentdate_array_mailbody=array();
$paymenttansid_array_mailbody=array();
$paymentdataheader_sale_type_array=array();

if(count($payment_array)>0)
{
	for($x=0;$x<count($payment_array);$x++){
		$payment_emp_code=$payment_array[$x]->payment_emp_code;
		$payment_trans_id=$payment_array[$x]->payment_trans_id;
		$payment_latt=$payment_array[$x]->payment_latt;
		$payment_longi=$payment_array[$x]->payment_longi;
		$payment_date=$payment_array[$x]->payment_date;
		$paymentdataheader_receipt_id=$payment_array[$x]->paymentdataheader_receipt_id;
		$paymentdataheader_customer_code=$payment_array[$x]->paymentdataheader_customer_code;
		$paymentdataheader_sale_type=$payment_array[$x]->paymentdataheader_sale_type;
		$paymentdataheader_p_remark=$payment_array[$x]->paymentdataheader_p_remark;
		$paymentdataheader_amount=$payment_array[$x]->paymentdataheader_amount;
		$paymentdataheader_cash_cheque=$payment_array[$x]->paymentdataheader_cash_cheque;
		$paymentdataheader_cheque_no=$payment_array[$x]->paymentdataheader_cheque_no;
		$paymentdataheader_date=$payment_array[$x]->paymentdataheader_date;
		$paymentdataheader_bank=$payment_array[$x]->paymentdataheader_bank;
		
		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if($payment_latt>0 && $payment_longi>0)
		{
			$sqlupdatelatlongzero="UPDATE location SET latt='".$payment_latt."',longi='".$payment_longi."' WHERE 
									emp_code='".$payment_emp_code."' AND latt='0' AND longi='0'";
			$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
		}

		
		//For checking that trans id exist or not for payment
		$sqlchkpaylocation="SELECT * FROM location WHERE trans_id='".$payment_trans_id."'";
		$reschkpaylocation = mysqli_query($link,$sqlchkpaylocation) or die(mysqli_error()." Error in check payment location: ".$sqlchkpaylocation); 
		$rowchkpaylocation = mysqli_fetch_assoc($reschkpaylocation);
		$countchkpaylocation=mysqli_num_rows($reschkpaylocation);
		
		//For update the location table for existing trans id for Payment
		if($countchkpaylocation>0)
		{
			//$payment_trans_id_chk=substr($payment_trans_id,1,19);
			if(!in_array($payment_trans_id,$payment_array_trans_id))
			{
				array_push($payment_array_trans_id,$payment_trans_id);
			}
			$sqlupdatepaylocation="UPDATE location SET emp_code='".$payment_emp_code."',
									latt='".$payment_latt."',
									longi='".$payment_longi."'
									WHERE trans_id='".$payment_trans_id."'";
			$rsupdatepaylocation=mysqli_query($link,$sqlupdatepaylocation) or die(mysqli_error()." Error in update payment location: ".$sqlupdatepaylocation);
			if($rsupdatepaylocation)
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
			// create the data for location table date field , by checking the current date and time and the actual date and time of payment
			$date=gmdate('d',strtotime('+329 minute'));
			$month=gmdate('m',strtotime('+329 minute'));
			$year=gmdate('Y',strtotime('+329 minute'));
			
			$hour=gmdate('H',strtotime('+329 minute'));
			$minute=gmdate('i',strtotime('+329 minute'));
			$second=gmdate('s',strtotime('+329 minute'));			
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			
			//For Insert into the location table for new trans id regarding payment
			$sqlinsertpaylocation="INSERT INTO location SET emp_code='".$payment_emp_code."',
									trans_id='".$payment_trans_id."',
									latt='".$payment_latt."',
									longi='".$payment_longi."',
									date='".$payment_date."',
									updatetime='".$location_date."'";
			
			//For Insert into the Payment Header table for new trans id
			$sqlinsertpaymentheader="INSERT INTO payment_header SET receipt_id='".$paymentdataheader_receipt_id."',
								  	customer_code 	='".$paymentdataheader_customer_code."',
									amount			='".$paymentdataheader_amount."',
									cash_cheque 	='".$paymentdataheader_cash_cheque."',
									cheque_no		='".$paymentdataheader_cheque_no."',
									date			='".$paymentdataheader_date."',
									bank 			='".$paymentdataheader_bank."',
									sale_type		='".$paymentdataheader_sale_type."',
									p_remark		='".addslashes($paymentdataheader_p_remark)."'";	
			if(mysqli_query($link,$sqlinsertpaylocation) && mysqli_query($link,$sqlinsertpaymentheader))
				{
					$flag=5;
				}
				else
				{
					mysqli_query($link,"ROLLBACK");
					echo $flag=0;
					return;
				}
				if($paymentdataheader_sale_type!='CASH'){
				//Regarding Email Sending
				$sqlempname="SELECT emp_name FROM employee_master WHERE emp_code='".$payment_emp_code."'";
				$rsempname=mysqli_query($link,$sqlempname);
				$rowempname=mysqli_fetch_assoc($rsempname);
				$emp_name=$rowempname['emp_name'];
				
				$sqlcustomername="SELECT customer_name FROM customer_master WHERE customer_code='".$paymentdataheader_customer_code."'";
				$rscustomername=mysqli_query($link,$sqlcustomername);
				$rowcustomername=mysqli_fetch_assoc($rscustomername);
				$customer_name=$rowcustomername['customer_name'];
				
				$addresspayment=getReverseGeo($payment_latt,$payment_longi);
				//For sending email to recipents in case of No Order transaction happened
				$last_operation_datetime=$payment_date;
				if(substr($payment_trans_id,0,1)=='N')
				{
					$nopaymentemailsubj="Activiy without Transaction ".$emp_name. " on ".date('d-m-Y',strtotime($payment_date))." @".date('H:i:s',strtotime($payment_date)).' hrs.';

					$nopaymentemailbody = "<html><head><title>No Payment</title></head>
										<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
										.$emp_name. "</b><br><br>".$emp_name." visited ".$customer_name." at ".$addresspayment.". 
										No payment happened. </table><br><br>
										<b>Remarks: </b> ".strtoupper($paymentdataheader_p_remark)."
										<br><br><br>Powered By aceDNS<br></body></html>";
					$headers  = "MIME-Version: 1.0\r\n";
					$headers .= "Content-type: text/html; charset=UTF-8\n";
					$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
								"Reply-To:".FROMEMAIL." \r\n" .
								"Bcc: ".BCCEMAIL." \r\n" .
								'X-Mailer: PHP/' . phpversion();
					if(mail(PAYMENTEMAILRECIPENTS, $nopaymentemailsubj, $nopaymentemailbody, $headers,'-facedns@coral.in'))
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
					//For constructing the email body for ORDER HEADER if ORDER has performed
					if($paymentdataheader_cash_cheque=='CHEQUE')
					{
						$paymentemailbodytranstypeth="<th style='width:60px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial CE'>Cheque No.</span></strong></th>
							<th style='width:70px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial CE'>Cheque Date</span></strong></th>
							<th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial CE'>Bank Name</span></strong></th>";
						$paymentemailbodytranstypetd="<td style='width:60px;text-align:right;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".round($paymentdataheader_cheque_no)."</span>&nbsp;</td>
							<td style='width:70px;text-align:right;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$paymentdataheader_date."</span>&nbsp;</td>
							<td style='width:160px;text-align:right;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$paymentdataheader_bank."</span>&nbsp;</td>";
					}
					if($paymentdataheader_cash_cheque=='CASH')
					{
						$paymentemailbodytranstypeth="<th style='width:60px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial CE'>Collection Type</span></strong></th>";
						$paymentemailbodytranstypetd="<td style='width:160px;text-align:right;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>CASH</span>&nbsp;</td>";
					}
				$paymentemailbody = "<html><head><title>Payment Details</title></head>
							<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
							.$emp_name. "</b><br><br><br><table border=1 style=background-color:AliceBlue>
							<tr>
							<th style='width:260px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial CE'>Customer Name</span></strong></th>
							<th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial CE'>Date & Time</span></strong></th>".$paymentemailbodytranstypeth."
							
							<th style='width:82px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial CE'>Total Amount Received</span></strong></th>
							</tr><tr><td style='width:260px;text-align:right;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>
							".$customer_name."</span>&nbsp;</td><td style='width:105px;text-align:right;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".gmdate('d-m-Y H:i:s',strtotime('+329 minute'))."</span>&nbsp;</td>".$paymentemailbodytranstypetd."
							
							<td style='width:82px;text-align:right;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".number_format($paymentdataheader_amount,2)."</span>&nbsp;</td>
							</tr></table>";
							
							array_push($paymentheader_array_mailbody,$paymentemailbody);
							$paymentinstructionemailbody="Remarks: <b>".strtoupper($paymentdataheader_p_remark)."</b>";
							array_push($paymentinstruction_array_mailbody,$paymentinstructionemailbody);
							array_push($paymentemp_array_mailbody,$emp_name);
							array_push($paymentdate_array_mailbody,$payment_date);
							array_push($paymenttansid_array_mailbody,$payment_trans_id);
					}
			}
			array_push($paymentdataheader_sale_type_array,$paymentdataheader_sale_type);
		}
	}// End for loop
	//For Insert into the Payment Details table for new trans id
		if(count($payment_details_array)>0)
		{
			for($k=0;$k<count($payment_details_array);$k++){
				$paymentdatadetails_receipt_id=$payment_details_array[$k]->paymentdatadetails_receipt_id;
				$paymentdatadetails_invoice_id=$payment_details_array[$k]->paymentdatadetails_invoice_id;
				$paymentdatadetails_recid=$payment_details_array[$k]->paymentdatadetails_recid;
				$paymentdatadetails_amount=$payment_details_array[$k]->paymentdatadetails_amount;
				$paymentdatadetails_discount=$payment_details_array[$k]->paymentdatadetails_discount;
				//$paymentdatadetails_receipt_id_chk='P'.$payment_details_array[$k]->paymentdatadetails_receipt_id;
				
				if(!in_array($paymentdatadetails_receipt_id,$payment_array_trans_id))
					{
					$sqlinsertpaymentdetails="INSERT INTO payment_details SET receipt_id='".$paymentdatadetails_receipt_id."',
											invoice_id 	='".$paymentdatadetails_invoice_id."',
											recid		='".$paymentdatadetails_recid."',
											amount		='".$paymentdatadetails_amount."',
											discount	='".$paymentdatadetails_discount."'";
					if(mysqli_query($link,$sqlinsertpaymentdetails))
					{
						$flag=5;
					}
					else
					{
						mysqli_query($link,"ROLLBACK");
						echo $flag=0;
						return;
					}
					$sqloutstanding="SELECT invoice_amount,due_amount FROM outstanding WHERE invoice_id='".$paymentdatadetails_invoice_id."'";
					$rsoutstanding=mysqli_query($link,$sqloutstanding);
					$rowoutstanding=mysqli_fetch_assoc($rsoutstanding);
					$invoice_amount=$rowoutstanding['invoice_amount'];
					$due_amount=$rowoutstanding['due_amount'];
					
					${a.$paymentdatadetails_receipt_id}.="
								<tr><td style='width:130px;text-align:right;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$paymentdatadetails_invoice_id."</span>&nbsp;</td>	
								<td style='width:120px;text-align:right;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".number_format($invoice_amount,2)."</span>&nbsp;</td>
								<td style='width:110px;text-align:right;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".number_format($due_amount,2)."</span>&nbsp;</td>
								<td style='width:130px;text-align:right;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".number_format($paymentdatadetails_amount,2)."</span>&nbsp;</td>
								<td style='width:110px;text-align:right;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".number_format($paymentdatadetails_discount,2)."</span>&nbsp;</td>
								</tr>";
				}
		   }
		}
		//End Insert into the Payment Details table for new trans id
		if(count($paymentheader_array_mailbody)>0)
		{
			for($countpayarr=0;$countpayarr<count($paymentheader_array_mailbody);$countpayarr++)
			{
				if($paymentdataheader_sale_type_array[$countpayarr]!='CASH'){
					$paymentemailsubj="Payment received by ".$paymentemp_array_mailbody[$countpayarr]." on ".date('d-m-Y',strtotime($paymentdate_array_mailbody[$countpayarr]))." @".date('H:i:s',strtotime($paymentdate_array_mailbody[$countpayarr])).' hrs.';
					$paymentemailbody = $paymentheader_array_mailbody[$countpayarr]."<br><table border=1 style=background-color:AliceBlue>
								<tr>
								<th style='width:130px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial CE'>Invoice No.</span></strong></th>
								<th style='width:120px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial CE'>Invoice Amount</span></strong></th>
								<th style='width:110px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial CE'>Due Amount</span></strong></th>
								<th style='width:130px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial CE'>Amount Received</span></strong></th>
							<th style='width:110px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial CE'>Discount Allowed </span></strong></th></tr>
					".${a.$paymenttansid_array_mailbody[$countpayarr]}."</table>
					<br><br><table>".$paymentinstruction_array_mailbody[$countpayarr]."</table><br /><br><br>Powered By aceDNS<br></body></html>";
					$headers  = "MIME-Version: 1.0\r\n";
					$headers .= "Content-type: text/html; charset=UTF-8\n";
					$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
								"Reply-To:".FROMEMAIL." \r\n" .
								"Bcc: ".BCCEMAIL." \r\n".
								'X-Mailer: PHP/' . phpversion();
					if(mail(PAYMENTEMAILRECIPENTS, $paymentemailsubj, $paymentemailbody, $headers,'-facedns@coral.in'))
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

 /* --------------------END QUERY FOR PAYMENT------------------------------------------------------------------------------------------------------------*/
if($flag==5)
{
	 $sqlupdatelastoperationtime="UPDATE changepassword SET last_operation_datetime='".$last_operation_datetime."' WHERE emp_code='".$emp_code."'";
	 $rsupdatelastoperationtime=mysqli_query($link,$sqlupdatelastoperationtime);	 
	 mysqli_query($link,"COMMIT");
	 
	 if($countdatarefresh >0)
	 {
		 echo $flag=2;
	 }
	 else
	 {
	 	echo $flag=1;
	 }
}
if($flag==6)
{
	$sqlupdatelastoperationtime="UPDATE changepassword SET last_operation_datetime='".$last_operation_datetime."' WHERE emp_code='".$emp_code."'";
	$rsupdatelastoperationtime=mysqli_query($link,$sqlupdatelastoperationtime);
	mysqli_query($link,"COMMIT");
	
	if($countdatarefresh >0)
	 {
		 echo $flag=2;
	 }
	 else
	 {
	 	echo $flag=1;
	 }
}
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = "http://www.coralindia.com/dev/acednsproduct/operationdb-transaction-3.1.5.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.coralindia.com/dev/acednsproduct/operationdb-transaction-3.1.5.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time"."\r\n";
	$insertPos=0;  // variable for saving 
	while (!feof($file)) {
		$line=fgets($file);
		if (strpos($line, 'http://')!==false) {
			$insertPos=ftell($file);
			$newline =  $newuser;
		}
		else
		{
			$newline.=$line;   // append existing data with new data of user
		}

	}
	fseek($file,$insertPos);   // move pointer to the file position where we saved above 
	fwrite($file, $newline);
	fclose($file);*/
?>
