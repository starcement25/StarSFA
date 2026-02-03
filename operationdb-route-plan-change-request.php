<?php
error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");
include '/home/acedns/public_html/php-calendar/classes/calendar.php';

$emp_code=$_REQUEST['emp_code'];

$body=file_get_contents('php://input');
$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
					 xml='".$body_xml."',
					insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);	
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><route_plan_change><route_plan_details><route_plan_trans_id><![CDATA[RPE002420220819214346]]></route_plan_trans_id><emp_code><![CDATA[E0024]]></emp_code><route_code><![CDATA[RT/1433]]></route_code><route_name><![CDATA[ALIPUR]]></route_name><visit_date><![CDATA[22-08-2022]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2022-08-19 21:52:32]]></create_date></route_plan_details></route_plan_change></root>";*/
$route_plan_trans_id = "*ROOT*ROUTE_PLAN_CHANGE*ROUTE_PLAN_DETAILS*ROUTE_PLAN_TRANS_ID";
$route_plan_emp_code = "*ROOT*ROUTE_PLAN_CHANGE*ROUTE_PLAN_DETAILS*EMP_CODE";
$route_plan_route_code ="*ROOT*ROUTE_PLAN_CHANGE*ROUTE_PLAN_DETAILS*ROUTE_CODE";
$route_plan_route_name ="*ROOT*ROUTE_PLAN_CHANGE*ROUTE_PLAN_DETAILS*ROUTE_NAME";
$route_plan_visit_date ="*ROOT*ROUTE_PLAN_CHANGE*ROUTE_PLAN_DETAILS*VISIT_DATE";
$route_plan_remarks ="*ROOT*ROUTE_PLAN_CHANGE*ROUTE_PLAN_DETAILS*REMARKS";
$route_plan_create_date ="*ROOT*ROUTE_PLAN_CHANGE*ROUTE_PLAN_DETAILS*CREATE_DATE";

$route_plan_array = array();
$route_plan_trans_id_array=array();
$route_plan_emp_code_array=array();
$route_plan_visit_date_array=array();
$route_plan_create_date_array=array();
$route_plan_visit_date_event_array=array();
$route_plan_del_emp_code_array=array();
$route_plan_del_visit_date_array=array();
$route_plan_route_code_array=array();
$counter = 0;

