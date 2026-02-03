<?php
//ob_start();
/*ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);*/
session_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
/*$employee = $_REQUEST['employee'];
$employee_arg = str_replace("#",",",$employee);
$employee_arg = str_replace("^","'",$employee_arg);*/

$branch=$_REQUEST['branch'];
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
$curdate=date('Y-m-d');
	 if(modified_customer_emp_route=='yes'){
		/*$sqlcustomerdetails="SELECT CM.dns_customer_code,CM.customer_code,CM.customer_name,RM.route_name,EM.emp_name,CM.phone_no,CM.cust_type,CM.base_latt,
							CM.base_longi FROM customer_master CM,customer_route_emp_relation CRR,route_master RM,employee_master EM 
							WHERE CM.customer_code=CRR.customer_code AND CRR.route_code=RM.route_code AND CRR.emp_code=EM.emp_code AND CRR.acedns='Y'
							AND CRR.emp_code IN(".$employee.") AND customer_name <> '' ORDER BY CM.customer_name ASC";*/
		/*$sqlcustomerdetails="SELECT CM.dns_customer_code,CM.customer_code,CM.customer_name,RM.route_name,CM.phone_no,CM.cust_type,CM.base_latt,
							CM.base_longi FROM customer_master CM,route_master RM
							WHERE CM.route_code=RM.route_code AND
							FIND_IN_SET(CM.branch_code,'".$branch."') AND CM.customer_name <> '' AND CM.customer_code IN(SELECT DISTINCT customer_code FROM customer_route_emp_relation WHERE acedns='Y') ORDER BY CM.customer_name ASC";*/
		$sqlcustomerdetails="SELECT CM.dns_customer_code,CM.customer_code,CM.customer_name,(SELECT DISTINCT RM.route_name FROM customer_route_emp_relation CRR,route_master RM WHERE CRR.route_code=RM.route_code AND CM.customer_code=CRR.customer_code  AND CRR.acedns='Y' LIMIT 0,1) AS route_name,CM.phone_no,CM.cust_type,CM.base_latt,
							CM.base_longi FROM customer_master CM WHERE  
							FIND_IN_SET(CM.branch_code,'".$branch."') AND CM.customer_name <> '' AND CM.customer_code IN(SELECT DISTINCT customer_code FROM customer_route_emp_relation WHERE acedns='Y') ORDER BY CM.customer_name ASC";	 

							/* 	$sqlcustomerdetails="SELECT CM.dns_customer_code,CM.customer_code,CM.customer_name,(SELECT RM.route_name FROM customer_route_emp_relation CRR INNER JOIN route_master RM  ON RM.route_code = CRR.route_code  WHERE CRR.customer_code = CM.customer_code AND CRR.acedns = 'Y' ORDER BY RM.download_time DESC LIMIT 1 ) AS route_name ,CM.phone_no,CM.cust_type,CM.base_latt,
							CM.base_longi FROM customer_master CM WHERE  
							FIND_IN_SET(CM.branch_code,'".$branch."') AND CM.customer_name <> '' AND CM.customer_code IN(SELECT DISTINCT customer_code FROM customer_route_emp_relation WHERE acedns='Y') ORDER BY CM.customer_name ASC";	*/
	 }
	 else
	 {
		$sqlcustomerdetails="SELECT CM.dns_customer_code,CM.customer_code,CM.customer_name,RM.route_name,CM.phone_no,CM.cust_type,CM.base_latt,
							CM.base_longi FROM customer_master CM,route_master RM
							WHERE CM.route_code=RM.route_code AND
							FIND_IN_SET(CM.branch_code,'".$branch."') AND CM.customer_name <> '' AND CM.acedns='Y' ORDER BY CM.customer_name ASC";
	 }
	 //echo $sqlcustomerdetails;
	$rescustomerdetails = mysqli_query($link,$sqlcustomerdetails);
	$totalcustomerdetails = mysqli_num_rows($rescustomerdetails);
	if($totalcustomerdetails >0){
		$count = 1;
		?>
		<table border="1" style="border-collapse:collapse;" class="border" width="90%">
          <tr class="TDHEAD_SUB">
          	<td colspan="10" align="center">Customer Information</td>
          </tr>
		  <tr class="TDHEAD" align="center">
			<td width="5%">SI</td>
            <td width="8%">Customer Code</td>
			<td width="26%">Customer Name</td>
            <td width="9%">Customer Type</td>
            <td width="9%">Phone No</td>
            <td width="18%">Route</td>
            <td width="8%">Latt</td>
            <td width="8%">Longi</td>
            <td width="9%">EDIT LOCATION</td>
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
				  </tr>";
			$count++;
		}
		?>
        </table>
        <?php
	}
	else{
		echo "<span style=\"color:red; font-weight:bold;\">No Record Found</span>";
	}
?>
