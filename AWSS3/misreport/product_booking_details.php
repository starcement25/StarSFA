<?php
error_reporting(0);
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>

<?php
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


if($_GET['condition_value'] == 1)
{
	$today = date('Y-m-d');
	$today = str_replace("-","",$today);
	$condition = "AND substring(SD.sauda_no,-14,8) LIKE '%$today%'";
	$sauda_duration = date('d-m-Y');
	$value = 1;
}
else if($_GET['condition_value'] == 2)
{
	@$current_date = date('Y-m-d');
	$month = explode("-",$current_date);
	$year = $month[0];
	$month = $month[1];
	/*$condition = "AND (YEAR(substring(OD.order_no,7,4)) =".$year." AND MONTH(substring(OD.order_no,11,2)) =".$month.")";*/
	$condition = "AND substring(SD.sauda_no,8,4) =$year AND substring(SD.sauda_no,12,2) =$month";
	$sauda_duration = "From : 01-".$month."-".$year." To ".date('d-m-Y');
	$value = 2;
}
else if($_GET['condition_value'] == 3)
{
	$start_date = str_replace("-","",$_GET['start_date']);
	$strt = date('d-m-Y',strtotime($start_date));
	$end_date = str_replace("-","",$_GET['end_date']);
	$endt = date('d-m-Y',strtotime($end_date));
	$condition = "AND (substring(SD.sauda_no,-14,8) BETWEEN ".$start_date." AND ".$end_date.")";
	$sauda_duration = "From :".$strt." to ".$endt;
	$value = 3;
}


?>
<table width="100%" border="1" class="border" style="border-collapse:collapse;">
  <tr align="center" style="font-weight:bold;">
  	<td colspan="7" class="TDHEAD"><?php echo $_GET['product_group_name']." ".$sauda_duration; ?></td>
  </tr>
  <tr align="center" style="font-weight:bold;" class="TDHEAD_SUB">
    <td rowspan="2">Product</td>
    <td colspan="3">Booked</td>
    <td rowspan="2">Rate</td>
    <td rowspan="2">TD</td>
    <td rowspan="2">Total Value</td>
  </tr>
  <tr align="center" style="font-weight:bold;" class="TDHEAD_SUB">
    <td>MT</td>
    <td>LTR</td>
    <td>Cases</td>
  </tr>

<?php
$product_name_array=array();
$sale_rate_array=array();
$td_array=array();
if($_SESSION['admin_login']=="admin"){
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


	$sql_quantity_booked = "SELECT PM.prod_desc,round(((PM.conversion_factor*sum(SD.qty))/PM.conversion_factor_two),2 )as mt, (PM.conversion_factor*sum(SD.qty)) as ltr, sum(SD.qty), SD.sale_rate, SD.TD, SD.amount FROM sauda_details SD, product_master PM, $sauda_table_value $acronym WHERE SD.sku_code = PM.prod_code AND PM.product_group_code = $group_code AND $group_name LIKE '%$_GET[product_group_name]%' $emp_hierarchy_condition_one $condition GROUP BY PM.prod_desc";
	$res_quantity_booked = mysql_query($sql_quantity_booked);
	while($row_quantity_booked = mysql_fetch_array($res_quantity_booked))
	{
		$product_name = $row_quantity_booked['prod_desc'];
		if(!in_array($product_name,$product_name_array))
		{
			array_push($product_name_array,$product_name);
			array_push($sale_rate_array,$row_quantity_booked['sale_rate']);
			array_push($td_array,$row_quantity_booked['TD']);
		}
		${mt.$product_name}=${mt.$product_name}+$row_quantity_booked['mt'];
		${ltr.$product_name}=${ltr.$product_name}+$row_quantity_booked['ltr'];
		${bookedqty.$product_name}=${bookedqty.$product_name}+$row_quantity_booked['sum(SD.qty)'];
		${amount.$product_name}=${amount.$product_name}+$row_quantity_booked['amount'];
	}

for($i=0;$i <count($product_name_array);$i++)
{
	echo "<tr>
    <td>$product_name_array[$i]</td>
    <td align=\"right\">".${mt.$product_name_array[$i]}."</td>
    <td align=\"right\">".${ltr.$product_name_array[$i]}."</td>
    <td align=\"right\">".${bookedqty.$product_name_array[$i]}."</td>
    <td align=\"right\">$sale_rate_array[$i]</td>
    <td align=\"right\">$td_array[$i]</td>
    <td align=\"right\">".number_format(${amount.$product_name_array[$i]},2)."</td>
  </tr>";
  
  $total_mt=$total_mt+${mt.$product_name_array[$i]};
  $total_ltr=$total_ltr+${ltr.$product_name_array[$i]};
  $total_bookedqty=$total_bookedqty+${bookedqty.$product_name_array[$i]};
  $total_amount=$total_amount+${amount.$product_name_array[$i]};
}

echo "<tr style=\"font-weight:bold;\">
    <td>Total</td>
    <td align=\"right\">$total_mt</td>
    <td align=\"right\">$total_ltr</td>
    <td align=\"right\">$total_bookedqty</td>
    <td align=\"right\"></td>
    <td align=\"right\"></td>
    <td align=\"right\">".number_format($total_amount,2)."</td>
  </tr>";
  
echo "</table>";

if(empty($product_name_array))
	echo "<font color=\"#FF0000\"><strong>No records found</strong></font>";+
	
	print_r($mt);
	
	mysql_close($link);
?>
