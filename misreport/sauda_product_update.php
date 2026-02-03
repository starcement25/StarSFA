<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");


if($_SESSION['admin_login']=="admin")
{
	$sql_select_emp = "SELECT emp_code FROM employee_master WHERE reporting_to = ''";
	$res_select_emp = mysqli_query($link,$sql_select_emp);
	$row_select_emp = mysqli_fetch_assoc($res_select_emp);
	$emp = $row_select_emp['emp_code'];
}


$ip = $_SERVER['REMOTE_ADDR'];

$sauda_number = $_REQUEST['sauda_number'];
$product_code = $_REQUEST['product_code'];
$booked = $_REQUEST['booked'];
$freight = $_REQUEST['freight'];
$trade_discount = $_REQUEST['trade_discount'];
$premium = $_REQUEST['premium'];
$sauda_date = date('Y-m-d',strtotime(substr($sauda_number,-14,8)));

$sql_select_booked = "SELECT * FROM sauda_details WHERE sku_code = '".$product_code."' AND sauda_no = '".$sauda_number."'";
$res_select_booked = mysqli_query($link,$sql_select_booked);
$row_select_booked = mysqli_fetch_assoc($res_select_booked);
$booked_mt_before = $row_select_booked['convert_qty_two'];
$booked_case_before = $row_select_booked['qty'];
$freight_charge_before = $row_select_booked['freight_charge'];
$trade_discount_before = $row_select_booked['TD'];
$premium_before = $row_select_booked['premium'];
$depot_cost = $row_select_booked['depot_cost'];
$primary_freight = $row_select_booked['primary_freight'];
$liquid_TD = $row_select_booked['liquid_TD'];
$sale_rate = $row_select_booked['sale_rate'];

$affected_field = '';
$current_value = '';
$previous_value = '';
if($booked != $booked_case_before)
{
	$affected_field .= 'qty,';
	$current_value .= $booked.",";
	$previous_value .= $booked_case_before.",";
}
if($freight != $freight_charge_before)
{
	$affected_field .= 'freight_charge,';
	$current_value .= $freight.",";
	$previous_value .= $freight_charge_before.",";
}

if($premium>0)
{
	if($premium != $premium_before)
	{
		$affected_field .= 'premium,';
		$current_value .= $premium.",";
		$previous_value .= $premium_before.",";
	}
}
else
{
	if($trade_discount != $trade_discount_before)
	{
		$affected_field .= 'TD,';
		$current_value .= $trade_discount.",";
		$previous_value .= $trade_discount_before.",";
	}
}

$affected_field = rtrim($affected_field, ",");
$current_value = rtrim($current_value, ",");
$previous_value = rtrim($previous_value, ",");

$sql_product_details = "SELECT * FROM product_master WHERE prod_code = '".$product_code."'";
$res_product_details = mysqli_query($link,$sql_product_details);
$row_product_details = mysqli_fetch_assoc($res_product_details);

$conversion_factor = $row_product_details['conversion_factor'];
$conversion_factor_two = $row_product_details['conversion_factor_two'];

$convert_qty_one=round(($booked*$conversion_factor),3);
$convert_qty_two=round((($booked*$conversion_factor)/$conversion_factor_two),3);

$td = $row_product_details['TD'];

$get_date = substr($sauda_number,-14,8);
$today = date('Ymd');
$yesterday = date('Y-m-d',strtotime("-1 days"));

$sql_check_allocation_balance = "SELECT distinct(SA.product_filter_code), SA.qty, SA.BAL ,PM.dns_prod_code FROM sauda_allocation SA, sauda_details SD, product_master PM WHERE SD.sku_code = '".$product_code."' AND SD.sku_code = PM.prod_code AND PM.product_group_code = SA.product_filter_code";
$res_check_allocation_balance = mysqli_query($link,$sql_check_allocation_balance);
$row_check_allocation_balance = mysqli_fetch_assoc($res_check_allocation_balance);

$quantity_alloted = $row_check_allocation_balance['qty'];
$quantity_remain = $row_check_allocation_balance['BAL'];
$product_group_code = $row_check_allocation_balance['product_filter_code'];
$dns_prod_code = $row_check_allocation_balance['dns_prod_code'];

