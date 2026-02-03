<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$emp_code = $_REQUEST['emp_code'];
$emp_hierarchy=return_employee_hierarchy($emp_code);
$emp_hierarchy_condition=" AND EM.emp_code IN (".$emp_hierarchy.") AND EM.acedns!='N' ";

if($_GET['type'] == 'today'){
	$today = date('Y-m-d');
	$order_condition = " AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d')='".$today."' ";
	$payment_condition = " AND DATE_FORMAT(SUBSTRING(PH.receipt_id,-14,8),'%Y-%m-%d')='".$today."' ";
}
else if($_GET['type'] == 'mtd'){
	@$current_date = date('Y-m-d');
	$month = explode("-",$current_date);
	$year = $month[0];
	$month = $month[1];
	$order_condition = "AND YEAR(DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d')) =". $year." AND MONTH(DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d')) =" .$month;
	$payment_condition = "AND YEAR(DATE_FORMAT(SUBSTRING(PH.receipt_id,-14,8),'%Y-%m-%d')) =". $year." AND MONTH(DATE_FORMAT(SUBSTRING(PH.receipt_id,-14,8),'%Y-%m-%d')) =" .$month;
}
else if($_GET['type'] == 'custom'){
	$start_date = $_GET['start_date'];
	$strt = date('d-m-Y',strtotime($start_date));
	$end_date = $_GET['end_date'];
	$endt = date('d-m-Y',strtotime($end_date));
	$order_condition = " AND (DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."') ";
	$payment_condition = " AND (DATE_FORMAT(SUBSTRING(PH.receipt_id,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."') ";
}
if($_GET['type'] == 'today'){
	$column_name = 'Transaction Time';
}
else{
	$column_name = 'Total Visit';
}

$sql_check = "SELECT OH.order_no FROM order_header OH, employee_master EM WHERE 1 ".$order_condition.$emp_hierarchy_condition." AND OH.order_no LIKE 'O%' AND SUBSTRING(OH.order_no,2,5)=EM.emp_code";
$res_check = mysqli_query($link,$sql_check);
$total_rows = mysqli_num_rows($res_check);
if($total_rows>0){
?>
    <table border="1" width="100%" style="border-collapse:collapse;" cellpadding="4px" class="border">
      <tr class="TDHEAD" align="center">
        <td>SI</td>
        <td>Location</td>
        <td>Customer Name</td>
        <td><?php echo $column_name; ?></td>
        <td>Order</td>
        <td>Collection</td>
      </tr>
    
    <?php
	$count = 1;
    $sql_customer_order_header = "SELECT DISTINCT CM.customer_name, CM.customer_code, RM.route_name FROM customer_master CM, order_header OH, employee_master EM, route_master RM WHERE OH.customer_code=CM.customer_code AND OH.order_no LIKE 'O%'".$order_condition.$emp_hierarchy_condition." AND CM.emp_code=EM.emp_code AND CM.route_code=RM.route_code ORDER BY RM.route_name, CM.customer_name ASC";
    $res_customer_order_header = mysqli_query($link,$sql_customer_order_header);
    while($row_customer_header = mysqli_fetch_assoc($res_customer_order_header)){
        $customer_name = $row_customer_header['customer_name'];
        $customer_code = $row_customer_header['customer_code'];
		$route_name = $row_customer_header['route_name'];
		
		/*$sql_route_name = "SELECT route_name FROM route_master WHERE route_code = '".$route_code."'";
		$res_route_name = mysqli_query($link,$sql_route_name);
		$row_route_name = mysqli_fetch_assoc($res_route_name);
		$route_name = $row_route_name['route_name'];*/
        
		$product_group_quantity = array();
		$product_group_name = array();
        $sql_order_header = "SELECT OH.order_no FROM order_header OH WHERE OH.customer_code='".$customer_code."'".$order_condition." AND OH.order_no LIKE 'O%'";
		$res_order_header = mysqli_query($link,$sql_order_header);
		while($row_order_header = mysqli_fetch_assoc($res_order_header)){
			$order_no = $row_order_header['order_no'];
			
			$sql_product_group_quantity = "SELECT PGM.product_group_code, PGM.product_group_name, SUM(OD.qty) as sumqty FROM product_group_master PGM, product_master PM, order_details OD WHERE OD.order_no='".$order_no."' AND OD.sku_code=PM.prod_code AND PM.product_group_code=PGM.product_group_code GROUP BY PGM.product_group_code";
			$res_product_group_quantity = mysqli_query($link,$sql_product_group_quantity);
			while($row_product_quantity = mysqli_fetch_assoc($res_product_group_quantity)){
				$product_gr_code = $row_product_quantity['product_group_code'];
				$product_gr_name = $row_product_quantity['product_group_name'];
				$tot_qty = $row_product_quantity['sumqty'];
				$product_group_quantity[$product_gr_code] += $tot_qty;
				$product_group_name[$product_gr_code]= $product_gr_name;
			}
		}
		$sql_collection = "SELECT SUM(PH.amount) as tot_amount FROM payment_header PH, employee_master EM WHERE PH.customer_code='".$customer_code."' AND PH.receipt_id LIKE 'P%'".$payment_condition.$emp_hierarchy_condition." AND SUBSTRING(PH.receipt_id,2,5)=EM.emp_code";
		$res_collection = mysqli_query($link,$sql_collection);
		$row_collection = mysqli_fetch_assoc($res_collection);
		$total_collection = $row_collection['tot_amount'];
		
		$sql_min_time = "SELECT MIN(SUBSTRING(OH.order_no,-6)) as min_time FROM order_header OH WHERE OH.customer_code='".$customer_code."'".$order_condition;
		$res_min_time = mysqli_query($link,$sql_min_time);
		$row_min_time = mysqli_fetch_assoc($res_min_time);
		$time_min = $row_min_time['min_time'];
		$time_min = date('H:i:s',strtotime(''.$time_min.''));
		
		if($_GET['type'] != 'today'){
			$sql_count_visit = "SELECT COUNT(OH.order_no) as tot_visit FROM order_header OH, employee_master EM WHERE OH.customer_code='".$customer_code."'".$order_condition.$emp_hierarchy_condition." AND OH.order_no LIKE 'O%' AND SUBSTRING(OH.order_no,2,5)=EM.emp_code";
			$res_count_visit = mysqli_query($link,$sql_count_visit);
			$row_count_visit = mysqli_fetch_assoc($res_count_visit);
			$total_visit = $row_count_visit['tot_visit'];
		}

		foreach($product_group_quantity as $prod_gr_code=>$quantity){
			$order_string .= $product_group_name[$prod_gr_code]."=".$quantity."<br>";
		}
		echo "<tr>
				<td>".$count."</td>
				<td>".$route_name."</td>
				<td>".$customer_name."</td>";
		if($_GET['type'] == 'today')
			echo "<td align=\"center\">".$time_min."</td>";
		else
			echo "<td align=\"right\">".$total_visit."</td>";
		
		if($order_string == '')	
			echo "<td>No Order</td>";
		else
			echo "<td>".$order_string."</td>";
		if($total_collection != '')		
				echo "<td align=\"right\">".number_format($total_collection,2)."</td>";
		else
			echo "<td align=\"right\">No Collection</td>";
		echo "</tr>";
		$order_string = '';
		$count++;
    }
}
else
{
	echo "<strong><font color=\"red\">No records found</font></strong>";
}
mysqli_close($link);
?>