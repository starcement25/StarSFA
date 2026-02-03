<?php
ob_start();
session_start();
require("adminUtils.php");

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

$employee = $_REQUEST['employee'];
$employee_arg = str_replace(",","#",$employee);
$employee_arg = str_replace("'","^",$employee_arg);

$date_array = array();

$sql_order_date = "SELECT SUBSTRING(order_no,-14,8) AS no_order_date FROM order_header WHERE SUBSTRING(order_no,3,5) IN(".$employee.") AND (SUBSTRING(order_no,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') AND order_no LIKE 'N%'";
$res_order_date = mysql_query($sql_order_date);
while($row_order_date = mysql_fetch_array($res_order_date)){
	$no_order_date = $row_order_date['no_order_date'];
	if(!in_array($no_order_date,$date_array))
		array_push($date_array,$no_order_date);
}

$sql_no_payment = "SELECT SUBSTRING(receipt_no,-14,8) AS no_payment_date FROM payment_header WHERE SUBSTRING(receipt_no,3,5) IN(".$employee.") AND (SUBSTRING(receipt_no,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') AND receipt_no LIKE 'N%'";
$res_no_payment = mysql_query($sql_no_payment);
while($row_no_payment = mysql_fetch_array($res_no_payment)){
	$no_payment_date = $row_no_payment['no_payment_date'];
	if(!in_array($no_payment_date,$date_array))
		array_push($date_array,$no_payment_date);
}

$sql_no_stock = "SELECT SUBSTRING(transaction_id,-14,8) AS no_stock_date FROM stock_audit WHERE SUBSTRING(transaction_id,3,5) IN(".$employee.") AND (SUBSTRING(transaction_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') AND transaction_id LIKE 'NS%'";
$res_no_stock = mysql_query($sql_no_stock);
while($row_no_stock = mysql_fetch_array($res_no_stock)){
	$no_stock_date = $row_no_stock['no_stock_date'];
	if(!in_array($no_stock_date,$date_array))
		array_push($date_array,$no_stock_date);
}

sort($date_array);