$get_emp_code = substr($sauda_number,2,5);

if($get_date == $today)
{
	if(TD_allocation_app=='yes')
	{
		$sql_allocated_TD="SELECT TD FROM  TD_allocation WHERE emp_code = '".$get_emp_code."' AND product_filter_code = '".$product_group_code."'";
		$rs_allocated_TD=mysqli_query($link,$sql_allocated_TD);
		$row_allocated_TD=mysqli_fetch_assoc($rs_allocated_TD);
		$td=$row_allocated_TD['TD'];
	}
	else
	{
		$td=$td;
	}
	if($td<$trade_discount)
	{
		echo "Trade discount cannot be greater than assigned";
		die;
	}
	else
	{
		$sql_emp_allocation_access = "SELECT SAA.get_allocation FROM sauda_allocation_access SAA, employee_master EM WHERE EM.emp_code = 
		'".substr($sauda_number,2,5)."' AND EM.designation = SAA.designation";
		$res_emp_allocation_access = mysqli_query($link,$sql_emp_allocation_access);
		$row_emp_allocation_access = mysqli_fetch_assoc($res_emp_allocation_access);
		$allocation_access = $row_emp_allocation_access['get_allocation'];

		$sql_check_quantity_bal = "SELECT * FROM sauda_allocation WHERE emp_code = '".$get_emp_code."' AND product_filter_code = '".$product_group_code."'";
		$res_check_quantity_bal = mysqli_query($link,$sql_check_quantity_bal);
		$total_rows = mysqli_num_rows($res_check_quantity_bal);

		if($allocation_access == 'yes' && $total_rows>0)
		{
			$sql_check_quantity_bal = "SELECT * FROM sauda_allocation WHERE emp_code = '".$get_emp_code."' AND product_filter_code = '".$product_group_code."'";
			$res_check_quantity_bal = mysqli_query($link,$sql_check_quantity_bal);
			$row_check_quantity_bal = mysqli_fetch_assoc($res_check_quantity_bal);
			$get_emp_qty_alloted = $row_check_quantity_bal['qty'];
			$get_emp_bal_remain = $row_check_quantity_bal['BAL'];

			/*if($convert_qty_two>$get_emp_qty_alloted)
			{
				echo "Booked quantity cannot be greater than alloted";
				die;
			}

			if($convert_qty_two>$get_emp_bal_remain)
			{
				echo "Booked quantity cannot be greater than remaining quantity";
				die;
			}*/

			if($convert_qty_two>$booked_mt_before)
			{
				$booked_now = $convert_qty_two-$booked_mt_before;
				$quantity_remain_now = $get_emp_bal_remain-$booked_now;
			}
			else if($convert_qty_two<$booked_mt_before)
			{
				$booked_now = $booked_mt_before-$convert_qty_two;
				$quantity_remain_now = $get_emp_bal_remain+$booked_now;
			}
			/*if($quantity_remain_now>$get_emp_qty_alloted)
			{
				echo "Booked quantity exceeding alloted";
				die;
			}*/

			$emp_hierarchy = return_employee_upper_hierarchy($get_emp_code);
			$emp_hierarchy .= ",'".$get_emp_code."'";

			$sql_empl_set_balance = "SELECT emp_name, emp_code FROM employee_master WHERE emp_code IN (".$emp_hierarchy.")";
			$res_empl_set_balance = mysqli_query($link,$sql_empl_set_balance);
			while($row_empl_set_balance = mysqli_fetch_assoc($res_empl_set_balance))
			{
				$emp_code_set_balance = $row_empl_set_balance['emp_code'];

				$sql_check_allocation = "SELECT SA.product_filter_code, SA.qty, SA.BAL FROM sauda_allocation SA WHERE SA.product_filter_code = '".$product_group_code."' AND emp_code = '".$emp_code_set_balance."'";
				$res_check_allocation = mysqli_query($link,$sql_check_allocation);
				$row_check_allocation = mysqli_fetch_assoc($res_check_allocation);

				$quantity_alloted = $row_check_allocation_balance['qty'];
				$quantity_remain = $row_check_allocation_balance['BAL'];

				if($convert_qty_two>$booked_mt_before)
				{
					$booked_now = $convert_qty_two-$booked_mt_before;
					$quantity_remain_now = $quantity_remain-$booked_now;
				}
				else if($convert_qty_two<$booked_mt_before)
				{
					$booked_now = $booked_mt_before-$convert_qty_two;
					$quantity_remain_now = $quantity_remain+$booked_now;
				}

				$sql_update_sauda_allocation = "UPDATE sauda_allocation SET BAL = '".$quantity_remain_now."' WHERE product_filter_code = '".$product_group_code."' AND emp_code = '".$emp_code_set_balance."'";
				$res_update_sauda_allocation = mysqli_query($link,$sql_update_sauda_allocation);
			}
		}
		else
		{
			$sql_select_reporting_to = "SELECT reporting_to FROM employee_master WHERE emp_code = '".$get_emp_code."'";
			$res_select_reporting_to = mysqli_query($link,$sql_select_reporting_to);
			$row_select_reporting_to = mysqli_fetch_assoc($res_select_reporting_to);
			$reporting_to_emp = $row_select_reporting_to['reporting_to'];

			$sql_check_quantity_bal = "SELECT * FROM sauda_allocation WHERE emp_code = '".$reporting_to_emp."' AND product_filter_code = '".$product_group_code."'";
			$res_check_quantity_bal = mysqli_query($link,$sql_check_quantity_bal);
			$row_check_quantity_bal = mysqli_fetch_assoc($res_check_quantity_bal);
			$get_emp_qty_alloted = $row_check_quantity_bal['qty'];
			$get_emp_bal_remain = $row_check_quantity_bal['BAL'];

			/*if($convert_qty_two>$get_emp_qty_alloted)
			{
				echo "Booked quantity cannot be greater than alloted";
				die;
			}*/

			/*if($convert_qty_two>$get_emp_bal_remain)
			{
				echo "Booked quantity cannot be greater than remaining quantity";
				die;
			}*/

			if($convert_qty_two>$booked_mt_before)
			{
				$booked_now = $convert_qty_two-$booked_mt_before;
				$quantity_remain_now = $get_emp_bal_remain-$booked_now;
			}
			else if($convert_qty_two<$booked_mt_before)
			{
				$booked_now = $booked_mt_before-$convert_qty_two;
				$quantity_remain_now = $get_emp_bal_remain+$booked_now;
			}
			if($quantity_remain_now>$get_emp_qty_alloted)
			{
				echo "Booked quantity exceeding alloted";
				die;
			}

			$emp_hierarchy = return_employee_upper_hierarchy($reporting_to_emp);
			$emp_hierarchy .= ",'".$reporting_to_emp."'";

			$sql_empl_set_balance = "SELECT emp_name, emp_code FROM employee_master WHERE emp_code IN (".$emp_hierarchy.")";
			$res_empl_set_balance = mysqli_query($link,$sql_empl_set_balance);
			while($row_empl_set_balance = mysqli_fetch_assoc($res_empl_set_balance))
			{
				$emp_code_set_balance = $row_empl_set_balance['emp_code'];

				$sql_check_allocation = "SELECT SA.product_filter_code, SA.qty, SA.BAL FROM sauda_allocation SA WHERE SA.product_filter_code = '".$product_group_code."' AND emp_code = '".$emp_code_set_balance."'";
				$res_check_allocation = mysqli_query($link,$sql_check_allocation);
				$row_check_allocation = mysqli_fetch_assoc($res_check_allocation);

				$quantity_alloted = $row_check_allocation_balance['qty'];
				$quantity_remain = $row_check_allocation_balance['BAL'];

				if($convert_qty_two>$booked_mt_before)
				{
					$booked_now = $convert_qty_two-$booked_mt_before;
					$quantity_remain_now = $quantity_remain-$booked_now;
				}
				else if($convert_qty_two<$booked_mt_before)
				{
					$booked_now = $booked_mt_before-$convert_qty_two;
					$quantity_remain_now = $quantity_remain+$booked_now;
				}

				$sql_update_sauda_allocation = "UPDATE sauda_allocation SET BAL = '".$quantity_remain_now."' WHERE product_filter_code = '".$product_group_code."' AND emp_code = '".$emp_code_set_balance."'";
				$res_update_sauda_allocation = mysqli_query($link,$sql_update_sauda_allocation);
			}
		}

		/*$sql_update_sauda_details = "UPDATE sauda_details SET qty = '".$booked."', convert_qty_one = '".$convert_qty_one."', convert_qty_two = '".$convert_qty_two."', TD = '".$trade_discount."' premium = '".$premium."', freight_charge = '".$freight."', 
		amount = (((qty*$freight)+(qty*sale_rate))-(qty*$trade_discount)) (qty*($primary_freight+$freight_charge+$depot_cost+$premium-$trade_discount-$liquid_TD
		WHERE sku_code = '".$product_code."' AND sauda_no = '".$sauda_number."'";*/
		$sql_update_sauda_details = "UPDATE sauda_details SET qty = '".$booked."', convert_qty_one = '".$convert_qty_one."', convert_qty_two = '".$convert_qty_two."', TD = '".$trade_discount."', premium = '".$premium."', freight_charge = '".$freight."', 
		amount = ROUND((qty*($sale_rate+$primary_freight+$freight+$depot_cost+$premium-$trade_discount-$liquid_TD)),2)
		WHERE sku_code = '".$product_code."' AND sauda_no = '".$sauda_number."'";
		$res_update_sauda_details = mysqli_query($link,$sql_update_sauda_details);
		
		$sql_update_transaction_log = "UPDATE sauda_transaction_log SET qty = '".$booked."', convert_qty_one = '".$convert_qty_one."', convert_qty_two = '".$convert_qty_two."', TD = '".$trade_discount."', premium = '".$premium."', freight_charge = '".$freight."', 
		amount =  ROUND((qty*($sale_rate+$primary_freight+$freight+$depot_cost+$premium-$trade_discount-$liquid_TD)),2)
		WHERE prod_code = '".$product_code."' AND sauda_no = '".$sauda_number."'";
		$res_update_transaction_log = mysqli_query($link,$sql_update_transaction_log);
		
		$sql_update_download_log = "UPDATE sauda_download_log SET qty = '".$booked."', convert_qty_two = '".$convert_qty_two."', 
		TD = '".$trade_discount."', premium = '".$premium."', freight_charge = '".$freight."', 
		amount =  ROUND((qty*($sale_rate+$primary_freight+$freight+$depot_cost+$premium-$trade_discount-$liquid_TD)),2)
		WHERE prod_code = '".$dns_prod_code."' AND sauda_no = '".$sauda_number."'";
		$res_update_download_log = mysqli_query($link,$sql_update_download_log);
		
		$sql_update_sauda_activity_log = "INSERT INTO sauda_activity_log SET transaction_id='".$sauda_number."', transaction_date='".$sauda_date."', operation_done_by='".$_SESSION['admin_login']."',operation_type='updation',affected_field='".$affected_field."',previous_value='".$previous_value."',current_value='".$current_value."',operation_performed_ip='".$ip."',update_datetime=current_timestamp,update_flag='0'";
		$res_update_sauda_allocation = mysqli_query($link,$sql_update_sauda_activity_log);

		echo "Data successfully updated";
	}
}
else
{
	if(TD_allocation_app=='yes')
	{
		$sql_allocated_TD="SELECT TD FROM  TD_allocation_log WHERE emp_code = '".$get_emp_code."' AND 
						product_filter_code = '".$product_group_code."' AND allocation_date LIKE '%".$yesterday."%' ORDER BY allocation_date DESC LIMIT 0,1";
		$rs_allocated_TD=mysqli_query($link,$sql_allocated_TD);
		$row_allocated_TD=mysqli_fetch_assoc($rs_allocated_TD);
		$td=$row_allocated_TD['TD'];
	}
	else
	{
		$td=$td;
	}
	if($td<$trade_discount)
	{
		echo "Trade discount cannot be greater than assigned";
		die;
	}
	else
	{
		$sql_emp_allocation_access = "SELECT SAA.get_allocation FROM sauda_allocation_access SAA, employee_master EM WHERE EM.emp_code = '".substr($sauda_number,2,5)."' AND EM.designation = SAA.designation";
		$res_emp_allocation_access = mysqli_query($link,$sql_emp_allocation_access);
		$row_emp_allocation_access = mysqli_fetch_assoc($res_emp_allocation_access);
		$allocation_access = $row_emp_allocation_access['get_allocation'];


		$sql_check_quantity_bal = "SELECT * FROM sauda_allocation_log WHERE emp_code = '".$get_emp_code."' AND product_filter_code = '".$product_group_code."' AND allocation_date LIKE '%".$yesterday."%'";
		$res_check_quantity_bal = mysqli_query($link,$sql_check_quantity_bal);
		$total_rows = mysqli_num_rows($res_check_quantity_bal);


		if($allocation_access == 'yes' && $total_rows>0)
		{
			$sql_check_quantity_bal = "SELECT * FROM sauda_allocation_log WHERE emp_code = '".$get_emp_code."' AND product_filter_code = '".$product_group_code."' AND substring(allocation_date,1,10) = (SELECT max(substring(allocation_date,1,10)) FROM sauda_allocation_log WHERE emp_code = '".$get_emp_code."' AND product_filter_code = '".$product_group_code."' AND substring(allocation_date,1,10) = '".$yesterday."')";
			$res_check_quantity_bal = mysqli_query($link,$sql_check_quantity_bal);
			$row_check_quantity_bal = mysqli_fetch_assoc($res_check_quantity_bal);
			$get_emp_qty_alloted = $row_check_quantity_bal['qty'];

			$empl_hierarchy = return_employee_hierarchy($get_emp_code);

			$sql_total_booked_sauda_details = "SELECT sum(SD.convert_qty_two) as booked_mt FROM sauda_details SD, product_master PM WHERE substring(SD.sauda_no,3,5) IN (".$empl_hierarchy.") AND date_format(substring(SD.sauda_no,-14,8),'%Y-%m-%d') = '".$yesterday."' AND SD.sauda_no = '".$sauda_number."' AND SD.sku_code = PM.prod_code AND PM.product_group_code = '".$product_group_code."'";
			$res_total_booked_sauda_details = mysqli_query($link,$sql_total_booked_sauda_details);
			$row_total_booked_sauda_details = mysqli_fetch_assoc($res_total_booked_sauda_details);
			$total_booked_yesterday = $row_total_booked_sauda_details['booked_mt'];

			/*if($convert_qty_two>$get_emp_qty_alloted)
			{
				echo "Booked quantity cannot be greater than alloted";
				die;
			}*/

			$get_emp_bal_remain = $get_emp_qty_alloted-$total_booked_yesterday;

			/*if($convert_qty_two>$get_emp_bal_remain)
			{
				echo "Booked quantity cannot be greater than remaining quantity";
				die;
			}*/

			if($convert_qty_two>$booked_mt_before)
			{
				$booked_now = $convert_qty_two-$booked_mt_before;
				$quantity_remain_now = $get_emp_bal_remain-$booked_now;
			}
			else if($convert_qty_two<$booked_mt_before)
			{
				$booked_now = $booked_mt_before-$convert_qty_two;
				$quantity_remain_now = $get_emp_bal_remain+$booked_now;
			}
			if($quantity_remain_now>$get_emp_qty_alloted)
			{
				echo "Booked quantity exceeding alloted";
				die;
			}

		}
		else
		{
			$sql_select_reporting_to = "SELECT reporting_to FROM employee_master WHERE emp_code = '".$get_emp_code."'";
			$res_select_reporting_to = mysqli_query($link,$sql_select_reporting_to);
			$row_select_reporting_to = mysqli_fetch_assoc($res_select_reporting_to);
			$reporting_to_emp = $row_select_reporting_to['reporting_to'];

			$sql_check_quantity_bal = "SELECT * FROM sauda_allocation_log WHERE emp_code = '".$reporting_to_emp."' AND product_filter_code = '".$product_group_code."' AND allocation_date = (SELECT max(allocation_date) FROM sauda_allocation_log WHERE emp_code = '".$reporting_to_emp."' AND product_filter_code = '".$product_group_code."' AND allocation_date LIKE '%".$yesterday."%')";
			$res_check_quantity_bal = mysqli_query($link,$sql_check_quantity_bal);
			$row_check_quantity_bal = mysqli_fetch_assoc($res_check_quantity_bal);
			$get_emp_qty_alloted = $row_check_quantity_bal['qty'];

			$empl_hierarchy = return_employee_hierarchy($reporting_to_emp);

			$sql_total_booked_sauda_details = "SELECT sum(SD.convert_qty_two) as booked_mt FROM sauda_details SD, product_master PM WHERE substring(SD.sauda_no,3,5) IN (".$empl_hierarchy.") AND date_format(substring(SD.sauda_no,-14,8),'%Y-%m-%d') = '".$yesterday."' AND SD.sauda_no = '".$sauda_number."' AND SD.sku_code = PM.prod_code AND PM.product_group_code = '".$product_group_code."'";
			$res_total_booked_sauda_details = mysqli_query($link,$sql_total_booked_sauda_details);
			$row_total_booked_sauda_details = mysqli_fetch_assoc($res_total_booked_sauda_details);
			$total_booked_yesterday = $row_total_booked_sauda_details['booked_mt'];

			/*if($convert_qty_two>$get_emp_qty_alloted)
			{
				echo "Booked quantity cannot be greater than alloted";
				die;
			}*/

			$get_emp_bal_remain = $get_emp_qty_alloted-$total_booked_yesterday;

			/*if($convert_qty_two>$get_emp_bal_remain)
			{
				echo "Booked quantity cannot be greater than remaining quantity";
				die;
			}*/

			if($convert_qty_two>$booked_mt_before)
			{
				$booked_now = $convert_qty_two-$booked_mt_before;
				$quantity_remain_now = $get_emp_bal_remain-$booked_now;
			}
			else if($convert_qty_two<$booked_mt_before)
			{
				$booked_now = $booked_mt_before-$convert_qty_two;
				$quantity_remain_now = $get_emp_bal_remain+$booked_now;
			}
			/*if($quantity_remain_now>$get_emp_qty_alloted)
			{
				echo "Booked quantity exceeding alloted";
				die;
			}*/

		}
	}
	$sql_update_sauda_details = "UPDATE sauda_details SET qty = '".$booked."', convert_qty_one = '".$convert_qty_one."', convert_qty_two = '".$convert_qty_two."', TD = '".$trade_discount."',premium = '".$premium."', freight_charge = '".$freight."', amount =ROUND( (qty*($sale_rate+$primary_freight+$freight+$depot_cost+$premium-$trade_discount-$liquid_TD)),2) WHERE sku_code = '".$product_code."' AND sauda_no = '".$sauda_number."'";
		$res_update_sauda_details = mysqli_query($link,$sql_update_sauda_details);
		
		$sql_update_transaction_log = "UPDATE sauda_transaction_log SET qty = '".$booked."', convert_qty_one = '".$convert_qty_one."', convert_qty_two = '".$convert_qty_two."', TD = '".$trade_discount."',premium = '".$premium."', freight_charge = '".$freight."', 
		amount =  ROUND((qty*($sale_rate+$primary_freight+$freight+$depot_cost+$premium-$trade_discount-$liquid_TD)),2)
		WHERE prod_code = '".$product_code."' AND sauda_no = '".$sauda_number."'";
		$res_update_transaction_log = mysqli_query($link,$sql_update_transaction_log);
		
		$sql_update_download_log = "UPDATE sauda_download_log SET qty = '".$booked."', convert_qty_two = '".$convert_qty_two."', TD = '".$trade_discount."', premium = '".$premium."', freight_charge = '".$freight."', 
		amount =  ROUND((qty*($sale_rate+$primary_freight+$freight+$depot_cost+$premium-$trade_discount-$liquid_TD)),2) WHERE prod_code = '".$dns_prod_code."' AND sauda_no = '".$sauda_number."'";
		
		$res_update_download_log = mysqli_query($link,$sql_update_download_log);
	$sql_update_sauda_activity_log = "INSERT INTO sauda_activity_log SET transaction_id='".$sauda_number."', transaction_date='".$sauda_date."', operation_done_by='".$_SESSION['admin_login']."',operation_type='updation',affected_field='".$affected_field."',previous_value='".$previous_value."',current_value='".$current_value."',operation_performed_ip='".$ip."',update_datetime=current_timestamp,update_flag='0'";
	$res_update_sauda_allocation = mysqli_query($link,$sql_update_sauda_activity_log);

	echo "Data successfully updated";

	mysqli_close($link);
}
?>
