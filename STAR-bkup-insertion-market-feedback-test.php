<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	define("SERVERREMOTE","52.66.101.239");
	define("USERREMOTE","root");
	define("PASSWORDREMOTE","cmcl@123");
	
	$db_namearray=array('STAR');
	foreach($db_namearray as $dbval){
		$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
		$linkremote=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE) or die("Database Connection Error remote.");
		
		mysqli_select_db("acedns_$dbval",$link) or die("could not connect the database for invalid nick name");
		mysqli_select_db("acedns_$dbval",$linkremote) or die("could not connect the database for invalid nick name remote");
		
		if($dbval=='STAR')
		{
			$date_condition="WHERE date_time >'2017-01-04'";
			$date_condition_one="WHERE DATE_FORMAT(SUBSTRING(market_feedback_id	,-14,14),'%Y-%m-%d %H:%i:%s') >'2017-01-04 23:59:59'";
			$date_condition_two="WHERE DATE_FORMAT(SUBSTRING(mf_stk_audit_id,-14,14),'%Y-%m-%d %H:%i:%s') >'2017-01-04 23:59:59'";
		}
		
		$sqllocationbkupthree="SELECT * FROM market_feedback ".$date_condition_one;
		$rslocationbkupthree=mysqli_query($link,$sqllocationbkupthree,$linkremote);
		$countlocationbkupthree=mysqli_num_rows($rslocationbkupthree);
		while($rowlocationbkupthree=mysqli_fetch_assoc($rslocationbkupthree))
		{
			$market_feedback_id_bkup=$rowlocationbkupthree['market_feedback_id	'];
			$route_code_bkup=$rowlocationbkupthree['route_code'];
			$customer_code_bkup=$rowlocationbkupthree['customer_code'];
			$product_group_bkup=$rowlocationbkupthree['product_group'];
			$competitor_name_bkup=$rowlocationbkupthree['competitor_name'];
			$PTD_bkup=$rowlocationbkupthree['PTD'];
			$PTR_bkup=$rowlocationbkupthree['PTR'];
			$PTC_bkup=$rowlocationbkupthree['PTC'];
			$PV_bkup=$rowlocationbkupthree['PV'];
		
			$sqllocationchkthree="SELECT * from market_feedback WHERE market_feedback_id='".$market_feedback_id_bkup."' 
								AND route_code='".$route_code_bkup."' AND customer_code='".$customer_code_bkup."' AND product_group='".$product_group_bkup."'
								AND competitor_name='".$competitor_name_bkup."' AND PTD='".$PTD_bkup."' AND PTR='".$PTR_bkup."' AND 
								PTC='".$PTC_bkup."' AND PV='".$PV_bkup."'";
			$rslocationchkthree=mysqli_query($link,$sqllocationchkthree,$link);
			$countlocationchkthree=mysqli_num_rows($rslocationchkthree);
			if($countlocationchkthree==0)
			{
				$sql  = "insert into market_feedback ";
				$sql .= " SET market_feedback_id='".$market_feedback_id_bkup."'";
				$sql .= " , route_code='".$route_code_bkup."'";
				$sql .= " , customer_code='".$customer_code_bkup."'";
				$sql .= " , product_group='".$product_group_bkup."'";
				$sql .= " , competitor_name	='".$competitor_name_bkup."'";
				$sql .= " , PTD	='".$PTD_bkup."'";
				$sql .= " , PTR	='".$PTR_bkup."'";
				$sql .= " , PTC	='".$PTC_bkup."'";
				$sql .= " , PV	='".$PV_bkup."'";
				
				echo $sql."<br>";
				//mysqli_query($link,$sql,$link);
			}
		}
	}
?>