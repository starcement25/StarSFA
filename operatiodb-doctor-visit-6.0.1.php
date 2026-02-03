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

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><DOCTOR_VISIT><location><emp_code><![CDATA[E0001]]></emp_code><trans_id><![CDATA[DRE000120211201163945]]></trans_id><latt><![CDATA[23.1212068]]></latt><longi><![CDATA[88.8600938]]></longi><date><![CDATA[2021-12-01 16:39:45]]></date></location><DOCTOR_VISIT_DATA><TRANS_ID><![CDATA[DRE000120211201163945]]></TRANS_ID><CUSOMER_CODE><![CDATA[C/0000899]]></CUSOMER_CODE><REMARKS><![CDATA[text remarks]]></REMARKS><OTHER_REMARKS><![CDATA[Interested]]></OTHER_REMARKS></DOCTOR_VISIT_DATA></DOCTOR_VISIT><DOCTOR_VISIT><location><emp_code><![CDATA[E0001]]></emp_code><trans_id><![CDATA[DRE000120211201174218]]></trans_id><latt><![CDATA[23.1211347]]></latt><longi><![CDATA[88.8598891]]></longi><date><![CDATA[2021-12-01 17:42:18]]></date></location><DOCTOR_VISIT_DATA><TRANS_ID><![CDATA[DRE000120211201174218]]></TRANS_ID><CUSOMER_CODE><![CDATA[C/0000898]]></CUSOMER_CODE><REMARKS><![CDATA[text remarks s]]></REMARKS><OTHER_REMARKS><![CDATA[visit next week]]></OTHER_REMARKS></DOCTOR_VISIT_DATA></DOCTOR_VISIT><DOCTOR_VISIT><location><emp_code><![CDATA[E0001]]></emp_code><trans_id><![CDATA[DRE000120211201174717]]></trans_id><latt><![CDATA[23.1216225]]></latt><longi><![CDATA[88.8585507]]></longi><date><![CDATA[2021-12-01 17:47:17]]></date></location><DOCTOR_VISIT_DATA><TRANS_ID><![CDATA[DRE000120211201174717]]></TRANS_ID><CUSOMER_CODE><![CDATA[C/0000896]]></CUSOMER_CODE><REMARKS><![CDATA[t]]></REMARKS><OTHER_REMARKS><![CDATA[Not intrested]]></OTHER_REMARKS></DOCTOR_VISIT_DATA></DOCTOR_VISIT><DOCTOR_VISIT><location><emp_code><![CDATA[E0001]]></emp_code><trans_id><![CDATA[DRE000120211201181217]]></trans_id><latt><![CDATA[23.1213469]]></latt><longi><![CDATA[88.8593387]]></longi><date><![CDATA[2021-12-01 18:12:17]]></date></location><DOCTOR_VISIT_DATA><TRANS_ID><![CDATA[DRE000120211201181217]]></TRANS_ID><CUSOMER_CODE><![CDATA[C/0000893]]></CUSOMER_CODE><REMARKS><![CDATA[test]]></REMARKS><OTHER_REMARKS><![CDATA[visit next week]]></OTHER_REMARKS></DOCTOR_VISIT_DATA></DOCTOR_VISIT><DOCTOR_VISIT><location><emp_code><![CDATA[E0001]]></emp_code><trans_id><![CDATA[DRE000120211201181535]]></trans_id><latt><![CDATA[23.1213469]]></latt><longi><![CDATA[88.8593387]]></longi><date><![CDATA[2021-12-01 18:15:35]]></date></location><DOCTOR_VISIT_DATA><TRANS_ID><![CDATA[DRE000120211201181535]]></TRANS_ID><CUSOMER_CODE><![CDATA[C/0000904]]></CUSOMER_CODE><REMARKS><![CDATA[test 5]]></REMARKS><OTHER_REMARKS><![CDATA[Interested]]></OTHER_REMARKS></DOCTOR_VISIT_DATA></DOCTOR_VISIT><DOCTOR_VISIT><location><emp_code><![CDATA[E0001]]></emp_code><trans_id><![CDATA[DRE000120211201182047]]></trans_id><latt><![CDATA[23.1213469]]></latt><longi><![CDATA[88.8593387]]></longi><date><![CDATA[2021-12-01 18:20:47]]></date></location><DOCTOR_VISIT_DATA><TRANS_ID><![CDATA[DRE000120211201182047]]></TRANS_ID><CUSOMER_CODE><![CDATA[C/0000889]]></CUSOMER_CODE><REMARKS><![CDATA[test]]></REMARKS><OTHER_REMARKS><![CDATA[Interested]]></OTHER_REMARKS></DOCTOR_VISIT_DATA></DOCTOR_VISIT><DOCTOR_VISIT><location><emp_code><![CDATA[E0001]]></emp_code><trans_id><![CDATA[DRE000120211201182914]]></trans_id><latt><![CDATA[23.1213469]]></latt><longi><![CDATA[88.8593387]]></longi><date><![CDATA[2021-12-01 18:29:14]]></date></location><DOCTOR_VISIT_DATA><TRANS_ID><![CDATA[DRE000120211201182914]]></TRANS_ID><CUSOMER_CODE><![CDATA[C/0000893]]></CUSOMER_CODE><REMARKS><![CDATA[test]]></REMARKS><OTHER_REMARKS><![CDATA[Interested]]></OTHER_REMARKS></DOCTOR_VISIT_DATA></DOCTOR_VISIT></root>";*/
$body_xml=str_replace("'",'"',$body);

