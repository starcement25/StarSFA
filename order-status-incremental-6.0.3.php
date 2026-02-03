<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
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
	$emp_hierarchy_condition='(SUBSTRING(order_no,2,5) IN('.$employee_hierarchy.'))';
}
else
{
	$emp_hierarchy_condition="(SUBSTRING(order_no,2,5)='".$emp_code."')";
}

if($incremental_download=='no')
{
	if($nick_name=='HALDIRAM')
	{
		$compare_datetime='2017-10-01 00:00:00';
		$login_condition=" AND UNIX_TIMESTAMP(POCM.download_time) >= UNIX_TIMESTAMP('".$compare_datetime."')";
	}
	else
	{
		$login_condition="";
	}
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(POCM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}

/*$sqlbranches="SELECT branch_code FROM branch_master WHERE 1";
$rsbranches=mysqli_query($link,$sqlbranches);
$countbranches=mysqli_num_rows($rsbranches);*/
if(strtoupper($nick_name)=='ABDOS' || strtoupper($nick_name)=='ABDOST')
{
	$sqlquery="SELECT POCM.order_no,POCM.customer_code,POCM.product_code,POCM.visit_qty,POCM.delivery_qty,POCM.status,POCM.rate,POCM.amount
			FROM prev_order_counting_master POCM WHERE (SUBSTRING(POCM.order_no,2,5) IN(".$employee_hierarchy.") OR SUBSTRING(POCM.order_no,3,5)IN(".$employee_hierarchy."))
			".$login_condition." AND POCM.status='pending' AND SUBSTRING(POCM.visit_date,1,10) >= '2020-04-01'";
}
else
{
$sqlquery="SELECT POCM.order_no,POCM.customer_code,POCM.product_code,POCM.visit_qty,POCM.delivery_qty,POCM.status,POCM.rate,POCM.amount
			FROM prev_order_counting_master POCM,customer_master CM WHERE (SUBSTRING(order_no,2,5)='".$emp_code."' OR SUBSTRING(order_no,3,5)='".$emp_code."')
			".$login_condition."  AND CM.customer_code=POCM.customer_code AND POCM.status='pending'";
}
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
		$contentsrowcolumn=$count.'¥'.'11';

		while($rowsorderstatus = mysqli_fetch_assoc($result))
		{
			$customer_code=$rowsorderstatus['customer_code'];
			$order_no=$rowsorderstatus['order_no'];
			$product_code=$rowsorderstatus['product_code'];
			$order_qty=$rowsorderstatus['visit_qty'];
			$delivery_qty=$rowsorderstatus['delivery_qty'];
			$status=$rowsorderstatus['status'];
			$rate=$rowsorderstatus['rate'];
			$amount=$rowsorderstatus['amount'];
			$remarks='';
			$flag='1';
			if(strtoupper($nick_name)=='ABDOS' || strtoupper($nick_name)=='ABDOST')
			 {
			 	$sqlweightage="SELECT weightage FROM order_details WHERE order_no='".$order_no."' AND sku_code='".$product_code."'";
				$rsweightage=mysqli_query($link,$sqlweightage);
				$rowweightage=mysqli_fetch_assoc($rsweightage);
				$weightage=$rowweightage['weightage'];
			 }
			 else $weightage='';
			
			$contents  = (($order_no!='')?$order_no: ' ')."^";
			$contents  .= (($customer_code!='')?$customer_code: ' ')."^";
			$contents  .= (($product_code!='')?$product_code: ' ')."^";
			$contents  .= (($order_qty!='')?$order_qty: ' ')."^";
			$contents  .= (($delivery_qty!='')?$delivery_qty: ' ')."^";
			$contents  .= (($status!='')?$status: ' ')."^";
			$contents  .= (($remarks!='')?$remarks: ' ')."^";
			$contents  .= (($flag!='')?$flag: ' ')."^";
			$contents  .= (($rate!='')?$rate: ' ')."^";
			$contents  .= (($amount!='')?$amount: ' ')."^";
			$contents  .= (($weightage!='')?$weightage: ' ');

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
			$datacontents = '0'.'¥'.'11';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/order-status-incremental-6.0.3.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/prev-stock-counting-master-txt-incremental-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download"."\r\n";
	$insertPos=0;  // variable for saving 
	while (!feof($file)) {
		$line=fgets($file);
		if (strpos($line, 'http://')!==false) {
			$insertPos=ftell($file);
			$newline =  $newuser;
		}
		else
		{
			$newline.=$line;   // append existing data with new data of user
		}
	}
	fseek($file,$insertPos);   // move pointer to the file position where we saved above 
	fwrite($file, $newline);
	fclose($file);*/	

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=order_status.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
