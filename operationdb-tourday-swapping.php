<?php
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);

$body=file_get_contents('php://input');
$body_xml=str_replace("'",'"',$body);
	$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
	mysqli_query($link,$sqlinsert_xml_data);
//$body=str_replace("'",'"',$body);

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><tour_swap><location><emp_code><![CDATA[E0233]]></emp_code><trans_id><![CDATA[TSE023320140822162132]]></trans_id><latt><![CDATA[22.5640746]]></latt><longi><![CDATA[88.3569888]]></longi><date><![CDATA[2014-08-22 16:21:33]]></date></location><tour_swap_details><tour_day_swap_trans_id><![CDATA[TSE023320140822162132]]></tour_day_swap_trans_id><emp_code><![CDATA[E0233]]></emp_code><actual_tourday><![CDATA[MONDAY]]></actual_tourday><deviate_tourday><![CDATA[THURSDAY]]></deviate_tourday><swap_date_actual><![CDATA[2017-07-05]]></swap_date_actual><swap_date_deviate><![CDATA[2017-07-06]]></swap_date_deviate></tour_swap_details></tour_swap></root>";*/

$tour_swap_location_emp_code="*ROOT*TOUR_SWAP*LOCATION*EMP_CODE";
$tour_swap_location_trans_id = "*ROOT*TOUR_SWAP*LOCATION*TRANS_ID";
$tour_swap_latt = "*ROOT*TOUR_SWAP*LOCATION*LATT";
$tour_swap_longi = "*ROOT*TOUR_SWAP*LOCATION*LONGI";
$tour_swap_date="*ROOT*TOUR_SWAP*LOCATION*DATE";
$tour_day_swap_trans_id = "*ROOT*TOUR_SWAP*TOUR_SWAP_DETAILS*TOUR_DAY_SWAP_TRANS_ID";
$tour_swap_emp_code = "*ROOT*TOUR_SWAP*TOUR_SWAP_DETAILS*EMP_CODE";
$actual_tourday ="*ROOT*TOUR_SWAP*TOUR_SWAP_DETAILS*ACTUAL_TOURDAY";
$deviate_tourday ="*ROOT*TOUR_SWAP*TOUR_SWAP_DETAILS*DEVIATE_TOURDAY";
$swap_date_actual ="*ROOT*TOUR_SWAP*TOUR_SWAP_DETAILS*SWAP_DATE_ACTUAL";
$swap_date_deviate ="*ROOT*TOUR_SWAP*TOUR_SWAP_DETAILS*SWAP_DATE_DEVIATE";

$tour_swap_array = array();
$tour_swap_trans_id_array=array();
$tour_swap_emp_code_array=array();
$counter = 0;

