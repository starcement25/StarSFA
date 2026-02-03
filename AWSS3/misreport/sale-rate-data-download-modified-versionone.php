<?php
error_reporting(0);
ob_start();
session_start();
require("adminUtils.php");
$from_date = $_REQUEST['from_date'];
$from_date_check=date('Y-m-d H:i:s',strtotime($from_date));
//$from_date_final=$from_date_check.' 23:59:59';
$from_date_final=date('Y-m-d H:i:s',strtotime($from_date));
$plant_name = $_REQUEST['plant_name'];
$sql_product_details = "SELECT * FROM (SELECT PM.dns_prod_code,PM.prod_desc,PM.packing_realization AS packing_realization_percent,MR.basic_rate,
						MR.packing_cost,MR.process_cost,MR.labour_cost,MR.extra_cost,MR.margin_cost,MR.sale_rate,MR.packing_realization,PM.prod_code,
						MR.parent_child,MR.basic_rate_ton,MR.process_cost_ton FROM product_master PM, sauda_mrp MR
						WHERE PM.prod_code = MR.product_code AND SUBSTRING(MR.create_date,1,19)='".$from_date_check."' ORDER BY create_date DESC) AS SAT 
						GROUP BY 1 ORDER BY 1";
$res_product_details = mysql_query($sql_product_details);
$total_rows=mysql_num_rows($res_product_details);

if($total_rows >0){
    echo '<table class="border" width="95%" border="1" style="border-collapse:collapse;" cellpadding="5px" align="center">
	<thead>
      <tr>
        <td colspan="15" class="TDHEAD" align="left"><b>Detail Costing Report - '.$from_date.'</b></td>
      </tr>
      <tr class="TDHEAD_SUB">
      	<td width="5%">SI</td>
        <td width="6%">Material Code</td>
        <td width="10%">Material Description</td>
        <td width="8%">Material Cost(CASE)</td>
		<td width="8%">Material Cost(TON)</td>
		<td width="7%">Process Cost(CASE)</td>
		<td width="8%">Process Cost(TON)</td>
        <td width="8%">Packing Cost</td>
		<td width="8%">Labour Cost</td>
		<td width="8%">Extra Cost</td>
        <td width="8%">Packing Realization</td>
		<td width="8%">Margin Cost</td>
        <td width="8%">Bargain Rate</td>
      </tr></thead>';
	$count = 1;
	while($row_product_details = mysql_fetch_array($res_product_details))
	{
		$dns_prod_code=$row_product_details['dns_prod_code'];
		$prod_desc=$row_product_details['prod_desc'];
		$basic_rate=$row_product_details['basic_rate'];
		$packing_cost=$row_product_details['packing_cost'];
		$process_cost=$row_product_details['process_cost'];
		$labour_cost=$row_product_details['labour_cost'];
		$extra_cost=$row_product_details['extra_cost'];
		$margin_cost=$row_product_details['margin_cost'];
		$sale_rate=$row_product_details['sale_rate'];
		$packing_realization=$row_product_details['packing_realization'];
		$packing_realization_percent=$row_product_details['packing_realization_percent'];
		$basic_rate_ton=$row_product_details['basic_rate_ton'];
		$process_cost_ton=$row_product_details['process_cost_ton'];
		//$extra_cost=((($packing_cost+$labour_cost)*10)/100)+$extra_cost;
		$extra_cost=((($packing_cost+$labour_cost)*10)/100)+$extra_cost;
		$prod_code=$row_product_details['prod_code'];
		$parent_child=$row_product_details['parent_child'];
		
		if($parent_child=='child') $margin_cost=0;
		
		$sqlselindustrialrate="SELECT sale_rate FROM industrial_rate WHERE product_code='".$prod_code."' AND 
								SUBSTRING(create_date,1,19)='".$from_date_check."' ORDER BY create_date DESC LIMIT 0,1";
		$rsselindustrialrate=mysql_query($sqlselindustrialrate);
		$rowselindustrialrate=mysql_fetch_array($rsselindustrialrate);
		$cntselindustrialrate=mysql_num_rows($rsselindustrialrate);
		if($cntselindustrialrate >0)
		{
			$sale_rate=$rowselindustrialrate['sale_rate'];
			$basic_rate=0;
			$process_cost=0;
			$packing_cost=0;
			$labour_cost=0;
			$extra_cost=0;
			$packing_realization_percent=0;
		}
		else $sale_rate=$sale_rate;
		
		echo "<tr>
				<td>".$count."</td>
				<td>".$dns_prod_code."</td>
				<td>".$prod_desc."</td>
				<td align=\"right\">".number_format($basic_rate,2)."</td>
				<td align=\"right\">".number_format($basic_rate_ton,2)."</td>
				<td align=\"right\">".number_format($process_cost,2)."</td>
				<td align=\"right\">".number_format($process_cost_ton,2)."</td>
				<td align=\"right\">".number_format($packing_cost,2)."</td>
				<td align=\"right\">".number_format($labour_cost,2)."</td>
				<td align=\"right\">".number_format($extra_cost,3)."</td>
				<td align=\"right\">".number_format($packing_realization_percent,2)."</td>
				<td align=\"right\">".number_format($margin_cost,2)."</td>
				<td align=\"right\">".number_format($sale_rate,2)."</td>
		</tr>";
		$count++;
	}
	echo "</table>";
	//echo "</div>";
	/*echo "<div style=\"width:60%;\" align=\"right\"><input name=\"print\" type=\"button\" value=\"Print\" id=\"print\" onClick=\"PrintElem('#display');\">&nbsp;
    <input name=\"export\" type=\"button\" value=\"Export\" id=\"btnExport\" onClick=\"ExportToExcel('');\" ></div>";*/
}
else
{
	echo "<strong><font color=\"red\">No records</font></strong>";
}
?>
