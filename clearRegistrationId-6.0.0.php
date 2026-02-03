<?php
require("include/config.php");
require("include/dbcon.php");

$deviceId=$_POST['deviceId'];
$emp_code=$_POST['emp_code'];
$registrationid=$_POST['registrationid'];

$sqlUpdate="UPDATE changepassword SET
			registrationid='' 
			WHERE emp_code='".$emp_code."'";
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
