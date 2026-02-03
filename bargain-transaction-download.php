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

$employee_hierarchy=return_employee_hierarchy($emp_code);
$emp_hierarchy_condition=' AND CRR.emp_code IN('.$employee_hierarchy.')';

if($incremental_download=='no')
{
	$login_condition="";
	$login_condition_one="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(SH.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}
    $sqlquery="SELECT DM.sauda_no,DM.customer_code,DM.branch_code,DM.sku_code,DM.mapped_sku_code,DM.qty,(DM.sale_rate+DM.freight_charge) AS sale_rate,DM.amount,DM.status,
				DM.incoterms,DM.dns_sauda_no  FROM DO_master DM,customer_route_emp_relation CRR WHERE DM.status='no' 
				AND DM.customer_code=CRR.customer_code  ".$emp_hierarchy_condition;
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
		while($rowDOtrans = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowDOtrans['sauda_no']!='')?$rowDOtrans['sauda_no']: ' ')."^";
			$contents  .= (($rowDOtrans['customer_code']!='')?$rowDOtrans['customer_code']: ' ')."^";
			$contents  .= (($rowDOtrans['branch_code']!='')?$rowDOtrans['branch_code']: ' ')."^";
			$contents  .= (($rowDOtrans['sku_code']!='')?$rowDOtrans['sku_code']: ' ')."^";
			$contents  .= (($rowDOtrans['qty']!='')?$rowDOtrans['qty']: ' ')."^";
			$contents  .= (($rowDOtrans['sale_rate']!='')?round($rowDOtrans['sale_rate'],2): ' ')."^";
			$contents  .= (($rowDOtrans['amount']!='')?round($rowDOtrans['amount'],2): ' ')."^";
			$contents  .= (($rowDOtrans['status']!='')?$rowDOtrans['status']: ' ')."^";
			$contents  .= (($rowDOtrans['incoterms']!='')?$rowDOtrans['incoterms']: ' ')."^";
			$contents  .= (($rowDOtrans['mapped_sku_code']!='')?$rowDOtrans['mapped_sku_code']: ' ')."^";
			$contents  .= (($rowDOtrans['dns_sauda_no']!='')?$rowDOtrans['dns_sauda_no']: ' ');
			$linecontents  .= $contents."\n";
		}
		$contentsrowcolumn=$count.'¥'.'11';
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
	$url = APICALLLOGURL."/bargain-transaction-download.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=bargain_transaction.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
