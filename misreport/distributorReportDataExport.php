<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

$start_date = date('Y-m-d');

/*---------------------------------> ADMIN/Employee hierarchy condition <--------------------------------*/
if($_SESSION['admin_login']=="admin"){
	$emp_hierarchy='';
	$emp_hierarchy_condition="";
	$emp_hierarchy_condition_one="";
	$emp_hierarchy_order_condition = "";
}
else
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition=" AND EM.emp_code IN(".$emp_hierarchy.") ";
	$emp_hierarchy_order_condition = " AND SUBSTRING(OH.order_no,-19,5) IN (".$emp_hierarchy.") ";
}
$employee=$_REQUEST['employee'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$distributor=$_REQUEST['distributor'];

if(strpos($distributor,",") == FALSE){
	$new_distributor = str_replace("'","",$distributor);
	$sql_distributor = "SELECT  customer_name FROM customer_master WHERE dns_customer_code = '".$new_distributor."'";
	$res_distributor = mysqli_query($link,$sql_distributor);
	$row_distributor = mysqli_fetch_assoc($res_distributor);
	$new_distributor_name = $row_distributor['customer_name'];
}
else{
	$new_distributor_name = "All";
}

	$sql_order_header = "SELECT CM.customer_name,RM.route_name,CM.phone_no,CM.pin,PM.prod_desc,OH.order_no,SUBSTRING(OH.order_no,2,5) AS emp_code, 
						DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%d-%m-%Y') as order_date,OD.qty,OD.UOM,CM.rds_tag  FROM 
						order_header OH INNER JOIN customer_master CM INNER JOIN route_master RM INNER JOIN order_details OD INNER JOIN product_master PM 
						ON OH.customer_code=CM.customer_code AND CM.route_code=RM.route_code AND OH.order_no=OD.order_no AND OD.sku_code=PM.prod_code AND
						DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."' 
						AND SUBSTRING(OD.order_no,2,5) IN (".$employee.") AND CM.rds_tag IN(SELECT customer_code FROM customer_master WHERE dns_customer_code IN(".$distributor.")) ORDER BY CM.rds_tag ASC,DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') DESC,CM.customer_name ASC";
	$res_order_header = mysqli_query($link,$sql_order_header);
	$count_order_header=mysqli_num_rows($res_order_header);

if(count($count_order_header)>0)
{
	$count = 1;
		 $header = "Distributor Report Of ".$new_distributor_name."\nDate"."\t"."Distributor Name"."\t"."Retailer Name"."\t"."Employee"."\t"."Area"."\t"."Retailer Phone No"."\t"."Pin Code"."\t"."Product/SKU"."\t"."Order Recieved(qty)"."\t"."UOM";

		while($row_total_order=mysqli_fetch_assoc($res_order_header)){
		$customer_name=trim(preg_replace('/[\r\n]+/', '',$row_total_order['customer_name']));
		$route_name=$row_total_order['route_name'];
		$phone_no=$row_total_order['phone_no'];
		$pin=$row_total_order['pin'];
		$prod_desc=$row_total_order['prod_desc'];
		$order_date=$row_total_order['order_date'];
		$qty=$row_total_order['qty'];
		$UOM=$row_total_order['UOM'];
		$route_name=$row_total_order['route_name'];
		$emp_code=$row_total_order['emp_code'];
		$rds_tag=$row_total_order['rds_tag'];
		$sql_distributor_name = "SELECT  customer_name FROM customer_master WHERE customer_code = '".$rds_tag."'";
		$res_distributor_name = mysqli_query($link,$sql_distributor_name);
		$row_distributor_name = mysqli_fetch_assoc($res_distributor_name);
		$distributor_name = trim(preg_replace('/[\r\n]+/', '',$row_distributor_name['customer_name']));		
		$sql_emp = "SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
	    $res_emp = mysqli_query($link,$sql_emp);
		$row_emp=mysqli_fetch_assoc($res_emp);
		$emp_name=$row_emp['emp_name'];
			$table_data .= $order_date."\t".$distributor_name."\t".$customer_name."\t".$emp_name."\t".$route_name."\t".$phone_no."\t".$pin."\t".$prod_desc."\t".$qty."\t".$UOM."\n";
			$count++;
		}
}
if($table_data !=''){	
	header("Content-type: application/octet-stream"); 
	header("Content-Disposition: attachment; filename=Distributor_report.xls"); 
	header("Pragma: no-cache"); 
	header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 
	echo ucwords($header)."\n".$table_data;
}
else
{
	echo "<span style=\"font-weight:bold; color:red;\">No Records Found!</span>";
}
mysqli_close($link);
?>