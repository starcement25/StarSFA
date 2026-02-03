<?php
/* ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);*/
 
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
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><att_checkout_info><attendance_id><![CDATA[AE000220210605105658]]></attendance_id><checkout_id><![CDATA[CHE000220210605185158]]></checkout_id><emp_code><![CDATA[E0002]]></emp_code><create_date><![CDATA[2021-06-05 10:56:58]]></create_date><att_starting_km><![CDATA[58]]></att_starting_km><att_odometer><![CDATA[.jpeg]]></att_odometer><att_necessary_items><![CDATA[Mask,sanitizer]]></att_necessary_items><att_activities><![CDATA[]]></att_activities><checkout_ending_km><![CDATA[159]]></checkout_ending_km><checkout_odometer><![CDATA[.jpeg]]></checkout_odometer><vehicle_type><![CDATA[four-wheleer]]></vehicle_type></att_checkout_info></root>";*/
$attendance_id="*ROOT*ATT_CHECKOUT_INFO*ATTENDANCE_ID";
$checkout_id = "*ROOT*ATT_CHECKOUT_INFO*CHECKOUT_ID";
$emp_code = "*ROOT*ATT_CHECKOUT_INFO*EMP_CODE";
$create_date = "*ROOT*ATT_CHECKOUT_INFO*CREATE_DATE";
$att_starting_km="*ROOT*ATT_CHECKOUT_INFO*ATT_STARTING_KM";
$att_odometer = "*ROOT*ATT_CHECKOUT_INFO*ATT_ODOMETER";
$att_necessary_items = "*ROOT*ATT_CHECKOUT_INFO*ATT_NECESSARY_ITEMS";
$att_activities = "*ROOT*ATT_CHECKOUT_INFO*ATT_ACTIVITIES";
$checkout_ending_km = "*ROOT*ATT_CHECKOUT_INFO*CHECKOUT_ENDING_KM";
$checkout_odometer = "*ROOT*ATT_CHECKOUT_INFO*CHECKOUT_ODOMETER";
$att_vehicle_type = "*ROOT*ATT_CHECKOUT_INFO*VEHICLE_TYPE";

$att_checkout_info_array=array();

$counter = 0;

