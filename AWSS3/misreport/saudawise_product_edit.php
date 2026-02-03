<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>

<?php
if($_GET['type'] == 'today')
{
	$today = date('Y-m-d');
	$today1 = str_replace("-","",$today);
	$condition = "AND DATE_FORMAT(SUBSTRING(SAL.allocation_date,1,10),'%Y-%m-%d') LIKE '%$today%'";
	$sauda_booked_condition = "AND substring(SD.sauda_no,-14,8) LIKE '".str_replace("-","",$today)."'";
	$sauda_duration = date('d-m-Y');
	$value = 1;
}
else if($_GET['type'] == 'mtd')
{
	@$current_date = date('Y-m-d');
	$month = explode("-",$current_date);
	$year = $month[0];
	$month = $month[1];
	$condition = "AND YEAR(SUBSTRING(SAL.allocation_date,1,10)) =". $year." AND MONTH(SUBSTRING(SAL.allocation_date,1,10)) =" .$month;
	$sauda_booked_condition = "AND substring(SD.sauda_no,8,4) =$year AND substring(SD.sauda_no,12,2) =$month";
	$sauda_duration = "From : 01-".$month."-".$year." To ".date('d-m-Y');
	$value = 2;
}
else if($_GET['type'] == 'custom')
{
	$start_date = str_replace("-","",$_GET['start_date']);
	$strt = date('d-m-Y',strtotime($start_date));
	$end_date = str_replace("-","",$_GET['end_date']);
	$endt = date('d-m-Y',strtotime($end_date));
	$condition = "AND SAL.allocation_date BETWEEN '".$_GET['start_date']." "."12:00:01' AND '".$_GET['end_date']." "."23:59:59'";
	$sauda_booked_condition = "AND (substring(SD.sauda_no,-14,8) BETWEEN ".$start_date." AND ".$end_date.")";
	$sauda_duration = "From :".$strt." to ".$endt;
	$value = 3;
}
else
{
	$today = date('Y-m-d');
	$condition = "AND DATE_FORMAT(SUBSTRING(SAL.allocation_date,1,10),'%Y-%m-%d') LIKE '$today'";
	$sauda_booked_condition = "AND substring(SD.sauda_no,-14,8) LIKE '".str_replace("-","",$today)."'";
	$sauda_duration = date('d-m-Y');
	$value = 1;
}
?>

<?php
date_default_timezone_set("Asia/Kolkata"); 
$sauda_date = date('d-m-Y');
$today = date('Y-m-d');

$today = str_replace("-","",$today);

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

$colorset = array('#FFE4B5','#EEEED1','#C1FFC1','#BBFFFF','#C6E2FF','#EEE0E5','#FFC1C1','#FFEBCD','#FFEC8B','#C1FFC1');

$sql_count_product = "SELECT $group_name, $group_code FROM $sauda_table_value $acronym ORDER BY $group_name ASC";
$res_count_product = mysql_query($sql_count_product);
$total_product = mysql_num_rows($res_count_product);

$count = 1;
$res_count_product = mysql_query($sql_count_product);
while($row_count_product = mysql_fetch_array($res_count_product))
{
	$pgcode = $row_count_product[$field_name1];
	$group_color_code[$pgcode] = $colorset[$count];
	$count++;
}
?>

<?php

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


$count = 1;
if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login']=="supervisor")
{
	$emp_condition = 1;
}
else
{
	$emp_condition = "reporting_to IN(".$emp_hierarchy.")";
}

$sauda_number = $_REQUEST['sauda_number'];
?>

<table border="1" class="border" style="border-collapse:collapse;" cellpadding="5px">
  <tr>
  	<td align="center" class="TDHEAD" colspan="8">Product Details Against Sauda No.:<?php echo $sauda_number; ?></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td>SI</td>
    <td>Product Name</td>
    <td>Booked(Case)</td>
    <td>Freight Charge</td>
    <td>TD/Premium</td>
    <td>Sale Rate</td>
    <td>Edit</td>
    <td>Delete</td>
  </tr>


<?php
$count = 1;
$sql_list_product = "SELECT PM.prod_desc, PM.prod_code, sum(SD.convert_qty_two) as mt_booked, sum(SD.qty) as booked_case, SD.freight_charge, SD.TD, SD.premium, SD.sale_rate,SD.depot_cost,SD.primary_freight,SD.liquid_TD  FROM sauda_details SD, product_master PM WHERE SD.sauda_no LIKE '".$sauda_number."' AND SD.sku_code = PM.prod_code GROUP BY SD.sku_code";
$res_list_product = mysql_query($sql_list_product);
while($row_list_product = mysql_fetch_array($res_list_product))
{
	$prod_name_code = $row_list_product['prod_desc']."^".$row_list_product['prod_code'];
	$trade_discount = $row_list_product['TD'];
	$premium = $row_list_product['premium'];
	$depot_cost = $row_list_product['depot_cost'];
	$primary_freight = $row_list_product['primary_freight'];
	$freight_charge = $row_list_product['freight_charge'];
	$liquid_TD = $row_list_product['liquid_TD'];
	
	$sale_rate=$row_list_product['sale_rate']+$primary_freight+$freight_charge+$depot_cost+$premium-$trade_discount-$liquid_TD;
	
	if($premium>0)
		$trade_discount_premium = $premium;
	else
		$trade_discount_premium = $trade_discount;
		
	echo "<tr>
			<td>".$count."</td>
			<td>".$row_list_product['prod_desc']."</td>
			<td align=\"right\">".$row_list_product['booked_case']."</td>
			<td align=\"right\">".$row_list_product['freight_charge']."</td>
			<td align=\"right\">".$trade_discount_premium."</td>
			<td align=\"right\">".number_format($sale_rate,2)."</td>
			<td><a href=\"#\" style=\"color:blue;\" onclick=\"edit_data('$prod_name_code','$row_list_product[booked_case]','$row_list_product[freight_charge]','$trade_discount','$premium','$sale_rate','$sauda_number');\">Edit</a></td>
			<td><a href=\"#\" style=\"color:red; text-decoration: none;\" onclick=\"delete_product('$sauda_number','$row_list_product[prod_code]','$row_list_product[booked_case]')\">Delete</a></td>
	</tr>";
	$count++;
}

?>
</table>
<?php
mysql_close($link);
?>