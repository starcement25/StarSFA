<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$mobile_no=$_REQUEST['mobile_no'];
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