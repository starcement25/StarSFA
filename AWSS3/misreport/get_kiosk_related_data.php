<?php
ob_start();
session_start();
require("adminUtils.php");

/*--------> Employee Hierarchy Condition <--------*/
$zone = $_REQUEST['zone'];
$region = $_REQUEST['region'];
$division = $_REQUEST['division'];
$ccc = $_REQUEST['ccc'];
	$sql_route_code= "SELECT  RM.route_code FROM route_master RM,customer_master CM WHERE 
					CM.route_code=RM.route_code AND CM.zone='".$zone."' AND CM.district='".$region."' AND RM.route_name='".$division."'"; 
	$rs_route_code=mysql_query($sql_route_code);	
	$row_route_code=mysql_fetch_array($rs_route_code);	
	$route_code=$row_route_code['route_code'];		
	$sql_ccc="SELECT  DISTINCT dns_customer_code FROM customer_master WHERE zone='".$zone."' AND district='".$region."' 
				AND route_code='".$route_code."' AND customer_name='".$ccc."'";				
	$res_ccc = mysql_query($sql_ccc);
	$count_ccc=mysql_num_rows($res_ccc);
		$onclick = "show_others('kiosk_id',this.value);";
		echo "<select name=\"kiosk_id\" id=\"kiosk_id\" onchange=\"".$onclick."\">";
		echo "<option value=\"\">Select</option>";
		while($row_ccc = mysql_fetch_array($res_ccc)){
			$ccc = $row_ccc['dns_customer_code'];
			echo "<option value='".$ccc."'>".$ccc."</option>";
		}
		echo "<option value=\"Other\">Other</option>";
		echo "</select>";
mysql_close($link);
?>