<?php
//echo "Work In Progress";die;
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
	$emp_hierarchy_condition = " AND LO.emp_code IN (".$emp_hierarchy.") ";
}
	
if($_GET['start_date'] != '' )
{
	$start_date = $_GET['start_date'];
	$table_columnname = 'Attendance';
	$condition = " AND SUBSTRING(LO.date,1,10)='".$start_date."'";
	$count_transid_condition = " ,COUNT(LO.trans_id) ";
	$attendance_condition = " SUBSTRING(LO.date,12) as attendance ";
	$primary_secondary_quantity_condition = " AND SUBSTRING(order_no,-14,8) = '".str_replace("-","",$start_date)."' ";
	$group_by = " GROUP BY EM.emp_code ";		
}
else
{
	$start_date = date('Y-m-d');
	$table_columnname = 'Attendance';
	$condition = " AND SUBSTRING(LO.date,1,10)='".$start_date."' ";
	$attendance_condition = " SUBSTRING(LO.date,12) as attendance ";
	$primary_secondary_quantity_condition = " AND (SUBSTRING(order_no,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND  '".str_replace("-","",$start_date)."') ";
}
//echo $employeeval=str_replace("'","",$_GET['empcode']) ;
//$employee_upper_hierarchy=return_employee_upper_hierarchy($employeeval);
//echo $employee_upper_hierarchy=$employee_upper_hierarchy.","."'".$employeeval."'";
$employeeval=$_GET['empcode'];

$employee_upper_hierarchy_condition= "AND LO.emp_code IN (".$employee_upper_hierarchy.")";
$vertical=$_GET['vertical'] ;
if($vertical=='all')
	{
		$vertical_condition='';
	}
	else
	{
		$vertical_condition=" AND PM.vertical_value='".$vertical."'";
	}
//$verticalparts=str_replace("'","",$vertical);
$verticalarray=array();
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
	$colspan = '16';
	if($vertical=='all')
	{
		
		$sqldistinctvertical="SELECT DISTINCT vertical_value FROM product_master WHERE acedns='Y'";
		$rsdistinctvertical=mysqli_query($link,$sqldistinctvertical);
		$countdistinctvertical=mysqli_num_rows($rsdistinctvertical);
		while($rowdistinctvertical=mysqli_fetch_assoc($rsdistinctvertical))
		{
			$secondary_row.= "<td align=\"center\">$rowdistinctvertical[vertical_value]</td>";
			array_push($verticalarray,$rowdistinctvertical['vertical_value']);
		}
		$colspan_vertical=$countdistinctvertical+1;
	}
	else
	{
	 $colspan_vertical='2';
	 $vertical=str_replace("'","",$vertical);
	 $secondary_row= "<td align=\"center\">$vertical</td>";
	 array_push($verticalarray,$vertical);
	}
	$table_column = "<td colspan=\"$colspan_vertical\">Primary</td>
					 <td colspan=\"$colspan_vertical\">Secondary</td>";

	$secondary_row.="<td align=\"center\">Total</td>";
	$colspan_two = '3';
}
else if($retailer == 'true'){
	$colspan = '10';
	$table_column = "<td colspan=\"2\">Secondary</td>";
	$secondary_row = "<td align=\"center\">Volume</td>
					  <td align=\"center\">Value</td>";
}
else if($distributor == 'true'){
	$colspan = '10';
	$table_column = "<td colspan=\"2\">Primary</td>";
	$secondary_row = '<td align=\"center\">Volume</td>
					  <td align=\"center\">Value</td>';
}

$non_active = '';
$active = '';
$count = 1;
if(stk_audit=='yes' || retailer_care=='yes'){
	$stk_audit_customer_TR='<td width="12%" align="left" style="padding-left:20px;">No of Stock Audit</td>';
	$stk_audit_customer_TR_secondary='<td width="12%" align="left" style="padding-left:20px;"></td>';
}
else
{
	$stk_audit_customer_TR='';
	$stk_audit_customer_TD='';
	$stk_audit_customer_TR_secondary='';
}
$sql_get_empdetails = "SELECT LO.emp_code,SUM(CASE WHEN LO.trans_id LIKE 'O%' THEN 1 ELSE 0
						END ) AS total_order_count,COUNT( DISTINCT (CASE WHEN POCM.order_no LIKE 'O%' THEN POCM.customer_code END )) AS productive_call, 
						COUNT( DISTINCT (CASE WHEN POCM.order_no LIKE 'NO%' THEN POCM.customer_code END )) AS non_productive_call, 
						COUNT(CASE WHEN POCM.order_no LIKE 'O%' THEN POCM.order_no END ) AS order_details_count,
						SUM(CASE WHEN POCM.cust_type='D' AND POCM.order_no LIKE 'O%' THEN POCM.amount ELSE 0 END ) AS primary_amount, 
