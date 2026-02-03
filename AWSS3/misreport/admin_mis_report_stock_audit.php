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
    <td>Stock Audit(Qty)</td>
    <td>Locate</td>
  </tr>
<?php
$count = 1;
$sql_emp_code = "SELECT emp_code, emp_name FROM employee_master WHERE emp_code IN(".$employee.") ORDER BY emp_name ASC";
$res_emp_code = mysql_query($sql_emp_code);
while($row_emp_code = mysql_fetch_array($res_emp_code)){
	$emp_code = $row_emp_code['emp_code'];
	$emp_name = $row_emp_code['emp_name'];
	
	$sql_check_transid_count = "SELECT COUNT(LO.trans_id) FROM location LO WHERE LO.emp_code = '".$emp_code."' AND (SUBSTRING(LO.trans_id,1,1) = 'S' AND SUBSTRING(LO.trans_id,1,2) != 'SU')".$date_condition;
	$res_check_transid_count = mysql_query($sql_check_transid_count);
	$row_check_transid_count = mysql_fetch_array($res_check_transid_count);
	$total_count = $row_check_transid_count['COUNT(LO.trans_id)'];
	if($total_count>0){
		echo "<tr><td colspan=\"8\" class=\"TDHEAD_SUB\" align=\"center\">$emp_code&nbsp;&nbsp;--&nbsp;&nbsp;$emp_name</td></tr>";
		
		$sql_stock_audit_id = "SELECT LO.trans_id, DATE_FORMAT(SUBSTRING(LO.date,1,10),'%d-%m-%Y') AS stock_date, DATE_FORMAT(LO.date,'%H:%i:%s') AS stock_time FROM location LO WHERE LO.emp_code = '".$emp_code."' AND (SUBSTRING(LO.trans_id,1,1) = 'S' AND SUBSTRING(LO.trans_id,1,2) != 'SU')".$date_condition." ORDER BY stock_date DESC";
		$res_stock_audit_id = mysql_query($sql_stock_audit_id);
		while($row_stock_audit_id = mysql_fetch_array($res_stock_audit_id)){
			$stock_audit_id = $row_stock_audit_id['trans_id'];
			$stock_audit_date = $row_stock_audit_id['stock_date'];
			$stock_audit_time = $row_stock_audit_id['stock_time'];
			
			$sql_customer_code = "SELECT customer_code, quantity FROM stock_audit WHERE transaction_id = '".$stock_audit_id."'";
			$res_customer_code = mysql_query($sql_customer_code);
			$row_customer_code = mysql_fetch_array($res_customer_code);
			$customer_code = $row_customer_code['customer_code'];
			$quantity = $row_customer_code['quantity'];
			
			$sql_customer_name = "SELECT dns_customer_code, customer_name, cust_type FROM customer_master WHERE customer_code = '".$customer_code."'";
			$res_customer_name = mysql_query($sql_customer_name);
			$row_customer_name = mysql_fetch_array($res_customer_name);
			$customer_name = $row_customer_name['customer_name'];
			$dns_customer_code = $row_customer_name['dns_customer_code'];
			$cust_type = $row_customer_name['cust_type'];
			
			echo "<tr>
					<td>".$count."</td>
					<td>".$stock_audit_date."</td>
					<td>".$stock_audit_time."</td>
					<td>".$dns_customer_code."</td>
					<td>".$customer_name."</td>
					<td>".$cust_type."</td>
					<td align=\"right\">".$quantity."</td>
					<td><a href=\"customerLocate.php?trans_id=$stock_audit_id&customer_code=$customer_code&
							emp_code=$emp_code&date=$date&from_date=$from_date&to_date=$to_date&mode=$mode&page=$page&visit_map=true\" 	style=\"color:brown;font-weight:bold;text-decoration:none;\" target=\"_blank\">Locate</a></td>
				  </tr>";
			$count++;
		}
	}
}
mysql_close($link);
?>
</table>