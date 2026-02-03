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
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><TECH_MEET_STATUS><meet_id><![CDATA[SUE000320210820130521]]></meet_id><meet_date><![CDATA[20/08/2021]]></meet_date><no_of_mason><![CDATA[20]]></no_of_mason><dealer_code><![CDATA[C/0000162]]></dealer_code><dealer_name><![CDATA[BHARATI ENT]]></dealer_name><mason_details><![CDATA[BABA;9991999999#WHWH;6666666666#HWHWHHW;4444444444#BWHWHG;5555555555#GAGWVWG;9999999999#]]></mason_details><meet_status><![CDATA[BABA;9991999999;Present#WHWH;6666666666;Present#HWHWHHW;4444444444;Present#BWHWHG;5555555555;Present#GAGWVWG;9999999999;Present]]></meet_status><status_update_date_time><![CDATA[2021-08-20 14:39:48]]></status_update_date_time><status_update_by><![CDATA[E0003]]></status_update_by><image><![CDATA[E000320210820143750.jpeg]]></image><remarks><![CDATA[test]]></remarks></TECH_MEET_STATUS></root>";*/

$meet_id="*ROOT*TECH_MEET_STATUS*MEET_ID";
$meet_date = "*ROOT*TECH_MEET_STATUS*MEET_DATE";
$no_of_mason = "*ROOT*TECH_MEET_STATUS*NO_OF_MASON";
$dealer_code = "*ROOT*TECH_MEET_STATUS*DEALER_CODE";
$dealer_name="*ROOT*TECH_MEET_STATUS*DEALER_NAME";
$mason_details="*ROOT*TECH_MEET_STATUS*MASON_DETAILS";
$meet_status = "*ROOT*TECH_MEET_STATUS*MEET_STATUS";
$status_update_date_time = "*ROOT*TECH_MEET_STATUS*STATUS_UPDATE_DATE_TIME";
$status_update_by = "*ROOT*TECH_MEET_STATUS*STATUS_UPDATE_BY";
$image = "*ROOT*TECH_MEET_STATUS*IMAGE";
$remarks = "*ROOT*TECH_MEET_STATUS*REMARKS";

$tech_meet_status_array=array();

$counter = 0;

class xml_tech_meet_status{
	var $meet_id,$meet_date,$no_of_mason,$dealer_code,$dealer_name,$mason_details,$meet_status,$status_update_date_time,$status_update_by,$image,$remarks;	
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
    global $current_tag,$counter,$tech_meet_status_array,$meet_id,$meet_date,$no_of_mason,$dealer_code,$dealer_name,$mason_details,$meet_status,$status_update_date_time,$status_update_by,$image,$remarks;	

//echo $current_tag.'<br />';
	//echo $data;
	if(substr($current_tag,0,22)=='*ROOT*TECH_MEET_STATUS')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $meet_id:
				$tech_meet_status_array[$counter] = new xml_tech_meet_status();
				$tech_meet_status_array[$counter]->meet_id = $data;
				break;
			case $meet_date:
				$tech_meet_status_array[$counter]->meet_date = $data;
				break;
			case $no_of_mason:
				$tech_meet_status_array[$counter]->no_of_mason = $data;
				break;
			case $dealer_code:
				$tech_meet_status_array[$counter]->dealer_code = $data;
				break;
			case $dealer_name:
				$tech_meet_status_array[$counter]->dealer_name = $data;
				break;
			case $mason_details:
				$tech_meet_status_array[$counter]->mason_details = $data;
				break;
			case $meet_status:
				$tech_meet_status_array[$counter]->meet_status = $data;
				break;
			case $status_update_date_time:
				$tech_meet_status_array[$counter]->status_update_date_time = $data;
				break;
			case $status_update_by:
				$tech_meet_status_array[$counter]->status_update_by = $data;
				break;
			case $image:
				$tech_meet_status_array[$counter]->image = $data;
				break;
			case $remarks:
				$tech_meet_status_array[$counter]->remarks = $data;
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
if(count($tech_meet_status_array)>0)
{
	for($x=0;$x<count($tech_meet_status_array);$x++){
		$meet_id=$tech_meet_status_array[$x]->meet_id;
		$meet_date=$tech_meet_status_array[$x]->meet_date;
		$no_of_mason=$tech_meet_status_array[$x]->no_of_mason;
		$dealer_code=$tech_meet_status_array[$x]->dealer_code;
		$dealer_name=$tech_meet_status_array[$x]->dealer_name;
		$mason_details=$tech_meet_status_array[$x]->mason_details;
		$meet_status=$tech_meet_status_array[$x]->meet_status;
		$status_update_date_time=$tech_meet_status_array[$x]->status_update_date_time;
		$status_update_by=$tech_meet_status_array[$x]->status_update_by;
		$image=$tech_meet_status_array[$x]->image;
		$remarks=$tech_meet_status_array[$x]->remarks;

			$sqlinserttechmeet="INSERT INTO tech_meet_status_details SET 
										   meet_id ='".$meet_id."',
										   meet_date ='".$meet_date."',
										   no_of_mason	='".$no_of_mason."',
										   dealer_code	='".$dealer_code."',
										   dealer_name	='".$dealer_name."',
										   mason_details ='".$mason_details."',
										   meet_status	='".$meet_status."',
										   status_update_date_time	='".$status_update_date_time."',
										   status_update_by     ='".$status_update_by."',
										   image         		='".$image."',
										   remarks         		='".$remarks."'";
			if(mysqli_query($link,$sqlinserttechmeet))
			{
				$flag=5;
			}
			else
			{
				mysqli_query($link,"ROLLBACK");
				echo $flag=0;
				return;	
			}
			
			$sqlupdateapprovedet="UPDATE survey_output SET 
								  is_approved 	='meet_completed'
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
