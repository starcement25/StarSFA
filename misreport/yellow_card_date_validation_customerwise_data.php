<?php
ob_start();
session_start();
require("adminUtils.php");
$branch = $_REQUEST['branch'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$validation_date=$_REQUEST['validation_date'];

$selselcustomer="SELECT customer_code,branch_code FROM customer_master WHERE branch_code IN(".$branch.")";
$rsselcustomer=mysqli_query($link,$selselcustomer);
while($rowselcustomer=mysqli_fetch_assoc($rsselcustomer))
{
	$customer_code=$rowselcustomer['customer_code'];
	$branch_code=$rowselcustomer['branch_code'];

	$sql="SELECT customer_code FROM yellow_card_date_validation_customerwise WHERE customer_code='".$customer_code."'";
	$rsquery=mysqli_query($link,$sql);
	$countquery=mysqli_num_rows($rsquery);
	if($countquery >0){
		$sqlup="UPDATE yellow_card_date_validation_customerwise SET validation_from='".$start_date."',validation_to='".$end_date."', 
				validation_last_date='".$validation_date."',validation_create_date=CURRENT_TIMESTAMP(),ip_address='".$_SERVER['REMOTE_ADDR']."', 
					branch='".$branch_code."' WHERE customer_code='".$customer_code."'";
		mysqli_query($link,$sqlup);
	}
	else
	{
		$sqlinsert="INSERT INTO yellow_card_date_validation_customerwise SET customer_code='".$customer_code."',validation_from='".$start_date."'
					,validation_to='".$end_date."',validation_last_date='".$validation_date."',validation_create_date=CURRENT_TIMESTAMP(), 
				branch='".$branch_code."',ip_address='".$_SERVER['REMOTE_ADDR']."'";
		mysqli_query($link,$sqlinsert);
	}
}
echo "<b>Yellow card date validation updated successfully.</b>";
?>