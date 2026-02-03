<?php
ob_start();
session_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
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
$employee = $_REQUEST['employee'];
$employee_arg = str_replace(",","#",$employee);
$employee_arg = str_replace("'","^",$employee_arg);

if($_SESSION['admin_login']=="admin"){
	$emp_hierarchy='';
	$emp_hierarchy_condition='';
	$emp_hierarchy_condition_one='';
}
else
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition_one=' AND LO.emp_code IN('.$employee.')';
}

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

//$emp_hierarchy_condition_one = ' AND SUBSTRING(OH.order_no,2,5) IN('.$employee.') ';
?>
<table width="100%" style="border-collapse:collapse;" border="1" cellpadding="6px">
  <tr class="TDHEAD">
  	<td></td>
    <td>Order Generated(Count)</td>
<?php
$cust_type_array = array();
$sql_cust_type = "SELECT DISTINCT cust_type FROM customer_master";
$res_cust_type = mysqli_query($link,$sql_cust_type);
while($row_cust_type = mysqli_fetch_assoc($res_cust_type)){
	$cust_type = $row_cust_type['cust_type'];
	if($cust_type == 'NON STAR'){
		$non_star = $cust_type;
	}
	else{
		echo "<td align=\"center\">".$cust_type."</td>";
		array_push($cust_type_array,$cust_type);
	}
}
echo "<td align=\"center\">$non_star</td>";
array_push($cust_type_array,$non_star);
?>
  </tr>
<?php
	if($start_date != '' && $end_date != ''){
		echo "<tr><td align=\"center\" style=\"color:black;font-weight:bold;\">Custom</td>";
		$date_condition = " (SUBSTRING(LO.date,1,10) BETWEEN '".$start_date."' AND '".$end_date."') ";
		
		foreach($cust_type_array as $cust_type_val){
			$table_data .= "<td align=\"right\">";
			
			$sql_order_count = "SELECT COUNT(LO.trans_id) FROM location LO, customer_master CM, order_header OH WHERE OH.order_no=LO.trans_id AND LO.trans_id LIKE 'O%' AND ".$date_condition." AND OH.customer_code = CM.customer_code AND CM.cust_type IN ('".$cust_type_val."') AND LO.emp_code IN(".$employee.")";
			$res_order_count = mysqli_query($link,$sql_order_count);
			$row_order_count = mysqli_fetch_assoc($res_order_count);
			$order_count = $row_order_count['COUNT(LO.trans_id)'];
			
			if($order_count == 0)
				$display_count = "--";
			else
				$display_count = "<a href=\"#\" style=\"color:blue;font-weight:bold;\" onclick=\"view_details('$val','$employee_arg','$cust_type_val');\">".$order_count."</a>";
			
			$table_data .= $display_count."</td>";
			$total_order_count += $order_count;
		}
		echo "<td align=\"right\"><a href=\"#\" style=\"color:blue;font-weight:bold;\" onclick=\"view_details('$val','$employee_arg','all');\">".$total_order_count."</a></td>".$table_data;
		echo "</tr>";
	}
	else{
		for($i=1;$i<=3;$i++){
			if($i == 1){
				echo "<tr><td align=\"center\" style=\"color:black;font-weight:bold;\">Today</td>";
				$date_condition = " SUBSTRING(LO.date,1,10) = '".$current_date."'";
				$val = "T";
			}
			else if($i == 2){
				echo "<tr><td align=\"center\" style=\"color:black;font-weight:bold;\">MTD</td>";
				$date_condition = " SUBSTRING(LO.date,1,7) = '".$month_date."'";
				$val = "MTD";
			}
			else if($i == 3){
				echo "<tr><td align=\"center\" style=\"color:black;font-weight:bold;\">YTD</td>";
				$date_condition = " (SUBSTRING(LO.date,1,10) BETWEEN '".$previous_year_date."' AND '".$current_date."') ";
				$val = "YTD";
			}
			foreach($cust_type_array as $cust_type_val){
				$table_data .= "<td align=\"right\">";
				
				$sql_order_count = "SELECT COUNT(LO.trans_id) FROM location LO, customer_master CM, order_header OH WHERE OH.order_no=LO.trans_id AND LO.trans_id LIKE 'O%' AND ".$date_condition." AND OH.customer_code = CM.customer_code AND CM.cust_type IN ('".$cust_type_val."') AND LO.emp_code IN(".$employee.")";
				$res_order_count = mysqli_query($link,$sql_order_count);
				$row_order_count = mysqli_fetch_assoc($res_order_count);
				$order_count = $row_order_count['COUNT(LO.trans_id)'];
				
				if($order_count == 0)
					$display_count = "--";
				else
					$display_count = "<a href=\"#\" style=\"color:blue;font-weight:bold;\" onclick=\"view_details('$val','$employee_arg','$cust_type_val');\">".$order_count."</a>";
				
				$table_data .= $display_count."</td>";
				$total_order_count += $order_count;
			}
			echo "<td align=\"right\"><a href=\"#\" style=\"color:blue;font-weight:bold;\" onclick=\"view_details('$val','$employee_arg','all');\">".$total_order_count."</a></td>".$table_data;
			echo "</tr>";
			$table_data = '';
			$total_order_count = '';
		}
	}
	
	mysqli_close($link);
?>
</table>