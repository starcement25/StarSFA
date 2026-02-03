<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

$get_emp_code = $_GET['emp_code'];
$res_emp_name = mysql_query("SELECT emp_name FROM employee_master WHERE emp_code = '".$get_emp_code."'");
$row_emp_name = mysql_fetch_array($res_emp_name);
$emp_name = $row_emp_name['emp_name'];

if($_REQUEST['condition_value'] == 1){
	$today = date('Y-m-d');
	$today1 = str_replace("-","",$today);
	$condition = "AND DATE_FORMAT(SUBSTRING(SAL.allocation_date,1,10),'%Y-%m-%d') LIKE '%$today%'";
	$sauda_booked_condition = "AND substring(SD.sauda_no,-14,8) LIKE '".str_replace("-","",$today)."'";
	$sauda_duration = date('d-m-Y');
	$value = 1;
}
else if($_REQUEST['condition_value'] == 2){
	@$current_date = date('Y-m-d');
	$month = explode("-",$current_date);
	$year = $month[0];
	$month = $month[1];
	$condition = "AND YEAR(SUBSTRING(SAL.allocation_date,1,10)) =". $year." AND MONTH(SUBSTRING(SAL.allocation_date,1,10)) =" .$month;
	$sauda_booked_condition = "AND substring(SD.sauda_no,8,4) =$year AND substring(SD.sauda_no,12,2) =$month";
	$sauda_duration = "From : 01-".$month."-".$year." To ".date('d-m-Y');
	$value = 2;
}
else if($_REQUEST['condition_value'] == 3){
	$start_date = str_replace("-","",$_GET['start_date']);
	$strt = date('d-m-Y',strtotime($start_date));
	$end_date = str_replace("-","",$_GET['end_date']);
	$endt = date('d-m-Y',strtotime($end_date));
	$condition = " AND DATE_FORMAT(SAL.allocation_date,'%Y-%m-%d') BETWEEN '".$_GET['start_date']."' AND '".$_GET['end_date']."'";
	$sauda_booked_condition = "AND (substring(SD.sauda_no,-14,8) BETWEEN ".$start_date." AND ".$end_date.")";
	$sauda_duration = "From: ".$strt." to ".$endt;
	$value = 3;
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

/*if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login']=="supervisor" || $_SESSION['admin_login']=="system"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
		$emp_hierarchy_condition_one='';
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=' AND SAL.emp_code IN('.$emp_hierarchy.')';
		$emp_hierarchy_condition_one=' AND substring(SD.sauda_no,3,5) IN ('.$emp_hierarchy.')';
	}*/


$count = 1;
/*if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login']=="supervisor" || $_SESSION['admin_login']=="system")
{
	$emp_condition = "1 AND EM.acedns!='N'";
}
else
{
	$emp_condition = "EM.emp_code IN(".$emp_hierarchy.") AND EM.acedns!='N'";
}*/

/*$sql_emp = "SELECT EM.emp_name, EM.emp_code FROM employee_master EM, sauda_details SD WHERE ".$emp_condition." AND substring(SD.sauda_no,3,5)=EM.emp_code ".$sauda_booked_condition." WHERE EM.emp_code = '".."'";
$res_emp = mysql_query($sql_emp);
$total_rows = mysql_num_rows($res_emp);
if($total_rows>0)
{*/
?>
<table width="100%" border="1" style="border-collapse:collapse;">
  <tr>
  	<td colspan="<?php echo (($total_product*2)+3); ?>" class="TDHEAD" align="center">Employee: <?php echo $emp_name."<br>";
	 ?><b>Customerwise <?php echo $sauda_duration; ?></b></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="center"><b>SI</b></td>
    <td align="center"><b>Customer Name</b></td>
    <?php
	$res_count_product = mysql_query($sql_count_product);
	while($row_count_product = mysql_fetch_array($res_count_product))
		echo "<td align=\"center\" colspan=\"2\"><b>$row_count_product[$field_name2]</b></td>";
	?>
    <td align="center"><b>Locate</b></td>
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
    <td>&nbsp;</td>
  </tr>
<?php
//$res_emp = mysql_query($sql_emp);
//while($row_emp = mysql_fetch_array($res_emp))
//{
  $sql_customer = "SELECT CM.customer_code, CM.customer_name, LO.latt, LO.longi, date_format(substring(LO.date,1,10),'%d-%m-%Y') as locate_date, substring(LO.date,12,8) as locate_time FROM customer_master CM, sauda_details SD, sauda_header SH, location LO WHERE SD.sauda_no=SH.sauda_no AND CM.customer_code=SH.customer_code AND substring(SD.sauda_no,3,5)='".$get_emp_code."' AND SH.sauda_no = LO.trans_id ".$sauda_booked_condition." GROUP BY SH.customer_code";
  $res_customer = mysql_query($sql_customer);
  while($row_customer = mysql_fetch_array($res_customer))	
  {
	  $customer_code = $row_customer['customer_code'];
	  $customer_name = $row_customer['customer_name'];
	  $customer_latt = $row_customer['latt'];
	  $customer_longi = $row_customer['longi'];
	  $customer_date = $row_customer['locate_date'];
	  $customer_time = $row_customer['locate_time'];
	  				
	  $sql_product = "SELECT $field_name1, $field_name2 FROM $sauda_table_value ORDER BY $field_name2";
	  $res_product = mysql_query($sql_product);
	  while($row_product = mysql_fetch_array($res_product)){
		   $product_group_value = $row_product[$field_name1];
	  
		   $sql_quantity_booked = "SELECT sum(SD.convert_qty_two) as mt_booked, sum(SD.qty) as case_booked FROM sauda_details SD, product_master PM, sauda_header SH WHERE SD.sauda_no=SH.sauda_no AND SH.customer_code='".$row_customer['customer_code']."' AND substring(SD.sauda_no,3,5)='".$get_emp_code."' AND SD.sku_code=PM.prod_code AND PM.$field_name1='".$row_product[$field_name1]."' ".$sauda_booked_condition;
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
	  
		   $product_group_code[$product_group_value] += $row_quantity_booked['mt_booked'];
		   
		   $customerwise_booked_mt[$customer_name."^".$customer_code][$product_group_value] += $quantity_booked;
		   $customerwise_booked_case[$customer_name."^".$customer_code][$product_group_value] += $quantity_booked_case;
		   $customerwise_latitude[$customer_name."^".$customer_code] = $customer_latt;
		   $customerwise_longitude[$customer_name."^".$customer_code] = $customer_longi;
		   $customerwise_date[$customer_name."^".$customer_code] = $customer_date;
		   $customerwise_time[$customer_name."^".$customer_code] = $customer_time;
		   $case_booked[$product_group_value] += $quantity_booked_case;
	  }
	  $count++;
  }

$count = 1;
/*foreach($customerwise_booked_mt as $customerwiseindex=>$customerwiseval){
	$customer_code_name = explode("^", $customerwiseindex);
	echo "<tr>
			<td>$count</td>
			<td><a href=\"#\" style=\"color:blue;\" onclick=\"show_customerdata_datewise('$customer_code_name[1]', '$customer_code_name[0]', '$value', '$start_date', '$end_date');\">".strtok($customerwiseindex,"^")."</a></td>";
	$customer_name_locate = strtok($customerwiseindex,"^");
	foreach($customerwiseval as $index=>$val){
		$color = $group_color_code[$index];
		if($val != ''){
			echo "<td align=\"right\" style=\"background:$color;\">".$val."</td>
			  	  <td align=\"right\" style=\"background:$color;\">".$customerwise_booked_case[$customerwiseindex][$index]."</td>";
		}
		else{
			echo "<td align=\"right\" style=\"background:$color;\"></td>
			  	  <td align=\"right\" style=\"background:$color;\"></td>";
		}
	}
	echo "<td><a href=\"sauda_locate.php?get_latt=$customerwise_latitude[$customerwiseindex]&get_longi=$customerwise_longitude[$customerwiseindex]&cust_name=$customer_name_locate&locate_date=$customerwise_date[$customerwiseindex]&locate_time=$customerwise_time[$customerwiseindex]\" style=\"color:blue;\">Locate</a></td>";
	$count++;
}*/
	foreach($customerwise_booked_mt as $customerwiseindex=>$customerwiseval){
		$customer_code_name = explode("^", $customerwiseindex);
		$sql_check_emp_customer = "SELECT customer_code FROM sauda_header WHERE SUBSTRING(sauda_no,3,5) = '".$emp_code."' AND customer_code = '".$customer_code_name[1]."' AND (SUBSTRING(sauda_no,-14,8) BETWEEN ".$start_date." AND ".$end_date.")";
		$res_check_emp_customer = mysql_query($sql_check_emp_customer);
		$total_rows = mysql_num_rows($res_check_emp_customer);
		if($total_rows>0){
			echo "<tr>
					<td>$count</td>
					<td><a href=\"#\" style=\"color:blue;\" onclick=\"show_customerdata_datewise('$customer_code_name[1]', '$customer_code_name[0]', '$get_emp_code', '$value', '$start_date', '$end_date');\">".strtok($customerwiseindex,"^")."</a></td>";
			$customer_name_locate = strtok($customerwiseindex,"^");
			foreach($customerwiseval as $index=>$val){
				$color = $group_color_code[$index];
				if($val != ''){
					echo "<td align=\"right\" style=\"background:$color;\">".$val."</td>
						  <td align=\"right\" style=\"background:$color;\">".$customerwise_booked_case[$customerwiseindex][$index]."</td>";
				}
				else{
					echo "<td align=\"right\" style=\"background:$color;\"></td>
						  <td align=\"right\" style=\"background:$color;\"></td>";
				}
			}
			echo "<td><a href=\"sauda_locate.php?get_latt=$customerwise_latitude[$customerwiseindex]&get_longi=$customerwise_longitude[$customerwiseindex]&cust_name=$customer_name_locate&locate_date=$customerwise_date[$customerwiseindex]&locate_time=$customerwise_time[$customerwiseindex]\" style=\"color:blue;\">Locate</a></td>";
			$count++;
		}
	}
echo "<tr style=\"font-weight:bold\">
		<td colspan=\"2\" align=\"center\">Total</td>
	  ";
	  
foreach($product_group_code as $key=>$val){
	$color = $group_color_code[$key];
	echo "<td align=\"right\" style=\"background:$color;\">".number_format($val,3)."</td>";
	echo "<td align=\"right\" style=\"background:$color;\">$case_booked[$key]</td>";
}
	echo "<td>&nbsp;</td>";
	echo "</tr></table>";
/*}
else
{
	echo "<strong><font color=\"red\">No records found</font></strong>";
}*/
?>