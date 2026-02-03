<?php
ob_start();
session_start();
require("adminUtils.php");

/*$employee = $_REQUEST['employee'];
$employee_arg = str_replace("#",",",$employee);
$employee_arg = str_replace("^","'",$employee_arg);*/

$branch=$_REQUEST['branch'];
$route=$_REQUEST['route'];
$cluster=$_REQUEST['cluster'];
$curdate=date('Y-m-d');
if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy_value = '';
		$emp_hierarchy_value_condition = '';
	}
	else{
		$emp_hierarchy_value=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_value_condition = " AND emp_code IN(".$emp_hierarchy_value.") ";
	}
	
	$sqlcustomerdetails="SELECT CM.dns_customer_code,CM.customer_code,CM.customer_name,RM.route_name,CM.phone_no,CM.cust_type,CM.base_latt,
						CM.base_longi,CM.address,BM.branch_name FROM customer_master CM,route_master RM,branch_master BM
						WHERE CM.route_code=RM.route_code 
						AND CM.branch_code=BM.branch_code AND 
						CM.route_code IN(".$route.") AND CM.cluster IN(".$cluster.") AND CM.customer_name <> '' AND CM.base_latt <> '0' AND CM.base_longi <> '0' AND
						CM.customer_code IN(SELECT DISTINCT customer_code FROM customer_route_emp_relation WHERE acedns='Y') 
						ORDER BY CM.cust_type ASC";					
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
			<td width="16%">Customer Name</td>
            <td width="9%">Phone No</td>
             <td width="15%">Route</td>
            <td width="9%">Customer Type</td>
            <td width="14%">Branch</td>
            <td width="16%">Address</td>
            <td width="8%">Action</td>
		  </tr>
		<?php
		$dealercount=0;
		$nonstarcount=0;
		$subdealercount=0;
		$otherscount=0;
		$selected_customer='';
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
			$address=$rowcustomerdetails['address'];
			$branch_name=$rowcustomerdetails['branch_name'];
			if($cust_type=='Dealer' && $dealercount<'5')
			{ 
				$checkedval='checked';
				$random_no = rand(1,9).rand(0,9).rand(0,9).rand(1,9);
				$sqlupdatecust="UPDATE customer_master SET tagging_order='".$random_no."' WHERE customer_code='".$customer_code."'";
				mysqli_query($link,$sqlupdatecust);
				$dealercount++;
			}
			else if($cust_type=='Non Star' && $nonstarcount < ((5-$dealercount)+10))
			{
				$checkedval='checked';
				$random_no = rand(1,9).rand(0,9).rand(0,9).rand(1,9);
				$sqlupdatecust="UPDATE customer_master SET tagging_order='".$random_no."' WHERE customer_code='".$customer_code."'";
				mysqli_query($link,$sqlupdatecust);
				$nonstarcount++;
			}
			else if($cust_type=='Sub Dealer' && $subdealercount < ((5-$dealercount)+(10-$nonstarcount)+10))
			{
				$checkedval='checked';
				$random_no = rand(1,9).rand(0,9).rand(0,9).rand(1,9);
				$sqlupdatecust="UPDATE customer_master SET tagging_order='".$random_no."' WHERE customer_code='".$customer_code."'";
				mysqli_query($link,$sqlupdatecust);
				$subdealercount++;
			}
			/*else if($subdealercount > 0 && $nonstarcount > 0 && ($otherscount <  ((5-$dealercount)+(10-$nonstarcount)+(10-$subdealercount)+15)))
			{
				$checkedval='checked';
				$random_no = rand(1,9).rand(0,9).rand(0,9).rand(1,9);
				$sqlupdatecust="UPDATE customer_master SET tagging_order='".$random_no."' WHERE customer_code='".$customer_code."'";
				mysqli_query($link,$sqlupdatecust);
				$otherscount++;
			}*/
			else
			{
				$checkedval='';
			}
			if($checkedval!='')
			{
				$selected_customer=$selected_customer."'".$customer_code."'".',';
				
			echo "<tr>
			
					<td width=\"5%\">".$count."</td>
					<td width=\"8%\">".$dns_customer_code."</td>
					<td width=\"16%\">".$customer_name."</td>
					<td width=\"9%\">".$phone_no."</td>
					<td width=\"15%\">".$route_name."</td>
					<td width=\"9%\">".$cust_type."</td>
					<td width=\"14%\">".$branch_name."</td>
					<td width=\"16%\">".$address."</td>
					<td align=\"center\" width=\"8%\"><input type='checkbox' name='tagging_customer[]' value=\"$customer_code\" $checkedval class=\"tagging_customer_chk\"></td>
				  </tr>";
			$count++;
			}
		}
		?>
        <?php
			$selected_customer=substr($selected_customer,0,-1);
			$sqlcustomerrandom="SELECT CM.dns_customer_code,CM.customer_code,CM.customer_name,RM.route_name,CM.phone_no,CM.cust_type,CM.base_latt,
						CM.base_longi,CM.address,BM.branch_name FROM customer_master CM,route_master RM,branch_master BM
						WHERE CM.route_code=RM.route_code 
						AND CM.branch_code=BM.branch_code AND 
						CM.route_code IN(".$route.") AND CM.cluster IN(".$cluster.") AND CM.customer_name <> '' AND CM.base_latt <> '0' AND CM.base_longi <> '0' AND
						CM.customer_code IN(SELECT DISTINCT customer_code FROM customer_route_emp_relation WHERE acedns='Y') AND  
						CM.customer_code NOT IN(".$selected_customer.") ORDER  BY tagging_order DESC";					
			$rescustomerrandom = mysqli_query($link,$sqlcustomerrandom);
			while($rowcustomerrandom = mysqli_fetch_assoc($rescustomerrandom)){
			//$emp_name = $rowcustomerdetails['emp_name'];
			$customer_code = $rowcustomerrandom['customer_code'];
			$dns_customer_code = $rowcustomerrandom['dns_customer_code'];
			$customer_name = $rowcustomerrandom['customer_name'];
			$phone_no=$rowcustomerrandom['phone_no'];
			$route_name=$rowcustomerrandom['route_name'];
			$cust_type=$rowcustomerrandom['cust_type'];
			$base_latt=$rowcustomerrandom['base_latt'];
			$base_longi=$rowcustomerrandom['base_longi'];
			$address=$rowcustomerrandom['address'];
			$branch_name=$rowcustomerrandom['branch_name'];
			if($otherscount <  ((5-$dealercount)+(10-$nonstarcount)+(10-$subdealercount)+15))
			{
				$random_no = rand(1,9).rand(0,9).rand(0,9).rand(1,9);
				$sqlupdatecust="UPDATE customer_master SET tagging_order='".$random_no."' WHERE customer_code='".$customer_code."'";
				mysqli_query($link,$sqlupdatecust);

				$checkedval='checked';
				$otherscount++;
			}
			else
			{
				$checkedval='';
			}
				
			echo "<tr>
			
					<td width=\"5%\">".$count."</td>
					<td width=\"8%\">".$dns_customer_code."</td>
					<td width=\"16%\">".$customer_name."</td>
					<td width=\"9%\">".$phone_no."</td>
					<td width=\"15%\">".$route_name."</td>
					<td width=\"9%\">".$cust_type."</td>
					<td width=\"14%\">".$branch_name."</td>
					<td width=\"16%\">".$address."</td>
					<td align=\"center\" width=\"8%\"><input type='checkbox' name='tagging_customer[]' value=\"$customer_code\" $checkedval class=\"tagging_customer_chk\"></td>
				  </tr>";
			$count++;
		}
	}
	else{
		echo "<span style=\"color:red; font-weight:bold;\">No Record Found</span>";
	}
	?>
    </table>