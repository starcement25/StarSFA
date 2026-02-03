<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_REQUEST['emp_code'];
$current_date=date('Y-m-d');
$sqlquery="select * FROM emp_datewise_route_allocation WHERE emp_code='".$emp_code."' AND allocation_date='".$current_date."'";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$contentsrowcolumn  =$count.'¥'.'3';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowallocation = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowallocation['emp_code']!='')?$rowallocation['emp_code']: ' ')."^";
			$contents  .= (($rowallocation['route_code']!='')?$rowallocation['route_code']: ' ')."^";
			$contents  .= (($rowallocation['allocation_date']!='')?$rowallocation['allocation_date']: ' ');
			$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);	
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/emp-datewise-route-allocation-txt.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=emp_datewise_route_allocation.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
