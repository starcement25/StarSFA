<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>	
<option value=" ">Select Employee</option>
<?php
$date_selected = $_REQUEST['date_selected'];

If(strtoupper($_SESSION['admin_login'])=='PRICESFATS')
{
	$vertical_condition=" AND EM.vertical_value='".$_SESSION['vertical_value']."'";
}
else
{
	$vertical_condition="";
}


$sql_getempl_name = "SELECT EM.emp_name, SH.sauda_no, date_format(substring(SH.sauda_no,-14,8),'%d-%m-%Y') as date_selected FROM employee_master EM, sauda_header SH WHERE date_format(substring(SH.sauda_no,-14,8),'%d-%m-%Y') = '$date_selected' 
AND substring(SH.sauda_no,3,5) = EM.emp_code ".$vertical_condition."  GROUP BY substring(SH.sauda_no,3,5) ORDER BY EM.emp_name ASC";
$res_getempl_name = mysqli_query($link,$sql_getempl_name);
while($row_getempl_name = mysqli_fetch_assoc($res_getempl_name))
{
	$value_selected = $row_getempl_name['sauda_no']."^".$row_getempl_name['date_selected'];
	echo "<option value=\"$value_selected\">".$row_getempl_name['emp_name']."</option>";
}

mysqli_close($link);
?>