<?php
require("include/config.php");
require("include/dbcon.php");

$deviceId=$_POST['deviceId'];
$emp_code=$_POST['emp_code'];
//$sqlquery="select * from employee_master";

$sqlUpdate="UPDATE changepassword SET
			deviceid='".$deviceId."'
			WHERE emp_code='".$emp_code."'";
if(mysqli_query($link,$sqlUpdate))
{
	echo "1";
}
else
{
	echo "0";
}
		
?>
