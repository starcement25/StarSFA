<?php
	$nick_name='RKBK';
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234");
	require("include/config-setup.php");
	define("DB","acedns_$nick_name");
	//require("include/dbcon.php");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	require("include/config-email-setup.php");
	
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		
		$val=$year.'-'.$month.'-'.$date;
		$dateprev=date('d/m/Y', strtotime("-1 days,$val "));
		$sqlbranch="SELECT branch_code,branch_name FROM branch_master WHERE branch_code IN 
					(SELECT DISTINCT branch_code FROM branch_product_group_wise_stock) ORDER BY branch_name ASC";
		$resbranch=mysqli_query($link,$sqlbranch) or die(mysqli_error()." Error in select branch: ".$sqlbranch);
        while($rowbranch=mysqli_fetch_assoc($resbranch))
        {
			$branch_code=$rowbranch['branch_code'];
			$branch_name=$rowbranch['branch_name'];
			$emailbody.="<html><head><title>MIS-SALE</title></head>
							<body><table border=1 >
							<tr>
							<th style='width:730px;min-height:40px;text-align:center' colspan='19'><strong><span style='font-size:8pt;font-family:Arial 
							CE'>".strtoupper($branch_name)." <br /> AS on ".$dateprev."</span></strong></th></tr>
							<tr>
							<th style='width:730px;min-height:15px;text-align:center;background-color:#92D3FC;' colspan='19'>
							<strong><span style='font-size:8pt;font-family:Verdana'>GROUP WISE SUMMARY</span></strong></th></tr>
							<tr>
							<th style='width:10px;min-height:21px;text-align:center;background-color:AliceBlue;'><strong>
							<span style='font-size:8pt;font-family:Verdana'></span></strong></th>
							<th style='width:200px;min-height:21px;text-align:left;background-color:AliceBlue;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>Product Group</span></strong></th>
							<th style='width:70px;min-height:21px;text-align:left;background-color:AliceBlue;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>Opening Stock</span></strong></th>
							<th style='width:150px;min-height:21px;text-align:center;background-color:#FFCCCC;' colspan='3'><strong>
							<span style='font-size:8pt;font-family:Verdana'>Purchase Qty</span></strong></th>
							<th style='width:150px;min-height:21px;text-align:center;background-color:#FFCCCC;' colspan='3'><strong>
							<span style='font-size:8pt;font-family:Verdana'>Purchase Amt</span></strong></th>
							<th style='width:150px;min-height:21px;text-align:center;background-color:#FFFF99;' colspan='3'><strong>
							<span style='font-size:8pt;font-family:Verdana'>Sale Qty</span></strong></th>
							<th style='width:150px;min-height:21px;text-align:center;background-color:#FFFF99;' colspan='3'><strong>
							<span style='font-size:8pt;font-family:Verdana'>Sale Amt</span></strong></th>
							<th style='width:70px;min-height:21px;text-align:left;background-color:AliceBlue;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>Closing Stock</span></strong></th>
							</tr>
							<tr>
							<th style='width:10px;min-height:21px;text-align:center'><strong>
							<span style='font-size:8pt;font-family:Verdana'></span></strong></th>
							<th style='width:200px;min-height:21px;text-align:left'><strong>
							<span style='font-size:8pt;font-family:Verdana'></span></strong></th>
							<th style='width:70px;min-height:21px;text-align:left;background-color:AliceBlue;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>Qty</span></strong></th>
							<th style='width:50px;min-height:21px;text-align:center;background-color:#FFCCCC;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>DAY</span></strong></th>
							<th style='width:50px;min-height:21px;text-align:center;background-color:#FFCCCC;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>MTD</span></strong></th>
							<th style='width:50px;min-height:21px;text-align:center;background-color:#FFCCCC;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>YTD</span></strong></th>
							<th style='width:50px;min-height:21px;text-align:center;background-color:#FFCCCC;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>DAY</span></strong></th>
							<th style='width:50px;min-height:21px;text-align:center;background-color:#FFCCCC;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>MTD</span></strong></th>
							<th style='width:50px;min-height:21px;text-align:center;background-color:#FFCCCC;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>YTD</span></strong></th>
							<th style='width:50px;min-height:21px;text-align:center;background-color:#FFFF99;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>DAY</span></strong></th>
							<th style='width:50px;min-height:21px;text-align:center;background-color:#FFFF99;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>MTD</span></strong></th>
							<th style='width:50px;min-height:21px;text-align:center;background-color:#FFFF99;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>YTD</span></strong></th>
							<th style='width:50px;min-height:21px;text-align:center;background-color:#FFFF99;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>DAY</span></strong></th>
							<th style='width:50px;min-height:21px;text-align:center;background-color:#FFFF99;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>MTD</span></strong></th>
							<th style='width:50px;min-height:21px;text-align:center;background-color:#FFFF99;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>YTD</span></strong></th>
							<th style='width:70px;min-height:21px;text-align:left;background-color:AliceBlue;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>Qty</span></strong></th>
							</tr> ";
			
			$sqlproductgroup="SELECT product_group_code,product_group_name FROM product_group_master 
							WHERE product_group_code IN(SELECT product_group_code FROM product_master)";	
			$resproductgroup=mysqli_query($link,$sqlproductgroup) or die(mysqli_error()." Error in select product group product: ".$sqlproductgroup);
			$sl_no_product_group_wise=1;
			while($rowproductgroup=mysqli_fetch_assoc($resproductgroup))
			{
				$product_group_code=$rowproductgroup['product_group_code'];
				$product_group_name=$rowproductgroup['product_group_name'];
				//$prod_code=$rowproduct['prod_code'];
				
				$sql_branch_product_group_stk="SELECT * FROM branch_product_group_wise_stock 
												WHERE branch_code='".$branch_code."' AND product_group_code='".$product_group_code."'";
				$res_branch_product_group_stk=mysqli_query($link,$sql_branch_product_group_stk) or die(mysqli_error()." 
										Error in select branch product group stk: ".$sql_branch_product_group_stk);	
				$cnt_branch_product_group_stk=mysqli_num_rows($res_branch_product_group_stk);
				while($row_branch_product_group_stk=mysqli_fetch_assoc($res_branch_product_group_stk))
				{
					$opening_stk_day=$row_branch_product_group_stk['opening_stk_day'];
					$opening_stk_month=$row_branch_product_group_stk['opening_stk_month'];
					$opening_stk_year=$row_branch_product_group_stk['opening_stk_year'];
					$purchase_stk_day=$row_branch_product_group_stk['purchase_stk_day'];
					$purchase_stk_month=$row_branch_product_group_stk['purchase_stk_month'];
					$purchase_stk_year=$row_branch_product_group_stk['purchase_stk_year'];
					$purchase_amount_day=$row_branch_product_group_stk['purchase_amount_day'];
					$purchase_amount_month=$row_branch_product_group_stk['purchase_amount_month'];
					$purchase_amount_year=$row_branch_product_group_stk['purchase_amount_year'];
					$sale_stk_day=$row_branch_product_group_stk['sale_stk_day'];
					$sale_stk_month=$row_branch_product_group_stk['sale_stk_month'];
					$sale_stk_year=$row_branch_product_group_stk['sale_stk_year'];
					$sale_amount_day=$row_branch_product_group_stk['sale_amount_day'];
					$sale_amount_month=$row_branch_product_group_stk['sale_amount_month'];
					$sale_amount_year=$row_branch_product_group_stk['sale_amount_year'];
					$closing_stk=$row_branch_product_group_stk['closing_stk'];
					
					$total_opening_stk_day=$total_opening_stk_day+$opening_stk_day;
					$total_opening_stk_month=$total_opening_stk_month+$opening_stk_month;
					$total_opening_stk_year=$total_opening_stk_year+$opening_stk_year;
					$total_purchase_stk_day=$total_purchase_stk_day+$purchase_stk_day;
					$total_purchase_stk_month=$total_purchase_stk_month+$purchase_stk_month;
					$total_purchase_stk_year=$total_purchase_stk_year+$purchase_stk_year;
					$total_purchase_amount_day=$total_purchase_amount_day+$purchase_amount_day;
					$total_purchase_amount_month=$total_purchase_amount_month+$purchase_amount_month;
					$total_purchase_amount_year=$total_purchase_amount_year+$purchase_amount_year;
					$total_sale_stk_day=$total_sale_stk_day+$sale_stk_day;
					$total_sale_stk_month=$total_sale_stk_month+$sale_stk_month;
					$total_sale_stk_year=$total_sale_stk_year+$sale_stk_year;
					$total_sale_amount_day=$total_sale_amount_day+$sale_amount_day;
					$total_sale_amount_month=$total_sale_amount_month+$sale_amount_month;
					$total_sale_amount_year=$total_sale_amount_year+$sale_amount_year;
					$total_closing_stk=$total_closing_stk+$closing_stk;

					$emailbody.="<tr>
							<td style='width:10px;min-height:21px;text-align:center;background-color:AliceBlue;'>
							<span style='font-size:8pt;font-family:Verdana'>".$sl_no_product_group_wise++."</span></td>
							<td style='width:200px;min-height:21px;text-align:left;background-color:AliceBlue;'>
							<span style='font-size:8pt;font-family:Verdana'>".$product_group_name."</span></td>
							<td style='width:70px;min-height:21px;text-align:right;background-color:AliceBlue;'>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($opening_stk_year,2)."</span></td>
							<td style='width:50px;min-height:21px;text-align:right;background-color:#FFCCCC;'>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($purchase_stk_day,2)."</span></td>
							<td style='width:50px;min-height:21px;text-align:right;background-color:#FFCCCC;'>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($purchase_stk_month,2)."</span></td>
							<td style='width:50px;min-height:21px;text-align:right;background-color:#FFCCCC;'>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($purchase_stk_year,2)."</span></td>
							<td style='width:50px;min-height:21px;text-align:right;background-color:#FFCCCC;'>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($purchase_amount_day,2)."</span></td>
							<td style='width:50px;min-height:21px;text-align:right;background-color:#FFCCCC;'>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($purchase_amount_month,2)."</span></td>
							<td style='width:50px;min-height:21px;text-align:right;background-color:#FFCCCC;'>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($purchase_amount_year,2)."</span></td>
							<td style='width:50px;min-height:21px;text-align:right;background-color:#FFFF99;'>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($sale_stk_day,2)."</span></td>
							<td style='width:50px;min-height:21px;text-align:right;background-color:#FFFF99;'>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($sale_stk_month,2)."</span></td>
							<td style='width:50px;min-height:21px;text-align:right;background-color:#FFFF99;'>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($sale_stk_year,2)."</span></td>
							<td style='width:50px;min-height:21px;text-align:right;background-color:#FFFF99;'>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($sale_amount_day,2)."</span></td>
							<td style='width:50px;min-height:21px;text-align:right;background-color:#FFFF99;'>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($sale_amount_month,2)."</span></td>
							<td style='width:50px;min-height:21px;text-align:right;background-color:#FFFF99;'>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($sale_amount_year,2)."</span></td>
							<td style='width:70px;min-height:21px;text-align:right'>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($closing_stk,2)."</span></td>
							</tr>";
				}
					$emailbodyproduct.="<tr><td style='width:10px;min-height:21px;text-align:center;background-color:AliceBlue;;font-weight:bold;'><strong>
									<span style='font-size:8pt;font-family:Verdana;'>#</span></strong></td>
									<td style='width:200px;min-height:21px;text-align:left;background-color:AliceBlue;font-weight:bold;'><strong>
									<span style='font-size:8pt;font-family:Verdana'>".$product_group_name."</span></strong></td>
									<td style='width:70px;min-height:21px;text-align:right'><strong>
									<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'></span></strong></td>
									<td style='width:50px;min-height:21px;text-align:right'><strong>
									<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'></span></strong></td>
									<td style='width:50px;min-height:21px;text-align:right'><strong>
									<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'></span></strong></td>
									<td style='width:50px;min-height:21px;text-align:right'><strong>
									<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'></span></strong></td>
									<td style='width:50px;min-height:21px;text-align:right'><strong>
									<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'></span></strong></td>
									<td style='width:50px;min-height:21px;text-align:right'><strong>
									<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'></span></strong></td>
									<td style='width:50px;min-height:21px;text-align:right'><strong>
									<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'></span></strong></td>
									<td style='width:50px;min-height:21px;text-align:right'><strong>
									<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'></span></strong></td>
									<td style='width:50px;min-height:21px;text-align:right'><strong>
									<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'></span></strong></td>
									<td style='width:50px;min-height:21px;text-align:right'><strong>
									<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'></span></strong></td>
									<td style='width:50px;min-height:21px;text-align:right'><strong>
									<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'></span></strong></td>
									<td style='width:50px;min-height:21px;text-align:right'><strong>
									<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'></span></strong></td>
									<td style='width:50px;min-height:21px;text-align:right'><strong>
									<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'></span></strong></td>
									<td style='width:70px;min-height:21px;text-align:right'><strong>
									<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'></span></strong></td>
									</tr>";
					$sqlproduct="SELECT prod_code,prod_desc FROM product_master WHERE product_group_code='".$product_group_code."'";	
					$resproduct=mysqli_query($link,$sqlproduct) or die(mysqli_error()." Error in select product group product: ".$sqlproduct);
					$sl_no_product_wise=1;
					while($rowproduct=mysqli_fetch_assoc($resproduct))
					{
						$prod_code=$rowproduct['prod_code'];
						$prod_desc=$rowproduct['prod_desc'];
						
						$sql_branch_product_stk="SELECT * FROM branch_product_wise_stock WHERE branch_code='".$branch_code."' AND product_code='".$prod_code."'";
						$res_branch_product_stk=mysqli_query($link,$sql_branch_product_stk) or die(mysqli_error()." 
												Error in select branch product stk: ".$sql_branch_product_stk);	
						$cnt_branch_product_stk=mysqli_num_rows($res_branch_product_stk);
						$row_branch_product_stk=mysqli_fetch_assoc($res_branch_product_stk);
							$opening_stk_day_product=$row_branch_product_stk['opening_stk_day'];
							$opening_stk_month_product=$row_branch_product_stk['opening_stk_month'];
							$opening_stk_year_product=$row_branch_product_stk['opening_stk_year'];
							$purchase_stk_day_product=$row_branch_product_stk['purchase_stk_day'];
							$purchase_stk_month_product=$row_branch_product_stk['purchase_stk_month'];
							$purchase_stk_year_product=$row_branch_product_stk['purchase_stk_year'];
							$purchase_amount_day_product=$row_branch_product_stk['purchase_amount_day'];
							$purchase_amount_month_product=$row_branch_product_stk['purchase_amount_month'];
							$purchase_amount_year_product=$row_branch_product_stk['purchase_amount_year'];
							$sale_stk_day_product=$row_branch_product_stk['sale_stk_day'];
							$sale_stk_month_product=$row_branch_product_stk['sale_stk_month'];
							$sale_stk_year_product=$row_branch_product_stk['sale_stk_year'];
							$sale_amount_day_product=$row_branch_product_stk['sale_amount_day'];
							$sale_amount_month_product=$row_branch_product_stk['sale_amount_month'];
							$sale_amount_year_product=$row_branch_product_stk['sale_amount_year'];
							$closing_stk_product=$row_branch_product_stk['closing_stk'];

						$emailbodyproduct.="<tr>
										<td style='width:10px;min-height:21px;text-align:center;background-color:AliceBlue;'>
										<span style='font-size:8pt;font-family:Verdana'>".$sl_no_product_wise++."</span></td>
										<td style='width:200px;min-height:21px;text-align:left;background-color:AliceBlue;'>
										<span style='font-size:8pt;font-family:Verdana'>".$prod_desc."</span></td>
										<td style='width:70px;min-height:21px;text-align:right;background-color:AliceBlue;'>
										<span style='font-size:8pt;font-family:Verdana'>".number_format($opening_stk_year_product,2)."</span></td>
										<td style='width:50px;min-height:21px;text-align:right;background-color:#FFCCCC;'>
										<span style='font-size:8pt;font-family:Verdana'>".number_format($purchase_stk_day_product,2)."</span></td>
										<td style='width:50px;min-height:21px;text-align:right;background-color:#FFCCCC;'>
										<span style='font-size:8pt;font-family:Verdana'>".number_format($purchase_stk_month_product,2)."</span></td>
										<td style='width:50px;min-height:21px;text-align:right;background-color:#FFCCCC;'>
										<span style='font-size:8pt;font-family:Verdana'>".number_format($purchase_stk_year_product,2)."</span></td>
										<td style='width:50px;min-height:21px;text-align:right;background-color:#FFCCCC;'>
										<span style='font-size:8pt;font-family:Verdana'>".number_format($purchase_amount_day_product,2)."</span></td>
										<td style='width:50px;min-height:21px;text-align:right;background-color:#FFCCCC;'>
										<span style='font-size:8pt;font-family:Verdana'>".number_format($purchase_amount_month_product,2)."</span></td>
										<td style='width:50px;min-height:21px;text-align:right;background-color:#FFCCCC;'>
										<span style='font-size:8pt;font-family:Verdana'>".number_format($purchase_amount_year_product,2)."</span></td>
										<td style='width:50px;min-height:21px;text-align:right;background-color:#FFFF99;'>
										<span style='font-size:8pt;font-family:Verdana'>".number_format($sale_stk_day_product,2)."</span></td>
										<td style='width:50px;min-height:21px;text-align:right;background-color:#FFFF99;'>
										<span style='font-size:8pt;font-family:Verdana'>".number_format($sale_stk_month_product,2)."</span></td>
										<td style='width:50px;min-height:21px;text-align:right;background-color:#FFFF99;'>
										<span style='font-size:8pt;font-family:Verdana'>".number_format($sale_stk_year_product,2)."</span></td>
										<td style='width:50px;min-height:21px;text-align:right;background-color:#FFFF99;'>
										<span style='font-size:8pt;font-family:Verdana'>".number_format($sale_amount_day_product,2)."</span></td>
										<td style='width:50px;min-height:21px;text-align:right;background-color:#FFFF99;'>
										<span style='font-size:8pt;font-family:Verdana'>".number_format($sale_amount_month_product,2)."</span></td>
										<td style='width:50px;min-height:21px;text-align:right;background-color:#FFFF99;'>
										<span style='font-size:8pt;font-family:Verdana'>".number_format($sale_amount_year_product,2)."</span></td>
										<td style='width:70px;min-height:21px;text-align:right'>
										<span style='font-size:8pt;font-family:Verdana'>".number_format($closing_stk_product,2)."</span></td>
										</tr>";
					}
					$emailbodyproduct.="<tr>
							<td style='width:10px;min-height:21px;text-align:center;background-color:AliceBlue;'><strong>
							<span style='font-size:8pt;font-family:Verdana'></span></strong></td>
							<td style='width:200px;min-height:21px;text-align:left;background-color:AliceBlue;font-weight:bold;'><strong>
							<span style='font-size:8pt;font-family:Verdana;'>Group Total (".$product_group_name.")</span></strong></td>
							<td style='width:70px;min-height:21px;text-align:right;background-color:AliceBlue;font-weight:bold;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($opening_stk_year,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right;background-color:#FFCCCC;font-weight:bold;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($purchase_stk_day,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right;background-color:#FFCCCC;font-weight:bold;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($purchase_stk_month,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right;background-color:#FFCCCC;font-weight:bold;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($purchase_stk_year,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right;background-color:#FFCCCC;font-weight:bold;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($purchase_amount_day,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right;background-color:#FFCCCC;font-weight:bold;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($purchase_amount_month,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right;background-color:#FFCCCC;font-weight:bold;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($purchase_amount_year,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right;background-color:#FFFF99;font-weight:bold;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($sale_stk_day,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right;background-color:#FFFF99;font-weight:bold;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($sale_stk_month,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right;background-color:#FFFF99;font-weight:bold;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($sale_stk_year,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right;background-color:#FFFF99;font-weight:bold;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($sale_amount_day,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right;background-color:#FFFF99;font-weight:bold;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($sale_amount_month,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right;background-color:#FFFF99;font-weight:bold;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($sale_amount_year,2)."</span></strong></td>
							<td style='width:70px;min-height:21px;text-align:right;font-weight:bold;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>".number_format($closing_stk,2)."</span></strong></td>
							</tr>";
			}
			$emailbody.="<tr style='height:20px;'><td colspan='15'></td></tr><tr><td style='width:10px;min-height:21px;text-align:center'><strong>
							<span style='font-size:8pt;font-family:Verdana;'></span></strong></th>
							<td style='width:200px;min-height:21px;text-align:left;background-color:#92D3FC;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>GRAND TOTAL</span></strong></td>
							<td style='width:70px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_opening_stk_year,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_purchase_stk_day,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_purchase_stk_month,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_purchase_stk_year,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_purchase_amount_day,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_purchase_amount_month,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_purchase_amount_year,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_sale_stk_day,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_sale_stk_month,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_sale_stk_year,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_sale_amount_day,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_sale_amount_month,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_sale_amount_year,2)."</span></strong></td>
							<td style='width:70px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_closing_stk,2)."</span></strong></td>
							</tr>";
			$emailbody.="<tr><th style='width:550px;min-height:15px;text-align:center;background-color:#92D3FC' colspan='19'>
						<strong><span style='font-size:8pt;font-family:Verdana'>GROUP & ITEM WISE SUMMARY</span></strong></th></tr>
						<tr style='background-color:AliceBlue'>
							<th style='width:10px;min-height:21px;text-align:center'><strong>
							<span style='font-size:8pt;font-family:Verdana'></span></strong></th>
							<th style='width:200px;min-height:21px;text-align:left'><strong>
							<span style='font-size:8pt;font-family:Verdana'>Item</span></strong></th>
							<th style='width:70px;min-height:21px;text-align:left'><strong>
							<span style='font-size:8pt;font-family:Verdana'>Opening Stock</span></strong></th>
							<th style='width:150px;min-height:21px;text-align:center;background-color:#FFCCCC;' colspan='3'><strong>
							<span style='font-size:8pt;font-family:Verdana'>Purchase Qty</span></strong></th>
							<th style='width:150px;min-height:21px;text-align:center;background-color:#FFCCCC;' colspan='3'><strong>
							<span style='font-size:8pt;font-family:Verdana'>Purchase Amt</span></strong></th>
							<th style='width:150px;min-height:21px;text-align:center;background-color:#FFFF99;' colspan='3'><strong>
							<span style='font-size:8pt;font-family:Verdana'>Sale Qty</span></strong></th>
							<th style='width:150px;min-height:21px;text-align:center;background-color:#FFFF99;' colspan='3'><strong>
							<span style='font-size:8pt;font-family:Verdana'>Sale Amt</span></strong></th>
							<th style='width:70px;min-height:21px;text-align:left'><strong>
							<span style='font-size:8pt;font-family:Verdana'>Closing Stock</span></strong></th>
						</tr>
						<tr>
							<th style='width:10px;min-height:21px;text-align:center'><strong>
							<span style='font-size:8pt;font-family:Verdana'></span></strong></th>
							<th style='width:200px;min-height:21px;text-align:left'><strong>
							<span style='font-size:8pt;font-family:Verdana'></span></strong></th>
							<th style='width:70px;min-height:21px;text-align:left;background-color:AliceBlue;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>Qty</span></strong></th>
							<th style='width:50px;min-height:21px;text-align:center;background-color:#FFCCCC;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>DAY</span></strong></th>
							<th style='width:50px;min-height:21px;text-align:center;background-color:#FFCCCC;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>MTD</span></strong></th>
							<th style='width:50px;min-height:21px;text-align:center;background-color:#FFCCCC;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>YTD</span></strong></th>
							<th style='width:50px;min-height:21px;text-align:center;background-color:#FFCCCC;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>DAY</span></strong></th>
							<th style='width:50px;min-height:21px;text-align:center;background-color:#FFCCCC;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>MTD</span></strong></th>
							<th style='width:50px;min-height:21px;text-align:center;background-color:#FFCCCC;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>YTD</span></strong></th>
							<th style='width:50px;min-height:21px;text-align:center;background-color:#FFFF99;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>DAY</span></strong></th>
							<th style='width:50px;min-height:21px;text-align:center;background-color:#FFFF99;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>MTD</span></strong></th>
							<th style='width:50px;min-height:21px;text-align:center;background-color:#FFFF99;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>YTD</span></strong></th>
							<th style='width:50px;min-height:21px;text-align:center;background-color:#FFFF99;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>DAY</span></strong></th>
							<th style='width:50px;min-height:21px;text-align:center;background-color:#FFFF99;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>MTD</span></strong></th>
							<th style='width:50px;min-height:21px;text-align:center;background-color:#FFFF99;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>YTD</span></strong></th>
							<th style='width:70px;min-height:21px;text-align:left;background-color:AliceBlue;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>Qty</span></strong></th>
							</tr> ".$emailbodyproduct."";
				$emailbody.="<tr style='height:20px;'><td colspan='19'></td></tr><tr><td style='width:10px;min-height:21px;text-align:center'><strong>
							<span style='font-size:8pt;font-family:Verdana;'></span></strong></th>
							<td style='width:200px;min-height:21px;text-align:left;background-color:#92D3FC;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>GRAND TOTAL</span></strong></td>
							<td style='width:70px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_opening_stk_year,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_purchase_stk_day,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_purchase_stk_month,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_purchase_stk_year,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_purchase_amount_day,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_purchase_amount_month,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_purchase_amount_year,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_sale_stk_day,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_sale_stk_month,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_sale_stk_year,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_sale_amount_day,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_sale_amount_month,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_sale_amount_year,2)."</span></strong></td>
							<td style='width:70px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($total_closing_stk,2)."</span></strong></td>
							</tr></table><br /><br />Powered By Forcepower<br /></body></html>";		
		}
	
		$mailsubj="RKBK sale in sale out report";
		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8\n";
		$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
						"Reply-To:".FROMEMAIL." \r\n" .
						"Bcc: ".BCCEMAIL." \r\n" .
						'X-Mailer: PHP/' . phpversion();
		$email='';				
		if(@mail($email, $mailsubj, $emailbody, $headers,'-facedns@coral.in'))
			{
				echo 'SUCCESSFULL';
			}
		
		echo $emailbody;
		exit();
?>		