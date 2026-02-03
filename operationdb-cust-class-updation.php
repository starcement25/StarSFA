<?php
ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

$emp_code=$_REQUEST['emp_code'];
$cust_class=$_POST['cust_class'] ?? '';
//$cust_class='NE002720170822191920#R2';
$cust_class_array=explode(';',$cust_class);
$flag=0;
if($cust_class!='')
{
foreach($cust_class_array as $cust_class_combined_value)
{
	$cust_class_combined_value_array=explode('#',$cust_class_combined_value);
	$customer_code=$cust_class_combined_value_array[0];
	$cust_class=$cust_class_combined_value_array[1];
	$sqlupdatecustomer="UPDATE customer_master SET cust_class='".$cust_class."',
					cust_class_update_time=CURRENT_TIMESTAMP(),
					cust_class_update_by='".$emp_code."' WHERE customer_code='".$customer_code."'";
	if(mysqli_query($link,$sqlupdatecustomer))
	{
		$flag=1;
	}
	else
	{
		$flag=2;
	}
	
}
if($flag==1)
{
	echo '1';
}
else echo '0';
}
else
{
	echo '0';
}
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = APICALLLOGURL."/operationdb-cust-class-updation.php?nick_name=$nick_name&emp_code=$emp_code&cust_class=$cust_class";
insertapilog($datetime,$emp_code,$url,$nick_name);
mysqli_close($link);
?>
