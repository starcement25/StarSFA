<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$branch_code = $_REQUEST['branch_code'];

/*----> SELECT PRODUCT FILTER <----*/
$sql_product_filter = "SELECT no_of_filter FROM acedns_acednsproduct.product_details WHERE nick_name = '".strtoupper($_SESSION['nick_name'])."'";
$res_product_filter = mysql_query($sql_product_filter);
$row_product_filter = mysql_fetch_array($res_product_filter);
$product_filter = $row_product_filter['no_of_filter'];

/*----> CONDITIONS ACCORDING TO PRODUCT FILTER <----*/
if($product_filter == 1){
	$td_struct = "<td>Product</td>";
	$product_master_query_string = " PM.prod_desc ";
	$table_name = " product_master PM ";
	$condition = " PM.prod_code = '".$sku_code."' ";
}
else if($product_filter == 2){
	$td_struct = "<td>Product Group</td><td>Product</td>";
	$product_master_query_string = " PGM.product_group_name, PM.prod_desc ";
	$table_name = " product_group_master PGM, product_master PM ";
	$condition = " PGM.product_group_name = PM.product_group_name ";
}
else if($product_filter == 3){
	$td_struct = "<td>Product Group</td><td>Product Sub Group</td><td>Product</td>";
	$product_master_query_string = " PGM.product_group_name, PSGM.product_sub_group_name, PM.prod_desc ";
	$table_name = " product_group_master PGM, product_sub_group_master PSGM, product_master PM ";
	$condition = " PGM.product_group_code = PM.product_group_code AND PSGM.product_sub_group_code = PM.product_sub_group_code";
}
else if($product_filter == 4){
}

$order_no_array = array();
$order_no_array_one = array();

/*----> CHECKS IF THERE IS ANY ORDER/GETS ORDER NO FROM order_header <----*/
$sql_order_exist_check = "SELECT OH.order_no, EM.emp_code, EM.emp_name, OH.status FROM order_header OH, employee_master EM WHERE SUBSTRING(OH.order_no,2,5) = EM.emp_code AND OH.order_no LIKE 'O%' AND OH.status IN('pending','') AND EM.HQ = (SELECT branch_name FROM branch_master WHERE branch_code = '".$branch_code."') AND SUBSTRING(OH.order_no,-14,8)>='20160901' ORDER BY SUBSTRING(OH.order_no,2,5) ASC, SUBSTRING(OH.order_no,-14,8) ASC";
$res_order_exist_check = mysql_query($sql_order_exist_check);
$total_row_check = mysql_num_rows($res_order_exist_check);
if($total_row_check>0){
?>
<table width="100%" class="border" cellpadding="6px" border="1" style="border-collapse:collapse;">
  <tr class="TDHEAD">
  	<td>Order No</td>
  	<td>Emp Name</td>
    <?php
	echo $td_struct;
	?>
	<td>Qty</td>
    <td>Rate</td>
    <td>Status</td>
  </tr>
<?php
	$res_order_exist_check = mysql_query($sql_order_exist_check);
	while($row_order_exist_check = mysql_fetch_array($res_order_exist_check)){
		$order_no = $row_order_exist_check['order_no'];
		$emp_code = $row_order_exist_check['emp_code'];
		$emp_name = $row_order_exist_check['emp_name'];
		$status = $row_order_exist_check['status'];
		
		$order_date = date('d-m-Y',strtotime(substr($order_no,-14,8)));
		$order_time = date('H:i:s',strtotime(substr($order_no,-6)));
		
		if($status == ''){
			$status_blank = " selected";
			$status_pending = "";
			
			$select_control = "<select id=\"select_$order_no\" onChange=\"edit_product_details('$order_no');\">
							<option value=\"\" $status_blank>Select</option>
							<option value=\"billed\">BILLED</option>
							<option value=\"spl permission\">SPL PERMISSION</option>
							<option value=\"hold\">HOLD</option>
						   </select>";
		}
		else if($status == 'pending'){
			$status_blank = "";
			$status_pending = " selected";
			
			$select_control = "<select id=\"select_$order_no\" onChange=\"edit_product_details('$order_no');\">
							<option value=\"\" $status_blank>Select</option>
							<option value=\"billed\">BILLED</option>
						   </select>";
		}
		
		
		
		/*----> COUNTS TOTAL PRODUCTS <----*/
		$sql_product_details_count = "SELECT COUNT(sku_code) FROM order_details WHERE order_no = '".$order_no."' AND qty != billed_qty";
		$res_product_details_count = mysql_query($sql_product_details_count);
		$row_product_details_count = mysql_fetch_array($res_product_details_count);
		$total_product = $row_product_details_count['COUNT(sku_code)'];
		
		/*----> LOOPS THROUGH order_details PRODUCTS <----*/
		$sql_product_details = "SELECT sku_code, qty, sale_rate, billed_qty FROM order_details WHERE order_no = '".$order_no."' AND qty != billed_qty";
		$res_product_details = mysql_query($sql_product_details);
		while($row_product_details = mysql_fetch_array($res_product_details)){
			
			$sku_code = $row_product_details['sku_code'];
			$qty = $row_product_details['qty'];
			$sale_rate = $row_product_details['sale_rate'];
			$billed_qty = $row_product_details['billed_qty'];
			
			/*----> GETS product_desc, product_group_name, product_sub_group_name ACCORDINGLY <----*/
			$sql_product = "SELECT ".$product_master_query_string." FROM ".$table_name." WHERE PM.prod_code = '".$sku_code."' AND ".$condition;
			$res_product = mysql_query($sql_product);
			$row_product = mysql_fetch_array($res_product);
			
			echo "<tr>";
			if($total_product>1){
				if(!in_array($order_no,$order_no_array_one)){
					array_push($order_no_array_one,$order_no);
					echo "<td rowspan=\"".$total_product."\" >".$emp_name."/".$order_date."/".$order_time."</td>";
				}
			}
			else if($total_product == 1){
				echo "<td>".$emp_name."/".$order_date."/".$order_time."</td>";
			}
			
			echo "<td>$emp_name</td>";
			
			if($product_filter == 1){
				$product_desc = $row_product['prod_desc'];
				echo "<td>$product_desc</td>";
			}
			else if($product_filter == 2){
				$product_desc = $row_product['prod_desc']; 
				$product_group_name = $row_product['product_group_name'];
				echo "<td>$product_group_name</td><td>$product_desc</td>";
			}
			else if($product_filter == 3){
				$product_desc = $row_product['prod_desc']; 
				$product_group_name = $row_product['product_group_name']; 
				$product_sub_group_name = $row_product['product_sub_group_name'];
				echo "<td>$product_group_name</td><td>$product_group_name</td><td>$product_desc</td>";
			}
			else if($product_filter == 4){
			}
			
			echo "<td align=\"right\">".$qty."</td>
					<td align=\"right\">".$sale_rate."</td>";
			
			if($total_product>1){
				if(!in_array($order_no,$order_no_array)){
					array_push($order_no_array,$order_no);
					echo "<td rowspan=\"".$total_product."\" width=\"15%\" style=\"background:#EEE9BF;\">$select_control</td>";
					echo "</tr>";
				}
				else{
					echo "</tr>";
				}
			}
			else if($total_product == 1){
				echo "<td width=\"15%\" style=\"background:#EEE9BF;\">$select_control</td>";
				echo "</tr>";
			}
		}
		
	}
}
else{
	echo "<div style=\"font-weight:bold; color:red;\">No records</div>";
}
mysql_close($link);
?>