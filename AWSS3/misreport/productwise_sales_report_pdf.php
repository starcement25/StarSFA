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
	$res_customer_name = mysql_query($sql_customer_name);
	$row_customer_name = mysql_fetch_array($res_customer_name);
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
		$res_emp = mysql_query($sql_emp);
		$row_emp = mysql_fetch_array($res_emp);
		$emp_name = $row_emp['emp_name'];
		
		$sql_distributor = "SELECT customer_name FROM customer_master WHERE customer_code = '".$distributor_code."'";
		$res_distributor = mysql_query($sql_distributor);
		$row_distributor = mysql_fetch_array($res_distributor);
		$distributor_name = $row_distributor['customer_name'];
		
		$table_header = "<table><tr class='TDHEAD_SUB'><td align='center' width='100%'>$emp_name -> $distributor_name</td></tr></table><br>";
	}
	else{
		$listing_table = ", order_header OH ";
		$listing_condition = " AND OH.order_no = OD.order_no AND SUBSTRING(OH.order_no,2,5) = '".$get_code."' ";
		
		$sql_emp = "SELECT emp_name FROM employee_master WHERE emp_code = '".$get_code."'";
		$res_emp = mysql_query($sql_emp);
		$row_emp = mysql_fetch_array($res_emp);
		$emp_name = $row_emp['emp_name'];
		
		$table_header = "<table><tr class='TDHEAD_SUB'><td align='center' width='100%'>$emp_name</td></tr></table><br>";
	}
}
else if($listing_type == 'ditributorlist'){
	$listing_table = ", order_header OH ";
	$listing_condition = " AND OH.order_no = OD.order_no AND OH.tag_distributor_code = '".$get_code."' ";
	
	$sql_distributor = "SELECT customer_name FROM customer_master WHERE customer_code = '".$get_code."'";
	$res_distributor = mysql_query($sql_distributor);
	$row_distributor = mysql_fetch_array($res_distributor);
	$distributor_name = $row_distributor['customer_name'];
	
	$table_header = "<table><tr class='TDHEAD_SUB'><td align='center' width='100%'>$distributor_name</td></tr></table><br>";
}
else if($listing_type == ''){
	$listing_table = "";
	$listing_condition = "";
	$table_header = '';
}

if($start_date != '' && $end_date != '')
	$date_condition = " AND (SUBSTRING(OD.order_no,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ";
else
	$date_condition = " AND SUBSTRING(OD.order_no,-14,8) = '".$today."' ";

