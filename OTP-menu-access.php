<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_REQUEST['emp_code'];
$sqldesignation="SELECT designation from employee_master WHERE emp_code='".$emp_code."'";
$rsdesignation=mysqli_query($link,$sqldesignation);
$rowdesignation=mysqli_fetch_assoc($rsdesignation);
$designation=$rowdesignation['designation'];

$sqlquery="select * from OTP_menu_access WHERE emp_code='".$emp_code."'";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	
	$contentsrowcolumn  =$count.'¥'.'1';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowaccess = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowaccess['accessible_menu']!='')?$rowaccess['accessible_menu']: ' ');
			$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);	
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/OTP-menu-access.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=menu_access.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
