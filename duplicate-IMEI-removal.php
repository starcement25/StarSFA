<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_VCONNECT");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	//require("include/config-email-setup.php");
		
	//$date_val=date('Y-m-d', strtotime(date('Y-m-d') ." -1 day"));
	$sqlsaledetails="SELECT IMEI FROM `stock_out_details` GROUP BY IMEI HAVING COUNT(IMEI) >1";
	$rssaledetails=mysqli_query($link,$sqlsaledetails);
	while($rowsaledetails=mysqli_fetch_assoc($rssaledetails))
	{
		$IMEI=$rowsaledetails['IMEI'];
		$sqlselbilling="SELECT stock_out_id FROM stock_out_details WHERE IMEI='".$IMEI."' 
						ORDER BY DATE_FORMAT(SUBSTRING(stock_out_id,-14,14),'%Y-%m-%d %H:%i:%s') DESC LIMIT 0,1";
		$rsselbilling=mysqli_query($link,$sqlselbilling);
		$rowselbilling=mysqli_fetch_assoc($rsselbilling);
		$stock_out_id=$rowselbilling['stock_out_id'];
		
		echo $sqldelstockout="DELETE FROM stock_out_details WHERE IMEI='".$IMEI."' AND stock_out_id <> '".$stock_out_id."'";
		mysqli_query($link,$sqldelstockout);
		
	}
	mysqli_close($link);
?>