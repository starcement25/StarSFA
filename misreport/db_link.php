<?php
session_start();



    $db = "acedns_".strtoupper($_SESSION['nick_name']);

define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","$db");

$link=mysqli_connect(SERVER,USER,PASSWORD,DB);

  
  
  
/*	$db = "acedns_".strtoupper($_SESSION['nick_name']);
	
	
	
	
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234");
	define("DB","$db");

	$link=mysqli_connect(SERVER,USER,PASSWORD,DB);
	
	echo $db;
	
	
	$link=mysqli_connect("localhost","acedns_dnsprod","dnsprod1234",".$db.") or die("Setup Database Connection Error.");
	
	//$link=mysqli_connect("localhost","acedns_dnsprod","dnsprod1234",$db);
*/	
?>