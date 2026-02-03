<?php
//error_reporting(E_ALL);
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

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><tour_expense><location><emp_code><![CDATA[E0016]]></emp_code><trans_id><![CDATA[TTE001620190701173619]]></trans_id><latt><![CDATA[22.5643343]]></latt><longi><![CDATA[88.3568303]]></longi><date><![CDATA[2019-07-01 17:36:19]]></date></location><tour_expense_details><TOUR_RXP_TRANS_ID><![CDATA[TTE001620190701173619]]></TOUR_RXP_TRANS_ID><EMP_CODE><![CDATA[E0016]]></EMP_CODE><TOUR_TYPE><![CDATA[EX]]></TOUR_TYPE><TOUR_DATE_FROM><![CDATA[2019-07-01]]></TOUR_DATE_FROM><TOUR_DATE_TO><![CDATA[null]]></TOUR_DATE_TO><DEP_TIME><![CDATA[]]></DEP_TIME><ARR_TIME><![CDATA[]]></ARR_TIME><TOUR_PLACE_FROM><![CDATA[habra]]></TOUR_PLACE_FROM><TOUR_PLACE_TO><![CDATA[kolkata]]></TOUR_PLACE_TO><LOCAL_CONVEYANCE><![CDATA[50]]></LOCAL_CONVEYANCE><TRAVEL_MODE><![CDATA[MODE]]></TRAVEL_MODE><TRANSPORT_FAIR><![CDATA[80]]></TRANSPORT_FAIR><FOODING_ALLOWANCE><![CDATA[90]]></FOODING_ALLOWANCE><HOTEL_CHARGE><![CDATA[]]></HOTEL_CHARGE><REMARKS><![CDATA[]]></REMARKS><TRANSPORT_ATTACHMENT><![CDATA[]]></TRANSPORT_ATTACHMENT><FOODING_ATTACHMENT><![CDATA[]]></FOODING_ATTACHMENT><LODGING_ATTACHMENT><![CDATA[]]></LODGING_ATTACHMENT><OTHER_EXPENSES><![CDATA[]]></OTHER_EXPENSES><ATTACHMENT_ID><![CDATA[]]></ATTACHMENT_ID><FUEL_BILL_ATTACHMENT><![CDATA[]]></FUEL_BILL_ATTACHMENT><MISC><![CDATA[]]></MISC></tour_expense_details></tour_expense></root>";*/



$tour_expense_location_emp_code="*ROOT*TOUR_EXPENSE*LOCATION*EMP_CODE";
$tour_expense_location_trans_id = "*ROOT*TOUR_EXPENSE*LOCATION*TRANS_ID";
$tour_expense_latt = "*ROOT*TOUR_EXPENSE*LOCATION*LATT";
$tour_expense_longi = "*ROOT*TOUR_EXPENSE*LOCATION*LONGI";
$tour_expense_date="*ROOT*TOUR_EXPENSE*LOCATION*DATE";
$tour_food_lodge_trans_id = "*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*TOUR_EXP_TRANS_ID";
$tour_expense_emp_code = "*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*EMP_CODE";
$tour_expense_tour_type = "*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*TOUR_TYPE";
$tour_expense_tour_date_from = "*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*TOUR_DATE_FROM";
$tour_expense_tour_date_to = "*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*TOUR_DATE_TO";
$tour_expense_dep_time ="*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*DEP_TIME";
$tour_expense_arr_time ="*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*ARR_TIME";
$tour_expense_place_from ="*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*TOUR_PLACE_FROM";
$tour_expense_place_to ="*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*TOUR_PLACE_TO";
$tour_expense_local_conveyance ="*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*LOCAL_CONVEYANCE";
$tour_expense_transport_mode ="*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*TRAVEL_MODE";
$tour_expense_transport_fair ="*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*TRANSPORT_FAIR";
$tour_expense_fooding_allowance ="*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*FOODING_ALLOWANCE";
$tour_expense_hotel_charge ="*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*HOTEL_CHARGE";
$tour_expense_remarks ="*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*REMARKS";
$tour_expense_transport_attachment ="*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*TRANSPORT_ATTACHMENT";
$tour_expense_fooding_attachment ="*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*FOODING_ATTACHMENT";
$tour_expense_lodging_attachment ="*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*LODGING_ATTACHMENT";
$tour_expense_other_expenses ="*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*OTHER_EXPENSES";
$tour_expense_supporting_attachment_file ="*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*ATTACHMENT_ID";
//$tour_expense_particulars ="*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*PARTICULARS";
$tour_expense_fuel_bill_attachment ="*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*FUEL_BILL_ATTACHMENT";
$tour_expense_misc ="*ROOT*TOUR_EXPENSE*TOUR_EXPENSE_DETAILS*MISC";

