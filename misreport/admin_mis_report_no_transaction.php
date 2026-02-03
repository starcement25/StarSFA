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
    <td>Time</td>
    <td>DNS Code</td>
    <td>Customer Name</td>
    <td>Customer Type</td>
    <td>Remarks</td>
    <td>Locate</td>
  </tr>

<?php
$count = 1;
$sql_emp_code = "SELECT emp_code, emp_name FROM employee_master WHERE emp_code IN(".$employee.") ORDER BY emp_name ASC";
$res_emp_code = mysqli_query($link,$sql_emp_code);
while($row_emp_code = mysqli_fetch_assoc($res_emp_code)){
	$emp_code = $row_emp_code['emp_code'];
	$emp_name = $row_emp_code['emp_name'];
	
	$sql_no_transaction = "SELECT COUNT(LO.trans_id) AS total_no_transaction FROM location LO WHERE (LO.trans_id LIKE 'NO%' OR LO.trans_id LIKE 'NC%') AND SUBSTRING(LO.emp_code,1,1)!='C' AND LO.emp_code = '".$emp_code."' ".$date_condition;
	$res_no_transaction = mysqli_query($link,$sql_no_transaction);
	$row_no_transaction = mysqli_fetch_assoc($res_no_transaction);
	$total_count = $row_no_transaction['total_no_transaction'];
	
	if($total_count>0){
		echo "<tr><td colspan=\"9\" class=\"TDHEAD_SUB\" align=\"center\">$emp_code&nbsp;&nbsp;--&nbsp;&nbsp;$emp_name&nbsp;&nbsp;--No Transaction:$total_count</td></tr>";
		
		$sql_no_transaction_id = "SELECT LO.trans_id, DATE_FORMAT(SUBSTRING(LO.date,1,10),'%d-%m-%Y') AS no_trans_date, DATE_FORMAT(LO.date,'%H:%i:%s') AS no_trans_time FROM location LO WHERE LO.emp_code = '".$emp_code."' AND (LO.trans_id LIKE 'NO%' OR LO.trans_id LIKE 'NC%')".$date_condition." ORDER BY no_trans_date DESC";
		$res_no_transaction_id = mysqli_query($link,$sql_no_transaction_id);
		while($row_no_transaction_id = mysqli_fetch_assoc($res_no_transaction_id)){
			$no_trans_id = $row_no_transaction_id['trans_id'];
			$no_trans_date = $row_no_transaction_id['no_trans_date'];
			$no_trans_time = $row_no_transaction_id['no_trans_time'];
			
			$operation_type_no=substr($no_trans_id,1,1);
			if($operation_type_no=='O'){
				$sql_order_header = "SELECT customer_code FROM order_header WHERE order_no = '".$no_trans_id."'";
				$res_order_header = mysqli_query($link,$sql_order_header);
				$row_order_header = mysqli_fetch_assoc($res_order_header);
				$customer_code = $row_order_header['customer_code'];
				
				$sql_remarks = "SELECT d_instruction FROM order_header WHERE order_no = '".$no_trans_id."'";
				$res_remarks = mysqli_query($link,$sql_remarks);
				$row_remarks = mysqli_fetch_assoc($res_remarks);
				$remarks = $row_remarks['d_instruction'];
			}
			else if($operation_type_no == 'C'){
				$sql_payment_header = "SELECT customer_code FROM payment_header WHERE order_no = '".$no_trans_id."'";
				$res_payment_header = mysqli_query($link,$sql_payment_header);
				$row_payment_header = mysqli_fetch_assoc($res_payment_header);
				$customer_code = $row_payment_header['customer_code'];
				
				$sql_remarks = "SELECT p_remark FROM payment_header WHERE receipt_id = '".$no_trans_id."'";
				$res_remarks = mysqli_query($link,$sql_remarks);
				$row_remarks = mysqli_fetch_assoc($res_remarks);
				$remarks = $row_remarks['p_remark'];
			}
			
			$sql_customer_name = "SELECT dns_customer_code, customer_name, cust_type FROM customer_master WHERE customer_code = '".$customer_code."'";
			$res_customer_name = mysqli_query($link,$sql_customer_name);
			$row_customer_name = mysqli_fetch_assoc($res_customer_name);
			$customer_name = $row_customer_name['customer_name'];
			$dns_customer_code = $row_customer_name['dns_customer_code'];
			$cust_type = $row_customer_name['cust_type'];
			
			echo "<tr>
					<td>".$count."</td>
					<td>".$no_trans_date."</td>
					<td>".$no_trans_time."</td>
					<td>".$dns_customer_code."</td>
					<td>".$customer_name."</td>
					<td>".$cust_type."</td>
					<td>".$remarks."</td>
					<td><a href=\"customerLocate.php?trans_id=$no_trans_id&customer_code=$customer_code&
							emp_code=$emp_code&date=$date&from_date=$from_date&to_date=$to_date&mode=$mode&page=$page&visit_map=true\" 	style=\"color:brown;font-weight:bold;text-decoration:none;\" target=\"_blank\">Locate</a></td>
				  </tr>";
			$count++;
		}
	}
}
mysqli_close($link);
?>