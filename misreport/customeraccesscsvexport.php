<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

if(providing_code == 'yes')
	$dns_empcode_name_heading = 'DNS Employee Code';
else
	$dns_empcode_name_heading = 'Employee Name';
	
$excelheader = "DNS Customer Code"."\t"."Customer Name"."\t"."DNS Route Code"."\t"."Route Name"."\t".$dns_empcode_name_heading."\t"."Acedns"."\t"."Blacklist"."\t"."Customer Type"."\n";

$count = 1;
$sql_customer_details = "SELECT dns_customer_code, customer_name, route_code, emp_code, acedns, black_list, cust_type FROM customer_master";
$res_customer_details = mysqli_query($link,$sql_customer_details);
while($row_customer_details = mysqli_fetch_assoc($res_customer_details))
{
	$dns_customer_code = $row_customer_details['dns_customer_code'];
	$customer_name = $row_customer_details['customer_name'];
	$route_code = $row_customer_details['route_code'];
	$emp_code = $row_customer_details['emp_code'];
	$acedns = $row_customer_details['acedns'];
	$black_list = $row_customer_details['black_list'];
	$cust_type = $row_customer_details['cust_type'];
	
	$sql_dnsroutecode = "SELECT dns_route_code, route_name FROM route_master WHERE route_code = '$route_code'";
	$res_dnsroutecode = mysqli_query($link,$sql_dnsroutecode);
	$row_dnsroutecode = mysqli_fetch_assoc($res_dnsroutecode);
	$dnsroutecode = $row_dnsroutecode['dns_route_code'];
	$route_name = $row_dnsroutecode['route_name'];
	
	$sql_empdnscode_name = "SELECT emp_name, dns_emp_code FROM employee_master WHERE emp_code = '$emp_code'";
	$res_empdnscode_name = mysqli_query($link,$sql_empdnscode_name);
	$row_empdnscode_name = mysqli_fetch_assoc($res_empdnscode_name);
	$emp_dns_code = $row_empdnscode_name['dns_emp_code'];
	$emp_name = $row_empdnscode_name['emp_name'];
	if(providing_code == 'yes')
	{
		$emp_dnscode_name = $emp_dns_code;
	}
	else
	{
		$emp_dnscode_name = $emp_name;
	}
	
			  
	$excelcontents .= $dns_customer_code."\t".$customer_name."\t".$dnsroutecode."\t".$route_name."\t".$emp_dnscode_name."\t".$acedns."\t".$black_list."\t".$cust_type."\n";
	$count++;
}
	header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"); 
	header("Content-Disposition: attachment; filename=customer master.xls");
	
	echo $excelheader;
	echo $excelcontents;
	
	mysqli_close($link);
?>
