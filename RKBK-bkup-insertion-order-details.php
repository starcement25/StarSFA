<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234");
	define("DBFETCH","acedns_RKBKT");
	define("DBINSERT","acedns_RKBK");
	$linkfetch=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	$linkinsert=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");

	mysqli_select_db(DBFETCH,$linkfetch) or die("could not connect the database for invalid nick name");

$sqllocationbkup="SELECT * FROM order_details WHERE order_no LIKE '%E0037%' AND DATE_FORMAT(SUBSTRING(order_no,-22,8),'%Y-%m-%d') BETWEEN '2016-07-01' AND '2016-08-12'";
$rslocationbkup=mysqli_query($link,$sqllocationbkup,$linkfetch);
$countlocationbkup=mysqli_num_rows($rslocationbkup);
while($rowlocationbkup=mysqli_fetch_assoc($rslocationbkup))
{
	$row_id_bkup=$rowlocationbkup['row_id'];
	$order_no_bkup=$rowlocationbkup['order_no'];
	$sku_code_bkup=$rowlocationbkup['sku_code'];
	$qty_bkup=$rowlocationbkup['qty'];
	$new_qty_bkup=$rowlocationbkup['new_qty'];
	$mrp_code_bkup=$rowlocationbkup['mrp_code'];
	$TD_bkup=$rowlocationbkup['TD'];
	$premium_bkup=$rowlocationbkup['premium'];
	$VAT_bkup=$rowlocationbkup['VAT'];
	$sale_rate_bkup=$rowlocationbkup['sale_rate'];
	$freight_charge_bkup=$rowlocationbkup['freight_charge'];
	$amount_bkup=$rowlocationbkup['amount'];
	$new_sale_rate_bkup=$rowlocationbkup['new_sale_rate'];
	$transaction_type_bkup=$rowlocationbkup['transaction_type'];
	
	mysqli_select_db(DBINSERT,$linkinsert) or die("could not connect the database for invalid nick name");

	$sqllocationchk="SELECT * from order_details WHERE row_id='".$row_id_bkup."'";
	$rslocationchk=mysqli_query($link,$sqllocationchk,$linkinsert);
	$countlocationchk=mysqli_num_rows($rslocationchk);

	if($countlocationchk==0)
	{
		$sql  = "insert into order_details ";
		$sql .= " SET row_id='".$row_id_bkup."'";
		$sql .= " , order_no='".$order_no_bkup."'";
		$sql .= " , sku_code='".$sku_code_bkup."'";
		$sql .= " , qty='".$qty_bkup."'";
		$sql .= " , new_qty='".$new_qty_bkup."'";
		$sql .= " , mrp_code='".$mrp_code_bkup."'";
		$sql .= " , TD='".$TD_bkup."'";
		$sql .= " , premium='".$premium_bkup."'";
		$sql .= " , VAT='".$VAT_bkup."'";
		$sql .= " , sale_rate='".$sale_rate_bkup."'";
		$sql .= " , freight_charge='".$freight_charge_bkup."'";
		$sql .= " , amount='".$amount_bkup."'";
		$sql .= " , new_sale_rate='".$new_sale_rate_bkup."'";
		$sql .= " , transaction_type='".$transaction_type_bkup."'";
		mysqli_query($link,$sql,$linkinsert);
	}
}
	
?>