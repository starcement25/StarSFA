<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>
<table width="60%" cellpadding="8px" >
  <tr>
    <td onclick="showtour();" style="cursor:pointer; font-weight:bold; background:#999999;" align="center" id="tdtour">TRAVELLING EXPENSES</td>
    <td onclick="showfood();" style="cursor:pointer; font-weight:bold; background:#CCCCCC;" align="center" id="tdfood">FOODING EXPENSES</td>
    <td onclick="showlodge();" style="cursor:pointer; font-weight:bold; background:#CCCCCC;" align="center" id="tdlodge">LODGING EXPENSES</td>
  </tr>
</table>

<?php

function get_emp_details($emp_code){
	$sql_emp_name = "SELECT emp_name, HQ, state FROM employee_master WHERE emp_code = '".$emp_code."'";
	$res_emp_name = mysql_query($sql_emp_name);
	$row_emp_name = mysql_fetch_array($res_emp_name);
	
	$emp_name = $row_emp_name['emp_name'];
	$HQ = $row_emp_name['HQ'];
	$state = $row_emp_name['state'];
	
	$return_data = $emp_name."^".$HQ."^".$state;
	
	return $return_data;
}

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$emp_code = $_REQUEST['emp_code'];

$startdate = str_replace("-","",$start_date);
$enddate = str_replace("-","",$end_date);

if(strtoupper($_SESSION['admin_login']) == "ADMIN"){
	
	if($emp_code == 'all'){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
		$emp_hierarchy_condition_one=" AND emp_code != '' ";
		
	}
	else{
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
		$emp_hierarchy_condition_one=" AND emp_code = '".$emp_code."' ";
	}
}
else
{
	if($emp_code == 'all'){
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition_one=' AND emp_code IN('.$emp_hierarchy.')';
	}
	else{
		$emp_hierarchy_condition_one=" AND emp_code = '".$emp_code."' ";
	}
	
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition_one=' AND emp_code IN('.$employee.')';
}

$tour_expense_fare_total = '';
$food_expense_fare_total = '';
$lodge_expense_fare_total = '';


$sql_tourexpense_emp = "SELECT DISTINCT emp_code FROM tour_expenses WHERE (tour_date BETWEEN '".date('Y-m-d', strtotime($startdate))."' AND '".date('Y-m-d', strtotime($enddate))."') ".$emp_hierarchy_condition_one." ORDER BY  emp_code ASC";
$res_tourexpense_emp = mysql_query($sql_tourexpense_emp);
$total_rows_tourexpense = mysql_num_rows($res_tourexpense_emp);
?>
<div id="tour_expenses">
<?php
if($total_rows_tourexpense>0){
	?>
    <table width="100%" border="1" style="border-collapse:collapse;" class="BORDER" id="touring_expenses">
      <tr class="TDHEAD">
        <td colspan="7" align="center">Travelling Expenses</td>
      </tr>
      <tr class="TDHEAD_SUB" align="center">
        <td>Date</td>
        <td>From</td>
        <td>To</td>
        <td>Mode of Transport</td>
        <td>KM</td>
        <td>Fare</td>
        <td>Voucher Attached</td>
      </tr>
    <?php
	$res_tourexpense_emp = mysql_query($sql_tourexpense_emp);
	while($row_tourexpense_emp = mysql_fetch_array($res_tourexpense_emp)){
		
		$tourexpense_emp_code = $row_tourexpense_emp['emp_code'];
		$date_selected_array = array();
		$emp_tour_expense_sum = '';
		
		$return_data = get_emp_details($tourexpense_emp_code);
		$return_data_array = explode("^",$return_data);
		$em_name = $return_data_array[0];
		$em_HQ = $return_data_array[1];
		$em_state = $return_data_array[2];
		
		echo "<tr class=\"TDHEAD_SUB\"><td colspan=\"7\" align=\"center\" >Name:".$em_name." HQ:".$em_HQ." State:".$em_state."</td></tr>";
		
		$sql_tourexpense = "SELECT DATE_FORMAT(tour_date,'%d-%m-%Y') as date_selected, start_destination, end_destination, transport_mode_cat_id, distance, fare, attachment_file FROM tour_expenses WHERE (tour_date BETWEEN '".date('Y-m-d', strtotime($startdate))."' AND '".date('Y-m-d', strtotime($enddate))."') AND emp_code = '".$tourexpense_emp_code."' ORDER BY DATE_FORMAT(tour_date,'%d-%m-%Y') ASC";
		$res_tourexpense = mysql_query($sql_tourexpense);
		while($row_tourexpense = mysql_fetch_array($res_tourexpense)){
			$transport_cat_id = $row_tourexpense['transport_mode_cat_id'];
			$sql_transport_type = "SELECT transport_mode_cat_name FROM transport_mode_category WHERE 	
	transport_mode_cat_id = '".$transport_cat_id."'";
			$res_transport_type = mysql_query($sql_transport_type);
			$row_transport_type = mysql_fetch_array($res_transport_type);
			$transport_name = $row_transport_type['transport_mode_cat_name'];
			
			$date_selected = $row_tourexpense['date_selected'];
			
			if($row_tourexpense['tour_attachment_file'] != '')
				$tour_file = 'Yes';
			else
				$tour_file = 'No';
				
						
			if(!in_array($date_selected,$date_selected_array))
			{
				array_push($date_selected_array, $date_selected);
			}
			else 
				$date_selected = '';
	
			echo "
			<tr>
			<td>".$date_selected."</td>
			<td>".$row_tourexpense['start_destination']."</td>
			<td>".$row_tourexpense['end_destination']."</td>
			<td>".$transport_name."</td>
			<td align=\"right\">".$row_tourexpense['distance']."</td>
			<td align=\"right\">".$row_tourexpense['fare']."</td>
			<td align=\"center\">".$tour_file."</td>
			</tr>";
			
			$tour_expense_fare_total += $row_tourexpense['fare'];
			$emp_tour_expense_sum += $row_tourexpense['fare'];
		}
		echo "<tr>
				<td colspan=\"5\" align=\"center\"><b>Total</b></td>
				<td align=\"right\"><b>".number_format($emp_tour_expense_sum,2)."</b></td>
				<td></td>
			  </tr>";
	}
	echo "</table>";
}
else{
	echo "<font color=\"red\"><strong>No records found</strong></font>";
}
?>
</div>

