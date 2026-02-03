<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_ASL");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	//require("include/config-email-setup.php");

	$sqlDOdetails="SELECT PM.dns_prod_code,DT.customer_code,DT.DO_qty,DT.DO_rate,DT.DO_amount,DATE_FORMAT(SUBSTRING(DT.DO_no,-14,8),'%d-%m-%Y') AS DO_date_done ,DT.DO_no,DT.sku_code,DT.sauda_no,DT.dns_DO_no,DATE_FORMAT(SUBSTRING(DT.sauda_no,-14,14),'%Y-%m-%d %H:%i:%s') AS bargain_date,DT.destination,DT.delivery_date,DT.PO_no FROM DO_transaction DT,product_master PM WHERE DT.is_settled='no' AND DT.sku_code NOT IN(SELECT DISTINCT PM.prod_code FROM product_master PM,product_unit_coversion_matrix PUCM WHERE PM.dns_prod_code=PUCM.mapped_prod_code) AND DT.sku_code=PM.prod_code 
	AND DT.sauda_no='FTE001020200430174252' ORDER BY DATE_FORMAT(SUBSTRING(DT.sauda_no,-14,14),'%Y-%m-%d %H:%i:%s') ASC ";				
$resDOdetails = mysqli_query($link,$sqlDOdetails);
$totalDOdetails = mysqli_num_rows($resDOdetails);
	while($rowDOdetails=mysqli_fetch_assoc($resDOdetails)){
	$DO_date = $rowDOdetails['DO_date_done'];
	$customer_code = $rowDOdetails['customer_code'];
	$DO_qty_child=$rowDOdetails['DO_qty'];
	$DO_rate=$rowDOdetails['DO_rate'];
			$DO_amount=$rowDOdetails['DO_amount'];
			$DO_no=$rowDOdetails['DO_no'];
			$sauda_no=$rowDOdetails['sauda_no'];
			$sku_code=$rowDOdetails['sku_code'];
			$dns_prod_code=$rowDOdetails['dns_prod_code'];
			$dns_DO_no=$rowDOdetails['dns_DO_no'];
			$dns_DO_no=str_replace('DO//','DO/BHD/',$dns_DO_no);
			$bargain_date=$rowDOdetails['bargain_date'];
			$destination=$rowDOdetails['destination'];
			$delivery_date=$rowDOdetails['delivery_date'];
			$PO_no=$rowDOdetails['PO_no'];
			
			
			$sqlmappedsku="SELECT mapped_prod_code FROM product_unit_coversion_matrix WHERE prod_code='".$dns_prod_code."'";
			$rsmappedsku=mysqli_query($link,$sqlmappedsku);
			$rowmappedsku=mysqli_fetch_assoc($rsmappedsku);
			$mapped_sku_code=$rowmappedsku['mapped_prod_code'];
			
			$sqlsaudadetails="SELECT dns_sauda_no,qty FROM DO_master WHERE sauda_no='".$sauda_no."' AND mapped_sku_code='".$mapped_sku_code."' 
								AND qty >0";
			$rssaudadetails=mysqli_query($link,$sqlsaudadetails);
			$cntsaudadetails=mysqli_num_rows($rssaudadetails);
			if($cntsaudadetails > 0)
			{
			$rowsaudadetails=mysqli_fetch_assoc($rssaudadetails);					
			$dns_sauda_no=$rowsaudadetails['dns_sauda_no'];
			$bargain_qty=$rowsaudadetails['qty'];
			}
			else
			{
				$dns_sauda_no='';
				$bargain_qty=0;			
			}
			
			$sqlbargainqty="SELECT qty,sku_code  FROM DO_master WHERE sauda_no='".$sauda_no."' AND mapped_sku_code	='".$mapped_sku_code."'";
			$rsbargainqty=mysqli_query($link,$sqlbargainqty);
			$bargain_qty_total=0;
			$total_DO_qty=0;
			while($rowbargainqty=mysqli_fetch_assoc($rsbargainqty))
			{
				$child_sku_code=$rowbargainqty['sku_code'];
				$sqltotalDOqty="SELECT SUM(DO_qty) AS  DO_qty FROM DO_transaction WHERE sauda_no='".$sauda_no."' AND sku_code	='".$child_sku_code."'";
				$rstotalDOqty=mysqli_query($link,$sqltotalDOqty);
				$rowtotalDOqty=mysqli_fetch_assoc($rstotalDOqty);
				//echo '<br />';
				$total_DO_qty=$total_DO_qty+$rowtotalDOqty['DO_qty'];
			}
			
			if($total_DO_qty > $bargain_qty)
			{
				//echo $DO_no.'-'.$sauda_no.'-'.$sku_code;
				//echo '<br />';
				//echo $total_DO_qty-$bargain_qty;
				//echo $DO_qty_child;
				
				if(($total_DO_qty-$bargain_qty) ==$DO_qty_child)
				{
settle_child_qty($DO_no,$sauda_no,$sku_code,$dns_prod_code,$mapped_sku_code,$customer_code,$bargain_date,$DO_qty_child,$destination,$delivery_date,$PO_no,$dns_DO_no,$DO_rate,$DO_amount,'update');
				}
				else
				{
				$sqlup="UPDATE DO_transaction SET is_settled='yes',DO_qty='".($total_DO_qty-$bargain_qty)."' WHERE sauda_no='".$sauda_no."' AND sku_code	='".$sku_code."' AND DO_no='".$DO_no."'";
				mysqli_query($link,$sqlup);
				$rest_child_qty=$DO_qty_child-($total_DO_qty-$bargain_qty);
				settle_child_qty($DO_no,$sauda_no,$sku_code,$dns_prod_code,$mapped_sku_code,$customer_code,$bargain_date,$rest_child_qty,$destination,$delivery_date,$PO_no,$dns_DO_no,$DO_rate,$DO_amount,'insert');

				}
			}
			else
			{
				$sqlup="UPDATE DO_transaction SET is_settled='yes' WHERE sauda_no='".$sauda_no."' AND sku_code	='".$sku_code."' AND DO_no='".$DO_no."'";
				mysqli_query($link,$sqlup);
			}
}
function settle_child_qty($DO_no,$sauda_no,$sku_code,$dns_prod_code,$mapped_sku_code,$customer_code,$bargain_date,$DO_qty_child,$destination,$delivery_date,$PO_no,$dns_DO_no,$DO_rate,$DO_amount,$clause)
{
	$sqlimmbargain="SELECT sauda_no,qty,sku_code,sale_rate  FROM DO_master WHERE customer_code='".$customer_code."' 
					AND mapped_sku_code	='".$mapped_sku_code."' AND DATE_FORMAT(SUBSTRING(sauda_no,-14,14),'%Y-%m-%d %H:%i:%s') 
					> '".$bargain_date."' AND qty >0 ORDER BY  DATE_FORMAT(SUBSTRING(sauda_no,-14,14),'%Y-%m-%d %H:%i:%s') DESC LIMIT 0,1";
	$rsimmbargain=mysqli_query($link,$sqlimmbargain);
	$rowimmbargain=mysqli_fetch_assoc($rsimmbargain);					
	$bargain_qty_next=$rowimmbargain['qty'];
	$sauda_no_next=$rowimmbargain['sauda_no'];
	
	$sqlbargainqty="SELECT qty,sku_code  FROM DO_master WHERE sauda_no='".$sauda_no_next."' AND mapped_sku_code	='".$mapped_sku_code."'";
	$rsbargainqty=mysqli_query($link,$sqlbargainqty);
	$bargain_qty_total=0;
	$total_DO_qty=0;
	while($rowbargainqty=mysqli_fetch_assoc($rsbargainqty))
	{
		$child_sku_code=$rowbargainqty['sku_code'];
		$sqltotalDOqty="SELECT SUM(DO_qty) AS  DO_qty FROM DO_transaction WHERE sauda_no='".$sauda_no."' AND sku_code	='".$child_sku_code."'";
		$rstotalDOqty=mysqli_query($link,$sqltotalDOqty);
		$rowtotalDOqty=mysqli_fetch_assoc($rstotalDOqty);
		//echo '<br />';
		$total_DO_qty_next=$total_DO_qty_next+$rowtotalDOqty['DO_qty'];
	}
	if(($bargain_qty_next-$total_DO_qty_next) > $DO_qty_child)
	{
		$sqlsaleratech="SELECT sale_rate FROM DO_master WHERE sauda_no='".$sauda_no_next."' AND sku_code	='".$sku_code."'";
		$rssaleratech=mysqli_query($link,$sqlsaleratech);
		$rowsaleratech=mysqli_fetch_assoc($rssaleratech);
		$sale_rate_next=$rowsaleratech['sale_rate'];

		if($clause=='update')
		{
		$amount_next=($DO_amount-($DO_rate*$DO_qty_child)+($sale_rate_next*$DO_qty_child));
		$sqlup="UPDATE DO_transaction SET is_settled='yes',DO_qty='".$DO_qty_child."',sauda_no='".$sauda_no_next."',DO_rate='".$sale_rate_next."',
		DO_amount='".$amount_next."' WHERE sauda_no='".$sauda_no."' AND sku_code	='".$sku_code."' AND DO_no='".$DO_no."'";
		mysqli_query($link,$sqlup);
		}
		else
		{
		$amount_next=($DO_amount-($DO_rate*$DO_qty_child)+($sale_rate_next*$DO_qty_child));
		$sqlinsertDOdetails="INSERT INTO DO_transaction SET sauda_no='".$sauda_no_next."',
							customer_code ='".$customer_code."',
							destination	='".$destination."',
							DO_no		='".$DO_no."',
							sku_code	='".$sku_code."',
							DO_qty		='".$DO_qty_child."',
							DO_rate		='".$sale_rate_next."',
							DO_amount	='".$amount_next."',
							DO_date		='".$DO_date."',
							PO_no 		='".$PO_no."',
							delivery_date ='".$delivery_date."',
							DO_status	='',
							dns_DO_no	='".$dns_DO_no."',
							download_time=CURRENT_TMESTAMP()";
		//mysqli_query($link,$sqlinsertDOdetails);					
		}
	}
}
	mysqli_close($link);
?>