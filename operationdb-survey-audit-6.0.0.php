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
//$last_update_time=$_REQUEST['last_update_time'];
//$last_update_time=str_replace('€',' ',$last_update_time);


/*$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$result = mysqli_query($link,$sqlquery);
$countdatarefresh=mysqli_num_rows($result);*/
$body=file_get_contents('php://input');

$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
					 xml='".$body_xml."',
					insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);	

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><surveyaudit><location><emp_code><![CDATA[E0008]]></emp_code><trans_id><![CDATA[SUAE001720151212135428]]></trans_id><latt><![CDATA[22.5642544]]></latt><longi><![CDATA[88.3568055]]></longi><date><![CDATA[2015-12-12 13:54:30]]></date></location><surveyauditdata><survey_audit_details><survey_audit_id><![CDATA[SUAE001720151212135428]]></survey_audit_id><survey_id><![CDATA[SUE001720151212135428]]></survey_id><action_id><![CDATA[ ]]></action_id><value><![CDATA[Hg]]></value><status><![CDATA[changed]]></status><row_id><![CDATA[RA002]]></row_id></survey_audit_details><survey_audit_details><survey_audit_id><![CDATA[SUAE001720151212135428]]></survey_audit_id><survey_id><![CDATA[SUE001720151212135428]]></survey_id><action_id><![CDATA[ ]]></action_id><value><![CDATA[Hg]]></value><status><![CDATA[changed]]></status><row_id><![CDATA[RA004]]></row_id></survey_audit_details></surveyauditdata></surveyaudit></root>";*/

$survey_audit_emp_code="*ROOT*SURVEYAUDIT*LOCATION*EMP_CODE";
$survey_audit_trans_id = "*ROOT*SURVEYAUDIT*LOCATION*TRANS_ID";
$survey_audit_latt = "*ROOT*SURVEYAUDIT*LOCATION*LATT";
$survey_audit_longi = "*ROOT*SURVEYAUDIT*LOCATION*LONGI";
$survey_audit_date="*ROOT*SURVEYAUDIT*LOCATION*DATE";
$survey_audit_id = "*ROOT*SURVEYAUDIT*SURVEYAUDITDATA*SURVEY_AUDIT_DETAILS*SURVEY_AUDIT_ID";
$survey_id = "*ROOT*SURVEYAUDIT*SURVEYAUDITDATA*SURVEY_AUDIT_DETAILS*SURVEY_ID";
$action_id = "*ROOT*SURVEYAUDIT*SURVEYAUDITDATA*SURVEY_AUDIT_DETAILS*ACTION_ID";
$value = "*ROOT*SURVEYAUDIT*SURVEYAUDITDATA*SURVEY_AUDIT_DETAILS*VALUE";
$status = "*ROOT*SURVEYAUDIT*SURVEYAUDITDATA*SURVEY_AUDIT_DETAILS*STATUS";
$type = "*ROOT*SURVEYAUDIT*SURVEYAUDITDATA*SURVEY_AUDIT_DETAILS*TYPE";
$row_id = "*ROOT*SURVEYAUDIT*SURVEYAUDITDATA*SURVEY_AUDIT_DETAILS*ROW_ID";

$survey_audit_array=array();
$survey_audit_details_array=array();

$counter = 0;
$countersurveyaudit=0;
$countersurveyauditdetails=0;

class xml_survey_audit{
	var $survey_audit_emp_code,$survey_audit_trans_id,$survey_audit_latt,$survey_audit_longi,$survey_audit_date;	
}
class xml_survey_audit_details{
	var $survey_audit_id,$survey_id,$action_id,$value,$status,$type,$row_id;	
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
    global $current_tag, $counter, $countersurveyaudit,$countersurveyauditdetails,$survey_audit_array,$survey_audit_details_array,
	$survey_audit_emp_code,$survey_audit_trans_id,$survey_audit_latt,$survey_audit_longi,$survey_audit_date,$survey_audit_id,$survey_id,$action_id,
	$value,$status,$type,$row_id;
	//echo $current_tag.'<br />';
	//echo $data;
	if(substr($current_tag,0,17)=='*ROOT*SURVEYAUDIT')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $survey_audit_emp_code:
				$survey_audit_array[$countersurveyaudit] = new xml_survey_audit();
				$survey_audit_array[$countersurveyaudit]->survey_audit_emp_code = $data;
				break;
			case $survey_audit_trans_id:
				$survey_audit_array[$countersurveyaudit]->survey_audit_trans_id = $data;
				break;
			case $survey_audit_latt:
				$survey_audit_array[$countersurveyaudit]->survey_audit_latt = $data;
				break;
			case $survey_audit_longi:
				$survey_audit_array[$countersurveyaudit]->survey_audit_longi = $data;
				break;
			case $survey_audit_date:
				$survey_audit_array[$countersurveyaudit]->survey_audit_date = $data;
				$countersurveyaudit++;
				break;
		}
	}
	if(substr($current_tag,0,54)=='*ROOT*SURVEYAUDIT*SURVEYAUDITDATA*SURVEY_AUDIT_DETAILS')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $survey_audit_id:
				$survey_audit_details_array[$countersurveyauditdetails] = new xml_survey_audit_details();
				$survey_audit_details_array[$countersurveyauditdetails]->survey_audit_id = $data;
				break;
			case $survey_id:
				$survey_audit_details_array[$countersurveyauditdetails]->survey_id = $data;
				break;	
			case $action_id:
				$survey_audit_details_array[$countersurveyauditdetails]->action_id = $data;
				break;
			case $value:
				$survey_audit_details_array[$countersurveyauditdetails]->value = $data;
				break;
			case $status:
				$survey_audit_details_array[$countersurveyauditdetails]->status = $data;
				break;	
			case $type:
				$survey_audit_details_array[$countersurveyauditdetails]->type = $data;
				break;		
			case $row_id:
				$survey_audit_details_array[$countersurveyauditdetails]->row_id = $data;
				$countersurveyauditdetails++;
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

