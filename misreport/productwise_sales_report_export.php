<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

if(strtoupper($_SESSION['admin_login']) == "ADMIN"){
	$emp_hierarchy = "";
	$emp_hierarchy_condition = "";
}
else{
	$emp_hierarchy = return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition = " AND SUBSTRING(OD.order_no,2,5) IN (".$emp_hierarchy.") ";
}

$today = date('Ymd');
$admin_login = $_REQUEST['admin_login'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$get_code = $_REQUEST['get_code'];
$listing_type = $_REQUEST['listing_type'];

if($listing_type == 'customerlist'){
	$listing_table = ", order_header OH ";
	$listing_condition = " AND OH.order_no = OD.order_no AND OH.customer_code = '".$get_code."' ";
	
	$sql_customer_name = "SELECT customer_name FROM customer_master WHERE customer_code = '".$get_code."'";
	$res_customer_name = mysqli_query($link,$sql_customer_name);
	$row_customer_name = mysqli_fetch_assoc($res_customer_name);
	$customer_name = $row_customer_name['customer_name'];
	
	$table_header = "<table><tr class='TDHEAD_SUB'><td align='center' width='100%'>$customer_name</td></tr></table><br>";
}
else if($listing_type == 'emplist'){
	if(tagged_distributor_for_order == 'yes'){
		$get_code_split = explode("^",$get_code);
		$get_emp_code = $get_code_split[0];
		$distributor_code = $get_code_split[1];
		$listing_table = ", order_header OH ";
		$listing_condition = " AND OH.order_no = OD.order_no AND OH.tag_distributor_code = '".$distributor_code."' AND SUBSTRING(OH.order_no,2,5) = '".$get_emp_code."' ";
		//print_r($get_code_split);die;
		$sql_emp = "SELECT emp_name FROM employee_master WHERE emp_code = '".$get_emp_code."'";
		$res_emp = mysqli_query($link,$sql_emp);
		$row_emp = mysqli_fetch_assoc($res_emp);
		$emp_name = $row_emp['emp_name'];
		
		$sql_distributor = "SELECT customer_name FROM customer_master WHERE customer_code = '".$distributor_code."'";
		$res_distributor = mysqli_query($link,$sql_distributor);
		$row_distributor = mysqli_fetch_assoc($res_distributor);
		$distributor_name = $row_distributor['customer_name'];
		
		$table_header = "<table><tr class='TDHEAD_SUB'><td align='center' width='100%'>$emp_name -> $distributor_name</td></tr></table><br>";
	}
	else{
		$listing_table = ", order_header OH ";
		$listing_condition = " AND OH.order_no = OD.order_no AND SUBSTRING(OH.order_no,2,5) = '".$get_code."' ";
		
		$sql_emp = "SELECT emp_name FROM employee_master WHERE emp_code = '".$get_code."'";
		$res_emp = mysqli_query($link,$sql_emp);
		$row_emp = mysqli_fetch_assoc($res_emp);
		$emp_name = $row_emp['emp_name'];
		
		$table_header = "<table><tr class='TDHEAD_SUB'><td align='center' width='100%'>$emp_name</td></tr></table><br>";
	}
}
else if($listing_type == 'ditributorlist'){
	$listing_table = ", order_header OH ";
	$listing_condition = " AND OH.order_no = OD.order_no AND OH.tag_distributor_code = '".$get_code."' ";
	
	$sql_distributor = "SELECT customer_name FROM customer_master WHERE customer_code = '".$get_code."'";
	$res_distributor = mysqli_query($link,$sql_distributor);
	$row_distributor = mysqli_fetch_assoc($res_distributor);
	$distributor_name = $row_distributor['customer_name'];
	
	$table_header = "<table><tr class='TDHEAD_SUB'><td align='center' width='100%'>$distributor_name</td></tr></table><br>";
}
else if($listing_type == ''){
	$listing_table = "";
	$listing_condition = "";
}

if($start_date != '' && $end_date != ''){
	$date_condition = " AND (SUBSTRING(OD.order_no,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ";
	
	$table_main_head = "Product Wise Sales - From ".date('d-m-Y',strtotime($start_date))." To ".date('d-m-Y',strtotime($end_date));
}
else{
	$date_condition = " AND SUBSTRING(OD.order_no,-14,8) = '".$today."' ";
	$table_main_head = "Product Wise Sales On - ".date('d-m-Y',strtotime($today));
}

if(no_of_filter == 1){
	
	$header = "<table border=\"1\" style=\"border-collapse:collapse;\">
				<tr>
					<th colspan=\"3\" align=\"center\">".$table_main_head."</th>
				</tr>
				<tr>
					<th>Product</th>
					<th>Quantity</th>
					<th>Amount</th>
				</tr>";
	
	$sql_order_details = "SELECT OD.sku_code, SUM(OD.qty), SUM(OD.amount) FROM order_details OD ".$listing_table."  WHERE OD.order_no LIKE 'O%'".$date_condition.$emp_hierarchy_condition.$listing_condition." GROUP BY OD.sku_code";
	$res_order_details = mysqli_query($link,$sql_order_details);
	while($row_order_details = mysqli_fetch_assoc($res_order_details)){
		$sku_code = $row_order_details['sku_code'];
		$qty = $row_order_details['SUM(OD.qty)'];
		$amount = $row_order_details['SUM(OD.amount)'];
		
		$total_qty += $qty;
		$total_amt += $amount;
				
		$sql_prod_name = "SELECT prod_desc FROM product_master WHERE prod_code = '".$sku_code."'";
		$res_prod_name = mysqli_query($link,$sql_prod_name);
		$row_prod_name = mysqli_fetch_assoc($res_prod_name);
		$prod_desc = $row_prod_name['prod_desc'];
		
		$header .= "<tr>
						<td>".$prod_desc."</td>
						<td align=\"right\">".$qty."</td>
						<td align=\"right\">".number_format($amount,2)."</td>
					</tr>";
	}
	$header .= "<tr>
					<td>TOTAL</td>
					<th align=\"right\">".$total_qty."</th>
					<th align=\"right\">".number_format($total_amt,2)."</th>
				</tr>
				</table>";
}
else if(no_of_filter == 2){
	$product_group_array = array();
		
	$header = "<table border=\"1\" style=\"border-collapse:collapse;\">
				<tr>
					<th colspan=\"4\" align=\"center\">".$table_main_head."</th>
				</tr>
				<tr>
					<th>Product Group</th>
					<th>Product</th>
					<th>Quantity</th>
					<th>Amount</th>
				</tr>";
	
	$sql_order_details = "SELECT PGM.product_group_code, PGM.product_group_name, PM.prod_desc, OD.sku_code, SUM(OD.qty), SUM(OD.amount) FROM order_details OD, product_group_master PGM, product_master PM ".$listing_table." WHERE OD.order_no LIKE 'O%'".$date_condition.$emp_hierarchy_condition.$listing_condition." AND PM.prod_code = OD.sku_code AND PM.product_group_code = PGM.product_group_code GROUP BY OD.sku_code ORDER BY PGM.product_group_code";
	$res_order_details = mysqli_query($link,$sql_order_details);
	while($row_order_details = mysqli_fetch_assoc($res_order_details)){
		$product_group_code = $row_order_details['product_group_code'];
		$product_group_name = $row_order_details['product_group_name'];
		$sku_code = $row_order_details['sku_code'];
		$prod_desc = $row_order_details['prod_desc'];
		$qty = $row_order_details['SUM(OD.qty)'];
		$amount = $row_order_details['SUM(OD.amount)'];
				
		if(!in_array($product_group_code,$product_group_array)){
			array_push($product_group_array,$product_group_code);
			if(count($product_group_array) == 1){
			}
			else{
				if(count($product_group_array) == 2){
					$header .= "<tr>
									<th colspan=\"2\" align=\"center\">GROUP TOTAL</th>
									<th align=\"right\">".$total_qty."</th>
									<th align=\"right\">".number_format($total_amt,2)."</th>
								</tr>";
					
					$total_qty = '';
					$total_amt = '';
				}
				else{
					$header .= "<tr>
									<th colspan=\"2\" align=\"center\">GROUP TOTAL</th>
									<th align=\"right\">".$total_qty."</th>
									<th align=\"right\">".number_format($total_amt,2)."</th>
								</tr>";
				
					$total_qty = '';
					$total_amt = '';
				}
			}
		}
		
		$total_qty += $qty;
		$total_amt += $amount;
		
		$grand_qty_total += $qty;
		$grand_amt_total += $amount;
		
		$header .= "<tr>
						<td>".$product_group_name."</td>
						<td>".$prod_desc."</td>
						<td align=\"right\">".$qty."</td>
						<td align=\"right\">".number_format($amount,2)."</td>
					</tr>";
	}
	$header .= "<tr>
					<th colspan=\"2\" align=\"center\">GROUP TOTAL</th>
					<th align=\"right\">".$total_qty."</th>
					<th align=\"right\">".number_format($total_amt,2)."</th>
				</tr>";
	$header .= "<tr>
					<th colspan=\"2\" align=\"center\">GRAND TOTAL</th>
					<th align=\"right\">".$grand_qty_total."</th>
					<th align=\"right\">".number_format($grand_amt_total,2)."</th>
				</tr>
				</table>";
}
else if(no_of_filter == 3){
	$array_count = '';
	$product_group_array = array();
	
	$header = "<table border=\"1\" style=\"border-collapse:collapse;\">
				<tr>
					<th colspan=\"5\" align=\"center\">".$table_main_head."</th>
				</tr>
				<tr>
					<th>Product Group</th>
					<th>Product Sub Group</th>
					<th>Product</th>
					<th>Quantity</th>
					<th>Amount</th>
				</tr>";
	
	$sql_order_details = "SELECT PGM.product_group_code, PGM.product_group_name, PSGM.product_sub_group_name, PM.prod_desc, OD.sku_code, SUM(OD.qty), SUM(OD.amount) FROM order_details OD, product_group_master PGM, product_sub_group_master PSGM, product_master PM ".$listing_table." WHERE OD.order_no LIKE 'O%'".$date_condition.$emp_hierarchy_condition." AND PM.prod_code = OD.sku_code AND PM.product_group_code = PGM.product_group_code AND PM.product_sub_group_code = PSGM.product_sub_group_code ".$listing_condition." GROUP BY OD.sku_code ORDER BY PGM.product_group_code";
	$res_order_details = mysqli_query($link,$sql_order_details);
	while($row_order_details = mysqli_fetch_assoc($res_order_details)){
		$product_group_code = $row_order_details['product_group_code'];
		$product_group_name = $row_order_details['product_group_name'];
		$product_sub_group_name = $row_order_details['product_sub_group_name'];
		$prod_desc = $row_order_details['prod_desc'];
		$sku_code = $row_order_details['sku_code'];
		$qty = $row_order_details['SUM(OD.qty)'];
		$amount = $row_order_details['SUM(OD.amount)'];
				
		if(!in_array($product_group_code,$product_group_array)){
			array_push($product_group_array,$product_group_code);
			if(count($product_group_array) == 1){
			}
			else{
				if(count($product_group_array) == 2){
					$header .= "<tr>
									<th colspan=\"3\" align=\"center\">GROUP TOTAL</th>
									<th align=\"right\">".$total_qty."</th>
									<th align=\"right\">".number_format($total_amt,2)."</th>
								</tr>";
					$total_qty = '';
					$total_amt = '';
				}
				else{
					$header .= "<tr>
									<th colspan=\"3\" align=\"center\">GROUP TOTAL</th>
									<th align=\"right\">".$total_qty."</th>
									<th align=\"right\">".number_format($total_amt,2)."</th>
								</tr>";
					$total_qty = '';
					$total_amt = '';
				}
			}
		}
		
		$total_qty += $qty;
		$total_amt += $amount;
		
		$grand_qty_total += $qty;
		$grand_amt_total += $amount;
				
		$header .= "<tr>
						<td>".$product_group_name."</td>
						<td>".$product_sub_group_name."</td>
						<td>".$prod_desc."</td>
						<td align=\"right\">".$qty."</td>
						<td align=\"right\">".number_format($amount,2)."</td>
					</tr>";
	}
		
	$header .= "<tr>
					<th colspan=\"3\" align=\"center\">GROUP TOTAL</th>
					<th align=\"right\">".$total_qty."</th>
					<th align=\"right\">".number_format($total_amt,2)."</th>
				</tr>";
	$header .= "<tr>
					<th colspan=\"3\" align=\"center\">GRAND TOTAL</th>
					<th align=\"right\">".$grand_qty_total."</th>
					<th align=\"right\">".number_format($grand_amt_total,2)."</th>
				</tr></table>";
}
else if(no_of_filter == 4){
	$product_group_array = array();
	$header = "<table border=\"1\" style=\"border-collapse:collapse;\">
				<tr>
					<th colspan=\"6\" align=\"center\">".$table_main_head."</th>
				</tr>
				<tr>
					<th>Product Group</th>
					<th>Product Sub Group</th>
					<th>Brand Name</th>
					<th>Product</th>
					<th>Quantity</th>
					<th>Amount</th>
				</tr>";
		
	$sql_order_details = "SELECT PGM.product_group_code, PGM.product_group_name, PSGM.product_sub_group_name, PBM.product_brand_name, PM.prod_desc, OD.sku_code, SUM(OD.qty), SUM(OD.amount) FROM order_details OD, product_group_master PGM, product_sub_group_master PSGM, product_brand_master PBM, product_master PM ".$listing_table." WHERE OD.order_no LIKE 'O%'".$date_condition.$emp_hierarchy_condition.$listing_condition." AND PM.prod_code = OD.sku_code AND PM.product_group_code = PGM.product_group_code AND PM.product_sub_group_code = PSGM.product_sub_group_code AND PM.product_brand_code = PBM.product_brand_code GROUP BY OD.sku_code ORDER BY PGM.product_group_code";
	$res_order_details = mysqli_query($link,$sql_order_details);
	while($row_order_details = mysqli_fetch_assoc($res_order_details)){
		$product_group_code = $row_order_details['product_group_code'];
		$product_group_name = $row_order_details['product_group_name'];
		$product_sub_group_name = $row_order_details['product_sub_group_name'];
		$product_brand_name = $row_order_details['product_brand_name'];
		$prod_desc = $row_order_details['prod_desc'];
		$sku_code = $row_order_details['sku_code'];
		$qty = $row_order_details['SUM(OD.qty)'];
		$amount = $row_order_details['SUM(OD.amount)'];
		
		if(!in_array($product_group_code,$product_group_array)){
			array_push($product_group_array,$product_group_code);
			if(count($product_group_array) == 1){
			}
			else{
				if(count($product_group_array) == 2){
					$header .= "<tr>
								<th colspan=\"4\" align=\"center\">GROUP TOTAL</th>
								<th align=\"right\">".$total_qty."</th>
								<th align=\"right\">".number_format($total_amt,2)."</th>
							</tr>";
					$total_qty = '';
					$total_amt = '';
				}
				else{
					$header .= "<tr>
								<th colspan=\"4\" align=\"center\">GROUP TOTAL</th>
								<th align=\"right\">".$total_qty."</th>
								<th align=\"right\">".number_format($total_amt,2)."</th>
							</tr>";
					$total_qty = '';
					$total_amt = '';
				}
			}
		}
		
		$total_qty += $qty;
		$total_amt += $amount;
		
		$grand_qty_total += $qty;
		$grand_amt_total += $amount;
				
		$content .= $product_group_name."\t".$product_sub_group_name."\t".$product_brand_name."\t".$prod_desc."\t".$qty."\t".number_format($amount,2)."\n";
		
		$header .= "<tr>
						<td>".$product_group_name."</td>
						<td>".$product_sub_group_name."</td>
						<td>".$product_brand_name."</td>
						<td>".$prod_desc."</td>
						<td align=\"right\">".$qty."</td>
						<td align=\"right\">".number_format($amount,2)."</td>
					</tr>";
	}
	
	$header .= "<tr>
					<th colspan=\"4\" align=\"center\">GROUP TOTAL</th>
					<th align=\"right\">".$total_qty."</th>
					<th align=\"right\">".number_format($total_amt,2)."</th>
				</tr>";
	$header .= "<tr>
					<th colspan=\"4\" align=\"center\">GRAND TOTAL</th>
					<th align=\"right\">".$grand_qty_total."</th>
					<th align=\"right\">".number_format($grand_amt_total,2)."</th>
				</tr></table>";				
}

header("Content-type: application/octet-stream"); 
header("Content-Disposition: attachment; filename=Productwisesales_Report.xls"); 
header("Pragma: no-cache"); 
header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 
//echo ucwords($header)."\n".$content; //- See more at: http://www.discussdesk.com/download-mysql-data-into-excel-file-in-php.htm#sthash.5bPI72JI.dpuf
echo $table_header.$header;

mysqli_close($link);
?>