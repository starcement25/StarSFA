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
$last_update_time=$_REQUEST['last_update_time'] ?? '';
$last_update_time=str_replace('€',' ',$last_update_time);

$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
//echo $sqlquery;
$result = mysqli_query($link,$sqlquery);
$countdatarefresh=mysqli_num_rows($result);
$body=file_get_contents('php://input');

$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
						xml='".$body_xml."',
						insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><yellowcard_info><location><emp_code><![CDATA[E0046]]></emp_code><trans_id><![CDATA[YE00462017021820170218174204]]></trans_id><latt><![CDATA[22.5640687]]></latt><longi><![CDATA[88.3567542]]></longi><date><![CDATA[2017-02-18 17:42:05]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE00462017021820170218174204]]></yellowcard_no><customer_code><![CDATA[C/0129337]]></customer_code><challan_no><![CDATA[123]]></challan_no><challan_date><![CDATA[18-03-17]]></challan_date><qty><![CDATA[123]]></qty><qty_UOM><![CDATA[PSC]]></qty_UOM></yellowcard_details></yellowcard_info></root>";*/


$attendance_emp_code = "*ROOT*ATTENDANCE*LOCATION*EMP_CODE";
$attendance_trans_id = "*ROOT*ATTENDANCE*LOCATION*TRANS_ID";
$attendance_latt = "*ROOT*ATTENDANCE*LOCATION*LATT";
$attendance_longi = "*ROOT*ATTENDANCE*LOCATION*LONGI";
$attendance_date = "*ROOT*ATTENDANCE*LOCATION*DATE";
$attendancedata_emp_code = "*ROOT*ATTENDANCE*ATTENDANCEDATA*EMP_CODE";
$attendancedata_date = "*ROOT*ATTENDANCE*ATTENDANCEDATA*DATE";

$location_emp_code="*ROOT*YELLOWCARD_INFO*LOCATION*EMP_CODE";
$location_trans_id = "*ROOT*YELLOWCARD_INFO*LOCATION*TRANS_ID";
$location_latt = "*ROOT*YELLOWCARD_INFO*LOCATION*LATT";
$location_longi = "*ROOT*YELLOWCARD_INFO*LOCATION*LONGI";
$location_date="*ROOT*YELLOWCARD_INFO*LOCATION*DATE";

$yellowcard_no = "*ROOT*YELLOWCARD_INFO*YELLOWCARD_DETAILS*YELLOWCARD_NO";
$customer_code = "*ROOT*YELLOWCARD_INFO*YELLOWCARD_DETAILS*CUSTOMER_CODE";
$challan_no = "*ROOT*YELLOWCARD_INFO*YELLOWCARD_DETAILS*CHALLAN_NO";
$challan_date = "*ROOT*YELLOWCARD_INFO*YELLOWCARD_DETAILS*CHALLAN_DATE";
$qty = "*ROOT*YELLOWCARD_INFO*YELLOWCARD_DETAILS*QTY";
$qty_UOM = "*ROOT*YELLOWCARD_INFO*YELLOWCARD_DETAILS*QTY_UOM";

$attendance_array = array();
$yellowcard_array=array();

$counteratt=0;
$counter = 0;

