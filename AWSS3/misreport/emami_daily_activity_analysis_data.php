<?php
ob_start();
	session_start();
	error_reporting(0);
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

/*---------------------------------> ADMIN/Employee hierarchy condition <--------------------------------*/
if($_SESSION['admin_login']=="admin"){
	$emp_hierarchy='';
	$emp_hierarchy_condition="";
	$emp_hierarchy_condition_one="";
	$emp_hierarchy_order_condition = "";
}
else
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition=" AND EM.emp_code IN(".$emp_hierarchy.") ";
	$emp_hierarchy_order_condition = " AND SUBSTRING(OH.order_no,-19,5) IN (".$emp_hierarchy.") ";
	
}

/*---------------------------------> Date check condition <--------------------------------*/
if($_GET['start_date'] != '' && $_GET['end_date'] != ''){
	$start_date = $_GET['start_date'];
	$end_date = $_GET['end_date'];
	$table_columnname = 'No of days present';
	$condition = " AND (SUBSTRING(LO.date,1,10) BETWEEN '".$start_date."' AND '".$end_date."') ";
	$group_by = " GROUP BY EM.emp_code ";
	$attendance_condition = " COUNT(LO.trans_id) as attendance ";
	$oppc_condition = " AND (DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."') ";
	if($start_date == $end_date){
		$table_columnname = 'Attendance';
		$condition = " AND SUBSTRING(LO.date,1,10)='".$start_date."' ";
		$attendance_condition = " SUBSTRING(LO.date,12) as attendance ";
		$oppc_condition = " AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d')='".$start_date."' ";
	}
		
}
else{
	$start_date = date('Y-m-d');
	$table_columnname = 'Attendance';
	$condition = " AND SUBSTRING(LO.date,1,10)='".$start_date."' ";
	$attendance_condition = " SUBSTRING(LO.date,12) as attendance ";
	$oppc_condition = " AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d')='".$start_date."' ";
}

