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
$last_update_time=$_REQUEST['last_update_time'] ??'';
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

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><JOINT_WORK><location><emp_code><![CDATA[E0096]]></emp_code><trans_id><![CDATA[JWE009620160418160521]]></trans_id><latt><![CDATA[22.5643652]]></latt><longi><![CDATA[88.3568814]]></longi><date><![CDATA[2016-04-18 16:05:21]]></date></location>
<JOINT_WORK_DATA><JOINT_WORK_ID><![CDATA[JWE009620160418160521]]></JOINT_WORK_ID><CUSOMER_CODE><![CDATA[]]></CUSOMER_CODE><EMP_CODE><![CDATA[]]></EMP_CODE><OBSERVATION_ON_CUSTOMER><![CDATA[]]></OBSERVATION_ON_CUSTOMER><OBSERVATION_ON_EMP><![CDATA[]]></OBSERVATION_ON_EMP></JOINT_WORK_DATA></JOINT_WORK></ROOT>";*/
$body_xml=str_replace("'",'"',$body);

$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
						xml='".$body_xml."',
						insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);
	
$location_emp_code="*ROOT*JOINT_WORK*LOCATION*EMP_CODE";
$location_trans_id = "*ROOT*JOINT_WORK*LOCATION*TRANS_ID";
$location_latt = "*ROOT*JOINT_WORK*LOCATION*LATT";
$location_longi = "*ROOT*JOINT_WORK*LOCATION*LONGI";
$location_date="*ROOT*JOINT_WORK*LOCATION*DATE";

$joint_work_id = "*ROOT*JOINT_WORK*JOINT_WORK_DATA*JOINT_WORK_ID";
$customer_code="*ROOT*JOINT_WORK*JOINT_WORK_DATA*CUSTOMER_CODE";
$route_code="*ROOT*JOINT_WORK*JOINT_WORK_DATA*ROUTE_CODE";
$emp_code="*ROOT*JOINT_WORK*JOINT_WORK_DATA*EMP_CODE";
$observation_on_customer="*ROOT*JOINT_WORK*JOINT_WORK_DATA*OBSERVATION_ON_CUSTOMER";
$observation_on_emp="*ROOT*JOINT_WORK*JOINT_WORK_DATA*OBSERVATION_ON_EMP";

