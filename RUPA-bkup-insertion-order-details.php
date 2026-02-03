<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	define("SERVERREMOTE","103.241.144.155");
	define("USERREMOTE","acedns_dnsprod");
	define("PASSWORDREMOTE","dnsprod1234");
	
	//$db_namearray=array('RUPA','PARLE','ABDOS');
	$db_namearray=array('EMAMI','UCLINDIA');
	foreach($db_namearray as $dbval)
	{
		$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
		$linkremote=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE) or die("Database Connection Error remote.");

		//define("DB","acedns_$dbval");	
		//define("DBREMOTE","acedns_$dbval");

		mysqli_select_db("acedns_$dbval",$link) or die("could not connect the database for invalid nick name");
		mysqli_select_db("acedns_$dbval",$linkremote) or die("could not connect the database for invalid nick name remote");

		/*if($dbval=='RUPA')
		{
			$date_condition="WHERE DATE_FORMAT(SUBSTRING(order_no,-14,14),'%Y-%m-%d %H:%i:%s') >'2016-12-17 23:59:59'";
		}
		if($dbval=='PARLE')
		{
			$date_condition="WHERE DATE_FORMAT(SUBSTRING(order_no,-14,14),'%Y-%m-%d %H:%i:%s') >'2016-12-20 23:59:59'";
		}
		if($dbval=='ABDOS')
		{
			$date_condition="WHERE DATE_FORMAT(SUBSTRING(order_no,-14,14),'%Y-%m-%d %H:%i:%s') >'2016-12-22 23:59:59'";
		}*/
		if($dbval=='EMAMI')
		{
			$date_condition="WHERE DATE_FORMAT(SUBSTRING(order_no,-14,14),'%Y-%m-%d %H:%i:%s') >'2017-01-19 23:59:59'";
		}
		if($dbval=='UCLINDIA')
		{
			$date_condition="WHERE DATE_FORMAT(SUBSTRING(order_no,-14,14),'%Y-%m-%d %H:%i:%s') >'2017-02-02 23:59:59'";
		}
		$sqllocationbkup="SELECT * FROM order_details ".$date_condition;
		$rslocationbkup=mysqli_query($link,$sqllocationbkup,$linkremote);
		$countlocationbkup=mysqli_num_rows($rslocationbkup);
		while($rowlocationbkup=mysqli_fetch_assoc($rslocationbkup))
		{
			$order_no_bkup=$rowlocationbkup['order_no'];
			$sku_code_bkup=$rowlocationbkup['sku_code'];
			$qty_bkup=$rowlocationbkup['qty'];
			$mrp_code_bkup=$rowlocationbkup['mrp_code'];
			$TD_bkup=$rowlocationbkup['TD'];
			$premium_bkup=$rowlocationbkup['premium'];
			$VAT_bkup=$rowlocationbkup['VAT'];
			$sale_rate_bkup=$rowlocationbkup['sale_rate'];
			$freight_charge_bkup=$rowlocationbkup['freight_charge'];
			$amount_bkup=$rowlocationbkup['amount'];
			//$billed_qty_bkup=$rowlocationbkup['billed_qty'];
			$transferred_bkup=$rowlocationbkup['transferred'];
			
			$sqllocationchk="SELECT * from order_details WHERE order_no='".$order_no_bkup."' AND sku_code='".$sku_code_bkup."'";
			$rslocationchk=mysqli_query($link,$sqllocationchk,$link);
			$countlocationchk=mysqli_num_rows($rslocationchk);
		
			if($countlocationchk==0)
			{
				$sql  = "insert into order_details ";
				$sql .= " SET order_no='".$order_no_bkup."'";
				$sql .= " , sku_code='".$sku_code_bkup."'";
				$sql .= " , qty='".$qty_bkup."'";
				$sql .= " , mrp_code='".$mrp_code_bkup."'";
				$sql .= " , TD='".$TD_bkup."'";
				$sql .= " , premium='".$premium_bkup."'";
				$sql .= " , VAT='".$VAT_bkup."'";
				$sql .= " , sale_rate='".$sale_rate_bkup."'";
				$sql .= " , freight_charge='".$freight_charge_bkup."'";
				$sql .= " , amount='".$amount_bkup."'";
				$sql .= " , transferred='".$transferred_bkup."'";
				mysqli_query($link,$sql,$link);
			}
		}
		mysqli_close($link);
		mysqli_close($linkremote);
	}
?>