<?php
ob_start();
session_start();
require("adminUtils.php");

$zone = $_REQUEST['zone'];
$state = $_REQUEST['state'];
$branch = $_REQUEST['branch'];
$department = $_REQUEST['department'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

if(strpos($zone,",") == FALSE)	$zone = str_replace("'","",$zone);
else								$zone = "All";

if(strpos($state,",") == FALSE)	$state = str_replace("'","",$state);
else								$state = "All";

if(strpos($branch,",") == FALSE)	$branch = str_replace("'","",$branch);
else								$branch = "All";

if(strpos($department,",") == FALSE)	$department = str_replace("'","",$department);
else									$department = "All";

$employee = $_REQUEST['employee'];
$employee_arg = str_replace(",","#",$employee);
$employee_arg = str_replace("'","^",$employee_arg);
//$survey_type = $_REQUEST['survey_type'];


$header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:Logistics&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));

$sql_distinct_date = "SELECT DISTINCT SUBSTRING(COD.trans_id,3,5) AS emp_code,DATE_FORMAT(SUBSTRING(COD.check_in_time,1,10),'%d-%m-%Y') AS checkin_checkout_date,
						 SUBSTRING(COD.check_in_time,12,8) as check_in_time,SUBSTRING(COD.check_out_time,12,8) as check_out_time,CM.customer_name
						FROM check_in_out_details COD INNER JOIN customer_master CM  ON CM.customer_code=COD.customer_code AND
					((SUBSTRING(COD.check_in_time,1,10) BETWEEN '".$start_date."' AND '".$end_date."') 
					AND SUBSTRING(COD.trans_id,3,5) IN(".$employee."))  ORDER BY DATE_FORMAT(SUBSTRING(COD.check_in_time,1,10),'%Y-%m-%d') DESC";
$res_distinct_date = mysql_query($sql_distinct_date);
$total_rows = mysql_num_rows($res_distinct_date);

if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="80%">
 		<tr>
      	  <td colspan="7" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
        </tr>
      <tr class="TDHEAD">
        <td width="10%">Date</td>
        <td width="10%">Employee Code</td>
        <td width="25%">Employee Name</td>
        <td width="15%">Branch</td>
        <td width="20%">Customer</td>
        <td width="10%">Check In Time</td>
        <td width="10%">Check Out Time</td>
      </tr>
    <?php
	$res_checkinout = mysql_query($sql_distinct_date);
	while($row_checkinout = mysql_fetch_array($res_checkinout)){
		$emp_code = $row_checkinout['emp_code'];
		$checkin_checkout_date = $row_checkinout['checkin_checkout_date'];
		$check_in_time = $row_checkinout['check_in_time'];
		$check_out_time = $row_checkinout['check_out_time'];
		$customer_name = $row_checkinout['customer_name'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name,branch_code FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysql_query($sql_emp_details);
		$row_emp_details = mysql_fetch_array($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		$branch_code = $row_emp_details['branch_code'];
		
		$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch_code."'";
		$rsbranchname=mysql_query($sqlbranchname);
		$rowbranchname=mysql_fetch_array($rsbranchname);
		$branch_name=$rowbranchname['branch_name'];
		
		echo "<tr>
				<td>".$checkin_checkout_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>
				<td>".$branch_name."</td>
				<td>".$customer_name."</td>
				<td >".$check_in_time."</td>
				<td >".$check_out_time."</td>
		  </tr>";
	}
}
else{
	echo "<center>No Records Found</center>";
}
mysql_close($link);
?>
