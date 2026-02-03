<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$oils_val=array('MCX_open_rate','MCX_close_rate');
$oils_val_string = "'" .implode("', '", $oils_val) . "'";
	$cnt=1;
	$contentsrowcolumn  ='2'.'¥'.'2';
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
		for($i=0;$i<count($oils_val);$i++ )
			{
			$base_oil=$rowbaseoil['base_oil'];
			$mandatory=$rowbaseoil['mandatory'];
			$lower_limit=$rowbaseoil['lower_limit'];
			$upper_limit=$rowbaseoil['upper_limit'];
			
			$sqlprevoilrate="SELECT $oils_val[$i] AS oils_rate  FROM crude_oil_rate WHERE 1 ORDER BY create_date DESC LIMIT 0,1";
			$rsprevoilrate=mysqli_query($link,$sqlprevoilrate); 
			$rowrsprevoilrate=mysqli_fetch_assoc($rsprevoilrate);
			$oils_rate_value=$rowrsprevoilrate['oils_rate'];
			
			$contents  = (($oils_val[$i]!='')?$oils_val[$i]: ' ')."^";
			$contents  .= (($oils_rate_value!='')?$oils_rate_value: ' ');
			$linecontents  .= $contents."\n";
			}
		if($linecontents !='')
		{	
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
				$datacontents = '0'.'¥'.'2';
			}
		}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url =APICALLLOGURL. "/base-oil-data-download-MCX.php?nick_name=$nick_name";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=base_oil_MCX.txt");
	print "$datacontents";	
	mysqli_close($link);
?>
