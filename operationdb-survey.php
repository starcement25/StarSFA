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

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><survey><location><emp_code><![CDATA[E0002]]></emp_code><trans_id><![CDATA[NOE000220150306180952]]></trans_id><latt><![CDATA[22.5641689]]></latt><longi><![CDATA[88.3569041]]></longi><date><![CDATA[2015-03-06 18:09:52]]></date></location><surveydata><survey_header><survey_no><![CDATA[NOE000220150306180952]]></survey_no><d_instruction><![CDATA[test]]></d_instruction><customer_code><![CDATA[C/0004586]]></customer_code></survey_header></surveydata></survey></root>";*/
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><survey><location><emp_code><![CDATA[E0002]]></emp_code><trans_id><![CDATA[NOE000220150306180952]]></trans_id><latt><![CDATA[22.5641689]]></latt><longi><![CDATA[88.3569041]]></longi><date><![CDATA[2015-03-06 18:09:52]]></date></location><surveydata><survey_header><survey_no><![CDATA[NOE000220150306180952]]></survey_no><d_instruction><![CDATA[test]]></d_instruction><customer_code><![CDATA[C/0004586]]></customer_code></survey_header></surveydata></survey></root>";*/
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><survey><location><emp_code><![CDATA[E0002]]></emp_code><trans_id><![CDATA[SUE000220150306180952]]></trans_id><latt><![CDATA[22.5641689]]></latt><longi><![CDATA[88.3569041]]></longi><date><![CDATA[2015-03-06 18:09:52]]></date></location><surveydata><survey_details><survey_id><![CDATA[SUE000220150306180952]]></survey_id><action_id><![CDATA[A56]]></action_id><value><![CDATA[Y]]></value><row_id><![CDATA[RA56]]></row_id></survey_details><survey_details><survey_id><![CDATA[SUE000220150306180952]]></survey_id><action_id><![CDATA[A54]]></action_id><value><![CDATA[N]]></value><row_id><![CDATA[RA54]]></row_id></survey_details></surveydata></survey></root>";*/

$attendance_emp_code = "*ROOT*ATTENDANCE*LOCATION*EMP_CODE";
$attendance_trans_id = "*ROOT*ATTENDANCE*LOCATION*TRANS_ID";
$attendance_latt = "*ROOT*ATTENDANCE*LOCATION*LATT";
$attendance_longi = "*ROOT*ATTENDANCE*LOCATION*LONGI";
$attendance_date = "*ROOT*ATTENDANCE*LOCATION*DATE";
$attendancedata_emp_code = "*ROOT*ATTENDANCE*ATTENDANCEDATA*EMP_CODE";
$attendancedata_date = "*ROOT*ATTENDANCE*ATTENDANCEDATA*DATE";

$survey_emp_code="*ROOT*SURVEY*LOCATION*EMP_CODE";
$survey_trans_id = "*ROOT*SURVEY*LOCATION*TRANS_ID";
$survey_latt = "*ROOT*SURVEY*LOCATION*LATT";
$survey_longi = "*ROOT*SURVEY*LOCATION*LONGI";
$survey_date="*ROOT*SURVEY*LOCATION*DATE";
$survey_id = "*ROOT*SURVEY*SURVEYDATA*SURVEY_DETAILS*SURVEY_ID";
$action_id = "*ROOT*SURVEY*SURVEYDATA*SURVEY_DETAILS*ACTION_ID";
$value = "*ROOT*SURVEY*SURVEYDATA*SURVEY_DETAILS*VALUE";
$row_id = "*ROOT*SURVEY*SURVEYDATA*SURVEY_DETAILS*ROW_ID";

$attendance_array = array();
$survey_array=array();
$survey_details_array=array();

$counter = 0;
$countersurvey=0;
$countersurveydetails=0;

