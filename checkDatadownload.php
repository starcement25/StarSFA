<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

$emp_code=$_REQUEST['emp_code'];

$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));

$contents =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second;

$sqlemplogchk="SELECT is_download FROM emp_data_download_log WHERE emp_code='".$emp_code."'";
$rsemplogchk=mysqli_query($link,$sqlemplogchk);
$countemplogchk=mysqli_num_rows($rsemplogchk);
if($countemplogchk>0)
{
	$rowemplogchk=mysqli_fetch_assoc($rsemplogchk);
	$is_download=$rowemplogchk['is_download'];
	if($is_download=='yes')
	{
		$response=1;
	}
	else
	{
		$response=1;
	}
}
else
{
	$response=0;
}
echo $response;
   $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/checkDatadownload.php?nick_name=$nick_name&emp_code=$emp_code";
insertapilog($datetime,$emp_code,$url,$nick_name);
?>