if(!empty($date_array)){
	?>
    <table class="border" width="100%" style="border-collapse:collapse;" border="1">
      <tr class="TDHEAD">
      	<td>Date</td>
        <td>Customer Code</td>
        <td>Customer Name</td>
        <td width="15%">Remarks</td>
        <td>Type</td>
      </tr>
    <?
	$sql_emp = "SELECT emp_code, dns_emp_code, emp_name FROM employee_master WHERE emp_code IN(".$employee.")";
	$res_emp = mysql_query($sql_emp);
	while($row_emp = mysql_fetch_array($res_emp)){
		$dns_emp_code = $row_emp['dns_emp_code'];
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		
		$sql_location_check = "SELECT trans_id FROM location WHERE (trans_id LIKE 'NO%' OR trans_id LIKE 'NC%' OR trans_id LIKE 'NS%') AND emp_code = '".$emp_code."' AND (SUBSTRING(date,1,10) BETWEEN '".$start_date."' AND '".$end_date."')";
		$res_location_check = mysql_query($sql_location_check);
		$location_row_check = mysql_num_rows($res_location_check);
		
		if($location_row_check>0){
			echo "<tr><td colspan = '5' class = 'TDHEAD_SUB' align = 'center'>$dns_emp_code - $emp_name</td></tr>";
			
			foreach($date_array as $date_val){
				$sql_no_order_details = "SELECT customer_code, d_instruction FROM order_header WHERE SUBSTRING(order_no,-14,8) = '".$date_val."' AND SUBSTRING(order_no,3,5) = '".$emp_code."' AND order_no LIKE 'NO%'";
				$res_no_order_details = mysql_query($sql_no_order_details);
				$no_order_rows = mysql_num_rows($res_no_order_details);
				if($no_order_rows>0){
					$res_no_order_details = mysql_query($sql_no_order_details);
					while($row_no_order_details = mysql_fetch_array($res_no_order_details)){
						$customer_code = $row_no_order_details['customer_code'];
						$d_instruction = $row_no_order_details['d_instruction'];
						
						$sql_customer_name = "SELECT dns_customer_code, customer_name FROM customer_master WHERE customer_code = '".$customer_code."'";
						$res_customer_name = mysql_query($sql_customer_name);
						$row_customer_name = mysql_fetch_array($res_customer_name);
						$customer_name = $row_customer_name['customer_name'];
						$dns_customer_code = $row_customer_name['dns_customer_code'];
						
						echo "<tr>
								<td>".date('d-m-Y',strtotime($date_val))."</td>
								<td>".$dns_customer_code."</td>
								<td>".$customer_name."</td>
								<td>".$d_instruction."</td>
								<td>No Order</td>
							  </tr>";
					}
				}
				
				$sql_no_payment_details = "SELECT customer_code, p_remark FROM payment_header WHERE SUBSTRING(receipt_id,-14,8) = '".$date_val."' AND SUBSTRING(receipt_id,3,5) = '".$emp_code."' AND receipt_id LIKE 'NC%'";
				$res_no_payment_details = mysql_query($sql_no_payment_details);
				$no_payment_rows = mysql_num_rows($res_no_payment_details);
				if($no_payment_rows>0){
					$res_no_payment_details = mysql_query($sql_no_payment_details);
					while($row_no_payment_details = mysql_fetch_array($res_no_payment_details)){
						$customer_code = $row_no_payment_details['customer_code'];
						$p_remark = $row_no_payment_details['p_remark'];
						
						$sql_customer_name = "SELECT customer_name, dns_customer_code FROM customer_master WHERE customer_code = '".$customer_code."'";
						$res_customer_name = mysql_query($sql_customer_name);
						$row_customer_name = mysql_fetch_array($res_customer_name);
						$customer_name = $row_customer_name['customer_name'];
						$dns_customer_code = $row_customer_name['dns_customer_code'];
						
						echo "<tr>
								<td>".date('d-m-Y',strtotime($date_val))."</td>
								<td>".$dns_customer_code."</td>
								<td>".$customer_name."</td>
								<td>".$p_remark."</td>
								<td>No Collection</td>
							  </tr>";
					}
				}
				
				$sql_no_stock_details = "SELECT customer_code, remarks FROM stock_audit WHERE SUBSTRING(transaction_id,-14,8) = '".$date_val."' AND SUBSTRING(transaction_id,3,5) = '".$emp_code."' AND transaction_id LIKE 'NS%'";
				$res_no_stock_details = mysql_query($sql_no_stock_details);
				$no_stock_rows = mysql_num_rows($res_no_stock_details);
				if($no_stock_rows>0){
					$res_no_stock_details = mysql_query($sql_no_stock_details);
					while($row_no_stock_details = mysql_fetch_array($res_no_stock_details)){
						$customer_code = $row_no_stock_details['customer_code'];
						$remark = $row_no_stock_details['remarks'];
						
						$sql_customer_name = "SELECT customer_name, dns_customer_code FROM customer_master WHERE customer_code = '".$customer_code."'";
						$res_customer_name = mysql_query($sql_customer_name);
						$row_customer_name = mysql_fetch_array($res_customer_name);
						$customer_name = $row_customer_name['customer_name'];
						$dns_customer_code = $row_customer_name['dns_customer_code'];
						
						echo "<tr>
								<td>".date('d-m-Y',strtotime($date_val))."</td>
								<td>".$dns_customer_code."</td>
								<td>".$customer_name."</td>
								<td>".$remark."</td>
								<td>No Stock</td>
							  </tr>";
					}
				}
			}
		}
	}
	?>
    </table>
    <br />
    <br>
<div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
}
else{
	echo "No Records";
}
mysql_close($link);
?>


