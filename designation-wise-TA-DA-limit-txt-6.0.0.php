<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$emp_code=$_REQUEST['emp_code'];
$sqldesignation="SELECT designation FROM employee_master where emp_code='".$emp_code."'";
$rsdesignation=mysqli_query($link,$sqldesignation);
$rowdesignation=mysqli_fetch_assoc($rsdesignation);
$designation=$rowdesignation['designation'];

	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	
	$hour=gmdate('H',strtotime('+330 minute'));
	$minute=gmdate('i',strtotime('+330 minute'));
	$second=gmdate('s',strtotime('+330 minute'));
	//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
	$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
	$linecontents='';
	
	$sqlquery="SELECT fuel_allowance,air_facility,rail_facility,fooding_in_station,fooding_night_stay,lodging_per_day,own_arrangement_per_day  
				FROM 
				designation_wise_TA_DA WHERE designation='".$designation."'";		
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	$cnt=1;
	if($count>0){
		while($rowTADAlimit = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowTADAlimit['fuel_allowance']!='')?$rowTADAlimit['fuel_allowance']: ' ')."^";
			$contents  .= (($rowTADAlimit['air_facility']!='')?$rowTADAlimit['air_facility']: ' ')."^";
			$contents  .= (($rowTADAlimit['rail_facility']!='')?$rowTADAlimit['rail_facility']: ' ')."^";
			$contents  .= (($rowTADAlimit['fooding_in_station']!='')?$rowTADAlimit['fooding_in_station']: ' ')."^";
			$contents  .= (($rowTADAlimit['fooding_night_stay']!='')?$rowTADAlimit['fooding_night_stay']: ' ')."^";
			$contents  .= (($rowTADAlimit['lodging_per_day']!='')?$rowTADAlimit['lodging_per_day']: ' ')."^";
			$contents  .= (($rowTADAlimit['own_arrangement_per_day']!='')?$rowTADAlimit['own_arrangement_per_day']: ' ');
			
			$linecontents  .= $contents."\n";
			$cnt++;
		}
		$contentsrowcolumn=$count.'¥'.'7';
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
  $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/designation-wise-TA-DA-limit-txt-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	
header("Content-type: application/text"); 
header("Content-Disposition: attachment; filename=TADA_limit.txt");
print "$datacontents"; 		
?>
