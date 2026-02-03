<?php
error_reporting(0);
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","acedns_EMAMI");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);
?>

<?php
if($_GET['type'] == 'today')
{
	$today = date('Y-m-d');
	$today = str_replace("-","",$today);
	$condition = "AND substring(SD.sauda_no,8,8) LIKE '$today'";
	$sauda_duration = date('d-m-Y');
	$value = 1;
}
else if($_GET['type'] == 'mtd')
{
	@$current_date = date('Y-m-d');
	$month = explode("-",$current_date);
	$year = $month[0];
	$month = $month[1];
	$condition = "AND substring(SD.sauda_no,8,4) =$year AND substring(SD.sauda_no,12,2) =$month";
	$sauda_duration = "From : 01-".$month."-".$year." To ".date('d-m-Y');
	$value = 2;
}
else
{
	$start_date = str_replace("-","",$_GET['start_date']);
	$strt = date('d-m-Y',strtotime($start_date));
	$end_date = str_replace("-","",$_GET['end_date']);
	$endt = date('d-m-Y',strtotime($end_date));
	$condition = "AND (substring(OD.order_no,7,8) BETWEEN ".$start_date." AND ".$end_date.")";
	$sauda_duration = "From :".$strt." to ".$endt;
	$value = 3;
}
?>

<?php
date_default_timezone_set("Asia/Kolkata"); 
$sauda_date = date('d-m-Y');
$today = date('Y-m-d');

$today = str_replace("-","",$today);

$sql_sauda_filter = "SELECT sauda_allocation_basedon_filter FROM acedns_acednsproduct.product_details WHERE nick_name='EMAMI'";
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

/*if($_GET)
	$condition = " WHERE emp_name LIKE '%$_GET[search_empl_name]%'";
else
	$condition = "";*/
	
$sql_count_product = "SELECT distinct($group_name) FROM employee_master EM, sauda_allocation SA, $sauda_table_value $acronym WHERE EM.emp_code=SA.emp_code AND SA.product_filter_code=$group_code ORDER BY $group_name ASC";
$res_count_product = mysqli_query($link,$sql_count_product);
$total_product = mysqli_num_rows($res_count_product);
?>

<table width="100%" border="1" style="border-collapse:collapse;">
  <tr>
  	<td colspan="<?php echo (($total_product*2)+2); ?>" class="TDHEAD" align="center"><b>Employeewise</b></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="center" style="width:2%;"><b>SI</b></td>
    <td align="center" style="width:14%;"><b>Employee Name</b></td>
    <?php
	$res_count_product = mysqli_query($link,$sql_count_product);
	while($row_count_product = mysqli_fetch_assoc($res_count_product))
		echo "<td align=\"center\" colspan=\"2\" style=\"width:16%;\"><b>$row_count_product[$field_name2]</b></td>";
	?>
  </tr>
  <tr class="TDHEAD_SUB">
    <td style="width:2%;">&nbsp;</td>
    <td style="width:14%;">&nbsp;</td>
    <?php
	for($i=1;$i<=$total_product;$i++)
		echo "<td style=\"width:8%;\"><b>Alloted</b></td>
    		  <td style=\"width:8%;\"><b>Booked</b></td>";
	?>
  </tr>
  
<?php

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


$emp_code = 'E0028';
$count = 1;
$sql_emp = "SELECT emp_name, emp_code FROM employee_master WHERE reporting_to = '$emp_code'";
$res_emp = mysqli_query($link,$sql_emp);
while($row_emp = mysqli_fetch_assoc($res_emp))
{
  echo "<tr>
			<td style=\"width:2%;\">$count</td>
			<td style=\"width:14%;\"><a href=\"#\" style=\"color:blue;\">$row_emp[emp_name]</a></td>";
  $sql_product = "SELECT $field_name1, $field_name2 FROM $sauda_table_value ORDER BY $field_name2";
  $res_product = mysqli_query($link,$sql_product);
  while($row_product = mysqli_fetch_assoc($res_product))
  {
	   $sql_sauda_details = "SELECT EM.emp_code, EM.emp_name, $group_name, $group_code, SA.emp_code, SA.product_filter_code, SA.qty FROM employee_master EM, sauda_allocation SA, $sauda_table_value $acronym WHERE EM.emp_code=SA.emp_code AND SA.product_filter_code=$group_code AND EM.emp_code LIKE '%$row_emp[emp_code]%' AND $group_name LIKE '%$row_product[$field_name2]%'";
	   $res_sauda_details = mysqli_query($link,$sql_sauda_details);
	   $row_sauda_details = mysqli_fetch_assoc($res_sauda_details);
  
	   $sql_quantity_booked = "SELECT sum(OD.qty) FROM order_details OD, product_master PM WHERE substring(OD.order_no,2,5)='$row_sauda_details[emp_code]' $condition AND OD.sku_code=PM.prod_code AND PM.$field_name1='$row_sauda_details[$field_name1]'";
	   $res_quantity_booked = mysqli_query($link,$sql_quantity_booked);
	   $row_quantity_booked = mysqli_fetch_assoc($res_quantity_booked);
	   if($row_quantity_booked['sum(OD.qty)'] != '')
		$quantity_booked = $row_quantity_booked['sum(OD.qty)'];
	   else
		$quantity_booked = 0;
  
	   echo "<td style=\"width:8%;\" align=\"right\" id=\"$row_emp[emp_code]_$row_product[$field_name2]\" >$row_sauda_details[qty]</td>
			<td style=\"width:8%;\" align=\"right\"><a href=\"#\" style=\"color:blue;\" onclick=\"show_customer_details('$row_product[$field_name1]','$row_emp[emp_code]','$row_product[$field_name2]','$value');\">$quantity_booked</a></td>";
	   
  }
    
  $count++;
}

echo "</table>";

mysqli_close($link);
?>