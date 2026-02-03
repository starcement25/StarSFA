<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

$start_date = date('Y-m-d');

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
	$emp_hierarchy_condition=" AND emp_code IN(".$emp_hierarchy.") ";
	$emp_hierarchy_order_condition = " AND SUBSTRING(OH.order_no,-19,5) IN (".$emp_hierarchy.") ";
	
}
$vertical=$_REQUEST['vertical'];
$state = $_REQUEST['state'];
$from_date_previous = $_REQUEST['start_date_prev'];
$to_date_previous = $_REQUEST['end_date_prev'];
$from_date_current = $_REQUEST['start_date_current'];
$to_date_current = $_REQUEST['end_date_current'];

if($state != ''){
	if($state == 'all')
		$state_condition = " state!=''";
	else
		if(strtoupper($_SESSION['nick_name']) == 'HALDIRAM'){
			$state_condition = " FIND_IN_SET(".$state.", state)";
		}
		else
		{
			$state_condition = " state IN(".$state.") ";
		}
}

/*--------------------> Condition to display data according to vertical name/all data<----------------------------*/
if($vertical!='')
{
	if(str_replace("'","",$vertical) == 'MACROMAN')
	{
		$vertical_condition = " AND SUBSTRING_INDEX(EM.vertical_value, ',', -1) LIKE 'M%' ";
		$vertical_condition_one = " AND POCM.vertical_value LIKE 'M%' ";
	}
	else
	{
		$vertical_condition = " AND SUBSTRING_INDEX(EM.vertical_value, ',', -1)=".$vertical." ";
		$vertical_condition_one = " AND POCM.vertical_value=".$vertical." ";
	}
}
$macroman_flag = 1;

$distinct_vertical_array = array();
/*---------------------------------> Condition to select DISTINCT vertical <--------------------------------*/
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$state_condition.$emp_hierarchy_condition." AND acedns='Y' ORDER BY emp_name ASC";
	$res_emp = mysqli_query($link,$sql_emp);
	while($row_emp = mysqli_fetch_assoc($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
	}
	$emp_code_string = rtrim($emp_code_string,",");
	
	/*$from_date_current=date('Y').'-'.'04'.'-'.'01';
	$to_date_current=(date('Y')+1).'-'.'03'.'-'.'31';
	$from_date_previous=(date('Y')-1).'-'.'04'.'-'.'01';
	$to_date_previous=date('Y').'-'.'03'.'-'.'31';*/
	
	$sql_total_historicdata = "SELECT CM.dns_customer_code,CM.customer_name,(SELECT dns_customer_code FROM customer_master WHERE customer_code = CM.rds_tag
				) AS rds_code,(SELECT customer_name FROM customer_master WHERE customer_code = CM.rds_tag) AS rds_name,EM.state,PM.prod_desc,EM.emp_name,SUM(CASE WHEN POCM.order_no LIKE 'O%' AND DATE_FORMAT(SUBSTRING(POCM.order_no,-14,8),'%Y-%m-%d') BETWEEN '".$from_date_previous."' AND '".$to_date_previous."' THEN POCM.visit_qty ELSE 0 END) 
									AS total_qty_prev_year, SUM(CASE WHEN POCM.order_no LIKE 'O%' AND DATE_FORMAT(SUBSTRING(POCM.order_no,-14,8),'%Y-%m-%d') BETWEEN '".$from_date_previous."' AND '".$to_date_previous."' THEN POCM.amount ELSE 0 END) 
									AS total_amount_prev_year,SUM(CASE WHEN POCM.order_no LIKE 'O%' AND DATE_FORMAT(SUBSTRING(POCM.order_no,-14,8),'%Y-%m-%d') BETWEEN '".$from_date_current."' AND '".$to_date_current."' THEN POCM.visit_qty ELSE 0 END) 
									AS total_qty_current_year, SUM(CASE WHEN POCM.order_no LIKE 'O%' AND DATE_FORMAT(SUBSTRING(POCM.order_no,-14,8),'%Y-%m-%d') BETWEEN '".$from_date_current."' AND '".$to_date_current."' THEN POCM.amount ELSE 0 END) 
									AS total_amount_current_year FROM employee_master EM INNER JOIN `prev_order_counting_master` POCM INNER JOIN 
	customer_master CM INNER JOIN product_master PM ON SUBSTRING(POCM.order_no,-19,5) IN (".$emp_code_string.") AND SUBSTRING(POCM.order_no,1,1) IN('O') ".$vertical_condition_one." AND POCM.customer_code=CM.customer_code AND POCM.product_code=PM.prod_code AND SUBSTRING(POCM.order_no,-19,5)=EM.emp_code AND POCM.cust_type='R' GROUP BY POCM.customer_code,POCM.product_code ORDER BY CM.customer_name ASC ";
	$rs_total_historicdata=mysqli_query($link,$sql_total_historicdata);
	$count_total_historicdata=mysqli_num_rows($rs_total_historicdata);

