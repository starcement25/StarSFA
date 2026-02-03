<?php
//error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);

$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$result = mysqli_query($link,$sqlquery);
$countdatarefresh=mysqli_num_rows($result);
$body=file_get_contents('php://input');
//$body=str_replace("'",'"',$body);

$body="<?xml version='1.0' encoding='UTF-8'?><root><lodge_expense><location><emp_code><![CDATA[E0233]]></emp_code><trans_id><![CDATA[TLE023320140823153839]]></trans_id><latt><![CDATA[22.5640118]]></latt><longi><![CDATA[88.357115]]></longi><date><![CDATA[2014-08-23 15:38:39]]></date></location><lodge_expense_details><lodge_exp_trans_id><![CDATA[TLE023320140823153839]]></lodge_exp_trans_id><emp_code><![CDATA[E0233]]></emp_code>
<base_station><![CDATA[delhi]]></base_station><hotel_name><![CDATA[taj bengal]]></hotel_name><rent><![CDATA[25000]]></rent><chkin_date><![CDATA[06-08-2014]]></chkin_date><chkout_date><![CDATA[21-08-2014]]></chkout_date><payment_amount><![CDATA[50000]]></payment_amount><payment_mode><![CDATA[cash]]></payment_mode><attachment_id><![CDATA[E023320140823153810.png]]></attachment_id></lodge_expense_details></lodge_expense></root>";

$lodge_expense_location_emp_code="*ROOT*LODGE_EXPENSE*LOCATION*EMP_CODE";
$lodge_expense_location_trans_id = "*ROOT*LODGE_EXPENSE*LOCATION*TRANS_ID";
$lodge_expense_latt = "*ROOT*LODGE_EXPENSE*LOCATION*LATT";
$lodge_expense_longi = "*ROOT*LODGE_EXPENSE*LOCATION*LONGI";
$lodge_expense_date="*ROOT*LODGE_EXPENSE*LOCATION*DATE";
$lodge_expense_trans_id = "*ROOT*LODGE_EXPENSE*LODGE_EXPENSE_DETAILS*LODGE_EXP_TRANS_ID";
$lodge_expense_emp_code = "*ROOT*LODGE_EXPENSE*LODGE_EXPENSE_DETAILS*EMP_CODE";
$lodge_expense_base_station ="*ROOT*LODGE_EXPENSE*LODGE_EXPENSE_DETAILS*BASE_STATION";
$lodge_expense_hotel_name ="*ROOT*LODGE_EXPENSE*LODGE_EXPENSE_DETAILS*HOTEL_NAME";
$lodge_expense_rent ="*ROOT*LODGE_EXPENSE*LODGE_EXPENSE_DETAILS*RENT";
$lodge_expense_chkin_date ="*ROOT*LODGE_EXPENSE*LODGE_EXPENSE_DETAILS*CHKIN_DATE";
$lodge_expense_chkout_date ="*ROOT*LODGE_EXPENSE*LODGE_EXPENSE_DETAILS*CHKOUT_DATE";
$lodge_expense_payment_amount ="*ROOT*LODGE_EXPENSE*LODGE_EXPENSE_DETAILS*PAYMENT_AMOUNT";
$lodge_expense_payment_mode ="*ROOT*LODGE_EXPENSE*LODGE_EXPENSE_DETAILS*PAYMENT_MODE";
$lodge_expense_supporting_attachment_file ="*ROOT*LODGE_EXPENSE*LODGE_EXPENSE_DETAILS*ATTACHMENT_ID";

$lodge_expense_array = array();
$lodge_expense_trans_id_array=array();
$lodge_expense_emp_code_array=array();
$counter = 0;

