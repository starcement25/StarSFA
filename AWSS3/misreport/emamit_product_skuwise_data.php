<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	error_reporting(0);

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
	$condition = "AND DATE_FORMAT(SUBSTRING(SAL.allocation_date,1,10),'%Y-%m-%d') LIKE '%$today%'";
	$sauda_booked_condition = "AND SUBSTRING(STL.sauda_no,-14,8) LIKE '".str_replace("-","",$today)."'";
	$sauda_duration = date('d-m-Y');
	$value = 1;
}

if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login']=="supervisor" || $_SESSION['admin_login']=="system")
{
	$res_get_emp_code = mysql_query("SELECT emp_code FROM employee_master WHERE reporting_to = '' AND acedns!='N'");
	$row_get_emp_code = mysql_fetch_array($res_get_emp_code);
	$get_emp_code = $row_get_emp_code['emp_code'];
	
	$emp_hierarchy=return_employee_hierarchy($get_emp_code);
	$emp_hierarchy_condition="WHERE EM.reporting_to IN(".$emp_hierarchy.") AND EM.acedns!='N' ";
	$emp_hierarchy_condition_one="WHERE EM.emp_code IN(SELECT emp_code FROM employee_master WHERE reporting_to=(SELECT emp_code FROM employee_master WHERE reporting_to='' AND acedns!='N') AND acedns!='N') AND EM.acedns!='N' ";
}
else
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition="WHERE EM.reporting_to IN(".$emp_hierarchy.") AND EM.acedns!='N' ";
	$emp_hierarchy_condition_one="WHERE EM.reporting_to IN('".$_SESSION['admin_login']."') AND EM.acedns!='N' ";
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

$sql_count_product = "SELECT $group_name, $group_code FROM $sauda_table_value $acronym".$emp_cond." ORDER BY $group_name ASC";
$res_count_product = mysql_query($sql_count_product);
while($row_count_product = mysql_fetch_array($res_count_product))
{
	$product_name = $row_count_product[$field_name2];
	
	$sql_emp = "SELECT EM.emp_code FROM employee_master EM ".$emp_hierarchy_condition_one;
	$res_emp = mysql_query($sql_emp);
	while($row_emp = mysql_fetch_array($res_emp))
	{
		$sql_sauda_allocation = "SELECT sum(SAL.qty) as alloted FROM sauda_allocation_log SAL WHERE SAL.emp_code='$row_emp[emp_code]' AND SAL.product_filter_code='$row_count_product[$field_name1]' ".$condition;
		$res_sauda_allocation = mysql_query($sql_sauda_allocation);
		$row_sauda_allocation = mysql_fetch_array($res_sauda_allocation);
		${$row_count_product[$field_name2].allocation} += $row_sauda_allocation['alloted'];
		$checkallocation += $row_sauda_allocation['alloted'];
	}
			
	$product_group[$product_name] = ${$row_count_product[$field_name2].allocation};
	$product_code[$product_name] = $row_count_product[$field_name1];
	
	$sql_emp = "SELECT EM.emp_code FROM employee_master EM ". $emp_hierarchy_condition;
	$res_emp = mysql_query($sql_emp);
	while($row_emp = mysql_fetch_array($res_emp))
	{
		$sql_products = "SELECT PM.prod_desc, SUM(STL.convert_qty_two) as mt_booked, SUM(STL.qty) as case_booked FROM sauda_transaction_log STL, product_master PM WHERE STL.prod_code=PM.prod_code AND PM.product_group_code='$row_count_product[$field_name1]' AND SUBSTRING(STL.sauda_no,3,5)='$row_emp[emp_code]' ".$sauda_booked_condition." GROUP BY STL.prod_code" ;
		$res_product = mysql_query($sql_products);
		while($row_product = mysql_fetch_array($res_product))
		{
			$prod_desc = $row_product['prod_desc'];
			${$prod_desc.booked} += $row_product['mt_booked'];
			${$prod_desc.casebooked} += $row_product['case_booked'];
			
			$product_booked[$product_name][$prod_desc] = ${$prod_desc.booked} ;
			$product_booked_case[$product_name][$prod_desc] = ${$prod_desc.casebooked};
		}
	}
}

if($checkallocation!='')
{
?>

<table border="1" style="border-collapse:collapse;" width="60%" class="border">

<?php
foreach($product_group as $productgroupindex=>$productgroupvalue)
{
	//$color = substr(md5(rand()), 0, 6);
	$color = array('#FFE4B5','#EEEED1','#C1FFC1','#BBFFFF','#C6E2FF','#EEE0E5','#FFC1C1','#FFEBCD','#FFEC8B','#C1FFC1');
	
	echo "<tr>
			<td colspan=\"3\" style=\"background:".$color[rand(0,10)]."; font-weight:bold;\" align=\"center\">".$productgroupindex."<div style=\"float:right;\" align=\"right\"> Alloted:".$productgroupvalue."</span></td>
		  </tr>
		  <tr class=\"TDHEAD_SUB\" align=\"center\">
		  	<td rowspan=\"2\">Product Name</td>
			<td colspan=\"2\">Booked</td>
		  </tr>
		  <tr class=\"TDHEAD_SUB\" align=\"center\">
		  	<td>MT</td>
			<td>Case</td>
		  </tr>";
	
	foreach($product_booked as $productbookedindex=>$productbookedvalue)
	{
		$total_booked_quantity = 0;
		$total_booked_quantity_case = 0;
		if($productbookedindex == $productgroupindex)
		{
			foreach($productbookedvalue as $index=>$value)
			{
			echo "<tr>
					<td>".$index."</td>
					<td align=\"right\">".$value."</td>
					<td align=\"right\">".$product_booked_case[$productbookedindex][$index]."</td>
				  </tr>";
			$total_booked_quantity += $value;
			$total_booked_quantity_case += $product_booked_case[$productbookedindex][$index];
			$total_booked_grand_total += $value;
			$total_booked_case_grand_total += $product_booked_case[$productbookedindex][$index];
			}
			echo "<tr style=\"font-weight:bold; background:#CAE1FF;\">
					<td align=\"center\">Total</td>
					<td align=\"right\">".number_format($total_booked_quantity,3)."</td>
					<td align=\"right\">".$total_booked_quantity_case."</td>
				  </tr>";
		}
	}
}

echo "<tr style=\"font-weight:bold; background:#CFCFCF;\">
					<td align=\"center\">Grand Total</td>
					<td align=\"right\">".number_format($total_booked_grand_total,3)."</td>
					<td align=\"right\">".$total_booked_case_grand_total."</td>
				  </tr>";
?>
</table>
<?php
}
else
{
	echo "<strong><font color=\"red\">No records found</font></strong>";
}
mysql_close($link);
?>