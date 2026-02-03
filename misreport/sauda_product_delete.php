<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

$sauda_number = $_REQUEST['sauda_number'];
$product_code = $_REQUEST['product_code'];
$check_sauda_number = $sauda_number;
$get_emp_code = substr($sauda_number,2,5);
$sauda_date = date('Y-m-d',strtotime(substr($sauda_number,-14,8)));

/*$sql_product_booked_quantity = "SELECT sum(convert_qty_two) as mt_booked FROM sauda_details WHERE sku_code = '$product_code' AND sauda_no = '$sauda_number'";
$res_product_booked_quantity = mysqli_query($link,$sql_product_booked_quantity);
$row_product_booked_quantity = mysqli_fetch_assoc($res_product_booked_quantity);*/
$booked = $_REQUEST['booked_case'];

$ip = $_SERVER['REMOTE_ADDR'];

$get_date = date('Ymd',strtotime(substr($sauda_number,-14,8)));
$today = date('Ymd');
$yesterday = date('Y-m-d',strtotime("-1 days"));

$sql_product_details = "SELECT * FROM product_master WHERE prod_code = '".$product_code."'";
$res_product_details = mysqli_query($link,$sql_product_details);
$row_product_details = mysqli_fetch_assoc($res_product_details);
	
$conversion_factor = $row_product_details['conversion_factor'];
$conversion_factor_two = $row_product_details['conversion_factor_two'];
					
$convert_qty_one=round(($booked*$conversion_factor),3);
$convert_qty_two=round((($booked*$conversion_factor)/$conversion_factor_two),3);

$sql_check_allocation_balance = "SELECT distinct(SA.product_filter_code) FROM sauda_allocation SA, sauda_details SD, product_master PM WHERE SD.sku_code = '".$product_code."' AND SD.sku_code = PM.prod_code AND PM.product_group_code = SA.product_filter_code";
$res_check_allocation_balance = mysqli_query($link,$sql_check_allocation_balance);
$row_check_allocation_balance = mysqli_fetch_assoc($res_check_allocation_balance);
$get_product_group_code = $row_check_allocation_balance['product_filter_code'];


$sql_select_booked = "SELECT * FROM sauda_details WHERE sku_code = '".$product_code."' AND sauda_no = '".$sauda_number."'";
$res_select_booked = mysqli_query($link,$sql_select_booked);
$row_select_booked = mysqli_fetch_assoc($res_select_booked);
$booked_mt_before = $row_select_booked['convert_qty_two'];
$booked_case_before = $row_select_booked['qty'];
$freight_charge_before = $row_select_booked['freight_charge'];
$trade_discount_before = $row_select_booked['TD'];
$previous_value = $booked_case_before.",".$freight_charge_before.",".$trade_discount_before;

$sql_totalproduct = "SELECT * FROM sauda_details WHERE sauda_no = '$sauda_number'";
$res_totalproduct = mysqli_query($link,$sql_totalproduct);
$total_rows = mysqli_num_rows($res_totalproduct);

$sql_emp_allocation_access = "SELECT SAA.get_allocation FROM sauda_allocation_access SAA, employee_master EM WHERE EM.emp_code = '".substr($sauda_number,2,5)."' AND EM.designation = SAA.designation";
$res_emp_allocation_access = mysqli_query($link,$sql_emp_allocation_access);
$row_emp_allocation_access = mysqli_fetch_assoc($res_emp_allocation_access);
$allocation_access = $row_emp_allocation_access['get_allocation'];

$sql_check_total_product = "SELECT * FROM sauda_details WHERE substring(sauda_no,3,5) LIKE '%$get_emp_code%' AND sauda_no LIKE '%$sauda_number%'";
$res_check_total_product = mysqli_query($link,$sql_check_total_product);
$total_product = mysqli_num_rows($res_check_total_product);

$sql_check_quantity_bal = "SELECT * FROM sauda_allocation WHERE emp_code = '".$get_emp_code."' AND product_filter_code = '".$get_product_group_code."'";
$res_check_quantity_bal = mysqli_query($link,$sql_check_quantity_bal);
$total_rows = mysqli_num_rows($res_check_quantity_bal);

