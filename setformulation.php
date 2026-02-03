<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_ASL");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	//require("include/config-email-setup.php");

	$sqlupdatemrp="SELECT DISTINCT prod_code FROM loose_oilrate_formulation WHERE prod_code IN(SELECT dns_prod_code FROM product_master WHERE acedns='Y')";
	$rsupdatemrp=mysqli_query($link,$sqlupdatemrp);
	while($rowupdatemrp=mysqli_fetch_assoc($rsupdatemrp))
	{
	$prod_code=$rowupdatemrp['prod_code'];
	$latestdate="SELECT datetime FROM loose_oilrate_formulation WHERE prod_code='".$prod_code."' ORDER BY datetime DESC LIMIT 0,1";
	$rslatestdate=mysqli_query($link,$latestdate);
	$rowlatestdate=mysqli_fetch_assoc($rslatestdate);
	$datetime=$rowlatestdate['datetime'];
	$sqlupdateindustrial="UPDATE loose_oilrate_formulation SET acedns='Y' WHERE prod_code='".$prod_code."' AND datetime='".$datetime."'";
	mysqli_query($link,$sqlupdateindustrial);
	}
	echo '1';
	mysqli_close($link);
?>