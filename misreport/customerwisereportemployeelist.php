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
	$condition = " AND DATE_FORMAT(SAL.allocation_date,'%Y-%m-%d') BETWEEN '".$_GET['start_date']."' AND '".$_GET['end_date']."'";
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

$colorset = array('#FFE4B5','#EEEED1','#C1FFC1','#BBFFFF','#C6E2FF','#EEE0E5','#FFC1C1','#FFEBCD','#FFEC8B','#C1FFC1');

$sql_count_product = "SELECT $group_name, $group_code FROM $sauda_table_value $acronym ORDER BY $group_name ASC";
$res_count_product = mysqli_query($link,$sql_count_product);
$total_product = mysqli_num_rows($res_count_product);

$count = 1;
$res_count_product = mysqli_query($link,$sql_count_product);
while($row_count_product = mysqli_fetch_assoc($res_count_product))
{
	$pgcode = $row_count_product[$field_name1];
	$group_color_code[$pgcode] = $colorset[$count];
	$count++;
}
?>

<?php

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


$count = 1;
if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login']=="supervisor" || $_SESSION['admin_login']=="system")
{
	$emp_condition = "1 AND EM.acedns!='N'";
}
else
{
	$emp_condition = "EM.emp_code IN(".$emp_hierarchy.") AND EM.acedns!='N'";
}

$sql_emp = "SELECT EM.emp_name, EM.emp_code FROM employee_master EM, sauda_details SD WHERE ".$emp_condition." AND substring(SD.sauda_no,3,5)=EM.emp_code ".$sauda_booked_condition." GROUP BY EM.emp_code ORDER BY EM.emp_name ASC";
$res_emp = mysqli_query($link,$sql_emp);
$total_rows = mysqli_num_rows($res_emp);
if($total_rows>0)
{
?>
<table width="100%" border="1" style="border-collapse:collapse;">
  <tr>
  	<td colspan="<?php echo (($total_product*2)+2); ?>" class="TDHEAD" align="center"><b>Customerwise <?php echo $sauda_duration; ?></b></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="center"><b>SI</b></td>
    <td align="center"><b>Employee Name</b></td>
    <?php
	$res_count_product = mysqli_query($link,$sql_count_product);
	while($row_count_product = mysqli_fetch_assoc($res_count_product))
		echo "<td align=\"center\" colspan=\"2\"><b>$row_count_product[$field_name2]</b></td>";
	?>
  </tr>
  <tr class="TDHEAD_SUB">
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <?php
	for($i=1;$i<=$total_product;$i++)
	{
		echo "<td align=\"center\"><b>MT</b></td>
			  <td align=\"center\"><b>Case</b></td>";
	}
	?>
  </tr>
<?php
$res_emp = mysqli_query($link,$sql_emp);
while($row_emp = mysqli_fetch_assoc($res_emp))
{
  $empl_code = $row_emp['emp_code'];
  $sql_customer = "SELECT CM.customer_code, CM.customer_name FROM customer_master CM, sauda_details SD, sauda_header SH WHERE SD.sauda_no=SH.sauda_no AND CM.customer_code=SH.customer_code AND substring(SD.sauda_no,3,5)='".$empl_code."' ".$sauda_booked_condition." GROUP BY SH.customer_code";
  $res_customer = mysqli_query($link,$sql_customer);
  while($row_customer = mysqli_fetch_assoc($res_customer))	
  {
	  $customer_code = $row_customer['customer_code'];
	  $customer_name = $row_customer['customer_name'];
	  	  				
	  $sql_product = "SELECT $field_name1, $field_name2 FROM $sauda_table_value ORDER BY $field_name2";
	  $res_product = mysqli_query($link,$sql_product);
	  while($row_product = mysqli_fetch_assoc($res_product)){
		   $product_group_value = $row_product[$field_name1];
	  
		   $sql_quantity_booked = "SELECT sum(SD.convert_qty_two) as mt_booked, sum(SD.qty) as case_booked FROM sauda_details SD, product_master PM, sauda_header SH WHERE SD.sauda_no=SH.sauda_no AND SH.customer_code='".$row_customer['customer_code']."' AND substring(SD.sauda_no,3,5)='".$empl_code."' AND SD.sku_code=PM.prod_code AND PM.$field_name1='$row_product[$field_name1]' ".$sauda_booked_condition;
		   $res_quantity_booked = mysqli_query($link,$sql_quantity_booked);
		   $row_quantity_booked = mysqli_fetch_assoc($res_quantity_booked);
		   
		   if($row_quantity_booked['mt_booked'] != '')
			$quantity_booked = $row_quantity_booked['mt_booked']; 
		   else
			$quantity_booked = 0;
			
		   if($row_quantity_booked['case_booked'] != '')
			$quantity_booked_case = $row_quantity_booked['case_booked']; 
		   else
			$quantity_booked_case = 0;	
	  
		   $product_group_code[$product_group_value] += $row_quantity_booked['mt_booked'];
		   
		   $empwise_booked_mt[$empl_code][$product_group_value] += $quantity_booked;
		   $empwise_booked_case[$empl_code][$product_group_value] += $quantity_booked_case;
		   
		   $case_booked[$product_group_value] += $quantity_booked_case;
	  }
	  $count++;
  }
}
/*echo "<pre>";
print_r($empwise_booked_mt);
echo "</pre>";*/
$count = 1;
foreach($empwise_booked_mt as $empwiseindex=>$empwiseval){
		$emp_code = $empwiseindex;
		$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_name = mysqli_query($link,$sql_emp_name);
		$row_emp_name = mysqli_fetch_assoc($res_emp_name);
		$emp_name = $row_emp_name['emp_name'];
		echo "<tr>
					<td>$count</td>
					<td><a href=\"#\" style=\"color:blue;\" onclick=\"show_empdata('$emp_code', '$value', '$start_date', '$end_date');\">".$emp_name."</a></td>";
			foreach($empwiseval as $index=>$val){
				$color = $group_color_code[$index];
				if($val != ''){
					echo "<td align=\"right\" style=\"background:$color;\">".$val."</td>
						  <td align=\"right\" style=\"background:$color;\">".$empwise_booked_case[$empwiseindex][$index]."</td>";
				}
				else{
					echo "<td align=\"right\" style=\"background:$color;\"></td>
						  <td align=\"right\" style=\"background:$color;\"></td>";
				}
			}
			$count++;
	}
echo "<tr style=\"font-weight:bold\">
		<td colspan=\"2\" align=\"center\">Total</td>
	  ";
	  
foreach($product_group_code as $key=>$val){
	$color = $group_color_code[$key];
	echo "<td align=\"right\" style=\"background:$color;\">".number_format($val,3)."</td>";
	echo "<td align=\"right\" style=\"background:$color;\">$case_booked[$key]</td>";
}
	echo "</tr></table>";
}
else
{
	echo "<strong><font color=\"red\">No records found</font></strong>";
}

mysqli_close($link);
?>