$tour_expense_array = array();
$tour_expense_trans_id_array=array();
$tour_expense_emp_code_array=array();
$counter = 0;
class xml_tour_expense{
	var $tour_expense_location_emp_code,$tour_expense_location_trans_id,$tour_expense_latt,$tour_expense_longi,$tour_expense_date,
	$tour_food_lodge_trans_id,$tour_expense_emp_code,$tour_expense_tour_type,$tour_expense_tour_date_from,$tour_expense_tour_date_to,$tour_expense_dep_time,
	$tour_expense_arr_time,$tour_expense_place_from,$tour_expense_place_to,$tour_expense_local_conveyance,$tour_expense_transport_mode,$tour_expense_transport_fair,$tour_expense_fooding_allowance,
	$tour_expense_hotel_charge,$tour_expense_remarks,$tour_expense_transport_attachment,$tour_expense_fooding_attachment,$tour_expense_lodging_attachment,
	$tour_expense_other_expenses,$tour_expense_supporting_attachment_file,$tour_expense_fuel_bill_attachment,$tour_expense_misc;
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
    global $tour_expense_location_emp_code,$tour_expense_location_trans_id,$tour_expense_latt,$tour_expense_longi,$tour_expense_date,
	$tour_food_lodge_trans_id,$tour_expense_emp_code,$tour_expense_tour_type,$tour_expense_tour_date_from,$tour_expense_tour_date_to,$tour_expense_dep_time,
	$tour_expense_arr_time,$tour_expense_place_from,$tour_expense_place_to,$tour_expense_local_conveyance,$tour_expense_transport_mode,
	$tour_expense_transport_fair,$tour_expense_fooding_allowance,
	$tour_expense_hotel_charge,$tour_expense_remarks,$tour_expense_transport_attachment,$tour_expense_fooding_attachment,$tour_expense_lodging_attachment,
	$tour_expense_other_expenses,$tour_expense_supporting_attachment_file,$tour_expense_fuel_bill_attachment,$tour_expense_misc,$tour_expense_array,$counter,$current_tag;
	if(substr($current_tag,0,18)=='*ROOT*TOUR_EXPENSE')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $tour_expense_location_emp_code:
				$tour_expense_array[$counter] = new xml_tour_expense();
				$tour_expense_array[$counter]->tour_expense_location_emp_code = $data;
				break;
			case $tour_expense_location_trans_id:
				$tour_expense_array[$counter]->tour_expense_location_trans_id = $data;
				break;
			case $tour_expense_latt:
				$tour_expense_array[$counter]->tour_expense_latt = $data;
				break;
			case $tour_expense_longi:
				$tour_expense_array[$counter]->tour_expense_longi = $data;
				break;
			case $tour_expense_date:
				$tour_expense_array[$counter]->tour_expense_date = $data;
				break;
			case $tour_food_lodge_trans_id:
				$tour_expense_array[$counter]->tour_food_lodge_trans_id = $data;
				break;
			case $tour_expense_emp_code:
				$tour_expense_array[$counter]->tour_expense_emp_code = $data;
				break;
			case $tour_expense_tour_type:
				$tour_expense_array[$counter]->tour_expense_tour_type = $data;
				break;
			case $tour_expense_tour_date_from:
				$tour_expense_array[$counter]->tour_expense_tour_date_from = $data;
				break;
			case $tour_expense_tour_date_to:
				$tour_expense_array[$counter]->tour_expense_tour_date_to = $data;
				break;					
			case $tour_expense_dep_time:
				$tour_expense_array[$counter]->tour_expense_dep_time = $data;
				break;
			case $tour_expense_arr_time:
				$tour_expense_array[$counter]->tour_expense_arr_time = $data;
				break;
			case $tour_expense_place_from:
				$tour_expense_array[$counter]->tour_expense_place_from = $data;
				break;
			case $tour_expense_place_to:
				$tour_expense_array[$counter]->tour_expense_place_to = $data;
				break;		
			case $tour_expense_local_conveyance:
				$tour_expense_array[$counter]->tour_expense_local_conveyance = $data;
				break;
			case $tour_expense_transport_mode:
				$tour_expense_array[$counter]->tour_expense_transport_mode = $data;
				break;
			case $tour_expense_transport_fair:
				$tour_expense_array[$counter]->tour_expense_transport_fair = $data;
				break;
			case $tour_expense_fooding_allowance:
				$tour_expense_array[$counter]->tour_expense_fooding_allowance = $data;
				break;		
			case $tour_expense_hotel_charge:
				$tour_expense_array[$counter]->tour_expense_hotel_charge = $data;
				break;
		   case $tour_expense_remarks:
				$tour_expense_array[$counter]->tour_expense_remarks = $data;
				break;
			case $tour_expense_transport_attachment:
				$tour_expense_array[$counter]->tour_expense_transport_attachment = $data;
				break;
			case $tour_expense_fooding_attachment:
				$tour_expense_array[$counter]->tour_expense_fooding_attachment = $data;
				break;
			case $tour_expense_lodging_attachment:
				$tour_expense_array[$counter]->tour_expense_lodging_attachment = $data;
				break;			
			case $tour_expense_other_expenses:
				$tour_expense_array[$counter]->tour_expense_other_expenses = $data;
				break;
			case $tour_expense_supporting_attachment_file:
				$tour_expense_array[$counter]->tour_expense_supporting_attachment_file = $data;
				break;
			case $tour_expense_fuel_bill_attachment:
				$tour_expense_array[$counter]->tour_expense_fuel_bill_attachment = $data;
				break;
			case $tour_expense_misc:
				$tour_expense_array[$counter]->tour_expense_misc = $data;
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
		$tour_expense_location_emp_code=$tour_expense_array[$x]->tour_expense_location_emp_code;
		$tour_expense_location_trans_id=$tour_expense_array[$x]->tour_expense_location_trans_id;
		$tour_expense_latt=$tour_expense_array[$x]->tour_expense_latt;
		$tour_expense_longi=$tour_expense_array[$x]->tour_expense_longi;
		$tour_expense_date=$tour_expense_array[$x]->tour_expense_date;
		$tour_food_lodge_trans_id=$tour_expense_array[$x]->tour_food_lodge_trans_id;
		$tour_expense_emp_code=$tour_expense_array[$x]->tour_expense_emp_code;
		$tour_expense_tour_type=$tour_expense_array[$x]->tour_expense_tour_type;
		$tour_expense_tour_date_from=$tour_expense_array[$x]->tour_expense_tour_date_from;
		$tour_expense_tour_date_to=$tour_expense_array[$x]->tour_expense_tour_date_to;
		$tour_expense_dep_time=$tour_expense_array[$x]->tour_expense_dep_time;
		$tour_expense_arr_time=$tour_expense_array[$x]->tour_expense_arr_time;
		$tour_expense_place_from=$tour_expense_array[$x]->tour_expense_place_from;
		$tour_expense_place_to=$tour_expense_array[$x]->tour_expense_place_to;
		$tour_expense_local_conveyance=$tour_expense_array[$x]->tour_expense_local_conveyance;
		$tour_expense_transport_mode=$tour_expense_array[$x]->tour_expense_transport_mode;
		$tour_expense_transport_fair=$tour_expense_array[$x]->tour_expense_transport_fair;
		$tour_expense_fooding_allowance=$tour_expense_array[$x]->tour_expense_fooding_allowance;
		$tour_expense_hotel_charge=$tour_expense_array[$x]->tour_expense_hotel_charge;
		$tour_expense_remarks=$tour_expense_array[$x]->tour_expense_remarks;
		$tour_expense_transport_attachment=$tour_expense_array[$x]->tour_expense_transport_attachment;
		$tour_expense_fooding_attachment=$tour_expense_array[$x]->tour_expense_fooding_attachment;
		$tour_expense_lodging_attachment=$tour_expense_array[$x]->tour_expense_lodging_attachment;
		$tour_expense_other_expenses=$tour_expense_array[$x]->tour_expense_other_expenses;
		$tour_expense_fuel_bill_attachment=$tour_expense_array[$x]->tour_expense_fuel_bill_attachment;
		$tour_expense_misc=$tour_expense_array[$x]->tour_expense_misc;
		$tour_expense_supporting_attachment_file=$tour_expense_array[$x]->tour_expense_supporting_attachment_file;
		//For checking that trans id exist or not for tour and expense
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$tour_expense_location_trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check  location: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for tour and expense
		if($countchkorlocation>0)
		{
			if(!in_array($tour_expense_location_trans_id,$tour_expense_trans_id_array))
			{
				array_push($tour_expense_trans_id_array,$tour_expense_location_trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$tour_expense_location_emp_code."',
									latt='".$tour_expense_latt."',
									longi='".$tour_expense_longi."'
									WHERE trans_id='".$tour_expense_location_trans_id."'";
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
			// create the data for location table date field , by checking the current date and time and the actual date and time of tour_expenses
			$date=gmdate('d',strtotime('+329 minute'));
			$month=gmdate('m',strtotime('+329 minute'));
			$year=gmdate('Y',strtotime('+329 minute'));

			$hour=gmdate('H',strtotime('+329 minute'));
			$minute=gmdate('i',strtotime('+329 minute'));
			$second=gmdate('s',strtotime('+329 minute'));
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			
			//For Insert into the location table for new trans id regarding tour_expenses
			$sqlinsertorlocation="INSERT INTO location SET emp_code='".$tour_expense_location_emp_code."',
									trans_id='".$tour_expense_location_trans_id."',
									latt='".$tour_expense_latt."',
									longi='".$tour_expense_longi."',
									date='".$tour_expense_date."',
									updatetime='".$location_date."'"; 
			
			//For Insert into the tour_expenses table for new trans id
			$sqlinserttourexpense="INSERT INTO tour_expenses_details SET tour_exp_trans_id='".$tour_food_lodge_trans_id."',
								  emp_code 					='".$tour_expense_emp_code."',
								  tour_type 				='".$tour_expense_tour_type."',
								  tour_date_from 			='".$tour_expense_tour_date_from."',
								  tour_date_to 				='".$tour_expense_tour_date_to."',
								  dep_time 					='".$tour_expense_dep_time."',
								  arr_time					='".$tour_expense_arr_time."',
								  tour_place_from			='".addslashes($tour_expense_place_from)."',
								  tour_place_to			    ='".addslashes($tour_expense_place_to)."',
								  local_conveyance			='".addslashes($tour_expense_local_conveyance)."',
								  transport_mode_type		='".$tour_expense_transport_mode."',
								  transport_other_charges	='".addslashes($tour_expense_transport_fair)."',
								  fooding_charges			='".addslashes($tour_expense_fooding_allowance)."',
								  hotel_charge			='".addslashes($tour_expense_hotel_charge)."',
								  transport_attachment			='".addslashes($tour_expense_transport_attachment)."',
								  fooding_attachment			='".addslashes($tour_expense_fooding_attachment)."',
								  hotel_attachment			='".addslashes($tour_expense_lodging_attachment)."',
								  other_expenses			='".addslashes($tour_expense_other_expenses)."',
									remarks					='".addslashes($tour_expense_remarks)."',
									fuel_bill_attachment 	='".addslashes($tour_expense_fuel_bill_attachment)."',
									misc					='".addslashes($tour_expense_misc)."',
								  other_attachment_file		='".$tour_expense_supporting_attachment_file."'";
			if(mysqli_query($link,$sqlinsertorlocation) && mysqli_query($link,$sqlinserttourexpense))
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
 /* --------------------END QUERY FOR TOUR EXPENSE------------------------------------------------------------------------------------------------------------*/
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