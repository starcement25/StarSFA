<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
$start_date = $_REQUEST['start_date'];	
$end_date = $_REQUEST['end_date'];
/*$emp_code = $_REQUEST['emp_code'];
if($emp_code == 'all'){
	$emp_condition = "";
}
else{
	$emp_condition = " AND SUBSTRING(SU.transaction_id,2,5)='".$emp_code."' ";
}*/
?>
<select name="select_customer" id="select_customer">
<option value="">SELECT</option>
<?php
$sql_select_customer = "SELECT DISTINCT CM.customer_code,CM.customer_name FROM customer_master CM,survey_output SO 
					WHERE SO.value=CM.customer_name AND SO.row_id='RA001'  AND SUBSTRING(SO.survey_id,-14,8) 
					BETWEEN ".date('Ymd',strtotime($start_date))." AND ".date('Ymd',strtotime($end_date))." ORDER BY CM.customer_name ASC";
$res_select_customer = mysqli_query($link,$sql_select_customer);
$res_select_customer = mysqli_query($link,$sql_select_customer);
while($row_select_customer = mysqli_fetch_assoc($res_select_customer))
{
	echo "<option value='".$row_select_customer['customer_code']."'>".$row_select_customer['customer_name']."</option>";
}
echo "</select>";
mysqli_close($link);
?>