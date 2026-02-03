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
	$sauda_booked_condition = "AND SUBSTRING(STL.sauda_no,-14,8) LIKE '".str_replace("-","",$today)."'";
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
	$sauda_booked_condition = "AND SUBSTRING(STL.sauda_no,8,4) =$year AND SUBSTRING(STL.sauda_no,12,2) =$month";
	$sauda_duration = "From : 01-".$month."-".$year." To ".date('d-m-Y');
	$value = 2;
}
else if($_GET['type'] == 'custom')
{
	$start_date = str_replace("-","",$_GET['start_date']);
	$strt = date('d-m-Y',strtotime($start_date));
	$end_date = str_replace("-","",$_GET['end_date']);
	$endt = date('d-m-Y',strtotime($end_date));
	$condition = " AND DATE_FORMAT(SAL.allocation_date,'%Y-%m-%d') BETWEEN '".$_GET['start_date']."' AND '".$_GET['end_date']."' ";
	$sauda_booked_condition = "AND (SUBSTRING(STL.sauda_no,-14,8) BETWEEN ".$start_date." AND ".$end_date.")";
	$sauda_duration = "From :".$strt." to ".$endt;
	$value = 3;
}
else
{
	$today = date('Y-m-d');
	$condition = "AND DATE_FORMAT(SUBSTRING(SAL.allocation_date,1,10),'%Y-%m-%d') LIKE '$today'";
	$sauda_booked_condition = "AND SUBSTRING(STL.sauda_no,-14,8) LIKE '".str_replace("-","",$today)."'";
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

if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login']=="supervisor" || $_SESSION['admin_login']=="system"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
		$emp_hierarchy_condition_one='';
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=' AND SAL.emp_code IN('.$emp_hierarchy.')';

		$emp_hierarchy_condition_one=' AND SUBSTRING(STL.sauda_no,3,5) IN ('.$emp_hierarchy.')';
		if(vertical_fields=='yes'){
			$sqlempvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$_SESSION['admin_login']."'";
			$rsempvertical=mysql_query($sqlempvertical);
			$rowempvertical=mysql_fetch_array($rsempvertical);
			$emp_vertical_value=$rowempvertical['vertical_value'];
			$emp_vertical_value_array=explode(',',$emp_vertical_value);
			//$emp_vertical_value = "'".implode("','", $emp_vertical_value_array)."'";
			$condition_one=" WHERE (";
			//$condition_three=" AND (";
			$condition_two='';
			foreach($emp_vertical_value_array as $emp_vertical_values)
			{
				$condition_two.=" FIND_IN_SET( '".$emp_vertical_values."',PGM.vertical_value) OR";
			}
			$condition_two=substr($condition_two,0,-2);
			$condition_one.=$condition_two.")";
			//$condition_three .= $condition_two.")";
			$emp_cond = ", employee_master EM $condition_one AND EM.emp_code = '".$_SESSION['admin_login']."' ";
		}
	}

$colorset = array('#FFE4B5','#EEEED1','#C1FFC1','#BBFFFF','#C6E2FF','#EEE0E5','#FFC1C1','#FFEBCD','#FFEC8B','#C1FFC1');
	
$sql_count_product = "SELECT $group_name, $group_code FROM $sauda_table_value $acronym".$emp_cond." ORDER BY $group_name ASC";
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


if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login']=="supervisor" || $_SESSION['admin_login']=="system")
{
	$emp_condition = "1 AND EM.acedns!='N'";
}
else
{
	$emp_condition = "EM.reporting_to IN(".$emp_hierarchy.") AND EM.acedns!='N'";
}

