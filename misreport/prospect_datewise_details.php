<?php
ob_start();

	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>

<?php

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
?>

<body>
<center>

<?php


$sql_prospect = "SELECT DATE_FORMAT(SUBSTRING(trans_id,-14,14),'%d-%m-%Y') as prospect_date,remarks,trans_id FROM prospective_customer_header WHERE customer_code = '$_GET[pcid]' ORDER BY DATE_FORMAT(SUBSTRING(trans_id,-14,14),'%Y-%m-%d %H:%i:%s') DESC";
echo "<table border=\"1\" style=\"border-collapse:collapse; width:100%;\" class=\"border\">
		<tr>
			<td class=\"TDHEAD\" colspan=\"6\" align=\"center\">Details of $_GET[customer_name]</td>
		</tr>
		<tr class=\"TDHEAD_SUB\">
			<td>SI</td>
			<td>Date</td>
			<td>Discussed Product</td>
			<td>Remarks</td>
		</tr>";

$res_prospect = mysqli_query($link,$sql_prospect);
$countslno=1;
while($row_prospect = mysqli_fetch_assoc($res_prospect))
{ 
  $trans_id=$row_prospect['trans_id'];
  $SQLproddiscussed="SELECT GROUP_CONCAT(PM.prod_desc SEPARATOR ',') as prod_discussed FROM prospective_customer_details PCD,product_master PM WHERE 
  					PM.prod_code=PCD.product_code AND PCD.trans_id='".$trans_id."' ORDER BY PM.prod_desc ASC";
  $rsproddiscussed=mysqli_query($link,$SQLproddiscussed);
  $rowproddiscussed=mysqli_fetch_assoc($rsproddiscussed);
  $prod_discussed=$rowproddiscussed['prod_discussed'];
?>
  <tr>
    <td><?php echo $countslno; ?></td>
  	<td><?php echo $row_prospect['prospect_date']; ?></td>
    <td><?php echo $prod_discussed; ?></td>
    <td><?php echo $row_prospect['remarks']; ?></td>
  </tr>
  <?php
	$countslno++;
	}?>
</table>

</center>
</body>
<?php
mysqli_close($link);
?>
