<?php
ob_start();
session_start();
require("adminUtils.php");
$month_select = $_REQUEST['month_select'];
$newdate=$_REQUEST['newdate'];
$sql="SELECT validation_month FROM yellow_card_date_validation WHERE validation_month='".$month_select."'";
$rsquery=mysqli_query($link,$sql);
$countquery=mysqli_num_rows($rsquery);
if($countquery >0){
	$sqlup="UPDATE yellow_card_date_validation SET validation_last_date='".$newdate."',ip_address='".$_SERVER['REMOTE_ADDR']."' 
			WHERE validation_month='".$month_select."'";
	mysqli_query($link,$sqlup);
}
else
{
	$sqlinsert="INSERT INTO yellow_card_date_validation SET validation_month='".$month_select."',validation_last_date='".$newdate."',
				ip_address='".$_SERVER['REMOTE_ADDR']."'";
	mysqli_query($link,$sqlinsert);
}
echo "<b>Yellow card date validation updated successfully.</b>";
?>