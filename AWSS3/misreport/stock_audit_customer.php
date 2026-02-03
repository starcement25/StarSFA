<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
$start_date = $_REQUEST['start_date'];	
$end_date = $_REQUEST['end_date'];
$emp_code = $_REQUEST['emp_code'];

if($emp_code == 'all'){
	$emp_condition = "";
}
else{
	$emp_condition = " AND SUBSTRING(SU.transaction_id,2,5)='".$emp_code."' ";
}
?>
<option value="">SELECT</option>

<?php
$sql_select_customer = "SELECT CM.customer_code, CM.customer_name FROM customer_master CM, stock_audit SU WHERE SU.customer_code=CM.customer_code ".$emp_condition." AND SUBSTRING(SU.transaction_id,-14,8) BETWEEN ".date('Ymd',strtotime($start_date))." AND ".date('Ymd',strtotime($end_date))." GROUP BY SU.customer_code ORDER BY CM.customer_name ASC";
$res_select_customer = mysql_query($sql_select_customer);
$total_rows = mysql_num_rows($res_select_customer);
if($total_rows>0)
{
	echo "<option value=\"all\">ALL</option>";
}
$res_select_customer = mysql_query($sql_select_customer);
while($row_select_customer = mysql_fetch_array($res_select_customer))
{
	echo "<option value='".$row_select_customer['customer_code']."'>".$row_select_customer['customer_name']."</option>";
}
mysql_close($link);
?>