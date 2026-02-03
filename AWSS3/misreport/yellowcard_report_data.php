<?php
ob_start();
session_start();
require("adminUtils.php");

$current_date = date('Y-m-d');
$month_date = date('Y-m');
$current_month = date('m');
if($current_month == '01' || $current_month == '02' || $current_month == '03'){
	$previous_year = date('Y', strtotime('-1 year'));
	$previous_year_date = $previous_year."-04-01";
}
else{
	$previous_year_date = date('Y-04-01');
}
$employee = $_REQUEST['employee'];
$employee_arg = str_replace(",","#",$employee);
$employee_arg = str_replace("'","^",$employee_arg);

$subdealer = $_REQUEST['subdealer'];
//$start_date = $_REQUEST['start_date'];
//$end_date = $_REQUEST['end_date'];

$month_data = $_REQUEST['month_data'];

$year_month_split = explode("-",$month_data);
$monthNum  = $year_month_split[1];
$year = $year_month_split[0];
$monthName = date('M', mktime(0, 0, 0, $monthNum, 10));

if($_SESSION['admin_login']=="admin"){
	$emp_hierarchy='';
	$emp_hierarchy_condition='';
	$emp_hierarchy_condition_one='';
}
else
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition_one=' AND LO.emp_code IN('.$employee.')';
}

$zone = $_REQUEST['zone'];
$state = $_REQUEST['state'];
$branch = $_REQUEST['branch'];
$department = $_REQUEST['department'];

if(strpos($zone,",") == FALSE)	$zone = str_replace("'","",$zone);
else								$zone = "All";

if(strpos($state,",") == FALSE)	$state = str_replace("'","",$state);
else								$state = "All";

if(strpos($branch,",") == FALSE)	$branch = str_replace("'","",$branch);
else								$branch = "All";

if(strpos($department,",") == FALSE)	$department = str_replace("'","",$department);
else									$department = "All";


if(strpos($employee,",") == FALSE){
	$new_emp_code = str_replace("'","",$employee);
	$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$new_emp_code."'";
	$res_emp_name = mysql_query($sql_emp_name);
	$row_emp_name = mysql_fetch_array($res_emp_name);
	$new_emp_name = $row_emp_name['emp_name'];
}
else{
	$new_emp_name = "All";
}

$sql_emp_branch = "SELECT branch_code FROM employee_master WHERE emp_code IN(".$employee.")";
$res_emp_branch = mysql_query($sql_emp_branch);
$row_emp_branch = mysql_fetch_array($res_emp_branch);
$branch_emp_code = $row_emp_branch['branch_code'];

$sql_branch_name = "SELECT branch_name FROM branch_master WHERE branch_code = '".$branch_emp_code."'";
$res_branch_name = mysql_query($sql_branch_name);
$row_branch_name = mysql_fetch_array($res_branch_name);
$branch_name = $row_branch_name['branch_name'];

$header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Month: ".$monthName." ".$year;

