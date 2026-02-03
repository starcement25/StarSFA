<?php
ob_start();
	session_start();
	require("adminUtils.php");
	require("datefunction.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	
	$start_date = $_REQUEST['start_date'];
	$end_date = $_REQUEST['end_date'];
	$product_group = $_REQUEST['product_group'];
	
	$sql_select_route = "SELECT MF.route_code as route_code, RM.route_name as route_name FROM route_master RM, market_feedback MF WHERE MF.route_code=RM.route_code AND MF.product_group LIKE '%".$product_group."%' AND  DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."' GROUP BY route_code";
	$res_select_route = mysqli_query($link,$sql_select_route);
	while($row_select_route = mysqli_fetch_assoc($res_select_route))
	{
		$route_code = $row_select_route['route_code'];
		$route_name = $row_select_route['route_name'];
		echo "<option value='".$route_code."'>".$route_name."</option>";
	}
	mysqli_close($link);
?>
