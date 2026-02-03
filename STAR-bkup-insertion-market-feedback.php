<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	define("SERVERREMOTE","52.66.101.239");
	define("USERREMOTE","root");
	define("PASSWORDREMOTE","cmcl@123");
	
	//$db_namearray=array('RUPA','PARLE','ABDOS');
	$db_namearray=array('STAR');
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
		if($dbval=='STAR')
		{
			$date_condition="WHERE date_time >'2017-01-04'";
			$date_condition_one="WHERE DATE_FORMAT(SUBSTRING(market_feedback_id	,-14,14),'%Y-%m-%d %H:%i:%s') >'2017-01-04 23:59:59'";
			$date_condition_two="WHERE DATE_FORMAT(SUBSTRING(mf_stk_audit_id,-14,14),'%Y-%m-%d %H:%i:%s') >'2017-01-04 23:59:59'";
		}
		$sqllocationbkupone="SELECT * FROM competitor_pricing ".$date_condition;
		$rslocationbkupone=mysqli_query($link,$sqllocationbkupone,$linkremote);
		$countlocationbkupone=mysqli_num_rows($rslocationbkupone);
		while($rowlocationbkupone=mysqli_fetch_assoc($rslocationbkupone))
		{
			$emp_code_bkup_one=$rowlocationbkupone['emp_code'];
			$customer_code_bkup_one=$rowlocationbkupone['customer_code'];
			$star_PTD_bkup_one=$rowlocationbkupone['star_PTD'];
			$star_PTR_bkup_one=$rowlocationbkupone['star_PTR'];
			$star_PTC_bkup_one=$rowlocationbkupone['star_PTC'];
			$star_PV_bkup_one=$rowlocationbkupone['star_PV'];
			$ambuja_PTD_bkup_one=$rowlocationbkupone['ambuja_PTD'];
			$ambuja_PTR_bkup_one=$rowlocationbkupone['ambuja_PTR'];
			$ambuja_PTC_bkup_one=$rowlocationbkupone['ambuja_PTC'];
			$ambuja_PV_bkup_one=$rowlocationbkupone['ambuja_PV'];
			$ultratech_PTD_bkup_one=$rowlocationbkupone['ultratech_PTD'];
			$ultratech_PTR_bkup_one=$rowlocationbkupone['ultratech_PTR'];
			$ultratech_PTC_bkup_one=$rowlocationbkupone['ultratech_PTC'];
			$ultratech_PV_bkup_one=$rowlocationbkupone['ultratech_PV'];
			$lafarge_PTD_bkup_one=$rowlocationbkupone['lafarge_PTD'];
			$lafarge_PTR_bkup_one=$rowlocationbkupone['lafarge_PTR'];
			$lafarge_PTC_bkup_one=$rowlocationbkupone['lafarge_PTC'];
			$lafarge_PV_bkup_one=$rowlocationbkupone['lafarge_PV'];
			$dalmia_PTD_bkup_one=$rowlocationbkupone['dalmia_PTD'];
			$dalmia_PTR_bkup_one=$rowlocationbkupone['dalmia_PTR'];
			$dalmia_PTC_bkup_one=$rowlocationbkupone['dalmia_PTC'];
			$dalmia_PV_bkup_one=$rowlocationbkupone['dalmia_PV'];
			$topcem_PTD_bkup_one=$rowlocationbkupone['topcem_PTD'];
			$topcem_PTR_bkup_one=$rowlocationbkupone['topcem_PTR'];
			$topcem_PTC_bkup_one=$rowlocationbkupone['topcem_PTC'];
			$topcem_PV_bkup_one=$rowlocationbkupone['topcem_PV'];
			$acc_PTD_bkup_one=$rowlocationbkupone['acc_PTD'];
			$acc_PTR_bkup_one=$rowlocationbkupone['acc_PTR'];
			$acc_PTC_bkup_one=$rowlocationbkupone['acc_PTC'];
			$acc_PV_bkup_one=$rowlocationbkupone['acc_PV'];
			$birla_gold_PTD_bkup_one=$rowlocationbkupone['birla_gold_PTD'];
			$birla_gold_PTR_bkup_one=$rowlocationbkupone['birla_gold_PTR'];
			$birla_gold_PTC_bkup_one=$rowlocationbkupone['birla_gold_PTC'];
			$birla_gold_PV_bkup_one=$rowlocationbkupone['birla_gold_PV'];
			$date_time_bkup_one=$rowlocationbkupone['date_time'];
				
				$sql  = "Insert into competitor_pricing ";
				$sql .= " SET emp_code='".$emp_code_bkup_one."'";
				$sql .= " , customer_code='".$customer_code_bkup_one."'";
				$sql .= " , star_PTD='".$star_PTD_bkup_one."'";
				$sql .= " , star_PTR='".$star_PTR_bkup_one."'";
				$sql .= " , star_PTC='".$star_PTC_bkup_one."'";
				$sql .= " , star_PV='".$star_PV_bkup_one."'";
				$sql .= " , ambuja_PTD='".$ambuja_PTD_bkup_one."'";
				$sql .= " , ambuja_PTR='".$ambuja_PTR_bkup_one."'";
				$sql .= " , ambuja_PTC='".$ambuja_PTC_bkup_one."'";
				$sql .= " , ambuja_PV='".$ambuja_PV_bkup_one."'";
				$sql .= " , ultratech_PTD='".$ultratech_PTD_bkup_one."'";
				$sql .= " , ultratech_PTR='".$ultratech_PTR_bkup_one."'";
				$sql .= " , ultratech_PTC	='".$ultratech_PTC_bkup_one."'";
				$sql .= " , ultratech_PV='".$ultratech_PV_bkup_one."'";
				$sql .= " , lafarge_PTD='".$lafarge_PTD_bkup_one."'";
				$sql .= " , lafarge_PTR='".$lafarge_PTR_bkup_one."'";
				$sql .= " , lafarge_PTC='".$lafarge_PTC_bkup_one."'";
				$sql .= " , lafarge_PV='".$lafarge_PV_bkup_one."'";
				$sql .= " , dalmia_PTD='".$dalmia_PTD_bkup_one."'";
				$sql .= " , dalmia_PTR='".$dalmia_PTR_bkup_one."'";
				$sql .= " , dalmia_PTC='".$dalmia_PTC_bkup_one."'";
				$sql .= " , dalmia_PV='".$dalmia_PV_bkup_one."'";
				$sql .= " , topcem_PTD='".$topcem_PTD_bkup_one."'";
				$sql .= " , topcem_PTR='".$topcem_PTR_bkup_one."'";
				$sql .= " , topcem_PTC='".$topcem_PTC_bkup_one."'";
				$sql .= " , topcem_PV='".$topcem_PV_bkup_one."'";
				$sql .= " , acc_PTD='".$acc_PTD_bkup_one."'";
				$sql .= " , acc_PTC='".$acc_PTC_bkup_one."'";
				$sql .= " , acc_PTR='".$acc_PTR_bkup_one."'";
				$sql .= " , acc_PV='".$acc_PV_bkup_one."'";
				$sql .= " , birla_gold_PTD	='".$birla_gold_PTD_bkup_one."'";
				$sql .= " , birla_gold_PTR	='".$birla_gold_PTR_bkup_one."'";
				$sql .= " , birla_gold_PTC	='".$birla_gold_PTC_bkup_one."'";
				$sql .= " , birla_gold_PV	='".$birla_gold_PV_bkup_one."'";
				$sql .= " , date_time	='".$date_time_bkup_one."'";
				
				mysqli_query($link,$sql,$link);
		}
		
		$sqllocationbkuptwo="SELECT * FROM competitor_stock ".$date_condition;
		$rslocationbkuptwo=mysqli_query($link,$sqllocationbkuptwo,$linkremote);
		$countlocationbkuptwo=mysqli_num_rows($rslocationbkuptwo);
		while($rowlocationbkuptwo=mysqli_fetch_assoc($rslocationbkuptwo))
		{
			$emp_code_bkup=$rowlocationbkuptwo['emp_code'];
			$customer_code_bkup=$rowlocationbkuptwo['customer_code'];
			$star_bkup=$rowlocationbkuptwo['star'];
			$ambuja_bkup=$rowlocationbkuptwo['ambuja'];
			$ultratech_bkup=$rowlocationbkuptwo['ultratech'];
			$lafarge_bkup=$rowlocationbkuptwo['lafarge'];
			$dalmia_bkup=$rowlocationbkuptwo['dalmia'];
			$topcem_bkup=$rowlocationbkuptwo['topcem'];
			$acc_bkup=$rowlocationbkuptwo['acc'];
			$birla_gold_bkup=$rowlocationbkuptwo['birla_gold'];
			$date_time_bkup=$rowlocationbkuptwo['date_time'];
		
			$sql  = "insert into competitor_stock ";
			$sql .= " SET emp_code='".$emp_code_bkup."'";
			$sql .= " , 	customer_code='".$customer_code_bkup."'";
			$sql .= " , star='".$star_bkup."'";
			$sql .= " , ambuja='".$ambuja_bkup."'";
			$sql .= " , ultratech	='".$ultratech_bkup."'";
			$sql .= " , lafarge	='".$lafarge_bkup."'";
			$sql .= " , dalmia	='".$dalmia_bkup."'";
			$sql .= " , topcem	='".$topcem_bkup."'";
			$sql .= " , acc	='".$acc_bkup."'";
			$sql .= " , birla_gold	='".$birla_gold_bkup."'";
			$sql .= " , date_time	='".$date_time_bkup."'";
			mysqli_query($link,$sql,$link);
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
				mysqli_query($link,$sql,$link);
			}
		}
		
		$sqllocationbkup="SELECT * FROM mf_stk_audit_header ".$date_condition_two;
		$rslocationbkup=mysqli_query($link,$sqllocationbkup,$linkremote);
		$countlocationbkup=mysqli_num_rows($rslocationbkup);
		while($rowlocationbkup=mysqli_fetch_assoc($rslocationbkup))
		{
			$mf_stk_audit_id_bkup=$rowlocationbkup['mf_stk_audit_id'];
			$customer_code_bkup=$rowlocationbkup['customer_code'];
			$image_bkup=$rowlocationbkup['image'];
			$remarks_bkup=$rowlocationbkup['remarks'];
			
			$sqllocationchk="SELECT * from mf_stk_audit_header WHERE mf_stk_audit_id='".$mf_stk_audit_id_bkup."' AND 	
							customer_code='".$customer_code_bkup."' AND image='".$image_bkup."' AND remarks='".$remarks_bkup."'";
			$rslocationchk=mysqli_query($link,$sqllocationchk,$link);
			$countlocationchk=mysqli_num_rows($rslocationchk);
			if($countlocationchk==0)
			{
				$sql  = "insert into mf_stk_audit_header ";
				$sql .= " SET mf_stk_audit_id='".$mf_stk_audit_id_bkup."'";
				$sql .= " , customer_code='".$customer_code_bkup."'";
				$sql .= " , image='".$image_bkup."'";
				$sql .= " , remarks='".$remarks_bkup."'";
				mysqli_query($link,$sql,$link);
			}
		}
		
		$sqlcustomerbkup="SELECT * FROM mf_stk_audit_details ".$date_condition_two;
		$rscustomerbkup=mysqli_query($link,$sqlcustomerbkup,$linkremote);
		$countcustomerbkup=mysqli_num_rows($rscustomerbkup);
		while($rowcustomerbkup=mysqli_fetch_assoc($rscustomerbkup))
		{
			$mf_stk_audit_id_bkup=$rowcustomerbkup['mf_stk_audit_id'];
			$competitor_name_bkup=$rowcustomerbkup['competitor_name'];
			$qty_mt_bkup=$rowcustomerbkup['qty_mt'];
			$scheme_discount_bkup=$rowcustomerbkup['scheme_discount'];
			
			$sqlcustomerchk="SELECT * from mf_stk_audit_details WHERE mf_stk_audit_id='".$mf_stk_audit_id_bkup."' AND 	
							competitor_name='".$competitor_name_bkup."' AND qty_mt='".$qty_mt_bkup."' AND scheme_discount='".$scheme_discount_bkup."'";
			$rslcustomerchk=mysqli_query($link,$sqlcustomerchk,$link);
			$countcustomerchk=mysqli_num_rows($rslcustomerchk);
			if($countcustomerchk==0)
			{
				$sql  = "insert into mf_stk_audit_details ";
				$sql .= " SET mf_stk_audit_id='".$mf_stk_audit_id_bkup."'";
				$sql .= " , competitor_name='".$competitor_name_bkup."'";
				$sql .= " , qty_mt='".addslashes($qty_mt_bkup)."'";
				$sql .= " , scheme_discount='".addslashes($scheme_discount_bkup)."'";
		
				mysqli_query($link,$sql,$link);
			}
		}
		mysqli_close($link);
		mysqli_close($linkremote);
	}
?>