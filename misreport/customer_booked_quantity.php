<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>

<?php
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
	$emp_code = $_REQUEST['emp_code'];
	$prod_code = $_REQUEST['product_code'];
	$prod_name = $_REQUEST['product_name'];
	
	$condition = "AND substring(SH.sauda_no,8,8) LIKE '%$today%'";
	$value = 1;
}
else if($_REQUEST['condition_value'] == 2)
{
	@$current_date = date('Y-m-d');
	$month = explode("-",$current_date);
	$year = $month[0];
	$month = $month[1];
	$emp_code = $_REQUEST['emp_code'];
	$prod_code = $_REQUEST['product_code'];
	$prod_name = $_REQUEST['product_name'];
	
	$condition = "AND substring(SH.sauda_no,8,4) =$year AND substring(SH.sauda_no,12,2) =$month";
	$value = 2;
}
else if($_REQUEST['condition_value'] == 3)
{
	$start_date = str_replace("-","",$_GET['start_date']);
	$strt = date('d-m-Y',strtotime($start_date));
	$start_date = str_replace("-","",$_GET['start_date']);
	$end_date = str_replace("-","",$_GET['end_date']);
	$endt = date('d-m-Y',strtotime($end_date));
	$end_date = str_replace("-","",$_GET['end_date']);
	$emp_code = $_REQUEST['emp_code'];
	$prod_code = $_REQUEST['product_code'];
	$prod_name = $_REQUEST['product_name'];
	
	$condition = "AND (substring(SH.sauda_no,8,8) BETWEEN ".$start_date." AND ".$end_date.")";
	$value = 3;
}

if($prod_code == '')
	$product_compare_condition = '';
else 
	$product_compare_condition = "AND PM.$field_name1='$prod_code'";
	
$sql_select_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code='$emp_code'";
$res_select_emp_name = mysqli_query($link,$sql_select_emp_name);
$row_select_emp_name = mysqli_fetch_assoc($res_select_emp_name);
$emp_name = $row_select_emp_name['emp_name'];
?>

<?php
$count = 1;

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
	
	
$sql_customer_booked = "SELECT CM.customer_code, CM.customer_name, BM.branch_name as depot, (sum(PM.conversion_factor*SD.qty)/PM.conversion_factor_two) as mt_booked, LO.latt, LO.longi FROM customer_master CM, sauda_header SH, branch_master BM, sauda_details SD, location LO, product_master PM WHERE substring(SH.sauda_no,3,5)='$emp_code' $condition AND SH.customer_code = CM.customer_code AND SH.branch_code=BM.branch_code AND SH.sauda_no=LO.trans_id AND SH.sauda_no=SD.sauda_no AND SD.sku_code=PM.prod_code  $product_compare_condition GROUP BY CM.customer_code";
$res_customer_booked = mysqli_query($link,$sql_customer_booked);
$total_rows = mysqli_num_rows($res_customer_booked);

if($total_rows>0)
{
?>
<table width="100%" border="1" class="border" style="border-collapse:collapse;">
  <tr>
  	<td align="center" colspan="5" class="TDHEAD"><?php if ($prod_code == '') echo $emp_name; else echo $prod_name;?></td>
  </tr>
  <tr class="TDHEAD_SUB">
    <td>Serial</td>
    <td>Customer Name</td>
    <td>Depot</td>
    <td>Booked</td>
    <td>Locate</td>
  </tr>
<?php
$res_customer_booked = mysqli_query($link,$sql_customer_booked);
while($row_customer_booked = mysqli_fetch_assoc($res_customer_booked))
{
	echo "<tr>
	<td>$count</td>
    <td><a href=\"#\" style=\"color:blue;\" onclick=\"show_customer('$row_customer_booked[customer_name]','$row_customer_booked[customer_code]','$emp_code','$prod_code','$value','$start_date','$end_date');\">$row_customer_booked[customer_name]</td>
    <td>$row_customer_booked[depot]</td>
    <td align=\"right\">".number_format($row_customer_booked['mt_booked'],3)."</td>
    <td><a href=\"#\" id=\"locate$count\" style=\"color:blue;\"  onclick=\"window.open('customer_locate.php?get_latt=$row_customer_booked[latt]&get_longi=$row_customer_booked[longi]&cust_name=$row_customer_booked[customer_name]','locate','width=600,height=400'); \">Locate</a></td>
  </tr>";
  
  $total_booked += $row_customer_booked['mt_booked'];
  $count++;
}
?>
<tr style="font-weight:bold;">
	<td>Total</td>
    <td></td>
    <td></td>
    <td align="right"><?php echo number_format($total_booked,3); ?></td>
    <td></td>
</tr>
</table>
<?php
}
else
echo "<strong><font color=\"Red\">No records found</font></strong>";

mysqli_close($link);
?>
