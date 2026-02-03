<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$depot = $_REQUEST['depot'];
$prod_group = $_REQUEST['prod_group'];
$start_date = $_REQUEST['start_date'];

if($start_date!='' && $prod_group != '' && $depot == ''){
	$condition = " AND SUBSTRING(DFC.datetime,1,10) = '".$start_date."' AND PGM.product_group_code IN(".$prod_group.") ";
}
else if($start_date!='' && $prod_group == '' && $depot != ''){
	$condition = " AND SUBSTRING(DFC.datetime,1,10) = '".$start_date."' AND BM.branch_code IN(".$depot.") ";
}
else if($start_date!='' && $prod_group != '' && $depot != ''){
	$condition = " AND SUBSTRING(DFC.datetime,1,10) = '".$start_date."' AND PGM.product_group_code IN(".$prod_group.") AND BM.branch_code IN(".$depot.") ";
}

$branch_name_array = array();
$count = 1;
$sql_freight_cost = "SELECT BM.branch_name, PGM.product_group_name, PM.prod_desc, DFC.depot_cost,DFC.datetime FROM depot_cost DFC, product_master PM, branch_master BM, product_group_master PGM WHERE DFC.branch_code=BM.branch_code AND DFC.dns_prod_code=PM.dns_prod_code AND PM.product_group_code=PGM.product_group_code".$condition." GROUP BY DFC.branch_code,DFC.dns_prod_code ORDER BY BM.branch_name, PGM.product_group_name ASC";
$res_freight_cost = mysql_query($sql_freight_cost);
$total_rows = mysql_num_rows($res_freight_cost);

if($total_rows>0){
	?>
    <table border="1" width="100%" style="border-collapse:collapse;" cellpadding="4">
      <tr class="TDHEAD" align="center">
      	<td>SI</td>
      	<td>Branch Name</td>
        <td>Product Group</td>
        <td>Product Desc</td>
        <td>Depot Cost</td>
        <td>Date</td>
      </tr>
    <?php
	$res_freight_cost = mysql_query($sql_freight_cost);
	while($row_freight_cost = mysql_fetch_array($res_freight_cost)){
		$branch_name = $row_freight_cost['branch_name'];
		$prod_group = $row_freight_cost['product_group_name'];
		$prod_desc = $row_freight_cost['prod_desc'];
		$freight = $row_freight_cost['depot_cost'];
		$date = $row_freight_cost['datetime'];
		
		if(!in_array($branch_name,$branch_name_array)){
			array_push($branch_name_array,$branch_name);
			echo "<tr class=\"TDHEAD_SUB\"><td colspan=\"6\" align=\"center\">".$branch_name."</td></tr>";
		}
		
		echo "<tr>
				<td>".$count."</td>
				<td>".$branch_name."</td>
				<td>".$prod_group."</td>
				<td>".$prod_desc."</td>
				<td align=\"right\">".$freight."</td>
				<td>".$date."</td>
			  </tr>";
		$count++;
	}
}
else{
	echo "<strong><font color=\"red\">No records found</font></strong>";
}
mysql_close($link);
?>