<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	define("SERVERREMOTE","103.241.144.155");
	define("USERREMOTE","acedns_dnsprod");
	define("PASSWORDREMOTE","dnsprod1234");
	
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	$linkremote=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE) or die("Database Connection Error remote.");
	define("DB","acedns_PARLE");	
	define("DBREMOTE","acedns_PARLE");

	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	mysqli_select_db(DBREMOTE,$linkremote) or die("could not connect the database for invalid nick name remote");
		
		$sqllocationbkup="SELECT * FROM distributor_route_relation WHERE download_time >'2016-12-20 23:59:59'";
		$rslocationbkup=mysqli_query($link,$sqllocationbkup,$linkremote);
		$countlocationbkup=mysqli_num_rows($rslocationbkup);
		while($rowlocationbkup=mysqli_fetch_assoc($rslocationbkup))
		{
			$distributor_code_bkup=$rowlocationbkup['distributor_code'];
			$route_code_bkup=$rowlocationbkup['route_code'];
			$emp_code_bkup=$rowlocationbkup['emp_code'];
			$status_bkup=$rowlocationbkup['status'];
			$download_time_bkup=$rowlocationbkup['download_time'];
			
			$sqllocationchk="SELECT * from distributor_route_relation WHERE emp_code='".$emp_code_bkup."' AND 	route_code='".$route_code_bkup."' 
							AND distributor_code='".$distributor_code_bkup."'";
			$rslocationchk=mysqli_query($link,$sqllocationchk,$link);
			$countlocationchk=mysqli_num_rows($rslocationchk);
			if($countlocationchk==0)
			{
				$sql  = "insert into distributor_route_relation ";
				$sql .= " SET distributor_code='".$distributor_code_bkup."'";
				$sql .= " , emp_code='".$emp_code_bkup."'";
				$sql .= " , route_code='".$route_code_bkup."'";
				$sql .= " , status='".$status_bkup."'";
				$sql .= " , download_time='".$download_time_bkup."'";
				mysqli_query($link,$sql,$link);
			}
		}
		
		$sqlcustomerbkup="SELECT * FROM wholesaler_details WHERE DATE_FORMAT(SUBSTRING(wholesale_trans_id,-14,14),'%Y-%m-%d %H:%i:%s') 
						>'2016-12-20 23:59:59'";
		$rscustomerbkup=mysqli_query($link,$sqlcustomerbkup,$linkremote);
		$countcustomerbkup=mysqli_num_rows($rscustomerbkup);
		while($rowcustomerbkup=mysqli_fetch_assoc($rscustomerbkup))
		{
			$wholesale_trans_id_bkup=$rowcustomerbkup['wholesale_trans_id'];
			$customer_code_bkup=$rowcustomerbkup['customer_code'];
			$debit_not_collected_bkup=$rowcustomerbkup['debit_not_collected'];
			$last_debit_note_received_bkup=$rowcustomerbkup['last_debit_note_received'];
			$closing_stock_value_bkup=$rowcustomerbkup['closing_stock_value'];
			$log_book_bkup=$rowcustomerbkup['log_book'];
			$download_time_bkup=$rowcustomerbkup['download_time'];
			
			$sqlcustomerchk="SELECT * from wholesaler_details WHERE wholesale_trans_id='".$wholesale_trans_id_bkup."'";
			$rslcustomerchk=mysqli_query($link,$sqlcustomerchk,$link);
			$countcustomerchk=mysqli_num_rows($rslcustomerchk);
			if($countcustomerchk==0)
			{
				$sql  = "insert into wholesaler_details ";
				$sql .= " SET wholesale_trans_id='".$wholesale_trans_id_bkup."'";
				$sql .= " , customer_code='".$customer_code_bkup."'";
				$sql .= " , debit_not_collected='".addslashes($debit_not_collected_bkup)."'";
				$sql .= " , last_debit_note_received='".addslashes($last_debit_note_received_bkup)."'";
				$sql .= " , closing_stock_value='".$closing_stock_value_bkup."'";
				$sql .= " , log_book='".$log_book_bkup."'";
				$sql .= " , download_time='".$download_time_bkup."'";
		
				mysqli_query($link,$sql,$link);
			}
		}
	mysqli_close($link);
	mysqli_close($linkremote);
?>