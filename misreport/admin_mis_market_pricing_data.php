<?php
ob_start();
session_start();
require("adminUtils.php");
/*require("include/config.php");
require("include/config-setup.php");*/
require("include/dbcon.php");

$month_data = $_REQUEST['month_data'];
$year_month_split = explode("-",$month_data);
$month_days = cal_days_in_month(CAL_GREGORIAN,$year_month_split[1],$year_month_split[0]);
$monthName = date('M', mktime(0, 0, 0, $year_month_split[1], 10));

$show_month = $monthName."-".$year_month_split[0];


$employee = $_REQUEST['employee'];
$employee_arg = str_replace("#",",",$employee);
$employee_arg = str_replace("^","'",$employee_arg);
?>

<table border="1" style="border-collapse:collapse;" class="border" width="100%">
  <tr class="TDHEAD">
  	<td align="center" valign="middle">Week</td>
    <td align="center" valign="middle">Month</td>
    <td align="center" valign="middle">Product</td>
    <td colspan="4" align="center">Competitor Price</td>
  </tr>
  <tr class="TDHEAD">
  	<td></td>
    <td></td>
    <td></td>
    <td align="center">Billing Ex</td>
    <td align="center">WSP EX</td>
    <td align="center">RSP EX</td>
    <td align="center">NOD</td>
  </tr>
