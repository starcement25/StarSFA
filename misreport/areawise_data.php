<?php
ob_start();
session_start();
require("adminUtils.php");
require ("attribute_selection.php");
if($_SESSION['admin_login']=="")  		header("product:index.php");

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$type = $_REQUEST['type'];

if(strtoupper($_SESSION['admin_login']) == 'ADMIN'){
	$emp_hierarchy='';
	$emp_hierarchy_condition='';
}
else{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition=' AND emp_code IN('.$emp_hierarchy.') AND ';
}

if($type == 'today'){
	$today = date('Y-m-d');
	$date_condition = " date_time = '".$today."' ";
}
else if($type == 'mtd'){
	$current_month = date('Y-m');
	$date_condition = " SUBSTRING(date_time,1,7) = '".$current_month."' ";
}
else if($type == 'custom'){
	$date_condition = " (SUBSTRING(date_time,1,10) BETWEEN '".$start_date."' AND '".$end_date."') ";
}

$count = 1;
$sql_route_code = "SELECT DISTINCT route_code FROM FMCG_sales WHERE ".$emp_hierarchy_condition.$date_condition;
$res_route_code = mysqli_query($link,$sql_route_code);
$total_rows = mysqli_num_rows($res_route_code);
if($total_rows>0){
	?>
    <table class="border" border="1" style="border-collapse:collapse;" width="100%">
          <tr class="TDHEAD">
          	<td>SL</td>
          	<td width="9%">Date</td>
          	<td>Employee Name</td>
          	<td>Beat Name</td>
            <td>Customer Type</td>
            <td>Customer Name</td>
            <td>Coverage Type</td>
            <td>Brand</td>
            <td>Prod Description</td>
            <td>Quantity</td>
            <td>Rate</td>
            <td>Amount</td>
          </tr>
    <?php
	$res_route_code = mysqli_query($link,$sql_route_code);
	while($row_route_code = mysqli_fetch_assoc($res_route_code)){
		$route_code = $row_route_code['route_code'];
		
		$sql_route_name = "SELECT route_name FROM route_master WHERE route_code = '".$route_code."'";
		$res_route_name = mysqli_query($link,$sql_route_name);
		$row_route_name = mysqli_fetch_assoc($res_route_name);
		$route_name = $row_route_name['route_name'];
		
		if($route_name != ''){
		echo "<tr><td colspan='12' align='center' class='TDHEAD_SUB'>".$route_name."</td></tr>";
		
		$sql_areawise = "SELECT * FROM FMCG_sales WHERE ".$emp_hierarchy_condition.$date_condition." AND route_code = '".$route_code."'";
		$res_areawise = mysqli_query($link,$sql_areawise);
		while($row_areawise = mysqli_fetch_assoc($res_areawise)){
			$emp_code = $row_areawise['emp_code'];
			$customer_code = $row_areawise['customer_code'];
			$cust_type = $row_areawise['cust_type'];
			$coverage_type = $row_areawise['coverage_type'];
			$brand_code = $row_areawise['brand_code'];
			$prod_code = $row_areawise['prod_code'];
			$rate = $row_areawise['rate'];
			$qty = $row_areawise['qty'];
			$amount = $row_areawise['amount'];
			$date_time = $row_areawise['date_time'];
			$display_date_time = date('d-m-Y',strtotime($date_time));
			
			$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
			$res_emp_name = mysqli_query($link,$sql_emp_name);
			$row_emp_name = mysqli_fetch_assoc($res_emp_name);
			$emp_name = $row_emp_name['emp_name'];
			
			$sql_cust_name = "SELECT customer_name FROM cust_master WHERE customer_code = '".$customer_code."'";
			$res_cust_name = mysqli_query($link,$sql_cust_name);
			$row_cust_name = mysqli_fetch_assoc($res_cust_name);
			$cust_name = $row_cust_name['customer_name'];
			
			$sql_prodgroup_name = "SELECT product_group_name FROM product_group_master WHERE product_group_code = '".$brand_code."'";
			$res_prodgroup_name = mysqli_query($link,$sql_prodgroup_name);
			$row_prodgroup_name = mysqli_fetch_assoc($res_prodgroup_name);
			$prod_group_name = $row_prodgroup_name['product_group_name'];
			
			$sql_prod_desc = "SELECT prod_desc FROM product_master WHERE prod_code = '".$prod_code."'";
			$res_prod_desc = mysqli_query($link,$sql_prod_desc);
			$row_prod_desc = mysqli_fetch_assoc($res_prod_desc);
			$prod_desc = $row_prod_desc['prod_desc'];
			
			echo "<tr>
					<td>".$count."</td>
					<td>".$display_date_time."</td>
					<td>".$emp_name."</td>
					<td>".$route_name."</td>
					<td>".$cust_type."</td>
					<td>".$cust_name."</td>
					<td>".$coverage_type."</td>
					<td>".$prod_group_name."</td>
					<td>".$prod_desc."</td>
					<td align=\"right\">".$qty."</td>
					<td align=\"right\">".$rate."</td>
					<td align=\"right\">".number_format($amount,2)."</td>
				  </tr>";
			$count++;
		}
		}
	}
}
else{
	echo "No records found";
}
mysqli_close($link);
?>