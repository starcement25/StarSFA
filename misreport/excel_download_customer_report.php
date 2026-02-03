<?php
ob_start();


	session_start();
	require("adminUtils.php");
//	require("db_link.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

$state_name = $_REQUEST['state_name'];
$emp_code = $_REQUEST['emp_code'];
$route_code = $_REQUEST['route_code'];
$submit_data = $_REQUEST['submit_data'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

/*if($state_name != '' && ($emp_code == '' && $route_code ==''))
{
	$condition = " EM.state='".$state_name."'";
}
else if(($state_name != '' && $emp_code != '') && $route_code =='')
{
	if($emp_code == 'all'){
		if(strtoupper($_SESSION['admin_login']) != "ADMIN"){
			$emp_hierarchy = return_employee_hierarchy($_SESSION['admin_login']);
			$condition = " EM.state='".$state_name."' AND EM.emp_code IN (".$emp_hierarchy.") ";
		}
		else{
			$condition = " EM.state='".$state_name."' AND EM.emp_code != '' ";
		}
	}
	else{
		$condition = " EM.state='".$state_name."' AND EM.emp_code='".$emp_code."' ";
	}
}
else if($state_name == ''&& $emp_code != ''){
	$condition = " EM.emp_code='".$emp_code."' ";
}
else
{
	$condition = " EM.state='".$state_name."' AND CM.emp_code='".$emp_code."' AND CM.route_code='".$route_code."'";
}

if($_SESSION['admin_login']=="admin")
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition="";
	
}
else
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition=" AND EM.emp_code IN (".$emp_hierarchy.")";
}*/
if($_SESSION['admin_login']=="admin")
{
	$emp_hierarchy="";
	
}
else
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
}
if(modified_customer_emp_route == 'yes'){
	if(strtoupper($_SESSION['admin_login']) == "ADMIN"){
		if($emp_code == 'all')
			$emp_hierarchy_condition = " AND CRER.emp_code != '' ";
		else
			$emp_hierarchy_condition = " AND CRER.emp_code = '".$emp_code."' ";
	}
	else{
		if($emp_code == 'all')
			$emp_hierarchy_condition = " AND CRER.emp_code IN (".$emp_hierarchy.") ";
		else
			$emp_hierarchy_condition = " AND CRER.emp_code = '".$emp_code."' ";
	}
}
else if(modified_customer_emp_route == 'no'){
	if(strtoupper($_SESSION['admin_login']) == "ADMIN"){
		if($emp_code == 'all')
			$emp_hierarchy_condition = " AND EM.emp_code != '' ";
		else
			$emp_hierarchy_condition = " AND EM.emp_code = '".$emp_code."' ";
	}
	else{
		if($emp_code == 'all')
			$emp_hierarchy_condition = " AND EM.emp_code IN (".$emp_hierarchy.") ";
		else
			$emp_hierarchy_condition = " AND EM.emp_code = '".$emp_code."' ";
	}
}
?>
<?php
$setExcelName = "NewCustomer";
if(modified_customer_emp_route == 'yes'){
	$sql_new_customer = "SELECT CRER.customer_code,CM.dns_customer_code, CM.customer_name, CM.address, CM.pin, CM.phone_no,CM.image, EM.emp_code, EM.dns_emp_code, EM.emp_name, EM.designation, RM.route_name, DATE_FORMAT(SUBSTRING(CRER.customer_code,-14,14),'%d-%m-%Y %H:%i:%s') as date_created, EM.vertical_value,(SELECT CMB.customer_name FROM customer_master CMB WHERE CMB.customer_code=CM.rds_tag) AS rds_tag,(SELECT CMB.customer_code FROM customer_master CMB WHERE CMB.customer_code=CM.rds_tag) AS rds_tag_code,EM.state,CM.owner_image,CM.owner_name,CM.firm_name,CM.firm_image,CM.TIN,
	CM.GST_image,CM.aadhar,CM.aadhar_image,CM.cust_type FROM employee_master EM, customer_master CM, route_master RM, customer_route_emp_relation CRER WHERE CRER.emp_code = EM.emp_code AND CM.customer_code = CRER.customer_code AND CRER.route_code = RM.route_code AND CRER.customer_code LIKE 'N%' ".$condition.$emp_hierarchy_condition." AND (DATE_FORMAT(SUBSTRING(CRER.customer_code,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."') ORDER BY DATE_FORMAT(SUBSTRING(CRER.customer_code,-14,8),'%d-%m-%Y'), EM.emp_name ASC, CM.customer_name ASC";
}
else if(modified_customer_emp_route == 'no'){
	$sql_new_customer = "SELECT CM.customer_code,CM.dns_customer_code, CM.customer_name, CM.address, CM.pin, CM.phone_no,CM.image, EM.emp_code, EM.dns_emp_code, EM.emp_name, EM.designation, RM.route_name, DATE_FORMAT(SUBSTRING(CM.customer_code,-14,8),'%d-%m-%Y') as date_created, EM.vertical_value,EM.state,
						(SELECT CMB.customer_name FROM customer_master CMB WHERE CMB.customer_code=CM.rds_tag) AS rds_tag,(SELECT CMB.dns_customer_code FROM customer_master CMB WHERE CMB.customer_code=CM.rds_tag) AS rds_tag_code FROM employee_master EM, customer_master CM, route_master RM WHERE CM.emp_code=EM.emp_code  AND CM.customer_code LIKE 'N%' AND RM.route_code = CM.route_code ".$condition.$emp_hierarchy_condition." AND (DATE_FORMAT(SUBSTRING(CM.customer_code,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."') ORDER BY DATE_FORMAT(SUBSTRING(CM.customer_code,-14,8),'%d-%m-%Y'), EM.emp_name ASC, CM.customer_name ASC";
}
$res_new_customer = mysqli_query($link,$sql_new_customer);
$total_rows = mysqli_num_rows($res_new_customer);
if($total_rows>0)
{
	if($submit_data == 'submitdata')
	{
		$count = 1;
		echo "<table width=\"100%\" border=\"1\" style=\"border-collapse:collapse;\" class=\"border\" cellpadding=\"6px\">
				  <tr class=\"TDHEAD\">
				  	<td>SI</td>"; 
					 /*if(strtoupper($_SESSION['nick_name'])=='RUPA'){
            		echo "<td>Customer Code</td>";
            		}*/
					echo "<td>Customer Name</td>"; 
                   if(tagged_distributor_for_order=='yes'){
					  echo "<td>Distributor Code</td>";   
            		  echo "<td>Distributor Name</td>";
            		}
				  echo "<td>State</td>
					<td>Area</td>
					<td>Pincode</td>
					<td>Phone No</td>
					<td>Emp Code</td>
					<td>Employee Name</td>
					<td>Designation</td>";
		if(vertical_fields == 'yes'){
			echo "<td>Vertical</td>";
		}
		if(add_customer_image_creation == 'yes'){
			echo "<td>Image</td>";
		}
		if(add_customer_details=='customize')
		{
				echo "<td>Type</td><td>Owner Name</td>
					<td>Firm Name</td>
					<td>GST</td>
					<td>Aadhar</td>
					";
		}
		echo "<td>Date Created</td><td>1st Productive Call</td>
		</tr>";
		$res_new_customer = mysqli_query($link,$sql_new_customer);
		while($row_new_customer = mysqli_fetch_assoc($res_new_customer))
		{
			$customer_dns_code = $row_new_customer['customer_code'];
			$customer_name = $row_new_customer['customer_name'];
			$address = $row_new_customer['address'];
			$pin = $row_new_customer['pin'];
			$phone_no = $row_new_customer['phone_no'];
			$emp_name = $row_new_customer['emp_name'];
			$route_name = $row_new_customer['route_name'];
			$date_created = $row_new_customer['date_created'];
			$vertical_value = $row_new_customer['vertical_value'];
			$rds_tag = $row_new_customer['rds_tag'];
			$state = $row_new_customer['state'];
			$plain_emp_code = $row_new_customer['emp_code'];
			$dns_emp_code = $row_new_customer['dns_emp_code'];
			$designation = $row_new_customer['designation'];
			$dns_customer_code=$row_new_customer['dns_customer_code'];
			$image=$row_new_customer['image'];
			$cust_type=$row_new_customer['cust_type'];
			$owner_image=$row_new_customer['owner_image'];
			$owner_name=$row_new_customer['owner_name'];
			$firm_name=$row_new_customer['firm_name'];
			$firm_image=$row_new_customer['firm_image'];
			$TIN=$row_new_customer['TIN'];
			$GST_image=$row_new_customer['GST_image'];
			$aadhar=$row_new_customer['aadhar'];
			$aadhar_image=$row_new_customer['aadhar_image'];
			
			
			
			if(strtoupper($_SESSION['nick_name'])=='ASL')
			{
				$sql1stproductivecall="SELECT  DATE_FORMAT(SUBSTRING(order_no,-14,14),'%d-%m-%Y %H:%i:%s') AS producttive_call_date FROM order_header WHERE 
								order_no lIKE 'O%' AND customer_code='".$customer_dns_code."' AND order_no IN(SELECT DISTINCT order_no FROM order_details) 
								AND DATE_FORMAT(SUBSTRING(order_no,-14,14),'%Y-%m-%d %H:%i:%s') > '2019-08-01 14:00:00' 
								ORDER BY DATE_FORMAT(SUBSTRING(order_no,-14,14),'%Y-%m-%d %H:%i:%s') DESC LIMIT 0,1";

			}
			else
			{
			$sql1stproductivecall="SELECT  DATE_FORMAT(SUBSTRING(order_no,-14,14),'%d-%m-%Y %H:%i:%s') AS producttive_call_date FROM order_header WHERE 
								order_no lIKE 'O%' AND customer_code='".$customer_dns_code."' 
								ORDER BY DATE_FORMAT(SUBSTRING(order_no,-14,14),'%Y-%m-%d %H:%i:%s') DESC LIMIT 0,1";
			}
			$rs1stproductivecall=mysqli_query($link,$sql1stproductivecall);
			$row1stproductivecall=mysqli_fetch_assoc($rs1stproductivecall);
			$productivecall1st=$row1stproductivecall['producttive_call_date'];
			if(strtoupper($_SESSION['nick_name'])=='RUPA'){
				$display_emp_code = $plain_emp_code;
				//$dns_customer_TD="<td>".$dns_customer_code."</td>";
			}
			else if(providing_code == 'yes'){
				$display_emp_code = $dns_emp_code;
			}
			else if(providing_code == 'no'){
				$display_emp_code = $plain_emp_code;
			}
			if(tagged_distributor_for_order=='yes'){
				//$sql_rds_name = "SELECT customer_name FROM customer_master WHERE customer_code = '".$rds_tag."'";
				/*$sql_rds_name = "SELECT customer_name FROM customer_master WHERE FIND_IN_SET(customer_code,'".$rds_tag."')";
				$res_rds_name = mysqli_query($link,$sql_rds_name);
				$rds_name='';
				while($row_rds_name = mysqli_fetch_assoc($res_rds_name)){
					$rds_name =$rds_name.$row_rds_name['customer_name'].",";
				}
				$rds_name=substr($rds_name,0,-1);*/
				$rds_name=$row_new_customer['rds_tag'];
				$rds_code=$row_new_customer['rds_tag_code'];
				echo "<tr>
					<td>".$count."</td>
					<td>".$customer_name."</td>
					<td>".$rds_code."</td>
					<td>".$rds_name."</td>
					<td>".$state."</td>
					<td>".$route_name."</td>
					<td>".$pin."</td>
					<td>".$phone_no."</td>
					<td>".$display_emp_code."</td>
					<td>".$emp_name."</td>
					<td>".$designation."</td>";
			}
			else
			{
				echo "<tr>
					<td>".$count."</td>".$dns_customer_TD."
					<td>".$customer_name."</td>
					<td>".$state."</td>
					<td>".$route_name."</td>
					<td>".$pin."</td>
					<td>".$phone_no."</td>
					<td>".$display_emp_code."</td>
					<td>".$emp_name."</td>
					<td>".$designation."</td>
					";
			}
			if(vertical_fields == 'yes'){
				echo "<td>".$vertical_value."</td>";
			}
			if(add_customer_image_creation == 'yes'){
				if($image!='')
				{
				$image_string = "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
				}
				else
				{
					$image_string='';				
				}

			echo "<td>$image_string</td>";
			}
			if(add_customer_details=='customize')
			{
				if($owner_image!='')
				{
				$image_owner = "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$owner_image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
				}
				else
				{
					$image_owner='';				
				}
				if($firm_image!='')
				{
				$image_firm = "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$firm_image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
				}
				else
				{
					$image_firm='';				
				}

				if($GST_image!='')
				{
				$image_GST = "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$GST_image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
				}
				else
				{
					$image_GST='';				
				}

				if($aadhar_image!='')
				{
				$image_aadhar = "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$aadhar_image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
				}
				else
				{
					$image_aadhar='';				
				}
				if($cust_type=='R') $cust_type='Retailer';
				else 				$cust_type=$cust_type;
				echo "<td>".$cust_type."</td>
					<td>".$owner_name.' '.$image_owner."</td>
					<td>".$firm_name.' '.$image_firm."</td>
					<td>".$TIN.' '.$image_GST."</td>
					<td>".$aadhar.' '.$image_aadhar."</td>";
			}

			echo "<td>".$date_created."</td><td>".$productivecall1st."</td>
				  </tr>";
			$count++;
		}
		echo "</table>";
		?>
        <br />
    <input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="export" onClick="exporttocsv();">
     <?php
	}
	else
	{
		if(vertical_fields == 'yes'){
			if(add_customer_image_creation == 'yes'){
				$header = "Customer Name"."\t"."Distributor Code"."\t"."Distributor Name"."\t"."State"."\t"."Area"."\t"."Pincode"."\t"."Phone No"."\t"."Employee Code"."\t"."Employee Name"."\t"."Designation"."\t"."Vertical"."\t"."Image"."\t"."Date Created"."\t"."1st Productive Call";
			}
			else
			{
			$header = "Customer Name"."\t"."Distributor Code"."\t"."Distributor Name"."\t"."State"."\t"."Area"."\t"."Pincode"."\t"."Phone No"."\t"."Employee Code"."\t"."Employee Name"."\t"."Designation"."\t"."Vertical"."\t"."Date Created"."\t"."1st Productive Call";
			}
		}
		else{
			if(add_customer_image_creation == 'yes'){
				$header = "Customer Name"."\t"."Distributor Code"."\t"."Distributor Name"."\t"."State"."\t"."Area"."\t"."Pincode"."\t"."Phone No"."\t"."Employee Code"."\t"."Employee Name"."\t"."Designation"."\t"."Image"."\t"."Date Created"."\t"."1st Productive Call";
			}
			else if(add_customer_details=='customize')
			{
				$header = "Customer Name"."\t"."Distributor Code"."\t"."Distributor Name"."\t"."State"."\t"."Area"."\t"."Pincode"."\t"."Phone No"."\t"."Employee Code"."\t"."Employee Name"."\t"."Designation"."\t"."Date Created"."\t".
				"1st Productive Call"."\t"."Type"."\t"."Owner name"."\t"."Firm name"."\t"."GST"."\t"."AADHAR";

			}
			else
			{
			$header = "Customer Name"."\t"."Distributor Code"."\t"."Distributor Name"."\t"."State"."\t"."Area"."\t"."Pincode"."\t"."Phone No"."\t"."Employee Code"."\t"."Employee Name"."\t"."Designation"."\t"."Date Created"."\t"."1st Productive Call";
			}
		}
		$res_new_customer = mysqli_query($link,$sql_new_customer);
		while($row_new_customer = mysqli_fetch_assoc($res_new_customer))
		{
			$customer_dns_code = $row_new_customer['customer_code'];
			$customer_name = $row_new_customer['customer_name'];
			$address = $row_new_customer['address'];
			$pin = $row_new_customer['pin'];
			$phone_no = $row_new_customer['phone_no'];
			$emp_name = $row_new_customer['emp_name'];
			$route_name = $row_new_customer['route_name'];
			$rds_tag = $row_new_customer['rds_tag'];
			$rds_code = $row_new_customer['rds_tag_code'];
			$state = $row_new_customer['state'];
			$date_created = $row_new_customer['date_created'];
			$vertical_value = $row_new_customer['vertical_value'];
			$plain_emp_code = $row_new_customer['emp_code'];
			$dns_emp_code = $row_new_customer['dns_emp_code'];
			$designation = $row_new_customer['designation'];
			$dns_customer_code=$row_new_customer['dns_customer_code'];
			$image=$row_new_customer['image'];
			$cust_type=$row_new_customer['cust_type'];
			$owner_image=$row_new_customer['owner_image'];
			$owner_name=$row_new_customer['owner_name'];
			$firm_name=$row_new_customer['firm_name'];
			$firm_image=$row_new_customer['firm_image'];
			$TIN=$row_new_customer['TIN'];
			$GST_image=$row_new_customer['GST_image'];
			$aadhar=$row_new_customer['aadhar'];
			$aadhar_image=$row_new_customer['aadhar_image'];

			$sql1stproductivecall="SELECT  DATE_FORMAT(SUBSTRING(order_no,-14,14),'%d-%m-%Y %H:%i:%s') AS producttive_call_date FROM order_header WHERE 
								order_no lIKE 'O%' AND customer_code='".$customer_dns_code."' ORDER BY 
								DATE_FORMAT(SUBSTRING(order_no,-14,14),'%Y-%m-%d %H:%i:%s') DESC LIMIT 0,1";
			$rs1stproductivecall=mysqli_query($link,$sql1stproductivecall);
			$row1stproductivecall=mysqli_fetch_assoc($rs1stproductivecall);
			$productivecall1st=$row1stproductivecall['producttive_call_date'];				
			if(tagged_distributor_for_order=='yes'){
				/*$sql_rds_name = "SELECT customer_name FROM customer_master WHERE customer_code = '".$rds_tag."'";
				$res_rds_name = mysqli_query($link,$sql_rds_name);
				$row_rds_name = mysqli_fetch_assoc($res_rds_name);
				$rds_name = $row_rds_name['customer_name'];*/
				/*$sql_rds_name = "SELECT customer_name FROM customer_master WHERE FIND_IN_SET(customer_code,'".$rds_tag."')";
				$res_rds_name = mysqli_query($link,$sql_rds_name);
				$rds_name='';
				while($row_rds_name = mysqli_fetch_assoc($res_rds_name)){
				 $rds_name =$rds_name.$row_rds_name['customer_name'].",";
				}
				$rds_name=substr($rds_name,0,-1);*/
				$rds_name=$row_new_customer['rds_tag'];
			}
			if(providing_code == 'yes'){
				$display_emp_code = $dns_emp_code;
			}
			else if(providing_code == 'no'){
				$display_emp_code = $plain_emp_code;
			}
			if(add_customer_image_creation == 'yes'){
				if($image!='')
				{
				$image_string = "http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$image;
				}
				else
				{
					$image_string='';				
				}
			}
			if(vertical_fields == 'yes'){
				if(add_customer_image_creation == 'yes'){
					$content .= $customer_name."\t".$rds_code."\t".$rds_name."\t".$state."\t".$route_name."\t".$pin."\t".$phone_no."\t".$plain_emp_code."\t".$emp_name."\t".$designation."\t".$vertical_value."\t".$image_string."\t".$date_created."\t".$productivecall1st."\n";
				}
				else
				{
				$content .= $customer_name."\t".$rds_code."\t".$rds_name."\t".$state."\t".$route_name."\t".$pin."\t".$phone_no."\t".$plain_emp_code."\t".$emp_name."\t".$designation."\t".$vertical_value."\t".$date_created."\t".$productivecall1st."\n";
				}
			}
			else{
				if(add_customer_image_creation == 'yes'){
					$content .= $customer_name."\t".$rds_code."\t".$rds_name."\t".$state."\t".$route_name."\t".$pin."\t".$phone_no."\t".$plain_emp_code."\t".$emp_name."\t".$designation."\t".$image_string."\t".$date_created."\t".$productivecall1st."\n";

				}
				else if(add_customer_details=='customize')
				{
					if($cust_type=='R') $cust_type='Retailer';
					else 				$cust_type=$cust_type;
					
				$content .= $customer_name."\t".$rds_code."\t".$rds_name."\t".$state."\t".$route_name."\t".$pin."\t".$phone_no."\t".$plain_emp_code."\t".$emp_name."\t".$designation."\t".$date_created."\t".$productivecall1st."\t".$cust_type."\t".$owner_name."\t".$firm_name."\t".$TIN."\t".$aadhar."\n";
				}
				else{
				$content .= $customer_name."\t".$rds_code."\t".$rds_name."\t".$state."\t".$route_name."\t".$pin."\t".$phone_no."\t".$plain_emp_code."\t".$emp_name."\t".$designation."\t".$date_created."\t".$productivecall1st."\n";
				}
			}
		}
			header("Content-type: application/octet-stream"); 
			header("Content-Disposition: attachment; filename=".$setExcelName."_Report.xls"); 
			header("Pragma: no-cache"); 
			header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 
			echo ucwords($header)."\n".$content; //- See more at: http://www.discussdesk.com/download-mysql-data-into-excel-file-in-php.htm#sthash.5bPI72JI.dpuf
	}
}
else
{
	echo "<font color=\"red\"><strong>No records</strong></font>";
}
?>