<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

	echo "<option value=\"\" selected>Select</option>";
	$sql_select_layer = "SELECT row_id, layout_name FROM survey_input WHERE type = 'layer'";
	$res_select_layer = mysql_query($sql_select_layer);
	while($row_select_layer = mysql_fetch_array($res_select_layer)){
		echo "<option>".$row_select_layer['layout_name']."</option>";
	}
?>