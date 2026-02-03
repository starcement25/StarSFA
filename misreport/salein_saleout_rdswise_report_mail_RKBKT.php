<?php
	$nick_name='RKBKT';
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234");
	require("include/config-setup.php");
	define("DB","acedns_$nick_name");
	//require("include/dbcon.php");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	//require("include/config-email-setup.php");
	define("BCCEMAIL","dipankarc@coral.in,acedns@coral.in");
	
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		
		$val=$year.'-'.$month.'-'.$date;
		$dateprev=date('d/m/Y', strtotime("-1 days,$val "));
		
		
			$emailbody.="<html><head><title>MIS-SALE</title></head>
							<body><table border=1 >
							<tr>
							<th style='width:730px;min-height:40px;text-align:center' colspan='19'><strong><span style='font-size:8pt;font-family:Arial 
							CE'>".strtoupper('GULAMKHERA PUMP')." <br /> AS on ".$dateprev."</span></strong></th></tr>
							<tr>
							<th style='width:730px;min-height:15px;text-align:center;background-color:#92D3FC;' colspan='19'>
							<strong><span style='font-size:8pt;font-family:Verdana'>ITEM & RDS WISE SUMMARY</span></strong></th></tr>";
							
			$sqlproduct="SELECT prod_code,prod_desc FROM product_master WHERE 1";	
			$resproduct=mysqli_query($link,$sqlproduct) or die(mysqli_error()." Error in select product group product: ".$sqlproduct);
			$sl_no_product_wise=1;
			while($rowproduct=mysqli_fetch_assoc($resproduct))
			{
				$prod_code=$rowproduct['prod_code'];
				$prod_desc=$rowproduct['prod_desc'];
				 
				$emailbody.="<tr>
							<th style='width:730px;min-height:15px;text-align:center;background-color:#92D3FC;' colspan='19'>
							<strong><span style='font-size:8pt;font-family:Verdana'>Product: ".$prod_desc."</span></strong></th></tr>";
				
				$emailbody.="<th style='width:10px;min-height:21px;text-align:center;background-color:AliceBlue;'><strong>
							<span style='font-size:8pt;font-family:Verdana'></span></strong></th>
							<th style='width:200px;min-height:21px;text-align:left;background-color:AliceBlue;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>RDS</span></strong></th>
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
			
				$sqlrdsemployee="SELECT rds_code,rds_name FROM rds_master WHERE emp_code='E0036'";
				$resrdsemployee=mysqli_query($link,$sqlrdsemployee) or die(mysqli_error()." Error in select rds employee: ".$sqlrdsemployee);
				$sl_no_rds_wise=1;
				while($rowrdsemployee=mysqli_fetch_assoc($resrdsemployee))
				{
					$rds_code=$rowrdsemployee['rds_code'];
					$rds_name=$rowrdsemployee['rds_name'];

				
					$sql_branch_rds_product_stk="SELECT * FROM branch_rds_product_wise_stock WHERE branch_code='B0001' AND rds_code='".$rds_code."' 
												AND product_code='".$prod_code."'";
					$res_branch_rds_product_stk=mysqli_query($link,$sql_branch_rds_product_stk) or die(mysqli_error()." 
											Error in select branch product stk: ".$sql_branch_rds_product_stk);	
					$cnt_branch_rds_product_stk=mysqli_num_rows($res_branch_rds_product_stk);
					$row_branch_rds_product_stk=mysqli_fetch_assoc($res_branch_rds_product_stk);
					$opening_stk_day_product=$row_branch_rds_product_stk['opening_stk_day'];
					$opening_stk_month_product=$row_branch_rds_product_stk['opening_stk_month'];
					$opening_stk_year_product=$row_branch_rds_product_stk['opening_stk_year'];
					$purchase_stk_day_product=$row_branch_rds_product_stk['purchase_stk_day'];
					$purchase_stk_month_product=$row_branch_rds_product_stk['purchase_stk_month'];
					$purchase_stk_year_product=$row_branch_rds_product_stk['purchase_stk_year'];
					$purchase_amount_day_product=$row_branch_rds_product_stk['purchase_amount_day'];
					$purchase_amount_month_product=$row_branch_rds_product_stk['purchase_amount_month'];
					$purchase_amount_year_product=$row_branch_rds_product_stk['purchase_amount_year'];
					$sale_stk_day_product=$row_branch_rds_product_stk['sale_stk_day'];
					$sale_stk_month_product=$row_branch_rds_product_stk['sale_stk_month'];
					$sale_stk_year_product=$row_branch_rds_product_stk['sale_stk_year'];
					$sale_amount_day_product=$row_branch_rds_product_stk['sale_amount_day'];
					$sale_amount_month_product=$row_branch_rds_product_stk['sale_amount_month'];
					$sale_amount_year_product=$row_branch_rds_product_stk['sale_amount_year'];
					$closing_stk_product=$row_branch_rds_product_stk['closing_stk'];
					
					${total_opening_stk_day.$prod_code}=${total_opening_stk_day.$prod_code}+$opening_stk_day_product;
					${total_opening_stk_month.$prod_code}=${total_opening_stk_month.$prod_code}+$opening_stk_month_product;
					${total_opening_stk_year.$prod_code}=${total_opening_stk_year.$prod_code}+$opening_stk_year_product;
					${total_purchase_stk_day.$prod_code}=${total_purchase_stk_day.$prod_code}+$purchase_stk_day_product;
					${total_purchase_stk_month.$prod_code}=${total_purchase_stk_month.$prod_code}+$purchase_stk_month_product;
					${total_purchase_stk_year.$prod_code}=${total_purchase_stk_year.$prod_code}+$purchase_stk_year_product;
					${total_purchase_amount_day.$prod_code}=${total_purchase_amount_day.$prod_code}+$purchase_amount_day_product;
					${total_purchase_amount_month.$prod_code}=${total_purchase_amount_month.$prod_code}+$purchase_amount_month_product;
					${total_purchase_amount_year.$prod_code}=${total_purchase_amount_year.$prod_code}+$purchase_amount_year_product;
					${total_sale_stk_day.$prod_code}=${total_sale_stk_day.$prod_code}+$sale_stk_day_product;
					${total_sale_stk_month.$prod_code}=${total_sale_stk_month.$prod_code}+$sale_stk_month_product;
					${total_sale_stk_year.$prod_code}=${total_sale_stk_year.$prod_code}+$sale_stk_year_product;
					${total_sale_amount_day.$prod_code}=${total_sale_amount_day.$prod_code}+$sale_amount_day_product;
					${total_sale_amount_month.$prod_code}=${total_sale_amount_month.$prod_code}+$sale_amount_month_product;
					${total_sale_amount_year.$prod_code}=${total_sale_amount_year.$prod_code}+$sale_amount_year_product;
					${total_closing_stk.$prod_code}=${total_closing_stk.$prod_code}+$closing_stk_product;

				
					$emailbody.="<tr>
							<td style='width:10px;min-height:21px;text-align:center;background-color:AliceBlue;'>
							<span style='font-size:8pt;font-family:Verdana'>".$sl_no_rds_wise++."</span></td>
							<td style='width:200px;min-height:21px;text-align:left;background-color:AliceBlue;'>
							<span style='font-size:8pt;font-family:Verdana'>".$rds_name."</span></td>
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

						$emailbody.="<tr style='height:20px;'><td colspan='15'></td></tr><tr><td style='width:10px;min-height:21px;text-align:center'><strong>
							<span style='font-size:8pt;font-family:Verdana;'></span></strong></th>
							<td style='width:200px;min-height:21px;text-align:left;background-color:#92D3FC;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>Product Total (".$prod_desc.")</span></strong></td>
							<td style='width:70px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format(${total_opening_stk_year.$prod_code},2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format(${total_purchase_stk_day.$prod_code},2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format(${total_purchase_stk_month.$prod_code},2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format(${total_purchase_stk_year.$prod_code},2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format(${total_purchase_amount_day.$prod_code},2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format(${total_purchase_amount_month.$prod_code},2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format(${total_purchase_amount_year.$prod_code},2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format(${total_sale_stk_day.$prod_code},2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format(${total_sale_stk_month.$prod_code},2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format(${total_sale_stk_year.$prod_code},2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format(${total_sale_amount_day.$prod_code},2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format(${total_sale_amount_month.$prod_code},2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format(${total_sale_amount_year.$prod_code},2)."</span></strong></td>
							<td style='width:70px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format(${total_closing_stk.$prod_code},2)."</span></strong></td>
							</tr>";
					$grandtotal_opening_stk_day=$grandtotal_opening_stk_day+${total_opening_stk_day.$prod_code};
					$grandtotal_opening_stk_month=$grandtotal_opening_stk_month+${total_opening_stk_month.$prod_code};
					$grandtotal_opening_stk_year=$grandtotal_opening_stk_year+${total_opening_stk_year.$prod_code};
					$grandtotal_purchase_stk_day=$grandtotal_purchase_stk_day+${total_purchase_stk_day.$prod_code};
					$grandtotal_purchase_stk_month=$grandtotal_purchase_stk_month+${total_purchase_stk_month.$prod_code};
					$grandtotal_purchase_stk_year=$grandtotal_purchase_stk_year+${total_purchase_stk_year.$prod_code};
					$grandtotal_purchase_amount_day=$grandtotal_purchase_amount_day+${total_purchase_amount_day.$prod_code};
					$grandtotal_purchase_amount_month=$grandtotal_purchase_amount_month+${total_purchase_amount_month.$prod_code};
					$grandtotal_purchase_amount_year=$grandtotal_purchase_amount_year+${total_purchase_amount_year.$prod_code};
					$grandtotal_sale_stk_day=$grandtotal_sale_stk_day+${total_sale_stk_day.$prod_code};
					$grandtotal_sale_stk_month=$grandtotal_sale_stk_month+${total_sale_stk_month.$prod_code};
					$grandtotal_sale_stk_year=$grandtotal_sale_stk_year+${total_sale_stk_year.$prod_code};
					$grandtotal_sale_amount_day=$grandtotal_sale_amount_day+${total_sale_amount_day.$prod_code};
					$grandtotal_sale_amount_month=$grandtotal_sale_amount_month+${total_sale_amount_month.$prod_code};
					$grandtotal_sale_amount_year=$grandtotal_sale_amount_year+${total_sale_amount_year.$prod_code};
					$grandtotal_closing_stk=$grandtotal_closing_stk+${total_closing_stk.$prod_code};
					}

				$emailbody.="<tr style='height:20px;'><td colspan='19'></td></tr><tr><td style='width:10px;min-height:21px;text-align:center'><strong>
							<span style='font-size:8pt;font-family:Verdana;'></span></strong></th>
							<td style='width:200px;min-height:21px;text-align:left;background-color:#92D3FC;'><strong>
							<span style='font-size:8pt;font-family:Verdana'>GRAND TOTAL</span></strong></td>
							<td style='width:70px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($grandtotal_opening_stk_year,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($grandtotal_purchase_stk_day,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($grandtotal_purchase_stk_month,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($grandtotal_purchase_stk_year,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($grandtotal_purchase_amount_day,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($grandtotal_purchase_amount_month,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($grandtotal_purchase_amount_year,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($grandtotal_sale_stk_day,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($grandtotal_sale_stk_month,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($grandtotal_sale_stk_year,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($grandtotal_sale_amount_day,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($grandtotal_sale_amount_month,2)."</span></strong></td>
							<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($grandtotal_sale_amount_year,2)."</span></strong></td>
							<td style='width:70px;min-height:21px;text-align:right'><strong>
							<span style='font-size:8pt;font-family:Verdana;color:#FF0000;'>".number_format($grandtotal_closing_stk,2)."</span></strong></td>
							</tr></table><br /><br />Powered By Forcepower<br /></body></html>";
			$mailsubj="RKBKT - GULAMKHERA PUMP sale in sale out report RDS and ITEM wise";
			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=UTF-8\n";
			$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
							"Reply-To:".FROMEMAIL." \r\n" .
							"Bcc: ".BCCEMAIL." \r\n".
							'X-Mailer: PHP/' . phpversion();
			$email='kkd@forcepower.in';				
			if(@mail($email, $mailsubj, $emailbody, $headers,'-facedns@acedns.in'))
				{
					echo 'SUCCESSFULL';
				}
			
			//echo $emailbody;
			//exit();
?>		