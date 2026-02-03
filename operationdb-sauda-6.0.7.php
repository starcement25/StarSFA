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

if($emp_code=='C0007' || $emp_code=='C0008')
{
	exit();
}
if($nick_name=='AMPL' || $nick_name=='TT')
{
  $spam_filter='-facedns@coral.in';
}
else
{
  $spam_filter='-facedns@acedns.in';
}

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
$body=file_get_contents('php://input');
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><sauda><location><emp_code><![CDATA[E0002]]></emp_code><trans_id><![CDATA[FTE000220190923181402]]></trans_id><latt><![CDATA[22.728049699999996]]></latt><longi><![CDATA[88.4904832]]></longi><date><![CDATA[2019-09-23 18:14:02]]></date></location><saudadata><sauda_header><sauda_no><![CDATA[FTE000220190923181402]]></sauda_no><TD><![CDATA[]]></TD><d_instruction><![CDATA[]]></d_instruction><broker_id><![CDATA[]]></broker_id><transaction_type><![CDATA[FOR PLANT]]></transaction_type><vat><![CDATA[]]></vat><branch_code><![CDATA[B0001]]></branch_code><valid_from><![CDATA[]]></valid_from><customer_code><![CDATA[C/0000004]]></customer_code><customer_phone><![CDATA[9811414883]]></customer_phone><customer_email><![CDATA[ashishg666@gmail.com]]></customer_email><PO_no><![CDATA[]]></PO_no></sauda_header><sauda_details><sauda_no><![CDATA[FTE000220190923181402]]></sauda_no><Sku_code><![CDATA[12071]]></Sku_code><qty><![CDATA[1]]></qty><TD><![CDATA[0]]></TD><premium><![CDATA[0]]></premium><sale_rate><![CDATA[982.41]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[982.41]]></amount><freight_charge><![CDATA[0.00]]></freight_charge><mrp_code><![CDATA[z40]]></mrp_code><primary_freight ><![CDATA[0.0]]></primary_freight><depot_cost><![CDATA[0.0]]></depot_cost><LIQUID_TD><![CDATA[0]]></LIQUID_TD><BROKERAGE_COST><![CDATA[0.00]]></BROKERAGE_COST><HONEYCOMB_COST><![CDATA[0.00]]></HONEYCOMB_COST><MARGIN_COST><![CDATA[0]]></MARGIN_COST></sauda_details><sauda_details><sauda_no><![CDATA[FTE000220190923181402]]></sauda_no><Sku_code><![CDATA[12072]]></Sku_code><qty><![CDATA[3]]></qty><TD><![CDATA[0]]></TD><premium><![CDATA[0]]></premium><sale_rate><![CDATA[1063.51]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3190.5299999999997]]></amount><freight_charge><![CDATA[0.00]]></freight_charge><mrp_code><![CDATA[z41]]></mrp_code><primary_freight ><![CDATA[0.0]]></primary_freight><depot_cost><![CDATA[0.0]]></depot_cost><LIQUID_TD><![CDATA[0]]></LIQUID_TD><BROKERAGE_COST><![CDATA[0.00]]></BROKERAGE_COST><HONEYCOMB_COST><![CDATA[0.00]]></HONEYCOMB_COST><MARGIN_COST><![CDATA[0]]></MARGIN_COST></sauda_details><sauda_details><sauda_no><![CDATA[FTE000220190923181402]]></sauda_no><Sku_code><![CDATA[12100]]></Sku_code><qty><![CDATA[8]]></qty><TD><![CDATA[0]]></TD><premium><![CDATA[0]]></premium><sale_rate><![CDATA[1234.51]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[9876.08]]></amount><freight_charge><![CDATA[0.00]]></freight_charge><mrp_code><![CDATA[z42]]></mrp_code><primary_freight ><![CDATA[0.0]]></primary_freight><depot_cost><![CDATA[0.0]]></depot_cost><LIQUID_TD><![CDATA[0]]></LIQUID_TD><BROKERAGE_COST><![CDATA[0.00]]></BROKERAGE_COST><HONEYCOMB_COST><![CDATA[0.00]]></HONEYCOMB_COST><MARGIN_COST><![CDATA[0]]></MARGIN_COST></sauda_details><sauda_details><sauda_no><![CDATA[FTE000220190923181402]]></sauda_no><Sku_code><![CDATA[12081]]></Sku_code><qty><![CDATA[25]]></qty><TD><![CDATA[0]]></TD><premium><![CDATA[0]]></premium><sale_rate><![CDATA[1115.41]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[27885.250000000004]]></amount><freight_charge><![CDATA[0.00]]></freight_charge><mrp_code><![CDATA[z59]]></mrp_code><primary_freight ><![CDATA[0.0]]></primary_freight><depot_cost><![CDATA[0.0]]></depot_cost><LIQUID_TD><![CDATA[0]]></LIQUID_TD><BROKERAGE_COST><![CDATA[0.00]]></BROKERAGE_COST><HONEYCOMB_COST><![CDATA[0.00]]></HONEYCOMB_COST><MARGIN_COST><![CDATA[0]]></MARGIN_COST></sauda_details><sauda_details><sauda_no><![CDATA[FTE000220190923181402]]></sauda_no><Sku_code><![CDATA[12073]]></Sku_code><qty><![CDATA[100]]></qty><TD><![CDATA[0]]></TD><premium><![CDATA[0]]></premium><sale_rate><![CDATA[610.73]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[61073.0]]></amount><freight_charge><![CDATA[0.00]]></freight_charge><mrp_code><![CDATA[z12]]></mrp_code><primary_freight ><![CDATA[0.0]]></primary_freight><depot_cost><![CDATA[0.0]]></depot_cost><LIQUID_TD><![CDATA[0]]></LIQUID_TD><BROKERAGE_COST><![CDATA[0.00]]></BROKERAGE_COST><HONEYCOMB_COST><![CDATA[0.00]]></HONEYCOMB_COST><MARGIN_COST><![CDATA[0]]></MARGIN_COST></sauda_details></saudadata></sauda></root>";*/

//For insertion of fetched xml
if($nick_name=='EMAMIT' || strtoupper($nick_name)=='EMAMI' || strtoupper($nick_name)=='ASL' )
{
	$body_xml=str_replace("'",'"',$body);
	$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
	mysqli_query($link,$sqlinsert_xml_data);	

}

$attendance_emp_code = "*ROOT*ATTENDANCE*LOCATION*EMP_CODE";
$attendance_trans_id = "*ROOT*ATTENDANCE*LOCATION*TRANS_ID";
$attendance_latt = "*ROOT*ATTENDANCE*LOCATION*LATT";
$attendance_longi = "*ROOT*ATTENDANCE*LOCATION*LONGI";
$attendance_date = "*ROOT*ATTENDANCE*LOCATION*DATE";
$attendancedata_emp_code = "*ROOT*ATTENDANCE*ATTENDANCEDATA*EMP_CODE";
$attendancedata_date = "*ROOT*ATTENDANCE*ATTENDANCEDATA*DATE";

$sauda_emp_code="*ROOT*SAUDA*LOCATION*EMP_CODE";
$sauda_trans_id = "*ROOT*SAUDA*LOCATION*TRANS_ID";
$sauda_latt = "*ROOT*SAUDA*LOCATION*LATT";
$sauda_longi = "*ROOT*SAUDA*LOCATION*LONGI";
$sauda_date="*ROOT*SAUDA*LOCATION*DATE";
$saudadataheader_sauda_no = "*ROOT*SAUDA*SAUDADATA*SAUDA_HEADER*SAUDA_NO";
$saudadataheader_customer_code = "*ROOT*SAUDA*SAUDADATA*SAUDA_HEADER*CUSTOMER_CODE";
$saudadataheader_TD = "*ROOT*SAUDA*SAUDADATA*SAUDA_HEADER*TD";
$saudadataheader_d_instruction = "*ROOT*SAUDA*SAUDADATA*SAUDA_HEADER*D_INSTRUCTION";
$saudadataheader_broker_id = "*ROOT*SAUDA*SAUDADATA*SAUDA_HEADER*BROKER_ID";
$saudadataheader_transaction_type = "*ROOT*SAUDA*SAUDADATA*SAUDA_HEADER*TRANSACTION_TYPE";
$saudadataheader_VAT= "*ROOT*SAUDA*SAUDADATA*SAUDA_HEADER*VAT";
$saudadataheader_branch_code = "*ROOT*SAUDA*SAUDADATA*SAUDA_HEADER*BRANCH_CODE";
$saudadataheader_sauda_valid_from = "*ROOT*SAUDA*SAUDADATA*SAUDA_HEADER*VALID_FROM";
$saudadataheader_customer_phone = "*ROOT*SAUDA*SAUDADATA*SAUDA_HEADER*CUSTOMER_PHONE";
$saudadataheader_customer_email = "*ROOT*SAUDA*SAUDADATA*SAUDA_HEADER*CUSTOMER_EMAIL";
$saudadataheader_PO_no = "*ROOT*SAUDA*SAUDADATA*SAUDA_HEADER*PO_NO";

