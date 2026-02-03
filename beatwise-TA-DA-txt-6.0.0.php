<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition=" emp_code='".$emp_code."'";
}
	$sql_TA_DA = "SELECT * FROM beatwise_TA_DA WHERE ".$emp_hierarchy_condition." AND acedns='Y' ORDER BY emp_name ASC";
	$res_TA_DA = mysqli_query($link,$sql_TA_DA);
	$count=mysqli_num_rows($res_TA_DA);
	//print_r($vertical_array);
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		$countappraisal=0;
		while($row_TA_DA = mysqli_fetch_assoc($res_TA_DA)){
		$emp_code = $row_TA_DA['emp_code'];
		$emp_name = $row_TA_DA['emp_name'];
		$reporting_to = $row_TA_DA['reporting_to'];
		$reporting_to_name = $row_TA_DA['reporting_to_name'];
		$route_code = $row_TA_DA['route_code'];
		$route_name = $row_TA_DA['route_name'];
		$HQ_EX_OS = $row_TA_DA['HQ_EX_OS'];
		$DA = $row_TA_DA['DA'];
		$distance = $row_TA_DA['distance'];
		$TA = $row_TA_DA['TA'];
		$acedns = $row_TA_DA['acedns'];
			 
			$contents  = (($emp_code!='')?$emp_code: ' ')."^";
			$contents  .= (($emp_name!='')?trim(preg_replace('/[\r\n]+/', '',$emp_name)): ' ')."^";
			$contents  .= (($reporting_to!='')?$reporting_to: ' ')."^";
			$contents  .= (($reporting_to_name!='')?trim(preg_replace('/[\r\n]+/', '',$reporting_to_name)): ' ')."^";
			$contents  .= (($route_code!='')?$route_code: ' ')."^";
			$contents  .= (($route_name!='')?trim(preg_replace('/[\r\n]+/', '',$route_name)): ' ')."^";
			$contents  .= (($HQ_EX_OS!='')?$HQ_EX_OS: ' ')."^";
			$contents  .= (($DA!='')?$DA: ' ')."^";
			$contents  .= (($distance!='')?$distance: ' ')."^";
			$contents  .= (($TA!='')?$TA: ' ')."^";
			$contents  .= (($acedns!='')?$acedns: ' ');
			$linecontents  .= $contents."\n";
			$countappraisal++;
		}
			$contentsrowcolumn=$count.'¥'.'11';
			$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
		}
		else
		{
			$datacontents = '0'.'¥'.'0';
		}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/beatwise-TA-DA-txt-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=beatwise-TA-DA.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
