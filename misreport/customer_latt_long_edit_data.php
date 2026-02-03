<?php
ob_start();
session_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
/*$branch=$_REQUEST['branch'];
if(strpos($employee,",") == FALSE){
	$new_emp_code = str_replace("'","",$employee);
	$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$new_emp_code."'";
	$res_emp_name = mysqli_query($link,$sql_emp_name);
	$row_emp_name = mysqli_fetch_assoc($res_emp_name);
	$new_emp_name = $row_emp_name['emp_name'];
}
else{
	$new_emp_name = "All";
}*/
$employee = $_REQUEST['employee'];
if($employee == 'all'){
	$emp_condition = '';
}
else{
	$emp_condition = " AND emp_code IN('".$employee."') ";
}

$curdate=date('Y-m-d');
	 if(modified_customer_emp_route=='yes'){
		/*$sqlcustomerdetails="SELECT CM.dns_customer_code,CM.customer_code,CM.customer_name,RM.route_name,EM.emp_name,CM.phone_no,CM.cust_type,CM.base_latt,
							CM.base_longi FROM customer_master CM,customer_route_emp_relation CRR,route_master RM,employee_master EM 
							WHERE CM.customer_code=CRR.customer_code AND CRR.route_code=RM.route_code AND CRR.emp_code=EM.emp_code AND CRR.acedns='Y'
							AND CRR.emp_code IN(".$employee.") AND customer_name <> '' ORDER BY CM.customer_name ASC";*/
		$sqlcustomerdetails="SELECT CM.dns_customer_code,CM.customer_code,CM.customer_name,RM.route_name,CM.phone_no,CM.cust_type,CM.base_latt,
							CM.base_longi FROM customer_master CM,route_master RM
							WHERE CM.route_code=RM.route_code AND CM.customer_name <> '' AND CM.base_latt!='0' AND CM.base_longi!='0' 
							AND CM.customer_code IN(SELECT DISTINCT customer_code FROM customer_route_emp_relation WHERE acedns='Y'".$emp_condition.") ORDER BY CM.customer_name ASC";					
	 }
	 else
	 {
		$sqlcustomerdetails="SELECT CM.dns_customer_code,CM.customer_code,CM.customer_name,RM.route_name,CM.phone_no,CM.cust_type,CM.base_latt,
							CM.base_longi FROM customer_master CM,route_master RM
							WHERE CM.route_code=RM.route_code AND
							FIND_IN_SET(CM.branch_code,'".$branch."') AND CM.customer_name <> '' AND CM.acedns='Y' ORDER BY CM.customer_name ASC";
	 }
	$rescustomerdetails = mysqli_query($link,$sqlcustomerdetails);
	$totalcustomerdetails = mysqli_num_rows($rescustomerdetails);
	if($totalcustomerdetails >0){
		$count = 1;
		?>
         <form name="frm_opts_multiple" action="customer_latt_long_edit.php" method="post" >
        <input type="hidden" name="mode" value="editcustomer_multiple">
		<table border="1" style="border-collapse:collapse;" class="border" width="90%">
          <tr class="TDHEAD_SUB">
          	<td colspan="10" align="center">Customer Information</td>
          </tr>
		  <tr class="TDHEAD" align="center">
			<td width="5%">SI</td>
            <td width="7%">Customer Code</td>
			<td width="23%">Customer Name</td>
            <td width="9%">Customer Type</td>
            <td width="9%">Phone No</td>
            <td width="15%">Route</td>
            <td width="8%">Latt</td>
            <td width="8%">Longi</td>
            <td width="8%">EDIT LOCATION</td>
            <td width="8%">Bulk Activity</td>
		  </tr>
		<?php
		
		while($rowcustomerdetails = mysqli_fetch_assoc($rescustomerdetails)){
			//$emp_name = $rowcustomerdetails['emp_name'];
			$customer_code = $rowcustomerdetails['customer_code'];
			$dns_customer_code = $rowcustomerdetails['dns_customer_code'];
			$customer_name = $rowcustomerdetails['customer_name'];
			$phone_no=$rowcustomerdetails['phone_no'];
			$route_name=$rowcustomerdetails['route_name'];
			$cust_type=$rowcustomerdetails['cust_type'];
			$base_latt=$rowcustomerdetails['base_latt'];
			$base_longi=$rowcustomerdetails['base_longi'];
			echo "<tr>
					<td width=\"5%\">".$count."</td>
					<td width=\"8%\">".$dns_customer_code."</td>
					<td width=\"16%\">".$customer_name."</td>
					<td width=\"9%\">".$cust_type."</td>
					<td width=\"9%\">".$phone_no."</td>
					<td width=\"14%\">".$route_name."</td>
					<td width=\"8%\">".$base_latt."</td>
					<td width=\"8%\">".$base_longi."</td>
					<td align=\"center\" width=\"9%\"><a href=\"javascript:access_add_edit('".$customer_code."');\" title=\" Edit customer location\" style=\"color: #F00;\"><img src=\"images/edit_icon.gif\" alt=\"\" /></a></td>
					<td align=\"center\" ><input type=\"checkbox\" name=\"sel_customer_code[]\" value=\"$customer_code\" /></td>
				  </tr>";
				  
			$count++;
		}
		echo "<tr height=\"30\">
				<td width=\"5%\"></td>
				<td width=\"8%\"></td>
				<td width=\"16%\"></td>
				<td width=\"9%\"></td>
				<td width=\"9%\"></td>
				<td width=\"14%\"></td>
				<td width=\"8%\"></td>
				<td width=\"8%\"></td>
				<td align=\"right\" colspan=\"2\"><input type=\"submit\" value=\" SUBMIT \" class=\"inplogin\"></td>
			  </tr>";
		?>
        </table>
        <?php
	}
	else{
		echo "<span style=\"color:red; font-weight:bold;\">No Record Found</span>";
	}
?>
