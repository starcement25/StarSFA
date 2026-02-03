<?php
date_default_timezone_set("Asia/Kolkata");
try {
$dbhost = 'localhost';
$dbname='acedns_START';
$dbuser = 'acedns_dnsprod';
$dbpass = 'dnsprod1234#';
$connec = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
}catch (PDOException $e) {
echo "Error : " . $e->getMessage() . "<br/>";
die();
}
?>