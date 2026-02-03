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
	$res_emp_name = mysqli_query($link,$sql_emp_name);
	$row_emp_name = mysqli_fetch_assoc($res_emp_name);
	$new_emp_name = $row_emp_name['emp_name'];
}
else{
	$new_emp_name = "All";
}
$month = $_REQUEST['month'];
$month_year = explode("-",$month);
$monthvalue = $month_year[1];
$year = $month_year[0];
$curdate=date('Y-m-d');
	 if(modified_customer_emp_route=='yes'){
		$sqlorderdetails="SELECT CM.dns_customer_code,CM.customer_code,CM.customer_name,EM.emp_name,CM.phone_no,PM.prod_desc,
		OD.qty,OD.sale_rate,OD.amount,DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%d-%m-%Y') AS order_date,OH.order_no,PM.prod_code,CM.cust_type 
		FROM customer_master CM,employee_master EM,product_master PM,order_details OD,
		order_header OH WHERE OH.customer_code=CM.customer_code AND OH.order_no=OD.order_no AND PM.prod_code=OD.sku_code  
		AND SUBSTRING(OH.order_no,-19,5) IN(".$employee.") AND SUBSTRING(OH.order_no,-19,5)=EM.emp_code AND SUBSTRING(OH.order_no,-14,4)='".$year."' AND SUBSTRING(OH.order_no,-10,2)='".$monthvalue."' 
		ORDER BY DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') DESC";
	 }
	 else
	 {
		$sqlorderdetails="SELECT CM.dns_customer_code,CM.customer_code,CM.customer_name,CM.rds_tag,RM.route_name,EM.emp_name,CM.phone_no,PM.prod_desc,
		OD.qty,OD.sale_rate,OD.amount,DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%d-%m-%Y') AS order_date,OH.order_no,PM.prod_code,CM.cust_type 
		FROM customer_master CM,route_master RM,employee_master EM,product_master PM,order_details OD,
		order_header OH WHERE CM.route_code=RM.route_code AND CM.emp_code=EM.emp_code AND
		OH.customer_code=CM.customer_code AND OH.order_no=OD.order_no AND PM.prod_code=OD.sku_code  
		AND CM.emp_code IN(".$employee.") AND SUBSTRING(OH.order_no,-14,4)='".$year."'  AND SUBSTRING(OH.order_no,-10,2)='".$monthvalue."' ORDER BY CM.customer_name ASC,DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%d-%m-%Y') DESC";
	 }
	$resorderdetails = mysqli_query($link,$sqlorderdetails);
	$totalorderdetails = mysqli_num_rows($resorderdetails);
	if($totalorderdetails >0){
		$count = 1;
		?>
		<table border="1" id="display_table" style="border-collapse:collapse;" class="border" width="100%">
          <tr class="TDHEAD_SUB">
          	<td colspan="13" align="center">Order Information</td>
          </tr>
		  <tr class="TDHEAD" align="center">
			<td width="5%">SI</td>
			<td width="14%">Customer Name</td>
            <td width="8%">Customer Type</td>
            <td width="12%">Order Done</td>
            <td width="10%">Order Date</td>
            <td width="12%">Product</td>
            <td width="10%">Qty</td>
            <td width="9%">Rate</td>
			<td width="10%">Order Amount</td>
            <td width="10%">EDIT</td>
		  </tr>
		<?php
		while($roworderdetails = mysqli_fetch_assoc($resorderdetails)){
			$customer_name = $roworderdetails['customer_name'];
			$customer_code = $roworderdetails['customer_code'];
			$cust_type = $roworderdetails['cust_type'];
			$dns_customer_code = $roworderdetails['dns_customer_code'];
			$rds_tag=$rowcustomerdetails['rds_tag'];
			$phone_no=$rowcustomerdetails['phone_no'];
			$route_name=$rowcustomerdetails['route_name'];
			$prod_desc = $roworderdetails['prod_desc'];
			$order_date = $roworderdetails['order_date'];
			$qty = $roworderdetails['qty'];
			$sale_rate = $roworderdetails['sale_rate'];
			$amount = $roworderdetails['amount'];
			$emp_name = $roworderdetails['emp_name'];
			$order_no = $roworderdetails['order_no'];
			$prod_code = $roworderdetails['prod_code'];
			
			if($cust_type =='R' )   $cust_type='Secondary';
			else    				$cust_type='Primary';
			echo "<tr>
					<td align=\"right\">".$count."</td>
					<td>".$customer_name."</td>
					<td>".$cust_type."</td>
					<td>".$emp_name."</td>
					<td>".$order_date."</td>
					<td>".$prod_desc."</td>
					<td align=\"right\">".$qty."</td>
					<td align=\"right\">".$sale_rate."</td>
					<td align=\"right\">".$amount."</td>
					<td align=\"center\" width=\"10%\"><a href=\"javascript:access_add_edit('".$prod_code."','".$order_no."');$('.scrolldiv')[0].focus()\" title=\" Edit order \" style=\"color: #F00;\"><img src=\"images/edit_icon.gif\" alt=\"\" /></a></td>
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