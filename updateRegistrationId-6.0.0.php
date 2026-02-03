<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$deviceId=$_POST['deviceId'];
$emp_code_phone=$_POST['emp_code'];
$registrationid=$_POST['registrationid'];
//$sqlquery="select * from employee_master";
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = APICALLLOGURL."/updateRegistrationId-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code_phone&registrationid=$registrationid&deviceId=$deviceId";
	insertapilog($datetime,$emp_code_phone,$url,$nick_name);
	
if(app_phoneno_login=='yes')
{
	$sqlUpdate="UPDATE changepassword SET
			registrationid='".$registrationid."' 
			WHERE emp_code=(SELECT emp_code FROM employee_master WHERE phone_no='".$emp_code_phone."')";
}
else
{

$sqlUpdate="UPDATE changepassword SET
			registrationid='".$registrationid."' 
			WHERE emp_code='".$emp_code_phone."'";
}
if(mysqli_query($link,$sqlUpdate))
{
	echo "1";
}
else
{
	echo "0";
}
mysqli_close($link);
?>