if($get_date == $today)
{
	if($allocation_access == 'yes' && $total_rows>0)
	{
		$emp_upper_hierarchy = return_employee_upper_hierarchy($get_emp_code);
		$emp_upper_hierarchy .= ",'".$get_emp_code."'";
		$sql_emp_set_bal = "SELECT emp_name, emp_code FROM employee_master WHERE emp_code IN (".$emp_upper_hierarchy.")";
		$res_emp_set_bal = mysqli_query($link,$sql_emp_set_bal);
		while($row_emp_set_bal = mysqli_fetch_assoc($res_emp_set_bal))
		{
			$emp_hierarchy_code = $row_emp_set_bal['emp_code'];
			$sql_check_allocation_balance = "SELECT SA.product_filter_code, SA.qty, SA.BAL FROM sauda_allocation SA, sauda_details SD, product_master PM WHERE SD.sku_code = '".$product_code."' AND SD.sku_code = PM.prod_code AND PM.product_group_code = SA.product_filter_code AND SA.emp_code = '$emp_hierarchy_code' AND substring(SD.sauda_no,-14,8) = '$today' AND SA.emp_code = '".$emp_hierarchy_code."'";
			$res_check_allocation_balance = mysqli_query($link,$sql_check_allocation_balance);
			$row_check_allocation_balance = mysqli_fetch_assoc($res_check_allocation_balance);
			
			$quantity_alloted = $row_check_allocation_balance['qty'];
			$quantity_remain = $row_check_allocation_balance['BAL'];
			$product_group_code = $row_check_allocation_balance['product_filter_code'];
			$quantity_remain_now = $quantity_remain+$convert_qty_two;
			
			$sql_update_sauda_allocation = "UPDATE sauda_allocation SET BAL = '".$quantity_remain_now."' WHERE product_filter_code = '$product_group_code' AND emp_code = '".$emp_hierarchy_code."'";
			$res_update_sauda_allocation = mysqli_query($link,$sql_update_sauda_allocation);
		}
	}
	
	else
	{
		$sql_reporting_to = "SELECT reporting_to FROM employee_master WHERE emp_code = '".$get_emp_code."'";
		$res_reporting_to = mysqli_query($link,$sql_reporting_to);
		$row_reporting_to = mysqli_fetch_assoc($res_reporting_to);
		$emp_code_reporting = $row_reporting_to['reporting_to'];
		
		$emp_upper_hierarchy = return_employee_upper_hierarchy($emp_code_reporting);
		$emp_upper_hierarchy .= ",'".$emp_code_reporting."'";
		$sql_emp_set_bal = "SELECT emp_name, emp_code FROM employee_master WHERE emp_code IN (".$emp_upper_hierarchy.")";
		$res_emp_set_bal = mysqli_query($link,$sql_emp_set_bal);
		while($row_emp_set_bal = mysqli_fetch_assoc($res_emp_set_bal))
		{
			$emp_hierarchy_code = $row_emp_set_bal['emp_code'];
			$sql_check_allocation_balance = "SELECT SA.product_filter_code, SA.qty, SA.BAL FROM sauda_allocation SA, sauda_details SD, product_master PM WHERE SD.sku_code = '".$product_code."' AND SD.sku_code = PM.prod_code AND PM.product_group_code = SA.product_filter_code AND SA.emp_code = '".$emp_hierarchy_code."' AND substring(SD.sauda_no,-14,8) = '".$today."' AND SA.emp_code = '".$emp_hierarchy_code."'";
			$res_check_allocation_balance = mysqli_query($link,$sql_check_allocation_balance);
			$row_check_allocation_balance = mysqli_fetch_assoc($res_check_allocation_balance);
			
			$quantity_alloted = $row_check_allocation_balance['qty'];
			$quantity_remain = $row_check_allocation_balance['BAL'];
			$product_group_code = $row_check_allocation_balance['product_filter_code'];
			$quantity_remain_now = $quantity_remain+$convert_qty_two;
			
			$sql_update_sauda_allocation = "UPDATE sauda_allocation SET BAL = '".$quantity_remain_now."' WHERE product_filter_code = '".$product_group_code."' AND emp_code = '".$emp_hierarchy_code."'";
			$res_update_sauda_allocation = mysqli_query($link,$sql_update_sauda_allocation);
		}
	}
}

$sql_delete_sauda = "DELETE FROM sauda_details WHERE sku_code = '".$product_code."' AND sauda_no = '".$sauda_number."'";
$res_delete_sauda = mysqli_query($link,$sql_delete_sauda);

$sql_delete_transaction_log = "DELETE FROM sauda_transaction_log WHERE sauda_no='".$sauda_number."'";
$res_delete_transaction_log = mysqli_query($link,$sql_delete_transaction_log);

$sql_delete_data_download_log = "DELETE FROM sauda_download_log WHERE sauda_no='".$sauda_number."'";
$res_delete_data_download_log = mysqli_query($link,$sql_delete_data_download_log);

if($total_product == 1)
{
	$sauda_number = "N".$sauda_number;
	$sql_update_location = "UPDATE location SET trans_id = '".$sauda_number."' WHERE trans_id = '".$check_sauda_number."'";
	$res_update_location = mysqli_query($link,$sql_update_location);
	
	$sql_update_saudaheader = "UPDATE sauda_header SET sauda_no = '$sauda_number' WHERE sauda_no = '$check_sauda_number'";
	$res_update_saudaheader = mysqli_query($link,$sql_update_saudaheader);
}

$sql_update_sauda_activity_log = "INSERT INTO sauda_activity_log SET transaction_id='$check_sauda_number', transaction_date='$sauda_date', operation_done_by='$_SESSION[admin_login]',operation_type='deletion',affected_field='$product_code',previous_value='$previous_value',current_value='',operation_performed_ip='$ip',update_datetime=current_timestamp,update_flag='0'";
$res_update_sauda_allocation = mysqli_query($link,$sql_update_sauda_activity_log);

echo "Data succesfully deleted";
mysqli_close($link);
?>