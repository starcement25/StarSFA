<?php
error_reporting(0);
ob_start();
	session_start();
	if(strtoupper($_SESSION['admin_login'])=="ED01"){
		require("adminUtils_HBC_SFATS.php");
	}
	else
	{
		require("adminUtils.php");
	}
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>
<?php
if($_GET['type'] == 'today')
{
	$today = date('Y-m-d');
	$today = str_replace("-","",$today);
	$condition = "AND substring(SD.sauda_no,-14,8)='$today'";
	$sauda_duration = date('d-m-Y');
}
else if($_GET['type'] == 'mtd')
{
	@$current_date = date('Y-m-d');
	$month = explode("-",$current_date);
	$year = $month[0];
	$month = $month[1];
	$condition = "AND substring(SD.sauda_no,8,4) =$year AND substring(SD.sauda_no,12,2) =$month";
	$sauda_duration = "From : 01-".$month."-".$year." To ".date('d-m-Y');
}
else if($_GET['type'] == 'custom')
{
	$start_date = str_replace("-","",$_GET['start_date']);
	$strt = date('d-m-Y',strtotime($start_date));
	$end_date = str_replace("-","",$_GET['end_date']);
	$endt = date('d-m-Y',strtotime($end_date));
	$condition = "AND (substring(SD.sauda_no,-14,8) BETWEEN ".$start_date." AND ".$end_date.")";
	$sauda_duration = "From :".$strt." to ".$endt;
}
else
{
	$today = date('Y-m-d');
	$today = str_replace("-","",$today);
	$condition = "AND substring(SD.sauda_no,-14,8)='$today'";
	$sauda_duration = date('d-m-Y');
}
?>
<div style="position:absolute; width:inherit;">
<table width="100%" style="border-collapse:collapse;" class="border">
  <tr>
  	<td colspan="8" align="center" class="TDHEAD">Saudawise: <?php echo $sauda_duration;?></td>
  </tr>
  <tr class="TDHEAD_SUB" align="center">
  	<td width="5%">SI</td>
    <td width="18%">Product</td>
    <td width="6%">Booked</td>
    <td width="6%">Rate</td>
    <td width="6%">TD</td>
    <td width="8%">Amount</td>
    <td width="10%">Sauda Valid From</td>
    <td width="10%">Valid Upto</td>
  </tr>
</table>
</div>
<br /><br />
<table width="100%" border="1" style="border-collapse:collapse;">
<?php
if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login']=="supervisor" || $_SESSION['admin_login']=="system" ){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
		$emp_hierarchy_condition_one='';
	}
	else
	{
		if(strtoupper($_SESSION['admin_login'])=="ED01"){
			$emp_hierarchy=return_employee_hierarchy('E0042');
		}
		else
		{
			$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		}
			$emp_hierarchy_condition=' AND SAL.emp_code IN('.$emp_hierarchy.')';
			$emp_hierarchy_condition_one=' AND substring(SD.sauda_no,3,5) IN ('.$emp_hierarchy.')';
	}
	

	$sql_sauda_wise = "SELECT SD.sauda_no, PM.prod_desc, round((sum(PM.conversion_factor*SD.qty)/PM.conversion_factor_two),2 )as mt_booked, SD.sku_code, SD.sale_rate, SD.TD, SD.amount, SH.customer_code, SH.sauda_valid_from, CM.sauda_validity_period, DATE_FORMAT(SUBSTRING(SD.sauda_no,-14,8),'%d-%m-%Y') as sauda_date FROM sauda_details SD, sauda_header SH, product_master PM, customer_master CM WHERE SH.sauda_no=SD.sauda_no $emp_hierarchy_condition_one $condition AND SD.sku_code=PM.prod_code AND SH.customer_code=CM.customer_code GROUP BY DATE_FORMAT(SUBSTRING(SD.sauda_no,-14,8),'%d-%m-%Y'),SD.sku_code ORDER BY DATE_FORMAT(SUBSTRING(SD.sauda_no,-14,8),'%Y-%m-%d') DESC";
	$res_sauda_wise = mysqli_query($link,$sql_sauda_wise);
	$total_rows = mysqli_num_rows($res_sauda_wise);
	$count = 1;
	$sauda_date_array = array();
	
	$res_sauda_wise = mysqli_query($link,$sql_sauda_wise);
	while($row_sauda_wise = mysqli_fetch_assoc($res_sauda_wise))
	{
		$sauda_date = $row_sauda_wise['sauda_date'];
		if(!in_array($sauda_date,$sauda_date_array))
		{
			
			array_push($sauda_date_array, $sauda_date);
			echo "<tr style=\"background:#999999;\"><td colspan=\"8\" align=\"center\"><b>$sauda_date</b></td></tr>";
		}
		$sauda_valid_from = date('d-m-Y',strtotime($row_sauda_wise['sauda_valid_from']));
		$sauda_valid_days = $row_sauda_wise['sauda_validity_period'];
		$valid_upto = date('d-m-Y',strtotime($sauda_valid_from. '+'.$sauda_valid_days.' days'));
		echo "<tr>
	<td>$count</td>
    <td>$row_sauda_wise[prod_desc]</td>
    <td align=\"right\">$row_sauda_wise[mt_booked]</td>
    <td align=\"right\">$row_sauda_wise[sale_rate]</td>
    <td align=\"right\">$row_sauda_wise[TD]</td>
    <td align=\"right\">".number_format($row_sauda_wise['amount'],2)."</td>
    <td align=\"center\">$sauda_valid_from</td>
    <td align=\"center\">$valid_upto</td>
  </tr>";
  
  		$total_booked += $row_sauda_wise['mt_booked'];
		$total_amount += $row_sauda_wise['amount'];
		$count++;
	}
	/*echo "<pre>";
	print_r($row_sauda_wise);
	echo "</pre>";*/
echo "<tr style=\"font-weight:bold;\">
	<td>Total</td>
    <td></td>
    <td align=\"right\">$total_booked</td>
    <td align=\"right\"></td>
    <td align=\"right\"></td>
    <td align=\"right\">".number_format($total_amount,2)."</td>
    <td align=\"center\"></td>
    <td align=\"center\"></td>
  </tr>";
echo "</table>";

if($total_rows == 0)
echo "<font color=\"#FF0000\"><strong>No records found</strong></font>";

mysqli_close($link);
?>




