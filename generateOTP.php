<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
//echo"<pre>";print_r('test befor functions');

require("include/functions.php");
//echo"<pre>";print_r('test after functions');die;


$mobile_no=$_REQUEST['mobile_no'];
//echo $mobile_no; die;
$OTP=generate_OTP();

if($OTP=='' || strlen($OTP)!=4)
{
	echo 0;
}
else
{
	echo $OTP;	
}
mysqli_close($link);
?>