<?php
ini_set('memory_limit', '-1');
set_time_limit(1000);


require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' AND SUBSTRING(customer_code,3,5) IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition=" AND SUBSTRING(customer_code,3,5)='".$emp_code."'";
}
    $nick_name=$_REQUEST['nick_name'];
    $ddb = "acedns_".$nick_name;

    define("DBL","$ddb");
    
    $link=mysqli_connect(SERVER,USER,PASSWORD,DBL) or die("Database Connection Error.");
	if(strtouuper($nick_name)=='ILS' & $emp_code=='E0001')
	{
	$sqlquery="SELECT * FROM `prospective_customer_master` WHERE 1 $emp_hierarchy_condition" LIMIT 100;
	}
	else
	{
		$sqlquery="SELECT * FROM `prospective_customer_master` WHERE 1 $emp_hierarchy_condition";
	}
	//$sqlquery="SELECT * FROM `prospective_customer_master`";	
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'10';
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time."\n";
		while($rowprospectivecustomermaster = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowprospectivecustomermaster['emp_code']!='')?$rowprospectivecustomermaster['emp_code']: ' ')."^";
			$contents  .= (($rowprospectivecustomermaster['customer_code']!='')?$rowprospectivecustomermaster['customer_code']: ' ')."^";
			$contents  .= (($rowprospectivecustomermaster['customer_name']!='')?trim(preg_replace('/[\r\n]+/', '',$rowprospectivecustomermaster['customer_name'])): ' ')."^";
			$contents  .= (($rowprospectivecustomermaster['address']!='')?trim(preg_replace('/[\r\n]+/', '',$rowprospectivecustomermaster['address'])): ' ')."^";
			$contents  .= (($rowprospectivecustomermaster['pin']!='')?$rowprospectivecustomermaster['pin']: ' ')."^";
			$contents  .= (($rowprospectivecustomermaster['area']!='')?trim(preg_replace('/[\r\n]+/', '',$rowprospectivecustomermaster['area'])): ' ')."^";
			$contents  .= (($rowprospectivecustomermaster['phone_no']!='')?$rowprospectivecustomermaster['phone_no']: ' ')."^";
			$contents  .= (($rowprospectivecustomermaster['cust_type']!='')?$rowprospectivecustomermaster['cust_type']: ' ')."^";
			$contents  .= (($rowprospectivecustomermaster['tagged_customer_code']!='')?$rowprospectivecustomermaster['tagged_customer_code']: ' ')."^";
			$contents  .= (($rowprospectivecustomermaster['category_of_store']!='')?$rowprospectivecustomermaster['category_of_store']: ' ');

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
			$datacontents = '0'.'¥'.'10';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/prospective_customer_master_6.0.1.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=prospective_customer_master.txt");
	print "$datacontents";	
	mysqli_close($link);
?>
