<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
require("include/config.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_GET['emp_code'];
$deviceId=$_GET['deviceId'];
//$sqlquery="select * from employee_master";

$sqlselect="SELECT is_delete FROM dbbackupcheck  WHERE emp_code='".$emp_code."' and device_id='".$deviceId."'";
$sqlselect="SELECT is_delete FROM dbbackupcheck  WHERE emp_code='".$emp_code."'";
//echo $sqlselect;
$rsselect=mysqli_query($link,$sqlselect);
$count=mysqli_num_rows($rsselect);
$rowselect=mysqli_fetch_assoc($rsselect);
$is_delete=$rowselect['is_delete'];

$sqlupdate="UPDATE dbbackupcheck SET is_delete='0' WHERE emp_code='".$emp_code."' and device_id='".$deviceId."'";

$sqlupdate="UPDATE dbbackupcheck SET is_delete='0' WHERE emp_code='".$emp_code."'";

$rsupdate=mysqli_query($link,$sqlupdate);

/*$upd_sql="UPDATE changepassword SET deviceid='',registrationid='' WHERE emp_code = '" .$emp_code."'";
mysqli_query($link,$upd_sql) or die(mysqli_error()." Error in device clear.");*/


echo $is_delete;
mysqli_close($link);
?>
