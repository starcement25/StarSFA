<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

?>
<table class="border" style="border-collapse:collapse;" border="1" cellpadding="6" width="100%">
  <tr class="TDHEAD">
  	<td>Invoice No</td>
    <td>Date</td>
    <td>Amount</td>
  </tr>
  
<?php
	$sql_order_header = "SELECT order_no FROM order_header WHERE order_no LIKE 'O%' AND status = 'billed'";
	$res_order_header = mysql_query($sql_order_header);
	while($row_order_header = mysql_fetch_array($res_order_header)){
		$order_no = $row_order_header['order_no'];
		
		$sql_order_track = "SELECT entry_date, invoice_no, invoice_amount FROM order_tracking WHERE order_no = '".$order_no."' GROUP BY invoice_no";
		$res_order_track = mysql_query($sql_order_track);
		while($row_order_track = mysql_fetch_array($res_order_track)){
			$entry_date = $row_order_track['entry_date'];
			$invoice_no = $row_order_track['invoice_no'];
			$invoice_amount = $row_order_track['invoice_amount'];
			
			echo " <tr>
					<td><a href=\"#\" style=\"color:blue;\" onclick=\"get_invoice_details('".$invoice_no."');\">".$invoice_no."</a></td>
					<td>".date('d-m-Y H:i:s',strtotime($entry_date))."</td>
					<td align=\"right\">".$invoice_amount."</td>
				  </tr>";
		}
	}
	mysql_close($link);
?>
</table>