/* --------------------START QUERY FOR SURVEY AUDIT ----------------------------------------------------------------------------------------------------*/
$survey_audit_array_trans_id=array();
$surveyaudittansid_array_mailbody=array();
$surveyauditdate_array_mailbody=array();
$surveyauditemp_array_mailbody=array();
$surveyauditexcel_array_mailbody=array();
$surveyauditheader_array_mailbody=array();
$survey_id_array=array();

//print_r($survey_array);
if(count($survey_audit_array)>0)
{
	for($x=0;$x<count($survey_audit_array);$x++){
		$survey_audit_emp_code=$survey_audit_array[$x]->survey_audit_emp_code;
		$survey_audit_trans_id=$survey_audit_array[$x]->survey_audit_trans_id;
		$survey_audit_latt=$survey_audit_array[$x]->survey_audit_latt;
		$survey_audit_longi=$survey_audit_array[$x]->survey_audit_longi;
		$survey_audit_date=$survey_audit_array[$x]->survey_audit_date;
		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if($survey_latt>0 && $survey_longi>0)
		{
			$sqlupdatelatlongzero="UPDATE location SET latt='".$survey_audit_latt."',longi='".$survey_audit_longi."' WHERE 
									emp_code='".$survey_audit_emp_code."' AND latt='0' AND longi='0'";
			$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
		}
		//For checking that trans id exist or not for survey audit
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$survey_audit_trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check survey audit location: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for survey
		if($countchkorlocation>0)
		{
			//$survey_trans_id_chk=substr($survey_trans_id,1,19);
			if(!in_array($survey_audit_trans_id,$survey_audit_array_trans_id))
			{
				array_push($survey_audit_array_trans_id,$survey_audit_trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$survey_audit_emp_code."',
									latt='".$survey_audit_latt."',
									longi='".$survey_audit_longi."'
									WHERE trans_id='".$survey_audit_trans_id."'";
			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update survey audit location: ".$sqlupdateorlocation);
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
			$sqlempname="SELECT emp_name FROM employee_master WHERE emp_code='".$survey_audit_emp_code."'";
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
			$sqlinsertsurveyauditlocation="INSERT INTO location SET emp_code='".$survey_audit_emp_code."',
									trans_id='".$survey_audit_trans_id."',
									latt='".$survey_audit_latt."',
									longi='".$survey_audit_longi."',
									date='".$survey_audit_date."',
									updatetime='".$location_date."'"; 
			if(mysqli_query($link,$sqlinsertsurveyauditlocation))
			{
				$flag=5;
			}
			else
			{
				mysqli_query($link,"ROLLBACK");
				echo $flag=0;
				return;
			}	
			$addresssurvey=getReverseGeo($survey_audit_latt,$survey_audit_longi);	
			//For constructing the email body for SURVEY  if SURVEY has performed
			$surveyemailbody = "<html><head><title>Survey Audit Details</title></head>
						<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
						.$emp_name. " On ".date('d-m-Y H:i:s',strtotime($survey_audit_date))."</b><br /><br />Refference no: <b>".$survey_audit_trans_id."</b><br />Address: <b>".$addresssurvey."</b><br /><br />";
						
			array_push($surveyauditemp_array_mailbody,$emp_name);
			array_push($surveyauditdate_array_mailbody,$survey_audit_date);
			array_push($surveyaudittansid_array_mailbody,$survey_audit_trans_id);
			array_push($surveyauditheader_array_mailbody,$surveyemailbody);
			${layout_name_array.$survey_audit_trans_id}=array();
		 }//End of else
	}// End for loop
	//echo count($survey_details_array);
	
	if(count($survey_audit_details_array)>0)
	{
		for($i=0;$i<count($survey_audit_details_array);$i++){
			$survey_audit_id=$survey_audit_details_array[$i]->survey_audit_id;
			$survey_id=$survey_audit_details_array[$i]->survey_id;
			$action_id=$survey_audit_details_array[$i]->action_id;
			$value=$survey_audit_details_array[$i]->value;
			$status=$survey_audit_details_array[$i]->status;
			$type=$survey_audit_details_array[$i]->type;
			$row_id=$survey_audit_details_array[$i]->row_id;
			if(!in_array($survey_audit_id,$survey_audit_array_trans_id))
			{
				//exit();
			//For Insert into the Survey output table for new survey id
			 $sqlinsertsurveyaudit="INSERT INTO DCA_transaction SET DCA_trans_id='".$survey_audit_id."',
									survey_id='".$survey_id."',
								   action_id 	='".$action_id."',
								   value		='".addslashes(preg_replace('/[\r\n]+/', '',$value))."',
								   status		='".addslashes(preg_replace('/[\r\n]+/', '',$status))."',
								   type			='".addslashes(preg_replace('/[\r\n]+/', '',$type))."',
								   row_id		='".$row_id."'";   
			if(mysqli_query($link,$sqlinsertsurveyaudit))
			{
				$flag=5;
			}
			else
			{
				mysqli_query($link,"ROLLBACK");
				echo $flag=0;
				return;
			}
			if($status=='CHANGED')
			{
				$sqlupdatepublish="UPDATE survey_publish SET status='published',value='".addslashes(preg_replace('/[\r\n]+/', '',$value))."' 
									WHERE survey_id='".$survey_id."' AND row_id='".$row_id."'";
				if(mysqli_query($link,$sqlupdatepublish))
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
			else if($status=='CLOSED')
			{
				$sqlupdatepublish="UPDATE survey_publish SET status='closed' WHERE survey_id='".$survey_id."'";
				if(mysqli_query($link,$sqlupdatepublish))
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
			if(!in_array($survey_id,$survey_id_array))
			{
				array_push($survey_id_array,$survey_id);
			}
			$survey_input_details="SELECT display_name,layout_name FROM survey_input WHERE row_id='".$row_id."' AND action_id='".trim($action_id)."'";
			$rssurvey_input_details=mysqli_query($link,$survey_input_details);
			$rowsurvey_input_details=mysqli_fetch_assoc($rssurvey_input_details);
			$display_name=$rowsurvey_input_details['display_name'];
			$layout_name=$rowsurvey_input_details['layout_name'];
			if(!in_array($layout_name,${layout_name_array.$survey_audit_id}))
			{
				if($i!=0)
				{
					${surveyexcelbody.$survey_audit_id}.="\n";
				}
				${surveyexcelbody.$survey_audit_id}.=$layout_name."\n";
				array_push(${layout_name_array.$survey_audit_id},$layout_name);
			}
			${surveyexcelbody.$survey_audit_id}.=$display_name."\t".preg_replace('/[\r\n]+/', '',$value)."\n";
		  }
		}//End for loop
		//print_r($survey_id_array);
		foreach($survey_id_array as $survey_id_val)
		{
			$sqlupdatesurveyheader="UPDATE survey_header SET status='DCA' WHERE survey_id='".$survey_id_val."' AND status='ready to publish'";
			if(mysqli_query($link,$sqlupdatesurveyheader))
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
	}//End of if
	//End Insert into the Survey output table for new survey id
		/*for($countarr=0;$countarr<count($survey_array);$countarr++)
		{
			$survey_email=SURVEYEMAILRECIPENTS;
			$surveyemailsubj="Survey made by ".$surveyemp_array_mailbody[$countarr]." on ".date('d-m-Y H:i:s',strtotime($surveydate_array_mailbody[$countarr]));
			$surveyemailbody = $surveyheader_array_mailbody[$countarr]."Powered By aceDNS<br /></body></html>";
			
			$strSid = md5(uniqid(time()));
			$headers='';
			//$headers .= "Content-type: text/html; charset=UTF-8\n";
			//$headers  = "MIME-Version: 1.0\r\n";
			//$headers .= "Content-type: text/html; charset=UTF-8\n";
			$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
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
		}*/
	}
 /* -------------------------------------END QUERY FOR SURVEY AUDIT -----------------------------------------------------------------------------------*/
	if($flag==5)
	{
		 $sqlupdatelastoperationtime="UPDATE changepassword SET last_operation_datetime='".$last_operation_datetime."' WHERE emp_code='".$emp_code."'";
		 $rsupdatelastoperationtime=mysqli_query($link,$sqlupdatelastoperationtime);	 
		 mysqli_query($link,"COMMIT");
		 
		 echo $flag=1;
	}
	if($flag==6)
	{
		$sqlupdatelastoperationtime="UPDATE changepassword SET last_operation_datetime='".$last_operation_datetime."' WHERE emp_code='".$emp_code."'";
		$rsupdatelastoperationtime=mysqli_query($link,$sqlupdatelastoperationtime);
		mysqli_query($link,"COMMIT");
		
		echo $flag=1;
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/operationdb-survey-audit-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&last_git_master_update_time=$last_git_master_update_time&last_loyalty_purchase_update_time=$last_loyalty_purchase_update_time";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	mysqli_close($link);
?>
