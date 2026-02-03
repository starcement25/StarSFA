<?php
 /*ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);*/
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

function getReverseGeo($latitude,$longitude)
	{
		// format this string with the appropriate latitude longitude
		$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true&key=AIzaSyBhJB9maJFpMdTZ_JXAbB7HBX4H8oDFURo";
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
	function getReverseGeoAdd($latitude,$longitude)
	{
		// format this string with the appropriate latitude longitude
		$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true&key=AIzaSyAC5XJHC0k1ALyl5Bnelv3Nvuxpzr9nLdc";
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
   $spam_filter='-finfo@salesmpower.acedns.in';
}

$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$result = mysqli_query($link,$sqlquery);
$countdatarefresh=mysqli_num_rows($result);
$body=file_get_contents('php://input');
if($body=='')
{
	$body=$_POST['xmldata'];
}
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = APICALLLOGURL."/operationdb-attendance-6.0.2.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);

//For insertion of fetched xml
if(strtoupper($nick_name)=='ABDOS' || strtoupper($nick_name)=='RUPA' || strtoupper($nick_name)=='DURO' || strtoupper($nick_name)=='STAR')
{
	$body_xml=str_replace("'",'"',$body);
	$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
						 xml='".$body."',
						insertdate=CURRENT_TIMESTAMP()";
	mysqli_query($link,$sqlinsert_xml_data);	
}
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><attendance><location><emp_code><![CDATA[E0004]]></emp_code><trans_id><![CDATA[AE000420170703101422]]></trans_id><latt><![CDATA[27.3862616]]></latt><longi><![CDATA[79.5920707]]></longi><date><![CDATA[2017-07-03 10:14:22]]></date></location><attendancedata><emp_code><![CDATA[E0004]]></emp_code><date><![CDATA[2017-07-03]]></date></attendancedata></attendance><attendance><location><emp_code><![CDATA[E0004]]></emp_code><trans_id><![CDATA[AE000420170704093027]]></trans_id><latt><![CDATA[22.7934475]]></latt><longi><![CDATA[88.2138393]]></longi><date><![CDATA[2017-07-04 09:30:27]]></date></location><attendancedata><emp_code><![CDATA[E0004]]></emp_code><date><![CDATA[2017-07-04]]></date></attendancedata></attendance></root>";*/
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><attendance><location><emp_code><![CDATA[E0555]]></emp_code><trans_id><![CDATA[AE055520240211155806]]></trans_id><latt><![CDATA[23.1211135]]></latt><longi><![CDATA[88.8598207]]></longi><date><![CDATA[2024-02-11 15:58:06]]></date><TA_DA_MODE><![CDATA[]]></TA_DA_MODE></location><attendancedata><emp_code><![CDATA[E0555]]></emp_code><date><![CDATA[2024-02-11]]></date></attendancedata></attendance></root>";*/

$attendance_emp_code = "*ROOT*ATTENDANCE*LOCATION*EMP_CODE";
$attendance_trans_id = "*ROOT*ATTENDANCE*LOCATION*TRANS_ID";
$attendance_latt = "*ROOT*ATTENDANCE*LOCATION*LATT";
$attendance_longi = "*ROOT*ATTENDANCE*LOCATION*LONGI";
$attendance_date = "*ROOT*ATTENDANCE*LOCATION*DATE";
$attendance_TA_DA_mode = "*ROOT*ATTENDANCE*LOCATION*TA_DA_MODE";

$attendancedata_emp_code = "*ROOT*ATTENDANCE*ATTENDANCEDATA*EMP_CODE";
$attendancedata_date = "*ROOT*ATTENDANCE*ATTENDANCEDATA*DATE";

$attendance_array = array();
$counter = 0;
class xml_attendance{
    var $emp_code, $trans_id,$latt,$longi,$attendance_date,$attendance_TA_DA_mode,$attendancedata_emp_code,$attendancedata_date;
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
    global $current_tag, $attendance_emp_code, $attendance_trans_id,$attendance_latt,$attendance_longi,$attendance_date,$attendance_TA_DA_mode,$attendancedata_emp_code,$attendancedata_date, $counter,$attendance_array;
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
			case $attendance_TA_DA_mode:
				$attendance_array[$counter]->attendance_TA_DA_mode = $data;
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
}
$xml_parser = xml_parser_create();
xml_set_element_handler($xml_parser, "startTag", "endTag");
xml_set_character_data_handler($xml_parser, "contents");
$data = $body;
//echo $data;exit();
if(!(xml_parse($xml_parser, $data, LIBXML_PARSEHUGE))){
    die("XML Error on line " . xml_get_current_line_number($xml_parser));
}
xml_parser_free($xml_parser);
//print_r($attendance_array);
//echo count($attendance_array);
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
		//echo "<pre>";
		//print_r($attendance_date);
		$attendance_TA_DA_mode=$attendance_array[$x]->attendance_TA_DA_mode;
		$attendance_emp_code=$attendance_array[$x]->attendancedata_emp_code;
		$attendancedata_date=$attendance_array[$x]->attendancedata_date;
		
		
		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if(DCR_map=='no' &&  strtoupper($nick_name)!='NIMBUS')
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
		
		$sqlchkattlocationempdate="SELECT * FROM location WHERE emp_code='".$emp_code."' AND SUBSTRING(date,1,10)='".substr($attendance_date,0,10)."' 
		AND (trans_id LIKE 'A%' OR trans_id LIKE 'WO%' OR trans_id LIKE 'LR%')";
		$reschkattlocationempdate = mysqli_query($link,$sqlchkattlocationempdate) or die(mysqli_error()." Error in check attendance location emp date: ".$sqlchkattlocationempdate); 
		$countchkattlocationempdate=mysqli_num_rows($reschkattlocationempdate);
		
		//For update the location table for existing trans id
		if($countchkattlocation>0 || $countchkattlocationempdate >0)
		{
			$sqlupdateattlocation="UPDATE location SET emp_code='".$emp_code."',
									latt='".$latt."',
									longi='".$longi."'
									WHERE trans_id='".$trans_id."'";
									
			$rsupdateattlocation=mysqli_query($link,$sqlupdateattlocation) or die(mysqli_error()." Error in update attendance location: ".$sqlupdateattlocation);
			
			$last_operation_datetime=$attendance_date;
			
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
			
			if(strtoupper($nick_name)=='ABDOS')
			{
				$TA_DA_condiion=" TA_DA_mode	='".$attendance_TA_DA_mode."',";
			}
			else
			{
				$TA_DA_condiion='';
			}

			//For Insert into the location table for new trans id			
			$sqlinsertattlocation="INSERT INTO location SET emp_code='".$emp_code."',
									trans_id='".$trans_id."',
									latt='".$latt."',
									longi='".$longi."',
									date='".$attendance_date."',".$TA_DA_condiion."
									updatetime='".$location_date."'";
			
			//For Insert into the attendance table for new trans id
			$sqlinsertattendance="INSERT INTO attendence SET emp_code='".$attendance_emp_code."', date='".$attendance_date."'";	
			
			//echo mysqli_query($link,$sqlinsertattlocation);
			$last_operation_datetime=$attendance_date;
			
			if(mysqli_query($link,$sqlinsertattlocation) && mysqli_query($link,$sqlinsertattendance))
				{
					$flag=5;
					if(retailer_app=='yes')
					{
						$sqlselcustomer="SELECT customer_code FROM customer_route_emp_relation WHERE emp_code='".$emp_code."'";
						$rsselcustomer=mysqli_query($link,$sqlselcustomer);
						$rowselcustomer=mysqli_fetch_assoc($rsselcustomer);
						$customer_code=$rowselcustomer['customer_code'];
						$sqlchklatlong="SELECT base_latt,base_longi FROM customer_master WHERE customer_code='".$customer_code."'";
						$rschklatlong=mysqli_query($link,$sqlchklatlong);
						$rowchklatlong=mysqli_fetch_assoc($rschklatlong);
						$base_latt=$rowchklatlong['base_latt'];
						$base_longi=$rowchklatlong['base_longi'];
						if($base_latt==0 && $base_longi==0)
						{
							$sqlupdatecustomer="UPDATE customer_master SET base_latt='".$latt."',base_longi='".$longi."',
									download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$customer_code."'";
							$rsupdatecustomer=mysqli_query($link,$sqlupdatecustomer);
						}
					}
					$sqlempname="SELECT emp_name,vertical_value,branch_code,dns_emp_code FROM employee_master WHERE emp_code='".$emp_code."'";
					$rsempname=mysqli_query($link,$sqlempname);
					$rowempname=mysqli_fetch_assoc($rsempname);
					//Start for STAR mis data details present
					if($nick_name=='STAR')
					{
						$qty='';
						$trans_type='A';
						$trans_sub_type='';
						update_transaction_STAR($emp_code,$trans_id,$qty,$trans_type,$trans_sub_type,$link);
						$sqlchkattinfo="SELECT Emp_Id FROM  t_att_checkout_info WHERE Emp_Id='".$rowempname['dns_emp_code']."' AND SUBSTRING(Entry_Date,1,10)='".substr($attendance_date,0,10)."'";
						
						//echo $sqlchkattinfo;
						
						$reschkattinfo = mysqli_query($link,$sqlchkattinfo); 
						$countchkattinfo=mysqli_num_rows($reschkattinfo);
						if($countchkattinfo ==0)
						{
							$sqlinsertattinfo="INSERT INTO t_att_checkout_info SET Emp_Id='".$rowempname['dns_emp_code']."',
								 				Emp_Name='".$rowempname['emp_name']."',Entry_Date='".substr($attendance_date,0,10)."',CheckIN='".substr($attendance_date,11,8)."'";
							$rsinsertattinfo=mysqli_query($link,$sqlinsertattinfo);					
						}
					}
					if(strtoupper($nick_name)=='RUPA'  || strtoupper($nick_name)=='DURO')
					{
						 $address=getReverseGeo($latt,$longi);
						 $sqlupdateloc="UPDATE location set address='".addslashes($address)."' WHERE trans_id='".$trans_id."'";
	 					 $rsupdateloc=mysqli_query($link,$sqlupdateloc);
					}
					if(strtoupper($nick_name)=='MAGIK')
					{
						 $address=getReverseGeoAdd($latt,$longi);
						 $sqlupdateloc="UPDATE location set address='".addslashes($address)."' WHERE trans_id='".$trans_id."'";
	 					 $rsupdateloc=mysqli_query($link,$sqlupdateloc);
					}
					if(strtoupper($nick_name)=='ABDOST')
					{
						//For Insert into the prev order counting table for att
						$sqlinsertprevorder="INSERT INTO prev_order_counting_master SET customer_code='".$emp_code."',
										order_no='".$trans_id."',
										visit_date='".$attendance_date."',
										download_time='".$location_date."'";
						 $rsinsertprevorder=mysqli_query($link,$sqlinsertprevorder);	 	
					}
					//echo $sqlinsertprevorder;exit();
					//End for STAR mis data details present
					$last_operation_datetime=$attendance_date;
					// For Sending email to recipents for attendance
				 	
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
	//echo $sqlupdatelastoperationtime;
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
mysqli_close($link);

?>