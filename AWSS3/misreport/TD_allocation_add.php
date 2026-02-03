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
?>

<?php

$today = date('Y-m-d');
$emp_code = $_REQUEST['emp_code'];
$product_code = $_REQUEST['product_code'];
$TD = $_REQUEST['TD'];

$emp_upper_hierarchy = return_employee_upper_hierarchy($emp_code);
$emp_upper_hierarchy=str_replace("'","",$emp_upper_hierarchy);
$emp_upper_hierarchy_array=explode(',',$emp_upper_hierarchy);

$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
$contentsdatetime =$year.$month.$date.$hour.$minute.$second;

$sql_top_emp="SELECT emp_code FROM employee_master WHERE reporting_to=''";
$rs_top_emp=mysql_query($sql_top_emp);
$row_top_emp=mysql_fetch_array($rs_top_emp);
$top_emp_code=$row_top_emp['emp_code'];

		$sql_check_exists = "SELECT TD FROM TD_allocation WHERE emp_code = '".$emp_code."' AND product_filter_code = '".$product_code."'";
		$res_check_exists = mysql_query($sql_check_exists);
		$total_rows = mysql_num_rows($res_check_exists);
		
		$trans_id='TDA'.$emp_code.$contentsdatetime;
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
				$sql_update_TD_allocation = "UPDATE TD_allocation SET 
												TD = '".$TD."'
												WHERE emp_code = '".$emp_code."' AND product_filter_code = '".$product_code."'";
				$res_update_TD_allocation  = mysql_query($sql_update_TD_allocation);
			}
			else
			{
				$sql_update_TD_allocation = "INSERT INTO TD_allocation SET 
												emp_code = '".$emp_code."', 
												product_filter_code = '".$product_code."', 
												TD = '".$TD."'";
				$res_update_TD_allocation = mysql_query($sql_update_TD_allocation);
			}
			$sql_insert_TD_log = "INSERT into TD_allocation_log SET 
									 allocation_id='".$trans_id."',
									 allocation_date=CURRENT_TIMESTAMP(),
									 emp_code = '".$emp_code."', 
									 product_filter_code = '".$product_code."', 
									TD = '".$TD."'";
			$res_insert_TD_log = mysql_query($sql_insert_TD_log);
	   }
		
		if(strtoupper($_SESSION['admin_login'])=='ADMIN' || $_SESSION['admin_login']==$top_emp_code)
		{
			foreach($emp_upper_hierarchy_array as $emp_upper_hierarchy_val)
			{
				$date=gmdate('d',strtotime('+330 minute'));
				$month=gmdate('m',strtotime('+330 minute'));
				$year=gmdate('Y',strtotime('+330 minute'));
				
				$hour=gmdate('H',strtotime('+330 minute'));
				$minute=gmdate('i',strtotime('+330 minute'));
				$second=gmdate('s',strtotime('+330 minute'));
				$contentsdatetime =$year.$month.$date.$hour.$minute.$second;

				$trans_id='TDA'.$emp_upper_hierarchy_val.$contentsdatetime;
				$sqlinsertlocation="INSERT INTO location SET emp_code='".$_SESSION['admin_login']."',
									trans_id='".$trans_id."',
									latt='".$_SERVER['REMOTE_ADDR']."',
									longi='0',
									date=CURRENT_TIMESTAMP,
									updatetime=CURRENT_TIMESTAMP"; 
				mysql_query($sqlinsertlocation);					

				$sql_check_upper_hierarchy_TD = "SELECT TD FROM TD_allocation WHERE emp_code ='".$emp_upper_hierarchy_val."' AND product_filter_code ='".$product_code."'";
				$res_check_upper_hierarchy_TD = mysql_query($sql_check_upper_hierarchy_TD);
				$total_check_upper_hierarchy_TD = mysql_num_rows($res_check_upper_hierarchy_TD);
				if($total_check_upper_hierarchy_TD>0)
				{
					$sql_update_allocation_upper_hierarchy = "UPDATE TD_allocation SET 
															TD = '".$TD."'
															WHERE emp_code = '".$emp_upper_hierarchy_val."' AND product_filter_code = '".$product_code."'";
					$res_update_allocation_upper_hierarchy = mysql_query($sql_update_allocation_upper_hierarchy);
				}
				else
				{
					$sql_update_allocation_upper_hierarchy = "INSERT INTO TD_allocation SET 
															  emp_code = '".$emp_upper_hierarchy_val."', 
															  product_filter_code = '".$product_code."', 
															  TD = '".$TD."'";
					$res_update_allocation_upper_hierarchy = mysql_query($sql_update_allocation_upper_hierarchy);
				}
				$sql_insert_TD_log_upper_hierarchy = "INSERT into TD_allocation_log SET
														 allocation_id='".$trans_id."',
														 allocation_date=CURRENT_TIMESTAMP(),
													     emp_code = '".$emp_upper_hierarchy_val."', 
													     product_filter_code = '".$product_code."', 
														 TD = '".$TD."'";
				$res_insert_TD_log_upper_hierarchy = mysql_query($sql_insert_TD_log_upper_hierarchy);
				
			}
		}
echo "Data successfully inserted";
//echo "$emp_code $product_code $quantity";
if($admin_login_value=='SFATS')
{
	$_SESSION['admin_login']='SFATS';
}


mysql_close($link);
?>