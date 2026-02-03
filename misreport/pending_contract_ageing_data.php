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
	//$emp_hierarchy=return_employee_hierarchy($emp_code);
	//$emp_hierarchy_condition=" AND CM.emp_code IN(".$emp_hierarchy.") ";
	$emp_hierarchy_condition=" AND CM.emp_code IN(".$emp_code.") ";
}
else if($customer_name != ''){
	//$customer_condition = " AND CM.customer_name='".$customer_name."' ";
	$customer_condition = " AND CM.customer_code IN(".$customer_name.") ";
	}
else if($product_group != ''){
	//$product_group_condition = " AND PGM.product_group_code='".$product_group."' ";
	$product_group_condition = " AND PGM.product_group_code IN(".$product_group.") ";
}
else if($product != ''){
	//$product_condition = " AND PM.prod_desc='".$product."' ";
	$product_condition = " AND PM.dns_prod_code IN(".$product.")";
}
else if($depot != ''){
	//$depot_contion = " AND PCA.branch_code='".$depot."' ";
	$depot_contion = " AND PCA.branch_code IN(".$depot.") ";
}
else if($plant != ''){
	//$plant_condition = " AND BRM.plant_name LIKE '%".$plant."%' ";
	$plant_condition = " AND BRM.plant_name IN(".$plant.") ";
}

$depot_array = array();
$sql_pending_contract_ageing = "SELECT PCA.*, CM.customer_name, PGM.product_group_name, PM.prod_desc, PCA.broker_id, BRM.branch_name,BRM.dns_branch_code,BRM.plant_name FROM pending_contract_ageing PCA, customer_master CM, product_group_master PGM, product_master PM, branch_master BRM WHERE PCA.customer_code=CM.customer_code AND PCA.product_group_code=PGM.product_group_code AND PCA.prod_code=PM.prod_code AND PCA.branch_code=BRM.branch_code AND 
PM.vertical_value='".$_SESSION['vertical_value']."' ".$emp_hierarchy_condition.$customer_condition.$product_group_condition.$product_condition.$depot_contion.$plant_condition." ORDER BY BRM.plant_name ASC,BRM.branch_name ASC,PGM.product_group_name ASC,CM.customer_name ASC";
$res_pending_contract_ageing = mysqli_query($link,$sql_pending_contract_ageing);
$total_rows = mysqli_num_rows($res_pending_contract_ageing);
$count = 1;
if($total_rows>0){
	?>
    <table width="100%" border="1" style="border-collapse:collapse;" class="BORDER" cellpadding="4">
      <tr class="TDHEAD_SUB" align="center">
      	<td rowspan="2">SI</td>
        <td rowspan="2">Plant name</td>
        <td rowspan="2">Depot code</td>
        <td rowspan="2">Depot name</td>
        <td rowspan="2">Product Group</td>
        <td rowspan="2">SKU</td>
        <td rowspan="2">Customer Name</td>
        <td rowspan="2">Broker</td>
        <td rowspan="2">Contract qty <br />(MT)</td>
        <td rowspan="2">Despatch qty <br />(MT)</td>
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
		$dns_branch_code = $row_pending_contract_ageing['dns_branch_code'];
		$plant_name = $row_pending_contract_ageing['plant_name'];
		$product_group_name = $row_pending_contract_ageing['product_group_name'];
		$prod_desc = $row_pending_contract_ageing['prod_desc'];
		$customer_name = $row_pending_contract_ageing['customer_name'];
		$broker_id = $row_pending_contract_ageing['broker_id'];
		$contract_qty = $row_pending_contract_ageing['contract_qty'];
		if($contract_qty==0)  $contract_qty='--';
		$despatch_qty = $row_pending_contract_ageing['despatch_qty'];
		if($despatch_qty==0)  $despatch_qty='--';
		$qty_0_15 = $row_pending_contract_ageing['qty_0_15'];
		if($qty_0_15==0)  $qty_0_15='--';
		$qty_16_30 = $row_pending_contract_ageing['qty_16_30'];
		if($qty_16_30==0)  $qty_16_30='--';
		$qty_31_45 = $row_pending_contract_ageing['qty_31_45'];
		if($qty_31_45==0)  $qty_31_45='--';
		$qty_46_60 = $row_pending_contract_ageing['qty_46_60'];
		if($qty_46_60==0)  $qty_46_60='--';
		$qty_greater_60 = $row_pending_contract_ageing['qty_greater_60'];
		if($qty_greater_60==0)  $qty_greater_60='--';
		$greater_60_days = $row_pending_contract_ageing['greater_60_days'];
		
		$sql_brokername = "SELECT broker_name FROM broker_master WHERE broker_id='".$broker_id."'";
		$res_brokername = mysqli_query($link,$sql_brokername);
		$row_brokername = mysqli_fetch_assoc($res_brokername);
		$broker_name = $row_brokername['broker_name'];
		
		/*if(!in_array($branch_name,$depot_array)){
			array_push($depot_array,$branch_name);
			echo "<tr class=\"TDHEAD\"><td colspan=\"13\" align=\"center\">".$branch_name."</td></tr>";
		}*/
		echo "<tr>
				<td>".$count."</td>
				<td>".$plant_name."</td>
				<td>".$dns_branch_code."</td>
				<td>".$branch_name."</td>
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
mysqli_close($link);
?>