class xml_lodge_expense{
	var $lodge_expense_location_emp_code,$lodge_expense_location_trans_id,$lodge_expense_latt,$lodge_expense_longi,$lodge_expense_date,$lodge_expense_trans_id,$lodge_expense_emp_code,$lodge_expense_base_station,$lodge_expense_hotel_name,$lodge_expense_rent,$lodge_expense_chkin_date,$lodge_expense_chkout_date,$lodge_expense_payment_amount,$lodge_expense_payment_mode,$lodge_expense_supporting_attachment_file;
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
    global $lodge_expense_location_emp_code,$lodge_expense_location_trans_id,$lodge_expense_latt,$lodge_expense_longi,$lodge_expense_date,$current_tag,$lodge_expense_trans_id,$lodge_expense_emp_code,$lodge_expense_base_station,$lodge_expense_hotel_name,$lodge_expense_rent,$lodge_expense_chkin_date,$lodge_expense_chkout_date,$lodge_expense_payment_amount,$lodge_expense_payment_mode,$lodge_expense_supporting_attachment_file,$lodge_expense_array,$counter;
	if(substr($current_tag,0,19)=='*ROOT*LODGE_EXPENSE')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $lodge_expense_location_emp_code:
				$lodge_expense_array[$counter] = new xml_lodge_expense();
				$lodge_expense_array[$counter]->lodge_expense_location_emp_code = $data;
				break;
			case $lodge_expense_location_trans_id:
				$lodge_expense_array[$counter]->lodge_expense_location_trans_id = $data;
				break;
			case $lodge_expense_latt:
				$lodge_expense_array[$counter]->lodge_expense_latt = $data;
				break;
			case $lodge_expense_longi:
				$lodge_expense_array[$counter]->lodge_expense_longi = $data;
				break;
			case $lodge_expense_date:
				$lodge_expense_array[$counter]->lodge_expense_date = $data;
				break;		
			case $lodge_expense_trans_id:
				$lodge_expense_array[$counter]->lodge_expense_trans_id = $data;
				break;
			case $lodge_expense_emp_code:
				$lodge_expense_array[$counter]->lodge_expense_emp_code = $data;
				break;
			case $lodge_expense_base_station:
				$lodge_expense_array[$counter]->lodge_expense_base_station = $data;
				break;
			case $lodge_expense_hotel_name:
				$lodge_expense_array[$counter]->lodge_expense_hotel_name = $data;
				break;
			case $lodge_expense_rent:
				$lodge_expense_array[$counter]->lodge_expense_rent = $data;
				break;
			case $lodge_expense_chkin_date:
				$lodge_expense_array[$counter]->lodge_expense_chkin_date = $data;
				break;
			case $lodge_expense_chkout_date:
				$lodge_expense_array[$counter]->lodge_expense_chkout_date = $data;
				break;			
			case $lodge_expense_payment_amount:
				$lodge_expense_array[$counter]->lodge_expense_payment_amount = $data;
				break;
			case $lodge_expense_payment_mode:
				$lodge_expense_array[$counter]->lodge_expense_payment_mode = $data;
				break;		
			case $lodge_expense_supporting_attachment_file:
				$lodge_expense_array[$counter]->lodge_expense_supporting_attachment_file = $data;
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
/* -------------------------------------------------------START QUERY FOR LODGE EXPENSE----------------------------------------------------------------------*/
if(count($lodge_expense_array)>0)
{
	//$count=1;
	for($x=0;$x<count($lodge_expense_array);$x++){
		$lodge_expense_location_emp_code=$lodge_expense_array[$x]->lodge_expense_location_emp_code;
		$lodge_expense_location_trans_id=$lodge_expense_array[$x]->lodge_expense_location_trans_id;
		$lodge_expense_latt=$lodge_expense_array[$x]->lodge_expense_latt;
		$lodge_expense_longi=$lodge_expense_array[$x]->lodge_expense_longi;
		$lodge_expense_date=$lodge_expense_array[$x]->lodge_expense_date;
		$lodge_expense_trans_id=$lodge_expense_array[$x]->lodge_expense_trans_id;
		$lodge_expense_emp_code=$lodge_expense_array[$x]->lodge_expense_emp_code;
		$lodge_expense_base_station=$lodge_expense_array[$x]->lodge_expense_base_station;
		$lodge_expense_hotel_name=$lodge_expense_array[$x]->lodge_expense_hotel_name;
		$lodge_expense_rent=$lodge_expense_array[$x]->lodge_expense_rent;
		$lodge_expense_chkin_date=date('Y-m-d',strtotime($lodge_expense_array[$x]->lodge_expense_chkin_date));
		$lodge_expense_chkout_date=date('Y-m-d',strtotime($lodge_expense_array[$x]->lodge_expense_chkout_date));
		$lodge_expense_payment_amount=$lodge_expense_array[$x]->lodge_expense_payment_amount;
		$lodge_expense_payment_mode=$lodge_expense_array[$x]->lodge_expense_payment_mode;
		$lodge_expense_supporting_attachment_file=$lodge_expense_array[$x]->lodge_expense_supporting_attachment_file;
		
		//For checking that trans id exist or not for lodge and expense
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$lodge_expense_location_trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check lodge expense location: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for lodge and expense
		if($countchkorlocation>0)
		{
			if(!in_array($lodge_expense_location_trans_id,$lodge_expense_trans_id_array))
			{
				array_push($lodge_expense_trans_id_array,$lodge_expense_location_trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$lodge_expense_location_emp_code."',
									latt='".$lodge_expense_latt."',
									longi='".$lodge_expense_longi."'
									WHERE trans_id='".$lodge_expense_location_trans_id."'";
			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update lodge expense location: ".$sqlupdateorlocation);
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
			// create the data for location table date field , by checking the current date and time and the actual date and time of lodge expenses
			$date=gmdate('d',strtotime('+329 minute'));
			$month=gmdate('m',strtotime('+329 minute'));
			$year=gmdate('Y',strtotime('+329 minute'));

			$hour=gmdate('H',strtotime('+329 minute'));
			$minute=gmdate('i',strtotime('+329 minute'));
			$second=gmdate('s',strtotime('+329 minute'));
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			
			//For Insert into the location table for new trans id regarding lodge expenses
			$sqlinsertorlocation="INSERT INTO location SET emp_code='".$lodge_expense_location_emp_code."',
									trans_id='".$lodge_expense_location_trans_id."',
									latt='".$lodge_expense_latt."',
									longi='".$lodge_expense_longi."',
									date='".$lodge_expense_date."',
									updatetime='".$location_date."'"; 
			
			//For Insert into the lodgeing_expenses table for new trans id
			$sqlinsertlodgeexpense="INSERT INTO lodging_expenses SET lodg_exp_trans_id='".$lodge_expense_trans_id."',
								  emp_code 			='".$lodge_expense_emp_code."',
								  base_station 		='".$lodge_expense_base_station."',
								  hotel_name		='".$lodge_expense_hotel_name."',
								  rent				='".$lodge_expense_rent."',
								  chkin_date		='".$lodge_expense_chkin_date."',
								  chkout_date		='".$lodge_expense_chkout_date."',
								  payment_amount	='".$lodge_expense_payment_amount."',
								  payment_mode		='".$lodge_expense_payment_mode."',
								  attachment_file	='".$lodge_expense_supporting_attachment_file."'";
			if(mysqli_query($link,$sqlinsertorlocation) && mysqli_query($link,$sqlinsertlodgeexpense))
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
 /* --------------------END QUERY FOR LODGE EXPENSE------------------------------------------------------------------------------------------------------------*/
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
?>