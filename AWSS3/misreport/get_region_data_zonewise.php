<?php
ob_start();
session_start();
require("adminUtils.php");

/*--------> Employee Hierarchy Condition <--------*/
$zone = $_REQUEST['zone'];
	$sql_region = "SELECT  DISTINCT district FROM customer_master WHERE zone='".$zone."' ORDER BY district ASC"; 
	$res_region = mysql_query($sql_region);
	$count_region=mysql_num_rows($res_region);
		$onclick = "show_others('region',this.value);region_division();";
		$region_string='';
		echo "<select name=\"region\" id=\"region\" onchange=\"".$onclick."\">";
		echo "<option value=\"\">Select</option>";
		while($row_region = mysql_fetch_array($res_region)){
			$region = $row_region['district'];
			echo "<option value='".$region."'>".$region."</option>";
		}
		echo "<option value=\"Other\">Other</option>";
		echo "</select>";
mysql_close($link);
?>