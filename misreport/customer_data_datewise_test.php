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

$colorset = array('#FFE4B5','#EEEED1','#C1FFC1','#BBFFFF','#C6E2FF','#EEE0E5','#FFC1C1','#FFEBCD','#FFEC8B','#C1FFC1');

$customer_code = $_REQUEST['customer_code'];
$customer_name = $_REQUEST['customer_name'];
$emp_code = $_REQUEST['emp_code'];

if($_REQUEST['condition_value'] == 1)
{
	$today = date('Y-m-d');
	$today1 = str_replace("-","",$today);
	$condition = "AND DATE_FORMAT(SUBSTRING(SAL.allocation_date,1,10),'%Y-%m-%d') LIKE '%$today%'";
	$sauda_booked_condition = "AND substring(SD.sauda_no,-14,8) LIKE '".str_replace("-","",$today)."'";
	$sauda_booked = " substring(sauda_no,-14,8) LIKE '".str_replace("-","",$today)."'";
	$sauda_duration = date('d-m-Y');
	$value = 1;
}
else if($_REQUEST['condition_value'] == 2)
{
	@$current_date = date('Y-m-d');
	$month = explode("-",$current_date);
	$year = $month[0];
	$month = $month[1];
	$condition = "AND YEAR(SUBSTRING(SAL.allocation_date,1,10)) =". $year." AND MONTH(SUBSTRING(SAL.allocation_date,1,10)) =" .$month;
	$sauda_booked_condition = "AND substring(SD.sauda_no,8,4) =$year AND substring(SD.sauda_no,12,2) =$month";
	$sauda_booked = " substring(sauda_no,8,4) =$year AND substring(sauda_no,12,2) =$month";
	$sauda_duration = "From : 01-".$month."-".$year." To ".date('d-m-Y');
	$value = 2;
}
else if($_REQUEST['condition_value'] == 3)
{
	$start_date = str_replace("-","",$_GET['start_date']);
	$strt = date('d-m-Y',strtotime($start_date));
	$end_date = str_replace("-","",$_GET['end_date']);
	$endt = date('d-m-Y',strtotime($end_date));
	$condition = "AND SAL.allocation_date BETWEEN '".$_GET['start_date']." "."12:00:01' AND '".$_GET['end_date']." "."23:59:59'";
	$sauda_booked_condition = "AND (substring(SD.sauda_no,-14,8) BETWEEN ".$start_date." AND ".$end_date.")";
	$sauda_booked = " (substring(sauda_no,-14,8) BETWEEN ".$start_date." AND ".$end_date.")";
	$sauda_duration = "From :".$strt." to ".$endt;
	$value = 3;
}


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

$colspan = ($total_product*2)+2;

/*if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login']=="supervisor"){
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


if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login']=="supervisor")
{
	$emp_condition = 1;
}
else
{
	$emp_condition = "emp_code IN(".$emp_hierarchy.")";
}*/

/*$sql_emp = "SELECT EM.emp_name, EM.emp_code FROM employee_master EM, sauda_details SD WHERE ".$emp_condition." AND substring(SD.sauda_no,3,5)=EM.emp_code ".$sauda_booked_condition." AND EM.emp_code = '".$emp_code."'";
$res_emp = mysqli_query($link,$sql_emp);
$total_rows = mysqli_num_rows($res_emp);

if($total_rows > 0)
{*/
?>
<table width="100%" border="1" style="border-collapse:collapse;">
  <tr>
  	<td colspan="<?php echo $colspan; ?>" class="TDHEAD" align="center"><b><?php echo $customer_name."<br>".$sauda_duration; ?></b></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="center"><b>SI</b></td>
    <td align="center"><b>Date</b></td>
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
		echo "<td align=\"center\"><b>MT</b></td>";
		echo "<td align=\"center\"><b>Case</b></td>";
	}
	?>
  </tr>
  
