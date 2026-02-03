<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

$today = date('Y-m-d');
$emp_code = $_REQUEST['emp_code'];
$product_code = $_REQUEST['product_code'];
$quantity = $_REQUEST['quantity'];

$emp_upper_hierarchy = return_employee_upper_hierarchy($emp_code);
$emp_upper_hierarchy=str_replace("'","",$emp_upper_hierarchy);
$emp_upper_hierarchy_array=explode(',',$emp_upper_hierarchy);

$sql_top_emp="SELECT emp_code FROM employee_master WHERE reporting_to=''";
$rs_top_emp=mysqli_query($link,$sql_top_emp);
$row_top_emp=mysqli_fetch_assoc($rs_top_emp);
$top_emp_code=$row_top_emp['emp_code'];

$flag = 1;

		$sql_check_exists = "SELECT TD FROM TD_allocation WHERE emp_code = '$emp_code' AND product_filter_code = '$product_code'";
		$res_check_exists = mysqli_query($link,$sql_check_exists);
		$total_rows = mysqli_num_rows($res_check_exists);
		
		if($total_rows>0)
		{
			/*foreach($emp_upper_hierarchy_array as $emp_upper_hierarchy_val)
			{
				$sql_check_TD = "SELECT TD FROM TD_allocation WHERE emp_code ='".$emp_upper_hierarchy_val."' AND product_filter_code ='".$product_code."'";
				$res_check_TD = mysqli_query($link,$sql_check_TD);
				$row_check_TD = mysqli_fetch_assoc($res_check_TD);
				$check_TD = $row_check_TD['TD'];
				if($quantity>$check_TD)
				{
					$flag = 0;
					break;
				}
			}*/
			
			/*if($flag == 1){*/
				$sql_update_sauda_allocation = "UPDATE TD_allocation SET 
												emp_code = '$emp_code', 
												product_filter_code = '$product_code', 
												TD = '$quantity'
												WHERE emp_code = '$emp_code' AND product_filter_code = '$product_code';";
				$res_update_sauda_allocation = mysqli_query($link,$sql_update_sauda_allocation);
				
				$sql_insert_sauda_log = "INSERT into TD_allocation_log SET 
					             emp_code = '$emp_code', 
								 product_filter_code = '$product_code', 
								 TD = '$quantity'";
				$res_insert_sauda_log = mysqli_query($link,$sql_insert_sauda_log);
				
				echo "Data successfully updated";
				die;/*}
			else if($flag == 0)
			{
				echo "TD greater than upper hierarchy";
				die;}*/
			
		}
		else
		{
			if(strtoupper($_SESSION['admin_login'])=='ADMIN' || $_SESSION['admin_login']==$top_emp_code)
			{
				$sql_update_sauda_allocation = "INSERT INTO TD_allocation SET 
												emp_code = '$emp_code', 
												product_filter_code = '$product_code', 
												TD = '$quantity'";
				$res_update_sauda_allocation = mysqli_query($link,$sql_update_sauda_allocation);
			}
			else
			{
				/*foreach($emp_upper_hierarchy_array as $emp_upper_hierarchy_val)
				{
					$sql_check_TD = "SELECT TD FROM TD_allocation WHERE emp_code ='".$emp_upper_hierarchy_val."' AND product_filter_code ='".$product_code."'";
					$res_check_TD = mysqli_query($link,$sql_check_TD);
					$row_check_TD = mysqli_fetch_assoc($res_check_TD);
					$check_TD = $row_check_TD['TD'];
					if($quantity>$check_TD)
					{
						$flag = 0;
						break;
					}
				}
				
				if($flag == 1)
				{*/
							
					$sql_update_sauda_allocation = "INSERT INTO TD_allocation SET 
													emp_code = '$emp_code', 
													product_filter_code = '$product_code', 
													TD = '$quantity'";
					$res_update_sauda_allocation = mysqli_query($link,$sql_update_sauda_allocation);
				/*}
				else if($flag == 0)
				{
					echo "TD greater than upper hierarchy";
					die;
				}*/
			}
		}
		
		$sql_insert_sauda_log = "INSERT into TD_allocation_log SET 
					             emp_code = '$emp_code', 
								 product_filter_code = '$product_code', 
								 TD = '$quantity'";
		$res_insert_sauda_log = mysqli_query($link,$sql_insert_sauda_log);
		
		
		/*if(strtoupper($_SESSION['admin_login'])=='ADMIN' || $_SESSION['admin_login']==$top_emp_code)
		{
			foreach($emp_upper_hierarchy_array as $emp_upper_hierarchy_val)
			{
				$sql_check_upper_hierarchy_qty = "SELECT TD FROM TD_allocation WHERE emp_code ='".$emp_upper_hierarchy_val."' AND product_filter_code ='".$product_code."'";
				$res_check_upper_hierarchy_qty = mysqli_query($link,$sql_check_upper_hierarchy_qty);
				$total_check_upper_hierarchy_qty = mysqli_num_rows($res_check_upper_hierarchy_qty);
				if($total_check_upper_hierarchy_qty>0)
				{
					$res_check_upper_hierarchy_qty = mysqli_query($link,$sql_check_upper_hierarchy_qty);
					$row_check_upper_hierarchy_qty = mysqli_fetch_assoc($res_check_upper_hierarchy_qty);
					$TD = $row_check_upper_hierarchy_qty['TD'];
					if($quantity>$TD)
					{
						echo "TD greater than upper hierarchy";
						die;
					}
				}
				else
				{
					$sql_update_allocation_upper_hierarchy = "INSERT INTO TD_allocation SET 
															  emp_code = '".$emp_upper_hierarchy_val."', 
															  product_filter_code = '".$product_code."', 
															  TD = '".$quantity."'";
					$res_update_allocation_upper_hierarchy = mysqli_query($link,$sql_update_allocation_upper_hierarchy);
				}
				$sql_insert_sauda_log_upper_hierarchy = "INSERT into TD_allocation_log SET 
													     emp_code = '".$emp_upper_hierarchy_val."', 
													     product_filter_code = '".$product_code."', 
														 TD = '".$quantity."'";
				$res_insert_sauda_log_upper_hierarchy = mysqli_query($link,$sql_insert_sauda_log_upper_hierarchy);
			}
		}*/
		//echo "$emp_code $product_code $quantity";
		echo "Data Updated Successfully";
		mysqli_close($link);
?>