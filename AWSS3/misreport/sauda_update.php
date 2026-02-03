<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	if(strtoupper($_SESSION['admin_login'])=='SFATS')
	{
		$admin_login_value='SFATS';
		$_SESSION['admin_login']='E0076';
	}
	else  $admin_login_value='';


$today = date('Y-m-d');
$emp_code = $_REQUEST['emp_code'];
$product_code = $_REQUEST['product_code'];
$quantity = $_REQUEST['quantity'];

function sauda_balance_qty($emp_code,$quantity)
{
	$sauda_booked_condition = "AND substring(SD.sauda_no,-14,8) LIKE '".str_replace("-","",$today)."'";
	$emp_hierarchy_condition=return_employee_hierarchy($emp_code);
	$sql_quantity_booked = "SELECT sum(SD.convert_qty_two) as mt_booked,PM.product_group_code FROM sauda_details SD, product_master PM 
							WHERE substring(SD.sauda_no,3,5) IN(".$emp_hierarchy_condition.")
							$sauda_booked_condition AND SD.sku_code=PM.prod_code AND PM.product_group_code='".$product_code."'";
	$res_quantity_booked = mysql_query($sql_quantity_booked);
	$row_quantity_booked = mysql_fetch_array($res_quantity_booked);
	$quantity_booked=$row_quantity_booked['mt_booked'];
	$sauda_balance = ($quantity-$quantity_booked);
	return $sauda_balance;
}
$emp_upper_hierarchy = return_employee_upper_hierarchy($emp_code);
$emp_upper_hierarchy=str_replace("'","",$emp_upper_hierarchy);
$emp_upper_hierarchy_array=explode(',',$emp_upper_hierarchy);
$sauda_balance_qty=sauda_balance_qty($emp_code,$quantity);

$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
$contentsdatetime =$year.$month.$date.$hour.$minute.$second."\n";

$sqlimmediatebossallocation="SELECT qty,emp_code FROM sauda_allocation WHERE 
								product_filter_code = '".$product_code."' AND emp_code =(SELECT reporting_to FROM employee_master WHERE emp_code='".$emp_code."')";
$rsimmediatebossallocation=mysql_query($sqlimmediatebossallocation);
$rowimmediatebossallocation=mysql_fetch_array($rsimmediatebossallocation);
$immediate_boss_allocation=$rowimmediatebossallocation['qty'];
$immediate_boss_emp_code=$rowimmediatebossallocation['emp_code'];
if($immediate_boss_allocation =='')   $immediate_boss_allocation=0;

$sqlchildcount="SELECT COUNT(emp_code) as tot_emp FROM employee_master WHERE reporting_to='".$immediate_boss_emp_code."'";
$rschildcount=mysql_query($sqlchildcount);
$rowqchildcount=mysql_fetch_array($rschildcount);
$tot_emp=$rowqchildcount['tot_emp'];
if($tot_emp > 1)
{
$sqlimmediatebosschildallocation="SELECT SUM(qty) AS child_qty FROM sauda_allocation WHERE 
								product_filter_code = '".$product_code."' AND 
								emp_code IN (SELECT emp_code FROM employee_master WHERE reporting_to='".$immediate_boss_emp_code."')";
$rsimmediatebosschildallocation=mysql_query($sqlimmediatebosschildallocation);
$rowimmediatebosschildallocation=mysql_fetch_array($rsimmediatebosschildallocation);
$immediate_boss_child_allocation=$rowimmediatebosschildallocation['child_qty'];
if($immediate_boss_child_allocation =='')   $immediate_boss_child_allocation=0;
$final_immediate_boss_allocation=$immediate_boss_allocation - $immediate_boss_child_allocation;
}
else
{
	$final_immediate_boss_allocation=$immediate_boss_allocation;
}

