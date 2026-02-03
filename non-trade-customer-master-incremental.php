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

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition='emp_code IN('.$employee_hierarchy.')';
}
else
{
  $emp_hierarchy_condition="emp_code='".$emp_code."'";
}

$login_condition=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";


	if($emp_code=='C0007')
	{
		$sqlquery="SELECT * FROM customer_master WHERE 1  ".$login_condition." ORDER BY customer_name ASC";
	}
	else
	{
		$sqlquery="SELECT customer_code,customer_name,address,phone_no,route_code,emp_code,rds_tag FROM non_trade_customer_master 
					WHERE ".$emp_hierarchy_condition.$login_condition."  ORDER BY customer_name ASC"; 
	}
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time."\n";
		while($rowsemp = mysqli_fetch_assoc($result))
		{
				$contents  = (($rowsemp['customer_code']!='')?$rowsemp['customer_code']: ' ')."^";
				$contents  .= (($rowsemp['customer_name']!='')?trim(preg_replace('/[\r\n]+/', '',$rowsemp['customer_name'])): ' ')."^";
				$contents  .= (($rowsemp['address']!='')?$rowsemp['address']: ' ')."^";
				$contents  .= (($rowsemp['phone_no']!='')?$rowsemp['phone_no']: ' ')."^";
				$contents  .= (($rowsemp['route_code']!='')?$rowsemp['route_code']: ' ')."^";
				$contents  .= (($rowsemp['emp_code']!='')?$rowsemp['emp_code']: ' ')."^";
				$contents  .= (($rowsemp['rds_tag']!='')?$rowsemp['rds_tag']: ' ')."^";
				
				$linecontents  .= $contents."\n";
				$countdata++;
		}
		$contentsrowcolumn=$count.'¥'.'7';
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/non-trade-customer-master-incremental.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=non_trade_customer_master.txt");
	print "$datacontents";
	mysqli_close($link); 		
?>
