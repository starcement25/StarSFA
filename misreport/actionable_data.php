<?php
ob_start();
session_start();
require("adminUtils.php");

$employee = $_REQUEST['employee'];
$employee_arg = str_replace(",","#",$employee);
$employee_arg = str_replace("'","^",$employee_arg);
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

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
	$res_emp_name = mysqli_query($link,$sql_emp_name);
	$row_emp_name = mysqli_fetch_assoc($res_emp_name);
	$new_emp_name = $row_emp_name['emp_name'];
}
else{
	$new_emp_name = "All";
}

$header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));

$sql_customer_visit = "SELECT * FROM `customer_visit_details` WHERE (SUBSTRING(trans_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') AND (trans_id LIKE 'OE%') AND emp_code IN(".$employee.") ORDER BY emp_code ASC";
$res_customer_visit = mysqli_query($link,$sql_customer_visit);
$total_row_check = mysqli_num_rows($res_customer_visit);

$count = 1;
function branch_name($branch_code){
	$sql_branch_name = "SELECT branch_name FROM branch_master WHERE branch_code = '".$branch_code."'";
	$res_branch_name = mysqli_query($link,$sql_branch_name);
	$row_branch_name = mysqli_fetch_assoc($res_branch_name);
	$branch_name = $row_branch_name['branch_name'];
	return $branch_name;
}
if($total_row_check>0){
	?>
     <table width="100%" style="border-collapse:collapse;" border="1"  cellpadding="2px">
      <tr class="TDHEAD">
      	<td colspan="16"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD_SUB" align="center">
        <td>SI</td>
        <td>Date of Visit</td>
        <td>Employee code</td>
        <td>Employee Name</td>
        <td>Branch</td>
        <td>Route</td>
        <td>Customer code</td>
        <td>Customer Name</td>
        <td>Contact Number</td>
        <td>Linked Dealer</td>
        <td>Requirement Type</td>
        <td>Requirement</td>
        <td>Status</td>
        <td>Date of Action</td>
        <td>Action taken details</td>
        <td>Trans Type</td>
      </tr>
    <?
	$res_customer_visit = mysqli_query($link,$sql_customer_visit);
	while($row_customer_visit = mysqli_fetch_assoc($res_customer_visit)){
		$emp_code = $row_customer_visit['emp_code'];
		$trans_id = $row_customer_visit['trans_id'];
		$customer_code = $row_customer_visit['customer_code'];
		$customer_name = $row_customer_visit['customer_name'];
		$rds_tag = $row_customer_visit['rds_tag'];
		$hint_remarks = $row_customer_visit['hint_remarks'];
		$route_code = $row_customer_visit['route_code'];
		$route_name = $row_customer_visit['route_name'];
		$date = date('d-m-Y',strtotime(substr($trans_id,-14,8)));
		
		$transid_substr = substr($trans_id,0,2);
		if($transid_substr == "OE" || $transid_substr == "NO"){
			$sql_branch_code = "SELECT branch_code FROM order_header WHERE order_no = '".$trans_id."'";
			$res_branch_code = mysqli_query($link,$sql_branch_code);
			$row_branch_code = mysqli_fetch_assoc($res_branch_code);
			$branch_code = $row_branch_code['branch_code'];
			$branch_name = branch_name($branch_code);
			
			if($transid_substr == 'OE')
				$trans_type = "Order";
			else
				$trans_type = "No Order";
		}
		/*else if($transid_substr == "SE" || $transid_substr == "NS"){
			$branch_name = branch_name($branch_code);
			if($transid_substr == 'SE')
				$trans_type = "Stock Audit";
			else
				$trans_type = "No Stock Audit";
		}*/
		
		$sql_rds_name = "SELECT customer_name FROM customer_master WHERE customer_code = '".$rds_tag."'";
		$res_rds_name = mysqli_query($link,$sql_rds_tag);
		$row_rds_name = mysqli_fetch_assoc($res_rds_name);
		$rds_name = $row_rds_name['customer_name'];
		
		$sql_contact = "SELECT owner_phone,dns_customer_code FROM customer_master WHERE customer_code = '".$customer_code."'";
		$res_contact = mysqli_query($link,$sql_contact);
		$row_contact = mysqli_fetch_assoc($res_contact);
		$contact = $row_contact['owner_phone'];
		$dns_customer_code = $row_contact['dns_customer_code'];
		
		$sql_emp_name = "SELECT emp_name, dns_emp_code FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_name = mysqli_query($link,$sql_emp_name);
		$row_emp_name = mysqli_fetch_assoc($res_emp_name);
		$emp_name = $row_emp_name['emp_name'];
		$dns_emp_code = $row_emp_name['dns_emp_code'];
		
		echo "<tr>
				<td>".$count."</td>
				<td>".$date."</td>
				<td>".$emp_code."</td>
				<td>".$emp_name."</td>
				<td>".$branch_name."</td>
				<td>".$route_name."</td>
				<td>".$dns_customer_code."</td>
				<td>".$customer_name."</td>
				<td>".$contact."</td>
				<td>".$rds_name."</td>
				<td>".$hint_remarks."</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td>".$trans_type."</td>
			  </tr>";
		$count++;
	}
}
else{
	echo "No Records Found";
}
?>