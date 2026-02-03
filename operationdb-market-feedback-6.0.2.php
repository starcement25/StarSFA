<?php

//  ini_set('display_errors', 1);
//  ini_set('display_startup_errors', 1);
//  error_reporting(E_ALL);

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
$xml_encoded = base64_encode($body);
//$body_xml=str_replace("'",'"',$body);
	$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$xml_encoded."',
							insertdate=CURRENT_TIMESTAMP()";
	mysqli_query($link,$sqlinsert_xml_data);

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><new_customer><location><emp_code><![CDATA[E0001]]></emp_code><trans_id><![CDATA[NE000120150827112916]]></trans_id><latt><![CDATA[22.5642279]]></latt><longi><![CDATA[88.3568262]]></longi><date><![CDATA[2015-08-27 11:29:16]]></date></location><new_customer_details><customer_code><![CDATA[NE000120150827112916]]></customer_code><customer_name><![CDATA[Test]]></customer_name><Phone_no><![CDATA[1234567890]]></Phone_no><pin_code><![CDATA[123456]]></pin_code><area><![CDATA[RT/3]]></area><area_name><![CDATA[MANISH NAGAR]]></area_name><rds_tag><![CDATA[]]></rds_tag></new_customer_details></new_customer></root>";*/
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><MARKET_FEEDBACK><location><emp_code><![CDATA[E0027]]></emp_code><trans_id><![CDATA[MFE002720150907182231]]></trans_id><latt><![CDATA[22.5642384]]></latt><longi><![CDATA[88.3567992]]></longi><date><![CDATA[2015-09-07 18:22:31]]></date></location><MARKET_FEEDBACK_DATA><MARKET_FEEDBACK_ID><![CDATA[MFE002720150907182231]]></MARKET_FEEDBACK_ID><route_code><![CDATA[]]></route_code><PRODUCT_GROUP><![CDATA[Rice Bran]]></PRODUCT_GROUP><COMPETITOR_NAME><![CDATA[Fortune]]></COMPETITOR_NAME><PTD><![CDATA[0]]></PTD><PTR><![CDATA[]]></PTR><PTC><![CDATA[]]></PTC></MARKET_FEEDBACK_DATA><MARKET_FEEDBACK_DATA><MARKET_FEEDBACK_ID><![CDATA[MFE002720150907182231]]></MARKET_FEEDBACK_ID><route_code><![CDATA[]]></route_code><PRODUCT_GROUP><![CDATA[Rice Bran]]></PRODUCT_GROUP><COMPETITOR_NAME><![CDATA[Ruchira]]></COMPETITOR_NAME><PTD><![CDATA[0]]></PTD><PTR><![CDATA[]]></PTR><PTC><![CDATA[]]></PTC></MARKET_FEEDBACK_DATA><MARKET_FEEDBACK_DATA><MARKET_FEEDBACK_ID><![CDATA[MFE002720150907182231]]></MARKET_FEEDBACK_ID><route_code><![CDATA[]]></route_code><PRODUCT_GROUP><![CDATA[Rice Bran]]></PRODUCT_GROUP><COMPETITOR_NAME><![CDATA[Mahakosh]]></COMPETITOR_NAME><PTD><![CDATA[]]></PTD><PTR><![CDATA[]]></PTR><PTC><![CDATA[]]></PTC></MARKET_FEEDBACK_DATA></MARKET_FEEDBACK><MARKET_FEEDBACK><location><emp_code><![CDATA[E0027]]></emp_code><trans_id><![CDATA[MFE002720150907182809]]></trans_id><latt><![CDATA[22.5642318]]></latt><longi><![CDATA[88.3568094]]></longi><date><![CDATA[2015-09-07 18:28:09]]></date></location><MARKET_FEEDBACK_DATA><MARKET_FEEDBACK_ID><![CDATA[MFE002720150907182809]]></MARKET_FEEDBACK_ID><route_code><![CDATA[]]></route_code><PRODUCT_GROUP><![CDATA[Rice Bran]]></PRODUCT_GROUP><COMPETITOR_NAME><![CDATA[Fortune]]></COMPETITOR_NAME><PTD><![CDATA[5]]></PTD><PTR><![CDATA[6]]></PTR><PTC><![CDATA[]]></PTC></MARKET_FEEDBACK_DATA><MARKET_FEEDBACK_DATA><MARKET_FEEDBACK_ID><![CDATA[MFE002720150907182809]]></MARKET_FEEDBACK_ID><route_code><![CDATA[]]></route_code><PRODUCT_GROUP><![CDATA[Rice Bran]]></PRODUCT_GROUP><COMPETITOR_NAME><![CDATA[Ruchira]]></COMPETITOR_NAME><PTD><![CDATA[0]]></PTD><PTR><![CDATA[]]></PTR><PTC><![CDATA[]]></PTC></MARKET_FEEDBACK_DATA><MARKET_FEEDBACK_DATA><MARKET_FEEDBACK_ID><![CDATA[MFE002720150907182809]]></MARKET_FEEDBACK_ID><route_code><![CDATA[]]></route_code><PRODUCT_GROUP><![CDATA[Rice Bran]]></PRODUCT_GROUP><COMPETITOR_NAME><![CDATA[Mahakosh]]></COMPETITOR_NAME><PTD><![CDATA[0]]></PTD><PTR><![CDATA[]]></PTR><PTC><![CDATA[]]></PTC></MARKET_FEEDBACK_DATA></MARKET_FEEDBACK></root>";*/
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><MARKET_FEEDBACK><location><emp_code><![CDATA[E0004]]></emp_code><trans_id><![CDATA[MFE000420160418172753]]></trans_id><latt><![CDATA[23.3711898]]></latt><longi><![CDATA[85.3403768]]></longi><date><![CDATA[2016-04-18 17:27:53]]></date></location><MARKET_FEEDBACK_DATA><MARKET_FEEDBACK_ID><![CDATA[MFE000420160418172753]]></MARKET_FEEDBACK_ID><route_code><![CDATA[RT/1330]]></route_code><customer_code><![CDATA[C/0002298]]></customer_code><PRODUCT_GROUP><![CDATA[]]></PRODUCT_GROUP><COMPETITOR_NAME><![CDATA[ULTRATECH PPC]]></COMPETITOR_NAME><PTD><![CDATA[]]></PTD><PTR><![CDATA[]]></PTR><PTC><![CDATA[285]]></PTC><PV><![CDATA[315]]></PV></MARKET_FEEDBACK_DATA></MARKET_FEEDBACK></root>";*/
$attendance_emp_code = "*ROOT*ATTENDANCE*LOCATION*EMP_CODE";
$attendance_trans_id = "*ROOT*ATTENDANCE*LOCATION*TRANS_ID";
$attendance_latt = "*ROOT*ATTENDANCE*LOCATION*LATT";
$attendance_longi = "*ROOT*ATTENDANCE*LOCATION*LONGI";
$attendance_date = "*ROOT*ATTENDANCE*LOCATION*DATE";
$attendancedata_emp_code = "*ROOT*ATTENDANCE*ATTENDANCEDATA*EMP_CODE";
$attendancedata_date = "*ROOT*ATTENDANCE*ATTENDANCEDATA*DATE";


