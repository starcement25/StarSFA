<?php

$nick_name = "";
	define("DB","");
	define("SERVER","localhost");
	define("USER","root");
	define("PASSWORD","Passw0rd123#$");
	define("APICALLLOGURL","http://demo.acedns.in");

function con_db($nick_name)
{
	$nick_name="acedns_".$nick_name;


	//$ddb = "acedns_".$nick_name;
	$ddb = "acedns_acednsproduct";
	define("DB","$ddb");


	
	$link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");

	
	return $link;
}

function con_setup(){
    $linksetup=mysqli_connect("localhost","root","Passw0rd123#$","acedns_acednsproduct") or die("Setup Database Connection Error.");
    
    return $linksetup;
}

/*		define("SERVERREMOTE","52.66.101.239");
        define("USERREMOTE","root");
        define("PASSWORDREMOTE","cmcl@123");

*/







?>