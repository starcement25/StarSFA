<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$emp_code = $_REQUEST['emp_code'];
$customer_name = $_REQUEST['customer_name'];
$product_group = $_REQUEST['product_group'];
$product = $_REQUEST['product'];
$depot = $_REQUEST['depot'];
$plant = $_REQUEST['plant'];

if($emp_code != ''){
	$emp_hierarchy=return_employee_hierarchy($emp_code);
	
	if(modified_customer_emp_route == 'yes'){
		$sql_getroute_employeewise = "SELECT DISTINCT ERR.route_code as route_code FROM emp_route_relation ERR WHERE ERR.emp_code IN(".$emp_hierarchy.")";
		$res_getroute_employeewise = mysqli_query($link,$sql_getroute_employeewise);
		while($row_getroute_employeewise = mysqli_fetch_assoc($res_getroute_employeewise)){
			$get_route .= "'".$row_getroute_employeewise['route_code']."',";
		}
		$get_route = rtrim($get_route,',');
		
		//$emp_route_relation_table = ", emp_route_relation ERR";
		$emp_hierarchy_condition = " AND CM.route_code IN(".$get_route.") ";
	}
	else{
		$emp_hierarchy_condition=" AND CM.emp_code IN(".$emp_hierarchy.") ";
	}
}
else if($customer_name != ''){
	$customer_condition = " AND CM.customer_name='".$customer_name."' ";
}
else if($product_group != ''){
	$product_group_condition = " AND PGM.product_group_code='".$product_group."' ";
}
else if($product != ''){
	$product_condition = " AND PM.prod_desc='".$product."' ";
}
else if($depot != ''){
	$depot_contion = " AND PCA.branch_code='".$depot."' ";
}
else if($plant != ''){
	$plant_condition = " AND BRM.plant_name LIKE '%".$plant."%' ";
}

$depot_array = array();
$sql_pending_contract_ageing = "SELECT PCA.*, CM.customer_name, CM.customer_code, PGM.product_group_name, PM.prod_desc, PCA.broker_id, BRM.branch_name FROM pending_contract_ageing PCA, customer_master CM, product_group_master PGM, product_master PM, branch_master BRM WHERE PCA.customer_code=CM.customer_code AND PCA.product_group_code=PGM.product_group_code AND PCA.prod_code=PM.prod_code AND PCA.branch_code=BRM.branch_code ".$emp_hierarchy_condition.$customer_condition.$product_group_condition.$product_condition.$depot_contion."ORDER BY BRM.branch_name,PGM.product_group_name,PM.prod_desc,CM.customer_name ASC";
$res_pending_contract_ageing = mysqli_query($link,$sql_pending_contract_ageing);
$total_rows = mysqli_num_rows($res_pending_contract_ageing);
$count = 1;
if($total_rows>0){
	?>
    <table width="100%" border="1" style="border-collapse:collapse;" class="BORDER" cellpadding="4">
      <tr class="TDHEAD_SUB" align="center">
      	<td rowspan="2">SI</td>
        <td rowspan="2">Product Group</td>
        <td rowspan="2">SKU</td>
        <td rowspan="2">Customer Name</td>
        <td rowspan="2">Broker</td>
        <td rowspan="2">Contract qty</td>
        <td rowspan="2">Despatch qty</td>
        <td colspan="5">Pending Qty</td>
        <td rowspan="2">No of Days</td>
      </tr>
      <tr class="TDHEAD_SUB" align="center">
      	<td>0 to 15 days</td>
        <td>16 to 30 days</td>
        <td>31 to 45 days</td>
        <td>46 to 60 days</td>
        <td>&gt;60 days</td>
      </tr>
    <?php
	$res_pending_contract_ageing = mysqli_query($link,$sql_pending_contract_ageing);
	while($row_pending_contract_ageing = mysqli_fetch_assoc($res_pending_contract_ageing)){
		$branch_name = $row_pending_contract_ageing['branch_name'];
		$product_group_name = $row_pending_contract_ageing['product_group_name'];
		$prod_desc = $row_pending_contract_ageing['prod_desc'];
		$customer_name = $row_pending_contract_ageing['customer_name'];
		$broker_id = $row_pending_contract_ageing['broker_id'];
		$contract_qty = $row_pending_contract_ageing['contract_qty'];
		$despatch_qty = $row_pending_contract_ageing['despatch_qty'];
		$qty_0_15 = $row_pending_contract_ageing['qty_0_15'];
		$qty_16_30 = $row_pending_contract_ageing['qty_16_30'];
		$qty_31_45 = $row_pending_contract_ageing['qty_31_45'];
		$qty_46_60 = $row_pending_contract_ageing['qty_46_60'];
		$qty_greater_60 = $row_pending_contract_ageing['qty_greater_60'];
		$greater_60_days = $row_pending_contract_ageing['greater_60_days'];
		
		$sql_brokername = "SELECT broker_name FROM broker_master WHERE broker_id='".$broker_id."'";
		$res_brokername = mysqli_query($link,$sql_brokername);
		$row_brokername = mysqli_fetch_assoc($res_brokername);
		$broker_name = $row_brokername['broker_name'];
		
		if(!in_array($branch_name,$depot_array)){
			array_push($depot_array,$branch_name);
			echo "<tr class=\"TDHEAD\"><td colspan=\"13\" align=\"center\">".$branch_name."</td></tr>";
		}
		
		echo "<tr>
				<td>".$count."</td>
				<td>".$product_group_name."</td>
				<td>".$prod_desc."</td>
				<td>".$customer_name."</td>
				<td>".$broker_name."</td>
				<td align=\"right\">".$contract_qty."</td>
				<td align=\"right\">".$despatch_qty."</td>
				<td align=\"right\">".$qty_0_15."</td>
				<td align=\"right\">".$qty_16_30."</td>
				<td align=\"right\">".$qty_31_45."</td>
				<td align=\"right\">".$qty_46_60."</td>
				<td align=\"right\">".$qty_greater_60."</td>
				<td align=\"right\">".$greater_60_days."</td>
			  </tr>";
		$count++;
	}
}
else{
	echo "<strong><font color=\"red\">No records found</font></strong>";
}

