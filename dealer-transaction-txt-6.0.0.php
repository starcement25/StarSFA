<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'] ?? null;
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
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	
	$hour=gmdate('H',strtotime('+330 minute'));
	$minute=gmdate('i',strtotime('+330 minute'));
	$second=gmdate('s',strtotime('+330 minute'));
	//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
	$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
	$linecontents='';
	$sqlquery="SELECT * FROM dealer_transaction WHERE ".$emp_hierarchy_condition." AND acedns='Y' ORDER BY download_time DESC";
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	$cnt=1;
	if($count>0){
		while($rowdealertrans = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowdealertrans['customer_code']!='')?$rowdealertrans['customer_code']: ' ')."^";
			$contents  .= (($rowdealertrans['customer_name']!='')?$rowdealertrans['customer_name']: ' ')."^";
			$contents  .= (($rowdealertrans['emp_code']!='')?$rowdealertrans['emp_code']: ' ')."^";
			$contents  .= (($rowdealertrans['contact_person_name']!='')?$rowdealertrans['contact_person_name']: ' ')."^";
			$contents  .= (($rowdealertrans['contact_person_phone']!='')?$rowdealertrans['contact_person_phone']: ' ')."^";
			$contents  .= (($rowdealertrans['YTD_sales']!='')?$rowdealertrans['YTD_sales']: ' ')."^";
			$contents  .= (($rowdealertrans['dues']!='')?$rowdealertrans['dues']: ' ')."^";
			$contents  .= (($rowdealertrans['legends_earned_points']!='')?$rowdealertrans['legends_earned_points']: ' ')."^";
			$contents  .= (($rowdealertrans['legends_tier']!='')?$rowdealertrans['legends_tier']: ' ')."^";
			$contents  .= (($rowdealertrans['legends_total_points']!='')?$rowdealertrans['legends_total_points']: ' ')."^";
			$contents  .= (($rowdealertrans['check_in_date']!='')?$rowdealertrans['check_in_date']: ' ')."^";
			$contents  .= (($rowdealertrans['comments']!='')?$rowdealertrans['comments']: ' ')."^";
			$contents  .= (($rowdealertrans['activity']!='')?$rowdealertrans['activity']: ' ');
		
			$linecontents  .= $contents."\n";
			$cnt++;
		}
		$contentsrowcolumn=$count.'¥'.'13';
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
header("Content-type: application/text"); 
header("Content-Disposition: attachment; filename=dealer_transaction.txt");
print "$datacontents"; 		
?>