$location_emp_code="*ROOT*MARKET_FEEDBACK*LOCATION*EMP_CODE";
$location_trans_id = "*ROOT*MARKET_FEEDBACK*LOCATION*TRANS_ID";
$location_latt = "*ROOT*MARKET_FEEDBACK*LOCATION*LATT";
$location_longi = "*ROOT*MARKET_FEEDBACK*LOCATION*LONGI";
$location_date="*ROOT*MARKET_FEEDBACK*LOCATION*DATE";

$market_feedback_id = "*ROOT*MARKET_FEEDBACK*MARKET_FEEDBACK_DATA*MARKET_FEEDBACK_ID";
$route_code="*ROOT*MARKET_FEEDBACK*MARKET_FEEDBACK_DATA*ROUTE_CODE";
$customer_code="*ROOT*MARKET_FEEDBACK*MARKET_FEEDBACK_DATA*CUSTOMER_CODE";
$product_group = "*ROOT*MARKET_FEEDBACK*MARKET_FEEDBACK_DATA*PRODUCT_GROUP";
$competitor_name = "*ROOT*MARKET_FEEDBACK*MARKET_FEEDBACK_DATA*COMPETITOR_NAME";
$PTD = "*ROOT*MARKET_FEEDBACK*MARKET_FEEDBACK_DATA*PTD";
$PTR = "*ROOT*MARKET_FEEDBACK*MARKET_FEEDBACK_DATA*PTR";
$PTC = "*ROOT*MARKET_FEEDBACK*MARKET_FEEDBACK_DATA*PTC";
$PV = "*ROOT*MARKET_FEEDBACK*MARKET_FEEDBACK_DATA*PV";