if(no_of_filter == 1){
	
	$table = "<table border=\"1\" style=\"border-collapse:collapse;\">
				<tr align=\"center\">
					<th>Product</th>
					<th>Quantity</th>
					<th>Amount</th>
				</tr>";
	
	$sql_order_details = "SELECT OD.sku_code, SUM(OD.qty), SUM(OD.amount) FROM order_details OD ".$listing_table." WHERE OD.order_no LIKE 'O%'".$date_condition.$emp_hierarchy_condition.$listing_condition." GROUP BY OD.sku_code";
	$res_order_details = mysql_query($sql_order_details);
	while($row_order_details = mysql_fetch_array($res_order_details)){
		$sku_code = $row_order_details['sku_code'];
		$qty = $row_order_details['SUM(OD.qty)'];
		$amount = $row_order_details['SUM(OD.amount)'];
		$amount = number_format($amount,2);
		
		$sql_prod_name = "SELECT prod_desc FROM product_master WHERE prod_code = '".$sku_code."'";
		$res_prod_name = mysql_query($sql_prod_name);
		$row_prod_name = mysql_fetch_array($res_prod_name);
		$prod_desc = $row_prod_name['prod_desc'];
		
		$table .= "<tr>
					<td>".$prod_desc."</td>
					<td>".$qty."</td>
					<td>".$amount."</td>
				</tr>";
	}
	$table .= "</table>";
}
else if(no_of_filter == 2){
	
	$table = "<table border=\"1\" style=\"border-collapse:collapse;\">
				<tr align=\"center\">
					<th>Product Group</th>
					<th>Product</th>
					<th>Quantity</th>
					<th>Amount</th>
				</tr>";
	
	$sql_order_details = "SELECT OD.sku_code, SUM(OD.qty), SUM(OD.amount) FROM order_details OD ".$listing_table." WHERE OD.order_no LIKE 'O%'".$date_condition.$emp_hierarchy_condition.$listing_condition." GROUP BY OD.sku_code";
	$res_order_details = mysql_query($sql_order_details);
	while($row_order_details = mysql_fetch_array($res_order_details)){
		$sku_code = $row_order_details['sku_code'];
		$qty = $row_order_details['SUM(OD.qty)'];
		$amount = $row_order_details['SUM(OD.amount)'];
		$amount = number_format($amount,2);
		
		$sql_prod_master = "SELECT product_group_code FROM product_master WHERE prod_code = '".$sku_code."'";
		$res_prod_master = mysql_query($sql_prod_master);
		$row_prod_master = mysql_fetch_array($res_prod_master);
		$product_group_code = $row_prod_master['product_group_code'];
		
		$sql_prodgroup_name = "SELECT product_group_name FROM product_group_master WHERE product_group_code = '".$product_group_code."'";
		$res_prodgroup_name = mysql_query($sql_prodgroup_name);
		$row_prodgroup_name = mysql_fetch_array($res_prodgroup_name);
		$prodgroupname = $row_prodgroup_name['product_group_name'];
		
		$sql_prod_name = "SELECT prod_desc FROM product_master WHERE prod_code = '".$sku_code."'";
		$res_prod_name = mysql_query($sql_prod_name);
		$row_prod_name = mysql_fetch_array($res_prod_name);
		$prod_desc = $row_prod_name['prod_desc'];
		
		$content .= $prodgroupname."\t".$prod_desc."\t".$qty."\t".$amount."\n";
		$table .= "<tr>
					<td>".$prodgroupname."</td>
					<td>".$prod_desc."</td>
					<td>".$qty."</td>
					<td>".$amount."</td>
				</tr>";
	}
	$table .= "</table>";
}
else if(no_of_filter == 3){
	$table = "<table border=\"1\" style=\"border-collapse:collapse;\">
				<tr align=\"center\">
					<th>Product Group</th>
					<th>Product Sub Group</th>
					<th>Product</th>
					<th>Quantity</th>
					<th>Amount</th>
				</tr>";
	
	$sql_order_details = "SELECT OD.sku_code, SUM(OD.qty), SUM(OD.amount) FROM order_details OD ".$listing_table." WHERE OD.order_no LIKE 'O%'".$date_condition.$emp_hierarchy_condition.$listing_condition." GROUP BY OD.sku_code";
	$res_order_details = mysql_query($sql_order_details);
	while($row_order_details = mysql_fetch_array($res_order_details)){
		$sku_code = $row_order_details['sku_code'];
		$qty = $row_order_details['SUM(OD.qty)'];
		$amount = $row_order_details['SUM(OD.amount)'];
		$amount = number_format($amount,2);
		
		$sql_prod_master = "SELECT product_group_code, product_sub_group_code FROM product_master WHERE prod_code = '".$sku_code."'";
		$res_prod_master = mysql_query($sql_prod_master);
		$row_prod_master = mysql_fetch_array($res_prod_master);
		$product_group_code = $row_prod_master['product_group_code'];
		$product_sub_group_code = $row_prod_master['product_sub_group_code'];
		
		$sql_prodgroup_name = "SELECT product_group_name FROM product_group_master WHERE product_group_code = '".$product_group_code."'";
		$res_prodgroup_name = mysql_query($sql_prodgroup_name);
		$row_prodgroup_name = mysql_fetch_array($res_prodgroup_name);
		$prodgroupname = $row_prodgroup_name['product_group_name'];
		
		$sql_subprodgroup_name = "SELECT product_sub_group_name FROM product_sub_group_master WHERE product_sub_group_code = '".$product_sub_group_code."'";
		$res_subprodgroup_name = mysql_query($sql_subprodgroup_name);
		$row_subprodgroup_name = mysql_fetch_array($res_subprodgroup_name);
		$prodsubgroupname = $row_subprodgroup_name['product_sub_group_name'];
		
		$sql_prod_name = "SELECT prod_desc FROM product_master WHERE prod_code = '".$sku_code."'";
		$res_prod_name = mysql_query($sql_prod_name);
		$row_prod_name = mysql_fetch_array($res_prod_name);
		$prod_desc = $row_prod_name['prod_desc'];
		
		$table .= "<tr>
					<td>".$prodgroupname."</td>
					<td>".$prodsubgroupname."</td>
					<td>".$prod_desc."</td>
					<td>".$qty."</td>
					<td>".$amount."</td>
				</tr>";
	}	
	$table .= "</table>";
}
else if(no_of_filter == 4){
	
	$table = "<table border=\"1\" style=\"border-collapse:collapse;\">
				<tr align=\"center\">
					<th>Product Group</th>
					<th>Product Sub Group</th>
					<th>Brand Name</th>
					<th>Product</th>
					<th>Quantity</th>
					<th>Amount</th>
				</tr>";
	
	$sql_order_details = "SELECT OD.sku_code, SUM(OD.qty), SUM(OD.amount) FROM order_details OD ".$listing_table."  WHERE OD.order_no LIKE 'O%'".$date_condition.$emp_hierarchy_condition.$listing_condition." GROUP BY OD.sku_code";
	$res_order_details = mysql_query($sql_order_details);
	while($row_order_details = mysql_fetch_array($res_order_details)){
		$sku_code = $row_order_details['sku_code'];
		$qty = $row_order_details['SUM(OD.qty)'];
		$amount = $row_order_details['SUM(OD.amount)'];
		$amount = number_format($amount,2);
		
		$sql_prod_master = "SELECT product_group_code, product_sub_group_code, product_brand_code FROM product_master WHERE prod_code = '".$sku_code."'";
		$res_prod_master = mysql_query($sql_prod_master);
		$row_prod_master = mysql_fetch_array($res_prod_master);
		$product_group_code = $row_prod_master['product_group_code'];
		$product_sub_group_code = $row_prod_master['product_sub_group_code'];
		$product_brand_code = $row_prod_master['product_brand_code'];
		
		$sql_prodgroup_name = "SELECT product_group_name FROM product_group_master WHERE product_group_code = '".$product_group_code."'";
		$res_prodgroup_name = mysql_query($sql_prodgroup_name);
		$row_prodgroup_name = mysql_fetch_array($res_prodgroup_name);
		$prodgroupname = $row_prodgroup_name['product_group_name'];
		
		$sql_subprodgroup_name = "SELECT product_sub_group_name FROM product_sub_group_master WHERE product_sub_group_code = '".$product_sub_group_code."'";
		$res_subprodgroup_name = mysql_query($sql_subprodgroup_name);
		$row_subprodgroup_name = mysql_fetch_array($res_subprodgroup_name);
		$prodsubgroupname = $row_subprodgroup_name['product_sub_group_name'];
		
		$sql_prodbrand_name = "SELECT product_brand_name FROM product_brand_master WHERE product_brand_code = '".$product_brand_code."'";
		$res_prodbrand_name = mysql_query($sql_prodbrand_name);
		$row_prodbrand_name = mysql_fetch_array($res_prodbrand_name);
		$product_brand_name = $row_prodbrand_name['product_brand_name'];
		
		$sql_prod_name = "SELECT prod_desc FROM product_master WHERE prod_code = '".$sku_code."'";
		$res_prod_name = mysql_query($sql_prod_name);
		$row_prod_name = mysql_fetch_array($res_prod_name);
		$prod_desc = $row_prod_name['prod_desc'];
		
		$table .= "<tr>
					<td>".$prodgroupname."</td>
					<td>".$prodsubgroupname."</td>
					<td>".$product_brand_name."</td>
					<td>".$prod_desc."</td>
					<td>".$qty."</td>
					<td>".$amount."</td>
				</tr>";
	}
	$table .= "</table>";
}

echo $table_header.$table;

mysql_close($link);
?>