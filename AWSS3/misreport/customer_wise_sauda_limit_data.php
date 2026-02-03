<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$emp_code = $_REQUEST['emp_code'];
$customer_code = $_REQUEST['cust_code'];
$cur_date=date('d-m-Y');

/*if($customer_code=='all')
{
	$emp_hierarchy = return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition = " emp_code IN (".$emp_hierarchy.") ";
	$sql_select_customer = "SELECT dns_customer_code, customer_name FROM customer_master  WHERE ".$emp_hierarchy_condition." AND acedns='Y' 
						ORDER BY customer_name ASC";
	$res_select_customer = mysql_query($sql_select_customer);
	while($row_select_customer = mysql_fetch_array($res_select_customer)){
		$customer_code_cond .= "'".$row_select_customer['dns_customer_code']."'".',';
	}
	$customer_code_cond=substr($customer_code_cond,0,-1);
}
else
{
	$customer_code_cond="'".$customer_code."'";
}*/

$sql_customer_saudalimit = "SELECT CM.customer_name,CM.dns_customer_code,CSL.sauda_limit,CSL.pending_qty  FROM 
							customer_master CM,customer_sauda_limit CSL 
							WHERE CM.dns_customer_code=CSL.customer_code AND CM.acedns='Y' AND CM.dns_customer_code IN(".$customer_code.") 
							GROUP BY CM.dns_customer_code ORDER BY CM.customer_name ASC";
$res_customer_saudalimit = mysql_query($sql_customer_saudalimit);
$total_rows = mysql_num_rows($res_customer_saudalimit);
$count = 1;
if($total_rows>0){
	?>
    <table width="100%" border="1" style="border-collapse:collapse;" class="BORDER" cellpadding="4">
     <tr>
        <td colspan="7" class="TDHEAD" align="center">Customer sauda limit report as on - <?php echo $cur_date; ?></td>
      </tr>
      <tr class="TDHEAD_SUB" align="center">
      	<td>SI</td>
        <td>Customer code</td>
        <td>Customer name</td>
        <td>Employee</td>
        <td>Sauda limit</td>
        <td>Pending qty</td>
        <td>Available sauda limit</td>
      </tr>
    <?php
	while($row_customer_saudalimit = mysql_fetch_array($res_customer_saudalimit)){
		$customer_name = $row_customer_saudalimit['customer_name'];
		$dns_customer_code = $row_customer_saudalimit['dns_customer_code'];
		$sauda_limit = $row_customer_saudalimit['sauda_limit'];
		$pending_qty = $row_customer_saudalimit['pending_qty'];
		
		$sql_emp = "SELECT EM.emp_name FROM employee_master EM,customer_master CM WHERE CM.emp_code=EM.emp_code 
					AND CM.acedns='Y' AND CM.dns_customer_code='".$dns_customer_code."'";
		$res_emp = mysql_query($sql_emp);
		$emp_name='';
		while($row_emp = mysql_fetch_array($res_emp)){
		$emp_name .= $row_emp['emp_name'].',';
		}
		$emp_name=substr($emp_name,0,-1);
		
		echo "<tr>
				<td>".$count."</td>
				<td>".$dns_customer_code."</td>
				<td>".$customer_name."</td>
				<td>".$emp_name."</td>
				<td align=\"right\">".$sauda_limit."</td>
				<td align=\"right\">".$pending_qty."</td>
				<td align=\"right\">".($sauda_limit-$pending_qty)."</td>
			  </tr>";
		$count++;
	}
}
else{
	echo "<strong><font color=\"red\">No records found</font></strong>";
}
mysql_close($link);
?>
