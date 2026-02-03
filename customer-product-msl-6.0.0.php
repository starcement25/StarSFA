<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
	$emp_hierarchy_condition=' AND CM.emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition=" AND CM.emp_code='".$emp_code."'";
}

 if($emp_code=='C0007')
 {
	$sqlquery="SELECT CPM.customer_code,CPM.prod_code,CPM.acedns,CPM.msl FROM customer_product_wise_msl CPM
				WHERE  CPM.acedns='Y'";
 }
 else
 {
	$sqlquery="SELECT CPM.customer_code,CPM.prod_code,CPM.acedns,CPM.msl FROM customer_product_wise_msl CPM,customer_route_emp_relation CM 
				WHERE  CPM.customer_code=CM.customer_code AND CPM.acedns='Y' ".$emp_hierarchy_condition.""; 
}

$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

	$contentsrowcolumn=$count.'¥'.'4';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		
		while($rowcustomermsl = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowcustomermsl['customer_code']!='')?$rowcustomermsl['customer_code']: ' ')."^";
			$contents  .= (($rowcustomermsl['prod_code']!='')?$rowcustomermsl['prod_code']: ' ')."^";
			$contents  .= (($rowcustomermsl['msl']!='')?$rowcustomermsl['msl']: ' ')."^";
			$contents  .= (($rowcustomermsl['acedns']!='')?$rowcustomermsl['acedns']: ' ');
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
			$datacontents = '0'.'¥'.'4';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/customer-product-msl-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=customer_product_msl.txt");
	print "$datacontents"; 		
?>
