<?php
ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
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

/*$body="<?xml version='1.0" encoding='UTF-8'?><root><product_promotion><location><emp_code><![CDATA[E0077]]></emp_code><trans_id><![CDATA[PPE007720151016134738]]></trans_id><latt><![CDATA[22.5642362]]></latt><longi><![CDATA[88.3568066]]></longi><date><![CDATA[2015-10-16 13:47:38]]></date></location><product_promotion_details><PROSPECT_CODE><![CDATA[PPE007720151016134738]]></PROSPECT_CODE><PROSPECT_NAME><![CDATA[Sourav Das]]></PROSPECT_NAME><PIN><![CDATA[700001]]></PIN><STREET_NAME><![CDATA[Ghasiara]]></STREET_NAME><STREET_NO><![CDATA[Sonarpur]]></STREET_NO><BUILDING_NO><![CDATA[]]></BUILDING_NO><APARTMENT_NO><![CDATA[]]></APARTMENT_NO><PHONE_NO><![CDATA[9801471234]]></PHONE_NO><EMAIL><![CDATA[]]></EMAIL><COMPETITOR_NAME><![CDATA[Dalda#Fortune]]></COMPETITOR_NAME><OIL_USED><![CDATA[Mustard,Rice Bran]]></OIL_USED></product_promotion_details></product_promotion></root>";*/

$attendance_emp_code = "*ROOT*ATTENDANCE*LOCATION*EMP_CODE";
$attendance_trans_id = "*ROOT*ATTENDANCE*LOCATION*TRANS_ID";
$attendance_latt = "*ROOT*ATTENDANCE*LOCATION*LATT";
$attendance_longi = "*ROOT*ATTENDANCE*LOCATION*LONGI";
$attendance_date = "*ROOT*ATTENDANCE*LOCATION*DATE";
$attendancedata_emp_code = "*ROOT*ATTENDANCE*ATTENDANCEDATA*EMP_CODE";
$attendancedata_date = "*ROOT*ATTENDANCE*ATTENDANCEDATA*DATE";

$location_emp_code="*ROOT*PRODUCT_PROMOTION*LOCATION*EMP_CODE";
$location_trans_id = "*ROOT*PRODUCT_PROMOTION*LOCATION*TRANS_ID";
$location_latt = "*ROOT*PRODUCT_PROMOTION*LOCATION*LATT";
$location_longi = "*ROOT*PRODUCT_PROMOTION*LOCATION*LONGI";
$location_date="*ROOT*PRODUCT_PROMOTION*LOCATION*DATE";

$prospect_code = "*ROOT*PRODUCT_PROMOTION*PRODUCT_PROMOTION_DETAILS*PROSPECT_CODE";
$prospect_name = "*ROOT*PRODUCT_PROMOTION*PRODUCT_PROMOTION_DETAILS*PROSPECT_NAME";
$pin = "*ROOT*PRODUCT_PROMOTION*PRODUCT_PROMOTION_DETAILS*PIN";
$street_name = "*ROOT*PRODUCT_PROMOTION*PRODUCT_PROMOTION_DETAILS*STREET_NAME";
$street_no = "*ROOT*PRODUCT_PROMOTION*PRODUCT_PROMOTION_DETAILS*STREET_NO";
$building_no = "*ROOT*PRODUCT_PROMOTION*PRODUCT_PROMOTION_DETAILS*BUILDING_NO";
$apartment_no = "*ROOT*PRODUCT_PROMOTION*PRODUCT_PROMOTION_DETAILS*APARTMENT_NO";
$phone_no = "*ROOT*PRODUCT_PROMOTION*PRODUCT_PROMOTION_DETAILS*PHONE_NO";
$email = "*ROOT*PRODUCT_PROMOTION*PRODUCT_PROMOTION_DETAILS*EMAIL";
$competitor_name = "*ROOT*PRODUCT_PROMOTION*PRODUCT_PROMOTION_DETAILS*COMPETITOR_NAME";
$address = "*ROOT*PRODUCT_PROMOTION*PRODUCT_PROMOTION_DETAILS*ADDRESS";
$oil_used = "*ROOT*PRODUCT_PROMOTION*PRODUCT_PROMOTION_DETAILS*OIL_USED";

