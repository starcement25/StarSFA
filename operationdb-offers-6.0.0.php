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
$body=file_get_contents('php://input');

$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
					 xml='".$body_xml."',
					insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);	


$offer_emp_code="*ROOT*OFFER*LOCATION*EMP_CODE";
$trans_id = "*ROOT*OFFER*LOCATION*TRANS_ID";
$offer_latt = "*ROOT*OFFER*LOCATION*LATT";
$offer_longi = "*ROOT*OFFER*LOCATION*LONGI";
$offer_date="*ROOT*OFFER*LOCATION*DATE";
$offer_trans_id = "*ROOT*OFFER*OFFERDATA*OFFER_TRANS_ID";
$row_id = "*ROOT*OFFER*OFFERDATA*ROW_ID";
$action_id = "*ROOT*OFFER*OFFERDATA*ACTION_ID";
$mall_id = "*ROOT*OFFER*OFFERDATA*MALL_ID";
$business_name = "*ROOT*OFFER*OFFERDATA*BUSINESS_NAME";
$value = "*ROOT*OFFER*OFFERDATA*VALUE";
$type = "*ROOT*OFFER*OFFERDATA*TYPE";
$survey_id = "*ROOT*OFFER*OFFERDATA*SURVEY_ID";

$offer_array=array();
$offer_details_array=array();

$counter = 0;
$counteroffer=0;
$counterofferdetails=0;

class xml_offer{
	var $offer_emp_code,$trans_id,$offer_latt,$offer_longi,$offer_date;	
}
class xml_offer_details{
	var $offer_trans_id,$row_id,$action_id,$mall_id,$business_name,$value,$type,$survey_id;	
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
    global $current_tag, $counter, $counteroffer,$counterofferdetails,$offer_array,$offer_details_array,
	$offer_emp_code,$trans_id,$offer_latt,$offer_longi,$offer_date,$offer_trans_id,$row_id,$action_id,$mall_id,$business_name,
	$value,$type,$survey_id;
	//echo $current_tag.'<br />';
	//echo $data;
	if(substr($current_tag,0,11)=='*ROOT*OFFER')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $offer_emp_code:
				$offer_array[$counteroffer] = new xml_offer();
				$offer_array[$counteroffer]->offer_emp_code = $data;
				break;
			case $trans_id:
				$offer_array[$counteroffer]->trans_id = $data;
				break;
			case $offer_latt:
				$offer_array[$counteroffer]->offer_latt = $data;
				break;
			case $offer_longi:
				$offer_array[$counteroffer]->offer_longi = $data;
				break;
			case $offer_date:
				$offer_array[$counteroffer]->offer_date = $data;
				$counteroffer++;
				break;
		}
	}
	if(substr($current_tag,0,21)=='*ROOT*OFFER*OFFERDATA')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $offer_trans_id:
				$offer_details_array[$counterofferdetails] = new xml_offer_details();
				$offer_details_array[$counterofferdetails]->offer_trans_id = $data;
				break;
			case $row_id:
				$offer_details_array[$counterofferdetails]->row_id = $data;
				break;	
			case $action_id:
				$offer_details_array[$counterofferdetails]->action_id = $data;
				break;
			case $mall_id:
				$offer_details_array[$counterofferdetails]->mall_id = $data;
				break;
			case $business_name:
				$offer_details_array[$counterofferdetails]->business_name = $data;
				break;	
			case $value:
				$offer_details_array[$counterofferdetails]->value = $data;
				break;
			case $type:
				$offer_details_array[$counterofferdetails]->type = $data;
				break;
			case $survey_id:
				$offer_details_array[$counterofferdetails]->survey_id = $data;
				$counterofferdetails++;
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
//print_r($offer_array);
mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");