if(count($count_total_historicdata)>0)
{
	$count = 1;
	echo "<table width='100%' border='1' style='border-collapse:collapse;' class='border' cellpadding='6px'>";
	echo "<tr class='TDHEAD'><td colspan='14' align='center'>Historical Data (Secondary)</td></tr>";
	echo "<tr class='TDHEAD_SUB' align=\"center\">
			<td>SL NO</td>
			<td>RDS Code</td>
			<td>RDS Name</td>
			<td>Party Code</td>
			<td>Party Name</td>
			<td>SO/SS Name</td>
			<td>State</td>
			<td>City</td>
			<td>Vertical</td>
			<td>Product DESC</td>
			<td>".(date('Y')-1).'-'.date('Y')."</td>
			<td>Amount</td>
			<td>".date('Y').'-'.(date('Y')+1)."</td>
			<td>Amount</td>
		  </tr>";
		while($row_total_historicdata=mysqli_fetch_assoc($rs_total_historicdata)){
		$dns_customer_code=$row_total_historicdata['dns_customer_code'];
		$customer_name=$row_total_historicdata['customer_name'];
		$rds_code=$row_total_historicdata['rds_code'];
		$rds_name=$row_total_historicdata['rds_name'];
		$emp_name=$row_total_historicdata['emp_name'];
		$state=$row_total_historicdata['state'];
		$city='';
		$prod_desc=$row_total_historicdata['prod_desc'];
		$qty_prev_year=$row_total_historicdata['total_qty_prev_year'];
		$amount_prev_year=$row_total_historicdata['total_amount_prev_year'];
		$qty_current_year=$row_total_historicdata['total_qty_current_year'];
		$amount_current_year=$row_total_historicdata['total_amount_current_year'];
		
			//if(!in_array($emp_code,$emp_code_array))
			//{
				//array_push($emp_code_array,$emp_code);
				if($qty_prev_year >0 ||  $qty_current_year > 0)
				{
					echo "<tr>
						<td>".$count."</td>
						<td>".$rds_code."</td>
						<td>".$rds_name."</td>
						<td>".$dns_customer_code."</td>
						<td>".$customer_name."</td>
						<td>".$emp_name."</td>
						<td >".$state."</td>
						<td >".$city."</td>
						<td >".str_replace("'","",$vertical)."</td>
						<td >".$prod_desc."</td>
						<td align='right'>".$qty_prev_year."</td>
						<td align='right'>".number_format($amount_prev_year,2)."</td>
						<td align='right'>".$qty_current_year."</td>
						<td align='right'>".number_format($amount_current_year,2)."</td>
					  </tr>";
					//}
					$count++;
			  }
		}
	/*echo "<tr style='font-weight:bold;'>
			<td colspan='4'>Total</td>
			<td align='right'>".$total_productive_call."</td>
			<td align='right'>".$total_non_productive_call."</td>
			<td align='right'>".$total_primary_quantity."</td>
			<td align='right'>".$total_secondary_quantity."</td>
		  </tr>";*/
	echo "</table> <br />";
	?>
    <div style="width:70%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
      <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
	</div>
    <?php
}
else
{
	echo "<font color='red'><strong>No records found</strong></font>";
}
mysqli_close($link);
?>