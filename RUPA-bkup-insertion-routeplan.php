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
		if($dbval=='EMAMI')
		{
			//$date_condition="WHERE DATE_FORMAT(SUBSTRING(route_plan_trans_id,-14,14),'%Y-%m-%d %H:%i:%s') >'2017-01-04 23:59:59'";
			$date_condition_one="AND DATE_FORMAT(SUBSTRING(customer_code,-14,14),'%Y-%m-%d %H:%i:%s') >'2017-01-19 23:59:59'";
		}
		if($dbval=='UCLINDIA')
		{
			//$date_condition="WHERE DATE_FORMAT(SUBSTRING(route_plan_trans_id,-14,14),'%Y-%m-%d %H:%i:%s') >'2017-01-04 23:59:59'";
			$date_condition_one="AND DATE_FORMAT(SUBSTRING(customer_code,-14,14),'%Y-%m-%d %H:%i:%s') >'2017-02-02 23:59:59'";
		}
		/*$sqllocationbkup="SELECT * FROM route_plan ".$date_condition;
		$rslocationbkup=mysqli_query($link,$sqllocationbkup,$linkremote);
		$countlocationbkup=mysqli_num_rows($rslocationbkup);
		while($rowlocationbkup=mysqli_fetch_assoc($rslocationbkup))
		{
			$route_plan_trans_id_bkup=$rowlocationbkup['route_plan_trans_id'];
			$emp_code_bkup=$rowlocationbkup['emp_code'];
			$route_code_bkup=$rowlocationbkup['route_code'];
			$visit_date_bkup=$rowlocationbkup['visit_date'];
			$remarks_bkup=$rowlocationbkup['remarks'];
			$distributor_code_bkup=$rowlocationbkup['distributor_code'];
			$status_bkup=$rowlocationbkup['status'];
			$create_date_bkup=$rowlocationbkup['create_date'];
			$update_date_bkup=$rowlocationbkup['update_date'];
			
			$sqllocationchk="SELECT * from route_plan WHERE emp_code='".$emp_code_bkup."' AND 	route_code='".$route_code_bkup."' 
							AND visit_date='".$visit_date_bkup."' AND status='".$status_bkup."' AND create_date='".$create_date_bkup."'";
			$rslocationchk=mysqli_query($link,$sqllocationchk,$link);
			$countlocationchk=mysqli_num_rows($rslocationchk);
			if($countlocationchk==0)
			{
				$sql  = "insert into route_plan ";
				$sql .= " SET route_plan_trans_id='".$route_plan_trans_id_bkup."'";
				$sql .= " , emp_code='".$emp_code_bkup."'";
				$sql .= " , route_code='".$route_code_bkup."'";
				$sql .= " , visit_date='".$visit_date_bkup."'";
				$sql .= " , remarks='".$remarks_bkup."'";
				$sql .= " , status='".$status_bkup."'";
				$sql .= " , distributor_code='".$distributor_code_bkup."'";
				$sql .= " , create_date='".$create_date_bkup."'";
				$sql .= " , update_date='".$update_date_bkup."'";
				mysqli_query($link,$sql,$link);
			}
		}*/
		
		$sqlcustomerbkup="SELECT * FROM customer_master WHERE customer_code LIKE 'N%' ".$date_condition_one;
		$rscustomerbkup=mysqli_query($link,$sqlcustomerbkup,$linkremote);
		$countcustomerbkup=mysqli_num_rows($rscustomerbkup);
		while($rowcustomerbkup=mysqli_fetch_assoc($rscustomerbkup))
		{
			$customer_code_bkup=$rowcustomerbkup['customer_code'];
			$dns_customer_code_bkup=$rowcustomerbkup['dns_customer_code'];
			$customer_name_bkup=$rowcustomerbkup['customer_name'];
			$address_bkup=$rowcustomerbkup['address'];
			$pin_bkup=$rowcustomerbkup['pin'];
			$phone_no_bkup=$rowcustomerbkup['phone_no'];
			$landline_no_bkup=$rowcustomerbkup['landline_no'];
			$route_code_bkup=$rowcustomerbkup['route_code'];
			$emp_code_bkup=$rowcustomerbkup['emp_code'];
			$current_balance_bkup=$rowcustomerbkup['current_balance'];
			$acedns_bkup=$rowcustomerbkup['acedns'];
			$black_list_bkup=$rowcustomerbkup['black_list'];
			$cust_type_bkup=$rowcustomerbkup['cust_type'];
			$rds_tag_bkup=$rowcustomerbkup['rds_tag'];
			$district_bkup=$rowcustomerbkup['district'];
			$download_time_bkup=$rowcustomerbkup['download_time'];
			$download_time_credit_limit_bkup=$rowcustomerbkup['download_time_credit_limit'];
			$branch_code_bkup=$rowcustomerbkup['branch_code'];
			$transferred_bkup=$rowcustomerbkup['transferred'];
			
			$sqlcustomerchk="SELECT * from customer_master WHERE customer_code='".$customer_code_bkup."'";
			$rslcustomerchk=mysqli_query($link,$sqlcustomerchk,$link);
			$countcustomerchk=mysqli_num_rows($rslcustomerchk);
			if($countcustomerchk==0)
			{
				$sql  = "insert into customer_master ";
				$sql .= " SET customer_code='".$customer_code_bkup."'";
				$sql .= " , dns_customer_code='".$dns_customer_code_bkup."'";
				$sql .= " , customer_name='".addslashes($customer_name_bkup)."'";
				$sql .= " , branch_code='".addslashes($branch_code_bkup)."'";
				$sql .= " , phone_no='".$phone_no_bkup."'";
				$sql .= " , pin='".$pin_bkup."'";
				$sql .= " , route_code='".$route_code_bkup."'";
				$sql .= " , current_balance	='".$current_balance_bkup."'";
				$sql .= " , acedns='Y'";
				$sql .= " , black_list='N'";
				$sql .= " , rds_tag='".$rds_tag_bkup."'";
				$sql .= " , cust_type='".$cust_type_bkup."'";
				$sql .= " , address='".$address_bkup."'";
				$sql .= " , district='".$district_bkup."'";
				$sql .= " , download_time='".$download_time_bkup."'";
				$sql .= " , download_time_credit_limit='".$download_time_credit_limit_bkup."'";
				$sql .= " , transferred='".$transferred_bkup."'";
		
				mysqli_query($link,$sql,$link);
			}
		}
		mysqli_close($link);
		mysqli_close($linkremote);
	}
?>