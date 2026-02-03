<?php
require("include/config.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_POST['emp_code'];
$deviceId=$_POST['deviceId'];
//$sqlquery="select * from employee_master";

$sqlselect="SELECT * FROM dbbackupcheck  WHERE device_id='".$deviceId."'";
$rsselect=mysqli_query($link,$sqlselect);
$count=mysqli_num_rows($rsselect);
$rowselect=mysqli_fetch_assoc($rsselect);
	
if($count==0)
{		
	$sqlInsert="INSERT INTO dbbackupcheck SET
				emp_code='".$emp_code."',
				device_id='".$deviceId."',
				is_checked='0'";
	 if(mysqli_query($link,$sqlInsert))
	 {
		 echo '0';
	 }
	 else
	 {
		 echo '3';
	 }
}
else
{
	echo $rowselect['is_checked'];
}
mysqli_close($link);
?>