$flag=1;
/* --------------------START QUERY FOR OFFER ----------------------------------------------------------------------------------------------------*/
$offer_array_trans_id=array();
$offer_trans_id_array=array();
$survey_id_array=array();
$mall_id_array=array();
$business_name_array=array();
//print_r($offer_array);
if(count($offer_array)>0)
{
	for($x=0;$x<count($offer_array);$x++){
		$offer_emp_code=$offer_array[$x]->offer_emp_code;
		$trans_id=$offer_array[$x]->trans_id;
		$offer_latt=$offer_array[$x]->offer_latt;
		$offer_longi=$offer_array[$x]->offer_longi;
		$offer_date=$offer_array[$x]->offer_date;
		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if($offer_latt>0 && $offer_longi>0)
		{
			$sqlupdatelatlongzero="UPDATE location SET latt='".$offer_latt."',longi='".$offer_longi."' WHERE 
									emp_code='".$offer_emp_code."' AND latt='0' AND longi='0'";
			$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
		}
		//For checking that trans id exist or not for offer
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check offer location: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for offer
		if($countchkorlocation>0)
		{
			if(!in_array($trans_id,$offer_array_trans_id))
			{
				array_push($offer_array_trans_id,$trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$offer_emp_code."',
									latt='".$offer_latt."',
									longi='".$offer_longi."'
									WHERE trans_id='".$trans_id."'";
			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update offer location: ".$sqlupdateorlocation);
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
			$sqlempname="SELECT emp_name FROM employee_master WHERE emp_code='".$offer_emp_code."'";
			$rsempname=mysqli_query($link,$sqlempname);
			$rowempname=mysqli_fetch_assoc($rsempname);
			$emp_name=$rowempname['emp_name'];
			
			// create the data for location table date field , by checking the current date and time and the actual date and time of offer
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));

			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			//For Insert into the location table for new trans id regarding offer
			$sqlinsertofferlocation="INSERT INTO location SET emp_code='".$offer_emp_code."',
									trans_id='".$trans_id."',
									latt='".$offer_latt."',
									longi='".$offer_longi."',
									date='".$offer_date."',
									updatetime='".$location_date."'"; 
			if(mysqli_query($link,$sqlinsertofferlocation))
			{
				$flag=5;
			}
			else
			{
				mysqli_query($link,"ROLLBACK");
				echo $flag=0;
				return;
			}	
			//$addresssurvey=getReverseGeo($survey_audit_latt,$survey_audit_longi);	
			//For constructing the email body for SURVEY  if SURVEY has performed
			/*$surveyemailbody = "<html><head><title>Survey Audit Details</title></head>
						<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
						.$emp_name. " On ".date('d-m-Y H:i:s',strtotime($survey_audit_date))."</b><br /><br />Refference no: <b>".$survey_audit_trans_id."</b><br />Address: <b>".$addresssurvey."</b><br /><br />";
						
			array_push($surveyauditemp_array_mailbody,$emp_name);
			array_push($surveyauditdate_array_mailbody,$survey_audit_date);
			array_push($surveyaudittansid_array_mailbody,$survey_audit_trans_id);
			array_push($surveyauditheader_array_mailbody,$surveyemailbody);
			${layout_name_array.$survey_audit_trans_id}=array();*/
		 }//End of else
	}// End for loop
	//echo count($survey_details_array);
	//print_r($offer_details_array);
	
	if(count($offer_details_array)>0)
	{
		for($i=0;$i<count($offer_details_array);$i++){
			$offer_trans_id=$offer_details_array[$i]->offer_trans_id;
			$row_id=$offer_details_array[$i]->row_id;
			$action_id=$offer_details_array[$i]->action_id;
			$mall_id=$offer_details_array[$i]->mall_id;
			$business_name=$offer_details_array[$i]->business_name;			
			$value=$offer_details_array[$i]->value;
			$type=$offer_details_array[$i]->type;
			$survey_id=$offer_details_array[$i]->survey_id;
			if(!in_array($offer_trans_id,$offer_array_trans_id))
			{
			//For Insert into the offer transaction table for new offer id
			 $sqlinsertofferdetails="INSERT INTO offer_transaction SET offer_trans_id='".$offer_trans_id."',
									row_id='".$row_id."',
								   	action_id 	='".$action_id."',
								   	mall_id 	='".$mall_id."',
								   	business_name 	='".addslashes($business_name)."',
								   	value		='".addslashes(preg_replace('/[\r\n]+/', '',$value))."',
									survey_id	='".$survey_id."',
								   	type		='".addslashes(preg_replace('/[\r\n]+/', '',$type))."'";   
			if(mysqli_query($link,$sqlinsertofferdetails))
			{
				$flag=5;
				if(!in_array($offer_trans_id,$offer_trans_id_array))
				{
					array_push($offer_trans_id_array,$offer_trans_id);
					array_push($survey_id_array,$survey_id);
					array_push($mall_id_array,$mall_id);
					array_push($business_name_array,$business_name);
				}
			}
			else
			{
				mysqli_query($link,"ROLLBACK");
				echo $flag=0;
				return;
			}
		  }
		}//End for loop
	}//End of if
	for($m=0;$m<count($offer_trans_id_array);$m++)
	{
		$sqlselectmenuname="SELECT menu_name FROM survey_header WHERE survey_id='".$survey_id_array[$m]."'";
		$rsselectmenuname=mysqli_query($link,$sqlselectmenuname);
		$rowselectmenuname=mysqli_fetch_assoc($rsselectmenuname);
		$menu_name=$rowselectmenuname['menu_name'];
		if(!in_array($offer_trans_id_array[$m],$offer_array_trans_id))
		{
		$sqlinsertofferheader="INSERT INTO offer_header SET 
							   survey_id='".$survey_id_array[$m]."',offer_trans_id='".$offer_trans_id_array[$m]."',mall_id='".$mall_id_array[$m]."',
								menu_name='".addslashes($menu_name)."',business_name='".addslashes($business_name_array[$m])."',status='PENDING',transferred_flag='no',
								download_time=CURRENT_TIMESTAMP()";
		if(mysqli_query($link,$sqlinsertofferheader))
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
 /* -------------------------------------END QUERY FOR OFFER -----------------------------------------------------------------------------------*/
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
	$url = "http://www.acedns.in/acednsproduct/operationdb-offers-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
	insertapilog($datetime,$emp_code,$url,$nick_name);
?>
