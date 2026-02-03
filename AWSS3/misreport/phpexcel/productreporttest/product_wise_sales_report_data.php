<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

if(strtoupper($_SESSION['admin_login']) == "ADMIN"){
	$emp_hierarchy = "";
	$emp_hierarchy_condition = "";
}
else{
	$emp_hierarchy = return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition = " AND SUBSTRING(OD.order_no,2,5) IN (".$emp_hierarchy.") ";
}

function header_value($column_name,$tabl_name,$compare_col,$compare_value){
	$sql = "SELECT $column_name FROM $tabl_name WHERE $compare_col = '".$compare_value."'";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	$header_value = $row[$column_name];
	return $header_value;
}

$today = date('Ymd');
$type = $_REQUEST['type'];
if($type == 'prod_group'){
	$prod_group_code = $_REQUEST['prod_group_code'];
	$table_name = ", product_group_master PGM, product_master PM ";
	$table_condition = " AND OD.sku_code = PM.prod_code AND PM.product_group_code = PGM.product_group_code AND PGM.product_group_code = '".$prod_group_code."' ";
	
	$header_value = header_value('product_group_name','product_group_master','product_group_code',$prod_group_code);
}
else if($type == 'prod_sub_group'){
	$prod_sub_group_code = $_REQUEST['prod_sub_group_code'];
	$table_name = ", product_sub_group_master PSGM, product_master PM ";
	$table_condition = " AND OD.sku_code = PM.prod_code AND PM.product_sub_group_code = PSGM.product_sub_group_code AND PSGM.product_sub_group_code = '".$prod_sub_group_code."' ";
	
	$header_value = header_value('product_sub_group_name','product_sub_group_master','product_sub_group_code',$prod_sub_group_code);
}
else if($type == 'prod_brand'){
	$prod_brand_code = $_REQUEST['prod_brand_code'];
	$table_name = ", product_brand_master PBM, product_master PM ";
	$table_condition = " AND OD.sku_code = PM.prod_code AND PM.product_brand_code = PBM.product_brand_code AND PBM.product_brand_code = '".$prod_brand_code."' ";
	
	$header_value = header_value('product_brand_name','product_brand_master','product_brand_code',$prod_brand_code);
}
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
if($start_date != '' && $end_date != '')
	$date_condition = " AND (SUBSTRING(OD.order_no,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ";
else
	$date_condition = " AND SUBSTRING(OD.order_no,-14,8) = '".$today."' ";

$sql_order_details = "SELECT OD.sku_code, SUM(OD.qty), SUM(OD.amount) FROM order_details OD ".$table_name." WHERE OD.order_no LIKE 'O%'".$date_condition.$table_condition.$emp_hierarchy_condition."GROUP BY OD.sku_code";
$res_order_details = mysql_query($sql_order_details);
$total_row_check = mysql_num_rows($res_order_details);
$count = 1;
if($total_row_check>0){
	?>
    <table class="border" width="100%" border="1" style="border-collapse:collapse;" cellpadding="4px">
      <tr>
      	<td class="TDHEAD" align="center" colspan="4"><?php echo $header_value; ?></td>
      </tr>
      <tr class="TDHEAD">
      	<td>SL</td>
      	<td>Product Name</td>
        <td>Quantity</td>
        <td>Amount</td>
      </tr>
    <?php
	$res_order_details = mysql_query($sql_order_details);
	while($row_order_details = mysql_fetch_array($res_order_details)){
		$sku_code = $row_order_details['sku_code'];
		$qty = $row_order_details['SUM(OD.qty)'];
		$amt = $row_order_details['SUM(OD.amount)'];
		
		$sql_prod_desc = "SELECT prod_desc FROM product_master WHERE prod_code = '".$sku_code."'";
		$res_prod_desc = mysql_query($sql_prod_desc);
		$row_prod_desc = mysql_fetch_array($res_prod_desc);
		$prod_desc = $row_prod_desc['prod_desc'];
		
		echo "<tr>
				<td>".$count."</td>
				<td>".$prod_desc."</td>
				<td align=\"right\">".$qty."</td>
				<td align=\"right\">".number_format($amt,2)."</td>
			  </tr>";
		
		$total_qty += $qty;
		$total_amt += $amt;
		
		$count++;
	}
	?>
      <tr class="TDHEAD_SUB">
      	<td colspan="2">Total</td>
        <td align="right"><?php echo $total_qty; ?></td>
        <td align="right"><?php echo number_format($total_amt,2); ?></td>
      </tr>
    </table>
    <br />
    <div style="width:100%;" align="right">
    <?php
	echo "<input name=\"export\" type=\"button\" value=\"Print\" id=\"btnExport\" onClick=\"pdffile('".$_SESSION['admin_login']."','".$start_date."','".$end_date."');\" >";
	?>
    <!--<input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">-->&nbsp;
    <!--<input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >-->
    <?php
	echo "<input name=\"export\" type=\"button\" value=\"Export\" id=\"btnExport\" onClick=\"csvexport('".$_SESSION['admin_login']."','".$start_date."','".$end_date."');\" >";
	?>
	</div>
    <?php
}
else{
	echo "<div style=\"color:red;\"><b>No Records Available</b></div>";
}
?>