<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$val = $_REQUEST['val'];
$employee = $_REQUEST['employee'];
$employee = str_replace("^","'",$employee);
$employee = str_replace("#",",",$employee);
$emp_hierarchy_condition=' AND LO.emp_code IN('.$employee.') ';


if($val == 'T'){
	$date = date('Y-m-d');
	$date_condition = " AND SUBSTRING(LO.date,1,10) = '".$date."' ";
}
else if($val == 'MTD'){
	$current_month = date('m');
	$mtd_date = date('m');
	$date_condition = " AND SUBSTRING(LO.date,6,2) = '".$current_month."' ";
}
else if($val == 'YTD'){
	$current_date = $date = date('Y-m-d');
	$current_month = date('m');
	if($current_month == '01' || $current_month == '02' || $current_month == '03'){
		$previous_year = date('Y', strtotime('-1 year'));
		$previous_year_date = $previous_year."-04-01";
	}
	else{
		$previous_year_date = date('Y-04-01');
	}
	$date_condition = " AND (SUBSTRING(LO.date,1,10) BETWEEN '".$previous_year_date."' AND '".$current_date."') ";
}
?>
<table width="100%" border="1" style="border-collapse:collapse;" class="border">
  <tr class="TDHEAD" align="center">
  	<td>SI</td>
    <td>Date</td>
    <td>Time</td>
    <td>DNS Code</td>
    <td>Customer Name</td>
    <td>Customer Type</td>
    <td>Locate</td>
  </tr>
<?

