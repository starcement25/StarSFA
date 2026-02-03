<?php
		define("SERVERREMOTE","52.66.101.239");
		define("USERREMOTE","root");
		define("PASSWORDREMOTE","cmcl@123");
		define("DBREMOTE","acedns_STAR");
		$link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE) or die("Database Connection Error.");
		mysqli_select_db(DBREMOTE,$link) or die("could not connect the database for invalid nick name");

$sqllocationbkup="SELECT * FROM order_header_bkup";
$rslocationbkup=mysqli_query($link,$sqllocationbkup);
$countlocationbkup=mysqli_num_rows($rslocationbkup);
while($rowlocationbkup=mysqli_fetch_assoc($rslocationbkup))
{
	$order_no_bkup=$rowlocationbkup['order_no'];
	$customer_code_bkup=$rowlocationbkup['customer_code'];
	$branch_code_bkup=$rowlocationbkup['branch_code'];
	$destination_code_bkup=$rowlocationbkup['destination_code'];
	$vertical_value_bkup=$rowlocationbkup['vertical_value'];
	$d_instruction_bkup=$rowlocationbkup['d_instruction'];
	$sale_type_bkup=$rowlocationbkup['sale_type'];
	$order_type_bkup=$rowlocationbkup['order_type'];
	$order_value_bkup=$rowlocationbkup['order_value'];
	$TD_bkup=$rowlocationbkup['TD'];
	$tag_distributor_code_bkup=$rowlocationbkup['tag_distributor_code'];
	$transaction_type_bkup=$rowlocationbkup['transaction_type'];
	$VAT_bkup=$rowlocationbkup['VAT'];
	$freight_component_bkup=$rowlocationbkup['freight_component'];
	$transferred_bkup=$rowlocationbkup['transferred'];
	
	$sqllocationchk="SELECT * from order_header WHERE order_no='".$order_no_bkup."'";
	$rslocationchk=mysqli_query($link,$sqllocationchk);
	$countlocationchk=mysqli_num_rows($rslocationchk);

	if($countlocationchk==0)
	{
		$sql  = "insert into order_header ";
		$sql .= " SET order_no='".$order_no_bkup."'";
		$sql .= " , customer_code='".$customer_code_bkup."'";
		$sql .= " , branch_code='".$branch_code_bkup."'";
		$sql .= " , destination_code='".$destination_code_bkup."'";
		$sql .= " , vertical_value='".$vertical_value_bkup."'";
		$sql .= " , d_instruction='".$d_instruction_bkup."'";
		$sql .= " , sale_type='".$sale_type_bkup."'";
		$sql .= " , order_type='".$order_type_bkup."'";
		$sql .= " , order_value='".$order_value_bkup."'";
		$sql .= " , TD='".$TD_bkup."'";
		$sql .= " , tag_distributor_code='".$tag_distributor_code_bkup."'";
		$sql .= " , transaction_type='".$transaction_type_bkup."'";
		$sql .= " , VAT='".$VAT_bkup."'";
		$sql .= " , freight_component='".$freight_component_bkup."'";
		$sql .= " , transferred='".$transferred_bkup."'";
		mysqli_query($link,$sql);
	}
}
	
?>