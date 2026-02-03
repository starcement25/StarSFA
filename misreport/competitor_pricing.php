<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$month = $_REQUEST['month'];
$month = date('m',strtotime(''.$month.''));
$year = $_REQUEST['year'];

$sql_check_data = "SELECT market_feedback_id FROM market_feedback WHERE SUBSTRING(market_feedback_id,-14,4)='".$year."' AND SUBSTRING(market_feedback_id,-10,2)='".$month."'";
$res_check_data = mysqli_query($link,$sql_check_data);
$total_records = mysqli_num_rows($res_check_data);
if($total_records>0){
?>
<table border="1" style="border-collapse:collapse;" cellpadding="4" width="100%">
  <tr style="font-weight:bold; text-align:center;" class="TDHEAD">
  	<td>&nbsp;</td>
    <td>&nbsp;</td>
<?php
$sql_month_selection = "SELECT DISTINCT SUBSTRING(market_feedback_id,-14,8) as dates FROM market_feedback WHERE SUBSTRING(market_feedback_id,-14,4)='".$year."' AND SUBSTRING(market_feedback_id,-10,2)='".$month."'";
$res_month_selection = mysqli_query($link,$sql_month_selection);
while($row_month_selection = mysqli_fetch_assoc($res_month_selection)){
	$date = $row_month_selection['dates'];
	
	$sql_route = "SELECT DISTINCT route_code as route_code FROM market_feedback WHERE SUBSTRING(market_feedback_id,-14,8)='".$date."'";
	$res_route = mysqli_query($link,$sql_route);
	$total_route = mysqli_num_rows($res_route);
	echo "<td colspan='$total_route'>".date('d/m/Y',strtotime(''.$date.''))."</td>";
}
?>
	</tr>
    <tr style="font-weight:bold; text-align:center;" class="TDHEAD_SUB"><td>Oil Type</td><td>Competitor Name</td>
<?php
$sql_month_selection = "SELECT DISTINCT SUBSTRING(market_feedback_id,-14,8) as dates FROM market_feedback WHERE SUBSTRING(market_feedback_id,-14,4)='".$year."' AND SUBSTRING(market_feedback_id,-10,2)='".$month."'";
$res_month_selection = mysqli_query($link,$sql_month_selection);
while($row_month_selection = mysqli_fetch_assoc($res_month_selection)){
	$date = $row_month_selection['dates'];
			
	$sql_route = "SELECT DISTINCT route_code as route_code FROM market_feedback WHERE SUBSTRING(market_feedback_id,-14,8)='".$date."'";
	$res_route = mysqli_query($link,$sql_route);
	
	while($row_route = mysqli_fetch_assoc($res_route)){
		$route_code = $row_route['route_code'];
		
		$sql_route_name = "SELECT route_name FROM route_master WHERE route_code='".$route_code."'";
		$res_route_name = mysqli_query($link,$sql_route_name);
		$row_route_name = mysqli_fetch_assoc($res_route_name);
		$route_name = $row_route_name['route_name'];
		echo "<td>".$route_name."</td>";
	}
}
echo "</tr>";

/*$sql_product_group_master = "SELECT product_group_name FROM product_group_master ORDER BY product_group_name ASC";
$res_product_group_name = mysqli_query($link,$sql_product_group_master);
while($row_product_group_name = mysqli_fetch_assoc($res_product_group_name)){*/
	//$product_group_name = $row_product_group_name['product_group_name'];
	$product_group_array = array();
	$sql_get_distinct_product_group = "SELECT DISTINCT product_group as product_group FROM market_feedback WHERE SUBSTRING(market_feedback_id,-14,4)='".$year."' AND SUBSTRING(market_feedback_id,-10,2)='".$month."' ORDER BY product_group ASC";
	$res_get_distinct_product_group = mysqli_query($link,$sql_get_distinct_product_group);
	while($row_get_distinct_product_group = mysqli_fetch_assoc($res_get_distinct_product_group)){
		$productgroupname = $row_get_distinct_product_group['product_group'];
		array_push($product_group_array,$productgroupname);
	}
	
	foreach($product_group_array as $value){
	$product_group_name = $value;
	
	$sql_product = "SELECT DISTINCT competitor_name as competitor_name FROM market_feedback WHERE product_group LIKE '%".$product_group_name."%'";
	$res_product = mysqli_query($link,$sql_product);
	while($row_product = mysqli_fetch_assoc($res_product)){
		$competitor_name = $row_product['competitor_name'];
		
		echo "<tr><td>$product_group_name</td><td>$competitor_name</td>";
	
		$sql_month_selection = "SELECT DISTINCT SUBSTRING(market_feedback_id,-14,8) as dates FROM market_feedback WHERE SUBSTRING(market_feedback_id,-14,4)='".$year."' AND SUBSTRING(market_feedback_id,-10,2)='".$month."'";
		$res_month_selection = mysqli_query($link,$sql_month_selection);
		while($row_month_selection = mysqli_fetch_assoc($res_month_selection)){
			$date = $row_month_selection['dates'];
			
			$sql_route = "SELECT DISTINCT route_code as route_code FROM market_feedback WHERE SUBSTRING(market_feedback_id,-14,8)='".$date."'";
			$res_route = mysqli_query($link,$sql_route);
			while($row_route = mysqli_fetch_assoc($res_route)){
				$route_code = $row_route['route_code'];
				
				$sql_ptr = "SELECT MAX(PTR) as ptr FROM market_feedback WHERE SUBSTRING(market_feedback_id,-14,8)='".$date."' AND competitor_name='".$competitor_name."' AND route_code='".$route_code."' AND product_group LIKE '%".$product_group_name."%'";
				$res_ptr = mysqli_query($link,$sql_ptr);
				$row_ptr = mysqli_fetch_assoc($res_ptr);
				$ptr = $row_ptr['ptr'];
				
				if($ptr != ''){
					echo "<td align=\"right\">".number_format($ptr,2)."</td>";
				}
				else{
					echo "<td>--</td>";
				}
			}
		}
		echo "</tr>";
	}
	}
?>
</table>
<?php
}
else{
	echo "<font color=\"red\"><strong>No records</strong></font>";
}
mysqli_close($link);
?>