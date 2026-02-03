<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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

$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
						xml='".$body_xml."',
						insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><wholesaler_info><location><emp_code><![CDATA[E0010]]></emp_code><trans_id><![CDATA[WE001020160425102210]]></trans_id><latt><![CDATA[22.720395]]></latt><longi><![CDATA[87.68254]]></longi><date><![CDATA[2016-04-25 10:22:10]]></date></location><wholesaler_details><wholesale_trans_id><![CDATA[WE001020160425102210]]></wholesale_trans_id><customer_code><![CDATA[C/00001]]></customer_code><debit_not_collected><![CDATA[Y]]></debit_not_collected><last_debit_note_received><![CDATA[05-2016]]></last_debit_note_received><closing_stock_value><![CDATA[39]]></closing_stock_value><log_book><![CDATA[N]]></log_book></wholesaler_details></wholesaler_info></root>";*/

$attendance_emp_code = "*ROOT*ATTENDANCE*LOCATION*EMP_CODE";
$attendance_trans_id = "*ROOT*ATTENDANCE*LOCATION*TRANS_ID";
$attendance_latt = "*ROOT*ATTENDANCE*LOCATION*LATT";
$attendance_longi = "*ROOT*ATTENDANCE*LOCATION*LONGI";
$attendance_date = "*ROOT*ATTENDANCE*LOCATION*DATE";
$attendancedata_emp_code = "*ROOT*ATTENDANCE*ATTENDANCEDATA*EMP_CODE";
$attendancedata_date = "*ROOT*ATTENDANCE*ATTENDANCEDATA*DATE";

$location_emp_code="*ROOT*WHOLESALER_INFO*LOCATION*EMP_CODE";
$location_trans_id = "*ROOT*WHOLESALER_INFO*LOCATION*TRANS_ID";
$location_latt = "*ROOT*WHOLESALER_INFO*LOCATION*LATT";
$location_longi = "*ROOT*WHOLESALER_INFO*LOCATION*LONGI";
$location_date="*ROOT*WHOLESALER_INFO*LOCATION*DATE";

$wholesale_trans_id = "*ROOT*WHOLESALER_INFO*WHOLESALER_DETAILS*WHOLESALE_TRANS_ID";
$customer_code = "*ROOT*WHOLESALER_INFO*WHOLESALER_DETAILS*CUSTOMER_CODE";
$debit_not_collected = "*ROOT*WHOLESALER_INFO*WHOLESALER_DETAILS*DEBIT_NOT_COLLECTED";
$last_debit_note_received = "*ROOT*WHOLESALER_INFO*WHOLESALER_DETAILS*LAST_DEBIT_NOT_RECEIVED";
$closing_stock_value = "*ROOT*WHOLESALER_INFO*WHOLESALER_DETAILS*CLOSING_STOCK_VALUE";
$log_book = "*ROOT*WHOLESALER_INFO*WHOLESALER_DETAILS*LOG_BOOK";

$attendance_array = array();
$wholesaler_array=array();

$counteratt=0;
$counter = 0;

