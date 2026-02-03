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
if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	//$emp_hierarchy_condition='emp_code IN('.$employee_hierarchy.')';
}
if($incremental_download=='no')
{
	$login_condition=" ";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(DATE_FORMAT(SUBSTRING(SOD.stock_out_id,-14,14),'%Y-%m-%d %H:%i:%s')) > UNIX_TIMESTAMP('".$last_update_time."') ";
}
$sqlstockoutsummary = "SELECT SOD.IMEI,SOD.prod_code,SOD.customer_code,SOD.stock_out_date,SOD.stock_out_qty,CPB.invoice_date 
				FROM customer_product_billing CPB,stock_out_details SOD WHERE CPB.IMEI=SOD.IMEI 
				AND SUBSTRING(SOD.stock_out_id,3,5) IN(".$employee_hierarchy.") ".$login_condition;	
$resultstockoutsummary = mysqli_query($link,$sqlstockoutsummary);
$count=mysqli_num_rows($resultstockoutsummary);

$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

	if($count>0){
		$contentsrowcolumn=$count.'¥'.'6';
		while($rowstockoutsummary = mysqli_fetch_assoc($resultstockoutsummary))
		{
			$IMEI=$rowstockoutsummary['IMEI'];
			$prod_code=$rowstockoutsummary['prod_code'];
			$customer_code=$rowstockoutsummary['customer_code'];
			$stock_out_date=$rowstockoutsummary['stock_out_date'];
			$stock_out_qty=$rowstockoutsummary['stock_out_qty'];
			$invoice_date=$rowstockoutsummary['invoice_date'];
			
				$contents = (($IMEI!='')?$IMEI: ' ')."^";
				$contents .= (($prod_code!='')?$prod_code: ' ')."^";
				$contents .= (($invoice_date!='')?$invoice_date: ' ')."^";
				$contents .= (($stock_out_date!='')?$stock_out_date: ' ')."^";
				$contents .= (($customer_code!='')?$customer_code: ' ')."^";
				$contents .= (($stock_out_qty!='')?$stock_out_qty: 0);
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/stock-out-summary-txt-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=stock_out_summary.txt");
	print "$datacontents"; 		
?>
