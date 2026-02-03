<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];

	$employee_hierarchy=return_employee_upper_hierarchy($emp_code);
	//$emp_hierarchy_condition=' emp_code IN('.$employee_hierarchy.')';
	$sqlquery="SELECT * FROM vendor_details WHERE vendor_code 
			  IN(SELECT dns_emp_code FROM employee_master WHERE emp_code IN(".$employee_hierarchy.")) AND acedns='Y'";
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'15';
	if($count>0){
		/*$date=date('Y-m-d');
		$time=date('h:i:s');
		$contentsdatetime = $date.'€'.$time."\n";*/
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		
		while($rowvendordetails = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowvendordetails['state']!='')?$rowvendordetails['state']: ' ')."^";
			$contents  .= (($rowvendordetails['area']!='')?$rowvendordetails['area']: ' ')."^";
			$contents  .= (($rowvendordetails['city']!='')?$rowvendordetails['city']: ' ')."^";
			$contents  .= (($rowvendordetails['location']!='')?$rowvendordetails['location']: ' ')."^";
			$contents  .= (($rowvendordetails['facing']!='')?$rowvendordetails['facing']: ' ')."^";
			$contents  .= (($rowvendordetails['media']!='')?$rowvendordetails['media']: ' ')."^";
			$contents  .= (($rowvendordetails['type']!='')?$rowvendordetails['type']: ' ')."^";
			$contents  .= (($rowvendordetails['L_R']!='')?$rowvendordetails['L_R']: ' ')."^";
			$contents  .= (($rowvendordetails['T_B']!='')?$rowvendordetails['T_B']: ' ')."^";
			$contents  .= (($rowvendordetails['qty']!='')?$rowvendordetails['qty']: ' ')."^";
			$contents  .= (($rowvendordetails['fascia']!='')?$rowvendordetails['fascia']: ' ')."^";
			$contents  .= (($rowvendordetails['sq_ft']!='')?$rowvendordetails['sq_ft']: ' ')."^";
			$contents  .= (($rowvendordetails['vendor']!='')?$rowvendordetails['vendor']: ' ')."^";
			$contents  .= (($rowvendordetails['vendor_code']!='')?$rowvendordetails['vendor_code']: ' ')."^";
			$contents  .= (($rowvendordetails['location_facing']!='')?$rowvendordetails['location_facing']: ' ');
			$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=vendor_details.txt");
	print "$datacontents"; 	
?>
