<?php
require("include/config.php");
require("include/dbcon.php");

$newpassword=$_POST['newpassword'];
$emp_code=$_POST['emp_code'];
$oldpassword=$_POST['oldpassword'];
//$sqlquery="select * from employee_master";

$sqlUpdate="UPDATE changepassword SET
			newpassword='".$newpassword."',
			oldpassword='".$oldpassword."'
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
