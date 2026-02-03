<?php
//error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);

/*$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$result = mysqli_query($link,$sqlquery);
$countdatarefresh=mysqli_num_rows($result);*/
$body=file_get_contents('php://input');
//$body=str_replace("'",'"',$body);

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><food_expense><location><emp_code><![CDATA[E0233]]></emp_code>
<trans_id><![CDATA[TFE023320140822162938]]></trans_id><latt><![CDATA[22.5640985]]></latt>
<longi><![CDATA[88.3569103]]></longi><date><![CDATA[2014-08-22 16:29:38]]></date></location><food_expense_details><food_exp_trans_id><![CDATA[TFE023320140822162938]]></food_exp_trans_id><emp_code><![CDATA[E0233]]></emp_code><base_station><![CDATA[kol]]></base_station><expense_type><![CDATA[lunch]]></expense_type><date><![CDATA[13-08-2014]]></date><accompany><![CDATA[2]]></accompany><payment_amount><![CDATA[2500]]></payment_amount>
<payment_mode><![CDATA[card]]></payment_mode><attachment_id><![CDATA[E023320140822162921.png]]></attachment_id></food_expense_details></food_expense></root>";*/

$food_expense_location_emp_code="*ROOT*FOOD_EXPENSE*LOCATION*EMP_CODE";
$food_expense_location_trans_id = "*ROOT*FOOD_EXPENSE*LOCATION*TRANS_ID";
$food_expense_latt = "*ROOT*FOOD_EXPENSE*LOCATION*LATT";
$food_expense_longi = "*ROOT*FOOD_EXPENSE*LOCATION*LONGI";
$food_expense_location_date="*ROOT*FOOD_EXPENSE*LOCATION*DATE";
$food_expense_trans_id = "*ROOT*FOOD_EXPENSE*FOOD_EXPENSE_DETAILS*FOOD_EXP_TRANS_ID";
$food_expense_emp_code = "*ROOT*FOOD_EXPENSE*FOOD_EXPENSE_DETAILS*EMP_CODE";
$food_expense_base_station ="*ROOT*FOOD_EXPENSE*FOOD_EXPENSE_DETAILS*BASE_STATION";
$food_expense_expense_type ="*ROOT*FOOD_EXPENSE*FOOD_EXPENSE_DETAILS*EXPENSE_TYPE";
$food_expense_date ="*ROOT*FOOD_EXPENSE*FOOD_EXPENSE_DETAILS*DATE";
$food_expense_accompany ="*ROOT*FOOD_EXPENSE*FOOD_EXPENSE_DETAILS*ACCOMPANY";
$food_expense_payment_amount ="*ROOT*FOOD_EXPENSE*FOOD_EXPENSE_DETAILS*PAYMENT_AMOUNT";
$food_expense_payment_mode ="*ROOT*FOOD_EXPENSE*FOOD_EXPENSE_DETAILS*PAYMENT_MODE";
$food_expense_supporting_attachment_file ="*ROOT*FOOD_EXPENSE*FOOD_EXPENSE_DETAILS*ATTACHMENT_ID";

$food_expense_array = array();
$food_expense_trans_id_array=array();
$food_expense_emp_code_array=array();
$counter = 0;

