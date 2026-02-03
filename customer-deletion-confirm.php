<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_POST['emp_code'];
$customer_codeval=$_POST['customer_code'];
//$emp_code='C0005';
$sqlupdatecustomerinactive="UPDATE customer_master_inactive SET app_deletion='yes' WHERE customer_code IN(".$customer_codeval.")";
if(mysqli_query($link,$sqlupdatecustomerinactive))
{
	echo '1';
}
else
{
	echo '0';
}
	
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = APICALLLOGURL."/customer-deletion-confirm.php?nick_name=$nick_name&emp_code=$emp_code&customer_code=$customer_codeval";
insertapilog($datetime,$emp_code,$url,$nick_name);
mysqli_close($link);	
?>