class xml_attendance{
    var $emp_code, $trans_id,$latt,$longi,$attendance_date,$attendancedata_emp_code,$attendancedata_date;
}
class xml_survey{
	var $survey_emp_cod,$survey_trans_id,$survey_latt,$survey_longi,$survey_date;	
}
class xml_survey_details{
	var $survey_id,$action_id,$value,$row_id;	
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
    global $current_tag, $attendance_emp_code, $attendance_trans_id,$attendance_latt,$attendance_longi,$attendance_date,$attendancedata_emp_code,$attendancedata_date, $counter, $countersurvey,$countersurveydetails,$counterpayment,$counterpaymentdetails,$attendance_array,$survey_array,$survey_details_array,
	$survey_emp_code,$survey_trans_id,$survey_latt,$survey_longi,$survey_date,$survey_id,$action_id,$value,$row_id;
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
	if(substr($current_tag,0,12)=='*ROOT*SURVEY')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $survey_emp_code:
				$survey_array[$countersurvey] = new xml_survey();
				$survey_array[$countersurvey]->survey_emp_code = $data;
				break;
			case $survey_trans_id:
				$survey_array[$countersurvey]->survey_trans_id = $data;
				break;
			case $survey_latt:
				$survey_array[$countersurvey]->survey_latt = $data;
				break;
			case $survey_longi:
				$survey_array[$countersurvey]->survey_longi = $data;
				break;
			case $survey_date:
				$survey_array[$countersurvey]->survey_date = $data;
				$countersurvey++;
				break;
		}
	}
	if(substr($current_tag,0,38)=='*ROOT*SURVEY*SURVEYDATA*SURVEY_DETAILS')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $survey_id:
				$survey_details_array[$countersurveydetails] = new xml_survey_details();
				$survey_details_array[$countersurveydetails]->survey_id = $data;
				break;
			case $action_id:
				$survey_details_array[$countersurveydetails]->action_id = $data;
				break;
			case $value:
				$survey_details_array[$countersurveydetails]->value = $data;
				break;
			case $row_id:
				$survey_details_array[$countersurveydetails]->row_id = $data;
				$countersurveydetails++;
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
//print_r($survey_array);
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
/* --------------------START QUERY FOR SURVEY----------------------------------------------------------------------------------------------------------------*/
$survey_array_trans_id=array();
$surveytansid_array_mailbody=array();
$surveydate_array_mailbody=array();
$surveyemp_array_mailbody=array();
$surveyexcel_array_mailbody=array();
$surveyheader_array_mailbody=array();

