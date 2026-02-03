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
	$emp_hierarchy_condition=" AND EM.emp_code IN(".$emp_hierarchy.") ";
	$emp_hierarchy_order_condition = " AND SUBSTRING(OH.order_no,-19,5) IN (".$emp_hierarchy.") ";
}
$employee=$_REQUEST['employee'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$distributor=$_REQUEST['distributor'];

if(strpos($distributor,",") == FALSE){
	$new_distributor = str_replace("'","",$distributor);
	$sql_distributor = "SELECT  customer_name FROM customer_master WHERE dns_customer_code = '".$new_distributor."'";
	$res_distributor = mysql_query($sql_distributor);
	$row_distributor = mysql_fetch_array($res_distributor);
	$new_distributor_name = $row_distributor['customer_name'];
}
else{
	$new_distributor_name = "All";
}

	$sql_order_header = "SELECT CM.customer_name,RM.route_name,CM.phone_no,CM.pin,PM.prod_desc,OH.order_no,SUBSTRING(OH.order_no,2,5) AS emp_code, 
						DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%d-%m-%Y') as order_date,OD.qty,OD.UOM,CM.rds_tag  FROM 
						order_header OH INNER JOIN customer_master CM INNER JOIN route_master RM INNER JOIN order_details OD INNER JOIN product_master PM 
						ON OH.customer_code=CM.customer_code AND CM.route_code=RM.route_code AND OH.order_no=OD.order_no AND OD.sku_code=PM.prod_code AND
						DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."' 
						AND SUBSTRING(OD.order_no,2,5) IN (".$employee.") AND CM.rds_tag IN(SELECT customer_code FROM customer_master WHERE dns_customer_code IN(".$distributor.")) ORDER BY CM.rds_tag ASC,DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') DESC,CM.customer_name ASC";
	$res_order_header = mysql_query($sql_order_header);
	$count_order_header=mysql_num_rows($res_order_header);

if(count($count_order_header)>0)
{
	$count = 1;
	echo "<table width='90%' border='1' style='border-collapse:collapse;' class='border datatable2' cellpadding='6px' align='center'>";
	 echo "<thead>";
	echo "<tr class='TDHEAD'><td colspan='14' align='center'>Distributor Report Of ".$new_distributor_name."</td></tr>";
	echo "<tr class='TDHEAD_SUB' align=\"center\">
			<td width=\"7%\">SL NO</td>
			<td width=\"10%\">Date</td>
			<td width=\"10%\">Distributor Name</td>
			<td width=\"10%\">Retailer Name</td>
			<td width=\"10%\">Employee</td>
			<td width=\"10%\">Area</td>
			<td width=\"8%\">Retailer Phone No </td>
			<td width=\"7%\">Pin Code </td>
			<td width=\"12%\">Product/SKU</td>
			<td width=\"9%\">Order Recieved(qty)</td>
			<td width=\"7%\">UOM</td>
		  </tr>";
		 echo "</thead><tbody>";  
		while($row_total_order=mysql_fetch_array($res_order_header)){
		$customer_name=$row_total_order['customer_name'];
		$route_name=$row_total_order['route_name'];
		$phone_no=$row_total_order['phone_no'];
		$pin=$row_total_order['pin'];
		$prod_desc=$row_total_order['prod_desc'];
		$order_date=$row_total_order['order_date'];
		$qty=$row_total_order['qty'];
		$UOM=$row_total_order['UOM'];
		$route_name=$row_total_order['route_name'];
		$emp_code=$row_total_order['emp_code'];
		$rds_tag=$row_total_order['rds_tag'];
		$sql_distributor_name = "SELECT  customer_name FROM customer_master WHERE customer_code = '".$rds_tag."'";
		$res_distributor_name = mysql_query($sql_distributor_name);
		$row_distributor_name = mysql_fetch_array($res_distributor_name);
		$distributor_name = $row_distributor_name['customer_name'];		
		$sql_emp = "SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
	    $res_emp = mysql_query($sql_emp);
		$row_emp=mysql_fetch_array($res_emp);
		$emp_name=$row_emp['emp_name'];
		
				echo "<tr>
					<td width=\"7%\">".$count."</td>
					<td width=\"10%\">".$order_date."</td>
					<td width=\"10%\">".$distributor_name."</td>
					<td width=\"10%\">".$customer_name."</td>
					<td width=\"10%\">".$emp_name."</td>
					<td width=\"10%\">".$route_name."</td>
					<td width=\"8%\">".$phone_no."</td>
					<td width=\"7%\">".$pin."</td>
					<td width=\"12%\">".$prod_desc."</td>
					<td align=\"right\" width=\"9%\">".$qty."</td>
					<td width=\"7%\">".$UOM."</td>
				  </tr>";
			
			$count++;
		}
	echo "</tbody>";
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
mysql_close($link);
?>