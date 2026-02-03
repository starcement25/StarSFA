<?php
ob_start();
 
session_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
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
	$emp_condition = " AND ACJ.emp_code IN('".$employee."') ";
}
if($start_date!='' && $end_date!='')
{
	/*$date_condition=" AND SUBSTRING(ACJ.attendance_id,-14,8) >= '".str_replace("-","",$start_date)."' 
		AND SUBSTRING(ACJ.attendance_id,-14,8) <='".str_replace("-","",$end_date)."'";*/
	$date_condition=" AND SUBSTRING(ACJ.create_date,1,10) >= '".$start_date."' AND SUBSTRING(ACJ.create_date,1,10) <='".$end_date."'";	
}
else
{
	$date_condition='';
}

/*$sql_order_date = "SELECT SUBSTRING(order_no,-14,8) AS no_order_date FROM order_header WHERE ".$order_condition." (SUBSTRING(order_no,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') AND order_no LIKE 'N%'";
$res_order_date = mysqli_query($link,$sql_order_date);
while($row_order_date = mysqli_fetch_assoc($res_order_date)){
	$no_order_date = $row_order_date['no_order_date'];
	if(!in_array($no_order_date,$date_array))
		array_push($date_array,$no_order_date);
}

$sql_no_payment = "SELECT SUBSTRING(receipt_no,-14,8) AS no_payment_date FROM payment_header WHERE ".$payment_condition." (SUBSTRING(receipt_no,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') AND receipt_no LIKE 'N%'";
$res_no_payment = mysqli_query($link,$sql_no_payment);
while($row_no_payment = mysqli_fetch_assoc($res_no_payment)){
	$no_payment_date = $row_no_payment['no_payment_date'];
	if(!in_array($no_payment_date,$date_array))
		array_push($date_array,$no_payment_date);
}

sort($date_array);*/

