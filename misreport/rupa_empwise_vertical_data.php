<?php
ob_start();
session_start();
require("adminUtils.php");

$count = 1;
$emp_code = $_REQUEST['emp_code'];
$vertical = $_REQUEST['vertical'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

if($vertical == 'MACROMAN'){
	$vertical_condition = " vertical_value LIKE 'M%' ";
}
else{
	$vertical_condition = " vertical_value = '".$vertical."' ";
}

//print_r($_REQUEST);

if($emp_code == 'all'){
	if(strtoupper($_SESSION['admin_login']) == 'ADMIN'){
		$emp_code_cond = ' 1 ';
	}
	else{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_code_cond = " SUBSTRING(order_no,-19,5) IN (".$emp_hierarchy.") ";
	}
}
else{
	$emp_code_cond = " SUBSTRING(order_no,-19,5) = '".$emp_code."' ";
}

$sql_prev_order = "SELECT DISTINCT SUBSTRING(order_no,-19,5) AS emp_code FROM prev_order_counting_master WHERE ".$emp_code_cond." AND order_no LIKE 'O%' AND ".$vertical_condition." AND (SUBSTRING(order_no,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."')";
$res_prev_order = mysqli_query($link,$sql_prev_order);
$total_rows = mysqli_num_rows($res_prev_order);
if($total_rows>0){
	?>
    <table class="border" width="54%" style="border-collapse:collapse;" border="1">
      <tr class="TDHEAD">
      	<td align="center" colspan="4">Vertical Value: <?php echo $vertical; ?></td>
      </tr>
      <tr class="TDHEAD_SUB">
      	<td>Serial</td>
      	<td>Emp Name</td>
        <td>Primary</td>
        <td>Secondary</td>
      </tr>
    
    <?php
	$res_prev_order = mysqli_query($link,$sql_prev_order);
	while($row_prev_order = mysqli_fetch_assoc($res_prev_order)){
		$emp_code_select = $row_prev_order['emp_code'];
		
		$sql_primary = "SELECT SUM(visit_qty) AS primary_val FROM prev_order_counting_master WHERE SUBSTRING(order_no,-19,5) = '".$emp_code_select."' AND order_no LIKE 'O%' AND ".$vertical_condition." AND (SUBSTRING(order_no,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') AND cust_type = 'D' GROUP BY SUBSTRING(order_no,-19,5)";
		$res_primary = mysqli_query($link,$sql_primary);
		$row_primary = mysqli_fetch_assoc($res_primary);
		$primary_val = $row_primary['primary_val'];
		
		$sql_secondary = "SELECT SUM(visit_qty) AS secondary_val FROM prev_order_counting_master WHERE SUBSTRING(order_no,-19,5) = '".$emp_code_select."' AND order_no LIKE 'O%' AND ".$vertical_condition." AND (SUBSTRING(order_no,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') AND cust_type = 'R' GROUP BY SUBSTRING(order_no,-19,5)";
		$res_secondary = mysqli_query($link,$sql_secondary);
		$row_secondary = mysqli_fetch_assoc($res_secondary);
		$secondary_val = $row_secondary['secondary_val'];
		
		$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$emp_code_select."'";
		$res_emp_name = mysqli_query($link,$sql_emp_name);
		$row_emp_name = mysqli_fetch_assoc($res_emp_name);
		$emp_name = $row_emp_name['emp_name'];
		
		echo "<tr>
				<td>".$count."</td>
				<td>".$emp_name."</td>
				<td align=\"right\">".$primary_val."</td>
				<td align=\"right\">".$secondary_val."</td>
			  </tr>";
			  
		$count++;
	}
	?>
    </table>
    <?php
}
else{
	echo "<div style=\"color:red; font-weight:bold;\">No records found</div>";
}
mysqli_close($link);
?>