<?php
$sql_food_emp = "SELECT DISTINCT emp_code FROM fooding_expenses WHERE (date BETWEEN '".date('Y-m-d', strtotime($startdate))."' AND '".date('Y-m-d', strtotime($enddate))."')  ".$emp_hierarchy_condition_one." ORDER BY emp_code ASC";
$res_food_emp = mysql_query($sql_food_emp);
$total_fooding_rows = mysql_num_rows($res_food_emp);
?>
<div id="food_expenses" hidden>
<?php
if($total_fooding_rows>0){
	?>
    <table width="100%" border="1" style="border-collapse:collapse;" class="BORDER" id="fooding_expenses">
      <tr class="TDHEAD">
        <td colspan="5" align="center">Fooding Expenses</td>
      </tr>
      <tr class="TDHEAD_SUB" align="center">
        <td>Date</td>
        <td>Expenses Incurred</td>
        <td>Payment Mode</td>
        <td>Voucher Attached</td>
        <td>No. of Persons</td>
      </tr>
    <?php
	$res_food_emp = mysql_query($sql_food_emp);
	while($row_food_emp = mysql_fetch_array($res_food_emp)){
		
		$food_emp_code = $row_food_emp['emp_code'];
		$date_selected_array = array();
		$emp_foodexpense_total = '';
		
		$return_data = get_emp_details($food_emp_code);
		$return_data_array = explode("^",$return_data);
		$em_name = $return_data_array[0];
		$em_HQ = $return_data_array[1];
		$em_state = $return_data_array[2];
		
		echo "<tr class=\"TDHEAD_SUB\"><td colspan=\"5\" align=\"center\" >Name:".$em_name." HQ:".$em_HQ." State:".$em_state."</td></tr>";
		
		$sql_foodexpense = "SELECT DATE_FORMAT(date,'%d-%m-%Y') as date_selected, payment_amount, payment_mode, attachment_file, accompany FROM fooding_expenses WHERE (date BETWEEN '".date('Y-m-d', strtotime($startdate))."' AND '".date('Y-m-d', strtotime($enddate))."') AND emp_code = '".$food_emp_code."' ORDER BY DATE_FORMAT(date,'%d-%m-%Y') ASC";
		$res_foodexpense = mysql_query($sql_foodexpense);
		while($row_foodexpense = mysql_fetch_array($res_foodexpense))
		{
			if($row_foodexpense['attachment_file'] != '')
				$food_file = 'Yes';
			else
				$food_file = '';
				
			$date_selected = $row_foodexpense['date_selected'];
			if(!in_array($date_selected,$date_selected_array))
			{
				array_push($date_selected_array,$date_selected);
			}
			else
				$date_selected = '';
			
			echo "<tr>
			<td>".$date_selected."</td>
			<td align=\"right\">".$row_foodexpense['payment_amount']."</td>
			<td>".$row_foodexpense['payment_mode']."</td>
			<td>".$food_file."</td>
			<td align=\"right\">".$row_foodexpense['accompany']."</td></tr>";
			
			$food_expense_fare_total += $row_foodexpense['payment_amount'];
			$emp_foodexpense_total += $row_foodexpense['payment_amount'];
		}
		echo "<tr>
				<td align=\"center\"><b>Total</b></td>
				<td align=\"right\"><b>".number_format($emp_foodexpense_total,2)."</b></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>";
	}
	echo "</table>";
}
else{
	echo "<font color=\"red\"><strong>No records found</strong></font>";
}
?>
</div>