$sql_yellow_card_details = "SELECT DATE_FORMAT(SUBSTRING(yellow_card_no,-14,8),'%d-%m-%Y') AS entry_date, SUBSTRING(yellow_card_no,-19,5) AS emp_code, yellow_card_no, DATE_FORMAT(challan_date,'%d-%m-%Y') AS chall_date, challan_no,  qty, qty_UOM FROM yellow_card_details WHERE customer_code IN(".$subdealer.") AND SUBSTRING(challan_date,1,7) = '".$month_data."'";
$res_yellow_card_details = mysql_query($sql_yellow_card_details);
$yellowcard_row_check = mysql_num_rows($res_yellow_card_details);
if($yellowcard_row_check>0){
	$sql_customer_master = "SELECT CM.route_code, CM.customer_name, CM.rds_tag, CM.owner_name, CM.owner_phone, CM.address FROM 
								customer_master CM,customer_route_emp_relation CRER
								WHERE CM.customer_code=CRER.customer_code AND CM.customer_code IN(".$subdealer.")";
	$res_customer_master = mysql_query($sql_customer_master);
	$row_customer_master = mysql_fetch_array($res_customer_master);
	
	$route_code = $row_customer_master['route_code'];
	$customer_name = $row_customer_master['customer_name'];
	$rds_tag = $row_customer_master['rds_tag'];
	$owner_name = $row_customer_master['owner_name'];
	$owner_phone = $row_customer_master['owner_phone'];
	$address = $row_customer_master['address'];
	
	$sql_route_name = "SELECT route_name FROM route_master WHERE route_code = '".$route_code."'";
	$res_route_name = mysql_query($sql_route_name);
	$row_route_name = mysql_fetch_array($res_route_name);
	$area = $row_route_name['route_name'];
	
	$sql_dealer = "SELECT dns_customer_code, customer_name FROM customer_master WHERE customer_code = '".$rds_tag."'";
	$res_dealer = mysql_query($sql_dealer);
	$row_dealer = mysql_fetch_array($res_dealer);
	$dealer_name = $row_dealer['customer_name'];
	$dns_customer_code = $row_dealer['dns_customer_code'];
	?>
    
    <table class="border" width="100%">
      <tr class="TDHEAD">
      	<td><?php echo $header_string; ?></td>
      </tr>
    </table>
    <br /><br />
    <div id="display_final">
    <table width="60%" style="border-collapse:collapse; border:1px solid #A92A61;"  cellpadding="2px">
      
      <tr>
        <td align="right">Route:</td>
        <td><?php echo $area; ?></td>
      </tr>
      <tr>
        <td align="right">Area/Dump:</td>
        <td><?php echo $branch_name; ?></td>
      </tr>
      <tr>
        <td align="right">Name Of Dealer:</td>
        <td><?php echo $dealer_name; ?></td>
      </tr>
      <tr>
        <td align="right">Dealer Code:</td>
        <td><?php echo $dns_customer_code; ?></td>
      </tr>
      <tr>
        <td align="right">Name Of Sub Dealer:</td>
        <td><?php echo $customer_name; ?></td>
      </tr>
      <tr>
        <td align="right">Contact Person:</td>
        <td><?php echo $owner_name; ?></td>
      </tr>
      <tr>
        <td align="right">Phone:</td>
        <td><?php echo $owner_phone; ?></td>
      </tr>
      <tr>
        <td align="right">Sub Dealer's Postal Address:</td>
        <td><?php echo $address; ?></td>
      </tr>
      <tr>
        <td align="right">Employee Name:</td>
        <td><?php echo $new_emp_name; ?></td>
      </tr>
      <tr>
        <td align="right">Month:</td>
        <td><?php echo $monthName." ".$year ?></td>
      </tr>
    </table>
    <br /><br />
    <table width="100%" style="border-collapse:collapse;" border="1"  cellpadding="2px">
      <tr class="TDHEAD" align="center">
        <td>SI</td>
        <td>Challan Date</td>
        <td>Challan No.</td>
        <td colspan="4">Quantity In Bags</td>
        <td>Total</td>
        <td>Sign &amp; Date Of Visit By Co. Officer</td>
      </tr>
      <tr class="TDHEAD" align="center">
        <td></td>
        <td></td>
        <td></td>
        <td>PPC</td>
        <td>PSC</td>
        <td>ARC</td>
        <td>OPC</td>
        <td></td>
        <td></td>
      </tr>
    <?php
	$count = 1;
	$ppc_total = 0;
	$psc_total = 0;
	$arc_total = 0;
	$opc_total = 0;
	$grand_total = 0;
	
	$res_yellow_card_details = mysql_query($sql_yellow_card_details);
	while($row_yellow_card_details = mysql_fetch_array($res_yellow_card_details)){
		$entry_date = $row_yellow_card_details['entry_date'];
		$yc_emp_code = $row_yellow_card_details['emp_code'];
		$yello_card_no = $row_yellow_card_details['yello_card_no'];
		$chall_date = $row_yellow_card_details['chall_date'];
		$challan_no = $row_yellow_card_details['challan_no'];
		$qty = $row_yellow_card_details['qty'];
		$qty_UOM = $row_yellow_card_details['qty_UOM'];
		
		$sql_yc_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$yc_emp_code."'";
		$res_yc_emp_name = mysql_query($sql_yc_emp_name);
		$row_yc_emp_name = mysql_fetch_array($res_yc_emp_name);
		$yc_emp_name = $row_yc_emp_name['emp_name'];
		
		echo "<tr>
				<td>".$count."</td>
				<td align=\"center\">".$chall_date."</td>
				<td align=\"right\">".$challan_no."</td>";
		
		if($qty_UOM == 'PPC'){
			echo "<td align=\"right\">".$qty."</td>
					<td align=\"center\">-</td>
					<td align=\"center\">-</td>
					<td align=\"center\">-</td>";
			$ppc_total += $qty;
		}
		else if($qty_UOM == 'PSC'){
			echo "<td align=\"center\">-</td>
					<td align=\"right\">".$qty."</td>
					<td align=\"center\">-</td>
					<td align=\"center\">-</td>";
			$psc_total += $qty;
		}
		else if($qty_UOM == 'ARC'){
			echo "<td align=\"center\">-</td>
					<td align=\"center\">-</td>
					<td align=\"right\">".$qty."</td>
					<td align=\"center\">-</td>";
			$arc_total += $qty;
		}
		else if($qty_UOM == 'OPC'){
			echo "<td align=\"center\">-</td>
					<td align=\"center\">-</td>
					<td align=\"center\">-</td>
					<td align=\"right\">".$qty."</td>";
			$opc_total += $qty;
		}
		$grand_total += $qty;
		
		echo "<td align=\"right\">".$qty."</td>
			  <td align=\"center\">".$yc_emp_name." ".$entry_date."</td>
			  </tr>";
		$count++;
	}
	?>
      <tr>
      	<td colspan="3" align="center"><b>Total</b></td>
        <td align="right"><?php echo $ppc_total; ?></td>
        <td align="right"><?php echo $psc_total; ?></td>
        <td align="right"><?php echo $arc_total; ?></td>
        <td align="right"><?php echo $opc_total; ?></td>
        <td align="right"><?php echo $grand_total; ?></td>
        <td></td>
      </tr>
    </table>
    </div>
    <?php
}
else{
	echo "No Records Found";
}

//$emp_hierarchy_condition_one = ' AND SUBSTRING(OH.order_no,2,5) IN('.$employee.') ';
?>


