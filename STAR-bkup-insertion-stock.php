<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	define("SERVERREMOTE","103.241.144.155");
	define("USERREMOTE","acedns_dnsprod");
	define("PASSWORDREMOTE","dnsprod1234");
	
	//$db_namearray=array('RUPA','PARLE','ABDOS');
	$db_namearray=array('UCLINDIA');
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
			$date_condition="WHERE DATE_FORMAT(SUBSTRING(route_plan_trans_id,-14,14),'%Y-%m-%d %H:%i:%s') >'2016-12-17 23:59:59'";
			$date_condition_one="AND DATE_FORMAT(SUBSTRING(customer_code,-14,14),'%Y-%m-%d %H:%i:%s') >'2016-12-17 23:59:59'";
		}
		if($dbval=='PARLE')
		{
			$date_condition="WHERE DATE_FORMAT(SUBSTRING(route_plan_trans_id,-14,14),'%Y-%m-%d %H:%i:%s') >'2016-12-20 23:59:59'";
			$date_condition_one="AND DATE_FORMAT(SUBSTRING(customer_code,-14,14),'%Y-%m-%d %H:%i:%s') >'2016-12-20 23:59:59'";
		}
		if($dbval=='ABDOS')
		{
			$date_condition="WHERE DATE_FORMAT(SUBSTRING(route_plan_trans_id,-14,14),'%Y-%m-%d %H:%i:%s') >'2016-12-22 23:59:59'";
			$date_condition_one="AND DATE_FORMAT(SUBSTRING(customer_code,-14,14),'%Y-%m-%d %H:%i:%s') >'2016-12-22 23:59:59'";
		}*/
		if($dbval=='UCLINDIA')
		{
			$date_condition="WHERE DATE_FORMAT(SUBSTRING(transaction_id,-14,14),'%Y-%m-%d %H:%i:%s') >'2017-02-02 23:59:59'";
			//$date_condition_one="WHERE DATE_FORMAT(SUBSTRING(survey_id,-14,14),'%Y-%m-%d %H:%i:%s') >'2017-01-04 23:59:59'";
		}
		$sqllocationbkupone="SELECT * FROM stock_audit ".$date_condition;
		$rslocationbkupone=mysqli_query($link,$sqllocationbkupone,$linkremote);
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
			$transferred_bkup_one=$rowlocationbkupone['transferred'];
			$sqllocationchkone="SELECT * from stock_audit WHERE transaction_id='".$transaction_id_bkup_one."' AND 	product_code='".$product_code_bkup_one."'";
			$rslocationchkone=mysqli_query($link,$sqllocationchkone,$link);
			$countlocationchkone=mysqli_num_rows($rslocationchkone);
			if($countlocationchkone==0)
			{
				$sql  = "insert into stock_audit ";
				$sql .= " SET transaction_id='".$transaction_id_bkup_one."'";
				$sql .= " , customer_code='".$customer_code_bkup_one."'";
				$sql .= " , product_code='".$product_code_bkup_one."'";
				$sql .= " , quantity='".$quantity_bkup_one."'";
				$sql .= " , product_mrp='".$product_mrp_bkup_one."'";
				$sql .= " , product_details='".$product_details_bkup_one."'";
				$sql .= " , remarks='".$remarks_bkup_one."'";
				$sql .= " , transferred='".$transferred_bkup_one."'";
				mysqli_query($link,$sql,$link);
			}
		}
		
		/*$sqllocationbkuptwo="SELECT * FROM survey_output ".$date_condition_one;
		$rslocationbkuptwo=mysqli_query($link,$sqllocationbkuptwo,$linkremote);
		$countlocationbkuptwo=mysqli_num_rows($rslocationbkuptwo);
		while($rowlocationbkuptwo=mysqli_fetch_assoc($rslocationbkuptwo))
		{
			$survey_id_bkup=$rowlocationbkuptwo['survey_id'];
			$row_id_bkup=$rowlocationbkuptwo['row_id'];
			$action_id_bkup=$rowlocationbkuptwo['action_id'];
			$value_bkup=$rowlocationbkuptwo['value'];
			$type_bkup=$rowlocationbkuptwo['type'];
		
			$sqllocationchktwo="SELECT * from survey_output WHERE survey_id='".$survey_id_bkup."' AND 	row_id='".$row_id_bkup."'";
			$rslocationchktwo=mysqli_query($link,$sqllocationchktwo,$link);
			$countlocationchktwo=mysqli_num_rows($rslocationchktwo);
			if($countlocationchktwo==0)
			{
				$sql  = "insert into survey_output ";
				$sql .= " SET survey_id='".$survey_id_bkup."'";
				$sql .= " , 	row_id='".$row_id_bkup."'";
				$sql .= " , action_id='".$action_id_bkup."'";
				$sql .= " , value='".$value_bkup."'";
				$sql .= " , type	='".$type_bkup."'";
				mysqli_query($link,$sql,$link);
			}
		}
		
		$sqllocationbkupthree="SELECT * FROM survey_header ".$date_condition_one;
		$rslocationbkupthree=mysqli_query($link,$sqllocationbkupthree,$linkremote);
		$countlocationbkupthree=mysqli_num_rows($rslocationbkupthree);
		while($rowlocationbkupthree=mysqli_fetch_assoc($rslocationbkupthree))
		{
			$survey_id_bkup=$rowlocationbkupthree['survey_id'];
			$survey_type_bkup=$rowlocationbkupthree['survey_type'];
			$time_9_to_12_bkup=$rowlocationbkupthree['time_9_to_12'];
			$time_12_to_3_bkup=$rowlocationbkupthree['time_12_to_3'];
			$time_3_to_6_bkup=$rowlocationbkupthree['time_3_to_6'];
			$time_6_to_9_bkup=$rowlocationbkupthree['time_6_to_9'];
			$status_bkup=$rowlocationbkupthree['status'];
			$download_time_bkup=$rowlocationbkupthree['download_time'];
			$active_bkup=$rowlocationbkupthree['active'];
			$transferred_flag_bkup=$rowlocationbkupthree['transferred_flag'];
		
			$sqllocationchkthree="SELECT * from survey_header WHERE survey_id='".$survey_id_bkup."' ";
			$rslocationchkthree=mysqli_query($link,$sqllocationchkthree,$link);
			$countlocationchkthree=mysqli_num_rows($rslocationchkthree);
			if($countlocationchkthree==0)
			{
				$sql  = "insert into survey_header ";
				$sql .= " SET survey_id='".$survey_id_bkup."'";
				$sql .= " , 	survey_type='".$survey_type_bkup."'";
				$sql .= " , time_9_to_12='".$time_9_to_12_bkup."'";
				$sql .= " , time_12_to_3='".$time_12_to_3_bkup."'";
				$sql .= " , time_3_to_6	='".$time_3_to_6_bkup."'";
				$sql .= " , time_6_to_9	='".$time_6_to_9_bkup."'";
				$sql .= " , status	='".$status_bkup."'";
				$sql .= " , download_time	='".$download_time_bkup."'";
				$sql .= " , active	='".$active_bkup."'";
				$sql .= " , transferred_flag	='".$transferred_flag_bkup."'";
				mysqli_query($link,$sql,$link);
			}
		}*/

		mysqli_close($link);
		mysqli_close($linkremote);
	}
?>