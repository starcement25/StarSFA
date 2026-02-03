<?php
		define("SERVERREMOTE","52.66.101.239");
		define("USERREMOTE","root");
		define("PASSWORDREMOTE","cmcl@123");
		define("DBREMOTE","acedns_STAR");
		$link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE) or die("Database Connection Error.");
		mysqli_select_db(DBREMOTE,$link) or die("could not connect the database for invalid nick name");

$sqllocationbkup="SELECT * FROM route_plan_bkup";
$rslocationbkup=mysqli_query($link,$sqllocationbkup);
$countlocationbkup=mysqli_num_rows($rslocationbkup);
while($rowlocationbkup=mysqli_fetch_assoc($rslocationbkup))
{
	$route_plan_trans_id_bkup=$rowlocationbkup['route_plan_trans_id'];
	$emp_code_bkup=$rowlocationbkup['emp_code'];
	$route_code_bkup=$rowlocationbkup['route_code'];
	$visit_date_bkup=$rowlocationbkup['visit_date'];
	$remarks_bkup=$rowlocationbkup['remarks'];
	$distributor_code_bkup=$rowlocationbkup['distributor_code'];
	$create_date_bkup=$rowlocationbkup['create_date'];
	$update_date_bkup=$rowlocationbkup['update_date'];
	
	$sqllocationchk="SELECT * from route_plan WHERE emp_code='".$emp_code_bkup."' AND 	route_code='".$route_code_bkup."' AND visit_date='".$visit_date_bkup."'";
	$rslocationchk=mysqli_query($link,$sqllocationchk);
	$countlocationchk=mysqli_num_rows($rslocationchk);
	if($countlocationchk==0)
	{
		$sql  = "insert into route_plan ";
		$sql .= " SET route_plan_trans_id='".$route_plan_trans_id_bkup."'";
		$sql .= " , emp_code='".$emp_code_bkup."'";
		$sql .= " , route_code='".$route_code_bkup."'";
		$sql .= " , visit_date='".$visit_date_bkup."'";
		$sql .= " , remarks='".$remarks_bkup."'";
		$sql .= " , distributor_code='".$distributor_code_bkup."'";
		$sql .= " , create_date='".$create_date_bkup."'";
		$sql .= " , update_date='".$update_date_bkup."'";
		mysqli_query($link,$sql);
	}
}


$sqllocationbkupone="SELECT * FROM stock_audit_bkup";
$rslocationbkupone=mysqli_query($link,$sqllocationbkupone);
$countlocationbkupone=mysqli_num_rows($rslocationbkupone);
while($rowlocationbkupone=mysqli_fetch_assoc($rslocationbkupone))
{
	$transaction_id_bkup_one=$rowlocationbkupone['transaction_id'];
	$customer_code_bkup_one=$rowlocationbkupone['customer_code'];
	$product_code_bkup_one=$rowlocationbkupone['product_code'];
	$quantity_bkup_one=$rowlocationbkupone['quantity'];
	$product_mrp_bkup_one=$rowlocationbkupone['product_mrp'];
	$product_details_bkup_one=$rowlocationbkupone['product_details'];
	$remarks_bkup_one=$rowlocationbkupone['remarks'];
	$sqllocationchkone="SELECT * from stock_audit WHERE transaction_id='".$transaction_id_bkup_one."' AND 	product_code='".$product_code_bkup_one."'";
	$rslocationchkone=mysqli_query($link,$sqllocationchkone);
	$countlocationchkone=mysqli_num_rows($rslocationchkone);
	if($countlocationchkone==0)
	{
		$sql  = "insert into stock_audit ";
		$sql .= " SET transaction_id='".$transaction_id_bkup_one."'";
		$sql .= " , 	customer_code='".$customer_code_bkup_one."'";
		$sql .= " , product_code='".$product_code_bkup_one."'";
		$sql .= " , quantity='".$quantity_bkup_one."'";
		$sql .= " , product_mrp='".$product_mrp_bkup_one."'";
		$sql .= " , product_details='".$product_details_bkup_one."'";
		$sql .= " , remarks='".$remarks_bkup_one."'";
		mysqli_query($link,$sql);
	}
}
$sqllocationbkuptwo="SELECT * FROM survey_output_bkup";
$rslocationbkuptwo=mysqli_query($link,$sqllocationbkuptwo);
$countlocationbkuptwo=mysqli_num_rows($rslocationbkuptwo);
while($rowlocationbkuptwo=mysqli_fetch_assoc($rslocationbkuptwo))
{
	$survey_id_bkup=$rowlocationbkuptwo['survey_id'];
	$row_id_bkup=$rowlocationbkuptwo['row_id'];
	$action_id_bkup=$rowlocationbkuptwo['action_id'];
	$value_bkup=$rowlocationbkuptwo['value'];
	$type_bkup=$rowlocationbkuptwo['type'];

	$sqllocationchktwo="SELECT * from survey_output WHERE survey_id='".$survey_id_bkup."' AND 	row_id='".$row_id_bkup."'";
	$rslocationchktwo=mysqli_query($link,$sqllocationchktwo);
	$countlocationchktwo=mysqli_num_rows($rslocationchktwo);
	if($countlocationchktwo==0)
	{
		$sql  = "insert into survey_output ";
		$sql .= " SET survey_id='".$survey_id_bkup."'";
		$sql .= " , 	row_id='".$row_id_bkup."'";
		$sql .= " , action_id='".$action_id_bkup."'";
		$sql .= " , value='".$value_bkup."'";
		$sql .= " , type	='".$type_bkup."'";
		mysqli_query($link,$sql);
	}
}

?>