class xml_attendance{
    var $emp_code, $trans_id,$latt,$longi,$attendance_date,$attendancedata_emp_code,$attendancedata_date;
}
class xml_wholesaler{
	var $location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$wholesale_trans_id,$customer_code,$debit_not_collected,$last_debit_note_received,$closing_stock_value,$log_book;	
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
    global $current_tag,$attendance_emp_code, $attendance_trans_id,$attendance_latt,$attendance_longi,$attendance_date,$attendancedata_emp_code,$attendancedata_date,$counter,$counteratt,$location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$wholesale_trans_id,$customer_code,$debit_not_collected,$last_debit_note_received,$closing_stock_value,$log_book,$wholesaler_array,$attendance_array;
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
	if(substr($current_tag,0,21)=='*ROOT*WHOLESALER_INFO')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $location_emp_code:
				$wholesaler_array[$counter] = new xml_wholesaler();
				$wholesaler_array[$counter]->location_emp_code = $data;
				break;
			case $location_trans_id:
				$wholesaler_array[$counter]->location_trans_id = $data;
				break;
			case $location_latt:
				$wholesaler_array[$counter]->location_latt = $data;
				break;
			case $location_longi:
				$wholesaler_array[$counter]->location_longi = $data;
				break;
			case $location_date:
				$wholesaler_array[$counter]->location_date = $data;
				break;
			case $wholesale_trans_id:
				$wholesaler_array[$counter]->wholesale_trans_id = $data;
				break;
			case $customer_code:
				$wholesaler_array[$counter]->customer_code = $data;
				break;
			case $debit_not_collected:
				$wholesaler_array[$counter]->debit_not_collected = $data;
				break;
			case $last_debit_note_received:
				$wholesaler_array[$counter]->last_debit_note_received = $data;
				break;
			case $closing_stock_value:
				$wholesaler_array[$counter]->closing_stock_value = $data;
				break;	
			case $log_book:
				$wholesaler_array[$counter]->log_book = $data;
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

/* --------------------START QUERY FOR WHOLESALER INFO------------------------------------------------------------------------------------------------*/
//print_r($new_customer_array);
$wholesaler_array_trans_id=array();
if(count($wholesaler_array)>0)
{
	for($x=0;$x<count($wholesaler_array);$x++){
		$location_emp_code=$wholesaler_array[$x]->location_emp_code;
		$location_trans_id=$wholesaler_array[$x]->location_trans_id;
		$location_latt=$wholesaler_array[$x]->location_latt;
		$location_longi=$wholesaler_array[$x]->location_longi;
		$location_date=$wholesaler_array[$x]->location_date;
		$wholesale_trans_id=$wholesaler_array[$x]->wholesale_trans_id;
		$customer_code= $wholesaler_array[$x]->customer_code;
		$debit_not_collected=$wholesaler_array[$x]->debit_not_collected;
		$last_debit_note_received=$wholesaler_array[$x]->last_debit_note_received;
		$closing_stock_value=$wholesaler_array[$x]->closing_stock_value;
		$log_book= $wholesaler_array[$x]->log_book;
		
		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if($location_latt>0 && $location_longi>0)
		{
			$sqlupdatelatlongzero="UPDATE location SET latt='".$location_latt."',longi='".$location_longi."' WHERE 
									emp_code='".$location_emp_code."' AND latt='0' AND longi='0'";
			$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
		}

		//For checking that trans id exist or not for order
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$location_trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check new customer: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for order
		if($countchkorlocation>0)
		{
			if(!in_array($location_trans_id,$wholesaler_array_trans_id))
			{
				array_push($wholesaler_array_trans_id,$location_trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$location_emp_code."',
									latt='".$location_latt."',
									longi='".$location_longi."'
									WHERE trans_id='".$location_trans_id."'";
			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update wholesaler info location: ".$sqlupdateorlocation);
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
		/*$sqlempname="SELECT emp_name,branch_code,vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
		$rsempname=mysqli_query($link,$sqlempname);
		$rowempname=mysqli_fetch_assoc($rsempname);
		$emp_name=title_case_emp($rowempname['emp_name']);
		$branch_code=$rowempname['branch_code'];
		$vertical_value=$rowempname['vertical_value'];*/
		
		$random_no_length=7-strlen($nick_name);//7 is the maximum length of the company nick name
		$foldernamerand=$nick_name.rand(pow(10, $random_no_length-1), pow(10, $random_no_length)-1);
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));

		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$location_date_updatetime=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;

		//For Insert into the location table for new trans id regarding wholesaler info
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
		
		$sqlwholesaler="SELECT customer_code FROM wholesaler_details WHERE customer_code='".$customer_code."'";
		$rswholesaler=mysqli_query($link,$sqlwholesaler);
		$countwholesaler=mysqli_num_rows($rswholesaler);
		if($countwholesaler<1)
		{
			//For addition of Wholesaler info
			$sqlinsertwholesaler="INSERT INTO wholesaler_details SET wholesale_trans_id ='".$wholesale_trans_id."',
							   customer_code 				='".$customer_code."',
							   debit_not_collected			='".$debit_not_collected."',
							   last_debit_note_received		='".$last_debit_note_received."',
							   closing_stock_value			='".$closing_stock_value."',
							   log_book						='".$log_book."',
							   download_time				=CURRENT_TIMESTAMP()";
			if(mysqli_query($link,$sqlinsertwholesaler))
			{
				$flag=5;
				$refreshflag=1;
			}
			else
			{
				mysqli_query($link,"ROLLBACK");
				echo $flag=0;
				return;
			}
		}//End of if
	  }//End of else
	}
}
 /* --------------------END QUERY FOR WHOLESALER INFO--------------------------------------------------------------------------------------------------------*/
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
$url = APICALLLOGURL."/operationdb-wholesaler-details.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
mysqli_close($link);
?>
