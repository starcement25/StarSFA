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

if($incremental_download=='no')
{
	$login_condition="";
	$login_condition_one="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(CM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
	$login_condition_one=" AND UNIX_TIMESTAMP(RM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}
$sqlmenuaccess="SELECT accessible_menu FROM OTP_menu_access WHERE emp_code='".$emp_code."'";
$rsmenuaccess=mysqli_query($link,$sqlmenuaccess);
$countmenuaccess=mysqli_num_rows($rsmenuaccess);
$menu_access_array=array();
if($countmenuaccess >0)
{
	while($rowmenuaccess=mysqli_fetch_assoc($rsmenuaccess))
	{
		array_push($menu_access_array,$rowmenuaccess['accessible_menu']);
	}
}

 if(in_array('loading',$menu_access_array)) // Start of loading
{ 
  	$sqlquery="SELECT DISTINCT sauda_no,customer_code,destination,DO_no,sku_code,DO_qty,DO_rate,DO_amount,DO_status,DO_date,dns_DO_no  
	FROM DO_transaction WHERE DO_status IN('weighbridge_in') AND DO_no NOT IN(SELECT DO_no FROM DO_tracking WHERE loading_id!='')";

}
else
{
	if(strtoupper(substr($emp_code,0,1))=='E')
	{
	$sqlquery="SELECT DISTINCT sauda_no,customer_code,destination,DO_no,sku_code,DO_qty,DO_rate,DO_amount,DO_status,DO_date,dns_DO_no  
	FROM DO_transaction WHERE DO_status 
					IN('approved','vehicle_allotted','invoice_generated','despatch','weighbridge_in','weighbridge_out') AND DO_no NOT IN(SELECT DO_no FROM DO_tracking WHERE vehicle_no!='')";
	}
	if(strtoupper(substr($emp_code,0,1))=='C')
	{
			$sqlquery="SELECT DISTINCT sauda_no,customer_code,destination,DO_no,sku_code,DO_qty,DO_rate,DO_amount,DO_status,DO_date,dns_DO_no  
	FROM DO_transaction WHERE DO_status 
					IN('approved','vehicle_allotted','invoice_generated','despatch','weighbridge_in','weighbridge_out') AND DO_no NOT IN(SELECT DO_no FROM DO_tracking WHERE vehicle_no!='') AND customer_code='".$emp_code."'";

	}
	if(strtoupper(substr($emp_code,0,1))=='B')
	{
			$sqlquery="SELECT DISTINCT sauda_no,customer_code,destination,DO_no,sku_code,DO_qty,DO_rate,DO_amount,DO_status,DO_date,dns_DO_no  
	FROM DO_transaction WHERE DO_status 
					IN('approved','vehicle_allotted','invoice_generated','despatch','weighbridge_in','weighbridge_out') AND DO_no NOT IN(SELECT DO_no FROM DO_tracking WHERE vehicle_no!='') AND customer_code IN (SELECT customer_code FROM customer_broker_relation WHERE broker_code='".$emp_code."')";

	}
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
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		$dns_route_code='';
		while($rowDOtrans = mysqli_fetch_assoc($result))
		{
			$dns_DO_no=str_replace('DO//','DO/BHD/',$rowDOtrans['dns_DO_no']);
			$contents  = (($rowDOtrans['sauda_no']!='')?$rowDOtrans['sauda_no']: ' ')."^";
			$contents  .= (($rowDOtrans['customer_code']!='')?$rowDOtrans['customer_code']: ' ')."^";
			$contents  .= (($rowDOtrans['destination']!='')?$rowDOtrans['destination']: ' ')."^";
			$contents  .= (($rowDOtrans['DO_no']!='')?$rowDOtrans['DO_no']: ' ')."^";
			$contents  .= (($rowDOtrans['sku_code']!='')?$rowDOtrans['sku_code']: ' ')."^";
			$contents  .= (($rowDOtrans['DO_qty']!='')?$rowDOtrans['DO_qty']: ' ')."^";
			$contents  .= (($rowDOtrans['DO_rate']!='')?$rowDOtrans['DO_rate']: ' ')."^";
			$contents  .= (($rowDOtrans['DO_amount']!='')?$rowDOtrans['DO_amount']: ' ')."^";
			$contents  .= (($rowDOtrans['DO_status']!='')?$rowDOtrans['DO_status']: ' ')."^";
			$contents  .= (($rowDOtrans['DO_date']!='')?$rowDOtrans['DO_date']: ' ')."^";
			$contents  .= (($dns_DO_no!='')?$dns_DO_no: ' ');
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
	$url = APICALLLOGURL."/OTP-DO-transaction-download.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=DO_transaction.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