SUM(CASE WHEN POCM.cust_type='R' AND POCM.order_no LIKE 'O%' THEN POCM.amount ELSE 0 END ) AS secondary_amount FROM location LO LEFT JOIN `prev_order_counting_master` 
POCM ON POCM.order_no=LO.trans_id WHERE SUBSTRING(LO.trans_id,1,1) IN('A','N','O') ".$condition." AND LO.emp_code IN(".$employeeval.")  GROUP BY LO.emp_code ORDER BY LO.emp_code ASC";
/*$sql_get_empdetails = "SELECT LO.emp_code,".$attendance_condition.",SUM(CASE WHEN LO.trans_id LIKE 'O%' THEN 1 ELSE 0
						END ) AS total_order_count FROM location LO WHERE (LO.trans_id LIKE 'A%' OR LO.trans_id LIKE 'O%' ) ".$condition.$emp_hierarchy_condition." 
						GROUP BY LO.emp_code ORDER BY LO.emp_code ASC";*/
$res_get_empdetails = mysqli_query($link,$sql_get_empdetails);
$total_rows = mysqli_fetch_assoc($res_get_empdetails);
if($total_rows>0){

	echo "<table width='100%' border='1' style='border-collapse:collapse;' class='border' cellpadding='6px'>";
	echo "<tr class='TDHEAD'><td colspan='".$colspan."' align='center'>Daily Activity Analysis</td></tr>";
	echo "<tr class='TDHEAD_SUB' align=\"center\">
			<td>SI</td>
			<td>Employee Name</td>
			<td>".$table_columnname."</td>
			<td>Total Calls</td>
			<td>Productive Calls</td>
			<td>Non Productive Calls</td>
			<td>LPPC</td>
			$table_column".$stk_audit_customer_TR."
		  </tr>";
	echo "<tr class='TDHEAD_SUB'>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td align=\"center\">Avg. Line</td>
			$secondary_row".$secondary_row.$stk_audit_customer_TR_secondary."
		  </tr>";
	$res_get_empdetails = mysqli_query($link,$sql_get_empdetails);
	while($row_get_empdetails = mysqli_fetch_assoc($res_get_empdetails)){
		
		$emp_code = $row_get_empdetails['emp_code'];
		$total_order_count = $row_get_empdetails['total_order_count'];
		$table_column_data_primary='';
		$table_column_data_secondary='';
		$sub_total_primary_amount='';
		$sub_total_secondary_amount='';
		
		/*----> Get employee name <----*/
		$sql_emp_name = "SELECT EM.emp_name,SUBSTRING(LO.date,12) AS attendance FROM employee_master EM,location LO 
						WHERE LO.emp_code=EM.emp_code AND EM.emp_code = '".$emp_code."' ".$condition;
		$res_emp_name = mysqli_query($link,$sql_emp_name);
		$row_emp_name = mysqli_fetch_assoc($res_emp_name);
		$emp_name = $row_emp_name['emp_name'];
		$attendance = $row_emp_name['attendance'];

		$sqlstockaudit="SELECT COUNT(LO.trans_id)AS total_stk_audit
						FROM location LO WHERE  LO.emp_code='".$emp_code."' 
						AND (LO.trans_id LIKE 'SE%')  AND SUBSTRING(LO.date,1,10)='".$start_date."'";
		$rsstockaudit=mysqli_query($link,$sqlstockaudit) or die(mysqli_error()." Error in total no transaction: ".$sqlstockaudit);
		$rowstockaudit=mysqli_fetch_assoc($rsstockaudit);
		if(stk_audit=='yes' || retailer_care=='yes'){
		$stk_audit_customer_TD="<td align=\"right\" >".$rowstockaudit['total_stk_audit']."</td>";
		$garnd_total_stk_audit=$garnd_total_stk_audit+$rowstockaudit['total_stk_audit'];
		}
		/*------------> Count of productive  and non-productive call <--------------------------------
		$sql_productive_nonproductive_call = "SELECT SUM(CASE WHEN LO.trans_id LIKE 'O%' THEN 1 ELSE 0 END ) AS productive_call, SUM(CASE WHEN LO.trans_id LIKE 'NO%' THEN 1 ELSE 0 END ) AS non_productive_call  FROM location LO WHERE LO.emp_code='".$emp_code."'".$condition;
		$res_productive_nonproductive_call = mysqli_query($link,$sql_productive_nonproductive_call);
		$row_productive_nonproductive_call = mysqli_fetch_assoc($res_productive_nonproductive_call);
		$productive_call = $row_productive_nonproductive_call['productive_call'];
		$non_productive_call = $row_productive_nonproductive_call['non_productive_call'];*/					
				
		/*$sql_productive_call = "SELECT COUNT( DISTINCT customer_code ) AS productive_call FROM prev_order_counting_master WHERE SUBSTRING(order_no,-19,5) = '".$emp_code."'".$primary_secondary_quantity_condition." AND order_no LIKE 'O%'";
		$res_productive_call = mysqli_query($link,$sql_productive_call);
		$row_productive_call = mysqli_fetch_assoc($res_productive_call);*/
		$productive_call = $row_get_empdetails['productive_call'];
		
		/*$sql_nonprod_call = "SELECT COUNT( DISTINCT customer_code ) AS non_productive_call FROM prev_order_counting_master WHERE SUBSTRING(order_no,-19,5) = '".$emp_code."'".$primary_secondary_quantity_condition." AND order_no LIKE 'NO%'";
		$res_nonprod_call = mysqli_query($link,$sql_nonprod_call);
		$row_nonprod_call = mysqli_fetch_assoc($res_nonprod_call);*/
		$non_productive_call = $row_get_empdetails['non_productive_call'];
		$total_call_individual=$productive_call+$non_productive_call;
		
		$total_productive_call += $productive_call;
		$total_non_productive_call += $non_productive_call;
		$total_call +=$total_call_individual;
		
		if($productive_call == 0 && $non_productive_call == 0){
			$color_name = 'FF0000';
			$non_active++;
		}
		else{
			$color_name = '';
			$active++;
		}
		$LPPC=0;
		if($total_order_count>0){
			$productive_call_count = '';
			$order_details_count = '';
			/*--------------------> Total primary and secondary sales & amount<--------------------------------*/
			/*$sql_total_primary_secondary = "SELECT COUNT(DISTINCT customer_code) AS order_header_count, COUNT(order_no) AS order_details_count,
											SUM(CASE WHEN cust_type='D'  THEN visit_qty ELSE 0 END ) AS primary_quantity,
											SUM(CASE WHEN cust_type='R'  THEN visit_qty ELSE 0 END ) AS secondary_quantity,
											SUM(CASE WHEN cust_type='D'  THEN amount ELSE 0 END ) AS primary_amount, 
											SUM(CASE WHEN cust_type='R'  THEN amount ELSE 0 END ) AS secondary_amount
											FROM prev_order_counting_master WHERE SUBSTRING(order_no,2,5) = '".$emp_code."' 
											AND order_no LIKE 'O%'".$primary_secondary_quantity_condition;
			$res_total_primary_secondary = mysqli_query($link,$sql_total_primary_secondary);
			$row_total_primary_secondary = mysqli_fetch_assoc($res_total_primary_secondary);*/
			$primary_quantity = $row_get_empdetails['primary_quantity'];
			$total_primary_quantity += $primary_quantity;
			
			$secondary_quantity = $row_get_empdetails['secondary_quantity'];
			$total_secondary_quantity += $secondary_quantity;
			$secondary_amount = $row_get_empdetails['secondary_amount'];
			$primary_amount = $row_get_empdetails['primary_amount'];
			$total_secondary_amount += $secondary_amount;
			$total_primary_amount += $primary_amount;

			/*---------------------------------> Count Order Header Data <--------------------------------*/
			//$productive_call_count = $row_total_primary_secondary['order_header_count'];
			$productive_call_count =$productive_call;
			$order_details_count = $row_get_empdetails['order_details_count'];
			
			$LPPC = $order_details_count/$productive_call_count;
			//$LPPC_total += $LPPC;
			
			$total_order_header_count += $productive_call_count;
			$total_order_details_count += $order_details_count;
			
			/*---------------------------------> Total secondary sales & amount <--------------------------------*/
			print_r($verticalarray);
			if($retailer == 'true' && $distributor == 'true'){
				
				for($i=0;$i<2;$i++)
				{
					foreach($verticalarray as $verticalvalfinal)
					{
						$sqlverticaltotal="SELECT SUM(CASE WHEN POCM.cust_type='D' AND POCM.order_no LIKE 'O%' THEN POCM.amount ELSE 0 END ) AS primary_amount, 
											SUM(CASE WHEN POCM.cust_type='R' AND POCM.order_no LIKE 'O%' THEN POCM.amount ELSE 0 END ) AS secondary_amount
											FROM `prev_order_counting_master` POCM,product_master PM
											WHERE POCM.product_code=PM.prod_code AND SUBSTRING(POCM.visit_date,1,10)='".$start_date."' 
											AND SUBSTRING(POCM.order_no,2,5)='".$emp_code."' AND PM.vertical_value='".$verticalvalfinal."'";
						$rsverticaltotal=mysqli_query($link,$sqlverticaltotal);
						$rowverticaltotal=mysqli_fetch_assoc($rsverticaltotal);
						${primary_amount.$verticalvalfinal}=$rowverticaltotal['primary_amount'];
						${secondary_amount.$verticalvalfinal}=$rowverticaltotal['secondary_amount'];
						if($i==0)
					    {
						 $sub_total_primary_amount=$sub_total_primary_amount+${primary_amount.$verticalvalfinal};
					    }
					   if($i==1)
					   {
						$sub_total_secondary_amount=$sub_total_secondary_amount+${secondary_amount.$verticalvalfinal};
					   }
					   if($i==0)
					   {
					     $table_column_data_primary.= "<td align='right'>".number_format(${primary_amount.$verticalvalfinal},2)."</td>";
					   }
					   if($i==1)
					   {
						  $table_column_data_secondary.= "<td align='right'>".number_format(${secondary_amount.$verticalvalfinal},2)."</td>";
					   }
					}
				   if($i==0)
				   {
					 $table_column_data_primary.= "<td align='right'>".number_format($sub_total_primary_amount,2)."</td>";
				   }
				   if($i==1)
				   {
					  $table_column_data_secondary.= "<td align='right'>".number_format($sub_total_secondary_amount,2)."</td>";
				   }
				}//end for loop
			}
		}
		else{
			if($retailer == 'true' && $distributor == 'true'){
				for($i=0;$i<2;$i++)
				{
					foreach($verticalarray as $verticalvalfinal)
					{
						$table_column_data .= "<td align='right'>--</td>";
					}
					$table_column_data .= "<td align='right'>--</td>";
				}
			}
		}
		
		echo "<tr>
				<td>".$count."</td>
				<td style='background:$color_name'>".$emp_name."</td>
				<td align='right'>".$attendance."</td>
				<td align='right'>".$total_call_individual."</td>
				<td align='right'>".$productive_call."</td>
				<td align='right'>".$non_productive_call."</td>
				<td align='right'>".number_format($LPPC,2)."</td>".
				$table_column_data_primary.$table_column_data_secondary.$stk_audit_customer_TD."
			  </tr>";
				  
		$count++;
	}
	if($retailer == 'true' && $distributor == 'true'){
		for($i=0;$i<2;$i++)
		 {
			foreach($verticalarray as $verticalvalfinal)
			{
				 if($i==0)
				 {
					$table_column_data_primary_total.= "<td align='right'>".number_format(${total_primary_amount.$verticalvalfinal},2)."</td>";
				 }
				 else
				 {
					$table_column_data_secondary_total.= "<td align='right'>".number_format(${total_secondary_amount.$verticalvalfinal},2)."</td>";
				 }
			}
		  if($i==0)
		   {
			 $table_column_data_primary_total.= "<td align='right'>".number_format($total_primary_amount,2)."</td>";
		   }
		   else
		   {
			  $table_column_data_secondary_total.= "<td align='right'>".number_format($total_secondary_amount,2)."</td>";
		   }
		 }
	}
	
	$LPPC_total = $total_order_details_count/$total_order_header_count;
	if(stk_audit=='yes' || retailer_care=='yes'){
					$stk_audit_customer_TD_total="<td align=\"right\" valign=\"top\" >
		".number_format($garnd_total_stk_audit,2)."</td>";
		}
	echo "<tr style='font-weight:bold;'>
			<td align='center'>Total</td>
			<td colspan='2' align='center'>Non-active:$non_active &nbsp; Active:$active</td>
			<td align='right'>".$total_call."</td>
			<td align='right'>".$total_productive_call."</td>
			<td align='right'>".$total_non_productive_call."</td>
			<td align='right'>".number_format($LPPC_total,2)."</td>
			$table_column_data_primary_total".$table_column_data_secondary_total.$stk_audit_customer_TD_total."
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