$sql_top_emp="SELECT emp_code FROM employee_master WHERE reporting_to=''";
$rs_top_emp=mysql_query($sql_top_emp);
$row_top_emp=mysql_fetch_array($rs_top_emp);
$top_emp_code=$row_top_emp['emp_code'];

		$sql_check_exists = "SELECT qty FROM sauda_allocation WHERE emp_code = '$emp_code' AND product_filter_code = '$product_code'";
		$res_check_exists = mysql_query($sql_check_exists);
		$total_rows = mysql_num_rows($res_check_exists);
		
		$trans_id='FA'.$emp_code.$contentsdatetime;
		$sqlinsertlocation="INSERT INTO location SET emp_code='".$_SESSION['admin_login']."',
							trans_id='".$trans_id."',
							latt='".$_SERVER['REMOTE_ADDR']."',
							longi='0',
							date=CURRENT_TIMESTAMP,
							updatetime=CURRENT_TIMESTAMP"; 
	   if(mysql_query($sqlinsertlocation))
	   {
			if($total_rows>0)
			{
				$sql_update_sauda_allocation = "UPDATE sauda_allocation SET 
												emp_code = '$emp_code', 
												product_filter_code = '$product_code', 
												qty = '$quantity',
												BAL = '$sauda_balance_qty'
												WHERE emp_code = '$emp_code' AND product_filter_code = '$product_code';";
				$res_update_sauda_allocation = mysql_query($sql_update_sauda_allocation);
			}
			else
			{
				$sql_update_sauda_allocation = "INSERT INTO sauda_allocation SET 
												emp_code = '$emp_code', 
												product_filter_code = '$product_code', 
												qty = '$quantity',
												BAL = '$sauda_balance_qty';";
				$res_update_sauda_allocation = mysql_query($sql_update_sauda_allocation);
			}
			$sql_insert_sauda_log = "INSERT into sauda_allocation_log SET 
									 allocation_id='".$trans_id."',
									 emp_code = '$emp_code', 
									 product_filter_code = '$product_code', 
									qty = '$quantity'";
			$res_insert_sauda_log = mysql_query($sql_insert_sauda_log);
	   }
		
		if((strtoupper($_SESSION['admin_login'])=='ADMIN' || $_SESSION['admin_login']==$top_emp_code || $_SESSION['admin_login']=='E0076') && $quantity > $final_immediate_boss_allocation)
		{
			foreach($emp_upper_hierarchy_array as $emp_upper_hierarchy_val)
			{
				$date=gmdate('d',strtotime('+330 minute'));
				$month=gmdate('m',strtotime('+330 minute'));
				$year=gmdate('Y',strtotime('+330 minute'));
				
				$hour=gmdate('H',strtotime('+330 minute'));
				$minute=gmdate('i',strtotime('+330 minute'));
				$second=gmdate('s',strtotime('+330 minute'));
				$contentsdatetime =$year.$month.$date.$hour.$minute.$second."\n";

				$trans_id='FA'.$emp_upper_hierarchy_val.$contentsdatetime;
				$sqlinsertlocation="INSERT INTO location SET emp_code='".$_SESSION['admin_login']."',
									trans_id='".$trans_id."',
									latt='".$_SERVER['REMOTE_ADDR']."',
									longi='0',
									date=CURRENT_TIMESTAMP,
									updatetime=CURRENT_TIMESTAMP"; 
				mysql_query($sqlinsertlocation);					

				//$emp_wise_hierarchy_condition=return_employee_hierarchy($emp_upper_hierarchy_val);
				$sql_total_qty="SELECT SUM(qty) AS total_qty FROM sauda_allocation WHERE product_filter_code ='".$product_code."' AND emp_code IN(SELECT emp_code FROM employee_master WHERE reporting_to='".$emp_upper_hierarchy_val."')";
				$rs_total_qty=mysql_query($sql_total_qty);
				$row_total_qty=mysql_fetch_array($rs_total_qty);
				$total_qty=$row_total_qty['total_qty'];
				$sauda_balance_qty_upper_hierarchy=sauda_balance_qty($emp_upper_hierarchy_val,$total_qty);
				$sql_check_upper_hierarchy_qty = "SELECT qty FROM sauda_allocation WHERE emp_code ='".$emp_upper_hierarchy_val."' AND product_filter_code ='".$product_code."'";
				$res_check_upper_hierarchy_qty = mysql_query($sql_check_upper_hierarchy_qty);
				$total_check_upper_hierarchy_qty = mysql_num_rows($res_check_upper_hierarchy_qty);
				if($total_check_upper_hierarchy_qty>0)
				{
					$sql_update_allocation_upper_hierarchy = "UPDATE sauda_allocation SET 
															emp_code = '".$emp_upper_hierarchy_val."', 
															product_filter_code = '".$product_code."', 
															qty = '".$total_qty."',
															BAL = '".$sauda_balance_qty_upper_hierarchy."'
															WHERE emp_code = '".$emp_upper_hierarchy_val."' AND product_filter_code = '".$product_code."'";
					$res_update_sauda_allocation_upper_hierarchy = mysql_query($sql_update_allocation_upper_hierarchy);
				}
				else
				{
					$sql_update_allocation_upper_hierarchy = "INSERT INTO sauda_allocation SET 
															  emp_code = '".$emp_upper_hierarchy_val."', 
															  product_filter_code = '".$product_code."', 
															  qty = '".$total_qty."',
															  BAL = '".$total_qty."'";
					$res_update_allocation_upper_hierarchy = mysql_query($sql_update_allocation_upper_hierarchy);
				}
				$sql_insert_sauda_log_upper_hierarchy = "INSERT into sauda_allocation_log SET
														 allocation_id='".$trans_id."',
													     emp_code = '".$emp_upper_hierarchy_val."', 
													     product_filter_code = '".$product_code."', 
														 qty = '".$total_qty."'";
				$res_insert_sauda_log_upper_hierarchy = mysql_query($sql_insert_sauda_log_upper_hierarchy);
			}
		}
		//echo "$emp_code $product_code $quantity";
		echo "Data Updated Successfully";
		if($admin_login_value=='SFATS')
		{
			$_SESSION['admin_login']='SFATS';
		}
		mysql_close($link);
?>