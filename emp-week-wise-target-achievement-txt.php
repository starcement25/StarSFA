<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' SAEW.emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition=" SAEW.emp_code='".$emp_code."'";
}
$year=gmdate('Y',strtotime('+330 minute'));
$sqlquery="SELECT SAEW.emp_code,SAEW.month,SAEW.year,SAEW.week1_target,SAEW.week1_ach,SAEW.week2_target,
			SAEW.week2_ach,SAEW.week3_target,
			SAEW.week3_ach,SAEW.week4_target,SAEW.week4_ach,SAEW.week5_target,SAEW.week5_ach,SAEW.month_target,
			SAEW.month_ach FROM self_appraisal_emp_week_wise SAEW WHERE  ".$emp_hierarchy_condition."";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowsappraisal = mysqli_fetch_assoc($result))
		{
				$contents  = (($rowsappraisal['emp_code']!='')?$rowsappraisal['emp_code']: ' ')."^";
				$contents  .= (($rowsappraisal['month']!='')?$rowsappraisal['month']: ' ')."^";
				$contents  .= (($rowsappraisal['year']!='')?$rowsappraisal['year']: ' ')."^";
				$contents  .= (($rowsappraisal['week1_target']!='')?$rowsappraisal['week1_target']: ' ')."^";
				$contents  .= (($rowsappraisal['week1_ach']!='')?$rowsappraisal['week1_ach']: ' ')."^";
				$contents  .= (($rowsappraisal['week2_target']!='')?$rowsappraisal['week2_target']: ' ')."^";
				$contents  .= (($rowsappraisal['week2_ach']!='')?$rowsappraisal['week2_ach']: ' ')."^";
				$contents  .= (($rowsappraisal['week3_target']!='')?$rowsappraisal['week3_target']: ' ')."^";
				$contents  .= (($rowsappraisal['week3_ach']!='')?$rowsappraisal['week3_ach']: ' ')."^";
				$contents  .= (($rowsappraisal['week4_target']!='')?$rowsappraisal['week4_target']: ' ')."^";
				$contents  .= (($rowsappraisal['week4_ach']!='')?$rowsappraisal['week4_ach']: ' ')."^";
				$contents  .= (($rowsappraisal['month_target']!='')?$rowsappraisal['month_target']: ' ')."^";
				$contents  .= (($rowsappraisal['month_ach']!='')?$rowsappraisal['month_ach']: ' ');
				$linecontents  .= $contents."\n";
		}
		$contentsrowcolumn=$count.'¥'.'13';

		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/emp-week-wise-target-achievement-txt.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=emp_week_wise_target_ach.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
