<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$order_no = $_REQUEST['order_no'];
$select_val = $_REQUEST['select_val'];

if($select_val == 'hold' || $select_val == 'spl permission'){
	?>

    <table class="border" style="border-collapse:collapse;" width="100%">
      <tr class="TDHEAD">
      	<td colspan="2" align="center"><?php echo strtoupper($select_val); ?></td>
      </tr>
      <tr>
      	<td align="center" width="10%"><b>Reason:</b></td>
        <td><br /><textarea name="reason" id="reason" cols="35" rows="8" style="resize:none;"></textarea></td>
      </tr>
      <tr>
      	<td align="center" colspan="2"><input type="submit" value="Submit" id="submit_reason" onclick="submit_reason('<?php echo $order_no; ?>','<?php echo $select_val; ?>');"  /></td>
      </tr>
    </table>
    <?php
}
else if($select_val == 'billed'){
	?>
    <table class="border" border="1" style="border-collapse:collapse;" width="100%" cellpadding="5">
      <tr class="TDHEAD">
      	<td width="40%">Product Desc</td>
        <td  width="35%">Order Qty</td>
        <td width="25%">Billed Qty</td>
      </tr>
    <?php
	$order_product_array = array();
	$sql_order_details = "SELECT `sku_code`, `qty`, `sale_rate`, `billed_qty` FROM order_details WHERE order_no = '".$order_no."' AND `qty` != `billed_qty`";
	$res_order_details = mysql_query($sql_order_details);
	$total_no_products = mysql_num_rows($res_order_details);
	
	$res_order_details = mysql_query($sql_order_details);
	while($row_order_details = mysql_fetch_array($res_order_details)){
		$sku_code = $row_order_details['sku_code'];
		$qty = $row_order_details['qty'];
		$sale_rate = $row_order_details['sale_rate'];
		$billed_qty = $row_order_details['billed_qty'];
		
		$sql_product_name = "SELECT prod_desc FROM product_master WHERE prod_code = '".$sku_code."'";
		$res_product_name = mysql_query($sql_product_name);
		$row_product_name = mysql_fetch_array($res_product_name);
		$product_desc = $row_product_name['prod_desc'];
		
		$order_qty_id = "order_".$order_no."_".$sku_code;
		$billed_qty_id = "billed_".$order_no."_".$sku_code;
		
		$order_product_string .= $order_no."_".$sku_code."^";	
		
		if($billed_qty != 0){
			$max_input_func = " onkeyup=\"max_value_check(this.value,'".$billed_qty."','".$qty."','".$billed_qty_id."');\"";
		}
		else{
			$max_input_func = '';
		}
		
		echo "<tr>
				<td align=\"right\">".$product_desc."</td>
				<td align=\"right\" ><span id=\"".$order_qty_id."\">$qty</span></td>
				<td><input type=\"text\" name=\"billed_qty\" id=\"".$billed_qty_id."\" value=\"".$billed_qty."\" $max_input_func /></td>
			  </tr>";
	}
	$order_product_string = rtrim($order_product_string,"^");
	?>
    <tr>
    	<td colspan="2" align="right"><b>Invoice No</b></td>
        <td><input type="text" name="invoice_no" id="invoice_no" /></td>
    </tr>
    <tr>
    	<td colspan="2" align="right"><b>Invoice Amount</b></td>
        <td><input type="text" name="invoice_amt" id="invoice_amt" /></td>
    </tr>
    <tr>
    	<td align="center" colspan="3" class="TDHEAD_SUB"><input type="submit" value="Submit" id="order_submit_id" onclick="submit_order('<?php echo $order_product_string; ?>');" /></td>
    </tr>
    <?php
}
mysql_close($link);
?>