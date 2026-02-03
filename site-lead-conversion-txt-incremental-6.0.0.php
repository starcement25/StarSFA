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
$sqlquery="SELECT * FROM site_lead_conversion_master WHERE ".$emp_hierarchy_condition.$login_condition." ORDER BY 	customer_name ASC";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'34';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

		while($rowsite = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowsite['site_lead_conversion_id']!='')?$rowsite['site_lead_conversion_id']: ' ')."^";
			$contents  .= (($rowsite['emp_code']!='')?$rowsite['emp_code']: ' ')."^";
			$contents  .= (($rowsite['customer_name']!='')?trim(preg_replace('/[\r\n]+/', '',$rowsite['customer_name'])): ' ')."^";
			$contents  .= (($rowsite['customer_contact_no']!='')?trim(preg_replace('/[\r\n]+/', '',$rowsite['customer_contact_no'])): ' ')."^";
			$contents  .= (($rowsite['full_address']!='')?$rowsite['full_address']: ' ')."^";
	$contents  .= (($rowsite['petty_contractor_head_mason_name']!='')?$rowsite['petty_contractor_head_mason_name']: '')."^";
			$contents  .= (($rowsite['petty_contractor_head_mason_contact_no']!='')?trim(preg_replace('/[\r\n]+/', '',$rowsite['petty_contractor_head_mason_contact_no'])): '')."^";
			$contents  .= (($rowsite['engineer_name']!='')?$rowsite['engineer_name']: '')."^";
			$contents  .= (($rowsite['engineer_contact_no']!='')?$rowsite['engineer_contact_no']: '')."^";
			$contents  .= (($rowsite['engineer_regd_in_star_stellar']!='')?trim(preg_replace('/[\r\n]+/', '',$rowsite['engineer_regd_in_star_stellar'])): ' ')."^";
			$contents  .= (($rowsite['site_segment']!='')?$rowsite['site_segment']: '')."^";
			$contents  .= (($rowsite['visit_type']!='')?trim(preg_replace('/[\r\n]+/', '',$rowsite['visit_type'])): ' ')."^";
			$contents  .= (($rowsite['project_segment']!='')?$rowsite['project_segment']: ' ')."^";
			$contents  .= (($rowsite['type_of_construction']!='')?trim(preg_replace('/[\r\n]+/', '',$rowsite['type_of_construction'])): ' ')."^";
			$contents  .= (($rowsite['site_potential_no_of_bags']!='')?$rowsite['site_potential_no_of_bags']: ' ')."^";
			$contents  .= (($rowsite['current_stage_of_construction']!='')?$rowsite['current_stage_of_construction']: ' ')."^";
			$contents  .= (($rowsite['cement_brand_used']!='')?$rowsite['cement_brand_used']: ' ')."^";
			$contents  .= (($rowsite['other_brand']!='')?trim(preg_replace('/[\r\n]+/', '',$rowsite['other_brand'])): '')."^";
			$contents  .= (($rowsite['consumed_till_date_no_of_bags']!='')?trim(preg_replace('/[\r\n]+/', '',$rowsite['consumed_till_date_no_of_bags'])): ' ')."^";
			$contents  .= (($rowsite['estimated_requirement_no_of_bags']!='')?$rowsite['estimated_requirement_no_of_bags']: ' ')."^";
			$contents  .= (($rowsite['meeting_person']!='')?$rowsite['meeting_person']: ' ')."^";
			$contents  .= (($rowsite['decision_maker']!='')?trim(preg_replace('/[\r\n]+/', '',$rowsite['decision_maker'])): ' ')."^";
			$contents  .= (($rowsite['conversion']!='')?$rowsite['conversion']: ' ')."^";
			$contents  .= (($rowsite['product']!='')?$rowsite['product']: ' ')."^";
			$contents  .= (($rowsite['requested_date_of_delivery']!='')?$rowsite['requested_date_of_delivery']: ' ')."^";
			$contents  .= (($rowsite['no_of_bags_ordered']!='')?$rowsite['no_of_bags_ordered']: ' ')."^";
			$contents  .= (($rowsite['lead_forwarded_dealer_rssd_name']!='')?$rowsite['lead_forwarded_dealer_rssd_name']: ' ')."^";
			$contents  .= (($rowsite['actual_date_of_delivery']!='')?trim(preg_replace('/[\r\n]+/', '',$rowsite['actual_date_of_delivery'])): ' ')."^";
			$contents  .= (($rowsite['reason_for_not_delivery']!='')?$rowsite['reason_for_not_delivery']: ' ')."^";
			$contents  .= (($rowsite['reasons_for_non_conversion']!='')?$rowsite['reasons_for_non_conversion']: ' ')."^";
			$contents  .= (($rowsite['other_ remarks']!='')?$rowsite['other_ remarks']: ' ')."^";
			$contents  .= (($rowsite['overall_remarks']!='')?$rowsite['overall_remarks']: '')."^";
			$contents  .= (($rowsite['branch']!='')?$rowsite['branch']: ' ')."^";
			$contents  .= (($rowsite['district']!='')?$rowsite['district']: ' ');

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
			$datacontents = '0'.'¥'.'34';
		}
	}
	
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/site-lead-conversion-txt-incremental-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=site_lead_conversion_master.txt");
	print "$datacontents"; 	
	mysqli_close($link);	
?>
