<?php
ob_start();
	session_start();
	require("adminUtils.php");
	require("datefunction.php");
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

if($_REQUEST['condition_value'] == 'today')
{
	$today = date('Y-m-d');
	$today = str_replace("-","",$today);
			
	$condition = "AND substring(SD.sauda_no,-14,8) LIKE '%$today%'";
	$value = 1;
	
	$report_subject = "Today";
}
else if($_REQUEST['condition_value'] == 'mtd')
{
	@$current_date = date('Y-m-d');
	$month = explode("-",$current_date);
	$year = $month[0];
	$month = $month[1];
	
	$condition = "AND substring(SD.sauda_no,8,4) =$year AND substring(SD.sauda_no,12,2) =$month";
	$value = 2;
	
	$report_subject = "MTD";
}
else if($_REQUEST['condition_value'] == 'custom')
{
	$start_date = $_GET['start_date'];
	$end_date = $_GET['end_date'];
	
	$condition = "AND (substring(SD.sauda_no,-14,8) BETWEEN ".date('Ymd', strtotime($start_date))." AND ".date('Ymd', strtotime($end_date)).")";
	$value = 3;
	
	$report_subject = "From:".date('d-m-Y', strtotime($start_date))." To ".date('d-m-Y', strtotime($end_date));
}

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
?><head>
  <title>tableToExcel Demo</title>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
</head>

<div id="export_table" style="font-family:Verdana, Geneva, sans-serif;">
<table width="100%" border="1" style="border-collapse:collapse;" class="border" cellpadding="5px">
<tr style="background:#FFFFCC; font-weight:bold;"><td colspan="7" align="center"><?php echo $report_subject; ?></td></tr>
<?php
$count = 1;
$product_group_name_array = array();
$sql_product_group = "SELECT product_group_code, product_group_name FROM product_group_master";
$res_product_group = mysqli_query($link,$sql_product_group);
while($row_product_group = mysqli_fetch_assoc($res_product_group))
{
	$product_group_code = $row_product_group['product_group_code'];
	if(!in_array($row_product_group['product_group_name'],$product_group_name_array))
	{
		array_push($product_group_name_array,$row_product_group['product_group_name']);
		echo "<tr class=\"TDHEAD\" style=\"background:#CCCCCC; font-weight:bold;\">
			<td align=\"center\" colspan=\"7\">".$row_product_group['product_group_name']."</td>
		</tr>
		<tr align=\"center\" style=\"font-weight:bold;\">
			<td rowspan=\"2\">SI</td>
			<td rowspan=\"2\">Date</td>
			<td rowspan=\"2\">Product</td>
			<td colspan=\"2\">Quantity</td>
			<td rowspan=\"2\">Sale Rate</td>
			<td rowspan=\"2\">Amount</td>
		  </tr>
		  <tr class=\"TDHEAD_SUB\" align=\"center\" style=\"font-weight:bold;\">
			<td>MT</td>
			<td>Case</td>
		  </tr>";
	}
	$date_array = array();
	$state_zone_array = array();
	
	if($_GET['mode'] == 'zone')
	{
		$sql_product = "SELECT EM.zone as zone_state_name, DATE_FORMAT(SUBSTRING(SD.sauda_no,-14,8),'%d-%m-%Y') as date_selected, PM.prod_desc, sum(convert_qty_two) as mt_booked, sum(SD.qty) as case_booked, SD.sale_rate, SD.freight_charge, SD.amount FROM sauda_details SD, product_master PM, employee_master EM WHERE SUBSTRING(SD.sauda_no,3,5)= EM.emp_code $emp_hierarchy_condition_one $condition AND PM.$field_name1='$product_group_code' AND SD.sku_code=PM.prod_code GROUP BY EM.zone, SD.sku_code ORDER BY date_selected, EM.zone";
	}
	else
	{
		$sql_product = "SELECT EM.state as zone_state_name, DATE_FORMAT(SUBSTRING(SD.sauda_no,-14,8),'%d-%m-%Y') as date_selected, PM.prod_desc, sum(convert_qty_two) as mt_booked, sum(SD.qty) as case_booked, SD.sale_rate, SD.freight_charge, SD.amount FROM sauda_details SD, product_master PM, employee_master EM WHERE SUBSTRING(SD.sauda_no,3,5)= EM.emp_code $emp_hierarchy_condition_one $condition AND PM.$field_name1='$product_group_code' AND SD.sku_code=PM.prod_code GROUP BY EM.state, SD.sku_code ORDER BY date_selected, EM.state";
	}
	$res_product = mysqli_query($link,$sql_product);
	while($row_product = mysqli_fetch_assoc($res_product))
	{
		if(!in_array($row_product['zone_state_name'],$state_zone_array))
		{
			array_push($state_zone_array,$row_product['zone_state_name']);
			echo "<tr style=\"background:#999966;\"><td align='center' colspan='7' style='font-weight:bold;'>".$row_product['zone_state_name']."</td></tr>";
		}
		echo "<tr><td>$count</td>";
		if(!in_array($row_product['date_selected'],$date_array))
		{
			array_push($date_array,$row_product['date_selected']);
			echo "<td>".$row_product['date_selected']."</td>";
		}
		else
		{
			echo "<td></td>";
		}
		
		echo "<td>".$row_product['prod_desc']."</td>
		  <td align=\"right\">".$row_product['mt_booked']."</td>
		  <td align=\"right\">".$row_product['case_booked']."</td>
		  <td align=\"right\">".number_format($row_product['sale_rate'],2)."</td>
		  <td align=\"right\">".$row_product['amount']."</td>";
		  
		$total_amount += $row_product['amount'];
  		$total_booked += $row_product['mt_booked'];
  		$total_booked_case += $row_product['case_booked'];
	
	
		$count++;
	}
	echo "<tr style=\"font-weight:bold; backgroud:#FFFAFA;\">
			<td colspan=\"3\" align=\"center\">Total</td>
			<td align=\"right\">$total_booked</td>
			<td align=\"right\">$total_booked_case</td>
			<td></td>
			<td align=\"right\">$total_amount</td>
		</tr>";
		
	$total_amount = '';
	$total_booked = '';
	$total_booked_case = '';
		
	unset($state_array);
	unset($date_array);
}

if($_GET['mode'] == 'zone')
	$report_name = 'zonewisereport';
else
	$report_name = 'statewisereport'
?>
</table>
</div>
<br />
<center><input type="button" value="Download" id="btnExport" />&nbsp;<input type="button" value="Print" id="btnPrint"
onclick="window.print();" /></center>
<br />

<script>
$(document).ready(function() {
    $("#btnExport").click(function(e) {
        //getting values of current time for generating the file name
        var dt = new Date();
        var day = dt.getDate();
        var month = dt.getMonth() + 1;
        var year = dt.getFullYear();
        var hour = dt.getHours();
        var mins = dt.getMinutes();
        var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
        //creating a temporary HTML link element (they support setting file names)
        var a = document.createElement('a');
        //getting data from our div that contains the HTML table
        var data_type = 'data:application/vnd.ms-excel';
        var table_div = document.getElementById('export_table');
        var table_html = table_div.outerHTML.replace(/ /g, '%20');
        a.href = data_type + ', ' + table_html;
        //setting the file name
        a.download = '<?php echo $report_name; ?>' + postfix + '.xls';
        //triggering the function
        a.click();
        //just in case, prevent default behaviour
        e.preventDefault();
    });
});
</script>

<div style="background: