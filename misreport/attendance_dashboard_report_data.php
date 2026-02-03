<?php
ob_start();
session_start();
require("adminUtils.php");

$employee = $_REQUEST['employee'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$date_array = array();
if($employee == 'all'){
	$emp_condition = '';
}
else{
	$emp_condition = " AND LO.emp_code IN(".$employee.") ";
}
if($start_date!='' && $end_date!='')
{
	$date_condition=" AND SUBSTRING(LO.trans_id,-14,8) >= '".str_replace("-","",$start_date)."' 
		AND SUBSTRING(LO.trans_id,-14,8) <='".str_replace("-","",$end_date)."'";
}
else
{
	$date_condition='';
}
	?>
    <table class="border" width="100%" style="border-collapse:collapse;" border="1">
    <tr><td colspan = '10' align = 'center' class = 'TDHEAD_SUB'>Attendance Dashboard From <?php echo date('d-m-Y',strtotime($start_date));?> to <?php echo date('d-m-Y',strtotime($end_date));?></td></tr>
      <tr class="TDHEAD">
      	<td>SI</td>
        <td>Branch</td>
        <td>Name of TSM/LAS</td>
        <td>Date</td>
        <td>Check in Time</td>
        <td>Check in Location</td>
	  	<td>Check out/last outlet time</td>
        <td>Check out /last outlet location</td>
        <td>Total Time spent</td>
      </tr>
    <?php
$sqlinformation="SELECT BM.branch_name,EM.emp_name,EM.emp_code,EM.dns_emp_code,EM.HQ,LO.trans_id,DATE_FORMAT(LO.date,'%T') AS time,EM.acedns,LO.latt,LO.longi,
					LO.address,DATE_FORMAT(LO.date,'%d-%m-%Y') as date_att FROM  branch_master BM,location LO,employee_master EM WHERE EM.branch_code=BM.branch_code AND LO.emp_code=EM.emp_code 
					AND LO.trans_id LIKE 'A%' AND SUBSTRING(EM.emp_code,1,1)!='C' ".$emp_condition.$date_condition." 
					ORDER BY EM.emp_name ASC,DATE_FORMAT(LO.date,'%d-%m-%Y') DESC ";
	$resinformation=mysqli_query($link,$sqlinformation) or die(mysqli_error()." Error in select transaction information: ".$sqlinformation);
	$count=mysqli_num_rows($resinformation);
	if($count >0){
		$cnt=1;
	while($rowinformation = mysqli_fetch_assoc($resinformation)){
		$emp_name = $rowinformation['emp_name'];
		$branch_name = $rowinformation['branch_name'];
		$date_att = $rowinformation['date_att'];
		$check_in_time = $rowinformation['time'];
		$check_in_address = $rowinformation['address'];
		//$check_in_address='';
		$emp_code=$rowinformation['emp_code'];
		$dns_emp_code=$rowinformation['dns_emp_code'];
		$date_att_formated=date('Y-m-d',strtotime($date_att));
		
		$sql_checkout = "SELECT SUBSTRING(LO.date,12) AS checkout_time,trans_id,latt,longi,address FROM location LO WHERE LO.emp_code = '".$emp_code."' 
						AND LO.trans_id LIKE 'CH%' AND SUBSTRING(LO.trans_id,-14,8)= '".str_replace("-","",$date_att_formated)."'";
		$res_checkout = mysqli_query($link,$sql_checkout);
		$row_checkout = mysqli_fetch_assoc($res_checkout);
		$check_out_time = $row_checkout['checkout_time'];
		$chk_out_trans_id=$row_checkout['trans_id'];
		$chk_out_latt=$row_checkout['latt'];
		$chk_out_longi=$row_checkout['longi'];
		$check_out_address = $row_checkout['address'];
		//$check_out_address='';
		if($check_out_time == '')
			$check_out_time = '--';
			
		if($check_out_time != '--')
		{
			$time_difference=strtotime($check_out_time)-strtotime($check_in_time);
			$time_difference_final=$time_difference;
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
		}
		else
		{
			$time_duration='--';
		}	

		echo "<tr>
			<td>".$cnt."</td>
			<td>".$branch_name."</td>
			<td>".$emp_name."</td>
			<td>".$date_att."</td>
			<td>".$check_in_time."</td>
			<td>".$check_in_address."</td>
			<td>".$check_out_time."</td>
			<td>".$check_out_address."</td>
			<td>".$time_duration."</td>
		  </tr>";
		  $cnt++;
	  }
	}
	else{
	echo "<tr><td colspan='10' align='center'>No Records</td><tr>";
	}
	?>
    </table>
    <br />
    <br>
<div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
mysqli_close($link);
?>


