<?php

ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);

ini_set('memory_limit', '-1');
set_time_limit(1000);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];
//$emp_code='100017206';
$sqlconversion= "SELECT * FROM (SELECT prod_code,mapped_prod_code,is_flash,flash_name FROM product_unit_coversion_matrix 
			WHERE acedns='Y' ORDER BY download_time DESC) AS SAT GROUP BY 1,2 ORDER BY 2 DESC";
$rsconversion = mysqli_query($link,$sqlconversion);
$count=mysqli_num_rows($rsconversion);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'4';
	if($count>0){
		/*$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time."\n";*/
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowconversion = mysqli_fetch_assoc($rsconversion))
		{
			$prod_code=$rowconversion['prod_code'];
			$mapped_prod_code=$rowconversion['mapped_prod_code'];
			$is_flash=$rowconversion['is_flash'];
			$flash_name=$rowconversion['flash_name'];
			
			$contents  = (($prod_code!='')?$prod_code: ' ')."^";
			$contents  .= (($mapped_prod_code!='')?$mapped_prod_code: ' ')."^";
			$contents  .= (($is_flash!='')?$is_flash: ' ')."^";
			$contents  .= (($flash_name!='')?$flash_name: ' ');
			$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$last_update_time=str_replace('?','',$last_update_time);
		$data_download_time=str_replace('?','',$data_download_time);
		if(strtotime($data_download_time)>=strtotime($last_update_time))
		{
			$datacontents = '0'.'¥'.'0';
		}
		else
		{
			$datacontents = '0'.'¥'.'4';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url =APICALLLOGURL. "/conversion-data-download-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=conversion.txt");
	print "$datacontents";	
	mysqli_close($link);
?>
