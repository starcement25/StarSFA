<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>

<?php
$customer_name = $_REQUEST['customer_name'];
$pincode = $_REQUEST['pincode'];
$streetname = $_REQUEST['streetname'];
$streetno = $_REQUEST['streetno'];
$phone = $_REQUEST['phone'];
$buildingno = $_REQUEST['buildingno'];
$apartno = $_REQUEST['apartno'];
$oilused = $_REQUEST['oilused'];

$trans_id = "PP".strtoupper($_SESSION['admin_login']).date('YmdHms');

$sql_product_promotion = "INSERT INTO product_promotion SET prospect_code = '".$trans_id."', prospect_name = '".$customer_name."', pin = '".$pincode."', street_name = '".$streetname."', street_no = '".$streetno."', building_no = '".$buildingno."', apartment_no = '".$apartno."', phone_no = '".$phone."', oil_used = '".$oilused."'";
$res_product_promotion = mysql_query($sql_product_promotion);

$sql_location = "INSERT INTO location SET emp_code = '".strtoupper($_SESSION['admin_login'])."', trans_id = '".$trans_id."', date = current_timestamp, updatetime = current_timestamp";
$res_location = mysql_query($sql_location);

echo "Data inserted successfully";
mysql_close($link);
?>