class xml_route_plan{
	var $route_plan_trans_id,$route_plan_emp_code,$route_plan_route_code,$route_plan_route_name,$route_plan_visit_date,$route_plan_remarks,$route_plan_create_date;
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
    global $current_tag,$route_plan_trans_id,$route_plan_emp_code,$route_plan_route_code,$route_plan_route_name,$route_plan_visit_date,$route_plan_remarks,
			$route_plan_create_date,$route_plan_array,$counter;
	if(substr($current_tag,0,16)=='*ROOT*ROUTE_PLAN')
	{
		/*echo $current_tag.'<br />';
		echo $data.'<br />';*/
		switch($current_tag){
			case $route_plan_trans_id:
				$route_plan_array[$counter] = new xml_route_plan();
				$route_plan_array[$counter]->route_plan_trans_id = $data;
				break;
			case $route_plan_emp_code:
				$route_plan_array[$counter]->route_plan_emp_code = $data;
				break;
			case $route_plan_route_code:
				$route_plan_array[$counter]->route_plan_route_code = $data;
				break;
			case $route_plan_route_name:
				$route_plan_array[$counter]->route_plan_route_name = $data;
				break;	
			case $route_plan_visit_date:
				$route_plan_array[$counter]->route_plan_visit_date = $data;
				break;
			case $route_plan_remarks:
				$route_plan_array[$counter]->route_plan_remarks = $data;
				break;	
			case $route_plan_create_date:
				$route_plan_array[$counter]->route_plan_create_date = $data;
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
//print_r($attendance_array);
//print_r($audit_array);
//print_r($stock_audit_array);
//echo count($route_plan_array);
mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");
$flag=1;
/* -------------------------------------------------------START QUERY FOR ROUTEPLAN CHANGE REQUEST-----------------------------------------------------------------------*/
if(count($route_plan_array)>0)
{
	//$count=1;
	$blank_val='  ';
	for($x=0;$x<count($route_plan_array);$x++){
		$route_plan_trans_id=$route_plan_array[$x]->route_plan_trans_id;
		$route_plan_emp_code=$route_plan_array[$x]->route_plan_emp_code;
		$route_plan_route_code=$route_plan_array[$x]->route_plan_route_code;
		$route_plan_route_name=$route_plan_array[$x]->route_plan_route_name;
		$route_plan_visit_date=$route_plan_array[$x]->route_plan_visit_date;
		$route_plan_remarks=$route_plan_array[$x]->route_plan_remarks;
		$route_plan_create_date=$route_plan_array[$x]->route_plan_create_date;
		$route_plan_visit_date_database=date('Y-m-d',strtotime($route_plan_visit_date));
		
		$routeplanemp_visitdate_merge=$route_plan_emp_code.$route_plan_visit_date_database.$route_plan_working_with;
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$update_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
				
		//For checking that trans id exist or not		
		$sqlselectrouteplan="SELECT route_plan_trans_id,route_code FROM route_plan 
							  WHERE emp_code ='".$route_plan_emp_code."' AND visit_date	='".$route_plan_visit_date_database."' and is_approved='no'";
		$resselectrouteplan = mysqli_query($link,$sqlselectrouteplan) or die(mysqli_error()." Error in chk trans id for route plan: ".$sqlselectrouteplan); 
		$countselectrouteplan=mysqli_num_rows($resselectrouteplan);
		if($countselectrouteplan<1)
		{
			if($route_plan_emp_code==$emp_code){
			$sqlinsertrouteplan="INSERT INTO route_plan SET route_plan_trans_id ='".$route_plan_trans_id."',
								  emp_code 			='".$route_plan_emp_code."',
								  route_code 		='".$route_plan_route_code."',
								  visit_date 		='".$route_plan_visit_date_database."',
								  remarks			='".addslashes($route_plan_remarks)."',
								  distributor_code	='',
								  status			='',
								  create_date 		='".$route_plan_create_date."',
								  update_date		='".$update_date."',
								  change_request	='yes',
								  working_with		=''";
			if(mysqli_query($link,$sqlinsertrouteplan))
			{
				$flag=5;
				if(!in_array($routeplanemp_visitdate_merge,$route_plan_del_emp_code_array))
				{
					array_push($route_plan_del_emp_code_array,$routeplanemp_visitdate_merge);
					//array_push($route_plan_del_visit_date_array,$route_plan_visit_date_database);
				}
			}
			else
			{
				mysqli_query($link,"ROLLBACK");
				echo $flag=0;
				return;
			}
			}
		}
		else
		{
			if($route_plan_emp_code==$emp_code){
				//For deleting the previous values of same visit date
				if(!in_array($routeplanemp_visitdate_merge,$route_plan_del_emp_code_array))
				{
					$sqldeleterouteplan="DELETE FROM route_plan WHERE  emp_code ='".$route_plan_emp_code."' AND 
										visit_date	='".$route_plan_visit_date_database."' AND is_approved='no_'";
					if(mysqli_query($link,$sqldeleterouteplan)){
						array_push($route_plan_del_emp_code_array,$routeplanemp_visitdate_merge);
						//array_push($route_plan_del_visit_date_array,$route_plan_visit_date_database);
					}
				}
				$sqlinsertrouteplan="INSERT INTO route_plan SET route_plan_trans_id ='".$route_plan_trans_id."',
									  emp_code 			='".$route_plan_emp_code."',
									  route_code 		='".$route_plan_route_code."',
									  visit_date 		='".$route_plan_visit_date_database."',
									  create_date 		='".$route_plan_create_date."',
									  remarks			='".addslashes($route_plan_remarks)."',
									  status			='',
									  distributor_code	='',
									  update_date		='".$update_date."',
									  change_request	='yes',
									  working_with		=''";
				if(mysqli_query($link,$sqlinsertrouteplan))
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
   }// End of for loop
   //print_r($route_plan_visit_date_event_array);
   
	if($flag==5){
		mysqli_query($link,"COMMIT");
		$flag=6;
	}
}
 /* --------------------END QUERY FOR ROUTEPLAN CHANGE REQUEST------------------------------------------------------------------------------------------------------------*/
if($flag==6)
{
	echo 1;
}
else
{
	echo 0;
}
mysqli_close($link);
?>