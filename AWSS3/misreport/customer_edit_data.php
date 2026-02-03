<?php
ob_start();
session_start();
require("adminUtils.php");

$employee = $_REQUEST['employee'];
$employee_arg = str_replace("#",",",$employee);
$employee_arg = str_replace("^","'",$employee_arg);

$vertical = $_REQUEST['vertical'];
$state = $_REQUEST['state'];
if(strpos($state,",") == FALSE)	$state = str_replace("'","",$state);
else								$state = "All";
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
$curdate=date('Y-m-d');
	 if(modified_customer_emp_route=='yes'){
		$sqlcustomerdetails="SELECT CM.dns_customer_code,CM.customer_code,CM.customer_name,CM.rds_tag,RM.route_name,EM.emp_name,CM.phone_no
		FROM customer_master CM,customer_route_emp_relation CRR,route_master RM,employee_master EM 
		WHERE CM.customer_code=CRR.customer_code AND CRR.route_code=RM.route_code AND CRR.emp_code=EM.emp_code AND CM.cust_type='R'
		AND CRR.emp_code IN(".$employee.") ORDER BY CM.customer_name ASC";
	 }
	 else
	 {
		$sqlcustomerdetails="SELECT CM.dns_customer_code,CM.customer_code,CM.customer_name,CM.rds_tag,RM.route_name,EM.emp_name,CM.phone_no
							FROM customer_master CM,route_master RM,employee_master EM 
							WHERE CM.route_code=RM.route_code AND CM.emp_code=EM.emp_code AND CM.cust_type='R' 
							AND CM.emp_code IN(".$employee.") ORDER BY CM.customer_name ASC";
	 }
	$rescustomerdetails = mysql_query($sqlcustomerdetails);
	$totalcustomerdetails = mysql_num_rows($rescustomerdetails);
	if($totalcustomerdetails >0){
		$count = 1;
		?><div class='tbl-header'>
		<table border="1" id="display_table" style="border-collapse:collapse;" class="border" width="80%">
          <tr class="TDHEAD_SUB">
          	<td colspan="7" align="center">Customer Information</td>
          </tr>
		  <tr class="TDHEAD" align="center">
			<td width="5%">SI</td>
			<td width="24%">Customer Name</td>
            <td width="10%">Phone No</td>
            <td width="17%">Mapped Employee</td>
            <td width="17%">Route</td>
			<td width="17%">Distributor</td>
            <td width="10%">EDIT</td>
		  </tr></table></div><div class='tbl-content'><table border="1" id="display_table" style="border-collapse:collapse;" class="border" width="80%">
		<?php
		
		while($rowcustomerdetails = mysql_fetch_array($rescustomerdetails)){
			$emp_name = $rowcustomerdetails['emp_name'];
			$customer_code = $rowcustomerdetails['customer_code'];
			$dns_customer_code = $rowcustomerdetails['dns_customer_code'];
			$customer_name = $rowcustomerdetails['customer_name'];
			$rds_tag=$rowcustomerdetails['rds_tag'];
			$phone_no=$rowcustomerdetails['phone_no'];
			$route_name=$rowcustomerdetails['route_name'];
				
				$sql_rds = "SELECT customer_name FROM customer_master WHERE customer_code = '".$rds_tag."'";
				$res_rds = mysql_query($sql_rds);
				$row_rds = mysql_fetch_array($res_rds);
				$rds_name = $row_rds['customer_name'];
			echo "<tr>
					<td width=\"5%\">".$count."</td>
					<td width=\"24%\">".$customer_name."</td>
					<td width=\"10%\">".$phone_no."</td>
					<td width=\"17%\">".$emp_name."</td>
					<td width=\"17%\">".$route_name."</td>
					<td width=\"17%\">".$rds_name."</td>
					<td align=\"center\" width=\"10%\"><a href=\"javascript:access_add_edit('".$customer_code."');\" title=\" Edit customer \" style=\"color: #F00;\"><img src=\"images/edit_icon.gif\" alt=\"\" /></a></td>
				  </tr>";
			$count++;
		}
		?>
        </table></div>
        <?php
	}
	else{
		echo "<span style=\"color:red; font-weight:bold;\">No Record Found</span>";
	}
?>
