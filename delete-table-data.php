<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_STAR");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	//require("include/config-email-setup.php");

	$sqldel="delete FROM apicalllog where SUBSTRING(date_time,1,10) < '2022-08-01'";
	$rsdel=mysqli_query($link,$sqldel);
	
	echo '1';
	mysqli_close($link);
?>