<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_DNV");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	//require("include/config-email-setup.php");

	
	//$sqlupdateproductCP="UPDATE product_master SET TD='5',download_time=current_timestamp() WHERE pack_size='CP' AND vertical_value!='Specialty Fats'";
	$sqlupdateproductCP="SELECT * 
FROM  `order_header` 
WHERE order_no LIKE  'O%'
AND customer_code
IN (

SELECT customer_code
FROM customer_master
WHERE cust_type =  'D'
)";
	$rsupdateproductCP=mysqli_query($link,$sqlupdateproductCP);
	while($rowupdateproductCP=mysqli_fetch_assoc($rsupdateproductCP))
	{
		$customer_code=$rowupdateproductCP['customer_code'];
		$order_no=$rowupdateproductCP['order_no'];
		
		$sqlcustomertype="SELECT cust_type FROM customer_master WHERE customer_code='".$customer_code."'";
		$rscustomertype=mysqli_query($link,$sqlcustomertype);
		$rowcustomertype=mysqli_fetch_assoc($rscustomertype);
		$cust_type=$rowcustomertype['cust_type'];
		
		$sqlselectorderdetails="SELECT sku_code,sale_rate,qty FROM order_details WHERE order_no='".$order_no."'";
		$rsselectorderdetails=mysqli_query($link,$sqlselectorderdetails);
		while($rowselectorderdetails=mysqli_fetch_assoc($rsselectorderdetails))
		{
			$sku_code=$rowselectorderdetails['sku_code'];
			$sale_rate=$rowselectorderdetails['sale_rate'];
			$qty=$rowselectorderdetails['qty'];
			
			$sqlmrpdetails="SELECT mrp,sale_rate,UOM,ws_rate,distributor_rate,ss_rate FROM mrp WHERE product_code='".$sku_code."'";
			$rsmrpdetails=mysqli_query($link,$sqlmrpdetails);
			$recmrpdetails=mysqli_fetch_assoc($rsmrpdetails);
			$distributor_rate=$recmrpdetails['distributor_rate'];
			
			if($cust_type=='R')  $sale_rate=$sale_rate;
			if($cust_type=='D')  $sale_rate=$distributor_rate;
			
			$amount=$qty*$sale_rate;
			
			echo $sqlupdateorderdetails="UPDATE order_details SET sale_rate='".$sale_rate."',amount='".$amount."' WHERE order_no='".$order_no."' AND sku_code='".$sku_code."'";
			//exit();
			mysqli_query($link,$sqlupdateorderdetails);
		}
	}
		
	mysqli_close($link);
?>