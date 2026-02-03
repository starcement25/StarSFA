<?php
//echo "Work In Progress";die;
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
if($_GET['start_date'] != '' && $_GET['end_date'] != '')
{
	$start_date = $_GET['start_date'];
	$end_date = $_GET['end_date'];
	$table_columnname = 'No of days present';
	$condition = " AND (SUBSTRING(LO.date,1,10) BETWEEN '".$start_date."' AND '".$end_date."') ";
	$count_transid_condition = " ,COUNT(LO.trans_id) ";
	$primary_secondary_quantity_condition = " AND (SUBSTRING(order_no,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND  '".str_replace("-","",$end_date)."') ";
	$group_by = " GROUP BY EM.emp_code ";
	$attendance_condition = " COUNT(LO.trans_id) as attendance ";
	if($start_date == $end_date){
		$table_columnname = 'Attendance';
		$condition = " AND SUBSTRING(LO.date,1,10)='".$start_date."' ";
		$attendance_condition = " SUBSTRING(LO.date,12) as attendance ";
		$primary_secondary_quantity_condition = " AND SUBSTRING(order_no,-14,8) = '".str_replace("-","",$start_date)."' ";
	}
		
}
else
{
	$start_date = date('Y-m-d');
	$table_columnname = 'Attendance';
	$condition = " AND SUBSTRING(LO.date,1,10)='".$start_date."' ";
	$attendance_condition = " SUBSTRING(LO.date,12) as attendance ";
	$primary_secondary_quantity_condition = " AND (SUBSTRING(order_no,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND  '".str_replace("-","",$start_date)."') ";
}

$sql_get_custtype = "SELECT DISTINCT cust_type as sales_type FROM customer_master";
$res_get_custtype = mysqli_query($link,$sql_get_custtype);
while($row_get_custtype = mysqli_fetch_assoc($res_get_custtype)){
	$sale_type = $row_get_custtype['sales_type'];
	if($sale_type == '')
		$sale_type = 'R';
		
	if($sale_type == 'R')
		$retailer = 'true';
		
	if($sale_type == 'D')
		$distributor = 'true';
}

if($retailer == 'true' && $distributor == 'true'){
	$colspan = '9';
	$table_column = "<td>Primary</td>
					 <td colspan=\"2\">Secondary</td>";
	$secondary_row = "<td></td>
					  <td align=\"center\">Volume</td>
					  <td align=\"center\">Value</td>";
	$colspan_two = '2';
}
else if($retailer == 'true'){
	$colspan = '8';
	$table_column = "<td colspan=\"2\">Secondary</td>";
	$secondary_row = "<td align=\"center\">Volume</td>
					  <td align=\"center\">Value</td>";
}
else if($distributor == 'true'){
	$colspan = '7';
	$table_column = "<td>Primary</td>";
	$secondary_row = '<td></td>';
}

