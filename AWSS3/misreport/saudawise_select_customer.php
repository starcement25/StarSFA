<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>

<option value=" ">Select Customer</option>

<?php
$sauda_number = explode("^",$_REQUEST['employee_selected']);
$emp_code = substr($sauda_number[0],2,5);

$sql_getcustomer_name = "SELECT CM.customer_name, CM.customer_code, date_format(substring(SH.sauda_no,-14,8),'%d-%m-%Y') as date_selected FROM customer_master CM, sauda_header SH WHERE date_format(substring(SH.sauda_no,-14,8),'%d-%m-%Y') = '$sauda_number[1]' AND substring(SH.sauda_no,3,5) = '$emp_code' AND SH.customer_code = CM.customer_code GROUP BY SH.customer_code ORDER BY CM.customer_name ASC";
$res_getcustomer_name = mysql_query($sql_getcustomer_name);
while($row_getcustomer_name = mysql_fetch_array($res_getcustomer_name))
{
	$select_value = $sauda_number."^".$row_getcustomer_name['customer_code']."^".$row_getcustomer_name['date_selected'];
	echo "<option value=\"$select_value\">".$row_getcustomer_name['customer_name']."</option>";
}
mysql_close($link);
?>