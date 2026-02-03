<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

$sql_sauda_filter = "SELECT sauda_allocation_basedon_filter FROM acedns_acednsproduct.product_details WHERE nick_name='$_SESSION[nick_name]'";
$res_sauda_filter = mysqli_query($link,$sql_sauda_filter);
$row_sauda_filter = mysqli_fetch_assoc($res_sauda_filter);

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
	
	$condition = "AND (substring(SD.sauda_no,-14,8) BETWEEN ".$start_date." AND ".$end_date.")";
	$value = 3;
}

if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login']=="supervisor" || $_SESSION['admin_login']=="system"){
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
	
	
$product_group_code = $_REQUEST['product_group_code'];
$zone = $_REQUEST['zone'];
$state = $_REQUEST['state'];

$sql_prodgr_name = "SELECT product_group_name FROM product_group_master WHERE product_group_code = '$product_group_code'";
$res_prodgr_name = mysqli_query($link,$sql_prodgr_name);
$row_prodgr_name = mysqli_fetch_assoc($res_prodgr_name);
$prod_gr_name = $row_prodgr_name['product_group_name'];

?>
<table width="100%" border="1" style="border-collapse:collapse;" class="border" cellpadding="5px">
<tr class="TDHEAD">
	<td align="center" colspan="7"><?php echo $prod_gr_name; ?></td>
</tr>
<tr class="TDHEAD_SUB" align="center">
  	<td rowspan="2">SI</td>
    <td rowspan="2">Date</td>
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
$date_array = array();
if($_REQUEST['mode'] == 'state')
{
	$sql_zonewise_product = "SELECT DATE_FORMAT(SUBSTRING(SD.sauda_no,-14,8),'%d-%m-%Y') as date_selected, PM.prod_desc, sum(convert_qty_two) as mt_booked, sum(SD.qty) as case_booked, SD.sale_rate, SD.freight_charge, SD.amount FROM sauda_details SD, product_master PM, employee_master EM WHERE SUBSTRING(SD.sauda_no,3,5)= EM.emp_code AND EM.state LIKE '%$state%' $emp_hierarchy_condition_one $condition AND PM.$field_name1='$product_group_code' AND SD.sku_code=PM.prod_code GROUP BY SD.sku_code ORDER BY date_selected, EM.state ASC";
}
else
{
	$sql_zonewise_product = "SELECT DATE_FORMAT(SUBSTRING(SD.sauda_no,-14,8),'%d-%m-%Y') as date_selected, PM.prod_desc, sum(convert_qty_two) as mt_booked, sum(SD.qty) as case_booked, SD.sale_rate, SD.freight_charge, SD.amount FROM sauda_details SD, product_master PM, employee_master EM WHERE SUBSTRING(SD.sauda_no,3,5)= EM.emp_code AND EM.zone LIKE '%$zone%' $emp_hierarchy_condition_one $condition AND PM.$field_name1='$product_group_code' AND SD.sku_code=PM.prod_code GROUP BY SD.sku_code ORDER BY date_selected, EM.zone ASC";
}
$res_zonewise_product = mysqli_query($link,$sql_zonewise_product);
while($row_zonewise_product = mysqli_fetch_assoc($res_zonewise_product))
{
	$date_selected = $row_zonewise_product['date_selected'];
	echo "<tr><td>".$count."</td>";
	if(!in_array($date_selected,$date_array))
	{
		array_push($date_array,$date_selected);
		echo "<td>".$date_selected."</td>";
	}
	else
	{
		echo "<td></td>";
	}
	echo "<td>".$row_zonewise_product['prod_desc']."</td>
		  <td align=\"right\">".$row_zonewise_product['mt_booked']."</td>
		  <td align=\"right\">".$row_zonewise_product['case_booked']."</td>
		  <td align=\"right\">".number_format($row_zonewise_product['sale_rate'],2)."</td>
		  <td align=\"right\">".$row_zonewise_product['amount']."</td>";
		  
	$total_amount += $row_zonewise_product['amount'];
  	$total_booked += $row_zonewise_product['mt_booked'];
  	$total_booked_case += $row_zonewise_product['case_booked'];
		  
	$count++;
}
?>
<tr style="font-weight:bold;">
	<td colspan="3" align="center">Total</td>
    <td align="right"><?php echo $total_booked; ?></td>
    <td align="right"><?php echo $total_booked_case; ?></td>
    <td></td>
    <td align="right"><?php echo $total_amount; ?></td>
</tr>
</table>
<?php
mysqli_close($link);
?>