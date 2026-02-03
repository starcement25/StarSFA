<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

$sql_sauda_filter = "SELECT sauda_allocation_basedon_filter FROM acedns_acednsproduct.product_details WHERE nick_name='$_SESSION[nick_name]'";
$res_sauda_filter = mysql_query($sql_sauda_filter);
$row_sauda_filter = mysql_fetch_array($res_sauda_filter);

$sauda_filter_value = $row_sauda_filter['sauda_allocation_basedon_filter'];

if($sauda_filter_value == 1)
{
	$sauda_table_value = 'product_group_master';
	$field_name1 = 'product_group_code';
	$field_name2 = 'product_group_name';
	$acronym = "PGM";
}
else if($sauda_filter_value == 2)
{
	$sauda_table_value = 'product_sub_group_master';
	$field_name1 = 'product_sub_group_code';
	$field_name2 = 'product_sub_group_name';
	$acronym = "PSGM";
}
else if($sauda_filter_value == 3)
{
	$sauda_table_value = 'product_brand_master';
	$field_name1 = 'product_brand_code';
	$field_name2 = 'product_brand_name';
	$acronym = "PBM";
}
else if($sauda_filter_value == 4)
{
	$sauda_table_value = 'product_master';
	$field_name1 = 'product_code';
	$field_name2 = 'product_name';
	$acronym = "PM";
}

$group_name = $acronym.".".$field_name2;
$group_code = $acronym.".".$field_name1;

$emp_code = $_REQUEST['emp_code'];
$emp_wise_hierarchy_condition=return_employee_hierarchy($emp_code);
$product_code = $_REQUEST['product_code'];
$product_name = $_REQUEST['product_name'];
$emp_name = $_REQUEST['emp_name'];
$customer_name = explode("^",$_REQUEST['customer_name']);
//$customer_code = $_REQUEST['customer_code'];
$branch_name = explode("^",$_REQUEST['branch_name']);
$product_group_code = $_REQUEST['product_group_code'];
$selected_date = $_REQUEST['selected_date'];


if($_REQUEST['condition_value'] == 1)
{
	$today = date('Y-m-d');
	$today = str_replace("-","",$today);
			
	$condition = "AND substring(SD.sauda_no,-14,8) LIKE '%$today%'";
	$value = 1;
}
else if($_REQUEST['condition_value'] == 2)
{
	@$current_date = date('Y-m-d');
	$month = explode("-",$current_date);
	$year = $month[0];
	$month = $month[1];
	
	$condition = "AND substring(SD.sauda_no,8,4) =$year AND substring(SD.sauda_no,12,2) =$month";
	$value = 2;
}
else if($_REQUEST['condition_value'] == 3)
{
	$start_date = $_GET['start_date'];
	$end_date = $_GET['end_date'];
	
	$condition = "AND (substring(SD.sauda_no,8,8) BETWEEN ".$start_date." AND ".$end_date.")";
	$value = 3;
}

  if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login']=="supervisor"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
		$emp_hierarchy_condition_one='';
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=' AND SAL.emp_code IN('.$emp_hierarchy.')';
		$emp_hierarchy_condition_one=' AND substring(SD.sauda_no,3,5) IN ('.$emp_hierarchy.')';
	}
	
?>
<table width="100%" border="1" style="border-collapse:collapse;" class="border">
  <tr>
  	<td colspan="7" class="TDHEAD" align="center"><?php if($customer_name[1] != ''){ echo $customer_name[0]; } else if($emp_code != ''){ echo $emp_name;} else {echo $branch_name[0];} echo "<br>".$product_name."<br>"; if($selected_date) echo $selected_date;?></td>
  </tr>
  <tr class="TDHEAD_SUB" align="center">
  	<td rowspan="2">SI</td>
  	<td rowspan="2">Product</td>
    <td colspan="2">Quantity</td>
    <td rowspan="2">Sale Rate</td>
    <td rowspan="2">Amount</td>
  </tr>
  <tr class="TDHEAD_SUB" align="center">
  	<td>MT</td>
    <td>Case</td>
  </tr>
  

<?php
$count = 1;

