<?php
//error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

function sauda_balance_qty($emp_code,$quantity)
{
	$sauda_booked_condition = "AND substring(SD.sauda_no,-14,8) LIKE '".str_replace("-","",$today)."'";
	$emp_hierarchy_condition=return_employee_hierarchy($emp_code);
	$sql_quantity_booked = "SELECT sum(SD.convert_qty_two) as mt_booked,PM.product_group_code FROM sauda_details SD, product_master PM 
							WHERE substring(SD.sauda_no,3,5) IN(".$emp_hierarchy_condition.")
							$sauda_booked_condition AND SD.sku_code=PM.prod_code AND PM.product_group_code='".$product_code."'";
	$res_quantity_booked = mysqli_query($link,$sql_quantity_booked);
	$row_quantity_booked = mysqli_fetch_assoc($res_quantity_booked);
	$quantity_booked=$row_quantity_booked['mt_booked'];
	$sauda_balance = ($quantity-$quantity_booked);
	return $sauda_balance;
}
$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);

$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$result = mysqli_query($link,$sqlquery);
$countdatarefresh=mysqli_num_rows($result);
$body=file_get_contents('php://input');
//$body=str_replace("'",'"',$body);

if($nick_name=='EMAMIT')
{
	$body_xml=str_replace("'",'"',$body);
	$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
	mysqli_query($link,$sqlinsert_xml_data);	

	/*$sqlselectdbversion="SELECT is_update FROM table_structure_updation  WHERE emp_code='".$emp_code."' and db_version_code='6.4'";
	$rsselectdbversion=mysqli_query($link,$sqlselectdbversion);
	$rowselectdbversion=mysqli_fetch_assoc($rsselectdbversion);
	$is_update=$rowselectdbversion['is_update'];

	if($is_update==1)
	{
		echo $flag=2;
		exit(); 
	}*/
}
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><tour_expense><location><emp_code><![CDATA[E0233]]></emp_code><trans_id><![CDATA[TTE023320140822162132]]></trans_id><latt><![CDATA[22.5640746]]></latt><longi><![CDATA[88.3569888]]></longi><date><![CDATA[2014-08-22 16:21:33]]></date></location><tour_expense_details><tour_exp_trans_id><![CDATA[TTE023320140822162132]]></tour_exp_trans_id><emp_code><![CDATA[E0233]]></emp_code><start_destination><![CDATA[kol]]></start_destination><end_destination><![CDATA[del]]></end_destination><fare><![CDATA[25]]></fare><tour_date><![CDATA[12-08-2014]]></tour_date>
<transport_mode_cat_id><![CDATA[7]]></transport_mode_cat_id><transport_mode_sub_cat_id><![CDATA[12]]></transport_mode_sub_cat_id><distance><![CDATA[25]]></distance><attachment_id><![CDATA[E023320140822162117.png]]></attachment_id><supporting_attached><![CDATA[yes]]></supporting_attached></tour_expense_details></tour_expense></root>";*/


$sauda_allocation_location_emp_code="*ROOT*SAUDA_ALLOCATION*LOCATION*EMP_CODE";
$sauda_allocation_location_trans_id = "*ROOT*SAUDA_ALLOCATION*LOCATION*TRANS_ID";
$sauda_allocation_latt = "*ROOT*SAUDA_ALLOCATION*LOCATION*LATT";
$sauda_allocation_longi = "*ROOT*SAUDA_ALLOCATION*LOCATION*LONGI";
$sauda_allocation_location_date="*ROOT*SAUDA_ALLOCATION*LOCATION*DATE";

$sauda_allocation_trans_id = "*ROOT*SAUDA_ALLOCATION*SAUDA_ALLOCATION_DETAILS*ALLOCATION_ID";
$sauda_allocation_date = "*ROOT*SAUDA_ALLOCATION*SAUDA_ALLOCATION_DETAILS*ALLOCATION_DATE";
$sauda_allocation_emp_code = "*ROOT*SAUDA_ALLOCATION*SAUDA_ALLOCATION_DETAILS*EMP_CODE";
$sauda_allocation_product_filter_code ="*ROOT*SAUDA_ALLOCATION*SAUDA_ALLOCATION_DETAILS*PRODUCT_FILTER_CODE";
$sauda_allocation_qty ="*ROOT*SAUDA_ALLOCATION*SAUDA_ALLOCATION_DETAILS*QTY";

