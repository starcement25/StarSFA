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
$sqlquery="SELECT * FROM  complaint_master WHERE ".$emp_hierarchy_condition.$login_condition." ORDER BY 	customer_name ASC";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'49';
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
			$contents  = (($rowcomplaint['complaint_id']!='')?$rowcomplaint['complaint_id']: ' ')."^";
			$contents  .= (($rowcomplaint['emp_code']!='')?$rowcomplaint['emp_code']: ' ')."^";
			$contents  .= (($rowcomplaint['complaint_segment']!='')?$rowcomplaint['complaint_segment']: ' ')."^";
			$contents  .= (($rowcomplaint['complaint_category']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcomplaint['complaint_category'])): ' ')."^";
			$contents  .= (($rowcomplaint['complaint_receiver']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcomplaint['complaint_receiver'])): ' ')."^";
			$contents  .= (($rowcomplaint['date_of_first_visit_to_customer']!='')?$rowcomplaint['date_of_first_visit_to_customer']: ' ')."^";
			$contents  .= (($rowcomplaint['first_visit_made_sales_team_name']!='')?$rowcomplaint['first_visit_made_sales_team_name']: ' ')."^";
			$contents  .= (($rowcomplaint['FIR_submitted']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcomplaint['FIR_submitted'])): ' ')."^";
			$contents  .= (($rowcomplaint['first_visit_made_by_TE_TM']!='')?$rowcomplaint['first_visit_made_by_TE_TM']: ' ')."^";
			$contents  .= (($rowcomplaint['customer_name']!='')?$rowcomplaint['customer_name']: ' ')."^";
			$contents  .= (($rowcomplaint['customer_contact_no']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcomplaint['customer_contact_no'])): ' ')."^";
			$contents  .= (($rowcomplaint['customer_address_pin']!='')?$rowcomplaint['customer_address_pin']: '')."^";
			$contents  .= (($rowcomplaint['type_of_complaint']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcomplaint['type_of_complaint'])): ' ')."^";
			$contents  .= (($rowcomplaint['remarks_others']!='')?$rowcomplaint['remarks_others']: ' ')."^";
			$contents  .= (($rowcomplaint['nature_of_complaint']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcomplaint['nature_of_complaint'])): ' ')."^";
			$contents  .= (($rowcomplaint['complaint_efforts_details']!='')?$rowcomplaint['complaint_efforts_details']: ' ')."^";
			$contents  .= (($rowcomplaint['type_of_cement']!='')?$rowcomplaint['type_of_cement']: ' ')."^";
			$contents  .= (($rowcomplaint['name_of_the_plant']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcomplaint['name_of_the_plant'])): ' ')."^";
			$contents  .= (($rowcomplaint['batch_no']!='')?$rowcomplaint['batch_no']: ' ')."^";
			$contents  .= (($rowcomplaint['date_of_supply']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcomplaint['date_of_supply'])): ' ')."^";
			$contents  .= (($rowcomplaint['no_of_bags_purchased']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcomplaint['no_of_bags_purchased'])): ' ')."^";
			$contents  .= (($rowcomplaint['date_of_usage']!='')?$rowcomplaint['date_of_usage']: ' ')."^";
			$contents  .= (($rowcomplaint['supplied_by']!='')?$rowcomplaint['supplied_by']: ' ')."^";
			$contents  .= (($rowcomplaint['current_status_of_site']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcomplaint['current_status_of_site'])): ' ')."^";
			$contents  .= (($rowcomplaint['storage_condition_of_cement']!='')?$rowcomplaint['storage_condition_of_cement']: ' ')."^";
			$contents  .= (($rowcomplaint['weight_of_cement_bags']!='')?$rowcomplaint['weight_of_cement_bags']: ' ')."^";
			$contents  .= (($rowcomplaint['quality_coarse_aggregates']!='')?$rowcomplaint['quality_coarse_aggregates']: ' ')."^";
			$contents  .= (($rowcomplaint['quality_fine_aggregates']!='')?$rowcomplaint['quality_fine_aggregates']: ' ')."^";
			$contents  .= (($rowcomplaint['quality_of_water']!='')?$rowcomplaint['quality_of_water']: ' ')."^";
			$contents  .= (($rowcomplaint['quality_of_admixture']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcomplaint['quality_of_admixture'])): ' ')."^";
			$contents  .= (($rowcomplaint['degree_quality_control']!='')?$rowcomplaint['degree_quality_control']: ' ')."^";
			$contents  .= (($rowcomplaint['investigation_observations']!='')?$rowcomplaint['investigation_observations']: ' ')."^";
			$contents  .= (($rowcomplaint['root_cause_analysis']!='')?$rowcomplaint['root_cause_analysis']: ' ')."^";
			$contents  .= (($rowcomplaint['corrections_suggested_technical_team']!='')?$rowcomplaint['corrections_suggested_technical_team']: ' ')."^";
			$contents  .= (($rowcomplaint['customer_is_convinced']!='')?$rowcomplaint['customer_is_convinced']: ' ')."^";
			$contents  .= (($rowcomplaint['star_cement_reused']!='')?$rowcomplaint['star_cement_reused']: ' ')."^";
			$contents  .= (($rowcomplaint['action_plan_not_convinced']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcomplaint['action_plan_not_convinced'])): ' ')."^";
			$contents  .= (($rowcomplaint['follow_up_plan']!='')?$rowcomplaint['follow_up_plan']: ' ')."^";
			$contents  .= (($rowcomplaint['managers_recommendation']!='')?$rowcomplaint['managers_recommendation']: ' ')."^";
			$contents  .= (($rowcomplaint['complaint_status']!='')?$rowcomplaint['complaint_status']: ' ')."^";
			$contents  .= (($rowcomplaint['expected_date_of_closing']!='')?$rowcomplaint['expected_date_of_closing']: ' ')."^";
			$contents  .= (($rowcomplaint['closed_date']!='')?$rowcomplaint['closed_date']: ' ')."^";
			$contents  .= (($rowcomplaint['remarks']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcomplaint['remarks'])): ' ')."^";
			$contents  .= (($rowcomplaint['FIR_image']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcomplaint['FIR_image'])): ' ')."^";
			$contents  .= (($rowcomplaint['CCR_image']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcomplaint['CCR_image'])): ' ')."^";
			$contents  .= (($rowcomplaint['complaint_image']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcomplaint['complaint_image'])): ' ')."^";
			$contents  .= (($rowcomplaint['Bill2_image']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcomplaint['Bill2_image'])): ' ')."^";
			$contents  .= (($rowcomplaint['branch']!='')?$rowcomplaint['branch']: ' ')."^";
			$contents  .= (($rowcomplaint['district']!='')?$rowcomplaint['district']: ' ');
			

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
			$datacontents = '0'.'¥'.'49';
		}
	}
	
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/complaint-master-txt-incremental-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=complaint_master.txt");
	print "$datacontents"; 	
	mysqli_close($link);	
?>