if($customer_name[1] != '')
{
	if($selected_date != '')
	{
		$sql_customer_product = "SELECT PM.prod_desc, sum(convert_qty_two) as mt_booked, sum(SD.qty) as case_booked, SD.sale_rate, SD.freight_charge, SD.amount FROM sauda_details SD, product_master PM, sauda_header SH WHERE SH.sauda_no=SD.sauda_no AND SH.customer_code='$customer_name[1]' $emp_hierarchy_condition_one AND  DATE_FORMAT(SUBSTRING(SD.sauda_no,-14,8),'%d-%m-%Y') = '$selected_date' AND PM.$field_name1='$product_group_code' AND SD.sku_code=PM.prod_code GROUP BY SD.sku_code";
	}
	else
	{
		$sql_customer_product = "SELECT PM.prod_desc, sum(convert_qty_two) as mt_booked, sum(SD.qty) as case_booked, SD.sale_rate, SD.freight_charge, SD.amount FROM sauda_details SD, product_master PM, sauda_header SH WHERE SH.sauda_no=SD.sauda_no AND SH.customer_code='$customer_code' $condition AND PM.$field_name1='$product_code' AND SD.sku_code=PM.prod_code GROUP BY SD.sku_code";
	}
}
else if($branch_name[1] != '')
{
	if($selected_date != '')
	{
		$sql_customer_product = "SELECT PM.prod_desc, sum(convert_qty_two) as mt_booked, sum(SD.qty) as case_booked, SD.sale_rate, SD.freight_charge, SD.amount FROM sauda_details SD, sauda_header SH, product_master PM WHERE SD.sauda_no=SH.sauda_no AND SH.branch_code='$branch_name[1]' $emp_hierarchy_condition_one AND DATE_FORMAT(SUBSTRING(SD.sauda_no,-14,8),'%d-%m-%Y') = '$selected_date' AND PM.$field_name1='$product_group_code' AND SD.sku_code=PM.prod_code GROUP BY SD.sku_code";
	}
	else
	{
		$sql_customer_product = "SELECT PM.prod_desc, sum(convert_qty_two) as mt_booked, sum(SD.qty) as case_booked, SD.sale_rate, SD.freight_charge, SD.amount FROM sauda_details SD, sauda_header SH, product_master PM WHERE SD.sauda_no=SH.sauda_no AND SH.branch_code='$branch_name[1]' $emp_hierarchy_condition_one $condition AND PM.$field_name1='$product_group_code' AND SD.sku_code=PM.prod_code GROUP BY SD.sku_code";
	}
}
else
{
	$sql_customer_product ="SELECT PM.prod_desc, sum(convert_qty_two) as mt_booked, sum(SD.qty) as case_booked, SD.sale_rate, SD.freight_charge, SD.amount FROM sauda_details SD, product_master PM 
							WHERE SUBSTRING(SD.sauda_no,3,5) IN (".$emp_wise_hierarchy_condition.") $condition AND PM.$field_name1='$product_code' AND SD.sku_code=PM.prod_code GROUP BY SD.sku_code";
}
$res_customer_product = mysql_query($sql_customer_product);
while($row_customer_product = mysql_fetch_array($res_customer_product))
{
	echo "<tr>
	<td>$count</td>
  	<td>$row_customer_product[prod_desc]</td>
    <td align=\"right\">".$row_customer_product['mt_booked']."</td>
	<td align=\"right\">".$row_customer_product['case_booked']."</td>
    <td align=\"right\">".number_format($row_customer_product['sale_rate'],2)."</td>
    <td align=\"right\">".number_format($row_customer_product['amount'],2)."</td>
  </tr>";
  
  $total_amount += $row_customer_product['amount'];
  $total_booked += $row_customer_product['mt_booked'];
  $total_booked_case += $row_customer_product['case_booked'];
  $count++;
}
?>
<tr style="font-weight:bold;">
  	<td colspan="2" align="center">Total</td>
    <td align="right"><?php echo number_format($total_booked,3); ?></td>
    <td align="right"><?php echo $total_booked_case ?></td>
    <td></td>
    <td align="right"><?php echo number_format($total_amount,2); ?></td>
  </tr>

</table>
<?php
mysql_close($link);
?>