$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
						xml='".$body_xml."',
						insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);
	
$location_emp_code="*ROOT*DOCTOR_VISIT*LOCATION*EMP_CODE";
$location_trans_id = "*ROOT*DOCTOR_VISIT*LOCATION*TRANS_ID";
$location_latt = "*ROOT*DOCTOR_VISIT*LOCATION*LATT";
$location_longi = "*ROOT*DOCTOR_VISIT*LOCATION*LONGI";
$location_date="*ROOT*DOCTOR_VISIT*LOCATION*DATE";

$trans_id = "*ROOT*DOCTOR_VISIT*DOCTOR_VISIT_DATA*TRANS_ID";
$customer_code="*ROOT*DOCTOR_VISIT*DOCTOR_VISIT_DATA*CUSOMER_CODE";
$remarks="*ROOT*DOCTOR_VISIT*DOCTOR_VISIT_DATA*REMARKS";
$other_remarks="*ROOT*DOCTOR_VISIT*DOCTOR_VISIT_DATA*OTHER_REMARKS";
$met_with="*ROOT*DOCTOR_VISIT*DOCTOR_VISIT_DATA*MET_WITH";

$doctor_visit_array=array();
$counter = 0;
class xml_doctor_visit{
	var $location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$trans_id,$customer_code,$remarks,$other_remarks,$met_with;	
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
    global $current_tag,$counter,$doctor_visit_array,$location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$trans_id,$customer_code,$remarks,$other_remarks,$met_with;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,18)=='*ROOT*DOCTOR_VISIT')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $location_emp_code:
				$doctor_visit_array[$counter] = new xml_doctor_visit();
				$doctor_visit_array[$counter]->location_emp_code = $data;
				break;
			case $location_trans_id:
				$doctor_visit_array[$counter]->location_trans_id = $data;
				break;
			case $location_latt:
				$doctor_visit_array[$counter]->location_latt = $data;
				break;
			case $location_longi:
				$doctor_visit_array[$counter]->location_longi = $data;
				break;
			case $location_date:
				$doctor_visit_array[$counter]->location_date = $data;
				break;
			case $trans_id:
				$doctor_visit_array[$counter]->trans_id = $data;
				break;
			case $customer_code:
				$doctor_visit_array[$counter]->customer_code = $data;
				break;
			case $remarks:
				$doctor_visit_array[$counter]->remarks = $data;
				break;	
			case $other_remarks:
				$doctor_visit_array[$counter]->other_remarks = $data;
				break;
			case $met_with:
				$doctor_visit_array[$counter]->met_with = $data;
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
/* --------------------START QUERY FOR DOCTOR VISIT ------------------------------------------------------------------------------------------*/
//print_r($notes_info_array);
$doctor_visit_array_trans_id=array();
if(count($doctor_visit_array)>0)
{
		for($x=0;$x<count($doctor_visit_array);$x++){

			$location_emp_code=$doctor_visit_array[$x]->location_emp_code;
			$location_trans_id=$doctor_visit_array[$x]->location_trans_id;
			$location_latt=$doctor_visit_array[$x]->location_latt;
			$location_longi=$doctor_visit_array[$x]->location_longi;
			$location_date=$doctor_visit_array[$x]->location_date;
			$trans_id=$doctor_visit_array[$x]->trans_id;
			$customer_code=$doctor_visit_array[$x]->customer_code;
			$remarks=$doctor_visit_array[$x]->remarks;
			$other_remarks=$doctor_visit_array[$x]->other_remarks;
			$met_with=$doctor_visit_array[$x]->met_with;
			
			//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
			/*if($location_latt>0 && $location_longi>0)
			{
				$sqlupdatelatlongzero="UPDATE location SET latt='".$location_latt."',longi='".$location_longi."' WHERE 
										emp_code='".$location_emp_code."' AND latt='0' AND longi='0'";
				$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
			}*/
	
			//For checking that trans id exist or not for doctor visit
			$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$location_trans_id."'";
			$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check notes info: ".$sqlchkorlocation); 
			$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
			$countchkorlocation=mysqli_num_rows($reschkorlocation);
			
			//For update the location table for existing trans id for joint work
			if($countchkorlocation>0)
			{
				/*if(!in_array($location_trans_id,$joint_work_array_trans_id))
				{
					array_push($joint_work_array_trans_id,$location_trans_id);
				}*/
				if($location_latt==""){
				    $location_latt="0";
				    $location_longi="0";
				}
				$sqlupdateorlocation="UPDATE location SET emp_code='".$location_emp_code."',
										latt='".$location_latt."',
										longi='".$location_longi."'
										WHERE trans_id='".$location_trans_id."'";
				$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update joint work location: ".$sqlupdateorlocation);
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
			
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
	
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			$location_date_updatetime=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
	
			//For Insert into the location table for new trans id regarding doctor visit
			if($location_latt==""){
				    $location_latt="0";
				    $location_longi="0";
				}
			$sqlinsertorlocation="INSERT INTO location SET emp_code='".$location_emp_code."',
								trans_id='".$location_trans_id."',
								latt='".$location_latt."',
								longi='".$location_longi."',
								date='".$location_date."',
								updatetime='".$location_date_updatetime."'"; 
			//For Insert into the doctor visit table for new trans id
			$sqlinsertdoctorvisit="INSERT INTO  doctor_visit_details SET trans_id='".$trans_id."',
								  customer_code 	='".$customer_code."',
								  remarks			='".addslashes($remarks)."',
								  other_remarks 	='".addslashes($other_remarks)."',
								  met_with 			='".$met_with."'";
		  if(mysqli_query($link,$sqlinsertorlocation) && mysqli_query($link,$sqlinsertdoctorvisit))
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
 /* --------------------END QUERY FOR DOCTOR VISIT --------------------------------------------------------------------------------------------------------*/
if($flag==5)
{
	 mysqli_query($link,"COMMIT");
	 echo $flag=1;
}
if($flag==6)
{
	 mysqli_query($link,"COMMIT");
	 echo $flag=1;
}
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url =APICALLLOGURL."/operatiodb-doctor-visit-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code";
insertapilog($datetime,$emp_code,$url,$nick_name);
mysqli_close($link);
?>
