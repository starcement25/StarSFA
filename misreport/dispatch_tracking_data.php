<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

function emp_name($emp_code){
	$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
	$res_emp_name = mysqli_query($link,$sql_emp_name);
	$row_emp_name = mysqli_fetch_assoc($res_emp_name);
	$emp_name = $row_emp_name['emp_name'];
	return $emp_name;
}
$count = 1;
$sql_order_track = "SELECT SUBSTRING(order_no,2,5) AS done_by, order_no, prod_code, billed_qty, dispatch_qty, invoice_no, invoice_amount, dispatch_through, remarks, DATE_FORMAT(SUBSTRING(entry_date,1,10),'%d-%m-%Y') AS entry_date, DATE_FORMAT(SUBSTRING(entry_date,1),'%H:%i:%s') AS entry_time, SUBSTRING(user_id,3,5) AS user_code FROM dispatch_tracking ORDER BY entry_date DESC";
$res_order_track = mysqli_query($link,$sql_order_track);
$total_row_check = mysqli_num_rows($res_order_track);
if($total_row_check>0){
	?>
    <table class="border" width="100%" style="border-collapse:collapse;" border="1" cellpadding="2px">
      <tr>
      	<td align="center" class="TDHEAD_SUB" colspan="12">Dispatch Report</td>
      </tr>
      <tr class="TDHEAD" align="center">
      	<td>Sl</td>
        <td>Entered By</td>
        <td>Order No</td>
        <td>Prod Code</td>
        <td>Billed Qty</td>
        <td>Dispatch Qty</td>
        <td>Invoice No</td>
        <td>Invoice Amount</td>
        <td>Dispatch Through</td>
        <td>Remarks</td>
        <td>Date Time</td>
      </tr>
    <?php
	$res_order_track = mysqli_query($link,$sql_order_track);
	while($row_order_track = mysqli_fetch_assoc($res_order_track)){
		$order_no = $row_order_track ['order_no'];
		$prod_code = $row_order_track ['prod_code'];
		$billed_qty = $row_order_track ['billed_qty'];
		$dispatch_qty = $row_order_track ['dispatch_qty'];
		$invoice_no = $row_order_track ['invoice_no'];
		$invoice_amount = $row_order_track ['invoice_amount'];
		$dispatch_through = $row_order_track ['dispatch_through'];
		$remarks = $row_order_track ['remarks'];
		$entry_date = $row_order_track ['entry_date'];
		$entry_time = $row_order_track ['entry_time'];
		$user_code = $row_order_track ['user_code'];
		$done_by = $row_order_track ['done_by'];
		
		$order_date = date('d-m-Y',strtotime(substr($order_no,-14,8)));
		$order_time = date('H:i:s',strtotime(substr($order_no,-6)));
						
		if($user_code != 'ADMIN')
			$emp_name = emp_name($user_code);
		else
			$emp_name = $user_code;
			
		$doneby_emp = emp_name($done_by);
				
		$pending_qty = $order_qty - $billed_qty;
		
		echo "<tr>
				<td>".$count."</td>
				<td>".$emp_name."</td>
				<td>".$doneby_emp."/".$order_date."/".$order_time."</td>
				<td>".$prod_code."</td>
				<td align=\"right\">".$billed_qty."</td>
				<td align=\"right\">".$dispatch_qty."</td>
				<td align=\"right\">".$invoice_no."</td>
				<td align=\"right\">".$invoice_amount."</td>
				<td>".$dispatch_through."</td>
				<td>".$remarks."</td>
				<td>".$entry_date." ".$entry_time."</td>
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
