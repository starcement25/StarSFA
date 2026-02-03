<?php
ob_start();
session_start();
require("adminUtils.php");

/*--------> Employee Hierarchy Condition <--------*/
$zone = $_REQUEST['zone'];
$region = $_REQUEST['region'];
	$sql_division = "SELECT  DISTINCT RM.route_name FROM route_master RM,customer_master CM WHERE 
					CM.route_code=RM.route_code AND CM.zone='".$zone."' AND CM.district='".$region."' ORDER BY RM.route_name ASC"; 
	$res_division = mysqli_query($link,$sql_division);
	$count_division=mysqli_num_rows($res_division);
		$onclick = "show_others('division',this.value);division_ccc();";
		echo "<select name=\"division\" id=\"division\" onchange=\"".$onclick."\">";
		echo "<option value=\"\">Select</option>";
		while($row_division = mysqli_fetch_assoc($res_division)){
			$division = $row_division['route_name'];
			echo "<option value='".$division."'>".$division."</option>";
		}
		echo "<option value=\"Other\">Other</option>";
		echo "</select>";
mysqli_close($link);
?>