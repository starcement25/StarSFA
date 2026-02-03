<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>
<?php
$product_desc = explode("^",$_REQUEST['product_desc']);
$booked = $_REQUEST['booked'];
$freight_charge = $_REQUEST['freight_charge'];
$trade_discount = $_REQUEST['trade_discount'];
$premium = $_REQUEST['premium'];
$sale_rate = $_REQUEST['sale_rate'];
$sauda_number  = $_REQUEST['sauda_number'];
?>

<table border="1" width="450px" style="border-collapse:collapse;" class="border" cellpadding="5px">
<tr class="TDHEAD">
	<td colspan="2" align="center">Edit Product Details: <?php echo  $product_desc[0];?></td>
<tr>
<tr>
	<td align="right">Booked(In cases):</td>
    <td align="left"><input type="text" name="booked" id="booked" value="<?php echo $booked; ?>" /></td>
</tr>
<tr>
	<td align="right">Freight Charge:</td>
    <td align="left"><input type="text" name="freight" id="freight" value="<?php echo $freight_charge; ?>" readonly /></td>
</tr>
<tr>
	<td align="right">Premium:</td>
    <td align="left"><input type="text" name="premium" id="premium" value="<?php echo $premium; ?>" /><!--input type="hidden" name="trade_discount" id="trade_discount" value="0" /--></td>
</tr> 
<tr>   
	<td align="right">Trade Discount:</td>
    <td align="left"><input type="text" name="trade_discount" id="trade_discount" value="<?php echo $trade_discount; ?>" /><!--input type="hidden" name="premium" id="premium" value="0" /--></td>
</tr>
<tr>
	<td align="right">Sale Rate:</td>
    <td align="left"><input type="text" name="sale_rate" id="sale_rate" value="<?php echo number_format($sale_rate,2); ?>" readonly /></td>
</tr>
<tr>
	<td></td>
    <td align="left"><input type="submit" name="submit" value="Submit" style="cursor:pointer;" onclick="edit_sauda_product_data();"/><input type="hidden" name="sauda_hidden_no" id="sauda_hidden_no" value="<?php echo $sauda_number; ?>" /><input type="hidden" name="hidden_product_code" id="product_hidden_code" value="<?php echo $product_desc[1]; ?>" /></td>
</tr>
