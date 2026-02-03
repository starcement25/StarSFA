<?php

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);

$date=date('Y-m-d');
$time=date('H:i:s');
$contentsdatetime = $date.'€'.$time."\n";



$from_date = $_REQUEST['from'];
$to_date = $_REQUEST['to'];
$from_dateArray = explode('/', $from_date);
$to_dateArray = explode('/', $to_date);
$from_date = $from_dateArray[2].$from_dateArray[1].$from_dateArray[0];
$to_date = $to_dateArray[2].$to_dateArray[1].$to_dateArray[0];
$from_date = date('Y-m-d', strtotime($from_date));
$to_date = date('Y-m-d', strtotime($to_date));


$sql_survey_output = "SELECT survey_id,row_id,value FROM survey_output where survey_id IN(SELECT survey_id FROM survey_output WHERE row_id ='RA417' AND value='".$emp_code."')";

            if ($from_date != '' && $to_date != '') {
                $date_condition = " AND DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%Y-%m-%d') >='" . $from_date . "' AND 
					  	DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%Y-%m-%d') <='" . $to_date . "'";
					  	
					  	
					  	
                $sql_survey_output = "SELECT survey_id,row_id,value FROM survey_output SO where survey_id IN(SELECT survey_id FROM survey_output WHERE row_id ='RA417' AND value='".$emp_code."' $date_condition)";
					  	
            }

//echo $sql_survey_output;
//echo $sql_survey_output."--".$from_date;exit();

$res_survey_output = mysqli_query($link,$sql_survey_output);
$survey_id_array=array();
while($row_survey_output = mysqli_fetch_assoc($res_survey_output)){
	$survey_id = $row_survey_output['survey_id'];
		$row_id = $row_survey_output['row_id'];
	if(!in_array($survey_id,$survey_id_array))
	{
		array_push($survey_id_array,$survey_id);
	}
	if($row_id=='RA164')
	{
		${'customer_name'.$row_id.$survey_id}=$row_survey_output['value'];
	}
	if($row_id=='RA168')
	{
		${'customer_contact_no'.$row_id.$survey_id}=$row_survey_output['value'];
	}
	if($row_id=='RA169')
	{
		${'full_address'.$row_id.$survey_id}=$row_survey_output['value'];	
	}
	if($row_id=='RA414')
	{
		${'customer_code'.$row_id.$survey_id}=$row_survey_output['value'];		
	}
	if($row_id=='RA411')
	{
		${'product'.$row_id.$survey_id}=$row_survey_output['value'];	
	}
	if($row_id=='RA413')
	{
		${'no_bags'.$row_id.$survey_id}=$row_survey_output['value'];	
	}
	if($row_id=='RA412')
	{
		${'delivery_date'.$row_id.$survey_id}=$row_survey_output['value'];	
	}
	if($row_id=='RA176')
	{
	    
		${'visit_type'.$row_id.$survey_id}=str_replace('#',' , ',$row_survey_output['value']);	
	}
	
	
}
$linesurveydetails = '';
if(count($survey_id_array) > 0){
foreach($survey_id_array as $survey_id_val)
{
	$sql_survey_status = "SELECT status,actual_date_delivery,delivery_remarks,reason_not_delivery FROM survey_header where survey_id='".$survey_id_val."'";
	$res_survey_status = mysqli_query($link,$sql_survey_status);
	$row_survey_status =mysqli_fetch_assoc($res_survey_status);
	$survey_status=$row_survey_status['status'];
	$actual_date_delivery=$row_survey_status['actual_date_delivery'];
	$delivery_remarks=$row_survey_status['delivery_remarks'];
	$reason_not_delivery=$row_survey_status['reason_not_delivery'];
	
	if($actual_date_delivery=='') $actual_date_delivery='N/A';
	if($delivery_remarks=='') $delivery_remarks='N/A';
	if($reason_not_delivery=='') $reason_not_delivery='N/A';
	
	$row_id_name='RA164';
	$row_id_contact='RA168';
	$row_id_address='RA169';
	$row_id_customer_code='RA414';
	$row_id_product='RA411';
	$row_id_no_of_bags='RA413';
	$row_id_delivery_date='RA412';
	$row_id_visit_type='RA176';
	
	
	$sql_survey_dealer = "SELECT customer_name FROM customer_master where dns_customer_code='".${'customer_code'.$row_id_customer_code.$survey_id_val}."'";
	$res_survey_dealer = mysqli_query($link,$sql_survey_dealer);
	$row_survey_dealer =mysqli_fetch_assoc($res_survey_dealer);
	$survey_dealer_name=$row_survey_dealer['customer_name'];
	
	$valuesurveydetails  = $survey_id_val."^";
	$valuesurveydetails .= ${'customer_name'.$row_id_name.$survey_id_val}."^";
	$valuesurveydetails .= ${'customer_contact_no'.$row_id_contact.$survey_id_val}."^";
	$valuesurveydetails .= ${'full_address'.$row_id_address.$survey_id_val}."^";
	$valuesurveydetails .=$survey_dealer_name."^";
	$valuesurveydetails .= ${'customer_code'.$row_id_customer_code.$survey_id_val}."^";
	$valuesurveydetails .= ${'product'.$row_id_product.$survey_id_val}."^";
	$valuesurveydetails .= ${'no_bags'.$row_id_no_of_bags.$survey_id_val}."^";
	$valuesurveydetails .= ${'delivery_date'.$row_id_delivery_date.$survey_id_val}."^";
	$valuesurveydetails .= ${'visit_type'.$row_id_visit_type.$survey_id_val}."^";
	$valuesurveydetails .= $survey_status."^";
	$valuesurveydetails .= $actual_date_delivery."^";
	$valuesurveydetails .= $delivery_remarks."^";
	$valuesurveydetails .= $reason_not_delivery;
	
	
	$linecontents  .= $valuesurveydetails."\n";
	
	
}
 $contentsrowcolumn=count($survey_id_array).'¥'.'14';
$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
}
else
	{
		
			$datacontents = '0'.'¥'.'14';
	}
	
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=site_leade_approval.txt");
	print "$datacontents"; 	
	mysqli_close($link);	
?>