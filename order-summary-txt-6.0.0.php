<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' (SUBSTRING(order_no,2,5) IN('.$employee_hierarchy.'))';
}
else
{
	$emp_hierarchy_condition=" SUBSTRING(order_no,2,5)='".$emp_code."'";
}

$sqlquery="SELECT POCM.order_no,POCM.customer_code,POCM.product_code,POCM.visit_qty,POCM.visit_date,POCM.rate,POCM.amount
			FROM prev_order_counting_master POCM,customer_master CM
			WHERE ".$emp_hierarchy_condition." AND CM.customer_code=POCM.customer_code";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));

		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		$countprevorder=0;
		while($rowsprevorder = mysqli_fetch_assoc($result))
		{
			$customer_code=$rowsprevorder['customer_code'];
			$product_code=$rowsprevorder['product_code'];
			$order_no=$rowsprevorder['order_no'];
			$visit_qty=$rowsprevorder['visit_qty'];
			$visit_date=$rowsprevorder['visit_date'];
			$rate=$rowsprevorder['rate'];
			$amount=$rowsprevorder['amount'];

			$contents  = (($order_no!='')?$order_no: ' ')."^";
			$contents  .= (($customer_code!='')?$customer_code: ' ')."^";
			$contents  .= (($product_code!='')?$product_code: ' ')."^";
			$contents  .= (($visit_qty!='')?$visit_qty: ' ')."^";
			$contents  .= (($visit_date!='')?$visit_date: ' ')."^";
			$contents  .= (($rate!='')?$rate: ' ')."^";
			$contents  .= (($amount!='')?$amount: ' ');

			$linecontents  .= $contents."\n";

		}
		$contentsrowcolumn=$count.'¥'.'7';
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
			$datacontents = '0'.'¥'.'7';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/order-summary-txt-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	header("Content-type: application/text");
	header("Content-Disposition: attachment; filename=order_summary.txt");
	print "$datacontents";
	mysqli_close($link);
?>
