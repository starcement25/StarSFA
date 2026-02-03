<?php
require("include/config.php");
require("include/dbcon.php");

$emp_code=$_REQUEST['emp_code'];

//$deviceId=$_POST['deviceId'];
//$sqlquery="select * from employee_master";
$sqlselect="SELECT is_licensed FROM changepassword WHERE emp_code='".$emp_code."'";
$rsselect=mysqli_query($link,$sqlselect);
$count=mysqli_num_rows($rsselect);
$rowselect=mysqli_fetch_assoc($rsselect);
if($count>0)
{
	echo $rowselect['is_licensed'];
}
else
{
	echo '2';
}
?>
