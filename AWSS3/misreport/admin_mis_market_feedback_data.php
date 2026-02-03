<?php
ob_start();
session_start();
require("adminUtils.php");

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

$employee = $_REQUEST['employee'];
$employee_arg = str_replace("#",",",$employee);
$employee_arg = str_replace("^","'",$employee_arg);
?>

<table border="1" style="border-collapse:collapse;" class="border" width="150%">
  <tr class="TDHEAD">
  	<td width="6%">Date</td>
    <td>Emp Code</td>
    <td>Emp Name</td>
    <td>Cust Code</td>
    <td>Cust Name</td>
    <td>Cust Category</td>
    <td>Star(MT)</td>
<?php
$competitor_name_array = array();
$sql_competitor_name = "SELECT competitor_name, UOM FROM competitor_group_master ORDER BY competitor_name ASC";
$res_competitor_name = mysql_query($sql_competitor_name);
while($row_competitor_name = mysql_fetch_array($res_competitor_name)){
	$competitor_name = $row_competitor_name['competitor_name'];
	$UOM = $row_competitor_name['UOM'];
	echo "<td>".$competitor_name."(".$UOM.")</td>";
	array_push($competitor_name_array,$competitor_name);
}
?>
	<td>Total</td>
  </tr>
<?php
$sql_competitor_stock = "SELECT * FROM competitor_stock WHERE emp_code IN (".$employee_arg.") AND (date_time BETWEEN '".$start_date."' AND '".$end_date."') ORDER BY date_time DESC";
$res_competitor_stock = mysql_query($sql_competitor_stock);
while($row_competitor_stock = mysql_fetch_array($res_competitor_stock)){
	$emp_code = $row_competitor_stock['emp_code'];
	$customer_code = $row_competitor_stock['customer_code'];
	$star = $row_competitor_stock['star'];
	$ambuja = $row_competitor_stock['ambuja'];
	$ultratech = $row_competitor_stock['ultratech'];
	$lafarge = $row_competitor_stock['lafarge'];
	$dalmia = $row_competitor_stock['dalmia'];
	$topcem = $row_competitor_stock['topcem'];
	$acc = $row_competitor_stock['acc'];
	$birla_gold = $row_competitor_stock['birla_gold'];
	$date_time = date('d-m-Y',strtotime($row_competitor_stock['date_time']));
	
	$total_quantity = ($star + $ambuja + $ultratech + $lafarge + $dalmia + $topcem + $acc + $birla_gold);
	
	$sql_emp_details = "SELECT emp_name, dns_emp_code FROM employee_master WHERE emp_code = '".$emp_code."'";
	$res_emp_details = mysql_query($sql_emp_details);
	$row_emp_details = mysql_fetch_array($res_emp_details);
	$emp_name = $row_emp_details['emp_name'];
	$dns_emp_code = $row_emp_details['dns_emp_code'];
	
	$sql_customer_details = "SELECT customer_name, dns_customer_code, cust_type FROM customer_master WHERE customer_code = '".$customer_code."'";
	$res_customer_details = mysql_query($sql_customer_details);
	$row_customer_details = mysql_fetch_array($res_customer_details);
	$customer_name = $row_customer_details['customer_name'];
	$dns_customer_code = $row_customer_details['dns_customer_code'];
	$cust_type = $row_customer_details['cust_type'];
	
	echo "<tr>
			<td>".$date_time."</td>
			<td>".$dns_emp_code."</td>
			<td>".$emp_name."</td>
			<td>".$dns_customer_code."</td>
			<td>".$customer_name."</td>
			<td>".$cust_type."</td>
			<td align=\"right\">".$star."</td>";
	
	foreach($competitor_name_array as $value){
		if($value == 'AMBUJA PPC')
			$quantity = $ambuja;
		else if($value == 'ULTRATECH PPC')
			$quantity = $ultratech;
		else if($value == 'LAFARGE PPC')
			$quantity = $lafarge;
		else if($value == 'DALMIA PPC')
			$quantity = $dalmia;
		else if($value == 'TOPCEM PPC')
			$quantity = $topcem;
		else if($value == 'ACC PPC')
			$quantity = $acc;
		else if($value == 'BIRLA GOLD PPC')
			$quantity = $birla_gold;
			
		echo "<td align=\"right\">".$quantity."</td>";
	}
	echo "<td align=\"right\">".$total_quantity."<td>";
	echo "</tr>";
}
mysql_close($link);
?>
</table>
