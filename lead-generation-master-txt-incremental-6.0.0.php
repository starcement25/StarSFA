<?php
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];

$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);
$linecontents='';
if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition='emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="emp_code='".$emp_code."'";
}
if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}
$sqlquery="SELECT * FROM  lead_generation_master WHERE ".$emp_hierarchy_condition.$login_condition." ORDER BY 	party_name ASC";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'29';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));

		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

		while($rowcomplaint = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowcomplaint['lead_generation_id']!='')?$rowcomplaint['lead_generation_id']: ' ')."^";
			$contents  .= (($rowcomplaint['emp_code']!='')?$rowcomplaint['emp_code']: ' ')."^";
			$contents  .= (($rowcomplaint['lead_type']!='')?$rowcomplaint['lead_type']: ' ')."^";
			$contents  .= (($rowcomplaint['party_name']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcomplaint['party_name'])): ' ')."^";
			$contents  .= (($rowcomplaint['branch']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcomplaint['branch'])): ' ')."^";
			$contents  .= (($rowcomplaint['district']!='')?$rowcomplaint['district']: ' ')."^";
			$contents  .= (($rowcomplaint['state']!='')?$rowcomplaint['state']: ' ')."^";
			$contents  .= (($rowcomplaint['qty_req']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcomplaint['qty_req'])): ' ')."^";
			$contents  .= (($rowcomplaint['product_packaging']!='')?$rowcomplaint['product_packaging']: ' ')."^";
			$contents  .= (($rowcomplaint['exp_rate_per_bag']!='')?$rowcomplaint['exp_rate_per_bag']: ' ')."^";
			$contents  .= (($rowcomplaint['contact_person_name']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcomplaint['contact_person_name'])): ' ')."^";
			$contents  .= (($rowcomplaint['contact_number']!='')?$rowcomplaint['contact_number']: '')."^";
			$contents  .= (($rowcomplaint['mail_id']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcomplaint['mail_id'])): ' ')."^";
			$contents  .= (($rowcomplaint['mode']!='')?$rowcomplaint['mode']: ' ')."^";
			$contents  .= (($rowcomplaint['quotation']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcomplaint['quotation'])): ' ')."^";
			$contents  .= (($rowcomplaint['PO']!='')?$rowcomplaint['PO']: ' ')."^";
			$contents  .= (($rowcomplaint['status']!='')?$rowcomplaint['status']: ' ')."^";
			$contents  .= (($rowcomplaint['remarks']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcomplaint['remarks'])): ' ')."^";
			$contents  .= (($rowcomplaint['assigned_to']!='')?$rowcomplaint['assigned_to']: ' ')."^";
			$contents  .= (($rowcomplaint['self_other']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcomplaint['self_other'])): ' ')."^";
			$contents  .= (($rowcomplaint['acc_block_is_required']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcomplaint['acc_block_is_required'])): ' ')."^";
			$contents  .= (($rowcomplaint['category_type_construction']!='')?$rowcomplaint['category_type_construction']: ' ')."^";
			$contents  .= (($rowcomplaint['next_visit_date']!='')?$rowcomplaint['next_visit_date']: ' ')."^";
			$contents  .= (($rowcomplaint['lead_status']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcomplaint['lead_status'])): ' ')."^";
			$contents  .= (($rowcomplaint['current_brand_used']!='')?$rowcomplaint['current_brand_used']: ' ')."^";
			$contents  .= (($rowcomplaint['current_price']!='')?$rowcomplaint['current_price']: ' ')."^";
			$contents  .= (($rowcomplaint['r_timing']!='')?$rowcomplaint['r_timing']: ' ')."^";
			$contents  .= (($rowcomplaint['action_on_lead']!='')?$rowcomplaint['action_on_lead']: ' ')."^";
			$contents  .= (($rowcomplaint['approved_price']!='')?$rowcomplaint['approved_price']: ' ')."";


			$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		//$datacontents = '0'.'¥'.'0';
		$last_update_time=str_replace('?','',$last_update_time);
		$data_download_time=str_replace('?','',$data_download_time);
		if(strtotime($data_download_time)>=strtotime($last_update_time))
		{
			$datacontents = '0'.'¥'.'0';
		}
		else
		{
			$datacontents = '0'.'¥'.'29';
		}
	}


	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/lead-generation-master-txt-incremental-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text");
	header("Content-Disposition: attachment; filename=complaint_master.txt");
	print "$datacontents";
	mysqli_close($link);
?>