class xml_food_expense{
	var $food_expense_location_emp_code,$food_expense_location_trans_id,$food_expense_latt,$food_expense_longi,$food_expense_location_date,$food_expense_trans_id,$food_expense_emp_code,$food_expense_base_station,$food_expense_expense_type,$food_expense_date,$food_expense_accompany,$food_expense_payment_amount,$food_expense_payment_mode,$food_expense_supporting_attachment_file;
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
    global $food_expense_location_emp_code,$food_expense_location_trans_id,$food_expense_latt,$food_expense_longi,$food_expense_location_date,$current_tag,$food_expense_trans_id,$food_expense_emp_code,$food_expense_base_station,$food_expense_expense_type,$food_expense_date,$food_expense_accompany,
		$food_expense_payment_amount,$food_expense_payment_mode,$food_expense_supporting_attachment_file,$food_expense_array,$counter;
	if(substr($current_tag,0,18)=='*ROOT*FOOD_EXPENSE')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $food_expense_location_emp_code:
				$food_expense_array[$counter] = new xml_food_expense();
				$food_expense_array[$counter]->food_expense_location_emp_code = $data;
				break;
			case $food_expense_location_trans_id:
				$food_expense_array[$counter]->food_expense_location_trans_id = $data;
				break;
			case $food_expense_latt:
				$food_expense_array[$counter]->food_expense_latt = $data;
				break;
			case $food_expense_longi:
				$food_expense_array[$counter]->food_expense_longi = $data;
				break;
			case $food_expense_location_date:
				$food_expense_array[$counter]->food_expense_location_date = $data;
				break;		
			case $food_expense_trans_id:
				$food_expense_array[$counter]->food_expense_trans_id = $data;
				break;
			case $food_expense_emp_code:
				$food_expense_array[$counter]->food_expense_emp_code = $data;
				break;
			case $food_expense_base_station:
				$food_expense_array[$counter]->food_expense_base_station = $data;
				break;
			case $food_expense_expense_type:
				$food_expense_array[$counter]->food_expense_expense_type = $data;
				break;
			case $food_expense_date:
				$food_expense_array[$counter]->food_expense_date = $data;
				break;
			case $food_expense_accompany:
				$food_expense_array[$counter]->food_expense_accompany = $data;
				break;
			case $food_expense_payment_amount:
				$food_expense_array[$counter]->food_expense_payment_amount = $data;
				break;
			case $food_expense_payment_mode:
				$food_expense_array[$counter]->food_expense_payment_mode = $data;
				break;		
			case $food_expense_supporting_attachment_file:
				$food_expense_array[$counter]->food_expense_supporting_attachment_file = $data;
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
/* -------------------------------------------------------START QUERY FOR FOOD EXPENSE-----------------------------------------------------------------------*/
if(count($food_expense_array)>0)
{
	//$count=1;
	for($x=0;$x<count($food_expense_array);$x++){
		$food_expense_location_emp_code=$food_expense_array[$x]->food_expense_location_emp_code;
		$food_expense_location_trans_id=$food_expense_array[$x]->food_expense_location_trans_id;
		$food_expense_latt=$food_expense_array[$x]->food_expense_latt;
		$food_expense_longi=$food_expense_array[$x]->food_expense_longi;
		$food_expense_location_date=$food_expense_array[$x]->food_expense_location_date;
		$food_expense_trans_id=$food_expense_array[$x]->food_expense_trans_id;
		$food_expense_emp_code=$food_expense_array[$x]->food_expense_emp_code;
		$food_expense_base_station=$food_expense_array[$x]->food_expense_base_station;
		$food_expense_expense_type=$food_expense_array[$x]->food_expense_expense_type;
		$food_expense_date=date('Y-m-d',strtotime($food_expense_array[$x]->food_expense_date));
		$food_expense_accompany=$food_expense_array[$x]->food_expense_accompany;
		$food_expense_payment_amount=$food_expense_array[$x]->food_expense_payment_amount;
		$food_expense_payment_mode=$food_expense_array[$x]->food_expense_payment_mode;
		$food_expense_supporting_attachment_file=$food_expense_array[$x]->food_expense_supporting_attachment_file;
		
		//For checking that trans id exist or not for food and expense
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$food_expense_location_trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check food expense location: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for food and expense
		if($countchkorlocation>0)
		{
			if(!in_array($food_expense_location_trans_id,$food_expense_trans_id_array))
			{
				array_push($food_expense_trans_id_array,$food_expense_location_trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$food_expense_location_emp_code."',
									latt='".$food_expense_latt."',
									longi='".$food_expense_longi."'
									WHERE trans_id='".$food_expense_location_trans_id."'";
			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update food expense location: ".$sqlupdateorlocation);
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
			// create the data for location table date field , by checking the current date and time and the actual date and time of food expenses
			$date=gmdate('d',strtotime('+329 minute'));
			$month=gmdate('m',strtotime('+329 minute'));
			$year=gmdate('Y',strtotime('+329 minute'));

			$hour=gmdate('H',strtotime('+329 minute'));
			$minute=gmdate('i',strtotime('+329 minute'));
			$second=gmdate('s',strtotime('+329 minute'));
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			
			//For Insert into the location table for new trans id regarding food expenses
			$sqlinsertorlocation="INSERT INTO location SET emp_code='".$food_expense_location_emp_code."',
									trans_id='".$food_expense_location_trans_id."',
									latt='".$food_expense_latt."',
									longi='".$food_expense_longi."',
									date='".$food_expense_location_date."',
									updatetime='".$location_date."'"; 
			
			//For Insert into the fooding_expenses table for new trans id
			$sqlinsertfoodexpense="INSERT INTO fooding_expenses SET food_exp_trans_id='".$food_expense_trans_id."',
								  emp_code 			='".$food_expense_emp_code."',
								  base_station 		='".$food_expense_base_station."',
								  expense_type		='".$food_expense_expense_type."',
								  date				='".$food_expense_date."',
								  accompany			='".$food_expense_accompany."',
								  payment_amount	='".$food_expense_payment_amount."',
								  payment_mode		='".$food_expense_payment_mode."',
								  attachment_file	='".$food_expense_supporting_attachment_file."'";
			if(mysqli_query($link,$sqlinsertorlocation) && mysqli_query($link,$sqlinsertfoodexpense))
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
 /* --------------------END QUERY FOR FOOD EXPENSE------------------------------------------------------------------------------------------------------------*/
$countdatarefresh=returndatarefresh($emp_code);
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