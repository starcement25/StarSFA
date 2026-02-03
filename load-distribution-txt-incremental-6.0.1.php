<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_REQUEST['emp_code'];

$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);

if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(LD.datetime) > UNIX_TIMESTAMP('".$last_update_time."')";
}

	//$sqlquery="SELECT prod_code,qty_truck_load,datetime,transport_mode,truck_load FROM load_distribution WHERE 1 ".$login_condition."";
	$sqlquery="SELECT * FROM (SELECT LD.transport_mode,LD.truck_load,LD.qty_truck_load,LD.datetime,LD.prod_code
								FROM
					load_distribution LD WHERE 1 ".$login_condition." ORDER BY LD.datetime DESC) AS SAT GROUP BY 1,2,5 ORDER BY 4";
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'5';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

		while($rowload = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowload['prod_code']!='')?$rowload['prod_code']: ' ')."^";
			$contents  .= (($rowload['qty_truck_load']!='')?floor($rowload['qty_truck_load']): ' ')."^";
			$contents  .= (($rowload['datetime']!='')?$rowload['datetime']: ' ')."^";
			$contents  .= (($rowload['transport_mode']!='')?$rowload['transport_mode']: ' ')."^";
			$contents  .= (($rowload['truck_load']!='')?$rowload['truck_load']: ' ');
			
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
			$datacontents = '0'.'¥'.'5';
		}
	}

	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/load-distribution-txt-incremental-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=load_distribution.txt");
	print "$datacontents"; 		
?>
