<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

$menu = $_REQUEST['menu'];
if($menu == ''){
	echo "<option>Select</option>";
}
else{
	$sql_get_layer = "SELECT layout_name FROM survey_input WHERE type = 'layer' AND menu_id = '".$menu."'";
	$res_get_layer = mysql_query($sql_get_layer);
	while($row_get_layer = mysql_fetch_array($res_get_layer)){
		echo "<option>".$row_get_layer['layout_name']."</option>";
	}
}
mysql_close($link);
?>