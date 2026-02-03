<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
//$emp_code='100017206';
$incremental_download=$_REQUEST['incremental_download'];
//$last_update_time='2014-06-06 13:40:25';
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);

if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(validation_create_date) > UNIX_TIMESTAMP('".$last_update_time."')";
}

	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$sqlqueryemp="SELECT customer_code,validation_from,validation_to,validation_last_date FROM yellow_card_date_validation_customerwise 
			WHERE customer_code IN(SELECT DISTINCT customer_code FROM customer_route_emp_relation WHERE acedns='Y' AND emp_code IN(".$employee_hierarchy."))";
	$resultemp = mysqli_query($link,$sqlqueryemp);
	$customercount=mysqli_num_rows($resultemp);
	$contentsrowcolumn  =$customercount.'¥'.'4';
	if($customercount >0)
	{
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
		
		while($rowvalidation = mysqli_fetch_assoc($resultemp))
		{
			$customer_code=$rowvalidation['customer_code'];
			$validation_from=$rowvalidation['validation_from'];
			$validation_to=$rowvalidation['validation_to'];
			$validation_last_date=$rowvalidation['validation_last_date'];
			
			$contents  = (($customer_code!='')?$customer_code: ' ')."^";
			$contents  .= (($validation_from!='')?$validation_from: ' ')."^";
			$contents  .= (($validation_to!='')?$validation_to: ' ')."^";
			$contents  .= (($validation_last_date!='')?$validation_last_date: ' ');
			$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/yellow-card-date-validation-txt-6.0.2.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);


	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=yellow-card-date-validation.txt");
	print "$datacontents"; 	
?>