<?php
$sql_emp = "SELECT DISTINCT emp_code FROM lodging_expenses WHERE (chkin_date BETWEEN '".date('Y-m-d', strtotime($startdate))."' AND '".date('Y-m-d', strtotime($enddate))."') ".$emp_hierarchy_condition_one." ORDER BY emp_code ASC";
$res_emp = mysql_query($sql_emp);
$total_lodge_rows = mysql_num_rows($res_emp);
?>
<div id="lodge_expenses" hidden>
<?php
if($total_lodge_rows>0){
	?>
    <table width="100%" border="1" style="border-collapse:collapse;" class="BORDER" id="lodging_expenses">
      <tr class="TDHEAD">
        <td colspan="6" align="center">Lodging Expenses</td>
      </tr>
      <tr class="TDHEAD_SUB" align="center">
        <td>Entry Date</td>
        <td>Expenses Incurred</td>
        <td>Payment Mode</td>
        <td>Voucher Attached</td>
        <td>Checkin Date</td>
        <td>Checkout Date</td>
      </tr>
    <?php
	$res_emp = mysql_query($sql_emp);
	while($row_emp = mysql_fetch_array($res_emp)){
		
		$date_selected_array = array();
		$lodge_emp_code = $row_emp['emp_code'];
		$emp_lodge_expense_total = '';
		
		$return_data = get_emp_details($lodge_emp_code);
		$return_data_array = explode("^",$return_data);
		$em_name = $return_data_array[0];
		$em_HQ = $return_data_array[1];
		$em_state = $return_data_array[2];
		
		echo "<tr class=\"TDHEAD_SUB\"><td colspan=\"6\" align=\"center\" >Name:".$em_name." HQ:".$em_HQ." State:".$em_state."</td></tr>";
		
		$sql_lodgeexpense = "SELECT DATE_FORMAT(SUBSTRING(lodg_exp_trans_id,-14,8),'%d-%m-%Y') as date_selected, payment_amount, payment_mode, attachment_file, chkin_date, chkout_date FROM lodging_expenses WHERE (chkin_date BETWEEN '".date('Y-m-d', strtotime($startdate))."' AND '".date('Y-m-d', strtotime($enddate))."') AND emp_code = '".$lodge_emp_code."' ORDER BY DATE_FORMAT(SUBSTRING(lodg_exp_trans_id,-14,8),'%d-%m-%Y'), emp_code ASC";
		$res_lodgeexpense = mysql_query($sql_lodgeexpense);
		while($row_lodgeexpense = mysql_fetch_array($res_lodgeexpense)){
			if($row_lodgeexpense['attachment_file'] != '')
				$lodge_file = 'Yes';
			else
				$lodge_file = '';
				
			$date_selected = $row_lodgeexpense['date_selected'];
			if(!in_array($date_selected,$date_selected_array))
			{
				array_push($date_selected_array,$date_selected);
			}
			else
				$date_selected = '';
				
			echo "<tr>
			<td>".$date_selected."</td>
			<td align=\"right\">".$row_lodgeexpense['payment_amount']."</td>
			<td>".$row_lodgeexpense['payment_mode']."</td>
			<td>".$lodge_file."</td>
			<td>".date('d-m-Y', strtotime($row_lodgeexpense['chkin_date']))."</td>
			<td>".date('d-m-Y', strtotime($row_lodgeexpense['chkout_date']))."</td>
			</tr>";
			
			$lodge_expense_fare_total += $row_lodgeexpense['payment_amount'];
			$emp_lodge_expense_total += $row_lodgeexpense['payment_amount'];
		}
		?>
        <tr>
            <td align="center"><b>Total</b></td>
            <td align="right"><b><?php echo $emp_lodge_expense_total; ?></b></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
        <?php
	}
	?>
    </table>
    <?php
}
else{
	echo "<font color=\"red\"><strong>No records found</strong></font>";
}
?>
</div>