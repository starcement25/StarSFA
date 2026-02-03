<?php
ob_start();
session_start();
require("adminUtils.php");

$employee = $_REQUEST['employee'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$date_array = array();
if($employee == 'all'){
	$order_condition = '';
	$payment_condition = '';
	$emp_condition = '';
}
else{
	$emp_condition = " AND WP.receive_emp_code IN('".$employee."') ";
}
if($start_date!='' && $end_date!='')
{
	$date_condition=" AND SUBSTRING(WP.receive_date,1,10) >= '".$start_date."' 
		AND SUBSTRING(WP.receive_date,1,10) <='".$end_date."'";
}
else
{
	$date_condition='';
}
$curdate=date('Y-m-d');
	$sql_log_PDF = "SELECT WP.*,EM.emp_name,DATE_FORMAT(SUBSTRING(receive_date,1,10),'%d-%m-%Y') As receive_date FROM employee_master EM,whats_app_log WP WHERE EM.emp_code=WP.receive_emp_code $emp_condition $date_condition ORDER BY EM.emp_name ASC,WP.receive_date DESC";
	$res_log_PDF = mysqli_query($link,$sql_log_PDF);
	$countlogPDF=mysqli_num_rows($res_log_PDF);
	if($countlogPDF >0){
		$count = 1;
		?>
		<table border="1" style="border-collapse:collapse;" class="border" width="70%" align="center">
          <tr class="TDHEAD_SUB">
          	<td colspan="10" align="center">Whats App PDF Information</td>
          </tr>
		  <tr class="TDHEAD" align="center">
			<td width="8%">SI</td>
            <td width="12%">Employee Code</td>
			<td width="">Employee Name</td>
            <td width="12%">Phone no</td>
            <!--td width="12%">Activity Date</td-->
            <td width="12%">Activity date</td>
            <td width="12%">PDF file</td>
            <td width="15%">PDF file less than 5 visits</td>
		  </tr>
		<?php
		while($rowlogPDF = mysqli_fetch_assoc($res_log_PDF)){
			$receive_emp_code = $rowlogPDF['receive_emp_code'];
			$emp_name = $rowlogPDF['emp_name'];
			$receiver_phone = $rowlogPDF['receiver_phone'];
			$receive_date = $rowlogPDF['receive_date'];
			/*if($receive_date=='25-08-2021' || $receive_date=='26-08-2021')
			{
				$receive_date=$receive_date;
			}
			else
			{
			$receive_date=date('d-m-Y', strtotime("-1 days,$receive_date "));
			}*/
			$activity_date=date('d-m-Y', strtotime("-1 days,$receive_date"));
			$PDF_URL = $rowlogPDF['PDF_URL'];
			$PDF_URL_2 = $rowlogPDF['PDF_URL_2'];
			$PDF_file_view="<a href=\"$PDF_URL\" style=\"color: #F00;\" target=\"_blank\">View</a>";
			if($PDF_URL_2!='')
			{
			$PDF_file_2_view="<a href=\"$PDF_URL_2\" style=\"color: #F00;\" target=\"_blank\">View</a>";
			}
			else $PDF_file_2_view='';
			echo "<tr>
					<td >".$count."</td>
					<td >".$receive_emp_code."</td>
					<td >".$emp_name."</td>
					<td >".$receiver_phone."</td>
					<td >".$receive_date."</td>
					<td >".$PDF_file_view."</td>
					<td >".$PDF_file_2_view."</td>
				  </tr>";
			$count++;
		}
		?>
        </table>
        <?php
	}
	else{
		echo "<span style=\"color:red; font-weight:bold;\">No Record Found</span>";
	}
?>