//print_r($survey_array);
if(count($survey_array)>0)
{
	for($x=0;$x<count($survey_array);$x++){
		$survey_emp_code=$survey_array[$x]->survey_emp_code;
		$survey_trans_id=$survey_array[$x]->survey_trans_id;
		$survey_latt=$survey_array[$x]->survey_latt;
		$survey_longi=$survey_array[$x]->survey_longi;
		$survey_date=$survey_array[$x]->survey_date;
		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if($survey_latt>0 && $survey_longi>0)
		{
			$sqlupdatelatlongzero="UPDATE location SET latt='".$survey_latt."',longi='".$survey_longi."' WHERE 
									emp_code='".$survey_emp_code."' AND latt='0' AND longi='0'";
			$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
		}
		//For checking that trans id exist or not for survey
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$survey_trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check survey location: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for survey
		if($countchkorlocation>0)
		{
			//$survey_trans_id_chk=substr($survey_trans_id,1,19);
			if(!in_array($survey_trans_id,$survey_array_trans_id))
			{
				array_push($survey_array_trans_id,$survey_trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$survey_emp_code."',
									latt='".$survey_latt."',
									longi='".$survey_longi."'
									WHERE trans_id='".$survey_trans_id."'";
			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update survey location: ".$sqlupdateorlocation);
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
			$sqlempname="SELECT emp_name FROM employee_master WHERE emp_code='".$survey_emp_code."'";
			$rsempname=mysqli_query($link,$sqlempname);
			$rowempname=mysqli_fetch_assoc($rsempname);
			$emp_name=$rowempname['emp_name'];
			
			// create the data for location table date field , by checking the current date and time and the actual date and time of survey
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));

			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			//For Insert into the location table for new trans id regarding survey
			$sqlinsertsurveylocation="INSERT INTO location SET emp_code='".$survey_emp_code."',
									trans_id='".$survey_trans_id."',
									latt='".$survey_latt."',
									longi='".$survey_longi."',
									date='".$survey_date."',
									updatetime='".$location_date."'"; 
			if(mysqli_query($link,$sqlinsertsurveylocation))
			{
				$flag=5;
			}
			else
			{
				mysqli_query($link,"ROLLBACK");
				echo $flag=0;
				return;
			}	
			$addresssurvey=getReverseGeo($survey_latt,$survey_longi);	
			//For constructing the email body for SURVEY  if SURVEY has performed
			$surveyemailbody = "<html><head><title>Survey Details</title></head>
						<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
						.$emp_name. " On ".date('d-m-Y H:i:s',strtotime($survey_date))."</b><br /><br />Refference no: <b>".$survey_trans_id."</b><br />Address: <b>".$addresssurvey."</b><br /><br />";
						
			array_push($surveyemp_array_mailbody,$emp_name);
			array_push($surveydate_array_mailbody,$survey_date);
			array_push($surveytansid_array_mailbody,$survey_trans_id);
			array_push($surveyheader_array_mailbody,$surveyemailbody);
			${layout_name_array.$survey_trans_id}=array();
		 }//End of else
	}// End for loop
	//echo count($survey_details_array);
	if(count($survey_details_array)>0)
	{
		for($i=0;$i<count($survey_details_array);$i++){
			$survey_id=$survey_details_array[$i]->survey_id;
			$action_id=$survey_details_array[$i]->action_id;
			$value=$survey_details_array[$i]->value;
			$row_id=$survey_details_array[$i]->row_id;
			if(!in_array($survey_id,$survey_array_trans_id))
			{
				
			//For Insert into the Survey output table for new survey id
			$sqlinsertsurveyoutput="INSERT INTO survey_output SET survey_id='".$survey_id."',
								   action_id 	='".$action_id."',
								   value		='".addslashes(preg_replace('/[\r\n]+/', '',$value))."',
								   row_id		='".$row_id."'";   
			if(mysqli_query($link,$sqlinsertsurveyoutput))
			{
				$flag=5;
			}
			else
			{
				mysqli_query($link,"ROLLBACK");
				echo $flag=0;
				return;
			}
			$survey_input_details="SELECT display_name,layout_name FROM survey_input WHERE row_id='".$row_id."' AND action_id='".trim($action_id)."'";
			$rssurvey_input_details=mysqli_query($link,$survey_input_details);
			$rowsurvey_input_details=mysqli_fetch_assoc($rssurvey_input_details);
			$display_name=$rowsurvey_input_details['display_name'];
			$layout_name=$rowsurvey_input_details['layout_name'];
			if(!in_array($layout_name,${layout_name_array.$survey_id}))
			{
				if($i!=0)
				{
					${surveyexcelbody.$survey_id}.="\n";
				}
				${surveyexcelbody.$survey_id}.=$layout_name."\n";
				array_push(${layout_name_array.$survey_id},$layout_name);
			}
			${surveyexcelbody.$survey_id}.=$display_name."\t".preg_replace('/[\r\n]+/', '',$value)."\n";
			}
			
		}//End for loop
	}//End of if
	//End Insert into the Survey output table for new survey id
		for($countarr=0;$countarr<count($survey_array);$countarr++)
		{
			$survey_email=SURVEYEMAILRECIPENTS;
			$surveyemailsubj="Survey made by ".$surveyemp_array_mailbody[$countarr]." on ".date('d-m-Y H:i:s',strtotime($surveydate_array_mailbody[$countarr]));
			$surveyemailbody = $surveyheader_array_mailbody[$countarr]."Powered By aceDNS<br /></body></html>";
			
			$strSid = md5(uniqid(time()));
			$headers='';
			//$headers .= "Content-type: text/html; charset=UTF-8\n";
			$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
						"Reply-To:".FROMEMAIL." \r\n" .
						"Bcc: ".BCCEMAIL." \r\n" .
						'X-Mailer: PHP/' . phpversion();
			$headers .= "MIME-Version: 1.0\r\n";			
			$headers .= "Content-Type: multipart/mixed; boundary=\"".$strSid."\"\n\n";
			$headers .= "This is a multi-part message in MIME format.\n";
			$headers .= "--".$strSid."\n";
			$headers .= "Content-type: text/html; charset=UTF-8\n"; // or UTF-8 //
			$headers .= "Content-Transfer-Encoding: 7bit\n\n";
			$headers .= $surveyemailbody."\n\n";
			$strContent1 = base64_encode(${surveyexcelbody.$surveytansid_array_mailbody[$countarr]});
			$headers .= "--".$strSid."\n";
			$headers .= "Content-Type: application/octet-stream; name=\"survey_$surveytansid_array_mailbody[$countarr].xls\"\n";
			$headers .= "Content-Transfer-Encoding: base64\n";
			$headers .= "Content-Disposition: attachment; filename=\"survey_$surveytansid_array_mailbody[$countarr].xls\"\n\n";
			$headers .= $strContent1."\n\n";			

			if(mail($survey_email, $surveyemailsubj, $surveyemailbody, $headers,$spam_filter))
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
 /* -------------------------------------END QUERY FOR SURVEY---------------------------------------------------------------------------------------------------------------*/
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
	$url = "http://www.acedns.in/acednsproduct/operationdb-survey.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&last_git_master_update_time=$last_git_master_update_time&last_loyalty_purchase_update_time=$last_loyalty_purchase_update_time";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/operationdb-survey.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&last_git_master_update_time=$last_git_master_update_time&last_loyalty_purchase_update_time=$last_loyalty_purchase_update_time"."\r\n";
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