$attendance_array = array();
$product_promotion_array=array();

$counter = 0;
$counteratt = 0;

class xml_attendance{
    var $emp_code, $trans_id,$latt,$longi,$attendance_date,$attendancedata_emp_code,$attendancedata_date;
}
class xml_product_promotion{
	var $location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$prospect_code,$prospect_name,$pin,$street_name,$street_no,$building_no,$apartment_no,$phone_no,$email,$competitor_name,$oil_used;	
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
    global $current_tag,$attendance_emp_code, $attendance_trans_id,$attendance_latt,$attendance_longi,$attendance_date,$attendancedata_emp_code,$attendancedata_date,$counter,$counteratt,$location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$prospect_code,$prospect_name,$pin,$street_name,$street_no,$building_no,$apartment_no,$phone_no,$email,$competitor_name,$oil_used,$product_promotion_array,$attendance_array;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,16)=='*ROOT*ATTENDANCE')
	{
		switch($current_tag){
			case $attendance_emp_code:
				$attendance_array[$counteratt] = new xml_attendance();
				$attendance_array[$counteratt]->emp_code = $data;
				break;
			case $attendance_trans_id:
				$attendance_array[$counteratt]->trans_id = $data;
				break;
			case $attendance_latt:
				$attendance_array[$counteratt]->latt = $data;
				break;
			case $attendance_longi:
				$attendance_array[$counteratt]->longi = $data;
				break;
			case $attendance_date:
				$attendance_array[$counteratt]->attendance_date = $data;
				break;
			case $attendancedata_emp_code:
				$attendance_array[$counteratt]->attendancedata_emp_code = $data;
				break;
			case $attendancedata_date:
				$attendance_array[$counteratt]->attendancedata_date = $data;
				$counteratt++;
				break;
		}
	}
	if(substr($current_tag,0,23)=='*ROOT*PRODUCT_PROMOTION')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $location_emp_code:
				$product_promotion_array[$counter] = new xml_product_promotion();
				$product_promotion_array[$counter]->location_emp_code = $data;
				break;
			case $location_trans_id:
				$product_promotion_array[$counter]->location_trans_id = $data;
				break;
			case $location_latt:
				$product_promotion_array[$counter]->location_latt = $data;
				break;
			case $location_longi:
				$product_promotion_array[$counter]->location_longi = $data;
				break;
			case $location_date:
				$product_promotion_array[$counter]->location_date = $data;
				break;
			case $prospect_code:
				$product_promotion_array[$counter]->prospect_code = $data;
				break;
			case $prospect_name:
				$product_promotion_array[$counter]->prospect_name = $data;
				break;
			case $pin:
				$product_promotion_array[$counter]->pin = $data;
				break;
			case $street_name:
				$product_promotion_array[$counter]->street_name = $data;
				break;
			case $street_no:
				$product_promotion_array[$counter]->street_no = $data;
				break;	
			case $building_no:
				$product_promotion_array[$counter]->building_no = $data;
				break;
			case $apartment_no:
				$product_promotion_array[$counter]->apartment_no = $data;
				break;
			case $phone_no:
				$product_promotion_array[$counter]->phone_no = $data;
				break;
			case $email:
				$product_promotion_array[$counter]->email = $data;
				break;
			case $competitor_name:
				$product_promotion_array[$counter]->competitor_name = $data;
				break;	
			case $oil_used:
				$product_promotion_array[$counter]->oil_used = $data;
				$counter++;
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