<?php
for($i=1;$i<=4;$i++){
	
	$sql_competitor_name = "SELECT competitor_name FROM competitor_group_master ORDER BY competitor_name ASC";
	$res_competitor_name = mysqli_query($link,$sql_competitor_name);
	while($row_competitor_name = mysqli_fetch_assoc($res_competitor_name)){
		$competitor_name = $row_competitor_name['competitor_name'];
		
		if($competitor_name == 'AMBUJA PPC'){
			$column_name_PTD = 'ambuja_PTD';
			$column_name_PTR = 'ambuja_PTR';
			$column_name_PTC = 'ambuja_PTC';
			$column_name_PV = 'ambuja_PV';
		}
		else if($competitor_name == 'ULTRATECH PPC'){
			$column_name_PTD = 'ultratech_PTD';
			$column_name_PTR = 'ultratech_PTR';
			$column_name_PTC = 'ultratech_PTC';
			$column_name_PV = 'ultratech_PV';
		}
		else if($competitor_name == 'LAFARGE PPC'){
			$column_name_PTD = 'lafarge_PTD';
			$column_name_PTR = 'lafarge_PTR';
			$column_name_PTC = 'lafarge_PTC';
			$column_name_PV = 'lafarge_PV';
		}
		else if($competitor_name == 'DALMIA PPC'){
			$column_name_PTD = 'dalmia_PTD';
			$column_name_PTR = 'dalmia_PTR';
			$column_name_PTC = 'dalmia_PTC';
			$column_name_PV = 'dalmia_PV';
		}
		else if($competitor_name == 'TOPCEM PPC'){
			$column_name_PTD = 'topcem_PTD';
			$column_name_PTR = 'topcem_PTR';
			$column_name_PTC = 'topcem_PTC';
			$column_name_PV = 'topcem_PV';
		}
		else if($competitor_name == 'ACC PPC'){
			$column_name_PTD = 'acc_PTD';
			$column_name_PTR = 'acc_PTR';
			$column_name_PTC = 'acc_PTC';
			$column_name_PV = 'acc_PV';
		}
		else if($competitor_name == 'BIRLA GOLD PPC'){
			$column_name_PTD = 'birla_gold_PTD';
			$column_name_PTR = 'birla_gold_PTR';
			$column_name_PTC = 'birla_gold_PTC';
			$column_name_PV = 'birla_gold_PV';
		}
		
		if($i == 1){
			$start_date = $month_data."-01";
			$end_date = $month_data."-07";
			$week = "Week 1";
			$style = " style=\"background:#FFE4C4;\"";
		}
		else if($i == 2){
			$start_date = $month_data."-08";
			$end_date = $month_data."-15";
			$week = "Week 2";
			$style = " style=\"background:#FAF0E6;\"";
		}
		else if($i == 3){
			$start_date = $month_data."-16";
			$end_date = $month_data."-23";
			$week = "Week 3";
			$style = " style=\"background:#EEE5DE;\"";
		}
		else if($i == 4){
			$start_date = $month_data."-24";
			$end_date = $month_data."-".$month_days;
			$week = "Week 4";
			$style = " style=\"background:#CDC9C9;\"";
		}
		
		$ptd = '';
		$ptr = '';
		$ptc = '';
		$pv = '';
		
		$sql_competitor_pricing = "SELECT $column_name_PTD, $column_name_PTR, $column_name_PTC, $column_name_PV FROM competitor_pricing WHERE emp_code IN (".$employee_arg.") AND (date_time BETWEEN '".$start_date."' AND '".$end_date."')";
		$res_competitor_pricing = mysqli_query($link,$sql_competitor_pricing);
		$total_column = mysqli_num_rows($res_competitor_pricing);
		if($total_column>0){
			$res_competitor_pricing = mysqli_query($link,$sql_competitor_pricing);
			while($row_competitor_pricing = mysqli_fetch_assoc($res_competitor_pricing)){
				$ptd += $row_competitor_pricing[$column_name_PTD];
				$ptr += $row_competitor_pricing[$column_name_PTR];
				$ptc += $row_competitor_pricing[$column_name_PTC];
				$pv +=  $row_competitor_pricing[$column_name_PV];
			}
			
			$sql_PTD_count = "SELECT COUNT($column_name_PTD) AS ptd_count FROM `competitor_pricing` WHERE emp_code IN (".$employee_arg.") AND (date_time BETWEEN '".$start_date."' AND '".$end_date."') AND $column_name_PTD != '0'";
			$res_PTD_count = mysqli_query($link,$sql_PTD_count);
			$row_PTD_count = mysqli_fetch_assoc($res_PTD_count);
			$PTD_count = $row_PTD_count['ptd_count'];
			
			$sql_PTR_count = "SELECT COUNT($column_name_PTR) AS ptr_count FROM `competitor_pricing` WHERE emp_code IN (".$employee_arg.") AND (date_time BETWEEN '".$start_date."' AND '".$end_date."') AND $column_name_PTR != '0'";
			$res_PTR_count = mysqli_query($link,$sql_PTR_count);
			$row_PTR_count = mysqli_fetch_assoc($res_PTR_count);
			$PTR_count = $row_PTR_count['ptr_count'];
			
			$sql_PTC_count = "SELECT COUNT($column_name_PTC) AS ptc_count FROM `competitor_pricing` WHERE emp_code IN (".$employee_arg.") AND (date_time BETWEEN '".$start_date."' AND '".$end_date."') AND $column_name_PTC != '0'";
			$res_PTC_count = mysqli_query($link,$sql_PTC_count);
			$row_PTC_count = mysqli_fetch_assoc($res_PTC_count);
			$PTC_count = $row_PTC_count['ptc_count'];
			
			$sql_PV_count = "SELECT COUNT($column_name_PV) AS pv_count FROM `competitor_pricing` WHERE emp_code IN (".$employee_arg.") AND (date_time BETWEEN '".$start_date."' AND '".$end_date."') AND $column_name_PV != '0'";
			$res_PV_count = mysqli_query($link,$sql_PV_count);
			$row_PV_count = mysqli_fetch_assoc($res_PV_count);
			$PV_count = $row_PV_count['pv_count'];
			
			$ptd_avg = number_format($ptd/$PTD_count,2);
			$ptr_avg = number_format($ptr/$PTR_count,2);
			$ptc_avg = number_format($ptc/$PTC_count,2);
			$pv_avg = number_format($pv/$PV_count,2);
				
			echo "<tr $style>
						<td>".$week."</td>
						<td>".$show_month."</td>
						<td>".$competitor_name."</td>
						<td align=\"right\">".$ptd_avg."</td>
						<td align=\"right\">".$ptr_avg."</td>
						<td align=\"right\">".$ptc_avg."</td>
						<td align=\"right\">".$pv_avg."</td>
					  </tr>";
			}
		
	}
}
mysqli_close($link);
?>
</table>
