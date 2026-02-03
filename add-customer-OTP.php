<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
if($nick_name!=''){
$mobile_no=$_REQUEST['mobile_no'];
$emp_code=$_REQUEST['emp_code'];
$digits = 4;
$digits_id = 9;
 $OTP=rand(pow(10, $digits-1), pow(10, $digits)-1);
 $OTP_id=rand(pow(10, $digits_id-1), pow(10, $digits_id)-1);
 //For sms
 
 echo $OTP_id.'#'.$OTP;
}
?>