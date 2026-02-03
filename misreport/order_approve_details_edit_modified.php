<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

if($_REQUEST['mode']=='approveorder'){
//$select_control_val = $_REQUEST['select_control_val'];
//$select_val = $_REQUEST['select_val'];
$checked_order_details_val=$_POST['order_details'];
$all_order_val=$_POST['all_order_val'];
foreach($all_order_val as $all_order_values)
{
	$all_order_values_aplit=explode('-',$all_order_values);
	if(in_array($all_order_values,$checked_order_details_val))
	{
		$sqlupdate="UPDATE order_header SET status='APPROVED' WHERE customer_code='".$all_order_values_aplit[1]."' AND 
			DATE_FORMAT(SUBSTRING(order_no,-14,8),'%d%m%Y')='".$all_order_values_aplit[0]."'";
		mysqli_query($link,$sqlupdate);
	}
	else
	{
		$sqlupdate="UPDATE order_header SET status='NOT APPROVED' WHERE customer_code='".$all_order_values_aplit[1]."' AND 
			DATE_FORMAT(SUBSTRING(order_no,-14,8),'%d%m%Y')='".$all_order_values_aplit[0]."'";
		mysqli_query($link,$sqlupdate);
	}
}
}
mysqli_close($link);
?>
<script language="javascript" type="text/javascript">alert("Order approval process successful.");
	window.location.href='order_approval_process_modified.php';</script>