$count = 1;
/*---------------------------------> Employee Details <--------------------------------*/
$sql_get_empdetails = "SELECT LO.emp_code, EM.emp_name,".$attendance_condition."FROM location LO, employee_master EM WHERE LO.trans_id LIKE 'A%' AND LO.emp_code = EM.emp_code AND EM.acedns != 'N'".$emp_hierarchy_condition.$condition.$group_by."ORDER BY EM.emp_name ASC";
$res_get_empdetails = mysql_query($sql_get_empdetails);
$total_rows = mysql_fetch_array($res_get_empdetails);
if($total_rows>0){
	echo "<table width='100%' border='1' style='border-collapse:collapse;' class='border' cellpadding='6px'>";
	echo "<tr class='TDHEAD'><td colspan='8' align='center'>Daily Activity Analysis</td></tr>";
	echo "<tr class='TDHEAD_SUB' align=\"center\">
			<td>SI</td>
			<td>Employee Name</td>
			<td>".$table_columnname."</td>
			<td>Total<br>Calls</td>
			<td>Productive<br>Calls</td>
			<td>OPPC</td>
			<td>LPPC</td>
			<td>Secondary<br>Sales</td>
		  </tr>";
	$res_get_empdetails = mysql_query($sql_get_empdetails);
	while($row_get_empdetails = mysql_fetch_array($res_get_empdetails)){
		$emp_code = $row_get_empdetails['emp_code'];
		
		$sql_check_menu_access = "SELECT emp_code FROM menu_access WHERE emp_code='".$emp_code."' AND not_accessible_menu = 'order'";
		$res_check_menu_access = mysql_query($sql_check_menu_access);
		$menu_access_rows = mysql_num_rows($res_check_menu_access);
		if($menu_access_rows == 0){
		
		$emp_name = $row_get_empdetails['emp_name'];
		$attendance = $row_get_empdetails['attendance'];
		$total_callsmade_emp = 0;
		
	/*---------------------------------> Count of productive call <--------------------------------*/
		$sql_productive_call = "SELECT COUNT(LO.trans_id) FROM location LO, employee_master EM WHERE LO.emp_code='".$emp_code."' AND LO.emp_code=EM.emp_code AND LO.trans_id LIKE 'O%' ".$condition;
		$res_productive_call = mysql_query($sql_productive_call);
		$row_productive_call = mysql_fetch_array($res_productive_call);
		$productive_call = $row_productive_call['COUNT(LO.trans_id)'];
		
	/*---------------------------------> Count of non-productive call <--------------------------------*/
		$sql_non_productive_call = "SELECT COUNT(LO.trans_id) FROM location LO, employee_master EM WHERE LO.emp_code='".$emp_code."' AND LO.emp_code=EM.emp_code AND (LO.trans_id LIKE 'NO%' OR LO.trans_id LIKE 'NC%') ".$condition;
		$res_non_productive_call = mysql_query($sql_non_productive_call);
		$row_non_productive_call = mysql_fetch_array($res_non_productive_call);
		$non_productive_call = $row_non_productive_call['COUNT(LO.trans_id)'];
		
		$total_callsmade_emp = ($productive_call+$non_productive_call);
					
		$total_productive_call += $productive_call;
		$total_calls += $total_callsmade_emp;
				
		if($productive_call == 0 && $non_productive_call == 0)
			$color_name = '#FF0000';
		else
			$color_name = '';
			
	
		$oppc_count = 0;
		$lppc_count = 0;
		$sql_order_no = "SELECT OH.order_no FROM order_header OH WHERE SUBSTRING(OH.order_no,2,5)='".$emp_code."' AND OH.order_no LIKE 'O%'".$oppc_condition;
		$res_order_no = mysql_query($sql_order_no);
		while($row_order_no = mysql_fetch_array($res_order_no)){
			$order_no = $row_order_no['order_no'];
			
			/*---------------------------------> Count of OPPC <--------------------------------*/
			$sql_OPPC = "SELECT COUNT(DISTINCT PGM.product_group_code) as oppc_count FROM product_group_master PGM, product_master PM, order_header OH, order_details OD WHERE OH.order_no LIKE 'O%' AND SUBSTRING(OH.order_no,2,5)='".$emp_code."'".$oppc_condition."AND OH.order_no=OD.order_no AND OH.order_no = '".$order_no."' AND OD.sku_code=PM.prod_code AND PM.product_group_code=PGM.product_group_code";
			$res_OPPC = mysql_query($sql_OPPC);
			$row_OPPC = mysql_fetch_array($res_OPPC);
			$oppc_count += $row_OPPC['oppc_count'];
			
			/*---------------------------------> Count of LPPC <--------------------------------*/
			$sql_LPPC = "SELECT COUNT(DISTINCT OD.sku_code) as lppc_count FROM order_details OD WHERE OD.order_no = '".$order_no."'";
			$res_LPPC = mysql_query($sql_LPPC);
			$row_LPPC = mysql_fetch_array($res_LPPC);
			$lppc_count += $row_LPPC['lppc_count'];
		}
		$oppc = number_format($oppc_count/$productive_call,2);
		$lppc = number_format($lppc_count/$productive_call,2);
		$total_oppc += $oppc;
		$total_lppc += $lppc;
		
		/*---------------------------------> Total secondary sales <--------------------------------*/
		$sql_total_secondary = "SELECT SUM(OD.qty) FROM order_details OD, customer_master CM, order_header OH WHERE OH.order_no=OD.order_no AND SUBSTRING(OD.order_no,2,5)='".$emp_code."'".$oppc_condition."AND OH.customer_code=CM.customer_code AND CM.cust_type='R'";
		$res_total_secondary = mysql_query($sql_total_secondary);
		$row_total_secondary = mysql_fetch_array($res_total_secondary);
		$secondary_quantity = $row_total_secondary['SUM(OD.qty)'];
		if($secondary_quantity == '')
			$secondary_quantity = 0;
		$total_secondary_quantity += $secondary_quantity;
					
		echo "<tr>
				<td>".$count."</td>
				<td style='background:$color_name'>".$emp_name."</td>
				<td align='right'>".$attendance."</td>
				<td align='right'>".$total_callsmade_emp."</td>
				<td align='right'>".$productive_call."</td>
				<td align='right'>".$oppc."</td>
				<td align='right'>".$lppc."</td>
				<td align='right'>".$secondary_quantity."</td>
			  </tr>";
			  
		$count++;
		}
	}
		
	echo "<tr style='font-weight:bold;'>
			<td colspan='3' align='center'>Total</td>
			<td align='right'>".$total_calls."</td>
			<td align='right'>".$total_productive_call."</td>
			<td align='right'>".number_format($total_oppc,2)."</td>
			<td align='right'>".number_format($total_lppc,2)."</td>
			<td align='right'>".$total_secondary_quantity."</td>
		  </tr>";
	echo "</table>";

}
else{
	echo "<font color='red'><strong>No records found</strong></font>";
}
mysql_close($link);
?>
