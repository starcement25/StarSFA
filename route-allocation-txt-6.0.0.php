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
		$sqlquerycustomerroute="SELECT DISTINCT EDRA.route_code FROM emp_datewise_route_allocation EDRA WHERE 
								EDRA.emp_code='".$emp_code."' AND EDRA.allocation_date='".$current_date."' 
								AND EDRA.route_code IN(SELECT route_code FROM route_master) ";
		$resultcustomerroute = mysqli_query($link,$sqlquerycustomerroute);
		$countcustomerroute=mysqli_num_rows($resultcustomerroute);
		if($countcustomerroute>0){
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
			
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
			while($rowscustomerroute = mysqli_fetch_assoc($resultcustomerroute))
			{
				$route_code=$rowscustomerroute['route_code'];
				$sqlroute="SELECT  route_name FROM route_master WHERE route_code='".$route_code."'";
				$rsroute=mysqli_query($link,$sqlroute);
				$rowroute=mysqli_fetch_assoc($rsroute);		
	
				$dns_route_code='';
				$route_name=preg_replace('/[\r\n]+/', '',$rowroute['route_name']);
				$contents  = (($route_code!='')?$route_code: ' ')."^";
				$contents  .= (($route_name!='')?$route_name: ' ')."^";
				$contents  .= (($route_code!='')?$route_code: ' '); // For dns route code forcefully given the original route code
				$linecontents  .= $contents."\n";
			}
			$contentsrowcolumn=$countcustomerroute.'¥'.'3';
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
				$datacontents = '0'.'¥'.'3';
			}
		}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/route-allocation-txt-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=route_master_CRM.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
