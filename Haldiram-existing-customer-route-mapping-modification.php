<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");

$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
$sqlselectSOEmployee='SELECT emp_code FROM employee_master WHERE ';
?>