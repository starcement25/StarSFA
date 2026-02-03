<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_ASL");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	//require("include/config-email-setup.php");
		
	$sqlcustomertruckload="SELECT DISTINCT loadability_ton FROM customer_master WHERE  transport_mode='Truck' AND loadability_ton >0 ORDER BY loadability_ton ASC";
	$rscustomertruckload=mysqli_query($link,$sqlcustomertruckload);
	while($rowcustomertruckload=mysqli_fetch_assoc($rscustomertruckload))
	{
		$transport_mode=$rowcustomertruckload['transport_mode'];
		$loadability_ton=$rowcustomertruckload['loadability_ton'];		
		$sqlproduct="SELECT dns_prod_code,conversion_factor_two FROM product_master WHERE acedns='Y'";
		$rsproduct=mysqli_query($link,$sqlproduct);
		while($rowproduct=mysqli_fetch_assoc($rsproduct))
		{
			$dns_prod_code=$rowproduct['dns_prod_code'];
			$conversion_factor_two=$rowproduct['conversion_factor_two'];
			$load_distribution=$loadability_ton/$conversion_factor_two;
							
			$sqlinsertloaddist="INSERT INTO load_distribution SET 
								plant_name='BHIWADI',
								transport_mode='Truck',
								truck_load='".$loadability_ton."',
								prod_code='".$dns_prod_code."',
								qty_truck_load='".$load_distribution."',
								datetime=CURRENT_TIMESTAMP(),
								user_id='".$_SESSION['admin_login']."',
								ip_address='".$_SERVER['REMOTE_ADDR']."'";
		   mysqli_query($link,$sqlinsertloaddist);						
		}
	}
	mysqli_close($link);
?>