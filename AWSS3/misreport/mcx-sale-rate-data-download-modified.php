<?php
error_reporting(0);
ob_start();
session_start();
require("adminUtils.php");
$from_date = $_REQUEST['from_date'];
$from_date_check=date('Y-m-d',strtotime($from_date));
$from_date_final=$from_date_check.' 23:59:59';
$plant_name = $_REQUEST['plant_name'];
$sql_product_details = "SELECT * FROM (SELECT PM.dns_prod_code,PM.prod_desc,PM.packing_realization AS packing_realization_percent,MR.basic_rate_open,
						MR.basic_rate_close,MR.packing_cost,MR.process_cost,MR.labour_cost,MR.extra_cost,MR.margin_cost,MR.sale_rate_open,MR.sale_rate_close,MR.packing_realization 
						FROM product_master PM, mcx_rate MR
						WHERE PM.prod_code = MR.product_code AND SUBSTRING(MR.create_date,1,10)='".$from_date_check."' ORDER BY create_date DESC) AS SAT 
						GROUP BY 1 ORDER BY 2";
$res_product_details = mysql_query($sql_product_details);
$total_rows=mysql_num_rows($res_product_details);

if($total_rows >0){
    echo '<table class="border" width="85%" border="1" style="border-collapse:collapse;" cellpadding="5px" align="center">
	<thead>
      <tr>
        <td colspan="15" class="TDHEAD" align="left"><b>Detail Costing Report MCX - '.$from_date.'</b></td>
      </tr>
      <tr class="TDHEAD_SUB">
      	<td width="6%">SI</td>
        <td width="10%">Material Code</td>
        <td width="16%">Material Description</td>
        <td width="8%">Material Cost Open (MCX)</td>
		 <td width="8%">Material Cost Close (MCX)</td>
		<td width="7%">Process Cost</td>
        <td width="7%">Packing Cost</td>
		<td width="7%">Labour Cost</td>
		<td width="7%">Extra Cost</td>
        <td width="8%">Packing Realization</td>
        <td width="8%">Bargain Rate Open (MCX)</td>
		<td width="8%">Bargain Rate Close (MCX)</td>
      </tr></thead>';
	$count = 1;
	while($row_product_details = mysql_fetch_array($res_product_details))
	{
		$dns_prod_code=$row_product_details['dns_prod_code'];
		$prod_desc=$row_product_details['prod_desc'];
		$basic_rate_open=$row_product_details['basic_rate_open'];
		$basic_rate_close=$row_product_details['basic_rate_close'];
		$packing_cost=$row_product_details['packing_cost'];
		$process_cost=$row_product_details['process_cost'];
		$labour_cost=$row_product_details['labour_cost'];
		$extra_cost=$row_product_details['extra_cost'];
		$margin_cost=$row_product_details['margin_cost'];
		$sale_rate_open=$row_product_details['sale_rate_open'];
		$sale_rate_close=$row_product_details['sale_rate_close'];
		$packing_realization=$row_product_details['packing_realization'];
		$packing_realization_percent=$row_product_details['packing_realization_percent'];
		//$extra_cost=((($packing_cost+$labour_cost)*10)/100)+$extra_cost;
		$extra_cost=((($packing_cost+$labour_cost)*10)/100)+$extra_cost;
		
		echo "<tr>
				<td>".$count."</td>
				<td>".$dns_prod_code."</td>
				<td>".$prod_desc."</td>
				<td align=\"right\">".number_format($basic_rate_open,2)."</td>
				<td align=\"right\">".number_format($basic_rate_close,2)."</td>
				<td align=\"right\">".number_format($process_cost,2)."</td>
				<td align=\"right\">".number_format($packing_cost,2)."</td>
				<td align=\"right\">".number_format($labour_cost,2)."</td>
				<td align=\"right\">".number_format($extra_cost,3)."</td>
				<td align=\"right\">".number_format($packing_realization_percent,2)."</td>
				<td align=\"right\">".number_format($sale_rate_open,2)."</td>
				<td align=\"right\">".number_format($sale_rate_close,2)."</td>
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