$sql_emp_code = "SELECT emp_code, emp_name FROM employee_master WHERE emp_code IN(".$employee.") ORDER BY emp_name ASC";
$res_emp_code = mysql_query($sql_emp_code);
while($row_emp_code = mysql_fetch_array($res_emp_code)){
	$emp_code = $row_emp_code['emp_code'];
	$emp_name = $row_emp_code['emp_name'];
	
	
	$sqlcustomervisit="SELECT COUNT(LO.trans_id) AS no_visit  FROM location LO WHERE (SUBSTRING(LO.trans_id,1,1) IN
						('O','P','S') OR SUBSTRING(LO.trans_id,1,2) IN('NO','NC')) AND SUBSTRING(LO.trans_id,1,2) NOT IN('PA')  
						AND SUBSTRING(LO.trans_id,1,2) NOT IN('SU') AND SUBSTRING(LO.emp_code,1,1)!='C' AND LO.emp_code = '".$emp_code."'".$date_condition;
	$rescustomervisit=mysql_query($sqlcustomervisit) or die(mysql_error()." Error in select customer visit: ".$sqlcustomervisit);
	$rowcustomervisit=mysql_fetch_array($rescustomervisit);
	$no_customer_visit=$rowcustomervisit['no_visit'];
	
	if($no_customer_visit != '0'){
		$count = 1;
		echo "<tr>
				<td colspan=\"7\" align=\"center\" class=\"TDHEAD_SUB\">$emp_code&nbsp;&nbsp;--&nbsp;&nbsp;$emp_name&nbsp;&nbsp;--&nbsp;&nbsp;Customer Visited:$no_customer_visit</td>
			  </tr>";
		$sql_customer = "SELECT trans_id, DATE_FORMAT(SUBSTRING(LO.date,1,10),'%d-%m-%Y') AS date_visit, DATE_FORMAT(LO.date,'%H:%i:%s') AS time_visit  FROM location LO WHERE (SUBSTRING(LO.trans_id,1,1) IN
						('O','P','S') OR SUBSTRING(LO.trans_id,1,2) IN('NO','NC')) AND SUBSTRING(LO.trans_id,1,2) NOT IN('PA')  
						AND SUBSTRING(LO.trans_id,1,2) NOT IN('SU') AND SUBSTRING(LO.emp_code,1,1)!='C' AND LO.emp_code = '".$emp_code."'".$date_condition." ORDER BY date_visit DESC";
		$res_customer = mysql_query($sql_customer);
		while($row_customer = mysql_fetch_array($res_customer)){
			$trans_id = $row_customer['trans_id'];
			$date_visit = $row_customer['date_visit'];
			$time_visit = $row_customer['time_visit'];
			
			$operation_type=substr($trans_id,0,1);
			
			if($operation_type=='O'){
				$order_no=$trans_id;
				
				$sqlorderheader="SELECT customer_code FROM order_header WHERE order_no='".$order_no."'";
				$rsorderheader=mysql_query($sqlorderheader) or die(mysql_error()." Error in select order: ".$sqlorderheader);
				$roworderheader=mysql_fetch_array($rsorderheader);
				$customer_code=$roworderheader['customer_code'];
			}
			else if($operation_type=='P'){
				$receipt_id=$trans_id;
				$sqlpaymentheader="SELECT customer_code FROM payment_header WHERE receipt_id='".$receipt_id."'";
				$rspaymentheader=mysql_query($sqlpaymentheader) or die(mysql_error()." Error in select payment header: ".$sqlpaymentheader);
				$rowpaymentheader=mysql_fetch_array($rspaymentheader);
				$customer_code=$rowpaymentheader['customer_code'];
				
			}
			else if($operation_type=='S'){
				$stk_counting_trans_id=$trans_id;
				$sqlcustomer="SELECT SA.customer_code  FROM 
							  stock_audit SA
							  WHERE SA.transaction_id='".$stk_counting_trans_id."'";
				$rscustomer=mysql_query($sqlcustomer) or die(mysql_error()." Error in select customer: ".$sqlcustomer);
				$rowcustomer=mysql_fetch_array($rscustomer);
				$customer_code=$rowcustomer['customer_code'];
			}
			else if($operation_type=='N'){
				$operation_type_no=substr($trans_id,1,1);
				if($operation_type_no=='O'){
					$order_no=$trans_id;
					$sqlcustomernoorder="SELECT OH.customer_code FROM order_header OH 
										WHERE OH.order_no='".$order_no."'";
					$rscustomernoorder=mysql_query($sqlcustomernoorder) or die(mysql_error()." Error in select customer for no order: ".$sqlcustomernoorder);
					$rowcustomernoorder=mysql_fetch_array($rscustomernoorder);
			
					$customer_code=$rowcustomernoorder['customer_code'];
					
				}
				else if($operation_type_no=='C'){
					$receipt_id=$trans_id;
					$sqlcustomernocollection="SELECT PH.customer_code FROM payment_header PH
											   WHERE PH.receipt_id='".$receipt_id."'";
					$rscustomernocollection=mysql_query($sqlcustomernocollection) or die(mysql_error()." 
												Error in select customer for no collection: ".$sqlcustomernocollection);
					$rowcustomernocollection=mysql_fetch_array($rscustomernocollection);
			
					$customer_code=$rowcustomernocollection['customer_code'];
				}
			}
		$sql_customer_name = "SELECT dns_customer_code, customer_name, cust_type FROM customer_master WHERE customer_code = '".$customer_code."'";
		$res_customer_name = mysql_query($sql_customer_name);
		$row_customer_name = mysql_fetch_array($res_customer_name);
		$customer_name = $row_customer_name['customer_name'];
		$cust_type = $row_customer_name['cust_type'];
		$dns_customer_code = $row_customer_name['dns_customer_code'];
		echo "<tr>
				<td>".$count."</td>
				<td>".$date_visit."</td>
				<td>".$time_visit."</td>
				<td>".$dns_customer_code."</td>
				<td>".$customer_name."</td>
				<td>".$cust_type."</td>
				<td align=\"center\"><a href=\"customerLocate.php?trans_id=$trans_id&customer_code=$customer_code&
							emp_code=$emp_code&date=$date&from_date=$from_date&to_date=$to_date&mode=$mode&page=$page&visit_map=true\" 	style=\"color:brown;font-weight:bold;text-decoration:none;\" target=\"_blank\">Locate</a></td>
			  </tr>";
		$count++;	
		}
	}
}
mysql_close($link);
?>



