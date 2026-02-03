<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>

<table border="1" class="border" style="border-collapse:collapse;">
<tr class="TDHEAD">
	<td align="center">Sauda Number</td>
</tr>
<?php
$sauda_number = explode("^",$_REQUEST['customer_selected']);

$sql_select_saudano = "SELECT SH.sauda_no FROM sauda_header SH WHERE date_format(substring(SH.sauda_no,-14,8),'%d-%m-%Y') = '$sauda_number[2]' AND SH.customer_code = '$sauda_number[1]' AND SH.sauda_no NOT LIKE 'N%'";
$res_select_saudano = mysqli_query($link,$sql_select_saudano);
while($row_select_saudano = mysqli_fetch_assoc($res_select_saudano))
{
	echo "<tr><td><a href=\"#\" style=\"color:blue;\" onclick=\"show_sauda_data('$row_select_saudano[sauda_no]');\">".$row_select_saudano['sauda_no']."</a></td></tr>";
}
?>
</table>
<?php
mysqli_close($link);
?>