<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

	$emp_code=$_REQUEST['emp_code'];

	$sqlqueryinvoice="SELECT * from invoice_information  WHERE SUBSTRING(invoice_no,-11,5)='".$emp_code."'";
	$resqueryinvoice = mysqli_query($link,$sqlqueryinvoice);
	$countqueryinvoice=mysqli_num_rows($resqueryinvoice);
	$contentsrowcolumn=$countcustomerroute.'¥'.'5';
	if($countqueryinvoice>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowsqueryinvoice = mysqli_fetch_assoc($resqueryinvoice))
		{
			$contents  = (($invoice_no!='')?$invoice_no: ' ')."^";
			$contents  .= (($invoice_date!='')?$invoice_date: ' ')."^";
			$contents  .= (($order_no!='')?$order_no: ' ')."^"; 
			$contents  .= (($customer_code!='')?$order_no: ' ')."^"; 
			$contents  .= (($chronological_no!='')?$chronological_no: ' ')."^"; 
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
	$url = "http://www.acedns.in/acednsproduct/invoice-txt-information-6.0.0p?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=invoice_info.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
