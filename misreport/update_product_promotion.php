<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>

<?php
$prospect_id = $_REQUEST['prospect_id'];
$customer_name = $_REQUEST['customer_name'];
$pincode = $_REQUEST['pincode'];
$streetname = $_REQUEST['streetname'];
$streetno = $_REQUEST['streetno'];
$phone = $_REQUEST['phone'];
$buildingno = $_REQUEST['buildingno'];
$apartno = $_REQUEST['apartno'];
$oilused = $_REQUEST['oilused'];

$sql_product_promotion = "UPDATE product_promotion SET prospect_name = '".$customer_name."', pin = '".$pincode."', street_name = '".$streetname."', street_no = '".$streetno."', building_no = '".$buildingno."', apartment_no = '".$apartno."', phone_no = '".$phone."', oil_used = '".$oilused."' WHERE prospect_code = '".$prospect_id."'";
$res_product_promotion = mysqli_query($link,$sql_product_promotion);


echo "Data updated successfully";

mysqli_close($link);
?>