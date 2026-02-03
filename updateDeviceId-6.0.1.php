<?php
/*
ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
 */
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
$deviceId=$_REQUEST['deviceId'];
$emp_code=$_REQUEST['emp_code'];

/*if(app_phoneno_login=='yes')
{
	$sqlselectempcode="SELECT emp_code FROM employee_master WHERE phone_no='".$emp_code."'";
	$rsselectempcode=mysqli_query($link,$sqlselectempcode);
	$rowselectempcode=mysqli_fetch_assoc($rsselectempcode);
	$emp_code_db=$rowselectempcode['emp_code'];
}
else
{
	$emp_code_db=$emp_code;
}*/
//$sqlquery="select * from employee_master";
$sqlselectversion="SELECT version_code FROM db_version ";
$rsselectversion=mysqli_query($link,$sqlselectversion);
$rowselectversion=mysqli_fetch_assoc($rsselectversion);
$versionCode=$rowselectversion['version_code'];
$sqlUpdate="UPDATE changepassword SET
			deviceid='".$deviceId."'
			WHERE emp_code='".$emp_code."'";

if(mysqli_query($link,$sqlUpdate))
{
	$sqlchecktablestructure="SELECT emp_code FROM table_structure_updation WHERE device_id='".$deviceId."' AND emp_code='".$emp_code."'";
	$rschecktablestructure=mysqli_query($link,$sqlchecktablestructure);
	$cntchecktablestructure=mysqli_num_rows($rschecktablestructure);
	if($cntchecktablestructure==0)
	{
		$sqlInsert="INSERT INTO table_structure_updation SET
						emp_code='".$emp_code."',
						db_version_code='".$versionCode."',
						device_id='".$deviceId."',
						is_update='0'";
		mysqli_query($link,$sqlInsert);
	}
	else
	{
	$sqlupdatetablestructure="UPDATE table_structure_updation SET emp_code='".$emp_code."' WHERE
							device_id='".$deviceId."' AND emp_code='".$emp_code."'";
	mysqli_query($link,$sqlupdatetablestructure);
	}
	echo "1";
}
else
{
	echo "0";
}
mysqli_close($link);
?>