$sql_emp = "SELECT EM.emp_name, EM.emp_code FROM employee_master EM, sauda_transaction_log STL WHERE ".$emp_condition." AND SUBSTRING(STL.sauda_no,3,5)=EM.emp_code ".$sauda_booked_condition." GROUP BY EM.emp_code";
$res_emp = mysql_query($sql_emp);
$total_rows = mysql_num_rows($res_emp);
if($total_rows>0)
{
?>
<table width="100%" border="1" style="border-collapse:collapse;" cellpadding="5px">
  <tr>
  	<td colspan="<?php echo (($total_product*2)+2); ?>" class="TDHEAD" align="center"><b>Zonewise <?php echo $sauda_duration; ?></b></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="center"><b>SI</b></td>
    <td align="center"><b>Zone</b></td>
    <?php
	$res_count_product = mysql_query($sql_count_product);
	while($row_count_product = mysql_fetch_array($res_count_product))
		echo "<td align=\"center\" colspan=\"2\"><b>$row_count_product[$field_name2]</b></td>";
	?>
  </tr>
  <tr class="TDHEAD_SUB">
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <?php
	for($i=1;$i<=$total_product;$i++)
	{
		echo "<td align=\"center\"><b>MT</b></td>";
		echo "<td align=\"center\"><b>Case</b></td>";
	}
	?>
  </tr>
<?php
$count = 1;
$res_emp = mysql_query($sql_emp);
while($row_emp = mysql_fetch_array($res_emp))
{
  $sql_zone = "SELECT EM.zone as zone_name FROM employee_master EM, sauda_transaction_log STL WHERE SUBSTRING(STL.sauda_no,3,5)='$row_emp[emp_code]' ".$sauda_booked_condition." AND EM.emp_code=SUBSTRING(STL.sauda_no,3,5) GROUP BY zone_name ORDER BY zone_name ASC";
  $res_zone = mysql_query($sql_zone);
  while($row_zone = mysql_fetch_array($res_zone))	
  {
	  $zone_name = $row_zone['zone_name'];
	  	  				
	  $sql_product = "SELECT $group_name, $group_code FROM $sauda_table_value $acronym".$emp_cond." ORDER BY $group_name ASC";
	  $res_product = mysql_query($sql_product);
	  while($row_product = mysql_fetch_array($res_product))
	  {
		   $product_group_value = $row_product[$field_name1];
	  
		   $sql_quantity_booked = "SELECT SUM(STL.convert_qty_two) as mt_booked, SUM(STL.qty) as case_booked FROM sauda_transaction_log STL, product_master PM, employee_master EM WHERE SUBSTRING(STL.sauda_no,3,5)='".$row_emp['emp_code']."' AND EM.emp_code=SUBSTRING(STL.sauda_no,3,5) AND STL.prod_code=PM.prod_code AND PM.$field_name1='$row_product[$field_name1]' AND EM.zone LIKE '%".$zone_name."%' ".$sauda_booked_condition;
		   $res_quantity_booked = mysql_query($sql_quantity_booked);
		   $row_quantity_booked = mysql_fetch_array($res_quantity_booked);
		   
		   if($row_quantity_booked['mt_booked'] != '')
			$quantity_booked = $row_quantity_booked['mt_booked']; 
		   else
			$quantity_booked = 0;
			
		   if($row_quantity_booked['case_booked'] != '')
			$quantity_booked_case = $row_quantity_booked['case_booked']; 
		   else
			$quantity_booked_case = 0;
			
			$zonewise[$zone_name][$product_group_value] += $quantity_booked;
			$zonewise_case_booked[$zone_name][$product_group_value] += $quantity_booked_case;
			$case_booked[$product_group_value] += $quantity_booked_case;
	  		$product_group_code[$product_group_value] += $row_quantity_booked['mt_booked'];
			$product_group_name[$product_group_value] = $row_product[$field_name2];
	  }
	  $count++;
  }
}


$count=1;
foreach($zonewise as $branchwiseindex=>$branchwisevalue)
{
	$branch_code = explode("^", $branchwiseindex);
	echo "<tr>
			<td>$count</td>
			<td style=\"font-weight:bold;\">".$branchwiseindex."</td>";
	foreach($branchwisevalue as $index=>$val)
	{
		$color = $group_color_code[$index];
		if($val != '')
		{
		   echo "
				<td align=\"right\" style=\"background:$color; color:blue; cursor:pointer; font-weight:bold;\" onclick=\"show_zonewise_data('$branchwiseindex','$start_date','$end_date','$value','$index');\">".number_format($val,3)."</td>";
		   echo "<td align=\"right\" style=\"background:$color;\" >".$zonewise_case_booked[$branchwiseindex][$index]."</td>";
		}
		else
		{
		   echo "
				<td align=\"right\" style=\"background:$color;\">".number_format($val,3)."</td>";
		   echo "<td style=\"background:$color;\"></td>";
		}
	}
	echo "</tr>";
	$count++;
}

echo "<tr style=\"font-weight:bold\">
		<td colspan=\"2\" align=\"center\">Total</td>
	  ";
foreach($product_group_code as $key=>$val)
{
	$color = $group_color_code[$key];
	echo "<td align=\"right\" style=\"background:$color;\">".number_format($val,3)."</td>
		  <td align=\"right\" style=\"background:$color;\">".$case_booked[$key]."</td>";
}

echo "</tr></table>";
}
else
{
	echo "<strong><font color=\"red\">No records found</font></strong>";
}
mysql_close($link);
?>