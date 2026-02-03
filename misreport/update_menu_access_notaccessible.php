<?php
// check error
// error_reporting(E_ERROR);
// ini_set('display_errors', 1);

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
ob_start();
	session_start();
    require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
$menu_checked_array_val = $_REQUEST['emparray'];
$menu_checked_array=explode(",",$menu_checked_array_val);
$emp_code = $_REQUEST['emp_code'];
$emp_code = str_replace('_','/',$_REQUEST['emp_code']);

//echo $emp_code;
//print_r($menu_checked_array);
$db = "acedns_".strtoupper($_SESSION['nick_name']);
define("SERVER","localhost");
define("USER","root");
define("PASSWORD","Passw0rd123#$");
define("DB","$db");
$link=mysqli_connect(SERVER,USER,PASSWORD,DB);


// $link=mysqli_connect(SERVER,USER,PASSWORD,DB);
$sql_clear_menuaccess = "DELETE FROM menu_access WHERE emp_code = '".$emp_code."'";
$res_clear_menuaccess = mysqli_query($link,$sql_clear_menuaccess);

foreach($menu_checked_array as $value)
{
	if($value!=''){
	$sql_insert_menuaccess = "INSERT INTO menu_access SET emp_code = '".$emp_code."', not_accessible_menu = '".$value."'";
	$res_insert_menuaccess = mysqli_query($link,$sql_insert_menuaccess);
	}
}
$nickname=$_SESSION['nick_name'];
// modifyempdatadownloadlog($emp_code,strtoupper($nickname));
echo "Successfully Updated";
mysqli_close($link);
?>