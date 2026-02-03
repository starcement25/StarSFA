<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$val = $_REQUEST['val'];
$employee = $_REQUEST['employee'];
$employee = str_replace("^","'",$employee);
$employee = str_replace("#",",",$employee);
$emp_hierarchy_condition=' AND LO.emp_code IN('.$employee.') ';


if($val == 'T'){
	$date = date('Y-m-d');
	$date_condition = " AND SUBSTRING(LO.date,1,10) = '".$date."' ";
}
else if($val == 'MTD'){
	$current_month = date('m');
	$mtd_date = date('m');
	$date_condition = " AND SUBSTRING(LO.date,6,2) = '".$current_month."' ";
}
else if($val == 'YTD'){
	$current_date = $date = date('Y-m-d');
	$current_month = date('m');
	if($current_month == '01' || $current_month == '02' || $current_month == '03'){
		$previous_year = date('Y', strtotime('-1 year'));
		$previous_year_date = $previous_year."-04-01";
	}
	else{
		$previous_year_date = date('Y-04-01');
	}
	$date_condition = " AND (SUBSTRING(LO.date,1,10) BETWEEN '".$previous_year_date."' AND '".$current_date."') ";
}

?>

<table width="100%" border="1" style="border-collapse:collapse;" class="border">
  <tr class="TDHEAD" align="center">
  	<td>SI</td>
    <td>Date</td>
    <td>Customer Name</td>
    <td>Payment Received</td>
  </tr>
  
<?php
$count = 1;
$sql_emp_code = "SELECT emp_code, emp_name FROM employee_master WHERE emp_code IN(".$employee.") ORDER BY emp_name ASC";
$res_emp_code = mysql_query($sql_emp_code);
while($row_emp_code = mysql_fetch_array($res_emp_code)){
	$emp_code = $row_emp_code['emp_code'];
	$emp_name = $row_emp_code['emp_name'];
	
	$sql_total_collection = "SELECT COUNT(LO.trans_id) FROM location LO WHERE LO.emp_code = '".$emp_code."' AND LO.trans_id LIKE 'P%'".$date_condition;
	$res_total_collection = mysql_query($sql_total_collection);
	$row_total_collection = mysql_fetch_array($res_total_collection);
	$total_count = $row_total_collection['COUNT(LO.trans_id)'];
	$count = 1;
	if($total_count>0){
		echo "<tr><td colspan=\"4\" class=\"TDHEAD_SUB\" align=\"center\">$emp_code&nbsp;&nbsp;--&nbsp;&nbsp;$emp_name</td></tr>";
		
		$sql_collection_id = "SELECT LO.trans_id, DATE_FORMAT(SUBSTRING(LO.date,1,10),'%d-%m-%Y') AS collection_date FROM location LO WHERE LO.emp_code = '".$emp_code."' AND LO.trans_id LIKE 'P%'".$date_condition;
		$res_collection_id = mysql_query($sql_collection_id);
		while($row_collection_id = mysql_fetch_array($res_collection_id)){
			$collection_id = $row_collection_id['trans_id'];
			$collection_date = $row_collection_id['collection_date'];
			
			$sql_customer_code = "SELECT customer_code FROM payment_header WHERE receipt_id = '".$collection_id."'";
			$res_customer_code = mysql_query($sql_customer_code);
			$row_customer_code = mysql_fetch_array($res_customer_code);
			$customer_code = $row_customer_code['customer_code'];
			
			$sql_customer_name = "SELECT customer_name FROM customer_master WHERE customer_code = '".$customer_code."'";
			$res_customer_name = mysql_query($sql_customer_name);
			$row_customer_name = mysql_fetch_array($res_customer_name);
			$customer_name = $row_customer_name['customer_name'];
			
			$sql_payment_details = "SELECT SUM(amount) FROM order_details WHERE order_no = '".$order_id."'";
			$res_payment_details = mysql_query($sql_payment_details);
			$row_payment_details = mysql_fetch_array($res_payment_details);
			$payment_amount = $row_order_details['SUM(amount)'];
			
			echo "<tr>
					<td>".$count."</td>
					<td>".$order_date."</td>
					<td>".$customer_name."</td>
					<td align=\"right\">".$payment_amount."</td>
				  </tr>";
			$count++;
			
		}
	}
}
mysql_close($link);
?>
</table>