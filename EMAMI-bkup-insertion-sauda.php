<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	define("SERVERREMOTE","103.241.144.155");
	define("USERREMOTE","acedns_dnsprod");
	define("PASSWORDREMOTE","dnsprod1234");
	
	//$db_namearray=array('RUPA','PARLE','ABDOS');
	$db_namearray=array('EMAMI');

	foreach($db_namearray as $dbval)
	{
		$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
		$linkremote=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE) or die("Database Connection Error remote.");

		//define("DB","acedns_$dbval");	
		//define("DBREMOTE","acedns_$dbval");

		mysqli_select_db("acedns_$dbval",$link) or die("could not connect the database for invalid nick name");
		mysqli_select_db("acedns_$dbval",$linkremote) or die("could not connect the database for invalid nick name remote");

		if($dbval=='EMAMI')
		{
			$date_condition="WHERE DATE_FORMAT(SUBSTRING(sauda_no,-14,14),'%Y-%m-%d %H:%i:%s') >'2017-01-19 23:59:59'";
			$date_condition_one="WHERE allocation_date >'2017-01-19 23:59:59'";
		}

		$sqllocationbkup="SELECT * FROM sauda_header ".$date_condition;
		$rslocationbkup=mysqli_query($link,$sqllocationbkup,$linkremote);
		$countlocationbkup=mysqli_num_rows($rslocationbkup);
		while($rowlocationbkup=mysqli_fetch_assoc($rslocationbkup))
		{
			$sauda_no_bkup=$rowlocationbkup['sauda_no'];
			$customer_code_bkup=$rowlocationbkup['customer_code'];
			$branch_code_bkup=$rowlocationbkup['branch_code'];
			$d_instruction_bkup=$rowlocationbkup['d_instruction'];
			$sauda_value_bkup=$rowlocationbkup['sauda_value'];
			$TD_bkup=$rowlocationbkup['TD'];
			$broker_id_bkup=$rowlocationbkup['broker_id'];
			$transaction_type_bkup=$rowlocationbkup['transaction_type'];
			$VAT_bkup=$rowlocationbkup['VAT'];
			$sauda_valid_from_bkup=$rowlocationbkup['sauda_valid_from'];
			$dwnld_transferred_bkup=$rowlocationbkup['dwnld_transferred'];
			
			$sqllocationchk="SELECT * from sauda_header WHERE sauda_no='".$sauda_no_bkup."'";
			$rslocationchk=mysqli_query($link,$sqllocationchk,$link);
			$countlocationchk=mysqli_num_rows($rslocationchk);
		
			if($countlocationchk==0)
			{
				$sql  = "insert into sauda_header ";
				$sql .= " SET sauda_no='".$sauda_no_bkup."'";
				$sql .= " , customer_code='".$customer_code_bkup."'";
				$sql .= " , branch_code='".$branch_code_bkup."'";
				$sql .= " , d_instruction='".$d_instruction_bkup."'";
				$sql .= " , sauda_value='".$sauda_value_bkup."'";
				$sql .= " , TD='".$TD_bkup."'";
				$sql .= " , broker_id='".$broker_id_bkup."'";
				$sql .= " , transaction_type='".$transaction_type_bkup."'";
				$sql .= " , VAT='".$VAT_bkup."'";
				$sql .= " , sauda_valid_from='".$sauda_valid_from_bkup."'";
				$sql .= " , dwnld_transferred='".$dwnld_transferred_bkup."'";
				mysqli_query($link,$sql,$link);
			}
		}
		
		$sqllocationbkupdetails="SELECT * FROM sauda_details ".$date_condition;
		$rslocationbkupdetails=mysqli_query($link,$sqllocationbkupdetails,$linkremote);
		$countlocationbkupdetails=mysqli_num_rows($rslocationbkupdetails);
		while($rowlocationbkupdetails=mysqli_fetch_assoc($rslocationbkupdetails))
		{
			$sauda_no_bkup=$rowlocationbkupdetails['sauda_no'];
			$sku_code_bkup=$rowlocationbkupdetails['sku_code'];
			$qty_bkup=$rowlocationbkupdetails['qty'];
			$convert_qty_one_bkup=$rowlocationbkupdetails['convert_qty_one'];
			$convert_qty_two_bkup=$rowlocationbkupdetails['convert_qty_two'];
			$mrp_code_bkup=$rowlocationbkupdetails['mrp_code'];
			$TD_bkup=$rowlocationbkupdetails['TD'];
			$premium_bkup=$rowlocationbkupdetails['premium'];
			$VAT_bkup=$rowlocationbkupdetails['VAT'];
			$sale_rate_bkup=$rowlocationbkupdetails['sale_rate'];
			$freight_charge_bkup=$rowlocationbkupdetails['freight_charge'];
			$amount_bkup=$rowlocationbkupdetails['amount'];
			//$billed_qty_bkup=$rowlocationbkup['billed_qty'];
			
			$sqllocationchkdetails="SELECT * from sauda_details WHERE sauda_no='".$sauda_no_bkup."' AND sku_code='".$sku_code_bkup."'";
			$rslocationchkdetails=mysqli_query($link,$sqllocationchkdetails,$link);
			$countlocationchkdetails=mysqli_num_rows($rslocationchkdetails);
		
			if($countlocationchkdetails==0)
			{
				$sql  = "insert into sauda_details ";
				$sql .= " SET sauda_no='".$sauda_no_bkup."'";
				$sql .= " , sku_code='".$sku_code_bkup."'";
				$sql .= " , convert_qty_one='".$convert_qty_one_bkup."'";
				$sql .= " , convert_qty_two='".$convert_qty_two_bkup."'";
				$sql .= " , qty='".$qty_bkup."'";
				$sql .= " , mrp_code='".$mrp_code_bkup."'";
				$sql .= " , TD='".$TD_bkup."'";
				$sql .= " , premium='".$premium_bkup."'";
				$sql .= " , VAT='".$VAT_bkup."'";
				$sql .= " , sale_rate='".$sale_rate_bkup."'";
				$sql .= " , freight_charge='".$freight_charge_bkup."'";
				$sql .= " , amount='".$amount_bkup."'";
				mysqli_query($link,$sql,$link);
			}
		}
		
		$sqllocationbkupallocation="SELECT * FROM sauda_allocation_log ".$date_condition_one;
		$rslocationbkupallocation=mysqli_query($link,$sqllocationbkupallocation,$linkremote);
		$countlocationbkupallocation=mysqli_num_rows($rslocationbkupallocation);
		while($rowlocationbkupallocation=mysqli_fetch_assoc($rslocationbkupallocation))
		{
			$allocation_id_bkup=$rowlocationbkupallocation['allocation_id'];
			$allocation_date_bkup=$rowlocationbkupallocation['allocation_date'];
			$emp_code_bkup=$rowlocationbkupallocation['emp_code'];
			$product_filter_code_bkup=$rowlocationbkupallocation['product_filter_code'];
			$qty_bkup=$rowlocationbkupallocation['qty'];
			
			$sqllocationallocation="SELECT * from sauda_allocation_log WHERE allocation_id='".$allocation_id_bkup."' AND 
									allocation_date='".$allocation_date_bkup."' AND emp_code='".$emp_code_bkup."' AND 
									product_filter_code='".$product_filter_code_bkup."' AND qty='".$qty_bkup."'";
			$rslocationallocation=mysqli_query($link,$sqllocationallocation,$link);
			$countlocationallocation=mysqli_num_rows($rslocationallocation);
		
			if($countlocationallocation==0)
			{
				$sql  = "insert into sauda_allocation_log ";
				$sql .= " SET allocation_id='".$allocation_id_bkup."'";
				$sql .= " , allocation_date='".$allocation_date_bkup."'";
				$sql .= " , emp_code='".$emp_code_bkup."'";
				$sql .= " , product_filter_code='".$product_filter_code_bkup."'";
				$sql .= " , qty='".$qty_bkup."'";
				mysqli_query($link,$sql,$link);
			}
		}
		
		
		$sqltruncate="truncate sauda_allocation";
		mysqli_query($link,$sqltruncate,$link);
		$sqlsauda="SELECT * FROM sauda_allocation";
		$rssauda=mysqli_query($link,$sqlsauda,$linkremote);
		$countsauda=mysqli_num_rows($rssauda);
		while($rowsauda=mysqli_fetch_assoc($rssauda))
		{
			$emp_code_bkup=$rowsauda['emp_code'];
			$product_filter_code_bkup=$rowsauda['product_filter_code'];
			$qty_bkup=$rowsauda['qty'];
			$BAL_bkup=$rowsauda['BAL'];
			$allot_qty_bkup=$rowsauda['allot_qty'];
			
			$sqlsaudachk="SELECT * from sauda_allocation WHERE emp_code='".$emp_code_bkup."' AND 
						product_filter_code='".$product_filter_code_bkup."' AND qty='".$qty_bkup."' AND 
						BAL='".$BAL_bkup."' AND allot_qty='".$allot_qty_bkup."'";
			$rssaudachk=mysqli_query($link,$sqlsaudachk,$link);
			$countsaudachk=mysqli_num_rows($rssaudachk);
		
			if($countsaudachk==0)
			{
				$sql  = "insert into sauda_allocation ";
				$sql .= " SET emp_code='".$emp_code_bkup."'";
				$sql .= " , product_filter_code='".$product_filter_code_bkup."'";
				$sql .= " , qty='".$qty_bkup."'";
				$sql .= " , BAL='".$BAL_bkup."'";
				$sql .= " , allot_qty='".$allot_qty_bkup."'";
				mysqli_query($link,$sql,$link);
			}
		}


	mysqli_close($link);
	mysqli_close($linkremote);
	}
?>