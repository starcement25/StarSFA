<?php
 //ini_set('display_errors', 1);
 //ini_set('display_startup_errors', 1);
 //error_reporting(E_ALL); 
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
$sqlquery="SELECT * FROM  mtl_testing_format WHERE ".$emp_hierarchy_condition.$login_condition." ORDER BY 	customer_name ASC";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'57';
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
			$contents = ""; // Initialize an empty string

$contents .= (($rowcomplaint['mtl_testing_format_id'] != '') ? $rowcomplaint['mtl_testing_format_id'] : ' ') . "^";
$contents .= (($rowcomplaint['mtl_no'] != '') ? $rowcomplaint['mtl_no'] : ' ') . "^";
$contents .= (($rowcomplaint['emp_code'] != '') ? $rowcomplaint['emp_code'] : ' ') . "^";
$contents .= (($rowcomplaint['region'] != '') ? $rowcomplaint['region'] : ' ') . "^";
$contents .= (($rowcomplaint['branch'] != '') ? $rowcomplaint['branch'] : ' ') . "^";
$contents .= (($rowcomplaint['district'] != '') ? $rowcomplaint['district'] : ' ') . "^";
$contents .= (($rowcomplaint['customer_name'] != '') ? $rowcomplaint['customer_name'] : ' ') . "^";
$contents .= (($rowcomplaint['contact_number'] != '') ? $rowcomplaint['contact_number'] : ' ') . "^";
$contents .= (($rowcomplaint['full_address'] != '') ? $rowcomplaint['full_address'] : ' ') . "^";
$contents .= (($rowcomplaint['petty_contractor_mason_name'] != '') ? $rowcomplaint['petty_contractor_mason_name'] : ' ') . "^";
$contents .= (($rowcomplaint['petty_contractor_mason_contact_no'] != '') ? $rowcomplaint['petty_contractor_mason_contact_no'] : ' ') . "^";
$contents .= (($rowcomplaint['consulting_engineer_name'] != '') ? $rowcomplaint['consulting_engineer_name'] : ' ') . "^";
$contents .= (($rowcomplaint['consulting_engineer_no'] != '') ? $rowcomplaint['consulting_engineer_no'] : ' ') . "^";
$contents .= (($rowcomplaint['site_segment'] != '') ? $rowcomplaint['site_segment'] : ' ') . "^";
$contents .= (($rowcomplaint['service_category'] != '') ? $rowcomplaint['service_category'] : ' ') . "^";
$contents .= (($rowcomplaint['dhalai_date'] != '') ? $rowcomplaint['dhalai_date'] : ' ') . "^";
$contents .= (($rowcomplaint['type_of_construction'] != '') ? $rowcomplaint['type_of_construction'] : ' ') . "^";
$contents .= (($rowcomplaint['area_sqr'] != '') ? $rowcomplaint['area_sqr'] : ' ') . "^";
$contents .= (($rowcomplaint['current_stage_construction'] != '') ? $rowcomplaint['current_stage_construction'] : ' ') . "^";
$contents .= (($rowcomplaint['brand_type_cement_used'] != '') ? $rowcomplaint['brand_type_cement_used'] : ' ') . "^";
$contents .= (($rowcomplaint['consumed_till_date_bag'] != '') ? $rowcomplaint['consumed_till_date_bag'] : ' ') . "^";
$contents .= (($rowcomplaint['future_requirement_bag'] != '') ? $rowcomplaint['future_requirement_bag'] : ' ') . "^";
$contents .= (($rowcomplaint['linked_associated_dealer'] != '') ? $rowcomplaint['linked_associated_dealer'] : ' ') . "^";
$contents .= (($rowcomplaint['cover_block_qty'] != '') ? $rowcomplaint['cover_block_qty'] : ' ') . "^";
$contents .= (($rowcomplaint['cover_block_placement_inspection'] != '') ? $rowcomplaint['cover_block_placement_inspection'] : ' ') . "^";
$contents .= (($rowcomplaint['plan'] != '') ? $rowcomplaint['plan'] : ' ') . "^";
$contents .= (($rowcomplaint['structural_drawing'] != '') ? $rowcomplaint['structural_drawing'] : ' ') . "^";
$contents .= (($rowcomplaint['bbs_inspection'] != '') ? $rowcomplaint['bbs_inspection'] : ' ') . "^";
$contents .= (($rowcomplaint['structural_inspection'] != '') ? $rowcomplaint['structural_inspection'] : ' ') . "^";
$contents .= (($rowcomplaint['formwork_inspection'] != '') ? $rowcomplaint['formwork_inspection'] : ' ') . "^";
$contents .= (($rowcomplaint['aggregate_inspection'] != '') ? $rowcomplaint['aggregate_inspection'] : ' ') . "^";
$contents .= (($rowcomplaint['sieve_analysis'] != '') ? $rowcomplaint['sieve_analysis'] : ' ') . "^";
$contents .= (($rowcomplaint['water_ph_test'] != '') ? $rowcomplaint['water_ph_test'] : ' ') . "^";
$contents .= (($rowcomplaint['water_tds_test'] != '') ? $rowcomplaint['water_tds_test'] : ' ') . "^";
$contents .= (($rowcomplaint['silt_test'] != '') ? $rowcomplaint['silt_test'] : ' ') . "^";
$contents .= (($rowcomplaint['mix_proportion_inspection'] != '') ? $rowcomplaint['mix_proportion_inspection'] : ' ') . "^";
$contents .= (($rowcomplaint['ball_test'] != '') ? $rowcomplaint['ball_test'] : ' ') . "^";
$contents .= (($rowcomplaint['slump_test'] != '') ? $rowcomplaint['slump_test'] : ' ') . "^";
$contents .= (($rowcomplaint['cube_test'] != '') ? $rowcomplaint['cube_test'] : ' ') . "^";
$contents .= (($rowcomplaint['cube_test_result_after3days'] != '') ? $rowcomplaint['cube_test_result_after3days'] : ' ') . "^";
$contents .= (($rowcomplaint['cube_test_result_after7days'] != '') ? $rowcomplaint['cube_test_result_after7days'] : ' ') . "^";
$contents .= (($rowcomplaint['cube_test_result_after28days'] != '') ? $rowcomplaint['cube_test_result_after28days'] : ' ') . "^";
$contents .= (($rowcomplaint['cube_test_date'] != '') ? $rowcomplaint['cube_test_date'] : ' ') . "^";
$contents .= (($rowcomplaint['cube_test_after3days'] != '') ? $rowcomplaint['cube_test_after3days'] : ' ') . "^";
$contents .= (($rowcomplaint['cube_test_after7days'] != '') ? $rowcomplaint['cube_test_after7days'] : ' ') . "^";
$contents .= (($rowcomplaint['cube_test_after28days'] != '') ? $rowcomplaint['cube_test_after28days'] : ' ') . "^";
$contents .= (($rowcomplaint['cube_test_done'] != '') ? $rowcomplaint['cube_test_done'] : ' ') . "^";
$contents .= (($rowcomplaint['cube_test_remarks'] != '') ? $rowcomplaint['cube_test_remarks'] : ' ') . "^";
$contents .= (($rowcomplaint['ndt_test'] != '') ? $rowcomplaint['ndt_test'] : ' ') . "^";
$contents .= (($rowcomplaint['slab_supervision'] != '') ? $rowcomplaint['slab_supervision'] : ' ') . "^";
$contents .= (($rowcomplaint['mix_design_concrete'] != '') ? $rowcomplaint['mix_design_concrete'] : ' ') . "^";
$contents .= (($rowcomplaint['mtv_services'] != '') ? $rowcomplaint['mtv_services'] : ' ') . "^";
$contents .= (($rowcomplaint['technical_service_others'] != '') ? $rowcomplaint['technical_service_others'] : ' ') . "^";
$contents .= (($rowcomplaint['gift_given'] != '') ? $rowcomplaint['gift_given'] : ' ') . "^";
$contents .= (($rowcomplaint['yes_service_details'] != '') ? $rowcomplaint['yes_service_details'] : ' ') . "^";
$contents .= (($rowcomplaint['cube_test_done_yn'] != '') ? $rowcomplaint['cube_test_done_yn'] : ' ') . "^";
$contents .= (($rowcomplaint['photo'] != '') ? $rowcomplaint['photo'] : ' ') . " ";


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
			$datacontents = '0'.'¥'.'57';
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
