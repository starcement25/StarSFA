<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$receipt_id = $_REQUEST['receipt_id'];
$customer_name = $_REQUEST['customer_name'];
?>

<?php
$count = 1;
$sql_invoice = "SELECT invoice_id, amount FROM payment_details WHERE receipt_id='".$receipt_id."'";
$res_invoice = mysqli_query($link,$sql_invoice);
$total_rows = mysqli_num_rows($res_invoice);

if($total_rows>0){
	?>
    <table width="100%" border="1" style="border-collapse:collapse;" cellpadding="6">
      <tr class="TDHEAD"><td colspan="5" align="center">Invoice Details - <?php echo $customer_name; ?></td></tr>
      <tr align="center" style="font-weight:bold;" class="TDHEAD_SUB">
        <td>SI</td>
        <td width="20%">Invoice Date</td>
        <td>Invoice No</td>
        <td>Invoice Amount</td>
        <td>Collected Amount</td>
      </tr>
    <?php
	$res_invoice = mysqli_query($link,$sql_invoice);
	while($row_invoice = mysqli_fetch_assoc($res_invoice)){
	$invoice_id = $row_invoice['invoice_id'];
	$amount = $row_invoice['amount'];
	
	$sql_outstanding = "SELECT date, invoice_amount FROM outstanding WHERE invoice_id = '".$invoice_id."'";
	$res_outstanding = mysqli_query($link,$sql_outstanding);
	$row_outstanding = mysqli_fetch_assoc($res_outstanding);
	$invoice_amt = $row_outstanding['invoice_amount'];
	$invoice_date = date('d-m-Y',strtotime(''.$row_outstanding['date'].''));
	if($invoice_id == ''){
		$invoice_id = 'On Account';
		$invoice_date='';
		$invoice_amt=0;
	}
	echo "<tr>
			<td>".$count."</td>
			<td>".$invoice_date."</td>
			<td>".$invoice_id."</td>
			<td align=\"right\">".number_format($invoice_amt,2)."</td>
			<td align=\"right\">".number_format($amount,2)."</td>
		  </tr>";
	$total_amount += $amount;
	$total_invoice_amount += $invoice_amt;
	$count++;
	}
	echo "<tr style=\"font-weight:bold;\">
			<td colspan=\"3\">Total</td>
			<td align=\"right\">".number_format($total_invoice_amount,2)."</td>
			<td align=\"right\">".number_format($total_amount,2)."</td>
		  </tr>";
}
else{
	echo "<font color='red'><strong>No records</strong></font>";
}
?>
</table>
<?php
mysqli_close($link);
?>