<?php
//error_reporting(E_ALL);
require("include/config.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

$emp_code=$_REQUEST['emp_code'];
$body=file_get_contents('php://input');
//$body=str_replace("'",'"',$body);

/*$body="<?xml version='1.0' encoding='UTF-8'?>
<root><route_plan><route_plan_details>
<route_plan_trans_id><![CDATA[RP10000316520131112132038]]></route_plan_trans_id><emp_code><![CDATA[100003165]]></emp_code>
<rds_code><![CDATA[4932]]></rds_code><route_code><![CDATA[FDR]]></route_code>
<visit_date><![CDATA[17-11-2013]]></visit_date><create_date><![CDATA[2013-11-12 13:30:13]]></create_date><type><![CDATA[add]]></type></route_plan_details>
</route_plan></root>";*/

$body="<?xml version='1.0' encoding='UTF-8'?><root><tour_expense><tour_expense_details><tour_exp_trans_id><![CDATA[TE100620131202110805]]></tour_exp_trans_id><emp_code><![CDATA[E0002]]></emp_code><start_destination><![CDATA[Sealdah]]></start_destination><end_destination><![CDATA[Dharmatala]]></end_destination><fare><![CDATA[500]]></fare><tour_date><![CDATA[2014-02-06 14:05:05]]></tour_date><transport_mode_sub_cat_id><![CDATA[11]]></transport_mode_sub_cat_id><supporting_attached><![CDATA[2014-02-06 14:05:05]]></supporting_attached></tour_expense_details></tour_expense></root>";

$tour_expense_trans_id = "*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*TOUR_EXPENSE_TRANS_ID";
$tour_expense_emp_code = "*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*EMP_CODE";
$tour_expense_start_destination ="*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*START_DESTINATION";
$tour_expense_end_destination ="*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*END_DESTINATION";
$tour_expense_fare ="*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*FARE";
$tour_expense_tour_date ="*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*TOUR_DATE";
$tour_expense_transport_mode_sub_cat_id ="*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*TRANSPORT_MODE_SUB_CAT_ID";
$tour_expense_supporting_attached ="*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*SUPPORTING_ATTACHED";

$tour_expense_array = array();
$tour_expense_trans_id_array=array();
$tour_expense_emp_code_array=array();
$counter = 0;

class xml_tour_expense{
	var $tour_expense_trans_id,$tour_expense_emp_code,$tour_expense_start_destination,$tour_expense_end_destination,$tour_expense_fare,$tour_expense_tour_date,
		$tour_expense_transport_mode_sub_cat_id,$tour_expense_supporting_attached;
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
    global $current_tag,$tour_expense_trans_id,$tour_expense_emp_code,$tour_expense_start_destination,$tour_expense_end_destination,$tour_expense_fare,$tour_expense_tour_date,
		$tour_expense_transport_mode_sub_cat_id,$tour_expense_supporting_attached,$tour_expense_array,$counter;
	if(substr($current_tag,0,18)=='*ROOT*TOUR_EXPENSE')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $tour_expense_trans_id:
				$tour_expense_array[$counter] = new xml_tour_expense();
				$tour_expense_array[$counter]->tour_expense_trans_id = $data;
				break;
			case $tour_expense_emp_code:
				$tour_expense_array[$counter]->tour_expense_emp_code = $data;
				break;
			case $tour_expense_start_destination:
				$tour_expense_array[$counter]->tour_expense_start_destination = $data;
				break;
			case $tour_expense_end_destination:
				$tour_expense_array[$counter]->tour_expense_end_destination = $data;
				break;
			case $tour_expense_fare:
				$tour_expense_array[$counter]->tour_expense_fare = $data;
				break;
			case $tour_expense_tour_date:
				$tour_expense_array[$counter]->tour_expense_tour_date = $data;
				break;
			case $tour_expense_transport_mode_sub_cat_id:
				$tour_expense_array[$counter]->tour_expense_transport_mode_sub_cat_id = $data;
				break;
			case $tour_expense_supporting_attached:
				$tour_expense_array[$counter]->tour_expense_supporting_attached = $data;
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
/* -------------------------------------------------------START QUERY FOR TOUR EXPENSE-----------------------------------------------------------------------*/
if(count($tour_expense_array)>0)
{
	//$count=1;
	for($x=0;$x<count($tour_expense_array);$x++){
		$tour_expense_trans_id=$tour_expense_array[$x]->tour_expense_trans_id;
		$tour_expense_emp_code=$tour_expense_array[$x]->tour_expense_emp_code;
		$tour_expense_start_destination=$tour_expense_array[$x]->tour_expense_start_destination;
		$tour_expense_end_destination=$tour_expense_array[$x]->tour_expense_end_destination;
		$tour_expense_fare=$tour_expense_array[$x]->tour_expense_fare;
		$tour_expense_tour_date=$tour_expense_array[$x]->tour_expense_tour_date;
		$tour_expense_transport_mode_sub_cat_id=$tour_expense_array[$x]->tour_expense_transport_mode_sub_cat_id;
		$tour_expense_supporting_attached=$tour_expense_array[$x]->tour_expense_supporting_attached;
	
		
		
		$date=gmdate('d',strtotime('+329 minute'));
		$month=gmdate('m',strtotime('+329 minute'));
		$year=gmdate('Y',strtotime('+329 minute'));
		$hour=gmdate('H',strtotime('+329 minute'));
		$minute=gmdate('i',strtotime('+329 minute'));
		$second=gmdate('s',strtotime('+329 minute'));
		$update_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
   }// End of for loop
   //print_r($route_plan_visit_date_event_array);	
	if($flag==5){}
}
 /* --------------------END QUERY FOR ROUTEPLAN------------------------------------------------------------------------------------------------------------*/
if($flag==6)
{
	echo 1;
}
else
{
	echo 0;
}
?>