class xml_attendance{
    var $emp_code, $trans_id,$latt,$longi,$attendance_date,$attendancedata_emp_code,$attendancedata_date;
}
class xml_yellowcard{
   var $location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$yellowcard_no,$customer_code,$challan_no,$challan_date,
   $qty,$qty_UOM;	
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
    global $current_tag,$attendance_emp_code, $attendance_trans_id,$attendance_latt,$attendance_longi,$attendance_date,$attendancedata_emp_code,$attendancedata_date,$counter,$counteratt,$location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$yellowcard_no,$customer_code,$challan_no,$challan_date,$qty,$qty_UOM,$yellowcard_array,$attendance_array;
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
	if(substr($current_tag,0,21)=='*ROOT*YELLOWCARD_INFO')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $location_emp_code:
				$yellowcard_array[$counter] = new xml_yellowcard();
				$yellowcard_array[$counter]->location_emp_code = $data;
				break;
			case $location_trans_id:
				$yellowcard_array[$counter]->location_trans_id = $data;
				break;
			case $location_latt:
				$yellowcard_array[$counter]->location_latt = $data;
				break;
			case $location_longi:
				$yellowcard_array[$counter]->location_longi = $data;
				break;
			case $location_date:
				$yellowcard_array[$counter]->location_date = $data;
				break;
			case $yellowcard_no:
				$yellowcard_array[$counter]->yellowcard_no = $data;
				break;
			case $customer_code:
				$yellowcard_array[$counter]->customer_code = $data;
				break;
			case $challan_no:
				$yellowcard_array[$counter]->challan_no = $data;
				break;
			case $challan_date:
				$yellowcard_array[$counter]->challan_date = $data;
				break;
			case $qty:
				$yellowcard_array[$counter]->qty = $data;
				break;	
			case $qty_UOM:
				$yellowcard_array[$counter]->qty_UOM = $data;
				$counter++;
				break;
		}
	}
}
$xml_parser = xml_parser_create();
xml_set_element_handler($xml_parser, "startTag", "endTag");
xml_set_character_data_handler($xml_parser, "contents");
$data = $body;
$flag=1;
if(!(xml_parse($xml_parser, $data, LIBXML_PARSEHUGE))){
    die("Error on line " . xml_get_current_line_number($xml_parser));
}
xml_parser_free($xml_parser);
//print_r($attendance_array);
//print_r($payment_array);
mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");
//echo "OK";die;

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

/* --------------------START QUERY FOR YELLOWCARD INFO------------------------------------------------------------------------------------------------*/
//print_r($new_customer_array);