$non_active = '';
$active = '';
$count = 1;
//$sql_get_empdetails = "SELECT LO.emp_code, EM.emp_name,".$attendance_condition."FROM location LO, employee_master EM WHERE LO.trans_id LIKE 'A%' AND LO.emp_code = EM.emp_code AND EM.acedns != 'N'".$condition.$group_by."ORDER BY EM.emp_name ASC";
$sql_get_empdetails = "SELECT LO.emp_code,".$attendance_condition." FROM location LO WHERE LO.trans_id LIKE 'A%'".$condition." GROUP BY LO.emp_code ORDER BY LO.emp_code ASC";
$res_get_empdetails = mysqli_query($link,$sql_get_empdetails);
$total_rows = mysqli_fetch_assoc($res_get_empdetails);
if($total_rows>0){
	echo "<table width='100%' border='1' style='border-collapse:collapse;' class='border' cellpadding='6px'>";
	echo "<tr class='TDHEAD'><td colspan='".$colspan."' align='center'>Daily Activity Analysis</td></tr>";
	echo "<tr class='TDHEAD_SUB' align=\"center\">
			<td>SI</td>
			<td>Employee Name</td>
			<td>".$table_columnname."</td>
			<td>Productive Calls</td>
			<td>Non Productive Calls</td>
			<td>LPPC</td>
			$table_column
		  </tr>";
	echo "<tr class='TDHEAD_SUB'>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td align=\"center\">Avg. Line</td>
			$secondary_row
		  </tr>";
	$res_get_empdetails = mysqli_query($link,$sql_get_empdetails);
	while($row_get_empdetails = mysqli_fetch_assoc($res_get_empdetails)){
		
		$emp_code = $row_get_empdetails['emp_code'];
		//$emp_name = $row_get_empdetails['emp_name'];
		$attendance = $row_get_empdetails['attendance'];
		
		/*----> Get employee name <----*/
		$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_name = mysqli_query($link,$sql_emp_name);
		$row_emp_name = mysqli_fetch_assoc($res_emp_name);
		$emp_name = $row_emp_name['emp_name'];
	
			
		/*---------------------------------> Count of productive call <--------------------------------*/
		$sql_productive_call = "SELECT COUNT(LO.trans_id) FROM location LO WHERE LO.emp_code='".$emp_code."' AND (LO.trans_id LIKE 'O%' OR LO.trans_id LIKE 'S%' OR LO.trans_id LIKE 'P%') AND LO.trans_id NOT LIKE 'PA%' ".$condition;
		$res_productive_call = mysqli_query($link,$sql_productive_call);
		$row_productive_call = mysqli_fetch_assoc($res_productive_call);
		$productive_call = $row_productive_call['COUNT(LO.trans_id)'];
		
		/*---------------------------------> Count of non-productive call <--------------------------------*/
		$sql_non_productive_call = "SELECT COUNT(LO.trans_id) FROM location LO WHERE LO.emp_code='".$emp_code."' AND (LO.trans_id LIKE 'NO%' OR LO.trans_id LIKE 'NC%') ".$condition;
		$res_non_productive_call = mysqli_query($link,$sql_non_productive_call);
		$row_non_productive_call = mysqli_fetch_assoc($res_non_productive_call);
		$non_productive_call = $row_non_productive_call['COUNT(LO.trans_id)'];
					
		$total_productive_call += $productive_call;
		$total_non_productive_call += $non_productive_call;
		
		if($productive_call == 0 && $non_productive_call == 0){
			$color_name = '#FF0000';
			$non_active++;
		}
		else{
			$color_name = '';
			$active++;
		}
		
		$sql_check_emp_exist = "SELECT COUNT(order_no) FROM prev_order_counting_master WHERE SUBSTRING(order_no,2,5) = '".$emp_code."' AND order_no LIKE 'O%'".$primary_secondary_quantity_condition;
		$res_check_emp_exist = mysqli_query($link,$sql_check_emp_exist);
		$total_emp_row_check = mysqli_num_rows($res_check_emp_exist);
		
		if($total_emp_row_check>0){
			
			/*---------------------------------> Total primary sales <--------------------------------*/
			
			$sql_total_primary = "SELECT SUM(visit_qty) FROM prev_order_counting_master WHERE SUBSTRING(order_no,2,5) = '".$emp_code."' AND order_no LIKE 'O%' AND cust_type='D'".$primary_secondary_quantity_condition;
			$res_total_primary = mysqli_query($link,$sql_total_primary);
			$row_total_primary = mysqli_fetch_assoc($res_total_primary);
			$primary_quantity = $row_total_primary['SUM(visit_qty)'];
			$total_primary_quantity += $primary_quantity;
			
			/*---------------------------------> Count Order Header Data <--------------------------------*/
			$productive_call_count = '';
			$order_details_count = '';
			
			$sql_productive_call_order = "SELECT COUNT(DISTINCT order_no) AS order_header_count, COUNT(order_no) AS order_details_count FROM prev_order_counting_master WHERE order_no LIKE 'O%' AND SUBSTRING(order_no,2,5) = '".$emp_code."' ".$primary_secondary_quantity_condition;
			$res_productive_call_order = mysqli_query($link,$sql_productive_call_order);
			$row_productive_call_order = mysqli_fetch_assoc($res_productive_call_order);
			$productive_call_count = $row_productive_call_order['order_header_count'];
			$order_details_count = $row_productive_call_order['order_details_count'];
			
			$LPPC = $order_details_count/$productive_call_count;
			//$LPPC_total += $LPPC;
			
			$total_order_header_count += $productive_call_count;
			$total_order_details_count += $order_details_count;
			
			/*---------------------------------> Total secondary sales & amount <--------------------------------*/
			
			$sql_total_secondary = "SELECT SUM(visit_qty), SUM(amount) FROM prev_order_counting_master WHERE SUBSTRING(order_no,2,5) = '".$emp_code."' AND order_no LIKE 'O%' AND cust_type='R'".$primary_secondary_quantity_condition;
			$res_total_secondary = mysqli_query($link,$sql_total_secondary);
			$row_total_secondary = mysqli_fetch_assoc($res_total_secondary);
			$secondary_quantity = $row_total_secondary['SUM(visit_qty)'];
			$total_secondary_quantity += $secondary_quantity;
			$secondary_amount = $row_total_secondary['SUM(amount)'];
			$total_secondary_amount += $secondary_amount;
			
						
			if($retailer == 'true' && $distributor == 'true'){
				$table_column_data = "<td align='right'>".number_format($total_primary_quantity,2)."</td>
										<td align='right'>".$secondary_quantity."</td>
										<td align='right'>".number_format($secondary_amount,2)."</td>";
			}
			else if($retailer == 'true'){
				$table_column_data = "<td align='right'>".$secondary_quantity."</td>
									  <td align='right'>".number_format($secondary_amount,2)."</td>";
			}
			else if($distributor == 'true'){
				$table_column_data = "<td align='right'>".number_format($total_primary_quantity,2)."</td>";
			}
		}
		else{
			if($retailer == 'true' && $distributor == 'true'){
				$table_column_data = "<td align='right'>--</td>
										<td align='right'>--</td>
										<td align='right'>--</td>";
			}
			else if($retailer == 'true'){
				$table_column_data = "<td align='right'>--</td>
									  <td align='right'>--</td>";
			}
			else if($distributor == 'true'){
				$table_column_data = "<td align='right'>--</td>";
			}
		}
		
		echo "<tr>
					<td>".$count."</td>
					<td style='background:$color_name'>".$emp_name."</td>
					<td align='right'>".$attendance."</td>
					<td align='right'>".$productive_call."</td>
					<td align='right'>".$non_productive_call."</td>
					<td align='right'>".number_format($LPPC,2)."</td>".
					$table_column_data."
				  </tr>";
				  
		$count++;
	}
	if($retailer == 'true' && $distributor == 'true'){
		$table_column_data = "<td align='right'>".number_format($primary_quantity,2)."</td>
			<td align='right'>".$total_secondary_quantity."</td>
			<td align='right'>".number_format($total_secondary_amount,2)."</td>";
	}
	else if($retailer == 'true'){
		$table_column_data = "<td align='right'>".$total_secondary_quantity."</td>
			<td align='right'>".number_format($total_secondary_amount,2)."</td>";
	}
	else if($distributor == 'true'){
		$table_column_data = "<td align='right'>".number_format($primary_quantity,2)."</td>";
	}
	
	$LPPC_total = $total_order_details_count/$total_order_header_count;
	
	echo "<tr style='font-weight:bold;'>
			<td align='center'>Total</td>
			<td colspan='2' align='center'>Non-active:$non_active &nbsp; Active:$active</td>
			<td align='right'>".$total_productive_call."</td>
			<td align='right'>".$total_non_productive_call."</td>
			<td align='right'>".number_format($LPPC_total,2)."</td>
			$table_column_data
		  </tr>";
	echo "</table>";
	echo "<br>
<div style=\"width:100%;\" align=\"right\"><input name=\"print\" type=\"button\" value=\"Print\" id=\"print\" onClick=\"PrintElem('#display');\">&nbsp;
    <input name=\"export\" type=\"button\" value=\"Export\" id=\"btnExport\" onClick=\"exporttocsv();\" >
</div>";

}
else{
	echo "<font color='red'><strong>No records found</strong></font>";
}
mysqli_close($link);
?>
