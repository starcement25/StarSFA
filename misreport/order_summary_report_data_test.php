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
	$emp_hierarchy_condition = " AND SUBSTRING(OH.order_no,2,5) IN (".$emp_hierarchy.") ";
}

$count = 1;

$sql_location = "SELECT DISTINCT EM.emp_name, EM.state, EM.HQ, LO.emp_code FROM employee_master EM, location LO WHERE DATE_FORMAT(LO.date,'%Y-%m') = '2017-03' AND LO.trans_id LIKE 'A%' AND LO.emp_code = EM.emp_code ORDER BY EM.state ASC";
$res_location = mysqli_query($link,$sql_location);
$total_row_check = mysqli_num_rows($res_location);
if($total_row_check>0){
	?>
    <table class="border" id="display_table" border="1" style="border-collapse:collapse;" width="100%">
      <tr class="TDHEAD">
      	<td>Emp Code</td>
        <td>Emp Name</td>
        <td>HQ</td>
        <td>State</td>
        <td>Month</td>
        <td>Total Days Present</td>
        <td>Total Customer</td>
        <td>Customer Visisted</td>
        <td>Total Calls</td>
        <td>Productive Calls</td>
        <td>Total Order Value</td>
        <td>Average Outlet Value</td>
      </tr>
    <?php
	$res_location = mysqli_query($link,$sql_location);
	while($row_location = mysqli_fetch_assoc($res_location)){
		$emp_name = $row_location['emp_name'];
		$emp_code = $row_location['emp_code'];
		$state = $row_location['state'];
		$HQ = $row_location['HQ'];
		
		/*----> TOTAL DAYS PRESENT <----*/
		$sql_total_days_present = "SELECT COUNT(trans_id) FROM location WHERE emp_code = '".$emp_code."' AND trans_id LIKE 'A%' AND DATE_FORMAT(date,'%Y-%m') = '2017-03'";
		$res_total_days_present = mysqli_query($link,$sql_total_days_present);
		$row_total_days_present = mysqli_fetch_assoc($res_total_days_present);
		$total_days_present = $row_total_days_present['COUNT(trans_id)'];
		
		/*----> TOTAL CUSTOMER COUNT <----*/
		$sql_customer_count = "SELECT COUNT(customer_code) FROM customer_route_emp_relation WHERE emp_code = '".$emp_code."'";
		$res_customer_count = mysqli_query($link,$sql_customer_count);
		$row_customer_count = mysqli_fetch_assoc($res_customer_count);
		$total_customer_count = $row_customer_count['COUNT(customer_code)'];
		
		/*----> TOTAL CUSTOMER VISITED <----*/
		$sql_unique_customer_visit = "SELECT COUNT(DISTINCT CONCAT(customer_code,'^',SUBSTRING(order_no,-14,8))) AS customer_visited FROM order_header WHERE SUBSTRING(order_no,-14,6) = '201703' AND SUBSTRING(order_no,-19,5) = '".$emp_code."'";
		$res_unique_customer_visit = mysqli_query($link,$sql_unique_customer_visit);
		$row_unique_customer_visit = mysqli_fetch_assoc($res_unique_customer_visit);
		$total_customer_visisted = $row_unique_customer_visit['customer_visited'];
		
		/*----> TOTAL CALLS <----*/
		$sql_total_calls = "SELECT COUNT(order_no) FROM order_header WHERE SUBSTRING(order_no,-14,6) = '201703' AND SUBSTRING(order_no,-19,5) = '".$emp_code."'";
		$res_total_calls = mysqli_query($link,$sql_total_calls);
		$row_total_calls = mysqli_fetch_assoc($res_total_calls);
		$total_calls = $row_total_calls['COUNT(order_no)'];
		
		/*----> TOTAL PRODUCTIVE CALLS <----*/
		$sql_productive_calls = "SELECT COUNT(order_no) FROM order_header WHERE SUBSTRING(order_no,-14,6) = '201703' AND SUBSTRING(order_no,-19,5) = '".$emp_code."' AND order_no LIKE 'O%'";
		$res_productive_calls = mysqli_query($link,$sql_productive_calls);
		$row_productive_calls = mysqli_fetch_assoc($res_productive_calls);
		$total_productive_calls = $row_productive_calls['COUNT(order_no)'];
		
		/*----> TOTAL ORDER VALUE <----*/
		$sql_total_order_value = "SELECT SUM(OD.amount) FROM order_details OD, order_header OH WHERE OH.order_no LIKE 'O%' AND SUBSTRING(OH.order_no,-14,6) = '201703' AND SUBSTRING(OH.order_no,-19,5) = '".$emp_code."' AND OH.order_no = OD.order_no";
		$res_total_order_value = mysqli_query($link,$sql_total_order_value);
		$row_total_order_value = mysqli_fetch_assoc($res_total_order_value);
		$total_order_value = $row_total_order_value['SUM(OD.amount)'];
		
		/*----> AVERAGE OUTLET VALUE <----*/
		$average_outlet_value = $total_order_value/$total_productive_calls;
		
		echo "<tr>
				<td>".$emp_code."</td>
				<td>".$emp_name."</td>
				<td>".$HQ."</td>
				<td>".$state."</td>
				<td>March</td>
				<td align=\"right\">".$total_days_present."</td>
				<td align=\"right\">".$total_customer_count."</td>
				<td align=\"right\">".$total_customer_visisted."</td>
				<td align=\"right\">".$total_calls."</td>
				<td align=\"right\">".$total_productive_calls."</td>
				<td align=\"right\">".number_format($total_order_value,2)."</td>
				<td align=\"right\">".number_format($average_outlet_value,2)."</td>
			  </tr>";
		$count++;
		//if($count == 5) break;
	}
	?>
    </table><br /><br />
    <p>
    <div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    </p>
    <?php
}
else{
	echo "<span style=\"font-weight:bold; color:red;\">No Records Found!</span>";
}

?>