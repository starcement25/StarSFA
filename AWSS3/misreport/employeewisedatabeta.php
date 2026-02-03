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
	$condition = "AND (SUBSTRING(SAL.allocation_date,1,10) BETWEEN '".$_GET['start_date']." "."12:00:01' AND '".$_GET['end_date']." "."23:59:59')";
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

	
$sql_count_product = "SELECT distinct($group_name) FROM $sauda_table_value $acronym ORDER BY $group_name ASC";
$res_count_product = mysql_query($sql_count_product);
$total_product = mysql_num_rows($res_count_product);
?>

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


$count = 1;
if($_SESSION['admin_login']=="admin")
{
	$emp_condition = 1;
}
else
{
	$emp_condition = "reporting_to IN(".$emp_hierarchy.")";
}

$sql_emp = "SELECT EM.emp_name, EM.emp_code FROM employee_master EM, sauda_details SD WHERE ".$emp_condition." 
          AND substring(SD.sauda_no,3,5)=EM.emp_code ".$sauda_booked_condition." GROUP BY EM.emp_code";
$res_emp = mysql_query($sql_emp);
$total_rows = mysql_num_rows($res_emp);
if($total_rows>0)
{
?>
<table width="100%" border="1" style="border-collapse:collapse;">
  <tr>
  	<td colspan="<?php echo (($total_product*1)+2); ?>" class="TDHEAD" align="center"><b>Employeewise <?php echo $sauda_duration; ?></b></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="center" style="width:2%;"><b>SI</b></td>
    <td align="center" style="width:14%;"><b>Employee Name</b></td>
    <?php
	$res_count_product = mysql_query($sql_count_product);
	while($row_count_product = mysql_fetch_array($res_count_product))
		echo "<td align=\"center\" style=\"width:16%;\"><b>$row_count_product[$field_name2]</b></td>";
	?>
  </tr>
  <tr class="TDHEAD_SUB">
    <td style="width:2%;">&nbsp;</td>
    <td style="width:14%;">&nbsp;</td>
    <?php
	for($i=1;$i<=$total_product;$i++)
		echo "<td style=\"width:8%;\" align=\"center\"><b>Booked</b></td>";
	?>
  </tr>
<?php
$res_emp = mysql_query($sql_emp);
while($row_emp = mysql_fetch_array($res_emp))
{
  echo "<tr>
			<td style=\"width:2%;\">$count</td>
			<td style=\"width:14%;\">$row_emp[emp_name]</td>";
  $sql_product = "SELECT $field_name1, $field_name2 FROM $sauda_table_value ORDER BY $field_name2";
  $res_product = mysql_query($sql_product);
  while($row_product = mysql_fetch_array($res_product))
  {
	   /*$sql_sauda_details = "SELECT EM.emp_code, EM.emp_name, $group_name, $group_code, SAL.qty FROM employee_master EM, sauda_allocation_log SAL, $sauda_table_value $acronym WHERE SAL.emp_code=EM.emp_code AND SAL.emp_code='$row_emp[emp_code]' AND SAL.product_filter_code=$group_code AND SAL.product_filter_code='$row_product[$field_name1]' $condition";
	   $res_sauda_details = mysql_query($sql_sauda_details);
	   $row_sauda_details = mysql_fetch_array($res_sauda_details);*/
	   $product_group_value = $row_product[$field_name1];
  
	   $sql_quantity_booked = "SELECT sum(SD.convert_qty_two) as mt_booked FROM sauda_details SD, product_master PM WHERE substring(SD.sauda_no,3,5)='$row_emp[emp_code]' $sauda_booked_condition AND SD.sku_code=PM.prod_code AND PM.$field_name1='$row_product[$field_name1]'";
	   $res_quantity_booked = mysql_query($sql_quantity_booked);
	   $row_quantity_booked = mysql_fetch_array($res_quantity_booked);
	   if($row_quantity_booked['mt_booked'] != '')
		$quantity_booked = $row_quantity_booked['mt_booked']; 
	   else
		$quantity_booked = 0;
  
	   if($quantity_booked != '')
	   echo "
			<td style=\"width:8%;\" align=\"right\"><a href=\"#\" style=\"color:blue;\" onclick=\"show_customer('$row_product[$field_name1]','$row_emp[emp_code]','$row_emp[emp_name]','$row_product[$field_name2]','$value','$start_date','$end_date');\">".$quantity_booked."</a></td>";
	   else
	   echo "
			<td style=\"width:8%;\" align=\"right\">".$quantity_booked."</td>";
			
	   $product_group_code[$product_group_value] += $row_quantity_booked['mt_booked'];
	   
	   
  }
    
  $count++;
}

echo "<tr style=\"font-weight:bold\">
		<td colspan=\"2\" align=\"center\">Total</td>
		
	  ";
foreach($product_group_code as $key=>$val)
echo "<td align=\"right\">$val</td>";

echo "</tr></table>";
}
else
{
	echo "<strong><font color=\"red\">No records found</font></strong>";
}
?>