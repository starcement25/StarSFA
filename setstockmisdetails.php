<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_VCONNECT");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	//require("include/config-email-setup.php");
	
	$list=array();
	$month = 02;
	$year = 2019;
	
	for($d=1; $d<=31; $d++)
	{
		$time=mktime(12, 0, 0, $month, $d, $year);          
		if (date('m', $time)==$month)       
			$list[]=date('Y-m-d', $time);
	}
	/*echo "<pre>";
	print_r($list);
	echo "</pre>";
	exit();*/	
	$date_val=date('Y-m-d', strtotime(date('Y-m-d') ." -1 day"));
	//foreach($list as $date_val)
	//{
	$sqlsaledetails="SELECT CM.customer_code,CM.customer_name,PM.prod_code,PM.prod_desc,
				 SUM(CASE WHEN SOD.stock_out_date='".$date_val."' AND SOD.IMEI!='' THEN 1 ELSE 0 END) AS stock_out_special_qty,
				 SUM(CASE WHEN SOD.stock_out_date='".$date_val."' AND SOD.IMEI='' THEN  SOD.stock_out_qty ELSE 0 END) AS stock_out_normal_qty, 
				 SUM(CASE WHEN SOD.stock_out_date < '".$date_val."' AND SOD.IMEI!='' THEN 1 ELSE 0 END) AS stock_out_special_qty_previous,
				 SUM(CASE WHEN SOD.stock_out_date < '".$date_val."' AND SOD.IMEI='' THEN  SOD.stock_out_qty ELSE 0 END) AS stock_out_normal_qty_previous,CM.rds_tag FROM 
				 stock_out_details SOD INNER JOIN customer_master CM INNER JOIN product_master PM INNER JOIN customer_route_emp_relation CRR 
				 ON SUBSTRING(SOD.stock_out_id,3,5)=CRR.emp_code AND CRR.customer_code=CM.customer_code AND SOD.prod_code=PM.prod_code 
				  GROUP BY CM.customer_code,SOD.prod_code ORDER BY CM.customer_code ASC,SOD.prod_code ASC";
	$rssaledetails=mysqli_query($link,$sqlsaledetails);
	while($rowsaledetails=mysqli_fetch_assoc($rssaledetails))
	{
		$customer_code=$rowsaledetails['customer_code'];
		$customer_name=$rowsaledetails['customer_name'];
		$rds_tag=$rowsaledetails['rds_tag'];
		$prod_code=$rowsaledetails['prod_code'];
		$prod_desc=$rowsaledetails['prod_desc'];
		$stock_out_special_qty=$rowsaledetails['stock_out_special_qty'];
		$stock_out_normal_qty=$rowsaledetails['stock_out_normal_qty'];
		$stock_out_special_qty_previous=$rowsaledetails['stock_out_special_qty_previous'];
		$stock_out_normal_qty_previous=$rowsaledetails['stock_out_normal_qty_previous'];
		$total_sales=$stock_out_special_qty+$stock_out_normal_qty;
		$total_sales_previous=$stock_out_special_qty_previous+$stock_out_normal_qty_previous;
		
		if($rds_tag=='')
		{
			$sqlselbilling="SELECT SUM(CASE WHEN CPB.invoice_date='".$date_val."' THEN 1 ELSE 0 END) AS billed_qty_current,
							SUM(CASE WHEN CPB.invoice_date < '".$date_val."' THEN 1 ELSE 0 END) AS billed_qty_previous
							FROM customer_product_billing CPB WHERE CPB.customer_code='".$customer_code."' AND CPB.prod_code='".$prod_code."'";
			$rsselbilling=mysqli_query($link,$sqlselbilling);
			$rowselbilling=mysqli_fetch_assoc($rsselbilling);
			$billed_qty_current=$rowselbilling['billed_qty_current'];
			$billed_qty_previous=$rowselbilling['billed_qty_previous'];
			$opening_stock=$billed_qty_previous-$total_sales_previous;
			if($opening_stock==''){						
			   $opening_stock=0;
			}
		}
		else
		{
			$sqlbilling="SELECT COUNT(IMEI) as consolidated_billed_qty FROM customer_product_billing  
						WHERE customer_code IN(SELECT customer_code FROM customer_master WHERE rds_tag='".$rds_tag."' AND acedns='Y') 
						AND prod_code='".$prod_code."' AND invoice_date='".$date_val."'";
			$rsbilling=mysqli_query($link,$sqlbilling);
			$rowbilling=mysqli_fetch_assoc($rsbilling);
			$billed_qty_current=$rowbilling['consolidated_billed_qty'];
			$sqlstockoutqty="SELECT SUM(CASE WHEN IMEI!='' AND stock_out_date='".$date_val."' THEN 1 ELSE 0 END) 
							AS consolidated_stock_out_qty,SUM(CASE WHEN IMEI!='' AND stock_out_date < '".$date_val."' THEN 1 ELSE 0 END) 
							AS consolidated_stock_out_qty_opening  FROM stock_out_details  
							WHERE customer_code IN(SELECT customer_code FROM customer_master WHERE rds_tag='".$rds_tag."' AND acedns='Y') 
							AND prod_code='".$prod_code."'";			
			$rsstockoutqty=mysqli_query($link,$sqlstockoutqty);
			$rowstockoutqty=mysqli_fetch_assoc($rsstockoutqty);
			$total_sales=$rowstockoutqty['consolidated_stock_out_qty'];
			$stock_out_qty_opening=$rowstockoutqty['consolidated_stock_out_qty_opening'];
			if($total_sales =='')   $total_sales=0;
			if($stock_out_qty_opening =='')   $stock_out_qty_opening=0;
			
			$sqlselopeningstockbilling="SELECT COUNT(IMEI) AS opening_stock_billing FROM customer_product_billing  WHERE 
										customer_code IN(SELECT customer_code FROM customer_master WHERE rds_tag='".$rds_tag."' AND acedns='Y') 
										AND prod_code='".$prod_code."' AND invoice_date < '".$date_val."'";
			$rsselopeningstockbilling=mysqli_query($link,$sqlselopeningstockbilling);
			$rowselopeningstockbilling=mysqli_fetch_assoc($rsselopeningstockbilling);
			$opening_stock_billing=$rowselopeningstockbilling['opening_stock_billing'];
			if($opening_stock_billing==''){						
			   $opening_stock_billing=0;
			}
			
			$opening_stock=$opening_stock_billing-$stock_out_qty_opening;
		}
		$closing_stock=$opening_stock+$billed_qty_current-$total_sales;
		if($closing_stock < 0)
		{
			$closing_stock=0;
		}
		$sqlinsertmis="INSERT INTO stock_mis_details SET customer_code='".$customer_code."',
						prod_code='".$prod_code."',	
						customer_name='".$customer_name."',
						prod_desc='".$prod_desc."',
						sale_qty='".$total_sales."',
						cl_stk_qty='".$closing_stock."',
						create_date='".$date_val."',
						download_time=CURRENT_TIMESTAMP(),active_flag='Y'";
		$rsinsertmis=mysqli_query($link,$sqlinsertmis);		
	}
	//exit();
	//}
	mysqli_close($link);
?>