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
	$sqlqueryemp="SELECT validation_month,validation_last_date FROM yellow_card_date_validation 
			WHERE emp_code='".$emp_code."'";
	$resultemp = mysqli_query($link,$sqlqueryemp);
	$empcount=mysqli_num_rows($resultemp);
	if($empcount >0)
	{
		$rowemp=mysqli_fetch_assoc($resultemp);
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$currentdate =$year.'-'.$month.'-'.$date;
		$validation_last_date_emp=$rowemp['validation_last_date'];
		if(strtotime($currentdate) > strtotime($validation_last_date_emp))
		{
			$sqlquery="SELECT validation_month,validation_last_date FROM yellow_card_date_validation 
						WHERE validation_last_date >=CURDATE()  AND emp_code='' ORDER BY validation_last_date ASC LIMIT 0,1";
		}
		else
		{
			$sqlquery="SELECT validation_month,validation_last_date FROM yellow_card_date_validation 
					WHERE emp_code='".$emp_code."'";
		}
	}
	else
	{
		$sqlquery="SELECT validation_month,validation_last_date FROM yellow_card_date_validation 
			WHERE validation_last_date >=CURDATE() AND emp_code='' ORDER BY validation_last_date ASC LIMIT 0,1";
	}
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
		$currentdate =$year.'-'.$month.'-'.$date;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		
		while($rowcompetitorgroup = mysqli_fetch_assoc($result))
		{
			$validation_month=$rowcompetitorgroup['validation_month'];
			$validation_month_array=explode("-",$validation_month);
			//echo $month;
			//echo $validation_month_array[1];
			$number_days_month = cal_days_in_month(CAL_GREGORIAN, $validation_month_array[1], $validation_month_array[0]);
			if( $validation_month_array[1]==$month){
				$validation_last_date=$currentdate;
			}
			else
			{
				$validation_last_date=$year.'-'.$validation_month_array[1].'-'.$number_days_month;
			}
			
			$contents  = (($rowcompetitorgroup['validation_month']!='')?$rowcompetitorgroup['validation_month']: ' ')."^";
			$contents  .= (($validation_last_date!='')?str_replace('-','',$validation_last_date): ' ');
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
