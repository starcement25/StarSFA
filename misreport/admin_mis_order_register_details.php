<?php
ob_start();
session_start();
require("adminUtils.php");
//require("include/dbcon.php");
$current_date = date('Y-m-d');
$month_date = date('Y-m');
$current_month = date('m');
if($current_month == '01' || $current_month == '02' || $current_month == '03'){
	$previous_year = date('Y', strtotime('-1 year'));
	$previous_year_date = $previous_year."-04-01";
}
else{
	$previous_year_date = date('Y-04-01');
}

$val = $_REQUEST['val'];
$cust_type = $_REQUEST['cust_type'];
$cust_type_condition = " CM.cust_type = '".$cust_type."' ";
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

if($cust_type == 'all'){
	$cust_type_array = array();
	$sql_cust_type = "SELECT DISTINCT cust_type FROM customer_master";
	$res_cust_type = mysqli_query($link,$sql_cust_type);
	while($row_cust_type = mysqli_fetch_assoc($res_cust_type)){
		$cust_type = $row_cust_type['cust_type'];
		$cust_type_string .= "'".$cust_type."',";
	}
	$cust_type_string = rtrim($cust_type_string,",");
	$cust_type_condition = " CM.cust_type IN(".$cust_type_string.") ";
}

if($val == 'T'){
	$date_condition = " SUBSTRING(LO.date,1,10) = '".$current_date."'";
}
else if($val == 'MTD'){
	$date_condition = " SUBSTRING(LO.date,1,7) = '".$month_date."'";
}
else if($val == 'YTD'){
	$date_condition = " (SUBSTRING(LO.date,1,10) BETWEEN '".$previous_year_date."' AND '".$current_date."') ";
}

if($start_date != '' && $end_date != ''){
	$date_condition = " (SUBSTRING(LO.date,1,10) BETWEEN '".$start_date."' AND '".$end_date."') ";
}

$employee = $_REQUEST['employee'];
$employee_arg = str_replace("#",",",$employee);
$employee_arg = str_replace("^","'",$employee_arg);

?>
<table class="border" border="1" style="border-collapse:collapse;" width="100%" >
  <tr class="TDHEAD" align="center">
  	<td width="10%">Date</td>
    <td>Branch</td>
    <td>Cust Code</td>
    <td>Cust Name</td>
    <td>Linked Dealer</td>
    <td>Freight</td>
    <td>Destination</td>
    <td>Product</td>
    <td>Order Qty</td>
    <td width="15%">Remarks</td>
  </tr>
