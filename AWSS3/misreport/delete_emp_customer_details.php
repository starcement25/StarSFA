<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","acedns_MINU");

mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB);

$sql_customer_details = "DELETE FROM customer_master WHERE emp_code = 'E0013'";
$res_customer_details = mysql_query($sql_customer_details);

$sql_order_header = "DELETE FROM order_header WHERE SUBSTRING(order_no,-19,5) = 'E0013'";
$res_order_header = mysql_query($sql_order_header);

$sql_order_details = "DELETE FROM order_details WHERE SUBSTRING(order_no,-19,5) = 'E0013'";
$res_order_details = mysql_query($sql_order_details);

$sql_payment_header = "DELETE FROM payment_header WHERE SUBSTRING(receipt_id,-19,5) = 'E0013'";
$res_payment_header = mysql_query($sql_payment_header);

$sql_payment_details = "DELETE FROM payment_details WHERE SUBSTRING(receipt_id,-19,5) = 'E0013'";
$res_payment_details = mysql_query($sql_payment_header);

$sql_prospect = "DELETE FROM `prospective_customer_details` WHERE SUBSTRING(trans_id,-19,5) = 'E0013'";
$res_prospect = mysql_query($sql_prospect);

$sql_location = "DELETE FROM `location` WHERE `emp_code` = 'E0013'";
$res_location = mysql_query($sql_location);

$sql_route = "DELETE FROM `route_master` WHERE `emp_code` = 'E0013'";
$res_route = mysql_query($sql_route);

mysql_close($link);
?>