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
$sqlpaymentheader = "SELECT receipt_id,customer_code,amount,cash_cheque,cheque_no,date,bank,rdate,'' as transferred,p_remark,sale_type 
FROM payment_header WHERE 1 ".$emp_val_collection.$login_condition;
$resultpaymentheader = mysqli_query($link,$sqlpaymentheader);
$count=mysqli_num_rows($resultpaymentheader);

$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

	if($count>0){
		$contentsrowcolumn=$count.'¥'.'11';
		while($rowpaymentheader = mysqli_fetch_assoc($resultpaymentheader))
		{
			$receipt_id=$rowpaymentheader['receipt_id'];
			$customer_code=$rowpaymentheader['customer_code'];
			$amount=$rowpaymentheader['amount'];
			$cash_cheque=$rowpaymentheader['cash_cheque'];
			$cheque_no=$rowpaymentheader['cheque_no'];
			$date=$rowpaymentheader['date'];
			$bank=$rowpaymentheader['bank'];
			$rdate=$roworderheader['rdate'];
			$transferred=$roworderheader['transferred'];
			$p_remark=$roworderheader['p_remark'];
			$sale_type=$roworderheader['sale_type'];
			
				$contents = (($receipt_id!='')?$receipt_id: ' ')."^";
				$contents .= (($customer_code!='')?$customer_code: ' ')."^";
				$contents .= (($amount!='')?$amount: ' ')."^";
				$contents .= (($cash_cheque!='')?$cash_cheque: ' ')."^";
				$contents .= (($cheque_no!='')?$cheque_no: ' ')."^";
				$contents .= (($date!='')?$date: ' ')."^";
				$contents .= (($bank!='')?$bank: ' ')."^";
				$contents .= (($rdate!='')?$rdate: ' ')."^";
				$contents .= (($transferred!='')?$transferred: ' ')."^";
				$contents .= (($p_remark!='')?$p_remark: ' ')."^";
				$contents .= (($sale_type!='')?$sale_type: ' ');
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/payment-header-txt-incremental-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=payment_header.txt");
	print "$datacontents"; 		
?>
