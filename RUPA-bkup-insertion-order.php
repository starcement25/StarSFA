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
		$sqllocationbkup="SELECT * FROM order_header ".$date_condition;
		$rslocationbkup=mysqli_query($link,$sqllocationbkup,$linkremote);
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
			$rslocationchk=mysqli_query($link,$sqllocationchk,$link);
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
				mysqli_query($link,$sql,$link);
			}
		}
	mysqli_close($link);
	mysqli_close($linkremote);
	}
?>