$billing_ex_for = "*ROOT*MARKET_FEEDBACK*MARKET_FEEDBACK_DATA*BILLING_EX_FOR";
$wsp_ex_for = "*ROOT*MARKET_FEEDBACK*MARKET_FEEDBACK_DATA*WSP_EX_FOR";
$rsp_ex_for = "*ROOT*MARKET_FEEDBACK*MARKET_FEEDBACK_DATA*RSP_EX_FOR";
$nod_ex_for = "*ROOT*MARKET_FEEDBACK*MARKET_FEEDBACK_DATA*NOD_EX_FOR";

$attendance_array = array();
$market_feedback_array=array();
$market_feedback_data_array=array();

$counteratt=0;
$counter = 0;
$countermarketdata=0;
class xml_attendance{
    var $emp_code, $trans_id,$latt,$longi,$attendance_date,$attendancedata_emp_code,$attendancedata_date;
}
class xml_market_feedback{
	var $location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date;
}
class xml_market_feedback_data{
	var $market_feedback_id,$route_code,$customer_code,$product_group,$competitor_name,$PTD,$PTR,$PTC,$PV,$billing_ex_for,$wsp_ex_for,$rsp_ex_for,$nod_ex_for;
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
    global $current_tag,$attendance_emp_code, $attendance_trans_id,$attendance_latt,$attendance_longi,$attendance_date,$attendancedata_emp_code,$attendancedata_date,$counter,$counteratt,$countermarketdata,$location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$market_feedback_id,$route_code,$customer_code,$product_group,$competitor_name,$PTD,$PTR,$PTC,$PV,
	$billing_ex_for,$wsp_ex_for,$rsp_ex_for,$nod_ex_for,$market_feedback_array,$market_feedback_data_array,$attendance_array;
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

