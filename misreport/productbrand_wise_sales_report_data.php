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

function header_value($column_name,$tabl_name,$compare_col,$compare_value){
	$sql = "SELECT $column_name FROM $tabl_name WHERE $compare_col = '".$compare_value."'";
	$res = mysqli_query($link,$sql);
	$row = mysqli_fetch_assoc($res);
	$header_value = $row[$column_name];
	return $header_value;
}

$page_href = "product_wise_sales_report_data.php";
	
$today = date('Ymd');
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$prod_sub_group_code = $_REQUEST['prod_sub_group_code'];
$type = $_REQUEST['type'];
$get_code = $_REQUEST['get_code'];
$listing_type = $_REQUEST['listing_type'];

if($type == 'prod_sub_group')
	$header_value = header_value('product_sub_group_name','product_sub_group_master','product_sub_group_code',$prod_sub_group_code);

if($start_date != '' && $end_date != '')
	$date_condition = " AND (SUBSTRING(OD.order_no,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ";
else
	$date_condition = " AND SUBSTRING(OD.order_no,-14,8) = '".$today."' ";
	
if($listing_type == 'customerlist'){
	$listing_table = ", order_header OH ";
	$listing_condition = " AND OH.order_no = OD.order_no AND OH.customer_code = '".$get_code."' ";
}
else if($listing_type == 'emplist'){
	if(tagged_distributor_for_order == 'yes'){
		$get_code_split = explode("^",$get_code);
		$get_emp_code = $get_code_split[0];
		$distributor_code = $get_code_split[1];
		$listing_table = ", order_header OH ";
		$listing_condition = " AND OH.order_no = OD.order_no AND OH.tag_distributor_code = '".$distributor_code."' AND SUBSTRING(OH.order_no,2,5) = '".$get_emp_code."' ";
		//print_r($get_code_split);die;
	}
	else{
		$listing_table = ", order_header OH ";
		$listing_condition = " AND OH.order_no = OD.order_no AND SUBSTRING(OH.order_no,2,5) = '".$get_code."' ";
	}
}
else if($listing_type == 'ditributorlist'){
	$listing_table = ", order_header OH ";
	$listing_condition = " AND OH.order_no = OD.order_no AND (OH.tag_distributor_code = '".$get_code."' OR OH.customer_code = '".$get_code."' ) ";
}
else if($listing_type == ''){
	$listing_table = "";
	$listing_condition = "";
}
	
$sql_order_header = "SELECT PM.product_brand_code, SUM(OD.qty), SUM(OD.amount) FROM order_details OD, product_master PM ".$listing_table." WHERE OD.order_no LIKE 'O%' ".$date_condition.$emp_hierarchy_condition.$listing_condition." AND PM.prod_code = OD.sku_code AND PM.product_sub_group_code = '".$prod_sub_group_code."' GROUP BY PM.product_brand_code";
$res_order_header = mysqli_query($link,$sql_order_header);
$total_row_check = mysqli_num_rows($res_order_header);
$count = 1;
if($total_row_check>0){
	if($listing_type == 'customerlist'){
		$sql_customer_name = "SELECT customer_name FROM customer_master WHERE customer_code = '".$get_code."'";
		$res_customer_name = mysqli_query($link,$sql_customer_name);
		$row_customer_name = mysqli_fetch_assoc($res_customer_name);
		$customer_name = $row_customer_name['customer_name'];
		
		echo "<table><tr class='TDHEAD_SUB'><td align='center' width='100%'>$customer_name</td></tr></table><br>";
	}
	else if($listing_type == 'emplist'){
		if(tagged_distributor_for_order == 'yes'){
			$sql_emp = "SELECT emp_name FROM employee_master WHERE emp_code = '".$get_emp_code."'";
			$res_emp = mysqli_query($link,$sql_emp);
			$row_emp = mysqli_fetch_assoc($res_emp);
			$emp_name = $row_emp['emp_name'];
			
			$sql_distributor = "SELECT customer_name FROM customer_master WHERE customer_code = '".$distributor_code."'";
			$res_distributor = mysqli_query($link,$sql_distributor);
			$row_distributor = mysqli_fetch_assoc($res_distributor);
			$distributor_name = $row_distributor['customer_name'];
		
			echo "<table><tr class='TDHEAD_SUB'><td align='center' width='100%'>$emp_name -> $distributor_name</td></tr></table><br>";
		}
		else{
			$sql_emp = "SELECT emp_name FROM employee_master WHERE emp_code = '".$get_code."'";
			$res_emp = mysqli_query($link,$sql_emp);
			$row_emp = mysqli_fetch_assoc($res_emp);
			$emp_name = $row_emp['emp_name'];
			
			echo "<table><tr class='TDHEAD_SUB'><td align='center' width='100%'>$emp_name</td></tr></table><br>";
		}
	}
	else if($listing_type == 'ditributorlist'){
		$sql_distributor = "SELECT customer_name FROM customer_master WHERE customer_code = '".$get_code."'";
		$res_distributor = mysqli_query($link,$sql_distributor);
		$row_distributor = mysqli_fetch_assoc($res_distributor);
		$distributor_name = $row_distributor['customer_name'];
				
		echo "<table><tr class='TDHEAD_SUB'><td align='center' width='100%'>$distributor_name</td></tr></table><br>";
	}
	?>
     <table class="border" width="100%" border="1" style="border-collapse:collapse;" cellpadding="4px">
       <tr>
      	<td class="TDHEAD" align="center" colspan="4"><?php echo $header_value; ?></td>
      </tr>
      <tr class="TDHEAD">
      	<td>SL</td>
      	<td>Product Brand Name</td>
        <td>Quantity</td>
        <td>Amount</td>
      </tr>
    <?
	$res_order_header = mysqli_query($link,$sql_order_header);
	while($row_order_header = mysqli_fetch_assoc($res_order_header)){
		$prod_brand_code = $row_order_header['product_brand_code'];
		$qty = $row_order_header['SUM(OD.qty)'];
		$amt = $row_order_header['SUM(OD.amount)'];
		
		$sql_prod_brandname = "SELECT product_brand_name FROM product_brand_master WHERE product_brand_code = '".$prod_brand_code."'";
		$res_prod_brandname = mysqli_query($link,$sql_prod_brandname);
		$row_prod_brandname = mysqli_fetch_assoc($res_prod_brandname);
		$prod_brandname = $row_prod_brandname['product_brand_name'];
		
		echo "<tr>
				<td>".$count."</td>
				<td><a href=\"#\" style=\"color:blue;\" onclick=\"show_sub_sub_next_level('".$page_href."','".$prod_brand_code."','".$start_date."','".$end_date."','prod_brand');\">".$prod_brandname."</a></td>
				<td align=\"right\">".$qty."</td>
				<td align=\"right\">".number_format($amt,2)."</td>
			  </tr>";
			  
		$total_qty += $qty;
		$total_amt += $amt;
		
		$count++;
	}
	?>
      <tr class="TDHEAD_SUB">
      	<td colspan="2">Total</td>
        <td align="right"><?php echo $total_qty; ?></td>
        <td align="right"><?php echo number_format($total_amt,2); ?></td>
      </tr>
    </table>
    <?php
}
else{
	echo "<div style=\"color:red;\"><b>No Records Available</b></div>";
}
mysqli_close($link);
?>