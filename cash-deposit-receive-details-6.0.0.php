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
		$emp_val_cash_deposit=' AND (SUBSTRING(cash_deposit_recv_id,3,5) IN('.$employee_hierarchy.'))';
	}
	else
	{
		$emp_val_cash_deposit=" AND SUBSTRING(cash_deposit_recv_id,3,5)='".$emp_code."'";
	}
$sqlcashdeposit = "SELECT cash_deposit_recv_id,emp_code,trans_type,date,amount,remarks FROM cash_deposit_receive_details 
					WHERE 1 ".$emp_val_cash_deposit.$login_condition;
$resultcashdeposit = mysqli_query($link,$sqlcashdeposit);
$count=mysqli_num_rows($resultcashdeposit);

$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

	if($count>0){
		$contentsrowcolumn=$count.'¥'.'6';
		while($rowcashdeposit = mysqli_fetch_assoc($resultcashdeposit))
		{
			$cash_deposit_recv_id=$rowcashdeposit['cash_deposit_recv_id'];
			$emp_code=$rowcashdeposit['emp_code'];
			$trans_type=$rowcashdeposit['trans_type'];
			$date=$rowcashdeposit['date'];
			$amount=$rowcashdeposit['amount'];
			$remarks=$rowcashdeposit['remarks'];
			
				$contents = (($cash_deposit_recv_id!='')?$cash_deposit_recv_id: ' ')."^";
				$contents .= (($emp_code!='')?$emp_code: ' ')."^";
				$contents .= (($trans_type!='')?$trans_type: ' ')."^";
				$contents .= (($date!='')?$date: ' ')."^";
				$contents .= (($amount!='')?$amount: ' ')."^";
				$contents .= (($remarks!='')?$remarks: ' ');
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/cash-deposit-receive-details-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=cash_deposit_receive_details.txt");
	print "$datacontents"; 		
?>