/* --------------------START QUERY FOR Product Promotion------------------------------------------------------------------------------------------------*/
//print_r($new_customer_array);
$product_promotion_array_trans_id=array();
if(count($product_promotion_array)>0)
{
	for($x=0;$x<count($product_promotion_array);$x++){

		$location_emp_code=$product_promotion_array[$x]->location_emp_code;
		$location_trans_id=$product_promotion_array[$x]->location_trans_id;
		$location_latt=$product_promotion_array[$x]->location_latt;
		$location_longi=$product_promotion_array[$x]->location_longi;
		$location_date=$product_promotion_array[$x]->location_date;
		$prospect_code=$product_promotion_array[$x]->prospect_code;
		$prospect_name=$product_promotion_array[$x]->prospect_name;
		$pin=$product_promotion_array[$x]->pin;
		$street_name=$product_promotion_array[$x]->street_name;
		$street_no=$product_promotion_array[$x]->street_no;
		$building_no=$product_promotion_array[$x]->building_no;
		$apartment_no=$product_promotion_array[$x]->apartment_no;
		$phone_no=$product_promotion_array[$x]->phone_no;
		$email=$product_promotion_array[$x]->email;
		$competitor_name=$product_promotion_array[$x]->competitor_name;
		$oil_used=$product_promotion_array[$x]->oil_used;
		
		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if($location_latt>0 && $location_longi>0)
		{
			$sqlupdatelatlongzero="UPDATE location SET latt='".$location_latt."',longi='".$location_longi."' WHERE 
									emp_code='".$location_emp_code."' AND latt='0' AND longi='0'";
			$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
		}

		//For checking that trans id exist or not for order
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$location_trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check Product Promotion: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for order
		if($countchkorlocation>0)
		{
			if(!in_array($location_trans_id,$newcustomer_array_trans_id))
			{
				array_push($product_promotion_array_trans_id,$location_trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$location_emp_code."',
									latt='".$location_latt."',
									longi='".$location_longi."'
									WHERE trans_id='".$location_trans_id."'";
			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update Product Promotion location: ".$sqlupdateorlocation);
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
		//Creation of code random no parameter
		$sqlempname="SELECT emp_name,branch_code,vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
		$rsempname=mysqli_query($link,$sqlempname);
		$rowempname=mysqli_fetch_assoc($rsempname);
		$emp_name=$rowempname['emp_name'];
		$branch_code=$rowempname['branch_code'];
		$vertical_value=$rowempname['vertical_value'];
		
		$random_no_length=7-strlen($nick_name);//7 is the maximum length of the company nick name
		$foldernamerand=$nick_name.rand(pow(10, $random_no_length-1), pow(10, $random_no_length)-1);
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));

		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$location_date_updatetime=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;

		//For Insert into the location table for new trans id regarding order
		$sqlinsertorlocation="INSERT INTO location SET emp_code='".$location_emp_code."',
									trans_id='".$location_trans_id."',
									latt='".$location_latt."',
									longi='".$location_longi."',
									date='".$location_date."',
									updatetime='".$location_date_updatetime."'"; 
	  if(mysqli_query($link,$sqlinsertorlocation))
		{
			$flag=5;
		}
		else
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}
		// Add new product promotion
		$sqlinsertproductpromotion="INSERT INTO product_promotion SET prospect_code ='".$prospect_code."',
							   prospect_name				='".addslashes($prospect_name)."',
							   pin							='".$pin."',
							   street_name					='".addslashes($street_name)."',
							   street_no					='".addslashes($street_no)."',
							   building_no					='".addslashes($building_no)."',
							   apartment_no					='".addslashes($apartment_no)."',
							   phone_no						='".$phone_no."',
							   email						='".$email."',
							   competitor_name				='".$competitor_name."',
							   oil_used						='".$oil_used."'";
		if(mysqli_query($link,$sqlinsertproductpromotion))
		{
			$flag=5;
		}
		else
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}
		/*if(strpos($oil_used,',')==false){
			$sql_generic_oil_chk="SELECT oil_name FROM generic_oil_master WHERE oil_name='".$oil_used."'";
			$rs_generic_oil_chk=mysqli_query($link,$sql_generic_oil_chk);
			$countgeneric_oil_check=mysqli_num_rows($rs_generic_oil_chk);
			if($countgeneric_oil_check <1)
			{
				$sqlinsertgenericoil="INSERT INTO generic_oil_master SET oil_name ='".$oil_used."'";
				if(mysqli_query($link,$sqlinsertgenericoil))
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
		}*/
		/*$sql_street_name_chk="SELECT street_name FROM street_master WHERE pin_code='".$pin."'";
		$rs_street_name_chk=mysqli_query($link,$sql_street_name_chk);
		$countstreet_name_chk=mysqli_num_rows($rs_street_name_chk);
		if($countstreet_name_chk <1)
		{
			$sqlinsertstreetname="INSERT INTO street_master SET street_name ='".$street_name."',pin_code='".$pin."'";
			if(mysqli_query($link,$sqlinsertstreetname))
			{
				$flag=5;
			}
			else
			{
				mysqli_query($link,"ROLLBACK");
				echo $flag=0;
				return;
			}
		}*/
		//Sending mail
		$operation_date=$date.'-'.$month.'-'.$year.' @ '.$hour.':'.$minute.':'.$second;
		/*$sqlprodgroup="SELECT product_group_name FROM product_group_master WHERE product_group_code='".$oil_used."'";
		$rsprodgroup=mysqli_query($link,$sqlprodgroup);
		$rowprodgroup=mysqli_fetch_assoc($rsprodgroup);
		$product_group_name=$rowprodgroup['product_group_name'];*/

		$productpromotionmailsubj="$nick_name - ​Product promoted by ".$emp_name." on ".date('d-m-Y',strtotime($location_date))." @".date('H:i:s',strtotime($location_date)).' hrs.';
		$addressproductpromotion=getReverseGeo($location_latt,$location_longi);
		
		$product_promotion_TR="<th style='width:200px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Customer Name</span></strong></th>
								<th style='width:150px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>PIN Code</span></strong></th>
								<th style='width:150px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>".$email_tag."</span></strong></th>
								<th style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Ph. No.</span></strong></th>";
				
		$product_promotion_TD="<td style='width:200px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>
								".$new_customer_name."</span>&nbsp;</td>
								<td style='width:150px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>
								".$pin_code."</span>&nbsp;</td>
								<td style='width:150px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>
								".$route_name."</span>&nbsp;</td>
								<td style='width:100px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>
								+91-".$phone_no."</span>&nbsp;</td>";
		$productpromotionmailbody = "<table><tr><td>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
								.$emp_name. "</b> at <b>".$addressproductpromotion."</b></td></tr></table>
								<br><br>
								<b>Customer Name: ".strtoupper($prospect_name)."</b><br /><br />
								<b>PIN Code: ".$pin."</b><br /><br />
								<b>Address1: ".$street_name."</b><br /><br />
								<b>Address2: ".$street_no."</b><br /><br />
								<b>Building/Flat No: ".$building_no."</b><br /><br />
								<b>Complex / Apartment Name: ".$apartment_no."</b><br /><br />
								<b>Phone No: +91-".$phone_no."</b><br /><br />
								<b>Email Id: ".$email."</b><br /><br />
								<b>Competitor: ".$competitor_name."</b><br /><br />
								<b>OIL Used: " .$oil_used. "</b>
								<br><br><br>Powered By aceDNS<br>";
        $headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8\n";
		$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
					"Reply-To:".FROMEMAIL." \r\n" .
					"Bcc: ".BCCEMAIL." \r\n" .
					'X-Mailer: PHP/' . phpversion();
		$productpromotionemail='agrotechemami@gmail.com';
		if(mail($productpromotionemail, $productpromotionmailsubj, $productpromotionmailbody, $headers,$spam_filter))
		{
			$flag=5;
		}
		else
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}
	  }//End of else
	}
}
 /* --------------------END QUERY FOR Product Promotion--------------------------------------------------------------------------------------------------------*/
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
//echo $flag=2;
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = APICALLLOGURL."/operationdb-product-promotion.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/operationdb-product-promotion.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time"."\r\n";
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
	mysqli_close($link);
?>