<?php
$sql_emp_name = "SELECT emp_code, emp_name FROM employee_master WHERE emp_code IN(".$employee_arg.") ORDER BY emp_name ASC";
$res_emp_name = mysqli_query($link,$sql_emp_name);
while($row_emp_name = mysqli_fetch_assoc($res_emp_name)){
	$emp_code = $row_emp_name['emp_code'];
	$emp_name = $row_emp_name['emp_name'];
	
	$sql_count_order = "SELECT COUNT(LO.trans_id) FROM location LO, order_header OH, customer_master CM WHERE LO.emp_code = '".$emp_code."' AND LO.trans_id LIKE 'O%' AND LO.trans_id = OH.order_no AND OH.customer_code = CM.customer_code AND ".$cust_type_condition." AND ".$date_condition;
	$res_count_order = mysqli_query($link,$sql_count_order);
	$row_count_order = mysqli_fetch_assoc($res_count_order);
	$total_rows_count = $row_count_order['COUNT(LO.trans_id)'];
	if($total_rows_count>0){
		echo "<tr><td colspan=\"10\" class=\"TDHEAD_SUB\" align=\"center\">$emp_code&nbsp;&nbsp;--&nbsp;&nbsp;$emp_name</td></tr>";
		$sql_order_trans_id = "SELECT LO.trans_id, DATE_FORMAT(LO.date,'%d-%m-%Y') AS trans_date FROM location LO, order_header OH, customer_master CM WHERE LO.emp_code = '".$emp_code."' AND LO.trans_id LIKE 'O%' AND LO.trans_id = OH.order_no AND OH.customer_code = CM.customer_code AND ".$cust_type_condition." AND ".$date_condition." ORDER BY trans_date DESC";
		$res_order_trans_id = mysqli_query($link,$sql_order_trans_id);
		while($row_order_trans_id = mysqli_fetch_assoc($res_order_trans_id)){
			$trans_id = $row_order_trans_id['trans_id'];
			$trans_date = $row_order_trans_id['trans_date'];
			
			$sql_order_header = "SELECT customer_code, branch_code, destination_code, d_instruction, order_type, freight_component FROM order_header WHERE order_no = '".$trans_id."'";
			$res_order_header = mysqli_query($link,$sql_order_header);
			$row_order_header = mysqli_fetch_assoc($res_order_header);
			$customer_code = $row_order_header['customer_code'];
			$branch_code = $row_order_header['branch_code'];
			$destination_code = $row_order_header['destination_code'];
			$remarks = $row_order_header['d_instruction'];
			$order_type = $row_order_header['order_type'];
			$freight_component = $row_order_header['freight_component'];
			
			$sql_customer_name = "SELECT dns_customer_code, customer_name, rds_tag FROM customer_master WHERE customer_code = '".$customer_code."'";
			$res_customer_name = mysqli_query($link,$sql_customer_name);
			$row_customer_name = mysqli_fetch_assoc($res_customer_name);
			$dns_customer_code = $row_customer_name['dns_customer_code'];
			$customer_name = $row_customer_name['customer_name'];
			$rds_tag = $row_customer_name['rds_tag'];
			
			if($order_type == 'Sub Dealer'){
				$sql_rds_name = "SELECT customer_name FROM customer_master WHERE customer_code = '".$rds_tag."'";
				$res_rds_name = mysqli_query($link,$sql_rds_name);
				$row_rds_name = mysqli_fetch_assoc($res_rds_name);
				$rds_name = $row_rds_name['customer_name'];
			}
			
			$sql_destination_name = "SELECT destination_name FROM destination_master WHERE destination_code = '".$destination_code."'";
			$res_destination_name = mysqli_query($link,$sql_destination_name);
			$row_destination_name = mysqli_fetch_assoc($res_destination_name);
			$destination_name = $row_destination_name['destination_name'];
			
			$sql_branch_name = "SELECT branch_name FROM branch_master WHERE branch_code = '".$branch_code."'";
			$res_branch_name = mysqli_query($link,$sql_branch_name);
			$row_branch_name = mysqli_fetch_assoc($res_branch_name);
			$branch_name = $row_branch_name['branch_name'];
			
			$sql_order_details = "SELECT sku_code, qty, freight_charge FROM order_details WHERE order_no = '".$trans_id."'";
			$res_order_details = mysqli_query($link,$sql_order_details);
			while($row_order_details = mysqli_fetch_assoc($res_order_details)){
				$sku_code = $row_order_details['sku_code'];
				$quantity = $row_order_details['qty'];
				$freight_charge = $row_order_details['freight_charge'];
				
				$sql_prod_name = "SELECT prod_desc FROM product_master WHERE prod_code = '".$sku_code."'";
				$res_prod_name = mysqli_query($link,$sql_prod_name);
				$row_prod_name = mysqli_fetch_assoc($res_prod_name);
				$prod_name = $row_prod_name['prod_desc'];
				
				echo "<tr>
						<td>".$trans_date."</td>
						<td>".$branch_name."</td>
						<td>".$dns_customer_code."</td>
						<td>".$customer_name."</td>
						<td>".$rds_name."</td>
						<td>".$freight_component."</td>
						<td>".$destination_name."</td>
						<td>".$prod_name."</td>
						<td align=\"right\">".$quantity."</td>
						<td>".$remarks."</td>
					  </tr>";
			}
		}
	}
}
mysqli_close($link);
?>
</table>