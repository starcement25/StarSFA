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

$body=file_get_contents('php://input');

$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
					xml='".$body_xml."',
					insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);	

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><nosurvey><location><emp_code><![CDATA[E0020]]></emp_code><trans_id><![CDATA[NSUE002020160608183821]]></trans_id><latt><![CDATA[22.5640857]]></latt><longi><![CDATA[88.3568232]]></longi><date><![CDATA[2016-06-08 18:38:21]]></date></location><nosurveydata><survey_id><![CDATA[NSUE002020160608183821]]></survey_id><survey_type><![CDATA[mall]]></survey_type><menu_name><![CDATA[RA115]]></menu_name><mall_id><![CDATA[M0167]]></mall_id><mall_name><![CDATA[Maidan Market ]]></mall_name><business_name><![CDATA[TEST FORCEO]]></business_name></nosurveydata></nosurvey></root>";*/

$location_emp_code="*ROOT*NOSURVEY*LOCATION*EMP_CODE";
$location_trans_id = "*ROOT*NOSURVEY*LOCATION*TRANS_ID";
$location_latt = "*ROOT*NOSURVEY*LOCATION*LATT";
$location_longi = "*ROOT*NOSURVEY*LOCATION*LONGI";
$location_date="*ROOT*NOSURVEY*LOCATION*DATE";

$survey_id = "*ROOT*NOSURVEY*NOSURVEYDATA*SURVEY_ID";
$survey_type = "*ROOT*NOSURVEY*NOSURVEYDATA*SURVEY_TYPE";
$menu_name = "*ROOT*NOSURVEY*NOSURVEYDATA*MENU_NAME";
$mall_id = "*ROOT*NOSURVEY*NOSURVEYDATA*MALL_ID";
$mall_name = "*ROOT*NOSURVEY*NOSURVEYDATA*MALL_NAME";
$business_name = "*ROOT*NOSURVEY*NOSURVEYDATA*BUSINESS_NAME";

$nosurvey_array=array();

$counter = 0;
class xml_nosurvey{
	var $location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$survey_id,$survey_type,$menu_name,$mall_id,$mall_name,$business_name;	
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
    global $current_tag,$counter,$location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$survey_id,$survey_type,$menu_name,$mall_id,$mall_name,$business_name,$nosurvey_array;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,14)=='*ROOT*NOSURVEY')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $location_emp_code:
				$nosurvey_array[$counter] = new xml_nosurvey();
				$nosurvey_array[$counter]->location_emp_code = $data;
				break;
			case $location_trans_id:
				$nosurvey_array[$counter]->location_trans_id = $data;
				break;
			case $location_latt:
				$nosurvey_array[$counter]->location_latt = $data;
				break;
			case $location_longi:
				$nosurvey_array[$counter]->location_longi = $data;
				break;
			case $location_date:
				$nosurvey_array[$counter]->location_date = $data;
				break;
			case $survey_id:
				$nosurvey_array[$counter]->survey_id = $data;
				break;
			case $survey_type:
				$nosurvey_array[$counter]->survey_type = $data;
				break;
			case $menu_name:
				$nosurvey_array[$counter]->menu_name = $data;
				break;			
			case $mall_id:
				$nosurvey_array[$counter]->mall_id = $data;
				break;	
			case $mall_name:
				$nosurvey_array[$counter]->mall_name = $data;
				break;
			case $business_name:
				$nosurvey_array[$counter]->business_name = $data;
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
//print_r($nosurvey_array);
mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");

$flag=1;

/* --------------------START QUERY FOR No survey------------------------------------------------------------------------------------------------*/
$nosurvey_array_trans_id=array();
if(count($nosurvey_array)>0)
{
	for($x=0;$x<count($nosurvey_array);$x++){
		$location_emp_code=$nosurvey_array[$x]->location_emp_code;
		$location_trans_id=$nosurvey_array[$x]->location_trans_id;
		$location_latt=$nosurvey_array[$x]->location_latt;
		$location_longi=$nosurvey_array[$x]->location_longi;
		$location_date=$nosurvey_array[$x]->location_date;
		$survey_id=$nosurvey_array[$x]->survey_id;
		$survey_type=$nosurvey_array[$x]->survey_type;
		$menu_name=$nosurvey_array[$x]->menu_name;
		$mall_id=$nosurvey_array[$x]->mall_id;
		$mall_name=$nosurvey_array[$x]->mall_name;
		$business_name=$nosurvey_array[$x]->business_name;
		
		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if($location_latt>0 && $location_longi>0)
		{
			$sqlupdatelatlongzero="UPDATE location SET latt='".$location_latt."',longi='".$location_longi."' WHERE 
									emp_code='".$location_emp_code."' AND latt='0' AND longi='0'";
			$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
		}

		//For checking that trans id exist or not for no survey
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$location_trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check Product Promotion: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for no survey
		if($countchkorlocation>0)
		{
			if(!in_array($location_trans_id,$nosurvey_array_trans_id))
			{
				array_push($nosurvey_array_trans_id,$location_trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$location_emp_code."',
									latt='".$location_latt."',
									longi='".$location_longi."'
									WHERE trans_id='".$location_trans_id."'";
			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update no survey location: ".$sqlupdateorlocation);
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
		$sqlempname="SELECT emp_name,branch_code,vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
		$rsempname=mysqli_query($link,$sqlempname);
		$rowempname=mysqli_fetch_assoc($rsempname);
		$emp_name=$rowempname['emp_name'];
		$branch_code=$rowempname['branch_code'];
		$vertical_value=$rowempname['vertical_value'];
		
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));

		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$location_date_updatetime=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;

		//For Insert into the location table for new trans id regarding no survey
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
		// Add new no survey
		$sqlinsertnosurvey="INSERT INTO survey_header SET survey_id ='".$survey_id."',
							survey_type					='".addslashes($survey_type)."',
							menu_name					='".addslashes($menu_name)."',
							mall_id						='".$mall_id."',
							mall_hs_name					='".addslashes($mall_name)."',
							business_name				='".addslashes($business_name)."',
							
							download_time				=CURRENT_TIMESTAMP(),
							status						='Not Interested'";
		if(mysqli_query($link,$sqlinsertnosurvey))
		{
			$flag=5;
		}
		else
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}
		//Start for STAR mis data details No survey data updation
		if($nick_name=='STAR')
		{
			$qty='';
			$trans_type='NSU';
			$trans_sub_type='';
			update_transaction_STAR($location_emp_code,$survey_id,$qty,$trans_type,$trans_sub_type,$link);
		}
	   //End for STAR mis data details No survey data updation

	  }//End of else
	}
}
 /* --------------------END QUERY For No survey--------------------------------------------------------------------------------------------------------*/
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
//echo $flag=2;
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = APICALLLOGURL."/operationdb-no-survey.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
mysqli_close($link);
?>
