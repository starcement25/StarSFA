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

/*$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$result = mysqli_query($link,$sqlquery);
$countdatarefresh=mysqli_num_rows($result);*/
$body=file_get_contents('php://input');

//For insertion of fetched xml
if($nick_name=='EMAMIT')
{
	$body_xml=str_replace("'",'"',$body);
	$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
	mysqli_query($link,$sqlinsert_xml_data);	

	/*$sqlselectdbversion="SELECT is_update FROM table_structure_updation  WHERE emp_code='".$emp_code."' and db_version_code='6.4'";
	$rsselectdbversion=mysqli_query($link,$sqlselectdbversion);
	$rowselectdbversion=mysqli_fetch_assoc($rsselectdbversion);
	$is_update=$rowselectdbversion['is_update'];

	if($is_update==1)
	{
		echo $flag=2;
		exit(); 
	}*/
}

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><sauda><location><emp_code><![CDATA[E0002]]></emp_code><trans_id><![CDATA[NOE000220150306180952]]></trans_id><latt><![CDATA[22.5641689]]></latt><longi><![CDATA[88.3569041]]></longi><date><![CDATA[2015-03-06 18:09:52]]></date></location><saudadata><sauda_header><sauda_no><![CDATA[NOE000220150306180952]]></sauda_no><d_instruction><![CDATA[test]]></d_instruction><customer_code><![CDATA[C/0004586]]></customer_code></sauda_header></saudadata></sauda></root>";*/
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><sauda><location><emp_code><![CDATA[E0039]]></emp_code><trans_id><![CDATA[FTE003920150709175956]]></trans_id><latt><![CDATA[]]></latt><longi><![CDATA[]]></longi><date><![CDATA[2015-07-09 17:59:56]]></date></location><saudadata><sauda_header><sauda_no><![CDATA[FTE003920150709175956]]></sauda_no><TD><![CDATA[]]></TD><d_instruction><![CDATA[]]></d_instruction><broker_id><![CDATA[BR0001]]></broker_id><transaction_type><![CDATA[OB]]></transaction_type><vat><![CDATA[]]></vat><branch_code><![CDATA[B0001]]></branch_code><valid_from><![CDATA[2015-07-09]]></valid_from><customer_code><![CDATA[C/0000042]]></customer_code></sauda_header><sauda_details><sauda_no><![CDATA[FTE003920150709175956]]></sauda_no><Sku_code><![CDATA[12048]]></Sku_code><qty><![CDATA[200]]></qty><TD><![CDATA[0]]></TD><premium><![CDATA[10]]></premium><sale_rate><![CDATA[60.19]]></sale_rate><VAT><![CDATA[]]></VAT><amount><![CDATA[15238.0]]></amount><freight_charge><![CDATA[6]]></freight_charge><mrp_code><![CDATA[z48]]></mrp_code></sauda_details></saudadata></sauda></root>";*/

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
	var $sauda_emp_code,$sauda_trans_id,$sauda_latt,$sauda_longi,$sauda_date,$saudadataheader_sauda_no,$saudadataheader_customer_code,$saudadataheader_TD,$saudadataheader_d_instruction,$saudadataheader_broker_id,$saudadataheader_transaction_type,$saudadataheader_VAT,$saudadataheader_branch_code,$saudadataheader_sauda_valid_from;	
}
class xml_sauda_details{
	var $saudadatadetails_sauda_no,$saudadatadetails_sku_code,$saudadatadetails_qty,$saudadatadetails_TD,$saudadatadetails_PREMIUM,$saudadatadetails_sale_rate,$saudadatadetails_VAT,$saudadatadetails_amount,$saudadatadetails_mrp_code,$saudadatadetails_freight_charge;
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
	$saudadataheader_d_instruction,$saudadataheader_broker_id,$saudadataheader_transaction_type,$saudadataheader_VAT,$saudadataheader_branch_code,$saudadataheader_sauda_valid_from,$saudadatadetails_sauda_no,$saudadatadetails_sku_code,$saudadatadetails_qty,$saudadatadetails_TD,$saudadatadetails_PREMIUM,$saudadatadetails_sale_rate,$saudadatadetails_VAT,$saudadatadetails_amount,$saudadatadetails_mrp_code,$saudadatadetails_freight_charge;
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
		${product_group_code_array.$saudadataheader_sauda_no}=array();
		
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
								  d_instruction		='".addslashes($saudadataheader_d_instruction)."'";   
			if(mysqli_query($link,$sqlinsertorlocation) && mysqli_query($link,$sqlinsertsaudaheader))
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
				$operation_type='Sauda';
				$sauda_email=fetch_corresponding_emails($operation_type,$vertical_value,$branch_code);
			}
			else
			{
				$sauda_email=SAUDAEMAILRECIPENTS;
			}
			$addresssauda=getReverseGeo($sauda_latt,$sauda_longi);
				
			$sqlcustomername="SELECT customer_name,emp_code FROM customer_master WHERE customer_code='".$saudadataheader_customer_code."'";
			$rscustomername=mysqli_query($link,$sqlcustomername);
			$rowcustomername=mysqli_fetch_assoc($rscustomername);
			$customer_name=$rowcustomername['customer_name'];
			$customer_mapped_emp_code=$rowcustomername['emp_code'];
			
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
				
				$sqlmrpdetails="SELECT mrp,sale_rate FROM sauda_mrp WHERE product_code='".$saudadatadetails_sku_code."' 
								AND mrp_code='".$saudadatadetails_mrp_code."'";
				$rsmrpdetails=mysqli_query($link,$sqlmrpdetails);
				$recmrpdetails=mysqli_fetch_assoc($rsmrpdetails);
				$mrp=$recmrpdetails['mrp'];
				$sale_rate_db=$recmrpdetails['sale_rate'];
				
					if(no_of_filter==1){
						$sqlproductdetails="SELECT prod_code,prod_desc,UOM1,UOM2,UOM3,conversion_factor,conversion_factor_two FROM product_master WHERE prod_code='".$saudadatadetails_sku_code."'";
					}
					if(no_of_filter==2){
						$sqlproductdetails="SELECT PGM.product_group_name,PGM.product_group_code,PM.prod_desc,PM.UOM1,PM.UOM2,PM.UOM3,PM.conversion_factor,PM.conversion_factor_two FROM product_master PM,product_group_master PGM 
											WHERE PM.product_group_code=PGM.product_group_code AND PM.prod_code='".$saudadatadetails_sku_code."'";
					}
					if(no_of_filter==3){
						$sqlproductdetails="SELECT PGM.product_group_name,PGM.product_group_code,PSGM.product_sub_group_name,PSGM.product_sub_group_code,PM.prod_desc,PM.UOM1,
											PM.UOM2,PM.UOM3,PM.conversion_factor,PM.conversion_factor_two FROM 
											product_master PM,product_group_master PGM,product_sub_group_master PSGM
											WHERE PM.product_group_code=PGM.product_group_code AND PM.product_sub_group_code=PSGM.product_sub_group_code 
											AND PM.prod_code='".$saudadatadetails_sku_code."'";
					}
					if(no_of_filter==4){
						$sqlproductdetails="SELECT PGM.product_group_name,PGM.product_group_code,PSGM.product_sub_group_name,PSGM.product_sub_group_code,PBM.product_brand_name,PBM.product_brand_code,PM.prod_desc,
											PM.UOM1,PM.UOM2,PM.UOM3,PM.conversion_factor,PM.conversion_factor_two 
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
												amount		='".$saudadatadetails_amount."',
												mrp_code	='".$saudadatadetails_mrp_code."'";
						if(mysqli_query($link,$sqlinsertsaudadetails))
						{
							$flag=5;
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
						 else if(sale_rate=='yes' && sale_rate_input_dropdown=='input')
						 {
							 $mrp_sale_rate_TD="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
										<span style='font-family:Arial CE;font-size:10pt'>".$sale_rate_mail."</span>&nbsp;</td>";
						 }
						 else if(sale_rate=='yes' && sale_rate_input_dropdown=='dropdown')
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
					}
			}//End for loop
		}
		//End Insert into the Sauda Details table for new trans id
		//For sending email for Sauda
		if(count($saudaheader_array_mailbody)>0)
		{
			for($countarr=0;$countarr<count($saudaheader_array_mailbody);$countarr++)
			{
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
 /* -------------------------------------END QUERY FOR SAUDA---------------------------------------------------------------------------------------------------------------*/
	$countdatarefresh=returndatarefresh($emp_code);
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
	
	/*$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/operationdb-sauda.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&last_git_master_update_time=$last_git_master_update_time&last_loyalty_purchase_update_time=$last_loyalty_purchase_update_time";
	insertapilog($datetime,$emp_code,$url,$nick_name);*/
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/operationdb-sauda.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&last_git_master_update_time=$last_git_master_update_time&last_loyalty_purchase_update_time=$last_loyalty_purchase_update_time"."\r\n";
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
