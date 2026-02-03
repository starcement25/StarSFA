<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","acedns_MINU");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);

$sql_customer_details = "DELETE FROM customer_master WHERE emp_code = 'E0013'";
$res_customer_details = mysqli_query($link,$sql_customer_details);

$sql_order_header = "DELETE FROM order_header WHERE SUBSTRING(order_no,-19,5) = 'E0013'";
$res_order_header = mysqli_query($link,$sql_order_header);

$sql_order_details = "DELETE FROM order_details WHERE SUBSTRING(order_no,-19,5) = 'E0013'";
$res_order_details = mysqli_query($link,$sql_order_details);

$sql_payment_header = "DELETE FROM payment_header WHERE SUBSTRING(receipt_id,-19,5) = 'E0013'";
$res_payment_header = mysqli_query($link,$sql_payment_header);

$sql_payment_details = "DELETE FROM payment_details WHERE SUBSTRING(receipt_id,-19,5) = 'E0013'";
$res_payment_details = mysqli_query($link,$sql_payment_header);

$sql_prospect = "DELETE FROM `prospective_customer_details` WHERE SUBSTRING(trans_id,-19,5) = 'E0013'";
$res_prospect = mysqli_query($link,$sql_prospect);

$sql_location = "DELETE FROM `location` WHERE `emp_code` = 'E0013'";
$res_location = mysqli_query($link,$sql_location);

$sql_route = "DELETE FROM `route_master` WHERE `emp_code` = 'E0013'";
$res_route = mysqli_query($link,$sql_route);

mysqli_close($link);
?>