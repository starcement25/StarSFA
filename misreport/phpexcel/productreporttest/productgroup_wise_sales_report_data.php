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

if(no_of_filter == 2)
	$page_href = "product_wise_sales_report_data.php";
else if(no_of_filter == 3 || no_of_filter == 4)
	$page_href = "productsubgroup_wise_sales_report_data.php";

$today = date('Ymd');
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
if($start_date != '' && $end_date != '')
	$date_condition = " AND (SUBSTRING(OD.order_no,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ";
else
	$date_condition = " AND SUBSTRING(OD.order_no,-14,8) = '".$today."' ";
	
$sql_order_header = "SELECT PM.product_group_code, SUM(OD.qty), SUM(OD.amount) FROM order_details OD, product_master PM WHERE order_no LIKE 'O%' ".$date_condition.$emp_hierarchy_condition." AND PM.prod_code = OD.sku_code GROUP BY PM.product_group_code";
$res_order_header = mysql_query($sql_order_header);
$total_row_check = mysql_num_rows($res_order_header);
$count = 1;
if($total_row_check>0){
	?>
     <table class="border" width="100%" border="1" style="border-collapse:collapse;" cellpadding="4px">
      <tr class="TDHEAD">
      	<td>SL</td>
      	<td>Product Group Name</td>
        <td>Quantity</td>
        <td>Amount</td>
      </tr>
    <?
	$res_order_header = mysql_query($sql_order_header);
	while($row_order_header = mysql_fetch_array($res_order_header)){
		$prod_group_code = $row_order_header['product_group_code'];
		$qty = $row_order_header['SUM(OD.qty)'];
		$amt = $row_order_header['SUM(OD.amount)'];
		
		$sql_prod_groupname = "SELECT product_group_name FROM product_group_master WHERE product_group_code = '".$prod_group_code."'";
		$res_prod_groupname = mysql_query($sql_prod_groupname);
		$row_prod_groupname = mysql_fetch_array($res_prod_groupname);
		$prod_group_name = $row_prod_groupname['product_group_name'];
		
		echo "<tr>
				<td>".$count."</td>
				<td><a href=\"#\" style=\"color:blue;\" onclick=\"show_next_level('".$page_href."','".$prod_group_code."','".$start_date."','".$end_date."','prod_group');\">".$prod_group_name."</a></td>
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