class xml_att_checkout{
	var $attendance_id,$checkout_id,$emp_code,$create_date,$att_starting_km,$att_odometer,$att_necessary_items,$att_activities,$checkout_ending_km,$checkout_odometer,$att_vehicle_type;	
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
    global $current_tag,$counter,$att_checkout_info_array,$attendance_id,$checkout_id,$emp_code,$create_date,$att_starting_km,$att_odometer,$att_necessary_items,$att_activities,$checkout_ending_km,$checkout_odometer,$att_vehicle_type;	

//echo $current_tag.'<br />';
	//echo $data;
	if(substr($current_tag,0,23)=='*ROOT*ATT_CHECKOUT_INFO')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $attendance_id:
				$att_checkout_info_array[$counter] = new xml_att_checkout();
				$att_checkout_info_array[$counter]->attendance_id = $data;
				break;
			case $checkout_id:
				$att_checkout_info_array[$counter]->checkout_id = $data;
				break;
			case $emp_code:
				$att_checkout_info_array[$counter]->emp_code = $data;
				break;
			case $create_date:
				$att_checkout_info_array[$counter]->create_date = $data;
				break;
			case $att_starting_km:
				$att_checkout_info_array[$counter]->att_starting_km = $data;
				break;
			case $att_odometer:
				$att_checkout_info_array[$counter]->att_odometer = $data;
				break;
			case $att_necessary_items:
				$att_checkout_info_array[$counter]->att_necessary_items = $data;
				break;
			case $att_activities:
				$att_checkout_info_array[$counter]->att_activities = $data;
				break;
			case $checkout_ending_km:
				$att_checkout_info_array[$counter]->checkout_ending_km = $data;
				break;
			case $checkout_odometer:
				$att_checkout_info_array[$counter]->checkout_odometer = $data;
				break;
			case $att_vehicle_type:
				$att_checkout_info_array[$counter]->att_vehicle_type = $data;
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
/* -----------------------------------------------START QUERY FOR ATT checkout--------------------------------------------------------------------------*/
if(count($att_checkout_info_array)>0)
{
	for($x=0;$x<count($att_checkout_info_array);$x++){
		$attendance_id=$att_checkout_info_array[$x]->attendance_id;
		$checkout_id=$att_checkout_info_array[$x]->checkout_id;
		$emp_code=$att_checkout_info_array[$x]->emp_code;
		$create_date=$att_checkout_info_array[$x]->create_date;
		$att_starting_km=$att_checkout_info_array[$x]->att_starting_km;
		$att_odometer=$att_checkout_info_array[$x]->att_odometer;
		$att_necessary_items=$att_checkout_info_array[$x]->att_necessary_items;
		$att_activities=$att_checkout_info_array[$x]->att_activities;
		$checkout_ending_km=$att_checkout_info_array[$x]->checkout_ending_km;
		$checkout_odometer=$att_checkout_info_array[$x]->checkout_odometer;
		$att_vehicle_type=$att_checkout_info_array[$x]->att_vehicle_type;

		if($attendance_id!='')
		{
		$sqlchkattcheckout="SELECT attendance_id FROM att_checkout_journey_info WHERE attendance_id='".$attendance_id."'";
		$reschkattcheckout = mysqli_query($link,$sqlchkattcheckout) or die(mysqli_error()." Error in check Att checkout: ".$sqlchkattcheckout); 
		$countchkattcheckout=mysqli_num_rows($reschkattcheckout);
		//For update the location table for existing trans id for mt
			if($countchkattcheckout==0)
			{
			    if($checkout_ending_km==""){
			        $checkout_ending_km = 0;
			    }
			    if($att_starting_km==""){
			        $att_starting_km = 0;
			    }
			$sqlinsertattcheckout="INSERT INTO att_checkout_journey_info SET attendance_id ='".$attendance_id."',
									  checkout_id 					='".$checkout_id."',
									  emp_code 						='".$emp_code."',
									  create_date 					='".$create_date."',
									  att_starting_km 				='".$att_starting_km."',
									  att_odometer					='".$att_odometer."',
									  att_necessary_items			='".$att_necessary_items."',
									  att_activities				='".$att_activities."',
									  att_vehicle_type				='".$att_vehicle_type."',
									  checkout_ending_km			='".$checkout_ending_km."',
									  checkout_odometer				='".$checkout_odometer."'";											  				//echo $sqlinsertattcheckout; 
				if(mysqli_query($link,$sqlinsertattcheckout))
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
			else
			{
				$sqlupdateattcheckout="UPDATE att_checkout_journey_info SET 
									  checkout_id 					='".$checkout_id."',
									  emp_code 						='".$emp_code."',
									  create_date 					='".$create_date."',
									  att_starting_km 				='".$att_starting_km."',
									  att_odometer					='".$att_odometer."',
									  att_necessary_items			='".$att_necessary_items."',
									  att_activities				='".$att_activities."',
									  checkout_ending_km			='".$checkout_ending_km."',
									  checkout_odometer				='".$checkout_odometer."' 
									  WHERE attendance_id='".$attendance_id."'";											 
				if(mysqli_query($link,$sqlupdateattcheckout))
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
		  else
		  {
		      
		      $sqlchkattcheckout="SELECT * FROM att_checkout_journey_info WHERE emp_code='".$emp_code."' AND create_date='".$create_date."'";
		      
		$reschkattcheckout1 = mysqli_query($link,$sqlchkattcheckout) or die(mysqli_error()." Error in check Att checkout: ".$sqlchkattcheckout); 
		$countchkattcheckout1=mysqli_num_rows($reschkattcheckout1);
		//For update the location table for existing trans id for mt
			if($countchkattcheckout1>0)
			{
			    
			    $flag=5;
			    
			}
			else{
			
			
			
		      if($att_starting_km==""){
			        $att_starting_km = 0;
			    }
			    if($checkout_ending_km==""){
			        $checkout_ending_km = 0;
			    }
			    
			  $sqlinsertattcheckout="INSERT INTO att_checkout_journey_info SET attendance_id ='".$attendance_id."',
									  checkout_id 					='".$checkout_id."',
									  emp_code 						='".$emp_code."',
									  create_date 					='".$create_date."',
									  att_starting_km 				='".$att_starting_km."',
									  att_odometer					='".$att_odometer."',
									  att_necessary_items			='".$att_necessary_items."',
									  att_activities				='".$att_activities."',
									  att_vehicle_type				='".$att_vehicle_type."',
									  checkout_ending_km			='".$checkout_ending_km."',
									  checkout_odometer				='".$checkout_odometer."'";											  				//echo $sqlinsertattcheckout;
				if(mysqli_query($link,$sqlinsertattcheckout))
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
}//End of att checkout if

/* -----------------End for att checkout-------------------------------------------------------------------------*/
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
