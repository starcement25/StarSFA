<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234");
	//require("include/config-setup.php");
	
	$linksetupDCR=mysqli_connect(SERVER,USER,PASSWORD) or die("Setup Database Connection Error.");
	mysqli_select_db("acedns_CSPL",$linksetupDCR) or die("could not connect the setup database");
	
	echo $_REQUEST['approval'];
	
	echo "ORDER APPROVED.";

?>