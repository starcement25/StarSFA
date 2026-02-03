<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$IMEI=$_REQUEST['IMEI'];
/*$sql="SELECT DATE_FORMAT(invoice_date,'%d-%m-%Y') AS invoice_date,DATE_FORMAT(stock_out_date,'%d-%m-%Y') AS stock_out_date,
		(SELECT customer_name FROM customer_master WHERE customer_code=customer_product_billing.customer_code) AS billed_customer,
		(SELECT customer_name FROM customer_master WHERE customer_code=customer_product_billing.stock_out_customer_code) AS stock_out_customer,IMEI,
		DATE_FORMAT(activation_date,'%d-%m-%Y') AS activation_date,DATE_FORMAT(activation_date,'%H:%i:%s') AS activation_time
			FROM 
		 customer_product_billing WHERE SUBSTRING(IMEI,-LENGTH(".$IMEI."))='".$IMEI."'";*/
$sql="SELECT DATE_FORMAT(invoice_date,'%d-%m-%Y') AS invoice_date,DATE_FORMAT(stock_out_date,'%d-%m-%Y') AS stock_out_date,
		(SELECT customer_name FROM customer_master WHERE customer_code=customer_product_billing.customer_code) AS billed_customer,
		(SELECT customer_name FROM customer_master WHERE customer_code=customer_product_billing.stock_out_customer_code) AS stock_out_customer,
		(SELECT emp_code FROM customer_route_emp_relation WHERE customer_code=customer_product_billing.stock_out_customer_code) AS stock_out_emp,
		IMEI,
		DATE_FORMAT(activation_date,'%d-%m-%Y') AS activation_date,DATE_FORMAT(activation_date,'%H:%i:%s') AS activation_time
			FROM 
		 customer_product_billing WHERE IMEI='".$IMEI."'";		 
$rs=mysqli_query($link,$sql) or die(mysqli_error()." Error in main: ".$sql);
$count=mysqli_num_rows($rs);

$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

	if($count>0){
		$contentsrowcolumn=$count.'¥'.'8';
		while($rec = mysqli_fetch_assoc($rs))
		{
			$invoice_date=$rec['invoice_date'];
			$IMEI=$rec['IMEI'];
			$billed_customer=$rec['billed_customer'];
            $activation_date=$rec['activation_date'];
            $activation_time=$rec['activation_time'];
			$stock_out_emp=$rec['stock_out_emp'];
			$sqlemp="SELECT emp_name FROM employee_master WHERE emp_code='".$stock_out_emp."'";
			$rsemp=mysqli_query($link,$sqlemp);
			$rowemp=mysqli_fetch_assoc($rsemp);
			$emp_name=$rowemp['emp_name'];
			$sqlstockoutdetails="SELECT DATE_FORMAT(SUBSTRING(stock_out_id,-14,8),'%d-%m-%Y') AS stock_out_date,
			(SELECT customer_name FROM customer_master WHERE customer_code=stock_out_details.customer_code) AS stock_out_customer,
			 DATE_FORMAT(SUBSTRING(stock_out_id,-14,14),'%H:%i:%s') AS stock_out_time
			FROM stock_out_details WHERE IMEI='".$rec['IMEI']."'";
			$rsstockoutdetails=mysqli_query($link,$sqlstockoutdetails);
			$rowstockoutdetails=mysqli_fetch_assoc($rsstockoutdetails);
			$stock_out_date=$rowstockoutdetails['stock_out_date'];
			$stock_out_time=$rowstockoutdetails['stock_out_time'];
			$stock_out_customer=$rowstockoutdetails['stock_out_customer'].'-'.$emp_name;

			
				$contents  = (($IMEI!='')?$IMEI: ' ')."^";
				$contents  .= (($invoice_date!='')?$invoice_date: ' ')."^";
				$contents  .= (($billed_customer!='')?$billed_customer: ' ')."^";
				$contents  .= (($stock_out_date!='')?$stock_out_date: ' ')."^";
				$contents  .= (($stock_out_time!='')?$stock_out_time: ' ')."^";
				$contents  .= (($stock_out_customer!='')?$stock_out_customer: ' ')."^";
				$contents  .= (($activation_date!='')?$activation_date: ' ')."^";
				$contents  .= (($activation_time!='')?$activation_time: ' ');
				
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/IMEIstatus-6.0.0.php?IMEI=$IMEI";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=IMEI_status.txt");
	print "$datacontents"; 		
?>
