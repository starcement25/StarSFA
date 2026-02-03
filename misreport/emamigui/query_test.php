<?php
session_start();
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db("acedns_EMAMI");
require("../adminUtils.php");


$sql_product_drilldown = "SELECT SUM(ROUND(STL.convert_qty_two,2)), SUM(STL.amount), STL.product_group_code,PGM.vertical_value, PGM.product_group_name FROM `sauda_transaction_log` STL, product_group_master PGM, employee_master EM WHERE STL.product_group_code = PGM.product_group_code AND STL.sauda_date LIKE '%2017-04-25%' AND PGM.vertical_value = 'HBC,Rasoi,BIB' AND FIND_IN_SET(STL.emp_code,REPLACE(EM.lower_leaves,\"'\",\"\")) AND EM.emp_code = 'E0027' GROUP BY STL.product_group_code";
$res_product_drilldown = mysql_query($sql_product_drilldown);
while($row_product_drilldown = mysql_fetch_array($res_product_drilldown)){
	
	echo $product_group_code_drillone = $row_product_drilldown['product_group_code'];
	$quantity = $row_product_drilldown['SUM(ROUND(STL.convert_qty_two,2))'];

	/*----> Product Drill <----*/
	$sql_product_drilldown_sub = "SELECT PM.prod_desc, SUM(ROUND(STL.convert_qty_two,2)) FROM sauda_transaction_log STL, product_master PM WHERE STL.prod_code = PM.prod_code AND STL.product_group_code = '".$product_group_code_drillone."' AND ".$date_condition.$emp_hierarchy_condition." GROUP BY PM.prod_code";
	$res_product_drilldown_sub = mysql_query($sql_product_drilldown_sub);
	while($row_product_drilldown_sub = mysql_fetch_array($res_product_drilldown_sub)){
		$prod_desc = $row_product_drilldown_sub['prod_desc'];
		$prod_qty = $row_product_drilldown_sub['SUM(ROUND(STL.convert_qty_two,2))'];
		
		$data_set_product_drilldown_data .= "['$prod_desc', $prod_qty],";
		
	}
	
	$data_set_product_drilldown_final .= "{
										id: '$product_group_code_drillone-$emp_code',
										data:[".rtrim($data_set_product_drilldown_data,",")."]
									},";
	
	$data_set_drilldown .= "{
								name: '".$prod_group_name."',
								y: $prod_qty,
								drilldown: '$product_group_code_drillone-$emp_code'
							},";
}
?>