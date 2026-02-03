<?php
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
	$login_condition_one="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(CM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
	$login_condition_one=" AND UNIX_TIMESTAMP(RM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}
    $sqlquery="SELECT DDD.sauda_no,DDD.customer_code,DDD.destination,DDD.DO_no,
				DDD.sku_code,DDD.batch_no,DDD.DO_qty,DDD.DO_rate,DDD.DO_amount,DDD.DO_date,
				DDD.loading_qty,DDD.invoice_no,DDD.invoice_amount,DDD.invoice_date
				FROM DO_despatch_details DDD,DO_tracking DT WHERE DDD.DO_no=DT.DO_no AND DT.wb_out_date!='0000-00-00 00:00:00'
					";
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		$dns_route_code='';
		while($rowDOtrans = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowDOtrans['sauda_no']!='')?$rowDOtrans['sauda_no']: ' ')."^";
			$contents  .= (($rowDOtrans['customer_code']!='')?$rowDOtrans['customer_code']: ' ')."^";
			$contents  .= (($rowDOtrans['destination']!='')?$rowDOtrans['destination']: ' ')."^";
			$contents  .= (($rowDOtrans['DO_no']!='')?$rowDOtrans['DO_no']: ' ')."^";
			$contents  .= (($rowDOtrans['sku_code']!='')?$rowDOtrans['sku_code']: ' ')."^";
			$contents  .= (($rowDOtrans['batch_no']!='')?$rowDOtrans['batch_no']: ' ')."^";
			$contents  .= (($rowDOtrans['DO_qty']!='')?$rowDOtrans['DO_qty']: ' ')."^";
			$contents  .= (($rowDOtrans['DO_rate']!='')?$rowDOtrans['DO_rate']: ' ')."^";
			$contents  .= (($rowDOtrans['DO_amount']!='')?$rowDOtrans['DO_amount']: ' ')."^";
			$contents  .= (($rowDOtrans['DO_date']!='')?$rowDOtrans['DO_date']: ' ')."^";
			$contents  .= (($rowDOtrans['loading_qty']!='')?$rowDOtrans['loading_qty']: ' ')."^";
			$contents  .= (($rowDOtrans['invoice_no']!='')?$rowDOtrans['invoice_no']: ' ')."^";
			$contents  .= (($rowDOtrans['invoice_amount']!='')?$rowDOtrans['invoice_amount']: ' ')."^";
			$contents  .= (($rowDOtrans['invoice_date']!='')?$rowDOtrans['invoice_date']: ' ')."^";
			$linecontents  .= $contents."\n";
		}
		$contentsrowcolumn=$count.'¥'.'14';
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
			$datacontents = '0'.'¥'.'14';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/OTP-loading-data-download.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=loading_data.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