<?php
	$count = 1;
	/*$res_emp = mysqli_query($link,$sql_emp);
	while($row_emp = mysqli_fetch_assoc($res_emp))
	{*/
		$sql_select_date = "SELECT DATE_FORMAT(SUBSTRING(sauda_no,-14,8),'%d-%m-%Y') as sauda_date FROM sauda_details WHERE".$sauda_booked." GROUP BY sauda_date ORDER BY sauda_date DESC";
		$res_select_date = mysqli_query($link,$sql_select_date);
		while($row_select_date = mysqli_fetch_assoc($res_select_date))
		{
			$sauda_date = $row_select_date['sauda_date'];
		
			$sql_product = "SELECT $field_name1, $field_name2 FROM $sauda_table_value ORDER BY $field_name2";
			$res_product = mysqli_query($link,$sql_product);
			while($row_product = mysqli_fetch_assoc($res_product))
			{
				$product_group_value = $row_product[$field_name1];
				
				$sql_quantity_booked = "SELECT sum(SD.convert_qty_two) as mt_booked, sum(SD.qty) as case_booked FROM sauda_details SD, product_master PM, sauda_header SH WHERE SD.sauda_no=SH.sauda_no AND SH.customer_code='".$customer_code."' AND substring(SD.sauda_no,3,5)='".$emp_code."' AND SD.sku_code=PM.prod_code AND PM.$field_name1='".$row_product[$field_name1]."' AND DATE_FORMAT(SUBSTRING(SD.sauda_no,-14,8),'%d-%m-%Y') = '".$sauda_date."'";
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
			
				$total_booked_mt[$sauda_date][$product_group_value] += $quantity_booked;
				$total_booked_case[$sauda_date][$product_group_value] += $quantity_booked_case;
				
				$grand_total_booked[$product_group_value] += $quantity_booked;
				$grand_total_booked_case[$product_group_value] += $quantity_booked_case;
		
			}
			$count++;
		}
		
	//}
	$customer_name = $customer_name."^".$customer_code;
	$count = 1;
	
	foreach($total_booked_mt as $total_booked_mt_index=>$total_booked_mt_value)
	{
		$flag = 0;
		foreach($total_booked_mt_value as $key=>$value){
			if($value>0){
				$flag = 1;
				break;
			}
		}
		if($flag == 1){
		echo "<tr>
				<td>$count</td>
				<td align=\"center\" style=\"background:#D9D9D9; font-weight:bold; width:24%;\">".$total_booked_mt_index."</td>";
		foreach($total_booked_mt_value as $mt_booked_index=>$mt_booked_value)
		{
			$color = $group_color_code[$mt_booked_index];
			
			$sql_product = "SELECT $field_name2 FROM $sauda_table_value WHERE $field_name1 = '$mt_booked_index'";
			$res_product = mysqli_query($link,$sql_product);
			$row_product = mysqli_fetch_assoc($res_product);
			$product_name = $row_product[$field_name2];
			
			if($mt_booked_value != '')
			{
				echo "<td align=\"right\" style=\"background:$color;\"><a href=\"#\" style=\"color:blue;\" onclick=\"show_customerdata('$customer_name','$product_name','$value','$mt_booked_index','$total_booked_mt_index');\">".number_format($mt_booked_value,3)."</a></td>
					  <td align=\"right\" style=\"background:$color;\">".$total_booked_case[$total_booked_mt_index][$mt_booked_index]."</td>";
			}
			else
			{
				echo "<td style=\"background:$color;\"></td>
					  <td style=\"background:$color;\"></td>";
			}
		}
		echo "<tr>";
		$count++;
		}
	}
	echo "<tr style=\"background:#7D9EC0; font-weight:bold;\">
			<td colspan=\"2\" align=\"center\" >Grand Total</td>";
	foreach($grand_total_booked as $index=>$val)
	{
		$color = $group_color_code[$index];	
		echo "<td align=\"right\" style=\"background:$color;\">".number_format($val,3)."</td>
			  <td align=\"right\" style=\"background:$color;\">".$grand_total_booked_case[$index]."</td>";
		
	}
	echo "</tr></table>";
/*}
else
{
	echo "<strong><font color=\"red\">No records found</font></strong>";
}*/
?>