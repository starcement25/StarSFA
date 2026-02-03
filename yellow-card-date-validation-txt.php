<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];

if(y_card_date_val_ineffictive_emp==$emp_code){
	$datacontents = '0'.'¥'.'0';
}
else
{

$sqlquery="SELECT validation_month,validation_last_date FROM yellow_card_date_validation 
			WHERE validation_month=CONCAT_WS('-',YEAR(CURDATE() - INTERVAL 1 MONTH),LPAD((MONTH(CURDATE()- INTERVAL 1 MONTH)), 2, '0'))";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'2';
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
		
		while($rowcompetitorgroup = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowcompetitorgroup['validation_month']!='')?$rowcompetitorgroup['validation_month']: ' ')."^";
			$contents  .= (($rowcompetitorgroup['validation_last_date']!='')?$rowcompetitorgroup['validation_last_date']: ' ');
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
			$datacontents = '0'.'¥'.'2';
		}
	}
}
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=yellow-card-date-validation.txt");
	print "$datacontents"; 	
?>
