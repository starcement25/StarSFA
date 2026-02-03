<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");


$emp_code = $_REQUEST['emp_code'];
$customer_name = $_REQUEST['customer_name'];

if($emp_code != ''){
	//$emp_hierarchy=return_employee_hierarchy($emp_code);
	//$emp_hierarchy_condition=' AND CM.emp_code IN('.$emp_hierarchy.') ';
	$emp_hierarchy_condition=" AND CM.emp_code IN(".$emp_code.") ";
}
else if($customer_name != ''){
	//$customer_condition = " AND OA.customer_name LIKE '%".$customer_name."%' ";
	$customer_condition = " AND OA.customer_code IN(".$customer_name.") ";
}

$sql_outstanding_ageing = "SELECT OA.*  FROM outstanding_ageing OA, customer_master CM WHERE OA.customer_code=CM.customer_code ".$emp_hierarchy_condition.$customer_condition." ORDER BY OA.customer_name ASC";
$res_outstanding_ageing = mysql_query($sql_outstanding_ageing);
$total_rows = mysql_num_rows($res_outstanding_ageing);
$count = 1;
if($total_rows>0){
	?>
    <table width="100%" border="1" style="border-collapse:collapse;" cellpadding="4" class="border">
      <tr class="TDHEAD_SUB" align="center">
      	<td rowspan="2">SI</td>
        <td rowspan="2">Customer Name</td>
        <td rowspan="2">Outstanding Amount</td>
        <td colspan="5">Outstanding</td>
      </tr>
      <tr class="TDHEAD_SUB" align="center">
      	<td>0 to 15 days</td>
        <td>16 to 30 days</td>
        <td>31 to 45 days</td>
        <td>46 to 90 days</td>
        <td>&gt;90 days</td>
      </tr>
    <?php
	$res_outstanding_ageing = mysql_query($sql_outstanding_ageing);
	while($row_outstanding_ageing = mysql_fetch_array($res_outstanding_ageing)){
		$customer_name = $row_outstanding_ageing['customer_name'];
		$outstanding_amount = number_format($row_outstanding_ageing['outstanding_amount']);
		$amount_0_15_days = number_format($row_outstanding_ageing['amount_0_15_days']);
		if($amount_0_15_days<0)
			$amount_0_15_days = 0;
		$amount_16_30_days = number_format($row_outstanding_ageing['amount_16_30_days']);
		if($amount_16_30_days<0)
			$amount_16_30_days = 0;
		$amount_31_45_days = number_format($row_outstanding_ageing['amount_31_45_days']);
		if($amount_31_45_days<0)
			$amount_31_45_days = 0;
		$amount_46_90_days = number_format($row_outstanding_ageing['amount_46_90_days']);
		if($amount_46_90_days<0)
			$amount_46_90_days = 0;
		$amount_greater_90_days = number_format($row_outstanding_ageing['amount_greater_90_days']);
		if($amount_greater_90_days<0)
			$amount_greater_90_days = 0;
		$greater_90_days = $row_outstanding_ageing['greater_90_days'];
		
		
		echo "<tr>
				<td>".$count."</td>
				<td>".$customer_name."</td>
				<td align=\"right\">".$outstanding_amount."</td>
				<td align=\"right\">".$amount_0_15_days."</td>
				<td align=\"right\">".$amount_16_30_days."</td>
				<td align=\"right\">".$amount_31_45_days."</td>
				<td align=\"right\">".$amount_46_90_days."</td>
				<td align=\"right\">".$amount_greater_90_days."</td>
			  </tr>";
		$count++;
	}
}
else{
	echo "<strong><font color=\"red\">No records found</font></strong>";
}
mysql_close($link);
?>
