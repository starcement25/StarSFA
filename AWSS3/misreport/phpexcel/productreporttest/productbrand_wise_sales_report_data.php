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
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	$header_value = $row[$column_name];
	return $header_value;
}

$page_href = "product_wise_sales_report_data.php";
	
$today = date('Ymd');
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$prod_sub_group_code = $_REQUEST['prod_sub_group_code'];
$type = $_REQUEST['type'];

if($type == 'prod_sub_group')
	$header_value = header_value('product_sub_group_name','product_sub_group_master','product_sub_group_code',$prod_sub_group_code);

if($start_date != '' && $end_date != '')
	$date_condition = " AND (SUBSTRING(OD.order_no,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ";
else
	$date_condition = " AND SUBSTRING(OD.order_no,-14,8) = '".$today."' ";
	
$sql_order_header = "SELECT PM.product_brand_code, SUM(OD.qty), SUM(OD.amount) FROM order_details OD, product_master PM WHERE order_no LIKE 'O%' ".$date_condition.$emp_hierarchy_condition." AND PM.prod_code = OD.sku_code AND PM.product_sub_group_code = '".$prod_sub_group_code."' GROUP BY PM.product_brand_code";
$res_order_header = mysql_query($sql_order_header);
$total_row_check = mysql_num_rows($res_order_header);
$count = 1;
if($total_row_check>0){
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
	$res_order_header = mysql_query($sql_order_header);
	while($row_order_header = mysql_fetch_array($res_order_header)){
		$prod_brand_code = $row_order_header['product_brand_code'];
		$qty = $row_order_header['SUM(OD.qty)'];
		$amt = $row_order_header['SUM(OD.amount)'];
		
		$sql_prod_brandname = "SELECT product_brand_name FROM product_brand_master WHERE product_brand_code = '".$prod_brand_code."'";
		$res_prod_brandname = mysql_query($sql_prod_brandname);
		$row_prod_brandname = mysql_fetch_array($res_prod_brandname);
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