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
	$login_condition=" ";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(DATE_FORMAT(SUBSTRING(receipt_id,-14,14),'%Y-%m-%d %H:%i:%s')) > UNIX_TIMESTAMP('".$last_update_time."') ";
}
if(employeewise_hierarchy=='yes'){
		$employee_hierarchy=return_employee_hierarchy($emp_code);
		$emp_val_collection=' AND (SUBSTRING(receipt_id,2,5) IN('.$employee_hierarchy.'))';
	}
	else
	{
		$emp_val_collection=" AND SUBSTRING(receipt_id,2,5)='".$emp_code."'";
	}
$sqlpaymentdetails = "SELECT receipt_id,invoice_id,amount,discount,recid FROM payment_details WHERE 1 ".$emp_val_collection.$login_condition;
$resultpaymentdetails = mysqli_query($link,$sqlpaymentdetails);
$count=mysqli_num_rows($resultpaymentdetails);

$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

	if($count>0){
		$contentsrowcolumn=$count.'¥'.'5';
		while($rowpaymentdetails = mysqli_fetch_assoc($resultpaymentdetails))
		{
			$receipt_id=$rowpaymentdetails['receipt_id'];
			$invoice_id=$rowpaymentdetails['invoice_id'];
			$amount=$rowpaymentdetails['amount'];
			$discount=$rowpaymentdetails['discount'];
			$rec_id=$rowpaymentdetails['rec_id'];
			
				$contents = (($receipt_id!='')?$receipt_id: ' ')."^";
				$contents .= (($invoice_id!='')?$invoice_id: ' ')."^";
				$contents .= (($amount!='')?$amount: ' ')."^";
				$contents .= (($discount!='')?$discount: ' ')."^";
				$contents .= (($rec_id!='')?$rec_id: ' ');
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/payment-details-txt-incremental-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=payment_details.txt");
	print "$datacontents"; 		
?>
