<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_ABDOS");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	//require("include/config-email-setup.php");

	$sqlselorderdetails="SELECT * FROM `order_details` WHERE `weightage` IS NOT NULL  and `UOM`='PC' AND `weightage`='0'";
	$rsselorderdetails=mysqli_query($link,$sqlselorderdetails);
	while($rowselorderdetails=mysqli_fetch_assoc($rsselorderdetails))
	{
	$sku_code=$rowselorderdetails['sku_code'];
	$order_no=$rowselorderdetails['order_no'];
	
	$qty=$rowselorderdetails['qty'];
	echo $weightage_conversion2="SELECT weightage_conversio1 FROM state_product_wise_weightage WHERE prod_code='".$sku_code."'";
	$rsweightage_conversion2=mysqli_query($link,$weightage_conversion2);
	$rowweightage_conversion2=mysqli_fetch_assoc($rsweightage_conversion2);
	$weightage_conversion2_val=$rowweightage_conversion2['weightage_conversio1'];
	$weightageval=$qty*$weightage_conversion2_val;
	echo $sqlupdateweightage="UPDATE order_details SET weightage='".$weightageval."' WHERE sku_code='".$sku_code."' AND order_no='".$order_no."'  AND UOM='PC'";
	mysqli_query($link,$sqlupdateweightage);
	}
	echo '1';
	mysqli_close($link);
?>