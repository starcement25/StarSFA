<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
$menu_checked_array = $_REQUEST['emparray'];
$emp_code = $_REQUEST['emp_code'];
$emp_code = str_replace('_','/',$_REQUEST['emp_code']);

//echo $emp_code;
//print_r($menu_checked_array);

$sql_clear_menuaccess = "DELETE FROM menu_access WHERE emp_code = '".$emp_code."'";
$res_clear_menuaccess = mysql_query($sql_clear_menuaccess);

foreach($menu_checked_array as $value)
{
	$sql_insert_menuaccess = "INSERT INTO menu_access SET emp_code = '".$emp_code."', not_accessible_menu = '".$value."'";
	$res_insert_menuaccess = mysql_query($sql_insert_menuaccess);
}
$nickname=$_SESSION['nick_name'];
modifyempdatadownloadlog($emp_code,strtoupper($nickname));
echo "Successfully Updated";
mysql_close($link);
?>