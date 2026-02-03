<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

$invoice_no = $_REQUEST['invoice_no'];
?>

<table class="border" border="1" style="border-collapse:collapse;" width="100%" cellpadding="4">
  <tr class="TDHEAD">
  	<td>SI</td>
    <td>Product Desc</td>
    <td width="5%">Billed Qty</td>
    <td  width="5%">Dispatch Qty</td>
    <td>Remarks</td>
  </tr>

<?php
	$count = 1;
	$sql_get_details = "SELECT order_no, prod_code, SUM(billed_qty), invoice_amount FROM order_tracking WHERE invoice_no = '".$invoice_no."' GROUP BY prod_code";
	$res_get_details = mysql_query($sql_get_details);
	while($row_get_details = mysql_fetch_array($res_get_details)){
		$order_no = $row_get_details['order_no'];
		$prod_code = $row_get_details['prod_code'];
		$billed_qty = $row_get_details['SUM(billed_qty)'];
		$invoice_amount = $row_get_details['invoice_amount'];
		$dispatch_qty_id = $order_no."_".$prod_code;
		
		$sql_dispatch_qty = "SELECT SUM(dispatch_qty) FROM dispatch_tracking WHERE prod_code = '".$prod_code."' AND invoice_no = '".$invoice_no."' AND order_no = '".$order_no."'";
		$res_dispatch_qty = mysql_query($sql_dispatch_qty);
		$row_dispatch_qty = mysql_fetch_array($res_dispatch_qty);
		$dispatch_qty = $row_dispatch_qty['SUM(dispatch_qty)'];
		
		if($dispatch_qty != $billed_qty){
			$sql_prod_desc = "SELECT prod_desc FROM product_master WHERE prod_code = '".$prod_code."'";
			$res_prod_desc = mysql_query($sql_prod_desc);
			$row_prod_desc = mysql_fetch_array($res_prod_desc);
			$prod_desc = $row_prod_desc['prod_desc'];
			$max_value = $billed_qty - $dispatch_qty;
			
			$max_input_func = " onkeyup=\"max_value_check(this.value,'".$billed_qty."','".$dispatch_qty_id."','".$max_value."','".$dispatch_qty."');\"";
			
			echo "<tr>
					<td>".$count."</td>
					<td>".$prod_desc."</td>
					<td align=\"right\">".$billed_qty."</td>
					<td><input type=\"text\" value=\"$dispatch_qty\" name=\"dispatch_qty\" id=\"".$dispatch_qty_id."\" style=\"width:80%;\" $max_input_func /></td>
					<td><input type=\"text\" id=\"remarks_$dispatch_qty_id\" ></td>
				  </tr>";
			$count++;
			$order_product_string .= $dispatch_qty_id."^";
		}
	}
	$order_product_string = rtrim($order_product_string,"^");
?>
  <tr>
  	<td colspan="5" ><b>Dispatch Through:</b> <input type="text" id="dispatch_through" /></td>
  </tr>
  <tr>
  	<td colspan="5" align="center">
    <input type="hidden" value="<?php echo $invoice_no; ?>" id="invoice_no" />
    <input type="hidden" value="<?php echo $order_no; ?>" id="order_no" />
    <input type="hidden" value="<?php echo $invoice_amount; ?>" id="invoice_amount" />
    <input type="button" value="Submit" onclick="submit_dispatch_details('<?php echo $order_product_string; ?>');" />
    </td>
  </tr>
</table>
<?php
mysql_close($link);
?>