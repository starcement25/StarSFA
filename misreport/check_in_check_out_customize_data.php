<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$employee = $_REQUEST['employee'];
$start_date = $_REQUEST['start_date'];
$start_date_search=str_replace('-','',$start_date);
$end_date = $_REQUEST['end_date'];
$end_date_search=str_replace('-','',$end_date);
$date_array = array();
if($employee == 'all'){
	$order_condition = '';
	$payment_condition = '';
	if(strtoupper($_SESSION['admin_login'])=="ADMIN"){
		$emp_condition = '';
	}
	else{
		$emp_hierarchy_value=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_condition = " AND LO.emp_code IN(".$emp_hierarchy_value.") ";
	}
}
else{
	$order_condition = " SUBSTRING(order_no,2,5) IN(".$employee.") AND ";
	$payment_condition = " SUBSTRING(receipt_id,2,5) IN(".$employee.") AND ";
	$checkinout_condition = " SUBSTRING(trans_id,3,5) IN('".$employee."') AND ";
	$emp_condition = " AND LO.emp_code IN('".$employee."') ";
}
$emp_date_checkout_array=array();
$sql_checkin_time = "SELECT SUBSTRING(check_in_time,1,10) AS checkin_date,customer_code,SUBSTRING(trans_id,3,5) As emp_code,check_in_time FROM check_in_out_details WHERE 
	".$checkinout_condition." (SUBSTRING(check_in_time,1,10) BETWEEN '".$start_date."' AND '".$end_date."') 
	AND trans_id LIKE 'C%' GROUP BY SUBSTRING(trans_id,3,5),SUBSTRING(check_in_time,1,10) ORDER BY SUBSTRING(trans_id,3,5) ASC,check_in_time ASC";
$res_checkin_time = mysqli_query($link,$sql_checkin_time);
while($row_checkin_time = mysqli_fetch_assoc($res_checkin_time)){
	$checkintime_date = str_replace('-','',$row_checkin_time['checkin_date']);
	$emp_code_checkin=$row_checkin_time['emp_code'];
	$check_in_time = $row_checkin_time['check_in_time'];
	${check_in_time.$emp_code_checkin.$checkintime_date}=$check_in_time;
}

$sql_checkout_time = "SELECT SUBSTRING(check_out_time,1,10) AS checkout_date,customer_code,SUBSTRING(trans_id,3,5) As emp_code,check_out_time FROM check_in_out_details WHERE 
	".$checkinout_condition." (SUBSTRING(check_out_time,1,10) BETWEEN '".$start_date."' AND '".$end_date."') 
	AND trans_id LIKE 'C%' ORDER BY SUBSTRING(trans_id,3,5) ASC,check_out_time DESC";
$res_checkout_time = mysqli_query($link,$sql_checkout_time);
while($row_checkout_time = mysqli_fetch_assoc($res_checkout_time)){
	$checkouttime_date = str_replace('-','',$row_checkout_time['checkout_date']);
	$emp_code_checkout=$row_checkout_time['emp_code'];
	$check_out_time = $row_checkout_time['check_out_time'];
	$emp_code_checkout_string=$emp_code_checkout.$checkouttime_date;
	if(!in_array($emp_code_checkout_string,$emp_date_checkout_array)){
		${check_out_time.$emp_code_checkout.$checkouttime_date}=$check_out_time;
		array_push($emp_date_checkout_array,$emp_code_checkout_string);
	}
}
	?>
    <table class="border" width="100%" style="border-collapse:collapse;" border="1">
        <tr class="TDHEAD">
      	<td width="10%">Date</td>
        <td width="45%">Employee Name</td>
        <td width="10%">Attendance time</td>
         <td width="10%">Customer Check in Time</td>
         <td width="10%">Customer Check out Time</td>
         <td width="15%">Duration</td>
      </tr>
    <?
		//For Check in and Check out
		$sqlcheckinout="SELECT EM.emp_name,EM.emp_code,LO.trans_id,DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%m%d') AS formatted_date,DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%d-%m-%Y') AS att_date,DATE_FORMAT(SUBSTRING(LO.trans_id,-14,14),'%H:%i:%s') AS att_time 
						FROM employee_master EM,location LO WHERE LO.emp_code=EM.emp_code $emp_condition AND
					 (SUBSTRING(LO.trans_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') 
					 AND LO.trans_id LIKE 'A%' ORDER BY EM.emp_name ASC,DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d') DESC";
		$rscheckinout=mysqli_query($link,$sqlcheckinout) or die(mysqli_error()." Error in select check in out details ".$sqlcheckinout);
		$countcheckinout=mysqli_num_rows($rscheckinout);
		if($countcheckinout >0)
		{
			while($rowcheckinout=mysqli_fetch_assoc($rscheckinout))
			{
				$emp_code=$rowcheckinout['emp_code'];
				$emp_name=$rowcheckinout['emp_name'];
				$formatted_date=$rowcheckinout['formatted_date'];
				$att_date=$rowcheckinout['att_date'];
				$att_time=$rowcheckinout['att_time'];
				$time_difference_final=strtotime(${check_out_time.$emp_code.$formatted_date})-strtotime(${check_in_time.$emp_code.$formatted_date});
				if($time_difference_final >=3600)
				{
					$hours = floor($time_difference_final / 3600);
					$minutes = floor(($time_difference_final / 60) % 60);
					$seconds = $time_difference_final % 60;
					$time_duration=$hours.' Hour(s) '.$minutes.' Minute(s) '.$seconds.' Second(s)';
				}
				else if($time_difference_final >=60 && $time_difference_final<3600)
				{
					$minutes = floor(($time_difference_final / 60) % 60);
					$seconds = $time_difference_final % 60;
					$time_duration=$minutes.' Minute(s) '.$seconds.' Second(s)';
				}
				else
				{
					$seconds = $time_difference_final % 60;
					$time_duration=$seconds.' Second(s)';
				}
				echo "<tr>
				<td>".$att_date."</td>
				<td>".$emp_name."</td>
				<td>".$att_time."</td>
				<td>".date('H:i:s',strtotime(${check_in_time.$emp_code.$formatted_date}))."</td>
				<td>".date('H:i:s',strtotime(${check_out_time.$emp_code.$formatted_date}))."</td>
				<td>".$time_duration."</td>";
				
			  echo "</tr>";
			  }
		}
	?>
    </table>
    <br />
    <br>
<div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
/*}
else{
	echo "No Records";
}*/
mysqli_close($link);
?>


