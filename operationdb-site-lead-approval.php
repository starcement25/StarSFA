<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

$emp_code=$_POST['emp_code'];
$survey_id=$_POST['survey_id'];
$status=$_POST['status'];
$actual_date_delivery=$_POST['actual_date_delivery'];
$delivery_remarks=$_POST['delivery_remarks'];
$reason_not_delivery=$_POST['reason_not_delivery'];


$sqlupdateapprovedet="UPDATE survey_header SET 
						  status 						='".$status."',
						  status_updated_datetime 		=CURRENT_TIMESTAMP(),
						  status_updated_by 			='".$emp_code."',
						  actual_date_delivery 			='".$actual_date_delivery."',
						  delivery_remarks 				='".$delivery_remarks."',
						  reason_not_delivery 			='".$reason_not_delivery."'
						  WHERE survey_id='".$survey_id."'";											  
	if(mysqli_query($link,$sqlupdateapprovedet))
	{
		echo $flag=1;
	}
	else
	{
		echo $flag=0;
	}
	
mysqli_close($link);
?>
