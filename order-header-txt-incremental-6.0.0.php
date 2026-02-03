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
$sqlorderheader = "SELECT order_no,customer_code,transferred,sale_type,d_instruction,TD,order_value,tag_distributor_code,transaction_type,VAT,vertical_value,destination_code,order_type,freight_component,GST_type,price_validation_type FROM order_header WHERE 1 ".$emp_val_order.$login_condition;
$resultorderheader = mysqli_query($link,$sqlorderheader);
$count=mysqli_num_rows($resultorderheader);

$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

	if($count>0){
		$contentsrowcolumn=$count.'¥'.'16';
		while($roworderheader = mysqli_fetch_assoc($resultorderheader))
		{
			$order_no=$roworderheader['order_no'];
			$customer_code=$roworderheader['customer_code'];
			$transferred=$roworderheader['transferred'];
			$sale_type=$roworderheader['sale_type'];
			$d_instruction=$roworderheader['d_instruction'];
			$TD=$roworderheader['TD'];
			$order_value=$roworderheader['order_value'];
			$tag_distributor_code=$roworderheader['tag_distributor_code'];
			$transaction_type=$roworderheader['transaction_type'];
			$VAT=$roworderheader['VAT'];
			$vertical_value=$roworderheader['vertical_value'];
			$destination_code=$roworderheader['destination_code'];
			$order_type=$roworderheader['order_type'];
			$freight_component=$roworderheader['freight_component'];
			$GST_type=$roworderheader['GST_type'];
			$price_validation_type=$roworderheader['price_validation_type'];
			
				$contents = (($order_no!='')?$order_no: ' ')."^";
				$contents .= (($customer_code!='')?$customer_code: ' ')."^";
				$contents .= (($transferred!='')?$transferred: ' ')."^";
				$contents .= (($sale_type!='')?$sale_type: ' ')."^";
				$contents .= (($d_instruction!='')?$d_instruction: ' ')."^";
				$contents .= (($TD!='')?$TD: ' ')."^";
				$contents .= (($order_value!='')?$order_value: ' ')."^";
				$contents .= (($tag_distributor_code!='')?$tag_distributor_code: ' ')."^";
				$contents .= (($transaction_type!='')?$transaction_type: ' ')."^";
				$contents .= (($VAT!='')?$VAT: ' ')."^";
				$contents .= (($vertical_value!='')?$vertical_value: ' ')."^";
				$contents .= (($destination_code!='')?$destination_code: ' ')."^";
				$contents .= (($order_type!='')?$order_type: ' ')."^";
				$contents .= (($freight_component!='')?$freight_component: ' ')."^";
				$contents .= (($GST_type!='')?$GST_type: ' ')."^";
				$contents .= (($price_validation_type!='')?$price_validation_type: ' ');
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/order-header-txt-incremental-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=order_header.txt");
	print "$datacontents"; 		
?>
