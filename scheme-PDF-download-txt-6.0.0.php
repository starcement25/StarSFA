<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	
	$hour=gmdate('H',strtotime('+330 minute'));
	$minute=gmdate('i',strtotime('+330 minute'));
	$second=gmdate('s',strtotime('+330 minute'));
	//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
	$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
	$linecontents='';
	$sqlquery="SELECT PDF_file_name,acedns,start_date,end_date FROM branch_schemes_PDF WHERE  1";
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	$cnt=1;
	if($count>0){
		while($rowschemePDF = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowschemePDF['PDF_file_name']!='')?$rowschemePDF['PDF_file_name']: ' ')."^";
			$contents  .= (($rowschemePDF['acedns']!='')?$rowschemePDF['acedns']: ' ')."^";
			$contents  .= (($rowschemePDF['start_date']!='')?$rowschemePDF['start_date']: ' ')."^";
			$contents  .= (($rowschemePDF['end_date']!='')?$rowschemePDF['end_date']: ' ');
			
			$linecontents  .= $contents."\n";
			$cnt++;
		}
		$contentsrowcolumn=$count.'¥'.'4';
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
header("Content-type: application/text"); 
header("Content-Disposition: attachment; filename=scheme_PDF.txt");
print "$datacontents"; 		
?>