//if(!empty($date_array)){
	?>
    <table class="border" width="100%" style="border-collapse:collapse;" border="1">
    <tr><td colspan = '14' align = 'center' class = 'TDHEAD_SUB'>Kilometer Reading report for the period 
	<?php echo date('d-m-Y',strtotime($start_date));?> to <?php echo date('d-m-Y',strtotime($end_date));?></td></tr>
      <tr class="TDHEAD">
      	<td>Date</td>
        <td>Emp Name</td>
        <td>Designation</td>
        <td>Phone</td>
        <td>Start Kilometer</td>
        <?php if(strtoupper($_SESSION['nick_name'])=='SUPERSHAKTI' || strtoupper($_SESSION['nick_name'])=='SEPL'){?>
        <td>Intra Day Kilometer</td>
        <?php }?>
	  	<td>End Kilometer</td>
        <?php if(strtoupper($_SESSION['nick_name'])=='ELEGANT'){
				$two_wheeler_allowance='3.25'; //Rs/K.M
				$four_wheeler_allowance='10'; //Rs/K.M
				
			?>
        <td>Total Km</td>
        <td>Travel mode</td>
        <td>Two-Wheeler</td>
        <td>Four-Wheeler</td>
        <?php }?>
      </tr>
    <?
	$sql_odometer = "SELECT ACJ.*,ACJ.create_date,DATE_FORMAT(SUBSTRING(ACJ.checkout_id,-14,14),'%d-%m-%Y %H:%i:%s') AS checkout_date,EM.emp_name,EM.designation,EM.phone_no FROM att_checkout_journey_info ACJ,employee_master EM WHERE ACJ.emp_code=EM.emp_code $emp_condition $date_condition ORDER BY SUBSTRING(ACJ.create_date,1,10) DESC,EM.emp_name ASC,SUBSTRING(ACJ.create_date,12,8) ASC";
	//echo $sql_odometer;
	$res_odometer = mysqli_query($link,$sql_odometer);
	$cnt_odometer=mysqli_num_rows($res_odometer);
	if($cnt_odometer >0){
		$date_emp_array=array();
	while($row_odometer = mysqli_fetch_assoc($res_odometer)){
		$emp_code = $row_odometer['emp_code'];
		
		$createdate = $row_odometer['create_date'];
		$create_date= date("d-M-Y",strtotime($createdate));
		$attendance_id= $row_odometer['attendance_id'];
		${'emp_name'.$emp_code.$create_date} = $row_odometer['emp_name'];
		${'designation'.$emp_code.$create_date} = $row_odometer['designation'];
		${'phone_no'.$emp_code.$create_date} = $row_odometer['phone_no'];
		if(strtoupper($_SESSION['nick_name'])=='ELEGANT'){ ${'vehicle_type'.$emp_code.$create_date}= $row_odometer['att_vehicle_type'];
		}
		if($attendance_id!='')
		{
			${'starting_time'.$emp_code.$create_date}= date("H:i:s",strtotime($createdate));
			${'att_starting_km'.$emp_code.$create_date} = $row_odometer['att_starting_km'];
			${'att_odometer'.$emp_code.$create_date} = $row_odometer['att_odometer'];
			${'checkout_ending_km'.$emp_code.$create_date} = $row_odometer['checkout_ending_km'];
			${'checkout_odometer'.$emp_code.$create_date} = $row_odometer['checkout_odometer'];
			${'checkout_time'.$emp_code.$create_date}= date("H:i:s",strtotime($row_odometer['checkout_date']));
			if(${'att_odometer'.$emp_code.$create_date}!='')
			{
				if(strtoupper($_SESSION['nick_name'])=='ELEGANT')
				{
				  ${'att_odometer_image'.$emp_code.$create_date} = "<a href=\"https://backup-ace.s3.ap-south-1.amazonaws.com/".strtoupper($_SESSION['nick_name'])."/".${'att_odometer'.$emp_code.$create_date}."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
				}
				else
				{
				  ${'att_odometer_image'.$emp_code.$create_date} = "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".${'att_odometer'.$emp_code.$create_date}."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";

				}
			}
			else
			{
				${'att_odometer_image'.$emp_code.$create_date}='';				
			}
			if(${'checkout_odometer'.$emp_code.$create_date}!='')
			{
				if(strtoupper($_SESSION['nick_name'])=='ELEGANT')
				{
					${'checkout_odometer_image'.$emp_code.$create_date}= "<a href=\"https://backup-ace.s3.ap-south-1.amazonaws.com/".strtoupper($_SESSION['nick_name'])."/".${'checkout_odometer'.$emp_code.$create_date}."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
				}
				else
				{
				 ${'checkout_odometer_image'.$emp_code.$create_date}= "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".${'checkout_odometer'.$emp_code.$create_date}."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
				}
			}
			else
			{
				${'checkout_odometer_image'.$emp_code.$create_date}='';				
			}
			$intra_time='';
			$intra_time_string=$emp_code.'#'.$create_date.'#'.$intra_time;
			array_push($date_emp_array,$intra_time_string);
		}
		else
		{
			$intra_time= date("H:i:s",strtotime($createdate));
			//${intra_time.$emp_code.$create_date}= date("H:i:s",strtotime($createdate));
			${'intra_km'.$emp_code.$create_date.$intra_time} = $row_odometer['att_starting_km'];
			${'intra_odometer'.$emp_code.$create_date.$intra_time} = $row_odometer['att_odometer'];
			if(${'intra_odometer'.$emp_code.$create_date.$intra_time}!='')
			{
				if(strtoupper($_SESSION['nick_name'])=='ELEGANT')
				{
					${'intra_odometer_image'.$emp_code.$create_date.$intra_time} = "<a href=\"https://backup-ace.s3.ap-south-1.amazonaws.com/".strtoupper($_SESSION['nick_name'])."/".${'intra_odometer'.$emp_code.$create_date.$intra_time}."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
				}
				else
				{
					${'intra_odometer_image'.$emp_code.$create_date.$intra_time} = "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".${'intra_odometer'.$emp_code.$create_date.$intra_time}."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
				}
			}
			else
			{
				${'intra_odometer_image'.$emp_code.$create_date.$intra_time}='';				
			}
			$intra_time_string=$emp_code.'#'.$create_date.'#'.$intra_time;
			array_push($date_emp_array,$intra_time_string);
		}
	  }
	  //print_r($date_emp_array);
	  for($i=0;$i<count($date_emp_array);$i++)
	  {
		$date_emp_val= $date_emp_array[$i];
		$date_emp_val_array=explode('#',$date_emp_val);
		$two_wheeler_total_allowance='';
		$four_wheeler_total_allowance='';		
		echo "<tr>
			<td>".$date_emp_val_array[1]."</td>
			<td>".${'emp_name'.$date_emp_val_array[0].$date_emp_val_array[1]}."</td>
			<td>".${'designation'.$date_emp_val_array[0].$date_emp_val_array[1]}."</td>
			<td>".${'phone_no'.$date_emp_val_array[0].$date_emp_val_array[1]}."</td>
			<td>".${'starting_time'.$date_emp_val_array[0].$date_emp_val_array[1]}."-".${'att_starting_km'.$date_emp_val_array[0].$date_emp_val_array[1]}." ${'att_odometer_image'.$date_emp_val_array[0].$date_emp_val_array[1]}</td>";
			if(strtoupper($_SESSION['nick_name'])=='SUPERSHAKTI' || strtoupper($_SESSION['nick_name'])=='SEPL'){
			echo"<td>".$date_emp_val_array[2]."-".${'intra_km'.$date_emp_val_array[0].$date_emp_val_array[1].$date_emp_val_array[2]}." ${'intra_odometer_image'.$date_emp_val_array[0].$date_emp_val_array[1].$date_emp_val_array[2]}</td>";
			}
			echo "<td>".${'checkout_time'.$date_emp_val_array[0].$date_emp_val_array[1]}."-".${'checkout_ending_km'.$date_emp_val_array[0].$date_emp_val_array[1]}." ${'checkout_odometer_image'.$date_emp_val_array[0].$date_emp_val_array[1]}</td>";

			if(strtoupper($_SESSION['nick_name'])=='ELEGANT'){
				if(${'checkout_ending_km'.$date_emp_val_array[0].$date_emp_val_array[1]} > 0)
				{
				$total_km=${'checkout_ending_km'.$date_emp_val_array[0].$date_emp_val_array[1]}-${'att_starting_km'.$date_emp_val_array[0].$date_emp_val_array[1]};
				}
				else $total_km=0;
				echo "<td align=\"right\">".$total_km."</td>
					<td>".${'vehicle_type'.$date_emp_val_array[0].$date_emp_val_array[1]}."</td>
				";
				if (strpos(${'vehicle_type'.$date_emp_val_array[0].$date_emp_val_array[1]}, 'Two') !== false) {
					$two_wheeler_total_allowance=number_format(($total_km*$two_wheeler_allowance),2);
				}
				if (strpos(${'vehicle_type'.$date_emp_val_array[0].$date_emp_val_array[1]}, 'Four') !== false) {
					$four_wheeler_total_allowance=number_format(($total_km*$four_wheeler_allowance),2);
				}
				echo"<td align=\"right\">".$two_wheeler_total_allowance."</td>
					<td align=\"right\">".$four_wheeler_total_allowance."</td>
				";
			}
		  echo "</tr>";
	  }
	}
	else{
	echo "<tr><td colspan='8' align='center'>No Records</td><tr>";
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