$sauda_allocation_array = array();
$sauda_allocation_details_array=array();
$counter = 0;
$counterdata = 0;

class xml_sauda_allocation{
	var $sauda_allocation_location_emp_code,$sauda_allocation_location_trans_id,$sauda_allocation_latt,$sauda_allocation_longi,$sauda_allocation_location_date;	
}
class xml_sauda_allocation_details{
	var $sauda_allocation_trans_id,$sauda_allocation_date,$sauda_allocation_emp_code,$sauda_allocation_product_filter_code,$sauda_allocation_qty;
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
    global $sauda_allocation_location_emp_code,$sauda_allocation_location_trans_id,$sauda_allocation_latt,$sauda_allocation_longi,$sauda_allocation_location_date,$current_tag,$sauda_allocation_trans_id,$sauda_allocation_date,$sauda_allocation_emp_code,$sauda_allocation_product_filter_code,$sauda_allocation_qty,$sauda_allocation_array,$sauda_allocation_details_array,$counter,$counterdata;
	if(substr($current_tag,0,22)=='*ROOT*SAUDA_ALLOCATION')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
       switch($current_tag){
			case $sauda_allocation_location_emp_code:
				$sauda_allocation_array[$counter] = new xml_sauda_allocation();
				$sauda_allocation_array[$counter]->sauda_allocation_location_emp_code = $data;
				break;
			case $sauda_allocation_location_trans_id:
				$sauda_allocation_array[$counter]->sauda_allocation_location_trans_id = $data;
				break;
			case $sauda_allocation_latt:
				$sauda_allocation_array[$counter]->sauda_allocation_latt = $data;
				break;
			case $sauda_allocation_longi:
				$sauda_allocation_array[$counter]->sauda_allocation_longi = $data;
				break;
			case $sauda_allocation_location_date:
				$sauda_allocation_array[$counter]->sauda_allocation_location_date = $data;
				$counter++;
				break;
	   }
	}
   if(substr($current_tag,0,47)=='*ROOT*SAUDA_ALLOCATION*SAUDA_ALLOCATION_DETAILS')
	  {
		//echo $current_tag.'<br />';
        //echo $data.'<br />';
		switch($current_tag){
			case $sauda_allocation_trans_id:
				$sauda_allocation_details_array[$counterdata] = new xml_sauda_allocation_details();
				$sauda_allocation_details_array[$counterdata]->sauda_allocation_trans_id = $data;
				break;
			case $sauda_allocation_date:
				$sauda_allocation_details_array[$counterdata]->sauda_allocation_date = $data;
				break;	
			case $sauda_allocation_emp_code:
				$sauda_allocation_details_array[$counterdata]->sauda_allocation_emp_code = $data;
				break;
			case $sauda_allocation_product_filter_code:
				$sauda_allocation_details_array[$counterdata]->sauda_allocation_product_filter_code = $data;
				break;
			case $sauda_allocation_qty:
				$sauda_allocation_details_array[$counterdata]->sauda_allocation_qty = $data;
				$counterdata++;
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
$sauda_allocation_array_trans_id=array();

/* -------------------------------------------------------START QUERY FOR SAUDA ALLOCATION-----------------------------------------------------------------------*/
if(count($sauda_allocation_array)>0)
{
	//$count=1;
	for($x=0;$x<count($sauda_allocation_array);$x++){
		$sauda_allocation_location_emp_code=$sauda_allocation_array[$x]->sauda_allocation_location_emp_code;
		$sauda_allocation_location_trans_id=$sauda_allocation_array[$x]->sauda_allocation_location_trans_id;
		$sauda_allocation_latt=$sauda_allocation_array[$x]->sauda_allocation_latt;
		$sauda_allocation_longi=$sauda_allocation_array[$x]->sauda_allocation_longi;
		$sauda_allocation_location_date=$sauda_allocation_array[$x]->sauda_allocation_location_date;

		//For checking that trans id exist or not for sauda allocation
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$sauda_allocation_array_trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check  location: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for sauda allocation
		if($countchkorlocation>0)
		{
			if(!in_array($sauda_allocation_location_trans_id,$sauda_allocation_array_trans_id))
			{
				array_push($sauda_allocation_array_trans_id,$sauda_allocation_location_trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$sauda_allocation_location_emp_code."',
									latt='".$sauda_allocation_latt."',
									longi='".$sauda_allocation_longi."'
									WHERE trans_id='".$sauda_allocation_location_trans_id."'";
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
			// create the data for location table date field , by checking the current date and time and the actual date and time of sauda allocation
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));

			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			
			//For Insert into the location table for new trans id regarding sauda allocation
			$sqlinsertsaudalocation="INSERT INTO location SET emp_code='".$sauda_allocation_location_emp_code."',
									trans_id='".$sauda_allocation_location_trans_id."',
									latt='".$sauda_allocation_latt."',
									longi='".$sauda_allocation_longi."',
									date='".$sauda_allocation_location_date."',
									updatetime='".$location_date."'"; 
			if(mysqli_query($link,$sqlinsertsaudalocation))
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
   //print_r($sauda_allocation_details_array);
   	for($i=0;$i<count($sauda_allocation_details_array);$i++){
		$sauda_allocation_trans_id=$sauda_allocation_details_array[$i]->sauda_allocation_trans_id;
		$sauda_allocation_date=$sauda_allocation_details_array[$i]->sauda_allocation_date;
		$sauda_allocation_emp_code=$sauda_allocation_details_array[$i]->sauda_allocation_emp_code;
		$sauda_allocation_product_filter_code=$sauda_allocation_details_array[$i]->sauda_allocation_product_filter_code;
		$sauda_allocation_qty=$sauda_allocation_details_array[$i]->sauda_allocation_qty;

		$sauda_balance_qty=sauda_balance_qty($sauda_allocation_emp_code,$sauda_allocation_qty);

		// For insert and update of sauda allocation table 
		$sql_check_exists = "SELECT qty FROM sauda_allocation WHERE emp_code = '".$sauda_allocation_emp_code."' AND 
							product_filter_code = '".$sauda_allocation_product_filter_code."'";
		$res_check_exists = mysqli_query($link,$sql_check_exists);
		$total_rows = mysqli_num_rows($res_check_exists);
		
		if($total_rows>0)
		{
			$row_check_exists=mysqli_fetch_assoc($res_check_exists);
			$chk_qty=$row_check_exists['qty'];
			$sql_update_sauda_allocation = "UPDATE sauda_allocation SET 
											qty = '".$sauda_allocation_qty."',
											BAL = '".$sauda_balance_qty."'
										    WHERE emp_code = '".$sauda_allocation_emp_code."' AND product_filter_code = '".$sauda_allocation_product_filter_code."'";
			$res_update_sauda_allocation = mysqli_query($link,$sql_update_sauda_allocation);
		}
		else
		{
			$sql_insert_sauda_allocation = "INSERT INTO sauda_allocation SET 
											emp_code = '".$sauda_allocation_emp_code."', 
											product_filter_code = '".$sauda_allocation_product_filter_code."', 
											qty = '".$sauda_allocation_qty."',
											BAL = '".$sauda_allocation_qty."'";
			if(mysqli_query($link,$sql_insert_sauda_allocation))
			{
				$flag=5;
			}
			else
			{
				mysqli_query($link,"ROLLBACK");
				echo $flag=0;
				return;
			}
			$chk_qty=0;
		}
		modifyempdatadownloadlog($sauda_allocation_emp_code,strtoupper($nick_name));
		// For update sauda allocation table alloted qty
		$sql_check_alloted_by = "SELECT qty FROM sauda_allocation WHERE emp_code = '".$emp_code."' AND 
							product_filter_code = '".$sauda_allocation_product_filter_code."'";
		$res_check_alloted_by = mysqli_query($link,$sql_check_alloted_by);
		$total_rows_alloted_by = mysqli_num_rows($res_check_alloted_by);

		if($total_rows_alloted_by >0)
		{
			$sqlselreportingto="SELECT reporting_to FROM employee_master WHERE emp_code='".$sauda_allocation_emp_code."'";
			$rsselreportingto=mysqli_query($link,$sqlselreportingto);
			$rowselreportingto=mysqli_fetch_assoc($rsselreportingto);
			$immediate_top=$rowselreportingto['reporting_to'];
			
			$emp_lower_array=array();
			$sqlimmediatelower="SELECT emp_code FROM employee_master WHERE reporting_to='".$emp_code."'";
			$rsimmediatelower=mysqli_query($link,$sqlimmediatelower);
			while($rowimmediatelower=mysqli_fetch_assoc($rsimmediatelower))
			{
				array_push($emp_lower_array,$rowimmediatelower['emp_code']);
			}
			
			if($chk_qty > $sauda_allocation_qty)
			{
				$qty_difference=$chk_qty-$sauda_allocation_qty;
				$condition_allot_qty="allot_qty =(allot_qty - $qty_difference)";
				$condition_bal_qty="BAL =(BAL + $qty_difference)";
			}
			else if($chk_qty < $sauda_allocation_qty)
			{
			    $qty_difference=$sauda_allocation_qty-$chk_qty;
				$condition_allot_qty="allot_qty =(allot_qty+$qty_difference)";
				$condition_bal_qty="BAL =(BAL - $qty_difference)";
			}
			else
			{
				$condition_allot_qty="allot_qty =(allot_qty+0)";
				$condition_bal_qty="BAL =(BAL +0)";
			}
			//if($reporting_to!='')
			//{
			//For allot qty update	
			if(in_array($sauda_allocation_emp_code,$emp_lower_array))
			{
				$sql_update_allot_qty = "UPDATE sauda_allocation SET ".$condition_allot_qty." WHERE emp_code = '".$emp_code."' AND product_filter_code = '".$sauda_allocation_product_filter_code."'";
				$res_update_allot_qty = mysqli_query($link,$sql_update_allot_qty);
			}
			
			//For balance update
			   if($immediate_top!='E0077' || $immediate_top!='E0076'){
				$sql_update_bal_qty = "UPDATE sauda_allocation SET ".$condition_bal_qty." WHERE emp_code = '".$immediate_top."' AND product_filter_code = '".$sauda_allocation_product_filter_code."'";
				$res_update_bal_qty = mysqli_query($link,$sql_update_bal_qty);
			   }
			   else{
				   $sql_update_bal_qty = "UPDATE sauda_allocation SET  BAL =(BAL + $sauda_allocation_qty) WHERE emp_code = '".$immediate_top."' 
				   							AND product_filter_code = '".$sauda_allocation_product_filter_code."'";
				   $res_update_bal_qty = mysqli_query($link,$sql_update_bal_qty);
			   }
			//}
			/*else
			{
				$sauda_balance_qty=sauda_balance_qty($emp_code,$sauda_allocation_qty);
				$sql_update_allot_qty = "UPDATE sauda_allocation SET '".$condition_allot_qty."',
										 qty = '".$sauda_allocation_qty."',
										 BAL = '".$sauda_balance_qty."'
									     WHERE emp_code = '".$emp_code."' AND product_filter_code = '".$sauda_allocation_product_filter_code."'";
				$res_update_sauda_allocation = mysqli_query($link,$sql_update_sauda_allocation);
			}*/
		}
		else
		{
		   $sql_insert_allot_qty = "INSERT INTO sauda_allocation SET 
									emp_code = '".$emp_code."', 
									allot_qty ='".$sauda_allocation_qty."',
									product_filter_code = '".$sauda_allocation_product_filter_code."', 
									qty = '".$sauda_allocation_qty."',
									BAL = '".$sauda_allocation_qty."'";
		   $res_insert_allot_qty = mysqli_query($link,$sql_insert_allot_qty);
		}
		 /*$emp_upper_hierarchy = return_employee_upper_hierarchy($sauda_allocation_emp_code);
		 $emp_upper_hierarchy=str_replace("'","",$emp_upper_hierarchy);
		 $emp_upper_hierarchy_array=explode(',',$emp_upper_hierarchy);
		 foreach($emp_upper_hierarchy_array as $emp_upper_hierarchy_val)
		 {
			 
		 }*/
		//End of sauda allocation table alloted qty
		//For addition sauda allocation log
		$sqlinsertsaudaallocationlog="INSERT INTO sauda_allocation_log SET allocation_id	='".$sauda_allocation_trans_id."',
								 allocation_date	   ='".$sauda_allocation_date."',
								 emp_code			   ='".$sauda_allocation_emp_code."',
								 product_filter_code   ='".$sauda_allocation_product_filter_code."',
								 qty				   ='".$sauda_allocation_qty."'";
		if(mysqli_query($link,$sqlinsertsaudaallocationlog))
		{
			$flag=5;
			//$sqlchksaudaallocation="SELECT qty FROM sauda_allocation WHERE ";
		}
		else
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}
	}
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
?>