$joint_work_array=array();
$counter = 0;
class xml_joint_work{
	var $location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$joint_work_id,$customer_code,$route_code,$emp_code,$observation_on_customer,$observation_on_emp;	
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
    global $current_tag,$counter,$location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$joint_work_id,$customer_code,
	$route_code,$joint_work_array,$emp_code,$observation_on_customer,$observation_on_emp;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,16)=='*ROOT*JOINT_WORK')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $location_emp_code:
				$joint_work_array[$counter] = new xml_joint_work();
				$joint_work_array[$counter]->location_emp_code = $data;
				break;
			case $location_trans_id:
				$joint_work_array[$counter]->location_trans_id = $data;
				break;
			case $location_latt:
				$joint_work_array[$counter]->location_latt = $data;
				break;
			case $location_longi:
				$joint_work_array[$counter]->location_longi = $data;
				break;
			case $location_date:
				$joint_work_array[$counter]->location_date = $data;
				break;
			case $joint_work_id:
				$joint_work_array[$counter]->joint_work_id = $data;
				break;
			case $customer_code:
				$joint_work_array[$counter]->customer_code = $data;
				break;
			case $route_code:
				$joint_work_array[$counter]->route_code = $data;
				break;	
			case $emp_code:
				$joint_work_array[$counter]->emp_code = $data;
				break;
			case $observation_on_customer:
				$joint_work_array[$counter]->observation_on_customer = $data;
				break;
			case $observation_on_emp:
				$joint_work_array[$counter]->observation_on_emp = $data;
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
/* --------------------START QUERY FOR JOINT WORK ------------------------------------------------------------------------------------------*/
//print_r($notes_info_array);
$joint_work_array_trans_id=array();
if(count($joint_work_array)>0)
{
		for($x=0;$x<count($joint_work_array);$x++){

			$location_emp_code=$joint_work_array[$x]->location_emp_code;
			$location_trans_id=$joint_work_array[$x]->location_trans_id;
			$location_latt=$joint_work_array[$x]->location_latt;
			$location_longi=$joint_work_array[$x]->location_longi;
			$location_date=$joint_work_array[$x]->location_date;
			$joint_work_id=$joint_work_array[$x]->joint_work_id;
			$customer_code=$joint_work_array[$x]->customer_code;
			$route_code=$joint_work_array[$x]->route_code;
			$emp_code=$joint_work_array[$x]->emp_code;
			$observation_on_customer=$joint_work_array[$x]->observation_on_customer;
			$observation_on_emp=$joint_work_array[$x]->observation_on_emp;
			
			//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
			if($location_latt==""){
			    $location_latt="0";
			    $location_longi="0";
			}
			if($location_latt>0 && $location_longi>0)
			{
				$sqlupdatelatlongzero="UPDATE location SET latt='".$location_latt."',longi='".$location_longi."' WHERE 
										emp_code='".$location_emp_code."' AND latt='0' AND longi='0'";
				$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
			}
	
			//For checking that trans id exist or not for joint work
			$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$location_trans_id."'";
			$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check notes info: ".$sqlchkorlocation); 
			$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
			$countchkorlocation=mysqli_num_rows($reschkorlocation);
			
			//For update the location table for existing trans id for joint work
			if($countchkorlocation>0)
			{
				if(!in_array($location_trans_id,$joint_work_array_trans_id))
				{
					array_push($joint_work_array_trans_id,$location_trans_id);
				}
				if($location_latt==""){
			    $location_latt="0";
			    $location_longi="0";
			    }
				$sqlupdateorlocation="UPDATE location SET emp_code='".$location_emp_code."',
										latt='".$location_latt."',
										longi='".$location_longi."'
										WHERE trans_id='".$location_trans_id."'";
										//echo $sqlupdateorlocation;
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
	
			//For Insert into the location table for new trans id regarding joint work
			if($location_latt==""){
			    $location_latt="0";
			    $location_longi="0";
			}
			
			$sqlinsertorlocation="INSERT INTO location SET emp_code='".$location_emp_code."',
								trans_id='".$location_trans_id."',
								latt='".$location_latt."',
								longi='".$location_longi."',
								date='".$location_date."',
								address='',
								updatetime='".$location_date_updatetime."'"; 
			//For Insert into the joint work observation table for new trans id
			//echo $sqlinsertorlocation;
			$sqlchkjointwork="SELECT joint_work_id FROM joint_work_observation WHERE joint_work_id='".$joint_work_id."'";
			$rschkjointwork=mysqli_query($link,$sqlchkjointwork);
			$countchkjointwork=mysqli_num_rows($rschkjointwork);
			
			$sqlinsertjointwork="INSERT INTO  joint_work_observation SET joint_work_id='".$joint_work_id."',
								  customer_code 	='".$customer_code."',
								  route_code		='".$route_code."',
								   emp_code 	='".$emp_code."',
								  	observation_on_customer 	='".addslashes($observation_on_customer)."',
								  	observation_on_emp='".addslashes($observation_on_emp)."' ";
					
		  if($countchkjointwork==0)
			{		
			  if(mysqli_query($link,$sqlinsertjointwork))
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
		}
	 }
}
 /* --------------------END QUERY FOR NOTES AND INFO--------------------------------------------------------------------------------------------------------*/
 $last_operation_datetime=date("Y-m-d H:i:s");
 
 
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
$url =APICALLLOGURL."/operationdb-joint-work-observation.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
mysqli_close($link);
?>
