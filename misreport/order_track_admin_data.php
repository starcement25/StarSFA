<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$count = 1;
$sql_order_track = "SELECT order_no, DATE_FORMAT(entry_date,'%d-%m-%Y %H:%i:%s') AS entry_time, SUBSTRING(user_id,3,5) AS user_code, prod_code, order_qty, SUM(billed_qty) FROM order_tracking GROUP BY prod_code, order_no ORDER BY entry_date DESC";
$res_order_track = mysqli_query($link,$sql_order_track);
$total_row_check = mysqli_num_rows($res_order_track);
if($total_row_check>0){
	?>
    <table class="border" width="100%" style="border-collapse:collapse;" border="1">
      <tr>
      	<td align="center" class="TDHEAD_SUB" colspan="8">Order Tracking Report</td>
      </tr>
      <tr class="TDHEAD">
      	<td>Sl</td>
        <td>Order No</td>
        <td>Prod Code</td>
        <td>Ordered Qty</td>
        <td>Billed Qty</td>
        <td>Pending Qty</td>
        <td>Entered By</td>
        <td>Date Time</td>
      </tr>
    <?php
	$res_order_track = mysqli_query($link,$sql_order_track);
	while($row_order_track = mysqli_fetch_assoc($res_order_track)){
		$order_no = $row_order_track ['order_no'];
		$entry_time = $row_order_track ['entry_time'];
		$user_code = $row_order_track ['user_code'];
		$prod_code = $row_order_track ['prod_code'];
		$order_qty = $row_order_track ['order_qty'];
		$billed_qty = $row_order_track ['SUM(billed_qty)'];
		
		if($user_code != 'ADMIN'){
			$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$user_code."'";
			$res_emp_name = mysqli_query($link,$sql_emp_name);
			$row_emp_name = mysqli_fetch_assoc($res_emp_name);
			$emp_name = $row_emp_name['emp_name'];
		}
		else{
			$emp_name = $user_code;
		}
		
		$pending_qty = $order_qty - $billed_qty;
		
		echo "<tr>
				<td>".$count."</td>
				<td>".$order_no."</td>
				<td>".$prod_code."</td>
				<td align=\"right\">".$order_qty."</td>
				<td align=\"right\">".$billed_qty."</td>
				<td align=\"right\">".$pending_qty."</td>
				<td>".$emp_name."</td>
				<td>".$entry_time."</td>
			  </tr>";
		$count++;
	}
	?>
    </table>
    <?php
}
else{
	echo "<div style=\"font-weight:bold; color:red;\">No records</div>";
}
mysqli_close($link);
?>
