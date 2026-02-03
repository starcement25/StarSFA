<?php
define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	//define("DB","acedns_VCONNECT");
	define("DB","acedns_DURO");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");

	$sqlcustomer="SELECT f_code FROM facilitator_master";
	$rscustomer=mysqli_query($link,$sqlcustomer);
	while($rowcustomer=mysqli_fetch_assoc($rscustomer))
	{
		$f_code=$rowcustomer['f_code'];
				$sqlchkexists="SELECT value  FROM survey_output WHERE value LIKE '%".$f_code."%' and type='Site Visit'";
				$rschkexists=mysqli_query($link,$sqlchkexists);
				$emp_exists=mysqli_num_rows($rschkexists);
				if($emp_exists > 0)
				{
				$sqlinsert="INSERT INTO faci_test SET f_code='".$f_code."'";
				mysqli_query($link,$sqlinsert);
				}
	}