	if(substr($current_tag,0,21)=='*ROOT*MARKET_FEEDBACK')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $location_emp_code:
				$market_feedback_array[$counter] = new xml_market_feedback();
				$market_feedback_array[$counter]->location_emp_code = $data;
				break;
			case $location_trans_id:
				$market_feedback_array[$counter]->location_trans_id = $data;
				break;
			case $location_latt:
				$market_feedback_array[$counter]->location_latt = $data;
				break;
			case $location_longi:
				$market_feedback_array[$counter]->location_longi = $data;
				break;
			case $location_date:
				$market_feedback_array[$counter]->location_date = $data;
				$counter++;
				break;
		}
	}
	if(substr($current_tag,0,42)=='*ROOT*MARKET_FEEDBACK*MARKET_FEEDBACK_DATA')
	  {
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $market_feedback_id:
				$market_feedback_data_array[$countermarketdata] = new xml_market_feedback_data();
				$market_feedback_data_array[$countermarketdata]->market_feedback_id = $data;
				break;
			case $route_code:
				$market_feedback_data_array[$countermarketdata]->route_code = $data;
				break;
			case $customer_code:
				$market_feedback_data_array[$countermarketdata]->customer_code = $data;
				break;
			case $product_group:
				$market_feedback_data_array[$countermarketdata]->product_group = $data;
				break;
			case $competitor_name:
				$market_feedback_data_array[$countermarketdata]->competitor_name = $data;
				break;
			case $PTD:
				$market_feedback_data_array[$countermarketdata]->PTD = $data;
				break;
			case $PTR:
				$market_feedback_data_array[$countermarketdata]->PTR = $data;
				break;
			case $PTC:
				$market_feedback_data_array[$countermarketdata]->PTC = $data;
				break;
			case $PV:
				$market_feedback_data_array[$countermarketdata]->PV = $data;
				break;
			case $billing_ex_for:
				$market_feedback_data_array[$countermarketdata]->billing_ex_for = $data;
				break;
			case $wsp_ex_for:
				$market_feedback_data_array[$countermarketdata]->wsp_ex_for = $data;
				break;
			case $rsp_ex_for:
				$market_feedback_data_array[$countermarketdata]->rsp_ex_for = $data;
				break;
			case $nod_ex_for:
				$market_feedback_data_array[$countermarketdata]->nod_ex_for = $data;
				$countermarketdata++;
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



		if(DCR_map=='no')
		{
			if($latt>0 && $longi>0)
			{
				$sqlupdatelatlongzero="UPDATE location SET latt='".$latt."',longi='".$longi."' WHERE emp_code='".$emp_code."' AND latt='0' AND longi='0'";
				$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero);
			}
		}

		//For checking that trans id exist or not
		$sqlchkattlocation="SELECT * FROM location WHERE trans_id='".$trans_id."'";
		$reschkattlocation = mysqli_query($link,$sqlchkattlocation) or die(mysqli_error()." Error in check attendance location: ".$sqlchkattlocation);
		$rowchkattlocation = mysqli_fetch_assoc($reschkattlocation);
		$countchkattlocation=mysqli_num_rows($reschkattlocation);
		if($latt==""){
		   $latt="0";
		}
		if($longi==""){
		   $longi="0";
		}
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
				$last_operation_datetime=$location_date;

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
        	if($latt==""){
        	    $latt="0";
        	}
        	if($longi==""){
        		 $longi="0";
        	}
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

					//Start for STAR mis data details present
					if($nick_name=='STAR')
					{
						$qty='';
						$trans_type='A';
						$trans_sub_type='';
						update_transaction_STAR($emp_code,$trans_id,$qty,$trans_type,$trans_sub_type);
					}
					//End for STAR mis data details present
					$last_operation_datetime=$attendance_date;
					// For Sending email to recipents for attendance
				 	$sqlempname="SELECT emp_name,vertical_value,branch_code FROM employee_master WHERE emp_code='".$emp_code."'";
					$rsempname=mysqli_query($link,$sqlempname);
					$rowempname=mysqli_fetch_assoc($rsempname);
					$emp_name=title_case_emp($rowempname['emp_name']);
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