$saudadatadetails_sauda_no = "*ROOT*SAUDA*SAUDADATA*SAUDA_DETAILS*SAUDA_NO";
$saudadatadetails_sku_code = "*ROOT*SAUDA*SAUDADATA*SAUDA_DETAILS*SKU_CODE";
$saudadatadetails_qty = "*ROOT*SAUDA*SAUDADATA*SAUDA_DETAILS*QTY";
$saudadatadetails_TD = "*ROOT*SAUDA*SAUDADATA*SAUDA_DETAILS*TD";
$saudadatadetails_PREMIUM = "*ROOT*SAUDA*SAUDADATA*SAUDA_DETAILS*PREMIUM";
$saudadatadetails_sale_rate = "*ROOT*SAUDA*SAUDADATA*SAUDA_DETAILS*SALE_RATE";
$saudadatadetails_VAT = "*ROOT*SAUDA*SAUDADATA*SAUDA_DETAILS*VAT";
$saudadatadetails_amount = "*ROOT*SAUDA*SAUDADATA*SAUDA_DETAILS*AMOUNT";
$saudadatadetails_mrp_code = "*ROOT*SAUDA*SAUDADATA*SAUDA_DETAILS*MRP_CODE";
$saudadatadetails_freight_charge = "*ROOT*SAUDA*SAUDADATA*SAUDA_DETAILS*FREIGHT_CHARGE";
$saudadatadetails_primary_freight = "*ROOT*SAUDA*SAUDADATA*SAUDA_DETAILS*PRIMARY_FREIGHT";
$saudadatadetails_depot_cost = "*ROOT*SAUDA*SAUDADATA*SAUDA_DETAILS*DEPOT_COST";
$saudadatadetails_liquid_TD = "*ROOT*SAUDA*SAUDADATA*SAUDA_DETAILS*LIQUID_TD";
$saudadatadetails_brokerage_cost = "*ROOT*SAUDA*SAUDADATA*SAUDA_DETAILS*BROKERAGE_COST";
$saudadatadetails_honeycomb_cost = "*ROOT*SAUDA*SAUDADATA*SAUDA_DETAILS*HONEYCOMB_COST";
$saudadatadetails_margin_cost = "*ROOT*SAUDA*SAUDADATA*SAUDA_DETAILS*MARGIN_COST";

$attendance_array = array();
$sauda_array=array();
$sauda_details_array=array();

$counter = 0;
$countersauda=0;
$countersaudadetails=0;

