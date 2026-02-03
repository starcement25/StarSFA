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
		
		$sqlbranch="SELECT branch_code FROM branch_master ORDER BY branch_name ASC";
		$resbranch=mysqli_query($link,$sqlbranch) or die(mysqli_error()." Error in select branch: ".$sqlbranch);
		
        while($rowbranch=mysqli_fetch_assoc($resbranch))
        {
			$branch_code=$rowbranch['branch_code'];
			$sqlbranchemployee="SELECT emp_code FROM employee_master WHERE branch_code='".$branch_code."' AND emp_code !='C0007'";
			$resbranchemployee=mysqli_query($link,$sqlbranchemployee) or die(mysqli_error()." Error in select branch employee: ".$sqlbranchemployee);
			$branch_employee='';
			while($rowbranchemployee=mysqli_fetch_assoc($resbranchemployee))
        	{
				$emp_code=$rowbranchemployee['emp_code'];
				$branch_employee=$branch_employee."'".$emp_code."'".',';
			}
			$branch_employee=substr($branch_employee,0,-1);
			//exit();
			if($branch_employee!=''){
			$sqlproduct="SELECT PGM.product_group_code,PM.prod_code FROM product_group_master PGM,product_master PM 
							WHERE PGM.product_group_code=PM.product_group_code ORDER BY PM.prod_desc ASC";	
			$resproduct=mysqli_query($link,$sqlproduct) or die(mysqli_error()." Error in select product group product: ".$sqlproduct);
			while($rowproduct=mysqli_fetch_assoc($resproduct))
			{
				$product_group_code=$rowproduct['product_group_code'];
				$prod_code=$rowproduct['prod_code'];					
		
				$sql_opening_stk_initial="SELECT opening_stk FROM branch_product_wise_opening_stk_master 
										  WHERE branch_code='".$branch_code."' AND product_code='".$prod_code."'";
				$res_opening_stk_initial=mysqli_query($link,$sql_opening_stk_initial) or die(mysqli_error()." 
										Error in select opening stk initial: ".$sql_opening_stk_initial);
				$row_opening_stk_initial=mysqli_fetch_assoc($res_opening_stk_initial);
				$opening_stk_initial=$row_opening_stk_initial['opening_stk'];
				${opening_stk_initial.$product_group_code}=${opening_stk_initial.$product_group_code}+$opening_stk_initial;
				if($month>='04'){
					$fiinancial_year=$year.'-04-01';
				}
				else
				{
					$fiinancial_year=($year-1).'-04-01';
				}
				
				$sql_purchase_day="SELECT SUM(OD.qty) AS total_purchase_qty_day,SUM(OD.qty*OD.sale_rate) AS total_purchase_amount_day 
									FROM order_header OH,order_details OD
									WHERE OH.order_no=OD.order_no AND OD.sku_code='".$prod_code."' AND SUBSTRING(OH.order_no,1,1)='O'
									AND SUBSTRING(OH.order_no,2,5) IN($branch_employee) AND OH.transaction_type='PB' AND
									DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d')=DATE_FORMAT(NOW(),'%Y%-%m-%d')";
				$res_purchase_day=mysqli_query($link,$sql_purchase_day) or die(mysqli_error()." Error in select purchase for the day: ".$sql_purchase_day);
				$row_purchase_day=mysqli_fetch_assoc($res_purchase_day);
				$purchase_day=$row_purchase_day['total_purchase_qty_day'];
				$purchase_amount_day=$row_purchase_day['total_purchase_amount_day'];
					
				$sql_sale_day="SELECT SUM(OD.qty) AS total_sale_qty_day,SUM(OD.qty*OD.sale_rate) AS total_sale_amount_day 
								FROM order_header OH,order_details OD
								WHERE OH.order_no=OD.order_no AND OD.sku_code='".$prod_code."' AND SUBSTRING(OH.order_no,1,1)='O'
								AND SUBSTRING(OH.order_no,2,5) IN($branch_employee) AND OH.transaction_type='SB' AND
								DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d')=DATE_FORMAT(NOW(),'%Y%-%m-%d')";
				$res_sale_day=mysqli_query($link,$sql_sale_day) or die(mysqli_error()." Error in select sale for the day: ".$sql_sale_day);
				$row_sale_day=mysqli_fetch_assoc($res_sale_day);
				$sale_day=$row_sale_day['total_sale_qty_day'];	
				$sale_amount_day=$row_sale_day['total_sale_amount_day'];

				$sql_purchase_till_day="SELECT SUM(OD.qty) AS total_purchase_qty_till_day,SUM(OD.qty*OD.sale_rate) AS total_purchase_amount_till_day 
										FROM order_header OH,order_details OD
										WHERE OH.order_no=OD.order_no AND OD.sku_code='".$prod_code."' AND SUBSTRING(OH.order_no,1,1)='O'
										AND SUBSTRING(OH.order_no,2,5) IN($branch_employee) AND OH.transaction_type='PB' AND
										DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d') <=DATE_FORMAT(NOW(),'%Y%-%m-%d') 
										AND DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d') >='".$fiinancial_year."'";
				$res_purchase_till_day=mysqli_query($link,$sql_purchase_till_day) or die(mysqli_error()." 
										Error in select purchase till day: ".$sql_purchase_till_day);
				$row_purchase_till_day=mysqli_fetch_assoc($res_purchase_till_day);
				$purchase_till_day=$row_purchase_till_day['total_purchase_qty_till_day'];
				$purchase_amount_till_day=$row_purchase_till_day['total_purchase_amount_till_day'];
					
				$sql_sale_till_day="SELECT SUM(OD.qty) AS total_sale_qty_till_day,SUM(OD.qty*OD.sale_rate) AS total_sale_amount_till_day 
									FROM order_header OH,order_details OD
									WHERE OH.order_no=OD.order_no AND OD.sku_code='".$prod_code."' AND SUBSTRING(OH.order_no,1,1)='O'
									AND SUBSTRING(OH.order_no,2,5) IN($branch_employee) AND OH.transaction_type='SB' AND
									DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d')<=DATE_FORMAT(NOW(),'%Y%-%m-%d')
									 AND DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d') >='".$fiinancial_year."'";
				$res_sale_till_day=mysqli_query($link,$sql_sale_till_day) or die(mysqli_error()." 
										Error in select sale till day: ".$sql_sale_till_day);
				$row_sale_till_day=mysqli_fetch_assoc($res_sale_till_day);
				$sale_till_day=$row_sale_till_day['total_sale_qty_till_day'];	
				$sale_amount_till_day=$row_sale_till_day['total_sale_amount_till_day'];
				
				//$opening_stk_day=($opening_stk_initial+$purchase_till_day)-$sale_till_day;
				$opening_stk_day=($opening_stk_initial+$purchase_day)-$sale_day;
				${opening_stk_day.$product_group_code}=${opening_stk_day.$product_group_code}+$opening_stk_day;					
				
				/*$sql_purchase_till_prev_month="SELECT SUM(OD.qty) AS total_purchase_qty_till_prev_month,
											SUM(OD.qty*OD.sale_rate) AS total_purchase_amount_till_prev_month 
											FROM order_header OH,order_details OD
											WHERE OH.order_no=OD.order_no AND OD.sku_code='".$prod_code."' AND SUBSTRING(OH.order_no,1,1)='O'
											AND SUBSTRING(OH.order_no,2,5) IN($branch_employee) AND OH.transaction_type='PB' AND
											DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d') <=LAST_DAY(NOW() - interval 1 month )
											 AND DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d')>='".$fiinancial_year."'";*/
				$sql_purchase_till_prev_month="SELECT SUM(OD.qty) AS total_purchase_qty_till_prev_month,
											SUM(OD.qty*OD.sale_rate) AS total_purchase_amount_till_prev_month 
											FROM order_header OH,order_details OD
											WHERE OH.order_no=OD.order_no AND OD.sku_code='".$prod_code."' AND SUBSTRING(OH.order_no,1,1)='O'
											AND SUBSTRING(OH.order_no,2,5) IN($branch_employee) AND OH.transaction_type='PB' AND
											DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d') >LAST_DAY(NOW() - interval 1 month )
											 AND DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d')<=DATE_FORMAT(NOW(),'%Y%-%m-%d')";							 
				$res_purchase_till_prev_month=mysqli_query($link,$sql_purchase_till_prev_month) or die(mysqli_error()." 
										Error in select purchase till prev month: ".$sql_purchase_till_prev_month);
				$row_purchase_till_prev_month=mysqli_fetch_assoc($res_purchase_till_prev_month);
				$purchase_till_prev_month=$row_purchase_till_prev_month['total_purchase_qty_till_prev_month'];
				$purchase_amount_till_prev_month=$row_purchase_till_prev_month['total_purchase_amount_till_prev_month'];	
				
				/*$sql_sale_till_prev_month="SELECT SUM(OD.qty) AS total_sale_qty_till_prev_month,
										SUM(OD.qty*OD.sale_rate) AS total_sale_amount_till_prev_month 
										FROM order_header OH,order_details OD
										WHERE OH.order_no=OD.order_no AND OD.sku_code='".$prod_code."' AND SUBSTRING(OH.order_no,1,1)='O'
										AND SUBSTRING(OH.order_no,2,5) IN($branch_employee) AND OH.transaction_type='SB' AND
										DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d') <=LAST_DAY(NOW() - interval 1 month )
										 AND DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d')>='".$fiinancial_year."'";*/
				$sql_sale_till_prev_month="SELECT SUM(OD.qty) AS total_sale_qty_till_prev_month,
										SUM(OD.qty*OD.sale_rate) AS total_sale_amount_till_prev_month 
										FROM order_header OH,order_details OD
										WHERE OH.order_no=OD.order_no AND OD.sku_code='".$prod_code."' AND SUBSTRING(OH.order_no,1,1)='O'
										AND SUBSTRING(OH.order_no,2,5) IN($branch_employee) AND OH.transaction_type='SB' AND
										DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d') >LAST_DAY(NOW() - interval 1 month )
										 AND DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d')<=DATE_FORMAT(NOW(),'%Y%-%m-%d')";						 
				$res_sale_till_prev_month=mysqli_query($link,$sql_sale_till_prev_month) or die(mysqli_error()." 
										Error in select sale till day: ".$sql_sale_till_prev_month);
				$row_sale_till_prev_month=mysqli_fetch_assoc($res_sale_till_prev_month);
				$sale_till_prev_month=$row_sale_till_prev_month['total_sale_qty_till_prev_month'];
				$sale_amount_till_prev_month=$row_sale_till_prev_month['total_sale_amount_till_prev_month'];	
				
				$opening_stk_month=($opening_stk_initial+$purchase_till_prev_month)-$sale_till_prev_month;
				${opening_stk_month.$product_group_code}=${opening_stk_month.$product_group_code}+$opening_stk_month;
				
				$purchase_qty_day=$purchase_day;
				$purchase_qty_month=$purchase_till_prev_month;
				$purchase_qty_year=$purchase_till_day;
				
				$purchase_amount_day=$purchase_day;
				$purchase_amount_month=$purchase_amount_till_prev_month;
				$purchase_amount_year=$purchase_amount_till_day;

				${purchase_qty_day.$product_group_code}=${purchase_qty_day.$product_group_code}+$purchase_qty_day;
				${purchase_qty_month.$product_group_code}=${purchase_qty_month.$product_group_code}+$purchase_qty_month;
				${purchase_qty_year.$product_group_code}=${purchase_qty_year.$product_group_code}+$purchase_qty_year;
				
				${purchase_amount_day.$product_group_code}=${purchase_amount_day.$product_group_code}+$purchase_amount_day;
				${purchase_amount_month.$product_group_code}=${purchase_amount_month.$product_group_code}+$purchase_amount_month;
				${purchase_amount_year.$product_group_code}=${purchase_amount_year.$product_group_code}+$purchase_amount_year;

				$sale_qty_day=$sale_day;
				$sale_qty_month=$sale_till_prev_month;
				$sale_qty_year=$sale_till_day;
				
				$sale_amount_day=$sale_day;
				$sale_amount_month=$sale_amount_till_prev_month;
				$sale_amount_year=$sale_amount_till_day;

				${sale_qty_day.$product_group_code}=${sale_qty_day.$product_group_code}+$sale_qty_day;
				${sale_qty_month.$product_group_code}=${sale_qty_month.$product_group_code}+$sale_qty_month;
				${sale_qty_year.$product_group_code}=${sale_qty_year.$product_group_code}+$sale_qty_year;
				
				${sale_amount_day.$product_group_code}=${sale_amount_day.$product_group_code}+$sale_amount_day;
				${sale_amount_month.$product_group_code}=${sale_amount_month.$product_group_code}+$sale_amount_month;
				${sale_amount_year.$product_group_code}=${sale_amount_year.$product_group_code}+$sale_amount_year;

				$closing_stock=($opening_stk_initial+$purchase_qty_year)-$sale_qty_year;
				${closing_stock.$product_group_code}=${closing_stock.$product_group_code}+$closing_stock;
				
				$sql_branch_product_stk="SELECT branch_code,product_code FROM branch_product_wise_stock WHERE branch_code='".$branch_code."' AND 
												product_code='".$prod_code."'";
				$res_branch_product_stk=mysqli_query($link,$sql_branch_product_stk) or die(mysqli_error()." 
										Error in select branch product stk: ".$sql_branch_product_stk);	
				$cnt_branch_product_stk=mysqli_num_rows($res_branch_product_stk);
				if($cnt_branch_product_stk<1)
				{											
					$sql_insert_branch_product_stk="INSERT INTO branch_product_wise_stock SET 
													branch_code='".$branch_code."',
													product_code='".$prod_code."',
													opening_stk_day='".$opening_stk_day."',
													opening_stk_month='".$opening_stk_month."',
													opening_stk_year='".$opening_stk_initial."',
													purchase_stk_day='".$purchase_qty_day."',
													purchase_stk_month='".$purchase_qty_month."',
													purchase_stk_year='".$purchase_qty_year."',
													purchase_amount_day='".$purchase_amount_day."',
													purchase_amount_month='".$purchase_amount_month."',
													purchase_amount_year='".$purchase_amount_year."',
													sale_stk_day='".$sale_qty_day."',
													sale_stk_month='".$sale_qty_month."',
													sale_stk_year='".$sale_qty_year."',
													sale_amount_day='".$sale_amount_day."',
													sale_amount_month='".$sale_amount_month."',
													sale_amount_year='".$sale_amount_year."',
													closing_stk='".$closing_stock."'";
					$res_insert_branch_product_stk=mysqli_query($link,$sql_insert_branch_product_stk) or die(mysqli_error()." error
										Insert in branch product stk: ".$sql_insert_branch_product_stk);	
				}
				else
				{
					$sql_update_branch_product_stk="UPDATE branch_product_wise_stock SET 
													opening_stk_day='".$opening_stk_day."',
													opening_stk_month='".$opening_stk_month."',
													opening_stk_year='".$opening_stk_initial."',
													purchase_stk_day='".$purchase_qty_day."',
													purchase_stk_month='".$purchase_qty_month."',
													purchase_stk_year='".$purchase_qty_year."',
													purchase_amount_day='".$purchase_amount_day."',
													purchase_amount_month='".$purchase_amount_month."',
													purchase_amount_year='".$purchase_amount_year."',
													sale_stk_day='".$sale_qty_day."',
													sale_stk_month='".$sale_qty_month."',
													sale_stk_year='".$sale_qty_year."',
													sale_amount_day='".$sale_amount_day."',
													sale_amount_month='".$sale_amount_month."',
													sale_amount_year='".$sale_amount_year."',
													closing_stk='".$closing_stock."' WHERE branch_code='".$branch_code."' AND product_code='".$prod_code."'";
					$res_update_branch_product_stk=mysqli_query($link,$sql_update_branch_product_stk) or die(mysqli_error()." error
										update in branch product stk: ".$sql_update_branch_product_stk);	
				}
			}
			
			$sqlproductgroup="SELECT product_group_code FROM product_group_master 
							WHERE product_group_code IN(SELECT product_group_code FROM product_master)";	
			$resproductgroup=mysqli_query($link,$sqlproductgroup) or die(mysqli_error()." Error in select product group product: ".$sqlproductgroup);
			while($rowproductgroup=mysqli_fetch_assoc($resproductgroup))
			{
				$product_group_code_product=$rowproductgroup['product_group_code'];
				$sql_branch_product_group_stk="SELECT branch_code,product_group_code FROM branch_product_group_wise_stock 
												WHERE branch_code='".$branch_code."' AND product_group_code='".$product_group_code_product."'";
				$res_branch_product_group_stk=mysqli_query($link,$sql_branch_product_group_stk) or die(mysqli_error()." 
										Error in select branch product group stk: ".$sql_branch_product_group_stk);	
				$cnt_branch_product_group_stk=mysqli_num_rows($res_branch_product_group_stk);
				if($cnt_branch_product_group_stk<1)
				{											
				$sql_insert_branch_product_group_stk="INSERT INTO branch_product_group_wise_stock SET 
													branch_code='".$branch_code."',
													product_group_code='".$product_group_code_product."',
													opening_stk_day='".${opening_stk_day.$product_group_code_product}."',
													opening_stk_month='".${opening_stk_month.$product_group_code_product}."',
													opening_stk_year='".${opening_stk_initial.$product_group_code_product}."',
													purchase_stk_day='".${purchase_qty_day.$product_group_code_product}."',
													purchase_stk_month='".${purchase_qty_month.$product_group_code_product}."',
													purchase_stk_year='".${purchase_qty_year.$product_group_code_product}."',
													purchase_amount_day='".${purchase_amount_day.$product_group_code_product}."',
													purchase_amount_month='".${purchase_amount_month.$product_group_code_product}."',
													purchase_amount_year='".${purchase_amount_year.$product_group_code_product}."',
													sale_stk_day='".${sale_qty_day.$product_group_code_product}."',
													sale_stk_month='".${sale_qty_month.$product_group_code_product}."',
													sale_stk_year='".${sale_qty_year.$product_group_code_product}."',
													sale_amount_day='".${sale_amount_day.$product_group_code_product}."',
													sale_amount_month='".${sale_amount_month.$product_group_code_product}."',
													sale_amount_year='".${sale_amount_year.$product_group_code_product}."',
													closing_stk='".${closing_stock.$product_group_code_product}."'";
				$res_insert_branch_product_group_stk=mysqli_query($link,$sql_insert_branch_product_group_stk) or die(mysqli_error()." 
										Insert in branch product group stk: ".$sql_insert_branch_product_group_stk);
				}
				else
				{
					$sql_update_branch_product_group_stk="Update branch_product_group_wise_stock SET 
															opening_stk_day='".${opening_stk_day.$product_group_code_product}."',
															opening_stk_month='".${opening_stk_month.$product_group_code_product}."',
															opening_stk_year='".${opening_stk_initial.$product_group_code_product}."',
															purchase_stk_day='".${purchase_qty_day.$product_group_code_product}."',
															purchase_stk_month='".${purchase_qty_month.$product_group_code_product}."',
															purchase_stk_year='".${purchase_qty_year.$product_group_code_product}."',
															purchase_amount_day='".${purchase_amount_day.$product_group_code_product}."',
															purchase_amount_month='".${purchase_amount_month.$product_group_code_product}."',
															purchase_amount_year='".${purchase_amount_year.$product_group_code_product}."',
															sale_stk_day='".${sale_qty_day.$product_group_code_product}."',
															sale_stk_month='".${sale_qty_month.$product_group_code_product}."',
															sale_stk_year='".${sale_qty_year.$product_group_code_product}."',
															sale_amount_day='".${sale_amount_day.$product_group_code_product}."',
															sale_amount_month='".${sale_amount_month.$product_group_code_product}."',
															sale_amount_year='".${sale_amount_year.$product_group_code_product}."',
															closing_stk='".${closing_stock.$product_group_code_product}."' 
															WHERE branch_code='".$branch_code."' AND product_group_code='".$product_group_code_product."'";
					$res_update_branch_product_group_stk=mysqli_query($link,$sql_update_branch_product_group_stk) or die(mysqli_error()." 
										update in branch product group stk: ".$sql_update_branch_product_group_stk);
				}
			}
		}
	}
				
	mysqli_close($link);
	echo 'SUCCESS';	
?>		