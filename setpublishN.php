<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_ASL");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	//require("include/config-email-setup.php");

	$sqlupdatemrp="UPDATE sauda_mrp SET acedns='N'";
	if(mysqli_query($link,$sqlupdatemrp))
	{
		$sqlupdateindustrial="UPDATE industrial_rate SET acedns='N'";
		if(mysqli_query($link,$sqlupdateindustrial))
		{
			echo 'SUCCESS';
		}
	}
	else
	{
		echo 'FAILURE';
	}
	
	mysqli_close($link);
?>