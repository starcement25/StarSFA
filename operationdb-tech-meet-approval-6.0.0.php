<?php
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

$emp_code=$_REQUEST['emp_code'];

$body=file_get_contents('php://input');
$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
					 xml='".$body_xml."',
					insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);	

//echo "RESPONSE OF $nick_name is --------------------------\n".$body;
//exit();
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><TECH_MEET_APPROVAL><meet_id><![CDATA[SUE000520210814113143]]></meet_id><meet_date><![CDATA[18/08/2021]]></meet_date><no_of_mason><![CDATA[12]]></no_of_mason><dealer_code><![CDATA[C/0000288]]></dealer_code><dealer_name><![CDATA[NEW G.M HARDWARE]]></dealer_name><is_approved><![CDATA[yes]]></is_approved><approved_date_time><![CDATA[17/08/2021 19:28:46]]></approved_date_time><approved_by><![CDATA[E0001]]></approved_by></TECH_MEET_APPROVAL></root>";*/

$meet_id="*ROOT*TECH_MEET_APPROVAL*MEET_ID";
$meet_date = "*ROOT*TECH_MEET_APPROVAL*MEET_DATE";
$no_of_mason = "*ROOT*TECH_MEET_APPROVAL*NO_OF_MASON";
$dealer_code = "*ROOT*TECH_MEET_APPROVAL*DEALER_CODE";
$dealer_name="*ROOT*TECH_MEET_APPROVAL*DEALER_NAME";
$is_approved = "*ROOT*TECH_MEET_APPROVAL*IS_APPROVED";
$approved_date_time = "*ROOT*TECH_MEET_APPROVAL*APPROVED_DATE_TIME";
$approved_by = "*ROOT*TECH_MEET_APPROVAL*APPROVED_BY";

$tech_meet_approval_array=array();

$counter = 0;

class xml_tech_meet_approval{
	var $meet_id,$meet_date,$no_of_mason,$dealer_code,$dealer_name,$is_approved,$approved_date_time,$approved_by;	
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
    global $current_tag,$counter,$tech_meet_approval_array,$meet_id,$meet_date,$no_of_mason,$dealer_code,$dealer_name,$is_approved,$approved_date_time,$approved_by;	

//echo $current_tag.'<br />';
	//echo $data;
	if(substr($current_tag,0,24)=='*ROOT*TECH_MEET_APPROVAL')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $meet_id:
				$tech_meet_approval_array[$counter] = new xml_tech_meet_approval();
				$tech_meet_approval_array[$counter]->meet_id = $data;
				break;
			case $meet_date:
				$tech_meet_approval_array[$counter]->meet_date = $data;
				break;
			case $no_of_mason:
				$tech_meet_approval_array[$counter]->no_of_mason = $data;
				break;
			case $dealer_code:
				$tech_meet_approval_array[$counter]->dealer_code = $data;
				break;
			case $dealer_name:
				$tech_meet_approval_array[$counter]->dealer_name = $data;
				break;
			case $is_approved:
				$tech_meet_approval_array[$counter]->is_approved = $data;
				break;
			case $approved_date_time:
				$tech_meet_approval_array[$counter]->approved_date_time = $data;
				break;
			case $approved_by:
				$tech_meet_approval_array[$counter]->approved_by = $data;
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
mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");
$flag=1;
/* -----------------------------------------------START QUERY FOR Approval--------------------------------------------------------------------------*/
if(count($tech_meet_approval_array)>0)
{
	for($x=0;$x<count($tech_meet_approval_array);$x++){
		$meet_id=$tech_meet_approval_array[$x]->meet_id;
		$meet_date=$tech_meet_approval_array[$x]->meet_date;
		$no_of_mason=$tech_meet_approval_array[$x]->no_of_mason;
		$dealer_code=$tech_meet_approval_array[$x]->dealer_code;
		$dealer_name=$tech_meet_approval_array[$x]->dealer_name;
		$is_approved=$tech_meet_approval_array[$x]->is_approved;
		$approved_date_time=$tech_meet_approval_array[$x]->approved_date_time;
		$approved_by=$tech_meet_approval_array[$x]->approved_by;

			$sqlupdateapprovedet="UPDATE survey_output SET 
								  is_approved 					='".$is_approved."',
								  approved_date_time 			='".$approved_date_time."',
								  approved_by 					='".$approved_by."'
								  WHERE survey_id='".$meet_id."'";											  
			if(mysqli_query($link,$sqlupdateapprovedet))
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
}//End of att checkout if

/* -----------------End for approval-------------------------------------------------------------------------*/
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
mysqli_close($link);
?>