/* --------------------START QUERY FOR survey------------------------------------------------------------------------------------------------*/
//print_r($market_feedback_array);
$market_feedback_array_trans_id=array();
if(count($market_feedback_array)>0)
{
		for($x=0;$x<count($market_feedback_array);$x++){
			$location_emp_code=$market_feedback_array[$x]->location_emp_code;
			$location_trans_id=$market_feedback_array[$x]->location_trans_id;
			$location_latt=$market_feedback_array[$x]->location_latt;
			$location_longi=$market_feedback_array[$x]->location_longi;
			$location_date=$market_feedback_array[$x]->location_date;

			//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
			if($location_latt>0 && $location_longi>0)
			{
				$sqlupdatelatlongzero="UPDATE location SET latt='".$location_latt."',longi='".$location_longi."' WHERE
										emp_code='".$location_emp_code."' AND latt='0' AND longi='0'";
				$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero);
			}

			//For checking that trans id exist or not for market feedback
			$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$location_trans_id."'";
			$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check market feedback: ".$sqlchkorlocation);
			$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
			$countchkorlocation=mysqli_num_rows($reschkorlocation);

			//For update the location table for existing trans id for market feedback
			if($countchkorlocation>0)
			{
				if(!in_array($location_trans_id,$market_feedback_array_trans_id))
				{
					array_push($market_feedback_array_trans_id,$location_trans_id);
				}
				if($location_longi==""){
				    $location_longi="0";
				}
				if($location_latt==""){
				    $location_latt="0";
				}
				$sqlupdateorlocation="UPDATE location SET emp_code='".$location_emp_code."',
										latt='".$location_latt."',
										longi='".$location_longi."'
										WHERE trans_id='".$location_trans_id."'";
				$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update market feedback location: ".$sqlupdateorlocation);
				if($rsupdateorlocation)
				{
					$flag=6;
					$last_operation_datetime=$location_date;
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
		    if($location_latt==""){
		        $location_latt="0";
    		}
    		if($location_longi==""){
    		   $location_longi="0";
    		}
			//For Insert into the location table for new trans id regarding market feedback
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
		}
	 }
	 //print_r($market_feedback_data_array);
	 //duplicate check
	 $seen = [];


	for($i=0;$i<count($market_feedback_data_array);$i++){
		$market_feedback_id=$market_feedback_data_array[$i]->market_feedback_id;
		$route_code=$market_feedback_data_array[$i]->route_code;
		$customer_code=$market_feedback_data_array[$i]->customer_code;
		$product_group=$market_feedback_data_array[$i]->product_group;
		$competitor_name=$market_feedback_data_array[$i]->competitor_name;
		$PTD=$market_feedback_data_array[$i]->PTD;
		$PTR=$market_feedback_data_array[$i]->PTR;
		$PTC=$market_feedback_data_array[$i]->PTC;
		$PV=$market_feedback_data_array[$i]->PV;
		$billing_ex_for=$market_feedback_data_array[$i]->billing_ex_for;
		$wsp_ex_for=$market_feedback_data_array[$i]->wsp_ex_for;
		$rsp_ex_for=$market_feedback_data_array[$i]->rsp_ex_for;
		$nod_ex_for=$market_feedback_data_array[$i]->nod_ex_for;
		//sk add check 29-05-25
		// Create a unique key based on customer_code and competitor_name
		$unique_key = $customer_code . '|' . $competitor_name;

		// Skip if this combination has already been processed
		if (isset($seen[$unique_key])) {
			continue;
		}

		// Mark this combination as seen
		$seen[$unique_key] = true;


	if($PV==""){
	    $PV = "0";
	}
	if($PTD==""){
	    $PTD = "0";
	}
	if($PTR==""){
	    $PTR = "0";
	}
	if($PTC==""){
	    $PTC = "0";
	}
	if($billing_ex_for==""){
	    $billing_ex_for = "0";
	}
	if($wsp_ex_for==""){
	    $wsp_ex_for = "0";
	}if($rsp_ex_for==""){
	    $rsp_ex_for = "0";
	}if($nod_ex_for==""){
	    $nod_ex_for = "0";
	}
		//For addition of market feedback
		$sqlinsertmarketfeedback="INSERT INTO market_feedback SET market_feedback_id	='".$market_feedback_id."',
								route_code					='".$route_code."',
								customer_code				='".$customer_code."',
								product_group				='".$product_group."',
								competitor_name				='".$competitor_name."',
								PTD							='".$PTD."',
								PTR						    ='".$PTR."',
								PTC					        ='".$PTC."',
								PV					        ='".$PV."',
								billing_ex_for			    ='".$billing_ex_for."',
								wsp_ex_for					='".$wsp_ex_for."',
								rsp_ex_for			        ='".$rsp_ex_for."',
								nod_ex_for					='".$nod_ex_for."'";
								//echo $sqlinsertmarketfeedback;
		if(mysqli_query($link,$sqlinsertmarketfeedback))
		{
			$flag=5;
			$competitor_condition ='';
			//For copetitor pricing log generation
			if($nick_name='STAR' || $nick_name='ECEMENT'){
			$sqlcompetitorpricechk="SELECT customer_code FROM competitor_pricing WHERE customer_code='".$customer_code."'
									AND emp_code='".$emp_code."' AND date_time=CURDATE()";
									//echo $sqlcompetitorpricechk;
			$rscompetitorpricechk=mysqli_query($link,$sqlcompetitorpricechk);
			//echo "kgkjgg".$competitor_name;
				if($competitor_name=='AMBUJA PPC')
				{
					$competitor_condition="ambuja_PTD='".$PTD."',ambuja_PTR='".$PTR."',ambuja_PTC='".$PTC."',ambuja_PV='".$PV."'";
				}
				else if($competitor_name=='ULTRATECH PPC')
				{
					$competitor_condition="ultratech_PTD='".$PTD."',ultratech_PTR='".$PTR."',ultratech_PTC='".$PTC."',ultratech_PV='".$PV."'";
				}
				else if($competitor_name=='LAFARGE PPC')
				{
					$competitor_condition="lafarge_PTD='".$PTD."',lafarge_PTR='".$PTR."',lafarge_PTC='".$PTC."',lafarge_PV='".$PV."'";
				}
				else if($competitor_name=='DALMIA PPC')
				{
					$competitor_condition="dalmia_PTD='".$PTD."',dalmia_PTR='".$PTR."',dalmia_PTC='".$PTC."',dalmia_PV='".$PV."'";
				}
				else if($competitor_name=='TOPCEM PPC')
				{
					$competitor_condition="topcem_PTD='".$PTD."',topcem_PTR='".$PTR."',topcem_PTC='".$PTC."',topcem_PV='".$PV."'";
				}
				else if($competitor_name=='ACC PPC')
				{
					$competitor_condition="acc_PTD='".$PTD."',acc_PTR='".$PTR."',acc_PTC='".$PTC."',acc_PV='".$PV."'";
				}
				else if($competitor_name=='BIRLA GOLD PPC')
				{
					$competitor_condition="birla_gold_PTD='".$PTD."',birla_gold_PTR='".$PTR."',birla_gold_PTC='".$PTC."',birla_gold_PV='".$PV."'";
				}else if($competitor_name=='STAR CEMENT PPC')
				{
					$competitor_condition="star_PTD='".$PTD."',star_PTR='".$PTR."',star_PTC='".$PTC."',star_PV='".$PV."'";
				}

			//	STAR CEMENT PPC(ADSTAR)


//echo "cccccc".$competitor_name;
			$countcompetitorpricechk=mysqli_num_rows($rscompetitorpricechk);
			if($countcompetitorpricechk >0)
			{
			    if($competitor_condition!=""){
    				$sqlupdatecompetitorprice="UPDATE competitor_pricing SET ".$competitor_condition." WHERE emp_code ='".$emp_code."' AND
    											date_time=CURDATE() AND customer_code ='".$customer_code."'";
    				mysqli_query($link,$sqlupdatecompetitorprice);
			    }
			}
			else
			{
			    if($competitor_condition==""){
			       /* $sqlinsertcompetitorprice="INSERT INTO competitor_pricing SET emp_code	='".$emp_code."',
											date_time=CURDATE(),
											customer_code ='".$customer_code."'";*/
			    }else{
				$sqlinsertcompetitorprice="INSERT INTO competitor_pricing SET emp_code	='".$emp_code."',
											date_time=CURDATE(),
											customer_code ='".$customer_code."',".$competitor_condition."";

			        mysqli_query($link,$sqlinsertcompetitorprice);
			    }
											//echo $sqlinsertcompetitorprice;

			}
		  }//End of STAR if
		}
		else
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}
	}
}
 /* --------------------END QUERY FOR Market feedback--------------------------------------------------------------------------------------------------------*/
 if($last_operation_datetime==""){
     $last_operation_datetime=date('Y-m-d h:i:s');
 }
if($flag==5)
{
	 $sqlupdatelastoperationtime="UPDATE changepassword SET last_operation_datetime='".$last_operation_datetime."' WHERE emp_code='".$emp_code."'";
	 $rsupdatelastoperationtime=mysqli_query($link,$sqlupdatelastoperationtime);
	 mysqli_query($link,"COMMIT");

	 /*if($countdatarefresh >0)
	 {
		 echo $flag=2;
	 }
	 else
	 {*/
	 	echo $flag=1;
	 //}
}
if($flag==6)
{
 $sqlupdatelastoperationtime="UPDATE changepassword SET last_operation_datetime='".$last_operation_datetime."' WHERE emp_code='".$emp_code."'";
	 $rsupdatelastoperationtime=mysqli_query($link,$sqlupdatelastoperationtime);
	 mysqli_query($link,"COMMIT");

	/* if($countdatarefresh >0)
	 {
		 echo $flag=2;
	 }
	 else
	 {*/
	 	echo $flag=1;
	//}
}
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = APICALLLOGURL."/operationdb-market-feedback.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
mysqli_close($link);
?>
