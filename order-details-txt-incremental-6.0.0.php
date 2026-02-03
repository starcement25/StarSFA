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
	$login_condition=" AND UNIX_TIMESTAMP(DATE_FORMAT(SUBSTRING(order_no,-14,14),'%Y-%m-%d %H:%i:%s')) > UNIX_TIMESTAMP('".$last_update_time."') ";
}
if(employeewise_hierarchy=='yes'){
		$employee_hierarchy=return_employee_hierarchy($emp_code);
		$emp_val_order=' AND (SUBSTRING(order_no,2,5) IN('.$employee_hierarchy.'))';
	}
	else
	{
		$emp_val_order=" AND SUBSTRING(order_no,2,5)='".$emp_code."'";
	}
$sqlorderdetails = "SELECT order_no,sku_code,qty,mrp_code,TD,sale_rate,VAT,amount,freight_charge,premium,UOM,scheme_type 
					FROM order_details WHERE 1 ".$emp_val_order.$login_condition;
$resultorderdetails = mysqli_query($link,$sqlorderdetails);
$count=mysqli_num_rows($resultorderdetails);

$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

	if($count>0){
		$contentsrowcolumn=$count.'¥'.'12';
		while($roworderdetails = mysqli_fetch_assoc($resultorderdetails))
		{
			$order_no=$roworderdetails['order_no'];
			$sku_code=$roworderdetails['sku_code'];
			$qty=$roworderdetails['qty'];
			$mrp_code=$roworderdetails['mrp_code'];
			$TD=$roworderdetails['TD'];
			$sale_rate=$roworderdetails['sale_rate'];
			$VAT=$roworderdetails['VAT'];
			$amount=$roworderdetails['amount'];
			$freight_charge=$roworderdetails['freight_charge'];
			$premium=$roworderdetails['premium'];
			$UOM=$roworderdetails['UOM'];
			$scheme_type=$roworderdetails['scheme_type'];
			
				$contents = (($order_no!='')?$order_no: ' ')."^";
				$contents .= (($sku_code!='')?$sku_code: ' ')."^";
				$contents .= (($qty!='')?$qty: ' ')."^";
				$contents .= (($mrp_code!='')?$mrp_code: ' ')."^";
				$contents .= (($TD!='')?$TD: ' ')."^";
				$contents .= (($sale_rate!='')?$sale_rate: ' ')."^";
				$contents .= (($VAT!='')?$VAT: ' ')."^";
				$contents .= (($amount!='')?$amount: ' ')."^";
				$contents .= (($freight_charge!='')?$freight_charge: ' ')."^";
				$contents .= (($premium!='')?$premium: ' ')."^";
				$contents .= (($UOM!='')?$UOM: ' ')."^";
				$contents .= (($scheme_type!='')?$scheme_type: ' ');
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/order-details-txt-incremental-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=order_details.txt");
	print "$datacontents"; 		
?>
