<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","acedns_STAR");

mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB);

$sql_market_feedback = "SELECT SUBSTRING(market_feedback_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(market_feedback_id,-14,8),'%Y-%m-%d') AS mf_date, customer_code, competitor_name, PTD, PTR, PTC, PV FROM market_feedback";
$res_market_feedback = mysql_query($sql_market_feedback);
while($row_market_feedback = mysql_fetch_array($res_market_feedback)){
	$emp_code = $row_market_feedback['emp_code'];
	$mf_date = $row_market_feedback['mf_date'];
	$customer_code = $row_market_feedback['customer_code'];
	$competitor_name = $row_market_feedback['competitor_name'];
	$PTD = $row_market_feedback['PTD'];
	$PTR = $row_market_feedback['PTR'];
	$PTC = $row_market_feedback['PTC'];
	$PV = $row_market_feedback['PV'];
	
	if($competitor_name == 'AMBUJA PPC'){
		$column_name_PTD = 'ambuja_PTD';
		$column_name_PTR = 'ambuja_PTR';
		$column_name_PTC = 'ambuja_PTC';
		$column_name_PV = 'ambuja_PV';
	}
	else if($competitor_name == 'ULTRATECH PPC'){
		$column_name_PTD = 'ultratech_PTD';
		$column_name_PTR = 'ultratech_PTR';
		$column_name_PTC = 'ultratech_PTC';
		$column_name_PV = 'ultratech_PV';
	}
	else if($competitor_name == 'LAFARGE PPC'){
		$column_name_PTD = 'lafarge_PTD';
		$column_name_PTR = 'lafarge_PTR';
		$column_name_PTC = 'lafarge_PTC';
		$column_name_PV = 'lafarge_PV';
	}
	else if($competitor_name == 'DALMIA PPC'){
		$column_name_PTD = 'dalmia_PTD';
		$column_name_PTR = 'dalmia_PTR';
		$column_name_PTC = 'dalmia_PTC';
		$column_name_PV = 'dalmia_PV';
	}
	else if($competitor_name == 'TOPCEM PPC'){
		$column_name_PTD = 'topcem_PTD';
		$column_name_PTR = 'topcem_PTR';
		$column_name_PTC = 'topcem_PTC';
		$column_name_PV = 'topcem_PV';
	}
	else if($competitor_name == 'ACC PPC'){
		$column_name_PTD = 'acc_PTD';
		$column_name_PTR = 'acc_PTR';
		$column_name_PTC = 'acc_PTC';
		$column_name_PV = 'acc_PV';
	}
	else if($competitor_name == 'BIRLA GOLD PPC'){
		$column_name_PTD = 'birla_gold_PTD';
		$column_name_PTR = 'birla_gold_PTR';
		$column_name_PTC = 'birla_gold_PTC';
		$column_name_PV = 'birla_gold_PV';
	}
	
	$sql_competitor_pricing_check = "SELECT emp_code FROM competitor_pricing WHERE emp_code = '".$emp_code."' AND customer_code = '".$customer_code."' AND date_time = '".$mf_date."'";
	$res_competitor_pricing_check = mysql_query($sql_competitor_pricing_check);
	$competitor_pricing_check = mysql_num_rows($res_competitor_pricing_check);
	
	if($competitor_pricing_check>0){
		$sql_update_competitor_pricing = "UPDATE competitor_pricing SET $column_name_PTD = '".$PTD."', $column_name_PTR = '".$PTR."', $column_name_PTC = '".$PTC."', $column_name_PV = '".$PV."' WHERE emp_code = '".$emp_code."' AND customer_code = '".$customer_code."' AND date_time = '".$mf_date."'";
	}
	else{
		$sql_update_competitor_pricing = "INSERT INTO competitor_pricing SET emp_code = '".$emp_code."', customer_code = '".$customer_code."', date_time = '".$mf_date."', $column_name_PTD = '".$PTD."', $column_name_PTR = '".$PTR."', $column_name_PTC = '".$PTC."', $column_name_PV = '".$PV."'";
	}
	$res_update_competitor_pricing = mysql_query($sql_update_competitor_pricing);
}
echo "Successfully Updated";
mysql_close($link);
?>