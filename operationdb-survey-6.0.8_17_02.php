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

$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
					 xml='".$body_xml."',
					insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);	

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><survey><location><emp_code><![CDATA[E0002]]></emp_code><trans_id><![CDATA[NOE000220150306180952]]></trans_id><latt><![CDATA[22.5641689]]></latt><longi><![CDATA[88.3569041]]></longi><date><![CDATA[2015-03-06 18:09:52]]></date></location><surveydata><survey_header><survey_no><![CDATA[NOE000220150306180952]]></survey_no><d_instruction><![CDATA[test]]></d_instruction><customer_code><![CDATA[C/0004586]]></customer_code></survey_header></surveydata></survey></root>";*/
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><survey><location><emp_code><![CDATA[E0002]]></emp_code><trans_id><![CDATA[NOE000220150306180952]]></trans_id><latt><![CDATA[22.5641689]]></latt><longi><![CDATA[88.3569041]]></longi><date><![CDATA[2015-03-06 18:09:52]]></date></location><surveydata><survey_header><survey_no><![CDATA[NOE000220150306180952]]></survey_no><d_instruction><![CDATA[test]]></d_instruction><customer_code><![CDATA[C/0004586]]></customer_code></survey_header></surveydata></survey></root>";*/
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><survey><location><emp_code><![CDATA[E0047]]></emp_code><trans_id><![CDATA[SUE004720190507112011]]></trans_id><latt><![CDATA[22.5643619]]></latt><longi><![CDATA[88.356868]]></longi><date><![CDATA[2019-05-07 11:20:11]]></date></location><surveydata><survey_header><survey_type><![CDATA[Customer Add]]></survey_type><menu_name><![CDATA[RA060]]></menu_name><mall_id><![CDATA[]]></mall_id><mall_name><![CDATA[]]></mall_name><business_name><![CDATA[]]></business_name><contact_name><![CDATA[]]></contact_name><phone_no><![CDATA[]]></phone_no><questions_answered><![CDATA[]]></questions_answered><route_code><![CDATA[]]></route_code><survey_id><![CDATA[SUE004720190507112011]]></survey_id></survey_header><survey_details><survey_id><![CDATA[SUE004720190507112011]]></survey_id><action_id><![CDATA[ ]]></action_id><value><![CDATA[TEST F;CE004720190507112011;E0047]]></value><type><![CDATA[Customer Add]]></type><row_id><![CDATA[RA062]]></row_id></survey_details><survey_details><survey_id><![CDATA[SUE004720190507112011]]></survey_id><action_id><![CDATA[A064]]></action_id><value><![CDATA[SUB DEALER:C/0000144]]></value><type><![CDATA[Customer Add]]></type><row_id><![CDATA[RA064]]></row_id></survey_details><survey_details><survey_id><![CDATA[SUE004720190507112011]]></survey_id><action_id><![CDATA[ ]]></action_id><value><![CDATA[DIPU SOM]]></value><type><![CDATA[Customer Add]]></type><row_id><![CDATA[RA065]]></row_id></survey_details><survey_details><survey_id><![CDATA[SUE004720190507112011]]></survey_id><action_id><![CDATA[ ]]></action_id><value><![CDATA[9839225544]]></value><type><![CDATA[Customer Add]]></type><row_id><![CDATA[RA066]]></row_id></survey_details><survey_details><survey_id><![CDATA[SUE004720190507112011]]></survey_id><action_id><![CDATA[A068]]></action_id><value><![CDATA[08/05/2019]]></value><type><![CDATA[Customer Add]]></type><row_id><![CDATA[RA068]]></row_id></survey_details><survey_details><survey_id><![CDATA[SUE004720190507112011]]></survey_id><action_id><![CDATA[ ]]></action_id><value><![CDATA[TEST]]></value><type><![CDATA[Customer Add]]></type><row_id><![CDATA[RA069]]></row_id></survey_details><survey_details><survey_id><![CDATA[SUE004720190507112011]]></survey_id><action_id><![CDATA[ ]]></action_id><value><![CDATA[MARKETING#TAKE PHOTO]]></value><type><![CDATA[Customer Add]]></type><row_id><![CDATA[RA070]]></row_id></survey_details></surveydata></survey></root>";*/
$body="<?xml version='1.0' encoding='UTF-8'?><root><survey><location><emp_code><![CDATA[E0555]]></emp_code><trans_id><![CDATA[SUE055520240216134420]]></trans_id><latt><![CDATA[22.52149]]></latt><longi><![CDATA[88.3485066]]></longi><date><![CDATA[2024-02-16 13:44:20]]></date></location><surveydata><survey_header><survey_type><![CDATA[Technical Meets]]></survey_type><menu_name><![CDATA[RA062]]></menu_name><mall_id><![CDATA[]]></mall_id><mall_name><![CDATA[]]></mall_name><business_name><![CDATA[]]></business_name><contact_name><![CDATA[]]></contact_name><phone_no><![CDATA[]]></phone_no><questions_answered><![CDATA[]]></questions_answered><route_code><![CDATA[]]></route_code><check_in_time><![CDATA[]]></check_in_time><survey_id><![CDATA[SUE055520240216134420]]></survey_id></survey_header><survey_details><survey_id><![CDATA[SUE055520240216134420]]></survey_id><action_id><![CDATA[ ]]></action_id><value><![CDATA[C/0002403]]></value><type><![CDATA[Technical Meets]]></type><row_id><![CDATA[RA063]]></row_id></survey_details><survey_details><survey_id><![CDATA[SUE055520240216134420]]></survey_id><action_id><![CDATA[ ]]></action_id><value><![CDATA[11]]></value><type><![CDATA[Technical Meets]]></type><row_id><![CDATA[RA064]]></row_id></survey_details><survey_details><survey_id><![CDATA[SUE055520240216134420]]></survey_id><action_id><![CDATA[ ]]></action_id><value><![CDATA[EE]]></value><type><![CDATA[Technical Meets]]></type><row_id><![CDATA[RA065]]></row_id></survey_details><survey_details><survey_id><![CDATA[SUE055520240216134420]]></survey_id><action_id><![CDATA[ ]]></action_id><value><![CDATA[85]]></value><type><![CDATA[Technical Meets]]></type><row_id><![CDATA[RA066]]></row_id></survey_details><survey_details><survey_id><![CDATA[SUE055520240216134420]]></survey_id><action_id><![CDATA[A067]]></action_id><value><![CDATA[E055520240216134406.JPEG; ;]]></value><type><![CDATA[Technical Meets]]></type><row_id><![CDATA[RA067]]></row_id></survey_details><survey_details><survey_id><![CDATA[SUE055520240216134420]]></survey_id><action_id><![CDATA[ ]]></action_id><value><![CDATA[NO]]></value><type><![CDATA[Technical Meets]]></type><row_id><![CDATA[RA068]]></row_id></survey_details><survey_details><survey_id><![CDATA[SUE055520240216134420]]></survey_id><action_id><![CDATA[ ]]></action_id><value><![CDATA[B0001]]></value><type><![CDATA[Technical Meets]]></type><row_id><![CDATA[RA140]]></row_id></survey_details></surveydata></survey><survey><location><emp_code><![CDATA[E0555]]></emp_code><trans_id><![CDATA[SUE055520240216134930]]></trans_id><latt><![CDATA[22.5214898]]></latt><longi><![CDATA[88.3485096]]></longi><date><![CDATA[2024-02-16 13:49:30]]></date></location><surveydata><survey_header><survey_type><![CDATA[Technical Meets]]></survey_type><menu_name><![CDATA[RA062]]></menu_name><mall_id><![CDATA[]]></mall_id><mall_name><![CDATA[]]></mall_name><business_name><![CDATA[]]></business_name><contact_name><![CDATA[]]></contact_name><phone_no><![CDATA[]]></phone_no><questions_answered><![CDATA[]]></questions_answered><route_code><![CDATA[]]></route_code><check_in_time><![CDATA[]]></check_in_time><survey_id><![CDATA[SUE055520240216134930]]></survey_id></survey_header><survey_details><survey_id><![CDATA[SUE055520240216134930]]></survey_id><action_id><![CDATA[ ]]></action_id><value><![CDATA[C/0002403]]></value><type><![CDATA[Technical Meets]]></type><row_id><![CDATA[RA063]]></row_id></survey_details><survey_details><survey_id><![CDATA[SUE055520240216134930]]></survey_id><action_id><![CDATA[ ]]></action_id><value><![CDATA[1]]></value><type><![CDATA[Technical Meets]]></type><row_id><![CDATA[RA064]]></row_id></survey_details><survey_details><survey_id><![CDATA[SUE055520240216134930]]></survey_id><action_id><![CDATA[ ]]></action_id><value><![CDATA[RT]]></value><type><![CDATA[Technical Meets]]></type><row_id><![CDATA[RA065]]></row_id></survey_details><survey_details><survey_id><![CDATA[SUE055520240216134930]]></survey_id><action_id><![CDATA[ ]]></action_id><value><![CDATA[22]]></value><type><![CDATA[Technical Meets]]></type><row_id><![CDATA[RA066]]></row_id></survey_details><survey_details><survey_id><![CDATA[SUE055520240216134930]]></survey_id><action_id><![CDATA[A067]]></action_id><value><![CDATA[E055520240216134904.JPEG; ;]]></value><type><![CDATA[Technical Meets]]></type><row_id><![CDATA[RA067]]></row_id></survey_details><survey_details><survey_id><![CDATA[SUE055520240216134930]]></survey_id><action_id><![CDATA[ ]]></action_id><value><![CDATA[NO]]></value><type><![CDATA[Technical Meets]]></type><row_id><![CDATA[RA068]]></row_id></survey_details><survey_details><survey_id><![CDATA[SUE055520240216134930]]></survey_id><action_id><![CDATA[ ]]></action_id><value><![CDATA[B0001]]></value><type><![CDATA[Technical Meets]]></type><row_id><![CDATA[RA140]]></row_id></survey_details></surveydata></survey></root>";


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

$surveyheader_survey_id = "*ROOT*SURVEY*SURVEYDATA*SURVEY_HEADER*SURVEY_ID";
$surveyheader_survey_type = "*ROOT*SURVEY*SURVEYDATA*SURVEY_HEADER*SURVEY_TYPE";
$surveyheader_menu_name = "*ROOT*SURVEY*SURVEYDATA*SURVEY_HEADER*MENU_NAME";
$surveyheader_mall_id = "*ROOT*SURVEY*SURVEYDATA*SURVEY_HEADER*MALL_ID";
$surveyheader_mall_name = "*ROOT*SURVEY*SURVEYDATA*SURVEY_HEADER*MALL_NAME";
$surveyheader_business_name = "*ROOT*SURVEY*SURVEYDATA*SURVEY_HEADER*BUSINESS_NAME";
$surveyheader_contact_name = "*ROOT*SURVEY*SURVEYDATA*SURVEY_HEADER*CONTACT_NAME";
$surveyheader_phone_no = "*ROOT*SURVEY*SURVEYDATA*SURVEY_HEADER*PHONE_NO";
$surveyheader_questions_answered = "*ROOT*SURVEY*SURVEYDATA*SURVEY_HEADER*QUESTIONS_ANSWERED";
$surveyheader_route_code = "*ROOT*SURVEY*SURVEYDATA*SURVEY_HEADER*ROUTE_CODE";
$surveyheader_check_in_time = "*ROOT*SURVEY*SURVEYDATA*SURVEY_HEADER*CHECK_IN_TIME";


$survey_id = "*ROOT*SURVEY*SURVEYDATA*SURVEY_DETAILS*SURVEY_ID";
$action_id = "*ROOT*SURVEY*SURVEYDATA*SURVEY_DETAILS*ACTION_ID";
$value = "*ROOT*SURVEY*SURVEYDATA*SURVEY_DETAILS*VALUE";
$type = "*ROOT*SURVEY*SURVEYDATA*SURVEY_DETAILS*TYPE";
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
	var $survey_emp_cod,$survey_trans_id,$survey_latt,$survey_longi,$survey_date,$surveyheader_survey_id,$surveyheader_survey_type,$surveyheader_menu_name,$surveyheader_mall_id,$surveyheader_mall_name,$surveyheader_business_name,$surveyheader_contact_name,$surveyheader_phone_no,$surveyheader_questions_answered,$surveyheader_route_code,$surveyheader_check_in_time;	
}
class xml_survey_details{
	var $survey_id,$action_id,$value,$type,$row_id;	
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
    global $current_tag, $attendance_emp_code, $attendance_trans_id,$attendance_latt,$attendance_longi,$attendance_date,$attendancedata_emp_code,$attendancedata_date, $counter, $countersurvey,$countersurveydetails,$attendance_array,$survey_array,$survey_details_array,
	$survey_emp_code,$survey_trans_id,$survey_latt,$survey_longi,$survey_date,$surveyheader_survey_id,$surveyheader_survey_type,$surveyheader_menu_name,$surveyheader_mall_id,$surveyheader_mall_name,$surveyheader_business_name,$surveyheader_contact_name,$surveyheader_phone_no,$surveyheader_questions_answered,$surveyheader_route_code,$surveyheader_check_in_time,$survey_id,$action_id,$value,$type,$row_id;
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
				break;
			case $surveyheader_survey_type:
				$survey_array[$countersurvey]->surveyheader_survey_type = $data;
				break;
			case $surveyheader_menu_name:
				$survey_array[$countersurvey]->surveyheader_menu_name = $data;
				break;
		 	case $surveyheader_mall_id:
				$survey_array[$countersurvey]->surveyheader_mall_id = $data;
				break;
			case $surveyheader_mall_name:
				$survey_array[$countersurvey]->surveyheader_mall_name = $data;
				break;
			case $surveyheader_business_name:
				$survey_array[$countersurvey]->surveyheader_business_name = $data;
				break;
			case $surveyheader_contact_name:
				$survey_array[$countersurvey]->surveyheader_contact_name = $data;
				break;
			case $surveyheader_phone_no:
				$survey_array[$countersurvey]->surveyheader_phone_no = $data;
				break;
			case $surveyheader_questions_answered:
				$survey_array[$countersurvey]->surveyheader_questions_answered = $data;
				break;
			case $surveyheader_route_code:
				$survey_array[$countersurvey]->surveyheader_route_code = $data;
				break;
			case $surveyheader_check_in_time:
				$survey_array[$countersurvey]->surveyheader_check_in_time = $data;
				break;	
			case $surveyheader_survey_id:
				$survey_array[$countersurvey]->surveyheader_survey_id = $data;
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
			case $type:
				$survey_details_array[$countersurveydetails]->type = $data;
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
					//Start for STAR mis data details present
					if($nick_name=='STAR')
					{
						$sqlupdatemisdatapresent="UPDATE mis_data_details SET present_tdy=(present_tdy+1),
													present_mtd=(present_mtd+1),
													present_ytd=(present_ytd+1) WHERE emp_code='".$emp_code."'";
						if(mysqli_query($link,$sqlupdatemisdatapresent))
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
					//End for STAR mis data details present
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
					//$address=getReverseGeo($latt,$longi);
					$qddress='';
					/*if($attendance_email!='')
					{
						$attendanceemailsubj="$nick_name - Attendance - ".$emp_name." on ".date('d-m-Y',strtotime($attendance_date))." @".date('H:i:s',strtotime($attendance_date)).' hrs.';
						$attendancemailbody = "<html><head><title>Attendance</title></head>
											<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
											.$emp_name. "</b><br><br>".$emp_name." marked as present on <b>".date('d-m-Y H:i:s',strtotime($attendance_date))."</b> 
											at <b>".$address."</b></table><br><br>Powered By aceDNS</body></html>";
						$headers  = "MIME-Version: 1.0\r\n";
						$headers .= "Content-type: text/html; charset=UTF-8\n";
						$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
									"Reply-To:".FROMEMAIL." \r\n" .
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
					}*/
				}
				/*else
				{
					mysqli_query($link,"ROLLBACK");
					echo $flag=0;
					return;
				}*/
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
$surveyroute_array_mailbody=array();
$surveytype_array_mailbody=array();
$update_column_array=array();

//print_r($survey_array);

if(count($survey_array)>0)
{
	for($x=0;$x<count($survey_array);$x++){
		$survey_emp_code=$survey_array[$x]->survey_emp_code;
		$survey_trans_id=$survey_array[$x]->survey_trans_id;
		$survey_latt=$survey_array[$x]->survey_latt;
		$survey_longi=$survey_array[$x]->survey_longi;
		$survey_date=$survey_array[$x]->survey_date;
		$surveyheader_survey_id=$survey_array[$x]->surveyheader_survey_id;
		$surveyheader_survey_type=$survey_array[$x]->surveyheader_survey_type;
		$surveyheader_menu_name=$survey_array[$x]->surveyheader_menu_name;
		$surveyheader_mall_id=$survey_array[$x]->surveyheader_mall_id;
		$surveyheader_mall_name=$survey_array[$x]->surveyheader_mall_name;
		$surveyheader_business_name=$survey_array[$x]->surveyheader_business_name;
		$surveyheader_contact_name=$survey_array[$x]->surveyheader_contact_name;
		$surveyheader_phone_no=$survey_array[$x]->surveyheader_phone_no;
		$surveyheader_questions_answered=$survey_array[$x]->surveyheader_questions_answered;
		$surveyheader_route_code=$survey_array[$x]->surveyheader_route_code;
		$surveyheader_check_in_time=$survey_array[$x]->surveyheader_check_in_time;				

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
			
			$sqlmenuname="SELECT display_name FROM survey_input WHERE row_id='".$surveyheader_menu_name."'";
			$rsmenuname=mysqli_query($link,$sqlmenuname);
			$rowmenuname=mysqli_fetch_assoc($rsmenuname);
			$menuname=$rowmenuname['display_name'];

			// create the data for location table date field , by checking the current date and time and the actual date and time of survey
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));

			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			//For Insert into the location table for new trans id regarding survey
			$survey_time=substr($survey_trans_id,-6,2);
				if($survey_time >='06' && $survey_time <'12'){
						$timeflag = 1;
				}
				else if($survey_time >='12' && $survey_time <'15'){
						$timeflag = 2;
				}
				else if($survey_time >='15' && $survey_time <'18'){
						$timeflag = 3;
				}
				else if($survey_time >='18' && $survey_time <='23'){
						$timeflag = 4;
				}
				else if($survey_time >='00' && $survey_time <'06'){
						$timeflag = 5;
				}
			  if($timeflag == 1)
				$column_name = "time_9_to_12";
			  if($timeflag == 2)
				$column_name = "time_12_to_3";
			  if($timeflag == 3)
				$column_name = "time_3_to_6";
			  if($timeflag == 4 || $timeflag == 5)
				$column_name = "time_6_to_9";
				$slot_count = 1;		
			$sqlinsertsurveylocation="INSERT INTO location SET emp_code='".$survey_emp_code."',
									trans_id='".$survey_trans_id."',
									latt='".$survey_latt."',
									longi='".$survey_longi."',
									date='".$survey_date."',
									updatetime='".$location_date."'";
			if($surveyheader_survey_id!='')
			{						
			$sql_insert_survey_header = "INSERT INTO survey_header SET 
											 survey_id = '".$surveyheader_survey_id."', 
											 survey_type = '".$surveyheader_survey_type."',
												 menu_name = '".addslashes($menuname)."', 
												 mall_id   = '".$surveyheader_mall_id."',
											  mall_hs_name = '".addslashes($surveyheader_mall_name)."',
											 business_name = '".addslashes($surveyheader_business_name)."',
											  contact_name = '".addslashes($surveyheader_contact_name)."',
												  phone_no = '".$surveyheader_phone_no."', 
										questions_answered = '".$surveyheader_questions_answered."',
										route_code         = '".$surveyheader_route_code."',
										check_in_time      = '".$surveyheader_check_in_time."',
										$column_name       = '".$slot_count."', 
												status     = 'pending',
											download_time  =CURRENT_TIMESTAMP()";
				if(mysqli_query($link,$sqlinsertsurveylocation) && mysqli_query($link,$sql_insert_survey_header))
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
			$addresssurvey=getReverseGeo($survey_latt,$survey_longi);	
			//For constructing the email body for SURVEY  if SURVEY has performed
			$surveyemailbody = "<html><head><title>Survey Details</title></head>
						<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
						.$emp_name. " On ".date('d-m-Y H:i:s',strtotime($survey_date))."</b><br /><br />Refference no: <b>".$survey_trans_id."</b><br />Address: <b>".$addresssurvey."</b><br /><br />";
						
			array_push($surveyemp_array_mailbody,$emp_name);
			array_push($surveydate_array_mailbody,$survey_date);
			array_push($surveytansid_array_mailbody,$survey_trans_id);
			array_push($surveyheader_array_mailbody,$surveyemailbody);
			array_push($surveyroute_array_mailbody,$surveyheader_route_code);
			array_push($surveytype_array_mailbody,$surveyheader_survey_type);
			${$layout_name_array.$survey_trans_id}=array();
		 }//End of else
	}// End for loop
	//echo count($survey_details_array);
	if(count($survey_details_array)>0)
	{
		for($i=0;$i<count($survey_details_array);$i++){
			$survey_id=$survey_details_array[$i]->survey_id;
			$action_id=$survey_details_array[$i]->action_id;
			$value=$survey_details_array[$i]->value;
			$type=$survey_details_array[$i]->type;
			$row_id=$survey_details_array[$i]->row_id;
			if(!in_array($survey_id,$survey_array_trans_id))
			{
				//exit();
				if($nick_name=='LIPL')
				{
					if($row_id=='RA037' || $row_id=='RA168' )
					{
						$value_replaced=str_replace('; ',';',$value);
						$value_explode=explode(';',$value_replaced);
						foreach($value_explode as $cat_val)
						{
							if($cat_val!='')
							{
								$sqlselcatid="SELECT cat_id FROM survey_category_master WHERE cat_name='".addslashes($cat_val)."'";
								$rsselcatid=mysqli_query($link,$sqlselcatid);
								$cntselcatid=mysqli_num_rows($rsselcatid);
								if($cntselcatid >0)
								{
									$rowselcatid=mysqli_fetch_assoc($rsselcatid);
									$cat_id=$rowselcatid['cat_id'];
									${$catstring.$survey_id}=${$catstring.$survey_id}.$cat_id.';';
								}
								else
								{
									${$catstring.$survey_id}=${$catstring.$survey_id}.$cat_val.';';
								}
						  }
						}
						${$catstring.$survey_id}=substr(${$catstring.$survey_id},0,-1);
						$value=${$catstring.$survey_id};
					}
					if($row_id=='RA038' || $row_id=='RA169' )
					{
						$value_replaced=str_replace('; ',';',$value);
						$value_explode=explode(';',$value_replaced);
						foreach($value_explode as $sub_cat_val)
						{
							if($sub_cat_val!='')
							{
								$sqlselsubcatid="SELECT sub_cat_id FROM survey_category_master WHERE sub_cat_name='".addslashes($sub_cat_val)."'";
								$rsselsubcatid=mysqli_query($link,$sqlselsubcatid);
								$cntselsubcatid=mysqli_num_rows($rsselsubcatid);
								if($cntselsubcatid >0)
								{
									$rowselsubcatid=mysqli_fetch_assoc($rsselsubcatid);
									$sub_cat_id=$rowselsubcatid['sub_cat_id'];
									${subcatstring.$survey_id}=${subcatstring.$survey_id}.$sub_cat_id.';';
								}
								else
								{
									${$subcatstring.$survey_id}=${$subcatstring.$survey_id}.$sub_cat_val.';';
								}
							}
						}
						${$subcatstring.$survey_id}=substr(${$subcatstring.$survey_id},0,-1);
						$value=${$subcatstring.$survey_id};
					}
				}
				//For Insert into the Survey output table for new survey id
			    $sqlinsertsurveyoutput="INSERT INTO survey_output SET survey_id='".$survey_id."',
									   action_id 	='".$action_id."',
									   value		='".addslashes(preg_replace('/[\r\n]+/', '',$value))."',
									   type			='".$type."',
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
				if($row_id=='RA002' || $row_id=='RA136')
				{
					${$business_name.$survey_id}=addslashes(preg_replace('/[\r\n]+/', '',$value));
				}
				if($row_id=='RA005' || $row_id=='RA143')
				{
					${$mall_name.$survey_id}=addslashes(preg_replace('/[\r\n]+/', '',$value));
				}
				${$type.$survey_id}=$type;
			
			if($nick_name=='MAITHAN'){
				if($row_id=='RA006' || $row_id=='RA010')
				{
					${$ihb_name.$survey_id}=addslashes(preg_replace('/[\r\n]+/', '',$value));
				}
				if($row_id=='RA007' || $row_id=='RA011')
				{
					${$ihb_no.$survey_id}=addslashes(preg_replace('/[\r\n]+/', '',$value));
				}
				if($row_id=='RA008' || $row_id=='RA012')
				{
					${$ihb_address.$survey_id}=addslashes(preg_replace('/[\r\n]+/', '',$value));
				}
				if($row_id=='RA014' || $row_id=='RA028')
				{
					$rds_tag_array=explode(':',$value);
					if($rds_tag_array[0]=='YES' || $rds_tag_array[0]=='Dealer/SubDealer'){
						if($rds_tag_array[0]=='Dealer/SubDealer')
						{
							${$ihb_rds_tag.$survey_id}=$rds_tag_array[1];
						}
						else
						{
							$rds_tag_array_part=explode('#',$rds_tag_array[1]);
							${$ihb_rds_tag.$survey_id}=$rds_tag_array_part[1];
						}
					}
				}
			}
			if($nick_name=='STAR' || $nick_name=='START'){
				if($row_id=='RA006')
				{
					${$KYC_mobile.$survey_id}=$value;
				}
			}
			$survey_input_details="SELECT display_name,layout_name,insert_table_detail FROM survey_input WHERE row_id='".$row_id."' AND action_id='".trim($action_id)."'";
			$rssurvey_input_details=mysqli_query($link,$survey_input_details);
			$rowsurvey_input_details=mysqli_fetch_assoc($rssurvey_input_details);
			$display_name=$rowsurvey_input_details['display_name'];
			$layout_name=$rowsurvey_input_details['layout_name'];
			$insert_table_detail=$rowsurvey_input_details['insert_table_detail'];
			if($insert_table_detail!=''){
			$insert_table_detail_array=explode('#',$insert_table_detail);
			if(strtolower($insert_table_detail_array[0])=='insert'){
				$insert_table_name=$insert_table_detail_array[1];
				$insert_column_name=$insert_table_detail_array[2];
				$insert_column_name_array=explode(';',$insert_column_name);
				$insert_column_val_array=explode(';',$value);
				$insert_table_name_array=explode("&",$insert_table_name);
				foreach ($insert_table_name_array as $insert_table_name_val)
				{
					$sqlinsertparts='';
					$sqlinsert="INSERT INTO ".$insert_table_name_val." SET ";
					for($k=0;$k<count($insert_column_name_array);$k++)
					{
						$sqlinsertparts.=" $insert_column_name_array[$k]='".$insert_column_val_array[$k]."',";
					}
					if($insert_table_name_val=='facilitator_master')
					{ 
						$sqlbranch="SELECT branch_code FROM employee_master WHERE emp_code='".$emp_code."'";
						$rsbranch=mysqli_query($link,$sqlbranch);
						$rowbranch=mysqli_fetch_assoc($rsbranch);
						$branch_code=$rowbranch['branch_code'];
						$sqlinsertparts.=" branch_code='".$branch_code."',";
					}
					$sqlinsertfinal=$sqlinsert.substr($sqlinsertparts,0,-1);
					//echo $sqlinsert;
					mysqli_query($link,$sqlinsertfinal);
				}
				/*$sqlinsert="INSERT INTO ".$insert_table_name." SET ";
				
				for($k=0;$k<count($insert_column_name_array);$k++)
				{
					$sqlinsertparts.=" $insert_column_name_array[$k]='".$insert_column_val_array[$k]."',";
				}
				$sqlinsertfinal=$sqlinsert.substr($sqlinsertparts,0,-1);
				//echo $sqlinsert;
				mysqli_query($link,$sqlinsertfinal);*/
				${$updatecolumnindexname.$survey_id}=$insert_column_name_array[1];
				${$updatecolumnindexval.$survey_id}=$insert_column_val_array[1];
			 }
			 if(strtolower($insert_table_detail_array[0])=='update'){
				$update_table_name=$insert_table_detail_array[1];
				$update_column_name=$insert_table_detail_array[2];
				if(in_array($update_column_name,$update_column_array))
				{
					$update_column_name_cond=$update_column_name."=CONCAT('".$value."',$update_column_name)";
				}
				else $update_column_name_cond=$update_column_name."='".$value."'";
				$sqlupdate="UPDATE ".$update_table_name." SET ".$update_column_name_cond." WHERE ${$updatecolumnindexname.$survey_id}='".${$updatecolumnindexval.$survey_id}."'";
				mysqli_query($link,$sqlupdate);
				array_push($update_column_array,$update_column_name);
			 }
			 if(strtolower($insert_table_detail_array[0])=='independent'){
				 ${$independent_id_val.$survey_id}=$value;
				 if(strpos(${$independent_id_val.$survey_id},':')!=false){
					 $independent_parts_val=explode(":",${$independent_id_val.$survey_id});
					 ${$independent_id_val.$survey_id}=$independent_parts_val[1];
				 }
				 if(strpos(${$independent_id_val.$survey_id},';')!=false){
					 $independent_parts_val=explode(";",${$independent_id_val.$survey_id});
					 ${$independent_id_val.$survey_id}=$independent_parts_val[1];
				 }
			 }
			 else if(strtolower($insert_table_detail_array[0])=='masterviewedit'){
				$update_table_name=$insert_table_detail_array[1];
				$update_column_name=$insert_table_detail_array[2];
				if(in_array($update_column_name,$update_column_array))
				{
					$update_column_name_cond=$update_column_name."=CONCAT('".$value."',$update_column_name)";
				}
				else $update_column_name_cond=$update_column_name."='".$value."'";
				$update_through_column=explode(";",$insert_table_detail_array[3]);
				${$update_through_name.$survey_id}=$update_through_column[0];
				$sqlupdate="UPDATE ".$update_table_name." SET ".$update_column_name_cond." WHERE ${update_through_name.$survey_id}='".${$independent_id_val.$survey_id}."'";
				mysqli_query($link,$sqlupdate);
				array_push($update_column_array,$update_column_name);
			 }
			}
			if(!in_array($layout_name,${$layout_name_array.$survey_id}))
			{
				if($i!=0)
				{
					${$surveyexcelbody.$survey_id}.="\n";
				}
				${$surveyexcelbody.$survey_id}.=$layout_name."\n";
				array_push(${$layout_name_array.$survey_id},$layout_name);
			}
			${$surveyexcelbody.$survey_id}.=$display_name."\t".preg_replace('/[\r\n]+/', '',$value)."\n";
			}
			if(strpos($value, '#OTP;')!=false) {
				$valuearray=explode('#OTP;',$value);
				$sql_insert_OTP="INSERT INTO OTP_details SET mobile_no='".$valuearray[0]."',
								   OTP 	='".$valuearray[1]."'";
			    mysqli_query($link,$sql_insert_OTP);				   
			}
		}//End for loop
	}//End of if
	//End Insert into the Survey output table for new survey id
		for($countarr=0;$countarr<count($survey_array);$countarr++)
		{
			$sqlupdatefootsoldier="UPDATE foot_soldier SET DCE_status='DONE' WHERE business_name=
			'".addslashes(${$business_name.$surveytansid_array_mailbody[$countarr]})."' AND mall_name='".addslashes(${$mall_name.$surveytansid_array_mailbody[$countarr]})."'";
			
			//echo $sqlupdatefootsoldier;
			$rsupdatefootsoldier=mysqli_query($link,$sqlupdatefootsoldier);
			//Start for STAR mis data details survey count updation
				if($nick_name=='STAR')
				{
				   if(!in_array($surveytansid_array_mailbody[$countarr],$survey_array_trans_id))
					{
						$qty='';
						$trans_type='SU';
						$trans_sub_type=${$type.$surveytansid_array_mailbody[$countarr]};
						if($trans_sub_type=='KYC') $qty=${$KYC_mobile.$surveytansid_array_mailbody[$countarr]};
						update_transaction_STAR(substr($surveytansid_array_mailbody[$countarr],2,5),$surveytansid_array_mailbody[$countarr],$qty,$trans_type,$trans_sub_type);
					}
				}
			//End for STAR mis data details survey count updation
			//Start for MAITHAN non tade customer data updation
			if($nick_name=='MAITHAN' && $surveytype_array_mailbody[$countarr]=='New IHB'){
				$sqlnontardecustomer="SELECT customer_code,phone_no FROM non_trade_customer_master WHERE 
									customer_name='".addslashes(${$ihb_name.$surveytansid_array_mailbody[$countarr]})."' 
									AND phone_no='".${$ihb_no.$surveytansid_array_mailbody[$countarr]}."'";
				$rsnontardecustomer=mysqli_query($link,$sqlnontardecustomer);
				$cntnontradecustomer=mysqli_num_rows($rsnontardecustomer);
				if($cntnontradecustomer==0)
				{
					$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  non_trade_customer_master";
					$rsmaxcustomercode=mysqli_query($link,$sqlmaxcustomercode);
					$rowmaxcustomercode=mysqli_fetch_assoc($rsmaxcustomercode);
					$max_customer_code=$rowmaxcustomercode['max_customer_code'];
						
					if($max_customer_code=='')
					{
						$max_customer_code='NT000001';
					}
					else
					{
						$max_customer_code++;
					}
					
				$sqlinsertnontardecustomer="INSERT INTO non_trade_customer_master SET customer_code='".$max_customer_code."',
												customer_name='".addslashes(${$ihb_name.$surveytansid_array_mailbody[$countarr]})."',
												address		='".addslashes(${$ihb_address.$surveytansid_array_mailbody[$countarr]})."',
												phone_no	='".${$ihb_no.$surveytansid_array_mailbody[$countarr]}."',
												route_code	='".$surveyroute_array_mailbody[$countarr]."',
												emp_code	='".substr($surveytansid_array_mailbody[$countarr],2,5)."',
												rds_tag		='".${$ihb_rds_tag.$surveytansid_array_mailbody[$countarr]}."',
												download_time=CURRENT_TIMESTAMP()";
					if(mysqli_query($link,$sqlinsertnontardecustomer))
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
				   $rownontardecustomer=mysqli_fetch_assoc($rsnontardecustomer);
				   $non_trade_customer_code=$rownontardecustomer['customer_code'];
				   $non_trade_customer_phone=$rownontardecustomer['phone_no'];
				   $sqlupdatenontardecustomer="UPDATE non_trade_customer_master SET
												address		='".addslashes(${$ihb_address.$surveytansid_array_mailbody[$countarr]})."',
												route_code	='".$surveyroute_array_mailbody[$countarr]."',
												emp_code	='".substr($surveytansid_array_mailbody[$countarr],2,5)."',
												rds_tag		='".${$ihb_rds_tag.$surveytansid_array_mailbody[$countarr]}."',
												download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$non_trade_customer_code."' AND 
												phone_no	='".$non_trade_customer_phone."'";
					if(mysqli_query($link,$sqlupdatenontardecustomer))
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
			//End for MAITHAN non tade customer data updation
			$survey_email=SURVEYEMAILRECIPENTS;
			$surveyemailsubj="$nick_name - Survey made by ".$surveyemp_array_mailbody[$countarr]." on ".date('d-m-Y H:i:s',strtotime($surveydate_array_mailbody[$countarr]));
			$surveyemailbody = $surveyheader_array_mailbody[$countarr]."Powered By aceDNS<br /></body></html>";
			
			$strSid = md5(uniqid(time()));
			$headers='';
			//$headers .= "Content-type: text/html; charset=UTF-8\n";
			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=UTF-8\n";
			$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
						"Bcc: ".BCCEMAIL." \r\n" .
						'X-Mailer: PHP/' . phpversion();
			/*$headers .= "MIME-Version: 1.0\r\n";			
			$headers .= "Content-Type: multipart/mixed; boundary=\"".$strSid."\"\n\n";
			$headers .= "This is a multi-part message in MIME format.\n";
			$headers .= "--".$strSid."\n";
			$headers .= "Content-type: text/html; charset=UTF-8\n"; // or UTF-8 //
			$headers .= "Content-Transfer-Encoding: 7bit\n\n";
			$headers .= $surveyemailbody."\n\n";
			$strContent1 = base64_encode(${$surveyexcelbody.$surveytansid_array_mailbody[$countarr]});
			$headers .= "--".$strSid."\n";
			$headers .= "Content-Type: application/octet-stream; name=\"survey_$surveytansid_array_mailbody[$countarr].xls\"\n";
			$headers .= "Content-Transfer-Encoding: base64\n";
			$headers .= "Content-Disposition: attachment; filename=\"survey_$surveytansid_array_mailbody[$countarr].xls\"\n\n";
			$headers .= $strContent1."\n\n";*/		

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
 /* -------------------------------------END QUERY FOR SURVEY------------------------------------------------------------------------------------*/
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
	$url = "http://www.acedns.in/acednsproduct/operationdb-survey-6.0.8.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&last_git_master_update_time=$last_git_master_update_time&last_loyalty_purchase_update_time=$last_loyalty_purchase_update_time";
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