$yellowcard_array_trans_id=array();
if(count($yellowcard_array)>0)
{
	for($x=0;$x<count($yellowcard_array);$x++){
		$location_emp_code=$yellowcard_array[$x]->location_emp_code;
		$location_trans_id=$yellowcard_array[$x]->location_trans_id;
		$location_latt=$yellowcard_array[$x]->location_latt;
		$location_longi=$yellowcard_array[$x]->location_longi;
		$location_date=$yellowcard_array[$x]->location_date;
		$yellowcard_no=$yellowcard_array[$x]->yellowcard_no;
		$customer_code= $yellowcard_array[$x]->customer_code;
		$challan_no= $yellowcard_array[$x]->challan_no;
		$challan_date=$yellowcard_array[$x]->challan_date;
		$qty=$yellowcard_array[$x]->qty;
		$qty_UOM=$yellowcard_array[$x]->qty_UOM;
		
		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if($location_latt>0 && $location_longi>0)
		{
			$sqlupdatelatlongzero="UPDATE location SET latt='".$location_latt."',longi='".$location_longi."' WHERE 
									emp_code='".$location_emp_code."' AND latt='0' AND longi='0'";
			$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
		}

		//For checking that trans id exist or not for yellowcard
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$location_trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check new customer: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for yellowcard
		if($countchkorlocation>0)
		{
			if(!in_array($location_trans_id,$yellowcard_array_trans_id))
			{
				array_push($yellowcard_array_trans_id,$location_trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$location_emp_code."',
									latt='".$location_latt."',
									longi='".$location_longi."'
									WHERE trans_id='".$location_trans_id."'";
			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update yellowcard info location: ".$sqlupdateorlocation);
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
		
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));

		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$location_date_updatetime=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;

		//For Insert into the location table for new trans id regarding YELLOWCARD info
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
		$sqllinkdealer="SELECT rds_tag FROM customer_master WHERE customer_code ='".$customer_code."'";
		$rslinkdealer=mysqli_query($link,$sqllinkdealer);
		$rowlinkdealer=mysqli_fetch_assoc($rslinkdealer);
		$linkeddealer=$rowlinkdealer['rds_tag'];
		
		//For Insert into the yellow card table for new trans id
		$sqlinsertyellowcard="INSERT INTO yellow_card_details SET yellow_card_no	='".$yellowcard_no."',
							  customer_code 	='".$customer_code."',
							  challan_no 		='".addslashes($challan_no)."',
							  challan_date		='".$challan_date."',
							  qty				='".$qty."',
							  qty_UOM          	='".$qty_UOM."',
							  linked_dealer_code ='".$linkeddealer."'";
							  
							  
		if(mysqli_query($link,$sqlinsertyellowcard))
		{
			$flag=5;
			if(strtoupper($nick_name)=='STAR')
			{
				$challan_date_parts=substr($challan_date,5,2);
				$challan_date_parts_one=substr($challan_date,0,4);
				
			$sqlchkyellowcardcount="SELECT customer_code FROM RSSD_yellow_card_count WHERE customer_code='".$customer_code."'";
			$rschkyellowcardcount=mysqli_query($link,$sqlchkyellowcardcount);
			$cntyellowcardcount=mysqli_num_rows($rschkyellowcardcount);
			if(strtotime($challan_date) >=strtotime('2020-04-01'))
			{
				$activity_column='curr_'.$challan_date_parts;
			}
			else
			{
				$activity_column='prev_'.$challan_date_parts;
			}
			if($cntyellowcardcount >0)
			{
				$sqlupdateyellowcardcount="UPDATE RSSD_yellow_card_count SET $activity_column=($activity_column+$qty) WHERE customer_code='".$customer_code."'";
				mysqli_query($link,$sqlupdateyellowcardcount);
			}
			else
			{
				$sqlselcustdetails="SELECT DISTINCT CM.customer_name,RM.route_code,RM.route_name,CM.appointment_date FROM customer_master CM,customer_route_emp_relation CRR,route_master RM
									WHERE CM.customer_code=CRR.customer_code AND CRR.route_code=RM.route_code AND CM.customer_code='".$customer_code."' 
									AND CRR.acedns='Y'";
				$rsselcustdetails=mysqli_query($link,$sqlselcustdetails);
				$rowselcustdetils=mysqli_fetch_assoc($rsselcustdetails);
				$sub_dealer_name=$rowselcustdetils['customer_name'];
				$sub_dealer_route_code=$rowselcustdetils['route_code'];
				$sub_dealer_route_name=$rowselcustdetils['route_name'];
				$appointment_date=$rowselcustdetils['appointment_date'];
				
				$sqldealername="SELECT customer_name FROM customer_master WHERE customer_code='".$linkeddealer."'";
				$rsdealername=mysqli_query($link,$sqldealername);
				$rowdealername=mysqli_fetch_assoc($rsdealername);
				$dealer_name=$rowdealername['customer_name'];
				
				$rsselcustdetails=mysqli_query($link,$sqlselcustdetails);
				$rowselcustdetils=mysqli_fetch_assoc($rsselcustdetails);					
				$sqlinsertyellowcardcount="INSERT INTO RSSD_yellow_card_count SET
											customer_code='".$customer_code."',
											customer_name='".$sub_dealer_name."',
											linked_dealer_code='".$linkeddealer."',
											linked_dealer_name='".$dealer_name."',
											route_code='".$sub_dealer_route_code."',
											route_name='".$sub_dealer_route_name."',
											appointment_date='".$appointment_date."',
											$activity_column='".$qty."'";
				mysqli_query($link,$sqlinsertyellowcardcount);							
			}
			}
			
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
 /* --------------------END QUERY FOR YELLOWCARD INFO--------------------------------------------------------------------------------------------------------*/
 
 $last_operation_datetime = date("Y-m-d H:i:s");
 
 
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
$url = APICALLLOGURL."/operationdb-yellowcard-details.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
mysqli_close($link);
?>
