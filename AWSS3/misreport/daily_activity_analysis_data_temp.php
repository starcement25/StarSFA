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
	$primary_secondary_quantity_condition = "AND  (DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."')";
	$group_by = " GROUP BY EM.emp_code ";
	$attendance_condition = " COUNT(LO.trans_id) as attendance ";
	if($start_date == $end_date){
		$table_columnname = 'Attendance';
		$condition = " AND SUBSTRING(LO.date,1,10)='".$start_date."' ";
		$attendance_condition = " SUBSTRING(LO.date,12) as attendance ";
		$primary_secondary_quantity_condition = "AND  DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%Y-%m-%d')='".$start_date."'";
	}
		
}
else
{
	$start_date = date('Y-m-d');
	$table_columnname = 'Attendance';
	$condition = " AND SUBSTRING(LO.date,1,10)='".$start_date."' ";
	$attendance_condition = " SUBSTRING(LO.date,12) as attendance ";
	$primary_secondary_quantity_condition = "AND  DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%Y-%m-%d')='".$start_date."'";
}
$sales_type_array = array();
$sql_get_custtype = "SELECT DISTINCT cust_type as sales_type FROM customer_master";
$res_get_custtype = mysql_query($sql_get_custtype);
while($row_get_custtype = mysql_fetch_array($res_get_custtype)){
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
	$secondary_row = "<td align=\"center\">Volume</td>
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
	$secondary_row = '';
}


$count = 1;
$sql_get_empdetails = "SELECT LO.emp_code, EM.emp_name,".$attendance_condition."FROM location LO, employee_master EM WHERE LO.trans_id LIKE 'A%' AND LO.emp_code = EM.emp_code AND EM.acedns != 'N'".$condition.$group_by."ORDER BY EM.emp_name ASC";
$res_get_empdetails = mysql_query($sql_get_empdetails);
$total_rows = mysql_fetch_array($res_get_empdetails);
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
			<td></td>
			$secondary_row
		  </tr>";
	$res_get_empdetails = mysql_query($sql_get_empdetails);
	while($row_get_empdetails = mysql_fetch_array($res_get_empdetails)){
		$emp_code = $row_get_empdetails['emp_code'];
		$emp_name = $row_get_empdetails['emp_name'];
		$attendance = $row_get_empdetails['attendance'];
		
	/*---------------------------------> Count of productive call <--------------------------------*/
		$sql_productive_call = "SELECT COUNT(LO.trans_id) FROM location LO WHERE LO.emp_code='".$emp_code."' AND (LO.trans_id LIKE 'O%' OR LO.trans_id LIKE 'S%' OR LO.trans_id LIKE 'P%') AND LO.trans_id NOT LIKE 'PA%' ".$condition;
		$res_productive_call = mysql_query($sql_productive_call);
		$row_productive_call = mysql_fetch_array($res_productive_call);
		$productive_call = $row_productive_call['COUNT(LO.trans_id)'];
		
	/*---------------------------------> Count of non-productive call <--------------------------------*/
		$sql_non_productive_call = "SELECT COUNT(LO.trans_id) FROM location LO WHERE LO.emp_code='".$emp_code."' AND (LO.trans_id LIKE 'NO%' OR LO.trans_id LIKE 'NC%') ".$condition;
		$res_non_productive_call = mysql_query($sql_non_productive_call);
		$row_non_productive_call = mysql_fetch_array($res_non_productive_call);
		$non_productive_call = $row_non_productive_call['COUNT(LO.trans_id)'];
					
		$total_productive_call += $productive_call;
		$total_non_productive_call += $non_productive_call;
		
		if($productive_call == 0 && $non_productive_call == 0)
			$color_name = '#FF0000';
		else
			$color_name = '';
			
		/*---------------------------------> Total primary sales <--------------------------------*/
		$sql_total_primary = "SELECT SUM(OD.qty) FROM order_details OD, customer_master CM, order_header OH WHERE OH.order_no=OD.order_no AND SUBSTRING(OD.order_no,2,5)='".$emp_code."' ".$primary_secondary_quantity_condition." AND OH.customer_code=CM.customer_code AND CM.cust_type='D'";
		$res_total_primary = mysql_query($sql_total_primary);
		$row_total_primary = mysql_fetch_array($res_total_primary);
		$primary_quantity = $row_total_primary['SUM(OD.qty)'];
		$total_primary_quantity += $primary_quantity;
		
		/*---------------------------------> Count Order Header Data <--------------------------------*/
		$productive_call_count = '';
		$order_details_count = '';
		
		$sql_productive_call_order = "SELECT COUNT(LO.trans_id) FROM location LO, employee_master EM WHERE LO.emp_code='".$emp_code."' AND LO.emp_code=EM.emp_code AND LO.trans_id LIKE 'O%' ".$condition;
		$res_productive_call_order = mysql_query($sql_productive_call_order);
		$row_productive_call_order = mysql_fetch_array($res_productive_call_order);
		$productive_call_count = $row_productive_call_order['COUNT(LO.trans_id)'];
		
		$sql_order_details = "SELECT COUNT(OD.order_no) FROM order_details OD, location LO WHERE LO.emp_code = '".$emp_code."' AND OD.order_no = LO.trans_id ".$condition;
		$res_order_details = mysql_query($sql_order_details);
		$row_order_details = mysql_fetch_array($res_order_details);
		$order_details_count = $row_order_details['COUNT(OD.order_no)'];
		
		$LPPC = $order_details_count/$productive_call_count;
		$LPPC_total += $LPPC;
		
		/*---------------------------------> Total secondary sales & amount <--------------------------------*/
		$sql_total_secondary = "SELECT SUM(OD.qty), SUM(OD.amount) FROM order_details OD, customer_master CM, order_header OH WHERE OH.order_no=OD.order_no AND SUBSTRING(OD.order_no,2,5)='".$emp_code."' ".$primary_secondary_quantity_condition." AND OH.customer_code=CM.customer_code AND CM.cust_type='R'";
		$res_total_secondary = mysql_query($sql_total_secondary);
		$row_total_secondary = mysql_fetch_array($res_total_secondary);
		$secondary_quantity = $row_total_secondary['SUM(OD.qty)'];
		$total_secondary_quantity += $secondary_quantity;
		$secondary_amount = $row_total_secondary['SUM(OD.amount)'];
		$total_secondary_amount += $secondary_amount;
		
		/*---------------------------------> Total secondary amount <--------------------------------
		$sql_total_amount_secondary = "SELECT SUM(OD.amount) FROM order_details OD, customer_master CM, order_header OH WHERE OH.order_no=OD.order_no AND SUBSTRING(OD.order_no,2,5)='".$emp_code."' ".$primary_secondary_quantity_condition." AND OH.customer_code=CM.customer_code AND CM.cust_type='R'";
		$res_total_amount_secondary = mysql_query($sql_total_amount_secondary);
		$row_total_amount_secondary = mysql_fetch_array($res_total_amount_secondary);
		$secondary_amount = $row_total_amount_secondary['SUM(OD.amount)'];
		$total_secondary_amount += $secondary_amount;*/
		
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
	
	echo "<tr style='font-weight:bold;'>
			<td colspan='3' align='center'>Total</td>
			<td align='right'>".$total_productive_call."</td>
			<td align='right'>".$total_non_productive_call."</td>
			<td align='right'>".$LPPC_total."</td>
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
?>
