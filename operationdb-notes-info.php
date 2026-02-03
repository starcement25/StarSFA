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
/*$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$result = mysqli_query($link,$sqlquery);
$countdatarefresh=mysqli_num_rows($result);*/
$body=file_get_contents('php://input');

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><NOTES_INFO><location><emp_code><![CDATA[E0096]]></emp_code><trans_id><![CDATA[NIE009620160418160521]]></trans_id><latt><![CDATA[22.5643652]]></latt><longi><![CDATA[88.3568814]]></longi><date><![CDATA[2016-04-18 16:05:21]]></date></location>
<NOTES_INFO_DATA><NOTES_INFO_ID><![CDATA[NIE009620160418160521]]></NOTES_INFO_ID><FEEDBACK><![CDATA[vygvgvgv]]></FEEDBACK></NOTES_INFO_DATA></NOTES_INFO><NOTES_INFO><location><emp_code><![CDATA[E0096]]></emp_code><trans_id><![CDATA[NIE009620160418160804]]></trans_id><latt><![CDATA[22.5643697]]></latt><longi><![CDATA[88.3568751]]></longi><date><![CDATA[2016-04-18 16:08:04]]></date></location><NOTES_INFO_DATA><NOTES_INFO_ID><![CDATA[NIE009620160418160804]]></NOTES_INFO_ID><FEEDBACK><![CDATA[HV yg gbgb]]></FEEDBACK></NOTES_INFO_DATA></NOTES_INFO><NOTES_INFO><location><emp_code><![CDATA[E0096]]></emp_code><trans_id><![CDATA[NIE009620160418161036]]></trans_id><latt><![CDATA[22.5643596]]></latt><longi><![CDATA[88.3568777]]></longi><date><![CDATA[2016-04-18 16:10:36]]></date></location><NOTES_INFO_DATA><NOTES_INFO_ID><![CDATA[NIE009620160418161036]]></NOTES_INFO_ID><FEEDBACK><![CDATA[fctftvtg]]></FEEDBACK></NOTES_INFO_DATA></NOTES_INFO></root>";*/

$body_xml=str_replace("'",'"',$body);
	$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
	mysqli_query($link,$sqlinsert_xml_data);
	
$location_emp_code="*ROOT*NOTES_INFO*LOCATION*EMP_CODE";
$location_trans_id = "*ROOT*NOTES_INFO*LOCATION*TRANS_ID";
$location_latt = "*ROOT*NOTES_INFO*LOCATION*LATT";
$location_longi = "*ROOT*NOTES_INFO*LOCATION*LONGI";
$location_date="*ROOT*NOTES_INFO*LOCATION*DATE";

$notes_info_id = "*ROOT*NOTES_INFO*NOTES_INFO_DATA*NOTES_INFO_ID";
$feedback="*ROOT*NOTES_INFO*NOTES_INFO_DATA*FEEDBACK";

$notes_info_array=array();

$counter = 0;

class xml_notes_info{
	var $location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$notes_info_id,$feedback;	
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
    global $current_tag,$counter,$location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$notes_info_id,$feedback,
	$notes_info_array;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,16)=='*ROOT*NOTES_INFO')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $location_emp_code:
				$notes_info_array[$counter] = new xml_notes_info();
				$notes_info_array[$counter]->location_emp_code = $data;
				break;
			case $location_trans_id:
				$notes_info_array[$counter]->location_trans_id = $data;
				break;
			case $location_latt:
				$notes_info_array[$counter]->location_latt = $data;
				break;
			case $location_longi:
				$notes_info_array[$counter]->location_longi = $data;
				break;
			case $location_date:
				$notes_info_array[$counter]->location_date = $data;
				break;
			case $notes_info_id:
				$notes_info_array[$counter]->notes_info_id = $data;
				break;
			case $feedback:
				$notes_info_array[$counter]->feedback = $data;
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
mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");

$flag=1;
/* --------------------START QUERY FOR NOTES AND INFO ------------------------------------------------------------------------------------------*/
//print_r($notes_info_array);
$notes_info_array_trans_id=array();
if(count($notes_info_array)>0)
{
		for($x=0;$x<count($notes_info_array);$x++){
			$location_emp_code=$notes_info_array[$x]->location_emp_code;
			$location_trans_id=$notes_info_array[$x]->location_trans_id;
			$location_latt=$notes_info_array[$x]->location_latt;
			$location_longi=$notes_info_array[$x]->location_longi;
			$location_date=$notes_info_array[$x]->location_date;
			$notes_info_id=$notes_info_array[$x]->notes_info_id;
			$feedback=$notes_info_array[$x]->feedback;
			
			//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
			if($location_latt>0 && $location_longi>0)
			{
				$sqlupdatelatlongzero="UPDATE location SET latt='".$location_latt."',longi='".$location_longi."' WHERE 
										emp_code='".$location_emp_code."' AND latt='0' AND longi='0'";
				$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
			}
	
			//For checking that trans id exist or not for notes info
			$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$location_trans_id."'";
			$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check notes info: ".$sqlchkorlocation); 
			$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
			$countchkorlocation=mysqli_num_rows($reschkorlocation);
			
			//For update the location table for existing trans id for notes info
			if($countchkorlocation>0)
			{
				if(!in_array($location_trans_id,$notes_info_array_trans_id))
				{
					array_push($notes_info_array_trans_id,$location_trans_id);
				}
				$sqlupdateorlocation="UPDATE location SET emp_code='".$location_emp_code."',
										latt='".$location_latt."',
										longi='".$location_longi."'
										WHERE trans_id='".$location_trans_id."'";
				$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update notes info location: ".$sqlupdateorlocation);
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
	
			//For Insert into the location table for new trans id regarding market feedback stock audit
			$sqlinsertorlocation="INSERT INTO location SET emp_code='".$location_emp_code."',
								trans_id='".$location_trans_id."',
								latt='".$location_latt."',
								longi='".$location_longi."',
								date='".$location_date."',
								updatetime='".$location_date_updatetime."'"; 
			//For Insert into the mf stock audit Header table for new trans id
			$sqlinsertnotesinfo="INSERT INTO notes_info_details SET notes_info_id='".$notes_info_id."',
								  feedback 	='".$feedback."'";
					
		  if(mysqli_query($link,$sqlinsertorlocation) && mysqli_query($link,$sqlinsertnotesinfo))
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
 /* --------------------END QUERY FOR NOTES AND INFO--------------------------------------------------------------------------------------------------------*/
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
$url =APICALLLOGURL."/operationdb-notes-info.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
mysqli_close($link);
?>
