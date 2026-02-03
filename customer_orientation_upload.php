<?php
 /*ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);*/
 
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
date_default_timezone_set('Asia/Kolkata');

$emp_code=$_REQUEST['emp_code'];

$datetime = gmdate('Y-m-d H:m:s');
$datetime = date('Y-m-d H:i:s');
$date= date('Y-m-d');
$url = APICALLLOGURL."/customer_orientation_upload.php.php?nick_name=$nick_name&emp_code=$emp_code";
insertapilog($datetime,$emp_code,$url,$nick_name);

$sql_distinct_date = "SELECT * FROM `customer_orientation` WHERE emp_code='".$emp_code."' AND Date_Format(`date_time`,'%Y-%m-%d')= '".$date."'
					ORDER BY `date_time` DESC limit 1";
					
$res_distinct_date = mysqli_query($link,$sql_distinct_date);
$total_rows = mysqli_num_rows($res_distinct_date);
//echo $total_rows;
if($total_rows>0){
    $sqlInsert="update customer_orientation SET
					date_time='".$datetime."' WHERE emp_code='".$emp_code."' AND Date_Format(`date_time`,'%Y-%m-%d')= '".$date."'";
					
		if(mysqli_query($link,$sqlInsert))
		{
			echo "1";
		}
		else
		{
			echo "0";
		}
}else{

		$sqlInsert="INSERT INTO customer_orientation SET
					emp_code='".$emp_code."'";
		if(mysqli_query($link,$sqlInsert))
		{
			echo "1";
		}
		else
		{
			echo "0";
		}

}
mysqli_close($link);
?>
