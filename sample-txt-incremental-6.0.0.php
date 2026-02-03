<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$emp_code=$_REQUEST['emp_code'];

$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);
if($incremental_download=='no')
{
	$login_condition=" acedns='Y'";
}
else
{
	$login_condition=" UNIX_TIMESTAMP(creation_date) > UNIX_TIMESTAMP('".$last_update_time."') ";
}
$sqlsampledetails = "SELECT reference_no,sample_photo,acedns FROM sample_master WHERE ".$login_condition;	
$rssampledetails= mysqli_query($link,$sqlsampledetails);
$count=mysqli_num_rows($rssampledetails);		

if($count >0){		
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowsample = mysqli_fetch_assoc($rssampledetails))
		{
			$sample_url='/sample/'.$rowsample['sample_photo'];
			$contents  = (($rowsample['reference_no']!='')?$rowsample['reference_no']: ' ')."^";
			$contents  .= (($rowsample['sample_photo']!='')?$sample_url: ' ')."^";
			$contents  .= (($rowsample['acedns']!='')?$rowsample['acedns']: ' ');
			
			$linecontents  .= $contents."\n";
		}
		$contentsrowcolumn=$count.'¥'.'3';
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/sample-txt-incremental-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	
header("Content-type: application/text"); 
header("Content-Disposition: attachment; filename=sample_info.txt");
print "$datacontents"; 		
?>
