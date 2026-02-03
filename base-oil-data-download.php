<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', '-1');
set_time_limit(1000);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$sqlbaseoil= "SELECT * FROM (SELECT base_oil,mandatory,lower_limit,upper_limit,sl_no FROM base_oil_master
				 WHERE acedns='Y' ORDER BY download_time DESC) AS SAT GROUP BY 1 ORDER BY 5 ASC";
$rsbaseoil = mysqli_query($link,$sqlbaseoil);
$count=mysqli_num_rows($rsbaseoil);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'5';
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
		while($rowbaseoil = mysqli_fetch_assoc($rsbaseoil))
		{
			$base_oil=$rowbaseoil['base_oil'];
			$mandatory=$rowbaseoil['mandatory'];
			$lower_limit=$rowbaseoil['lower_limit'];
			$upper_limit=$rowbaseoil['upper_limit'];
			
			$sqlprevoilrate="SELECT oils_rate FROM pricing_detials_formulation WHERE oils='".$base_oil."' ORDER BY datetime DESC LIMIT 0,1";
			$rsprevoilrate=mysqli_query($link,$sqlprevoilrate); 
			$rowrsprevoilrate=mysqli_fetch_assoc($rsprevoilrate);
			$oils_rate_value=$rowrsprevoilrate['oils_rate'];
			
			$contents  = (($base_oil!='')?$base_oil: ' ')."^";
			$contents  .= (($mandatory!='')?$mandatory: ' ')."^";
			$contents  .= (($lower_limit!='')?$lower_limit: ' ')."^";
			$contents  .= (($upper_limit!='')?$upper_limit: ' ')."^";
			$contents  .= (($oils_rate_value!='')?$oils_rate_value: ' ');
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
	$url =APICALLLOGURL. "/base-oil-data-download.php?nick_name=$nick_name";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=base_oil.txt");
	print "$datacontents";	
	mysqli_close($link);
?>
