<?php

ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$emp_code = $_REQUEST['emp_code'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

if($emp_code == 'all'){
	$emp_condition = " 1";
}
else{
	$emp_condition = " emp_code = '".$emp_code."'";
}


$sql_emp_code = "SELECT DISTINCT emp_code FROM tour_fooding_lodging_expenses WHERE ".$emp_condition." AND (tour_date BETWEEN '".$start_date."' AND '".$end_date."')";
$res_emp_code = mysqli_query($link,$sql_emp_code);
$total_row_check = mysqli_num_rows($res_emp_code);
if($total_row_check>0){
	?>
    <div id="display_data">
    <table class="border" style="border-collapse:collapse;" width="100%" border="1" cellpadding="4px">
    	<tr class="TDHEAD_SUB">
        	<td align="center" colspan="10">Travelling Bill</td>
        </tr>
    	<tr class="TDHEAD">
        	<td rowspan="2">Date</td>
            <td colspan="2" align="center">Time</td>
            <td rowspan="2">Particulars</td>
            <td rowspan="2">Local Conv.</td>
            <td rowspan="2">Train/Bus fair</td>
            <td rowspan="2">Fooding Allowance</td>
            <td rowspan="2">Hotel Charges</td>
            <td rowspan="2">Other Expenses</td>
            <td rowspan="2">Remark</td>
        </tr>
        <tr class="TDHEAD">
        	<td align="center">Dept.</td>
            <td align="center">Arr.</td>
        </tr>
    <?php
	$res_emp_code = mysqli_query($link,$sql_emp_code);
	while($row_emp_code = mysqli_fetch_assoc($res_emp_code)){
		$emp_code = $row_emp_code['emp_code'];
		
		$sql_emp_details = "SELECT emp_name, designation, sale_access FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$designation = $row_emp_details['designation'];
		$sale_access = $row_emp_details['sale_access'];
		
		/*$sql_max_min_date = "SELECT MAX(DATE_FORMAT(tour_date,'%d-%m-%Y')) AS end_date, MIN(DATE_FORMAT(tour_date,'%d-%m-%Y')) AS start_date FROM tour_fooding_lodging_expenses WHERE emp_code = '".$emp_code."' AND (tour_date BETWEEN '".$start_date."' AND '".$end_date."')";
		$res_max_min_date = mysqli_query($link,$sql_max_min_date);
		$row_max_min_date = mysqli_fetch_assoc($res_max_min_date);
		
		$max_date = $row_max_min_date['end_date'];
		$min_date = $row_max_min_date['start_date'];*/
		
		?>
        <tr>
        	<td colspan="10" align="center" style="padding:8px;">
            	<table border="1" style="border-collapse:collapse;" width="60%">
                	<tr>
                    	<td style="font-weight:bold;">Name</td>
                        <td><?php echo $emp_name; ?></td>
                        <td style="font-weight:bold;">Designation</td>
                        <td><?php echo $designation; ?></td>
                    </tr>
                    <tr>
                    	<td style="font-weight:bold;">Department</td>
                        <td><?php echo $sale_access; ?></td>
                        <td style="font-weight:bold;">Reason Of Travelling</td>
                        <td></td>
                    </tr>
                    <tr>
                    	<td colspan="4" align="center" style="font-weight:bold;">Details Of Expenses - (<?php echo date('d-m-Y', strtotime($start_date)). " to ".date('d-m-Y', strtotime($end_date)); ?>)</td>
                    </tr>
                </table>            	
            </td>
        </tr>
        <?php
				
		$sql_tour_details = "SELECT * FROM tour_fooding_lodging_expenses WHERE emp_code = '".$emp_code."' AND (tour_date BETWEEN '".$start_date."' AND '".$end_date."') ORDER BY tour_date ASC";
		$res_tour_details = mysqli_query($link,$sql_tour_details);
		while($row_tour_details = mysqli_fetch_assoc($res_tour_details)){
			
			$tour_date = date('d-m-Y',strtotime($row_tour_details['tour_date']));
			$dep_time = $row_tour_details['dep_time'];
			$arr_time = $row_tour_details['arr_time'];
			$particulars = $row_tour_details['particulars'];
			$local_conveyance = $row_tour_details['local_conveyance'];
			$travel_mode = $row_tour_details['travel_mode'];
			$transport_fair = $row_tour_details['transport_fair'];
			$fooding_allowance = $row_tour_details['fooding_allowance'];
			$hotel_charge = $row_tour_details['hotel_charge'];
			$other_expenses = $row_tour_details['other_expenses'];
			$remarks = $row_tour_details['remarks'];
			$supporting_attached = $row_tour_details['supporting_attached'];
			$attachment_file = $row_tour_details['attachment_file'];
			
			$sum_local_conveyance += $local_conveyance;
			$sum_transport_fair += $transport_fair;
			$sum_fooding_allowance += $fooding_allowance;
			$sum_hotel_charge += $hotel_charge;
			$sum_other_expenses += $other_expenses;
			
			echo "<tr>
					<td>".$tour_date."</td>
					<td>".$dep_time."</td>
					<td>".$arr_time."</td>
					<td>".$particulars."</td>
					<td align=\"right\">".$local_conveyance."</td>
					<td align=\"right\">".$transport_fair."</td>
					<td align=\"right\">".$fooding_allowance."</td>
					<td align=\"right\">".$hotel_charge."</td>
					<td align=\"right\">".$other_expenses."</td>
					<td>".$remarks."</td>
				</tr>";
		}
		echo "<tr>
				<td colspan=\"4\" align=\"right\" style=\"font-weight:bold;\">Total</td>
				<td align=\"right\">".$sum_local_conveyance."</td>
				<td align=\"right\">".$sum_transport_fair."</td>
				<td align=\"right\">".$sum_fooding_allowance."</td>
				<td align=\"right\">".$sum_hotel_charge."</td>
				<td align=\"right\">".$sum_other_expenses."</td>
				<td></td>
			</tr>";
		$sum_total_expenses = ($sum_local_conveyance+$sum_transport_fair+$sum_fooding_allowance+$sum_hotel_charge+$sum_other_expenses);
		echo "<tr>
				<th colspan=\"4\" align=\"right\" style=\"font-weight:bold;\">Total</th>
				<td align=\"left\" colspan=\"6\"><b>".$sum_total_expenses."</b></td>
			</tr>";
		
		$sum_local_conveyance = '';
		$sum_transport_fair = '';
		$sum_fooding_allowance = '';
		$sum_hotel_charge = '';
		$sum_other_expenses = '';
	}
	?>
    </table>
    </div>
    <?php
}
else{
	echo "<font color=\"red\"><strong>No records</strong></font>";
}
?>
    <input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display_data');">&nbsp;
    <input name="export" type="button" value="Export" id="export" onClick="exporttocsv('#display_data');">