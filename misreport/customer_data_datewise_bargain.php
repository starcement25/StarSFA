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

if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login']=="supervisor"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
		$emp_hierarchy_condition_one='';
		$emp_cond='';
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=' AND SAL.emp_code IN('.$emp_hierarchy.')';
		$emp_hierarchy_condition_one=' AND substring(SD.sauda_no,3,5) IN ('.$emp_hierarchy.')';
		if(vertical_fields=='yes'){
			$sqlempvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$_SESSION['admin_login']."'";
			$rsempvertical=mysqli_query($link,$sqlempvertical);
			$rowempvertical=mysqli_fetch_assoc($rsempvertical);
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
?>
<div >
<table width="100%" class="border" border="1">
  <tr>
  	<td colspan="10" align="center" class="TDHEAD">Bargainwise: of "<?php echo $customer_name;?>" <?php echo $sauda_duration;?></td>
  </tr>
  <tr class="TDHEAD_SUB" align="center" >
  	<td width="5%">SI</td>
    <td width="20%">Product</td>
    <td width="6%">Booked</td>
    <td width="8%">Approved</td>
    <td width="6%">UOM</td>
    <td width="6%">Rate</td>
    <td width="6%">TD</td>
    <td width="12%">Amount</td>
    <td width="15%">Bargain Valid From</td>
    <td width="15%">Valid Upto</td>
  </tr>
<!--/table-->
</div>
<br />
<!--table width="100%" border="1" style="border-collapse:collapse;"-->
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
   $sql_sauda_wise = "SELECT DISTINCT SD.sauda_no, PM.prod_desc,PM.UOM1,round(sum(PM.conversion_factor_two*SD.qty),2 )as mt_booked, 
					SD.sku_code, SD.sale_rate,SD.freight_charge,
					SD.TD, SD.amount, SH.customer_code, SH.sauda_valid_from, CM.sauda_validity_period, 
					DATE_FORMAT(SUBSTRING(SD.sauda_no,-14,8),'%d-%m-%Y') as sauda_date,SD.qty FROM sauda_details SD, 
					sauda_header SH, product_master PM, customer_master CM 
					WHERE SH.sauda_no=SD.sauda_no $emp_hierarchy_condition_one $sauda_booked_condition 
					AND SD.sku_code=PM.prod_code AND SH.customer_code=CM.customer_code AND SH.customer_code='".$customer_code."'
					GROUP BY DATE_FORMAT(SUBSTRING(SD.sauda_no,-14,14),'%d-%m-%Y %H:%i:%s'),SD.sku_code 
					ORDER BY DATE_FORMAT(SUBSTRING(SD.sauda_no,-14,8),'%Y-%m-%d') DESC";
	$res_sauda_wise = mysqli_query($link,$sql_sauda_wise);
	$total_rows = mysqli_num_rows($res_sauda_wise);
	$count = 1;
	$sauda_date_array = array();
	$res_sauda_wise = mysqli_query($link,$sql_sauda_wise);
	while($row_sauda_wise = mysqli_fetch_assoc($res_sauda_wise))
	{
		$sauda_date = $row_sauda_wise['sauda_date'];
		$sale_rate=$row_sauda_wise['sale_rate']+$row_sauda_wise['freight_charge']+$row_sauda_wise['TD'];
		$sqlapprovedqty="SELECT qty from DO_master WHERE sauda_no='".$row_sauda_wise['sauda_no']."' AND sku_code='".$row_sauda_wise['sku_code']."'";
			$rsapprovedqty=mysqli_query($link,$sqlapprovedqty);
			$rowapprovedqty=mysqli_fetch_assoc($rsapprovedqty);
			$approved_qty=$rowapprovedqty['qty'];
		if(!in_array($sauda_date,$sauda_date_array))
		{
			array_push($sauda_date_array, $sauda_date);
			echo "<tr style=\"background:grey;\"><td colspan=\"9\" align=\"center\"><b>$sauda_date</b></td></tr>";
		}
		$sauda_valid_from = date('d-m-Y',strtotime($row_sauda_wise['sauda_valid_from']));
		$sauda_valid_days = $row_sauda_wise['sauda_validity_period'];
		$valid_upto = date('d-m-Y',strtotime($sauda_valid_from. '+'.$sauda_valid_days.' days'));
		echo "<tr>
	<td width=\"5%\">$count</td>
    <td width=\"20%\">$row_sauda_wise[prod_desc]</td>
    <td align=\"right\" width=\"6%\">$row_sauda_wise[qty]</td>
	 <td align=\"right\" width=\"8%\">$approved_qty</td>
	<td width=\"6%\">$row_sauda_wise[UOM1]</td>
    <td align=\"right\" width=\"6%\">".number_format(round($sale_rate,2),2)."</td>
    <td align=\"right\" width=\"6%\">$row_sauda_wise[TD]</td>
    <td align=\"right\" width=\"12%\">".number_format($row_sauda_wise['amount'],2)."</td>
    <td align=\"center\" width=\"15%\">$sauda_valid_from</td>
    <td align=\"center\" width=\"15%\">$valid_upto</td>
  </tr>";
  
  		$total_booked += $row_sauda_wise['qty'];
		$total_amount += $row_sauda_wise['amount'];
		$count++;
	}
	/*echo "<pre>";
	print_r($row_sauda_wise);
	echo "</pre>";*/
echo "<tr style=\"font-weight:bold;\">
	<td width=\"5%\">Total</td>
    <td width=\"20%\"></td>
    <td align=\"right\" width=\"6%\">$total_booked</td>
	<td align=\"right\" width=\"8%\"></td>
	<td align=\"right\" width=\"6%\"></td>
    <td align=\"right\" width=\"6%\"></td>
    <td align=\"right\" width=\"6%\"></td>
    <td align=\"right\" width=\"12%\">".number_format($total_amount,2)."</td>
    <td align=\"center\" width=\"15%\"></td>
    <td align=\"center\" width=\"15%\"></td>
  </tr>";
echo "</table>";

if($total_rows == 0)
echo "<font color=\"#FF0000\"><strong>No records found</strong></font>";


mysqli_close($link);
?>