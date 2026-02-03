<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234");
	//require("include/config-setup.php");
	
	$linksetupDCR=mysqli_connect(SERVER,USER,PASSWORD) or die("Setup Database Connection Error.");
	mysqli_select_db("acedns_RKBK",$linksetupDCR) or die("could not connect the setup database");
$sql="update table_structure_updation SET is_update='1' WHERE SUBSTRING(emp_code,1,1)='C' AND db_version_code='6.8'";
mysqli_query($link,$sql);

?>