<?php
ob_start();
session_start();
require_once("adminUtils.php");

if($_SESSION['admin_login']=="")  		header("location:index.php");
	
$menu_checked_array_val = $_REQUEST['emparray'];
$menu_checked_array=explode(",",$menu_checked_array_val);
$emp_code = $_REQUEST['emp_code'];
$emp_code = str_replace('_','/',$_REQUEST['emp_code']);

require_once("include/config.php");
require_once("include/config-setup.php");
require_once("include/dbcon.php");
require_once("menu_access_bd_schema.php");

ensure_menu_access_bd_schema($link);

/* The checked boxes are the types the employee is NOT permitted to see. */
$sql_clear_menuaccess = "DELETE FROM menu_access_bd WHERE emp_code = '".$emp_code."'";
$res_clear_menuaccess = mysqli_query($link,$sql_clear_menuaccess);

foreach($menu_checked_array as $value)
{
	if($value!=''){
		$sql_insert_menuaccess = "INSERT INTO menu_access_bd SET emp_code = '".$emp_code."', not_accessible_menu = '".mysqli_real_escape_string($link, $value)."'";
		$res_insert_menuaccess = mysqli_query($link,$sql_insert_menuaccess);
	}
}
echo "Successfully Updated";
mysqli_close($link);
?>
