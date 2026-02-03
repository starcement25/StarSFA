<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

$survey_type = $_REQUEST['survey_type'];
if($survey_type == ''){
	echo "<option value=\"\" selected>Select</option>";
	echo $sql_select_menu = "SELECT menu_id, layout_name FROM survey_input WHERE type = 'menu'"; 
	$res_select_menu = mysql_query($sql_select_menu); 
	while($row_select_menu = mysql_fetch_array($res_select_menu)){ 
	echo "<option value = \"".$row_select_menu['menu_id']."\">".$row_select_menu['layout_name']."</option>";
	}
}
else{
	echo "<option value=\"\" selected>Select</option>";
	echo $sql_select_menu = "SELECT menu_id, layout_name FROM survey_input WHERE type = 'menu' AND survey_type = '".$survey_type."'"; 
	$res_select_menu = mysql_query($sql_select_menu); 
	while($row_select_menu = mysql_fetch_array($res_select_menu)){ 
	echo "<option value = \"".$row_select_menu['menu_id']."\">".$row_select_menu['layout_name']."</option>";} 
}
mysql_close($link);
?>