class xml_tour_swap{
	var $tour_swap_location_emp_code,$tour_swap_location_trans_id,$tour_swap_latt,$tour_swap_longi,$tour_swap_date,$tour_day_swap_trans_id,$tour_swap_emp_code,$actual_tourday,$deviate_tourday,$swap_date_actual,$swap_date_deviate;
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
    global $tour_swap_location_emp_code,$tour_swap_location_trans_id,$tour_swap_latt,$tour_swap_longi,$tour_swap_date,$tour_day_swap_trans_id,
			$tour_swap_emp_code,$actual_tourday,$deviate_tourday,$swap_date_actual,$swap_date_deviate,$current_tag,$tour_swap_array,$counter;
	if(substr($current_tag,0,15)=='*ROOT*TOUR_SWAP')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $tour_swap_location_emp_code:
				$tour_swap_array[$counter] = new xml_tour_swap();
				$tour_swap_array[$counter]->tour_swap_location_emp_code = $data;
				break;
			case $tour_swap_location_trans_id:
				$tour_swap_array[$counter]->tour_swap_location_trans_id = $data;
				break;
			case $tour_swap_latt:
				$tour_swap_array[$counter]->tour_swap_latt = $data;
				break;
			case $tour_swap_longi:
				$tour_swap_array[$counter]->tour_swap_longi = $data;
				break;
			case $tour_swap_date:
				$tour_swap_array[$counter]->tour_swap_date = $data;
				break;
			case $tour_day_swap_trans_id:
				$tour_swap_array[$counter]->tour_day_swap_trans_id = $data;
				break;
			case $tour_swap_emp_code:
				$tour_swap_array[$counter]->tour_swap_emp_code = $data;
				break;
			case $actual_tourday:
				$tour_swap_array[$counter]->actual_tourday = $data;
				break;
			case $deviate_tourday:
				$tour_swap_array[$counter]->deviate_tourday = $data;
				break;
			case $swap_date_actual:
				$tour_swap_array[$counter]->swap_date_actual = $data;
				break;
			case $swap_date_deviate:
				$tour_swap_array[$counter]->swap_date_deviate = $data;
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
/* -------------------------------------------------------START QUERY FOR TOUR SWAP-----------------------------------------------------------------------*/
if(count($tour_swap_array)>0)
{
	//$count=1;
	
	for($x=0;$x<count($tour_swap_array);$x++){
		$tour_swap_location_emp_code=$tour_swap_array[$x]->tour_swap_location_emp_code;
		$tour_swap_location_trans_id=$tour_swap_array[$x]->tour_swap_location_trans_id;
		$tour_swap_latt=$tour_swap_array[$x]->tour_swap_latt;
		$tour_swap_longi=$tour_swap_array[$x]->tour_swap_longi;
		$tour_swap_date=$tour_swap_array[$x]->tour_swap_date;
		$tour_day_swap_trans_id=$tour_swap_array[$x]->tour_day_swap_trans_id;
		$tour_swap_emp_code=$tour_swap_array[$x]->tour_swap_emp_code;
		$actual_tourday=$tour_swap_array[$x]->actual_tourday;
		$deviate_tourday=$tour_swap_array[$x]->deviate_tourday;
		$swap_date_actual=$tour_swap_array[$x]->swap_date_actual;
		$swap_date_deviate=$tour_swap_array[$x]->swap_date_deviate;
		
		//For checking that trans id exist or not for tour swap
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$tour_swap_location_trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check  location: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for tour and expense
		if($countchkorlocation>0)
		{
			if(!in_array($tour_swap_location_trans_id,$tour_swap_trans_id_array))
			{
				array_push($tour_swap_trans_id_array,$tour_swap_location_trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$tour_swap_location_emp_code."',
									latt='".$tour_swap_latt."',
									longi='".$tour_swap_longi."'
									WHERE trans_id='".$tour_swap_location_trans_id."'";
			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update tour location: ".$sqlupdateorlocation);
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
			// create the data for location table date field , by checking the current date and time and the actual date and time of tour_swap
			$date=gmdate('d',strtotime('+329 minute'));
			$month=gmdate('m',strtotime('+329 minute'));
			$year=gmdate('Y',strtotime('+329 minute'));

			$hour=gmdate('H',strtotime('+329 minute'));
			$minute=gmdate('i',strtotime('+329 minute'));
			$second=gmdate('s',strtotime('+329 minute'));
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			
			//For Insert into the location table for new trans id regarding tour_swap
			$sqlinsertorlocation="INSERT INTO location SET emp_code='".$tour_swap_location_emp_code."',
									trans_id='".$tour_swap_location_trans_id."',
									latt='".$tour_swap_latt."',
									longi='".$tour_swap_longi."',
									date='".$tour_swap_date."',
									updatetime='".$location_date."'"; 
			
			//For Insert into the tour_swap table for new trans id

			$sqlinserttourswap="INSERT INTO tour_day_swapping SET tour_day_swap_trans_id='".$tour_day_swap_trans_id."',
								  	emp_code 					='".$tour_swap_emp_code."',
								  	actual_tourday 					='".$actual_tourday."',
								  deviate_tourday					='".$deviate_tourday."',
								  swap_date_actual				='".$swap_date_actual."',
								  swap_date_deviate			='".$swap_date_deviate."'";
			if(mysqli_query($link,$sqlinsertorlocation) && mysqli_query($link,$sqlinserttourswap))
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
   }// End of for loop
	//if($flag==5){}
}
 /* --------------------END QUERY FOR TOUR SWAP------------------------------------------------------------------------------------------------------------*/
if($flag==5)
{
	 $sqlupdatelastoperationtime="UPDATE changepassword SET last_operation_datetime='".$last_operation_datetime."' WHERE emp_code='".$emp_code."'";
	 $rsupdatelastoperationtime=mysqli_query($link,$sqlupdatelastoperationtime);	 
	 mysqli_query($link,"COMMIT");
	 
	 if($countdatarefresh >0)
	 {
		 echo $flag=2;
	 }
	 else
	 {
	 	echo $flag=1;
	 }
}
if($flag==6)
{
	$sqlupdatelastoperationtime="UPDATE changepassword SET last_operation_datetime='".$last_operation_datetime."' WHERE emp_code='".$emp_code."'";
	$rsupdatelastoperationtime=mysqli_query($link,$sqlupdatelastoperationtime);
	mysqli_query($link,"COMMIT");
	
	if($countdatarefresh >0)
	 {
		 echo $flag=2;
	 }
	 else
	 {
	 	echo $flag=1;
	 }
}
mysqli_close($link);
?>