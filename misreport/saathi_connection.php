<?php
	date_default_timezone_set('Asia/Kolkata');
    if (isset($type) && $type == 'remortdb') {
    // Use remote DB connection
    //Live connection
    // define("SERVERREMOTE","172.17.0.2");
	// define("USERREMOTE","root");
	// define("PASSWORDREMOTE","Passw0rd123#$");
	// define("DBREMOTE","starsaat_STARS");
    //Dev connection

    // define("SERVERREMOTE","3.111.228.173");
	// define("USERREMOTE","root");
	// define("PASSWORDREMOTE","Passw0rd123#$");
	// define("DBREMOTE","starsaathi_STARS");

	// define("SERVERREMOTE","52.66.97.178");
	// define("USERREMOTE","root");
	// define("PASSWORDREMOTE","UZzRXN4CMJtOaUq7");
	// define("DBREMOTE","starsaathi_STARS");
	define("SERVERREMOTE","starsaathi-rds-server.clcy6zb4izp8.ap-south-1.rds.amazonaws.com");
	define("USERREMOTE","admin");
	define("PASSWORDREMOTE","zwPB6L65ZC}p8L89");
	define("DBREMOTE","starsaathi_STARS");

	$link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,DBREMOTE) or die("Database Connection Error.");

    } 

    if (isset($type) && $type == 'localdb') {

       define("SERVER","localhost");
		define("USER","root");
		define("PASSWORD","Passw0rd123#$");
		define("DB","acedns_STAR");

		$link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");
    }