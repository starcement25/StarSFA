<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$pincode = $_REQUEST['pincode'];
$oilused = $_REQUEST['oilused'];

if($pincode != '')
	$pincode_condition = " AND pin LIKE '%".$pincode."%' ";
else
	$pincode_condition = "";
	
if($oilused != '')
	$oilused_condition = " AND oil_used LIKE '%".$oilused."%' ";
else
	$oilused_condition = "";

$sql_product_promotion_details = "SELECT * FROM product_promotion WHERE DATE_FORMAT(SUBSTRING(prospect_code,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."'".$pincode_condition.$oilused_condition;
$res_product_promotion_details = mysqli_query($link,$sql_product_promotion_details);
$total_rows = mysqli_num_rows($res_product_promotion_details);
if($total_rows>0)
{
	if($_GET['export'] == 'true')
	{
		$count = 1;
		$header_data = "SI"."\t"."Name"."\t"."Apartment No"."\t"."Street No"."\t"."Building No"."\t"."Street Name"."\t"."Pincode"."\t"."Phone"."\t"."Oil Used";
		
		$res_product_promotion_details = mysqli_query($link,$sql_product_promotion_details);
		while($row_product_promotion_details = mysqli_fetch_assoc($res_product_promotion_details))
		{
			$contents .= $count."\t".$row_product_promotion_details['prospect_name']."\t".$row_product_promotion_details['apartment_no']."\t".$row_product_promotion_details['street_no']."\t".$row_product_promotion_details['building_no']."\t".$row_product_promotion_details['street_name']."\t".$row_product_promotion_details['pin']."\t".$row_product_promotion_details['phone_no']."\t".$row_product_promotion_details['oil_used']."\n";
			$count++;
		}
		header("Content-type: application/octet-stream"); 
		header("Content-Disposition: attachment; filename=Product_Promotion_Report.xls"); 
		header("Pragma: no-cache"); 
		header("Expires: 0");
		echo ucwords($header_data)."\n".$contents;
	}
	else
	{
		$count = 1;
		echo "<table width=\"100%\" border=\"1\" style=\"border-collapse:collapse;\" class=\"border\" cellpadding=\"6px\">
			  <tr class=\"TDHEAD\">
				<td colspan=\"10\" align=\"center\">Product Promotion Details</td>
			  </tr>
			  <tr class=\"TDHEAD_SUB\">
				<td>SI</td>
				<td>Name</td>
				<td>Apartment No</td>
				<td>Street No</td>
				<td>Building No</td>
				<td>Street Name</td>
				<td>Pincode</td>
				<td>Phone</td>
				<td>Oil Used</td>
				<td>Edit</td>
			  </tr>";
		$res_product_promotion_details = mysqli_query($link,$sql_product_promotion_details);
		while($row_product_promotion_details = mysqli_fetch_assoc($res_product_promotion_details))
		{
			echo "<tr>
					<td>".$count."</td>
					<td>".$row_product_promotion_details['prospect_name']."</td>
					<td>".$row_product_promotion_details['apartment_no']."</td>
					<td>".$row_product_promotion_details['street_no']."</td>
					<td>".$row_product_promotion_details['building_no']."</td>
					<td>".$row_product_promotion_details['street_name']."</td>
					<td>".$row_product_promotion_details['pin']."</td>
					<td>".$row_product_promotion_details['phone_no']."</td>
					<td>".$row_product_promotion_details['oil_used']."</td>
					<td><a href=\"product_promotion.php?prospect_code=$row_product_promotion_details[prospect_code]\" style=\"color:blue;\">Edit</a></td>
				  </tr>";
			$count++;
		}
		echo "</table>";
	}
}
else
{
	echo "<font color='red'><strong>No records found</strong></font>";
}
mysqli_close($link);
?>