class xml_attendance{
    var $emp_code, $trans_id,$latt,$longi,$attendance_date,$attendancedata_emp_code,$attendancedata_date;
}
class xml_sauda{
	var $sauda_emp_code,$sauda_trans_id,$sauda_latt,$sauda_longi,$sauda_date,$saudadataheader_sauda_no,$saudadataheader_customer_code,$saudadataheader_TD,$saudadataheader_d_instruction,$saudadataheader_broker_id,$saudadataheader_transaction_type,$saudadataheader_VAT,$saudadataheader_branch_code,$saudadataheader_sauda_valid_from,$saudadataheader_customer_phone,$saudadataheader_customer_email,$saudadataheader_PO_no;	
}
class xml_sauda_details{
	var $saudadatadetails_sauda_no,$saudadatadetails_sku_code,$saudadatadetails_qty,$saudadatadetails_TD,$saudadatadetails_PREMIUM,$saudadatadetails_sale_rate,$saudadatadetails_VAT,$saudadatadetails_amount,$saudadatadetails_mrp_code,$saudadatadetails_freight_charge,$saudadatadetails_primary_freight,$saudadatadetails_depot_cost,$saudadatadetails_liquid_TD,$saudadatadetails_brokerage_cost,$saudadatadetails_honeycomb_cost,$saudadatadetails_margin_cost;
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
    global $current_tag, $attendance_emp_code, $attendance_trans_id,$attendance_latt,$attendance_longi,$attendance_date,$attendancedata_emp_code,$attendancedata_date, $counter, $countersauda,$countersaudadetails,$counterpayment,$counterpaymentdetails,$attendance_array,$sauda_array,$sauda_details_array,
	$sauda_emp_code,$sauda_trans_id,$sauda_latt,$sauda_longi,$sauda_date,$saudadataheader_sauda_no,$saudadataheader_customer_code,$saudadataheader_TD,
	$saudadataheader_d_instruction,$saudadataheader_broker_id,$saudadataheader_transaction_type,$saudadataheader_VAT,$saudadataheader_branch_code,$saudadataheader_sauda_valid_from,$saudadataheader_customer_phone,$saudadataheader_customer_email,$saudadataheader_PO_no,$saudadatadetails_sauda_no,$saudadatadetails_sku_code,$saudadatadetails_qty,$saudadatadetails_TD,$saudadatadetails_PREMIUM,$saudadatadetails_sale_rate,$saudadatadetails_VAT,$saudadatadetails_amount,$saudadatadetails_mrp_code,$saudadatadetails_freight_charge,$saudadatadetails_primary_freight,$saudadatadetails_depot_cost,$saudadatadetails_liquid_TD,$saudadatadetails_brokerage_cost,$saudadatadetails_honeycomb_cost,$saudadatadetails_margin_cost,$saudadatadetails_PO_no;
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
	if(substr($current_tag,0,11)=='*ROOT*SAUDA')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $sauda_emp_code:
				$sauda_array[$countersauda] = new xml_sauda();
				$sauda_array[$countersauda]->sauda_emp_code = $data;
				break;
			case $sauda_trans_id:
				$sauda_array[$countersauda]->sauda_trans_id = $data;
				break;
			case $sauda_latt:
				$sauda_array[$countersauda]->sauda_latt = $data;
				break;
			case $sauda_longi:
				$sauda_array[$countersauda]->sauda_longi = $data;
				break;
			case $sauda_date:
				$sauda_array[$countersauda]->sauda_date = $data;
				break;
			case $saudadataheader_sauda_no:
				$sauda_array[$countersauda]->saudadataheader_sauda_no = $data;
				break;
			case $saudadataheader_TD:
				$sauda_array[$countersauda]->saudadataheader_TD = $data;
				break;
			case $saudadataheader_d_instruction:
				$sauda_array[$countersauda]->saudadataheader_d_instruction = $data;
				break;
			case $saudadataheader_broker_id:
				$sauda_array[$countersauda]->saudadataheader_broker_id = $data;
				break;
			case $saudadataheader_transaction_type:
				$sauda_array[$countersauda]->saudadataheader_transaction_type = $data;
				break;
			case $saudadataheader_VAT:
				$sauda_array[$countersauda]->saudadataheader_VAT = $data;
				break;
			case $saudadataheader_branch_code:
				$sauda_array[$countersauda]->saudadataheader_branch_code = $data;
				break;
			case $saudadataheader_sauda_valid_from:
				$sauda_array[$countersauda]->saudadataheader_sauda_valid_from = $data;
				break;	
			case $saudadataheader_customer_code:
				$sauda_array[$countersauda]->saudadataheader_customer_code = $data;
				break;
			case $saudadataheader_customer_phone:
				$sauda_array[$countersauda]->saudadataheader_customer_phone = $data;
				break;
			case $saudadataheader_customer_email:
				$sauda_array[$countersauda]->saudadataheader_customer_email = $data;
				break;
			case $saudadataheader_PO_no:
				$sauda_array[$countersauda]->saudadataheader_PO_no = $data;
				$countersauda++;
				break;				
		}
	}
	if(substr($current_tag,0,35)=='*ROOT*SAUDA*SAUDADATA*SAUDA_DETAILS')
		{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';			
			switch($current_tag){
				case $saudadatadetails_sauda_no:
					$sauda_details_array[$countersaudadetails] = new xml_sauda_details();
					$sauda_details_array[$countersaudadetails]->saudadatadetails_sauda_no = $data;
					break;
				case $saudadatadetails_sku_code:
					$sauda_details_array[$countersaudadetails]->saudadatadetails_sku_code = $data;
					break;
				case $saudadatadetails_qty:
					$sauda_details_array[$countersaudadetails]->saudadatadetails_qty = $data;
					break;
				case $saudadatadetails_TD:
					$sauda_details_array[$countersaudadetails]->saudadatadetails_TD = $data;
					break;
				case $saudadatadetails_PREMIUM:
					$sauda_details_array[$countersaudadetails]->saudadatadetails_PREMIUM = $data;
					break;						
				case $saudadatadetails_sale_rate:
					$sauda_details_array[$countersaudadetails]->saudadatadetails_sale_rate = $data;
					break;
				case $saudadatadetails_VAT:
					$sauda_details_array[$countersaudadetails]->saudadatadetails_VAT = $data;
					break;
				case $saudadatadetails_amount:
					$sauda_details_array[$countersaudadetails]->saudadatadetails_amount = $data;
					break;
				case $saudadatadetails_freight_charge:
					$sauda_details_array[$countersaudadetails]->saudadatadetails_freight_charge = $data;
					break;
				case $saudadatadetails_mrp_code:
					$sauda_details_array[$countersaudadetails]->saudadatadetails_mrp_code = $data;
					break;	
				case $saudadatadetails_primary_freight:
					$sauda_details_array[$countersaudadetails]->saudadatadetails_primary_freight = $data;
					break;
				case $saudadatadetails_depot_cost:
					$sauda_details_array[$countersaudadetails]->saudadatadetails_depot_cost = $data;
					break;
				case $saudadatadetails_liquid_TD:
					$sauda_details_array[$countersaudadetails]->saudadatadetails_liquid_TD = $data;
					break;
				case $saudadatadetails_brokerage_cost:
					$sauda_details_array[$countersaudadetails]->saudadatadetails_brokerage_cost = $data;
					break;
				case $saudadatadetails_honeycomb_cost:
					$sauda_details_array[$countersaudadetails]->saudadatadetails_honeycomb_cost = $data;
					break;
				case $saudadatadetails_margin_cost:
					$sauda_details_array[$countersaudadetails]->saudadatadetails_margin_cost = $data;
					$countersaudadetails++;
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
//print_r($sauda_array);
//print_r($sauda_details_array);
//print_r($payment_array);
mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");

$flag=1;
/* --------------------------------------------------START QUERY FOR ATTENDANCE---------------------------------------------------------------------*/
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
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
			
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
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
				 	$sqlempname="SELECT emp_name,vertical_value,branch_code FROM employee_master WHERE emp_code='".$emp_code."'";
					$rsempname=mysqli_query($link,$sqlempname);
					$rowempname=mysqli_fetch_assoc($rsempname);
					$emp_name=$rowempname['emp_name'];
					$vertical_value=$rowempname['vertical_value'];
					$branch_code=$rowempname['branch_code'];
					
					if(branch_vertical_operation_wise_email=='yes')
					{
						$operation_type='Attendance';
						$attendance_email=fetch_corresponding_emails($operation_type,$vertical_value,$branch_code);
					}
					else
					{
						$attendance_email=ATTENDANCEEMAILRECIPENTS;
					}
					$address=getReverseGeo($latt,$longi);
					$attendanceemailsubj="$nick_name - Attendance - ".$emp_name." on ".date('d-m-Y',strtotime($attendance_date))." @".date('H:i:s',strtotime($attendance_date)).' hrs.';
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
					if(mail($attendance_email, $attendanceemailsubj, $attendancemailbody, $headers,$spam_filter))
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
/* --------------------START QUERY FOR SAUDA----------------------------------------------------------------------------------------------------------------*/
$sauda_array_trans_id=array();
$saudaheader_array_mailbody=array();
$saudainstruction_array_mailbody=array();
$saudadetails_array_mailbody=array();
$saudaemp_array_mailbody=array();
$saudaempcode_array_mailbody=array();
$saudacustomer_mapped_empcode_array_mailbody=array();
$saudatansid_array_mailbody=array();
$saudacashreceived_array_mailbody=array();
$saudadate_array_mailbody=array();
$saudadataheader_TD_array=array();
$saudadataheader_transaction_type_array=array();
$saudavalidfrom_array_mailbody=array();
$saudacustomername_array_smsbody=array();
$saudacustomercode_array_smsbody=array();
$saudabranchcode_array_smsbody=array();
$saudabranchname_array_smsbody=array();
$saudacustomerphoneno_array_smsbody=array();
$saudavalidityperiod_array_smsbody=array();
$saudavertical_array_smsbody=array();
$saudaroute_array=array();

//print_r($sauda_array);
if(count($sauda_array)>0)
{
	for($x=0;$x<count($sauda_array);$x++){
		$sauda_emp_code=$sauda_array[$x]->sauda_emp_code;
		$sauda_trans_id=$sauda_array[$x]->sauda_trans_id;
		$sauda_latt=$sauda_array[$x]->sauda_latt;
		$sauda_longi=$sauda_array[$x]->sauda_longi;
		$sauda_date=$sauda_array[$x]->sauda_date;
		$saudadataheader_sauda_no=$sauda_array[$x]->saudadataheader_sauda_no;
		$saudadataheader_customer_code=$sauda_array[$x]->saudadataheader_customer_code;
		$saudadataheader_TD=$sauda_array[$x]->saudadataheader_TD;
		$saudadataheader_PREMIUM=$sauda_array[$x]->saudadataheader_PREMIUM;
		$saudadataheader_transaction_type=$sauda_array[$x]->saudadataheader_transaction_type;
		$saudadataheader_d_instruction=$sauda_array[$x]->saudadataheader_d_instruction;
		$saudadataheader_VAT=$sauda_array[$x]->saudadataheader_VAT;
		$saudadataheader_broker_id=$sauda_array[$x]->saudadataheader_broker_id;
		$saudadataheader_branch_code=$sauda_array[$x]->saudadataheader_branch_code;
		${saudadataheader_transaction_type.$saudadataheader_sauda_no}=$saudadataheader_transaction_type;
		$saudadataheader_sauda_valid_from=$sauda_array[$x]->saudadataheader_sauda_valid_from;
		$saudadataheader_sauda_valid_from=date('Y-m-d');
		$saudadataheader_customer_phone=$sauda_array[$x]->saudadataheader_customer_phone;
		$saudadataheader_customer_email=$sauda_array[$x]->saudadataheader_customer_email;
		$saudadataheader_PO_no=$sauda_array[$x]->saudadataheader_PO_no;	
		${product_group_code_array.$saudadataheader_sauda_no}=array();
		${sauda_date.$saudadataheader_sauda_no}=$sauda_date;
		${sauda_customer_code.$saudadataheader_sauda_no}=$saudadataheader_customer_code;
		
		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if($sauda_latt>0 && $sauda_longi>0)
		{
			$sqlupdatelatlongzero="UPDATE location SET latt='".$sauda_latt."',longi='".$sauda_longi."' WHERE 
									emp_code='".$sauda_emp_code."' AND latt='0' AND longi='0'";
			$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
		}
		//For checking that trans id exist or not for sauda
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$sauda_trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check sauda location: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for sauda
		if($countchkorlocation>0)
		{
			//$sauda_trans_id_chk=substr($sauda_trans_id,1,19);
			if(!in_array($sauda_trans_id,$sauda_array_trans_id))
			{
				array_push($sauda_array_trans_id,$sauda_trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$sauda_emp_code."',
									latt='".$sauda_latt."',
									longi='".$sauda_longi."'
									WHERE trans_id='".$sauda_trans_id."'";
			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update sauda location: ".$sqlupdateorlocation);
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
			$sqlempname="SELECT emp_name,branch_code,vertical_value,reporting_to,designation FROM employee_master WHERE emp_code='".$sauda_emp_code."'";
			$rsempname=mysqli_query($link,$sqlempname);
			$rowempname=mysqli_fetch_assoc($rsempname);
			$emp_name=$rowempname['emp_name'];
			$branch_code=$rowempname['branch_code'];
			$vertical_value=$rowempname['vertical_value'];
			${reporting_to.$saudadataheader_sauda_no}=$rowempname['reporting_to'];
			${designation.$saudadataheader_sauda_no}=$rowempname['designation'];
			${vertical_value.$saudadataheader_sauda_no}=$vertical_value;
			
			// create the data for location table date field , by checking the current date and time and the actual date and time of sauda
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));

			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			//For Insert into the location table for new trans id regarding sauda
			$sqlinsertorlocation="INSERT INTO location SET emp_code='".$sauda_emp_code."',
									trans_id='".$sauda_trans_id."',
									latt='".$sauda_latt."',
									longi='".$sauda_longi."',
									date='".$sauda_date."',
									updatetime='".$location_date."'"; 
			
			//For Insert into the Sauda Header table for new trans id
			$sqlinsertsaudaheader="INSERT INTO sauda_header SET sauda_no='".$saudadataheader_sauda_no."',
								  customer_code 	='".$saudadataheader_customer_code."',
								  branch_code		='".$saudadataheader_branch_code."',
								  TD				='".$saudadataheader_TD."',
								  broker_id			='".$saudadataheader_broker_id."',
								  VAT          		='".$saudadataheader_VAT."',
								  transaction_type  ='".${saudadataheader_transaction_type.$saudadataheader_sauda_no}."',
								  sauda_valid_from  ='".$saudadataheader_sauda_valid_from."',
								   PO_no  ='".$saudadataheader_PO_no."',
								  d_instruction		='".addslashes($saudadataheader_d_instruction)."'";   
			if(mysqli_query($link,$sqlinsertorlocation) && mysqli_query($link,$sqlinsertsaudaheader))
			{
				$flag=5;
				//For update customer email and phone
				if($saudadataheader_customer_phone!='' && $saudadataheader_customer_email!='')
				{
					$sqlupdatecustomer="UPDATE customer_master SET email='".$saudadataheader_customer_email."',
										phone_no='".$saudadataheader_customer_phone."' WHERE customer_code='".$saudadataheader_customer_code."'";
					if(mysqli_query($link,$sqlupdatecustomer))
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
			else
			{
				mysqli_query($link,"ROLLBACK");
				echo $flag=0;
				return;
			}	
			//Regarding Email Sending
			if(branch_vertical_operation_wise_email=='yes')
			{
				$operation_type='Sauda';
				$sauda_email=fetch_corresponding_emails($operation_type,$vertical_value,$branch_code);
			}
			else
			{
				$sauda_email=SAUDAEMAILRECIPENTS;
			}
			$addresssauda=getReverseGeo($sauda_latt,$sauda_longi);
				
			$sqlcustomername="SELECT customer_name,emp_code,phone_no,sauda_validity_period,dns_customer_code,route_code FROM customer_master 
							WHERE customer_code='".$saudadataheader_customer_code."'";
			$rscustomername=mysqli_query($link,$sqlcustomername);
			$rowcustomername=mysqli_fetch_assoc($rscustomername);
			$customer_name=$rowcustomername['customer_name'];
			$customer_phone_no=$rowcustomername['phone_no'];
			$sauda_validity_period=$rowcustomername['sauda_validity_period'];
			$customer_mapped_emp_code=$rowcustomername['emp_code'];
			$dns_customer_code=$rowcustomername['dns_customer_code'];
			${sauda_dns_customer_code.$saudadataheader_sauda_no}=$dns_customer_code;
			$route_code=$rowcustomername['route_code'];
			
			$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$route_code."'";
			$rsroutename=mysqli_query($link,$sqlroutename);
			$rowroutename=mysqli_fetch_assoc($rsroutename);
			$route_name=$rowroutename['route_name'];
			
			$saudaheader_TR="<th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Customer Name</span></strong></th>";
			$saudaheader_TD="<td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$customer_name."</span>&nbsp;</td>";
			//-------------------------------------------For Sauda depot Feature-----------------------------------------------------------------------------
			
			if(sauda_allocation=='yes' && sauda_depot_wise=='yes')
			{
				$sqlbrachname="SELECT branch_name FROM branch_master WHERE branch_code='".$saudadataheader_branch_code."'";
				$rsbrachname=mysqli_query($link,$sqlbrachname);
				$rowbrachname=mysqli_fetch_assoc($rsbrachname);
				$branch_name=$rowbrachname['branch_name'];
				
				$saudaheader_depot_TR="<th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Depot</span></strong></th>";
				$saudaheader_depot_TD="<td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$branch_name."</span>&nbsp;</td>";
			}
			//-------------------------------------------End For Sauda depot Feature-----------------------------------------------------------------------------
				$transaction_type_details='Sauda';
				if($saudadataheader_broker_id!='')
				{
					$sqlbroker="SELECT broker_name FROM broker_master WHERE broker_id='".$saudadataheader_broker_id."'";
					$rsbroker=mysqli_query($link,$sqlbroker);
					$rowbroker=mysqli_fetch_assoc($rsbroker);
					$broker_name=$rowbroker['broker_name'];

					$saudaheader_TR_broker="<th style='width:220px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Broker</span></strong></th>";
					$saudaheader_TD_broker="<td style='width:220px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$broker_name."</span>&nbsp;</td>";
				}
				else
				{
					$saudaheader_TR_broker="";
					$saudaheader_TD_broker="";
				}
				
				//For sending email to recipents in case of No activity transaction happened
				$last_operation_datetime=$sauda_date;
				if(substr($sauda_trans_id,0,1)=='N')
				{
					$nosaudaemailsubj="Activiy without Transaction ".$emp_name. " on ".date('d-m-Y',strtotime($sauda_date))." @".date('H:i:s',strtotime($sauda_date)).' hrs.';

					$nosaudaemailbody = "<html><head><title>No Sauda</title></head>
										<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
										.$emp_name. "</b><br><br>".$emp_name." visited ".$customer_name." at ".$addresssauda.". 
										No sauda happened. </table><br><br>
										<b>Remarks: </b> ".strtoupper($saudadataheader_d_instruction)."
										<br><br><br>Powered By aceDNS<br></body></html>";
					$headers  = "MIME-Version: 1.0\r\n";
					$headers .= "Content-type: text/html; charset=UTF-8\n";
					$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
								"Reply-To:".FROMEMAIL." \r\n" .
								"Bcc: ".BCCEMAIL." \r\n".
								'X-Mailer: PHP/' . phpversion();
					if(mail($sauda_email,$nosaudaemailsubj, $nosaudaemailbody, $headers,$spam_filter))
					{
						$flag=5;
					}
					else
					{
						mysqli_query($link,"ROLLBACK");
						echo $flag=0;
						return;
					}
				}// End of if block for No activity transaction checking
				else{
				//For constructing the email body for SAUDA HEADER if SAUDA has performed
				$saudaemailbody = "<html><head><title>Sauda Details</title></head>
							<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
							.$emp_name. "</b><br /><br />Refference no: <b>".$saudadataheader_sauda_no."</b><br /><br /><table border=1 style=background-color:AliceBlue>
							<tr>".$saudaheader_TR.$saudaheader_depot_TR.$saudaheader_TR_broker."
							<th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
							CE'>Date & Time</span></strong></th>
							<th style='width:140px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
							CE'>Transaction Type</span></strong></th>
							</tr><tr>".$saudaheader_TD.$saudaheader_depot_TD.$saudaheader_TD_broker."
							<td style='width:165px;text-align:right;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>".date('d-m-Y H:i:s',strtotime($sauda_date))."</span>&nbsp;</td>
							<td style='width:140px;text-align:right;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>".$transaction_type_details."</span>&nbsp;</td>
							</tr></table>";
							array_push($saudaheader_array_mailbody,$saudaemailbody);
							$saudainstructionemailbody="Delivery Instruction: <b>".strtoupper($saudadataheader_d_instruction)."</b>";
							$saudavalidfrommailbody="Sauda Valid From: <b>".date('d-m-Y',strtotime($saudadataheader_sauda_valid_from))."</b>";
							
							array_push($saudadataheader_TD_array,$saudadataheader_TD);
							array_push($saudainstruction_array_mailbody,$saudainstructionemailbody);
							array_push($saudaemp_array_mailbody,$emp_name);
							array_push($saudaempcode_array_mailbody,$sauda_emp_code);
							array_push($saudadate_array_mailbody,$sauda_date);
							array_push($saudatansid_array_mailbody,$sauda_trans_id);
							array_push($saudadataheader_transaction_type_array,$saudadataheader_transaction_type);
							array_push($saudavalidfrom_array_mailbody,$saudavalidfrommailbody);
							array_push($saudacustomer_mapped_empcode_array_mailbody,$customer_mapped_emp_code);
							array_push($saudacustomername_array_smsbody,$customer_name);
							array_push($saudacustomercode_array_smsbody,$saudadataheader_customer_code);
							array_push($saudabranchcode_array_smsbody,$saudadataheader_branch_code);
							array_push($saudabranchname_array_smsbody,$branch_name);
							array_push($saudacustomerphoneno_array_smsbody,$customer_phone_no);
							array_push($saudavalidityperiod_array_smsbody,$sauda_validity_period);
							array_push($saudavertical_array_smsbody,$vertical_value);
							array_push($saudaroute_array,$route_name);
							
							${TDvalidationskupartarray.$saudadataheader_sauda_no}=array();
					}
		 }//End of else
	}// End for loop
	//For Insert into the Sauda Details table for new trans id
	//print_r($sauda_details_array);
	//exit();
		if(count($sauda_details_array)>0)
		{
			for($i=0;$i<count($sauda_details_array);$i++){
				$saudadatadetails_sauda_no=$sauda_details_array[$i]->saudadatadetails_sauda_no;
				$saudadatadetails_sku_code=$sauda_details_array[$i]->saudadatadetails_sku_code;
				$saudadatadetails_qty=$sauda_details_array[$i]->saudadatadetails_qty;
				$saudadatadetails_TD=$sauda_details_array[$i]->saudadatadetails_TD;
				$saudadatadetails_PREMIUM=$sauda_details_array[$i]->saudadatadetails_PREMIUM;
				$saudadatadetails_VAT=$sauda_details_array[$i]->saudadatadetails_VAT;
				$saudadatadetails_mrp_code=$sauda_details_array[$i]->saudadatadetails_mrp_code;
				$saudadatadetails_amount=$sauda_details_array[$i]->saudadatadetails_amount;
				$saudadatadetails_sale_rate=$sauda_details_array[$i]->saudadatadetails_sale_rate;
				$saudadatadetails_freight_charge=$sauda_details_array[$i]->saudadatadetails_freight_charge;
				$saudadatadetails_primary_freight=$sauda_details_array[$i]->saudadatadetails_primary_freight;
				$saudadatadetails_depot_cost=$sauda_details_array[$i]->saudadatadetails_depot_cost;
				$saudadatadetails_liquid_TD=$sauda_details_array[$i]->saudadatadetails_liquid_TD;
				$saudadatadetails_brokerage_cost=$sauda_details_array[$i]->saudadatadetails_brokerage_cost;
				$saudadatadetails_honeycomb_cost=$sauda_details_array[$i]->saudadatadetails_honeycomb_cost;
				$saudadatadetails_margin_cost=$sauda_details_array[$i]->saudadatadetails_margin_cost;
				$sqlmrpdetails="SELECT mrp,sale_rate,basic_rate FROM sauda_mrp WHERE product_code='".$saudadatadetails_sku_code."' 
								AND mrp_code='".$saudadatadetails_mrp_code."'";
				$rsmrpdetails=mysqli_query($link,$sqlmrpdetails);
				$recmrpdetails=mysqli_fetch_assoc($rsmrpdetails);
				$mrp=$recmrpdetails['mrp'];
				$sale_rate_db=$recmrpdetails['sale_rate'];
				$basic_rate_db=$recmrpdetails['basic_rate'];
				
					if(no_of_filter==1){
						$sqlproductdetails="SELECT prod_code,dns_prod_code,prod_desc,UOM1,UOM2,UOM3,conversion_factor,conversion_factor_two,branch_code FROM product_master WHERE prod_code='".$saudadatadetails_sku_code."'";
					}
					if(no_of_filter==2){
						$sqlproductdetails="SELECT PGM.product_group_name,PGM.product_group_code,PM.prod_desc,PM.UOM1,PM.UOM2,PM.UOM3,PM.conversion_factor,PM.conversion_factor_two,PM.dns_prod_code,PM.branch_code FROM product_master PM,product_group_master PGM 
											WHERE PM.product_group_code=PGM.product_group_code AND PM.prod_code='".$saudadatadetails_sku_code."'";
					}
					if(no_of_filter==3){
						$sqlproductdetails="SELECT PGM.product_group_name,PGM.product_group_code,PSGM.product_sub_group_name,PSGM.product_sub_group_code,PM.prod_desc,PM.UOM1,
											PM.UOM2,PM.UOM3,PM.conversion_factor,PM.conversion_factor_two,PM.dns_prod_code,PM.branch_code FROM 
											product_master PM,product_group_master PGM,product_sub_group_master PSGM
											WHERE PM.product_group_code=PGM.product_group_code AND PM.product_sub_group_code=PSGM.product_sub_group_code 
											AND PM.prod_code='".$saudadatadetails_sku_code."'";
					}
					if(no_of_filter==4){
						$sqlproductdetails="SELECT PGM.product_group_name,PGM.product_group_code,PSGM.product_sub_group_name,PSGM.product_sub_group_code,PBM.product_brand_name,PBM.product_brand_code,PM.prod_desc,
											PM.UOM1,PM.UOM2,PM.UOM3,PM.conversion_factor,PM.conversion_factor_two,PM.dns_prod_code,PM.branch_code 
											FROM  product_master PM,product_group_master PGM,product_sub_group_master PSGM,product_brand_master PBM
											WHERE PM.product_group_code=PGM.product_group_code AND PM.product_sub_group_code=PSGM.product_sub_group_code
											AND PM.product_brand_code=PBM.product_brand_code AND PM.prod_code='".$saudadatadetails_sku_code."'";
					}
					$rsproductdetails=mysqli_query($link,$sqlproductdetails);
					$rowproductdetails=mysqli_fetch_assoc($rsproductdetails);
					$prod_code=$rowproductdetails['prod_code'];
					$prod_desc=$rowproductdetails['prod_desc'];
					$product_group_code=$rowproductdetails['product_group_code'];
					$product_group_name=$rowproductdetails['product_group_name'];
					$product_sub_group_code=$rowproductdetails['product_sub_group_code'];
					$product_sub_group_name=$rowproductdetails['product_sub_group_name'];
					$product_brand_code=$rowproductdetails['product_brand_code'];
					$product_brand_name=$rowproductdetails['product_brand_name'];
					$UOM1=$rowproductdetails['UOM1'];
					$UOM2=$rowproductdetails['UOM2'];
					$UOM3=$rowproductdetails['UOM3'];
					$conversion_factor=$rowproductdetails['conversion_factor'];
					$conversion_factor_two=$rowproductdetails['conversion_factor_two'];
					$dns_prod_code=$rowproductdetails['dns_prod_code'];
					$branch_code=$rowproductdetails['branch_code'];
					
					$sqlmargincostprodwise="SELECT margin_cost,auth_one_limit FROM margin_cost WHERE dns_prod_code='".$dns_prod_code."'
							AND branch_code='".$branch_code."' AND datetime <='".${sauda_date.$saudadatadetails_sauda_no}."' ORDER BY datetime DESC LIMIT 0,1";
					$rsmargincostprodwise=mysqli_query($link,$sqlmargincostprodwise);
					$rowmargincostprodwise=mysqli_fetch_assoc($rsmargincostprodwise);
					$margin_cost=round($rowmargincostprodwise['margin_cost'],2);
					$auth_one_limit=$rowmargincostprodwise['auth_one_limit'];
					if($margin_cost=='')       	  $margin_cost=0;
					if($auth_one_limit=='')       $auth_one_limit=0;
					
					$convert_qty_one=round(($saudadatadetails_qty*$conversion_factor),3);
					$convert_qty_two=round((($saudadatadetails_qty*$conversion_factor)/$conversion_factor_two),3);
					
					//print_r($sauda_array_trans_id);
					if(!in_array($saudadatadetails_sauda_no,$sauda_array_trans_id))
					{
						$sqlinsertsaudadetails="INSERT INTO sauda_details SET sauda_no='".$saudadatadetails_sauda_no."',
												sku_code 	='".$saudadatadetails_sku_code."',
												qty			='".$saudadatadetails_qty."',
												convert_qty_one	='".$convert_qty_one."',
												convert_qty_two	='".$convert_qty_two."',
												TD			='".$saudadatadetails_TD."',
												premium		='".$saudadatadetails_PREMIUM."',
												sale_rate	='".$saudadatadetails_sale_rate."',
												VAT			='".$saudadatadetails_VAT."',
												freight_charge ='".$saudadatadetails_freight_charge."',
												primary_freight ='".$saudadatadetails_primary_freight."',
												depot_cost 	='".$saudadatadetails_depot_cost."',
												honeycomb_cost 	='".$saudadatadetails_honeycomb_cost."',
												margin_cost 	='".$saudadatadetails_margin_cost."',
												brokerage_cost 	='".$saudadatadetails_brokerage_cost."',
												amount		='".$saudadatadetails_amount."',
												liquid_TD	='".$saudadatadetails_liquid_TD."',
												mrp_code	='".$saudadatadetails_mrp_code."'";
						if(mysqli_query($link,$sqlinsertsaudadetails))
						{
							$flag=5;
							//For sauda limit updation in customer master
							if(${vertical_value.$saudadatadetails_sauda_no}=='HBC:Rasoi:BIB'){
								$sqlupdatecustomersaudalimit="UPDATE customer_sauda_limit SET pending_qty=(pending_qty+$convert_qty_two),
															download_time=CURRENT_TIMESTAMP() WHERE 
															customer_code='".${sauda_dns_customer_code.$saudadatadetails_sauda_no}."'";
								mysqli_query($link,$sqlupdatecustomersaudalimit);
								$sqlupdatecustomer="UPDATE customer_master SET download_time=CURRENT_TIMESTAMP() WHERE 
													dns_customer_code='".${sauda_dns_customer_code.$saudadatadetails_sauda_no}."'";
								mysqli_query($link,$sqlupdatecustomer);
							}
						}					
						else
						{
							mysqli_query($link,"ROLLBACK");
							echo $flag=0;
							return;
						}
						//For constructing the email body for SAUDA DETAILS if SAUDA has performed
						
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
						if(sauda_rate_variable=='yes' && (sauda_rate_variable_value=='BOTH' || sauda_rate_variable_value=='FOR'))
						{
							${freightcharge.$saudadatadetails_sku_code}=$saudadatadetails_freight_charge*$saudadatadetails_qty;
						}
						else
						{
							${freightcharge.$saudadatadetails_sku_code}=0;
						}
						if(premium=='yes' )
						{
							${premiun.$saudadatadetails_sku_code}=$saudadatadetails_PREMIUM*$saudadatadetails_qty;
						}
						else
						{
							${premiun.$saudadatadetails_sku_code}=0;
						}
						
						if(mrp=='yes')
						{
							$totalmrpfreight=($saudadatadetails_qty*$mrp)+${freightcharge.$saudadatadetails_sku_code}+${premiun.$saudadatadetails_sku_code};
						}
						elseif(sale_rate=='yes')
						{
							$totalsaleratefreight=($saudadatadetails_qty*$saudadatadetails_sale_rate)+${freightcharge.$saudadatadetails_sku_code}+${premiun.$saudadatadetails_sku_code};
						}
						if(mrp=='yes' && TD=='yes' && TD_type=='sku wise'){
							if(TD_calc=='percentage')
							{
								$totalmrp=$totalmrpfreight-(($totalmrpfreight*$saudadatadetails_TD)/100);
							}
							else
							{
								if(TD_calc_basedon=='UOM')
								{
									$totalmrp=$totalmrpfreight-($saudadatadetails_qty*$saudadatadetails_TD);
								}
								else
								{
									$totalmrp=$totalmrpfreight-$saudadatadetails_TD;
								}
							}
							${grandtotal.$saudadatadetails_sauda_no}=${grandtotal.$saudadatadetails_sauda_no}+$totalmrp;
							${totalamount.$saudadatadetails_sku_code}=$totalmrp;
						 }
						 if(mrp=='yes' && TD=='no'){
							${grandtotal.$saudadatadetails_sauda_no}=${grandtotal.$saudadatadetails_sauda_no}+$totalmrpfreight;
							${totalamount.$saudadatadetails_sku_code}=$totalmrpfreight;
						 }
						 if(sale_rate=='yes' && TD=='yes' && TD_type=='sku wise'){
							 if(TD_calc=='percentage')
							 {
							 	$totalrate=$totalsaleratefreight-(($totalsaleratefreight*$saudadatadetails_TD)/100);
							 }
							 else
							 {
								if(TD_calc_basedon=='UOM')
								{
									$totalrate=$totalsaleratefreight-($saudadatadetails_qty*$saudadatadetails_TD);
								}
								else
								{
									$totalrate=$totalsaleratefreight-$saudadatadetails_TD;
								}
							 }
							${grandtotal.$saudadatadetails_sauda_no}=${grandtotal.$saudadatadetails_sauda_no}+$totalrate;
							${totalamount.$saudadatadetails_sku_code}=$totalrate;
						 }
						 if(sale_rate=='yes' && TD=='no'){
							${grandtotal.$saudadatadetails_sauda_no}=${grandtotal.$saudadatadetails_sauda_no}+$totalsaleratefreight;
							${totalamount.$saudadatadetails_sku_code}=$totalsaleratefreight;
						 }
						 if(mrp=='yes' && TD=='yes' && (TD_type=='sauda value wise' || TD_type=='customer wise')){
							${grandtotal.$saudadatadetails_sauda_no}=${grandtotal.$saudadatadetails_sauda_no}+$totalmrpfreight;
							${totalamount.$saudadatadetails_sku_code}=$totalmrpfreight;
						 }
						 if(sale_rate=='yes' && TD=='yes' && (TD_type=='sauda value wise' || TD_type=='customer wise')){
							${grandtotal.$saudadatadetails_sauda_no}=${grandtotal.$saudadatadetails_sauda_no}+$totalsaleratefreight;
							${totalamount.$saudadatadetails_sku_code}=$totalsaleratefreight;
						 }
						 //------------------Sauda allocation freight charge effect on amount-----------------------------------------
						/*${totalfreightcharge.$saudadatadetails_sauda_no}=${totalfreightcharge.$saudadatadetails_sauda_no}+${freightcharge.$saudadatadetails_sku_code};
						 if(sauda_allocation=='yes' && sauda_rate_variable=='yes' && $saudadatadetails_freight_charge !='')
						 {
							//${totalamount.$saudadatadetails_sku_code}=${totalamount.$saudadatadetails_sku_code}+${freightcharge.$saudadatadetails_sku_code};
							${grandtotal.$saudadatadetails_sauda_no}=${grandtotal.$saudadatadetails_sauda_no}+${freightcharge.$saudadatadetails_sku_code};
							
						 }*/
						 //------------------End Sauda allocation freight charge effect on amount--------------------------------------
						
						 if(VAT=='yes' && VAT_details=='amount')
						 {
							 ${grandtotal.$saudadatadetails_sauda_no}=${grandtotal.$saudadatadetails_sauda_no}+$saudadatadetails_VAT;
						 }
						 else if(VAT=='yes' && VAT_details=='percentage')
						 {
							 $vat_total=(${grandtotal.$saudadatadetails_sauda_no}*$saudadatadetails_VAT/100);
							 ${grandtotal.$saudadatadetails_sauda_no}=${grandtotal.$saudadatadetails_sauda_no}+$vat_total;
						 }
						 if(TD=='yes' && TD_type=='sku wise')
						 {
							$saudaemailbody_TD_VAL="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".number_format($saudadatadetails_TD,2)."</span>&nbsp;</td>";
						 }
						 else
						 {
							 $saudaemailbody_TD_VAL="";
						 }

						 if(VAT=='yes')
						 {
							$saudaemailbody_VAT_VAL="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>".number_format(round($saudadatadetails_VAT,2),2)."</span>&nbsp;</td>";
						 }
						 else
						 {
							 $saudaemailbody_VAT_VAL="";
						 }
						 if($saudadatadetails_sale_rate=='')
						 {
							$saudadatadetails_sale_rate=0; 
						 }
						 if(amount=='yes')
						 {
							 if($saudadatadetails_amount >0)
							 {
							 	$amount_mail=number_format(round($saudadatadetails_amount,2),2);
							 }
							 else
							 {
								 $amount_mail=number_format(round(${totalamount.$saudadatadetails_sku_code},2),2);
							 }
							 if($saudadatadetails_sale_rate >0)
							 {
								 $sale_rate_mail=number_format(round($saudadatadetails_sale_rate,4),2);
							 }
							 else
							 {
								 $sale_rate_mail=number_format(round($saudadatadetails_sale_rate,4),2);
							 }
						 }
						 else
						 {
							 $amount_mail=number_format(${totalamount.$saudadatadetails_sku_code},2);
							 $sale_rate_mail=number_format(round($saudadatadetails_sale_rate,4),2);
						 }
						 if(strpos($saudadatadetails_qty,'.')!=false){
							 $saudadatadetails_qty_mail=$saudadatadetails_qty;
						 }
						 else
						 {
							 $saudadatadetails_qty_mail=number_format($saudadatadetails_qty,2);
						 }
						 
						//For total value
						 ${saudadatadetails_VAT_total.$saudadatadetails_sauda_no}=${saudadatadetails_VAT_total.$saudadatadetails_sauda_no}+$saudadatadetails_VAT;
						 ${saudadatadetails_qty_total.$saudadatadetails_sauda_no}=${saudadatadetails_qty_total.$saudadatadetails_sauda_no}+$saudadatadetails_qty;
						
						if($saudadatadetails_amount >0)
						{
							${saudadatadetails_amount_total.$saudadatadetails_sauda_no}=${saudadatadetails_amount_total.$saudadatadetails_sauda_no}+$saudadatadetails_amount;
						}
						 else
						 {
							${saudadatadetails_amount_total.$saudadatadetails_sauda_no}=${saudadatadetails_amount_total.$saudadatadetails_sauda_no}+${totalamount.$saudadatadetails_sku_code};
						 }
						 if(mrp=='yes')
						 {
							$mrp_sale_rate_TD="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
										<span style='font-family:Arial CE;font-size:10pt'>".number_format($mrp,2)."</span>&nbsp;</td>";
						 }
						 //else if(sale_rate=='yes' && sale_rate_input_dropdown=='input')
						 else if(sale_rate=='yes' && sauda_sale_rate_input_dropdown=='input')
						 {
							 $mrp_sale_rate_TD="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
										<span style='font-family:Arial CE;font-size:10pt'>".$sale_rate_mail."</span>&nbsp;</td>";
						 }
						 //else if(sale_rate=='yes' && sale_rate_input_dropdown=='dropdown')
						 else if(sale_rate=='yes' && sauda_sale_rate_input_dropdown=='dropdown')
						 {
							 $mrp_sale_rate_TD="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
										<span style='font-family:Arial CE;font-size:10pt'>".number_format($sale_rate_db,2)."</span>&nbsp;</td>";
						 }
							 
						//------------------Sauda allocation freight charge and premium--------------------------------------
						if(sauda_allocation=='yes' && sauda_rate_variable=='yes')
						{
							$sauda_freight_charge_TD="<td style='width:60px;text-align:left;min-height:21px;background-color:white'><strong>
									<span style='font-family:Arial CE;font-size:10pt'>".@number_format($saudadatadetails_freight_charge,2)."</span></strong></td>";
						}
						else
						{
							$sauda_freight_charge_TD='';
						}
						if(premium=='yes')
						{
						  $sauda_premium_TD="<td style='width:60px;text-align:left;min-height:21px;background-color:white'><strong>
									<span style='font-family:Arial CE;font-size:10pt'>".number_format($saudadatadetails_PREMIUM,2)."</span></strong></td>";

						}
						else
						{
							 $sauda_premium_TD='';
						}
						//------------------End sauda allocation freight charge and premium-----------------------------------
						//-------------------------------------------------- For sauda allocated qty updation entity construction-----------------------------
						if(!in_array($product_group_code,${product_group_code_array.$saudadatadetails_sauda_no}))
						{
							array_push(${product_group_code_array.$saudadatadetails_sauda_no},$product_group_code);
						}
						${saudadatadetails_qty_UOM3.$saudadatadetails_sku_code}=($conversion_factor*$saudadatadetails_qty)/$conversion_factor_two;
						${product_group_code_qty.$product_group_code.$saudadatadetails_sauda_no}=${product_group_code_qty.$product_group_code.$saudadatadetails_sauda_no}+${saudadatadetails_qty_UOM3.$saudadatadetails_sku_code};
						
						//-------------------------------------------------- End of sauda allocated qty updation entity construction-----------------------------
						${a.$saudadatadetails_sauda_no} .="
								<tr>".$product_details_TD."
								<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".number_format($saudadatadetails_qty_mail,2)."</span>&nbsp;</td>
								<td style='width:60px;text-align:left;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".strtoupper($UOM1)."</span>&nbsp;</td>
								".$mrp_sale_rate_TD.$sauda_freight_charge_TD.$saudaemailbody_TD_VAL.$sauda_premium_TD."
								<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$amount_mail."</span>&nbsp;</td>".$saudaemailbody_VAT_VAL."
								</tr>";
					   $saudadatadetails_sms_sale_rate=$saudadatadetails_sale_rate-$saudadatadetails_TD+$saudadatadetails_PREMIUM+$saudadatadetails_freight_charge;
					   ${smsstring.$saudadatadetails_sauda_no}.=$prod_desc.' '.$saudadatadetails_qty.' '.'Case'.'@Rs.'.$saudadatadetails_sms_sale_rate.',';			
						
					   
					   $basic_rate=$saudadatadetails_sale_rate-$margin_cost;
					   /*if($saudadatadetails_TD <= $auth_one_limit)
					   {
						   $app_status='DECLINE,APPROVE';
					   }
					   else
					   {
							$app_status='DECLINE,EMAIL';
						   /*$margin_TD_diff=$margin_cost - $saudadatadetails_TD;
						   if($margin_TD_diff >= 0 && $margin_TD_diff <$margin_cost)
						   {
							   $app_status='DECLINE,EMAIL';
						   }*/
					   //}
					   $app_status='D,A';
					   $TDvalidationskupart="  prod_code='".$saudadatadetails_sku_code."',
											   prod_desc='".$prod_desc."',
											   product_group_code='".$product_group_code."',
											   product_group_name='".$product_group_name."',
											   qty='".$saudadatadetails_qty."',
											   sale_rate='".$saudadatadetails_sale_rate."',
											   basic_rate='".$basic_rate."',
											   secondary_freight='".$saudadatadetails_freight_charge."',
											   app_status='".$app_status."',
											   margin='".$margin_cost."',
											   auth_one_limit='".$auth_one_limit."',
											   qty_MT	='".$convert_qty_two."',
											   TD='".$saudadatadetails_TD."',";
						array_push(${TDvalidationskupartarray.$saudadatadetails_sauda_no},$TDvalidationskupart);					   
					}
			}//End for loop
		}
		//End Insert into the Sauda Details table for new trans id
		//For sending email for Sauda
		if(count($saudaheader_array_mailbody)>0)
		{
			$TD_approval_vertical_array=explode(',',run_time_TD_approval_vertical);
			for($countarr=0;$countarr<count($saudaheader_array_mailbody);$countarr++)
			{
				${smsstringfinal.$saudatansid_array_mailbody[$countarr]}="EAL confirms Sauda booking of:"
				.${smsstring.$saudatansid_array_mailbody[$countarr]}." with ".$saudacustomername_array_smsbody[$countarr].".".$saudacustomername_array_smsbody[$countarr]." agreed to lift sauda within ".$saudavalidityperiod_array_smsbody[$countarr]." days from sauda date. Freight & Taxes extra as applicable. For any deviation, please contact Mr Anirudha Nayak @ 9007227866 in 24 hours from receipt of this SMS.";
				//echo strtolower($saudavertical_array_smsbody[$countarr]);
				/*if(strtolower($saudavertical_array_smsbody[$countarr])=='specialty fats')
				{
						$url="http://websms.codez.in:8080/bulksms/bulksms?username=coz1-emami&password=EAL2017&type=0&dlr=1&source=EMAGRO&destination=
	 ".$saudacustomerphoneno_array_smsbody[$countarr]."&message=".urlencode(${smsstringfinal.$saudatansid_array_mailbody[$countarr]})."";
	 //echo $url;
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_TIMEOUT, 20);
						curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_exec($ch);
				}*/
				/*if(!in_array($saudavertical_array_smsbody[$countarr],$TD_approval_vertical_array)) //If Runtime TD approval is off for the sauda booked vertical*/
				if(strtolower($saudavertical_array_smsbody[$countarr])=='specialty fats')
				{
					if(strlen(${smsstringfinal.$saudatansid_array_mailbody[$countarr]})> 160)
					{
						${smsstringfinal.$saudatansid_array_mailbody[$countarr]}=wordwrap(${smsstringfinal.$saudatansid_array_mailbody[$countarr]}, 160, "<br />");
						${smsstringfinalarray.$saudatansid_array_mailbody[$countarr]}=explode('<br />',${smsstringfinal.$saudatansid_array_mailbody[$countarr]});
						foreach(${smsstringfinalarray.$saudatansid_array_mailbody[$countarr]} as $smsstringval)
						{
							//$url="http://smslive.in/push/default.aspx?user=e_agro&pws=emagro123&Receipent=".$saudacustomerphoneno_array_smsbody[$countarr]."&sms=".urlencode($smsstringval)."";
							$url="http://websms.codez.in:8080/bulksms/bulksms?username=coz1-emami&password=EAL2017&type=0&dlr=1&source=EMAGRO&destination=
	 ".$saudacustomerphoneno_array_smsbody[$countarr]."&message=".($smsstringval)."";
	 						//echo $url;
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $url);
							curl_setopt($ch, CURLOPT_TIMEOUT, 20);
							curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_exec($ch);
							sleep(3);
						}
					}
					else
					{
						//$url="http://smslive.in/push/default.aspx?user=e_agro&pws=emagro123&Receipent=9007227866,".$saudacustomerphoneno_array_smsbody[$countarr]."&sms=".urlencode(${smsstringfinal.$saudatansid_array_mailbody[$countarr]})."";
						$url="http://websms.codez.in:8080/bulksms/bulksms?username=coz1-emami&password=EAL2017&type=0&dlr=1&source=EMAGRO&destination=
	 ".$saudacustomerphoneno_array_smsbody[$countarr]."&message=".urlencode(${smsstringfinal.$saudatansid_array_mailbody[$countarr]})."";
	 					//echo $url;
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_TIMEOUT, 20);
						curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						
						curl_exec($ch);
					}
				}
				//exit();
				if(strtolower($saudavertical_array_smsbody[$countarr])=='specialty fats') //If Runtime TD approval is on for the sauda booked vertical
				{
					$location_date=str_replace('-','',$location_date);
					$location_date=str_replace(':','',$location_date);
					$location_date=str_replace(' ','',$location_date);
					$notification_id='PN'.$emp_code.'EMAMIAGRO'.$location_date;
					$notification_type='Individual';
					$apiKey='AAAAcwFa5lA:APA91bFLXdUw4ZQCrPcLce7xsI33eVlv2C9I1qgARtQMfESSvdEHd0wJ2vmSi63YVZQsTYCaJBDT1wK5j3y8OUATVaaSqL6Q6OpyYeKhIz9z-F25Rq-4qm9KlQ26f4WCteFcjwXaEAkS';
					$collapseKey=rand();
					 $title = "";
					$message='Sauda booked by '.$emp_name.' of '.$saudabranchname_array_smsbody[$countarr].'. Please validate the TD.';
					
					//This array contains, the token and the notification. The 'to' attribute stores the token.
					$data= array('sauda_no' =>$saudatansid_array_mailbody[$countarr],'notification_id' =>$notification_id, 'notification_type' => $notification_type, 'sender_id' => 'EMAMIAGRO', 'body' => $message); 
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
						$sqlnotificationmaster .= " ,sender_id='EMAMIAGRO'";
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
					if(substr(str_replace('-','',$saudadate_array_mailbody[$countarr]),0,8)==substr($location_date,0,8))
					{
					  for($countpartarr=0;$countpartarr<count(${TDvalidationskupartarray.$saudatansid_array_mailbody[$countarr]});$countpartarr++)
					  {
						$sqlinsertTDvalidation="INSERT INTO TD_realtime_validation_details SET  sauda_no='".$saudatansid_array_mailbody[$countarr]."',
												customer_code='".$saudacustomercode_array_smsbody[$countarr]."',
												customer_name='".$saudacustomername_array_smsbody[$countarr]."',
												depot_code='".$saudabranchcode_array_smsbody[$countarr]."',
												depot_name='".$saudabranchname_array_smsbody[$countarr]."',
												route_name='".$saudaroute_array[$countarr]."',
												".${TDvalidationskupartarray.$saudatansid_array_mailbody[$countarr]}[$countpartarr]."
												authorized_emp_code='".$authorized_emp_code."',
												status='suspended',
												download_time=CURRENT_TIMESTAMP()";
						if(mysqli_query($link,$sqlinsertTDvalidation))
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
				} // END of Runtime TD approval Process
				if(TD=='yes' && TD_type=='sauda value wise')
				{
					if(TD_calc=='percentage')
					{
						$TD_sauda_val="<br /><table>TD on sauda value: <b>".number_format($saudadataheader_TD_array[$countarr],2)."%</b></table>";
					}
					else
					{
						$TD_sauda_val="<br /><table>TD on sauda value: <b>".number_format($saudadataheader_TD_array[$countarr],2)."</b></table>";
					}
				}
				if(TD=='yes' && TD_type=='customer wise')
				{
					if(TD_calc=='percentage')
					{
						$TD_sauda_val="<br /><table>TD applicable for this customer: <b>".$saudadataheader_TD_array[$countarr]."%</b></table>";
					}
					else
					{
						$TD_sauda_val="<br /><table>TD applicable for this customer: <b>".$saudadataheader_TD_array[$countarr]."</b></table>";
					}
				}
				if(TD=='yes' && (TD_type=='sauda value wise' || TD_type=='customer wise')){
					
					if(TD_calc=='percentage')
					{
						$total_sauda_amount=(${grandtotal.$saudatansid_array_mailbody[$countarr]}-(${grandtotal.$saudatansid_array_mailbody[$countarr]}*$saudadataheader_TD_array[$countarr])/100);
					}
					else
					{
						$total_sauda_amount=(${grandtotal.$saudatansid_array_mailbody[$countarr]}- $saudadataheader_TD_array[$countarr]);
					}
				}
				else
				{
					$total_sauda_amount=${grandtotal.$saudatansid_array_mailbody[$countarr]};
				}
				
				$saudaemailsubj="Sauda booked by ".$saudaemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($saudadate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($saudadate_array_mailbody[$countarr])).' hrs.';
				$total_sauda_val="<br /><table>Total sauda booking value: <b>Rs. ".number_format($total_sauda_amount,2)."/-</b></table>";
				$saudacashreceivedemailbody='';
				
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
				$product_details_colspan=no_of_filter;			

				if(TD=='yes' && TD_type=='sku wise')
				{
					$saudaemailbody_TD_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>TD</span></strong></th>";
					$saudaemailbody_TD_total="<td style='width:60px;min-height:21px;text-align:right'><strong>
							<span style='font-size:10pt;font-family:Arial CE'></span></strong></td>";		
				}
				else 
				{
					$saudaemailbody_TD_TH="";
					$saudaemailbody_TD_total="";
				}
				if(VAT=='yes')
				{
					$saudaemailbody_VAT_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>VAT</span></strong></th>";
					$saudaemailbody_VAT_total="<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>".number_format(round(${saudadatadetails_VAT_total.$saudatansid_array_mailbody[$countarr]},2),2)."</span></strong></td>";		
				}
				else 
				{
					$saudaemailbody_VAT_TH="";
					$saudaemailbody_VAT_total="";
				}
				
				if(sale_rate=='yes')
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
				//------------------Sauda allocation freight charge and premiun--------------------------------------
				if($saudadataheader_transaction_type_array[$countarr]=='OB' && sauda_allocation=='yes' && sauda_rate_variable=='yes')
				{
					$sauda_freight_charge_TR="<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>Freight charge</span></strong></th>";
				}
				else
				{
					$sauda_freight_charge_TR='';
				}
				if(premium=='yes')
				{
					$sauda_premiun_TR="<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>Premium</span></strong></th>";
					$saudaemailbody_premium_total="<td style='width:60px;min-height:21px;text-align:right'><strong>
							<span style='font-size:10pt;font-family:Arial CE'></span></strong></td>";			
				}
				else
				{
					$sauda_premiun_TR='';
					$saudaemailbody_premium_total='';
				}
				//------------------End sauda allocation freight charge and premiun-----------------------------------
				$qty_TR="<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>Qty</span></strong></th>";
				 if(strpos(${saudadatadetails_qty_total.$saudatansid_array_mailbody[$countarr]},'.')!=false){
					 ${saudadatadetails_qty_total.$saudatansid_array_mailbody[$countarr]}=${saudadatadetails_qty_total.$saudatansid_array_mailbody[$countarr]};
				 }
				 else
				 {
					 ${saudadatadetails_qty_total.$saudatansid_array_mailbody[$countarr]}=number_format(${saudadatadetails_qty_total.$saudatansid_array_mailbody[$countarr]},2);
				 }
				//-------------------------------------------------- For sauda allocated qty updation-----------------------------------------
				$emp_upper_hierarchy = return_employee_upper_hierarchy($emp_code);
				$emp_upper_hierarchy=str_replace("'","",$emp_upper_hierarchy);
				$emp_upper_hierarchy_array=explode(',',$emp_upper_hierarchy);
				$updated_emp_upper_hierarchy_array=array();

				$sqlchkallocationaccess="SELECT get_allocation FROM sauda_allocation_access WHERE designation='".${designation.$saudatansid_array_mailbody[$countarr]}."'";
				$rschkallocationaccess=mysqli_query($link,$sqlchkallocationaccess);
				$rowchkallocationaccess=mysqli_fetch_assoc($rschkallocationaccess);
				$sqlchkallocation="SELECT emp_code  FROM sauda_allocation WHERE emp_code='".$emp_code."'";
				$rschkallocation=mysqli_query($link,$sqlchkallocation);
				$rowchkallocation=mysqli_fetch_assoc($rschkallocation);
				$countchkallocation=mysqli_num_rows($rschkallocation);
				$allocation_flag=$rowchkallocationaccess['get_allocation'];
				
				if($allocation_flag=='yes' && $countchkallocation >0)
				{
					$allocation_update_emp_code=$saudaempcode_array_mailbody[$countarr];
				}
				else if($allocation_flag=='yes' && $countchkallocation==0)
				{
					$allocation_update_emp_code=${reporting_to.$saudatansid_array_mailbody[$countarr]};
				}
				else if($allocation_flag=='yes' && ${reporting_to.$saudatansid_array_mailbody[$countarr]}=='')
				{
					$allocation_update_emp_code=$saudacustomer_mapped_empcode_array_mailbody[$countarr];
				}
				foreach(${product_group_code_array.$saudatansid_array_mailbody[$countarr]} as $product_group_code_val)
				{
					$product_group_code_qty=${product_group_code_qty.$product_group_code_val.$saudatansid_array_mailbody[$countarr]};
					$sqlupdatesaudaallocation="UPDATE sauda_allocation SET BAL=ROUND((BAL-$product_group_code_qty),3) WHERE
											  product_filter_code='".$product_group_code_val."' 
											  AND emp_code='".$allocation_update_emp_code."'";
					 $rsupdatesaudaallocation=mysqli_query($link,$sqlupdatesaudaallocation);
					 array_push($updated_emp_upper_hierarchy_array,$allocation_update_emp_code);
					//For upper hierarchy balance updation
					foreach($emp_upper_hierarchy_array as $emp_upper_hierarchy_val)
					{
						if(!in_array($emp_upper_hierarchy_val,$updated_emp_upper_hierarchy_array))
						{
							$sqlupdatesaudaallocation="UPDATE sauda_allocation SET BAL=ROUND((BAL-$product_group_code_qty),3) WHERE
												   product_filter_code='".$product_group_code_val."' 
												   AND emp_code='".$emp_upper_hierarchy_val."'";
					 		$rsupdatesaudaallocation=mysqli_query($link,$sqlupdatesaudaallocation);
						}
					}
				}
				//-------------------------------------------------- End of sauda allocated qty updation-----------------------------

				//Sauda approval process checking
				$headers  = "MIME-Version: 1.0\r\n";
				$headers .= "Content-type: text/html; charset=UTF-8\n";
				$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
							"Reply-To:".FROMEMAIL." \r\n" .
							"Bcc: ".BCCEMAIL." \r\n" .
							'X-Mailer: PHP/' . phpversion();
				$sauda_email=SAUDAEMAILRECIPENTS;			
				$saudaemailbody = $saudaheader_array_mailbody[$countarr]."<br><table border=1 style=background-color:AliceBlue>
								<tr>".$product_details_TH.$qty_TR."
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>UOM</span></strong></th>
								".$mrp_sale_rate_TR.$sauda_freight_charge_TR.$saudaemailbody_TD_TH.$sauda_premiun_TR."
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>Amount</span></strong></th>".$saudaemailbody_VAT_TH." 	
								</tr>
					".${a.$saudatansid_array_mailbody[$countarr]}."<tr>
								<td style='min-height:21px;text-align:center' colspan='".$product_details_colspan."'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>Total</span></strong></td>
								<td style='width:50px;min-height:21px;text-align:right'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".${saudadatadetails_qty_total.$saudatansid_array_mailbody[$countarr]}."</span></strong></td>
								<td style='width:50px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial CE'></span></strong></td>
								<td style='width:50px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'></span></strong></td><td style='width:50px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'></span></strong></td>".$saudaemailbody_TD_total.$saudaemailbody_premium_total."<th style='width:50px;min-height:21px;text-align:right'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".number_format(round(${saudadatadetails_amount_total.$saudatansid_array_mailbody[$countarr]},2),2)."</span></strong></th>".$saudaemailbody_VAT_total."
								</tr></table>
					".$TD_sauda_val.$total_sauda_val."
					<br /><br /><table>".$saudavalidfrom_array_mailbody[$countarr]."</table><br /><br /><table>".$saudainstruction_array_mailbody[$countarr]."</table><br /><br />
					<br /><br /><br />Powered By aceDNS<br /></body></html>";
					if(mail($sauda_email, $saudaemailsubj, $saudaemailbody, $headers,$spam_filter))
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
 /* -------------------------------------END QUERY FOR SAUDA------------------------------------------------------------------------------------------------*/
	
	/*if($vertical_value=='HBC:Rasoi:BIB')
	{
		$sqlInsert="INSERT INTO data_refresh_log SET refresh_date_time=CURRENT_TIMESTAMP(),uploading_ip='SYSTEM'";
		mysqli_query($link,$sqlInsert);
	}*/
	
	$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
	$result = mysqli_query($link,$sqlquery);
	$countdatarefresh=mysqli_num_rows($result);

	if($flag==5)
	{
		 $sqlupdatelastoperationtime="UPDATE changepassword SET last_operation_datetime='".$last_operation_datetime."' WHERE emp_code='".$emp_code."'";
		 $rsupdatelastoperationtime=mysqli_query($link,$sqlupdatelastoperationtime);	 
		 mysqli_query($link,"COMMIT");
		 
		//print_r($saudaheader_array_mailbody);
		if(strtoupper($nick_name)=='EMAMI')
		{
		for($counttrigger=0;$counttrigger<count($saudaheader_array_mailbody);$counttrigger++)
			{
				$sauda_no=$saudatansid_array_mailbody[$counttrigger];
				//exit();
				$url="http://salesmpower.acedns.in/sauda-download-log-preperation-trigger.php?sauda_no=$sauda_no&nick_name=$nick_name";
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_TIMEOUT, 100);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_exec($ch);
			}
		}
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
		
		//print_r($saudaheader_array_mailbody);
		if(strtoupper($nick_name)=='EMAMI')
		{
		for($counttrigger=0;$counttrigger<count($saudaheader_array_mailbody);$counttrigger++)
			{
				$sauda_no=$saudatansid_array_mailbody[$counttrigger];
				$url="http://salesmpower.acedns.in/sauda-download-log-preperation-trigger.php?sauda_no=$sauda_no&nick_name=$nick_name";
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_TIMEOUT, 100);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_exec($ch);
			}
		}

		 if($countdatarefresh >0)
		 {
			 echo $flag=2;
		 }
		 else
		 {
			echo $flag=1;
		 }
	}
	mysqli_close($link);
?>
