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
	$emp_condition_tent = '';
	$emp_condition_knocking ='';
}
else{
	$order_condition = " SUBSTRING(order_no,3,5) IN('".$employee."') AND ";
	$payment_condition = " SUBSTRING(receipt_no,3,5) IN('".$employee."') AND ";
	$emp_condition = " AND EM.emp_code IN('".$employee."') ";
	$emp_condition_tent = "  AND SUBSTRING(tent_form_id,3,5) IN('".$employee."')";
	$emp_condition_caller = "  AND SUBSTRING(telecaller_form_id,3,5) IN('".$employee."')";
}
	?>
     <table class="border" width="100%" style="border-collapse:collapse;" border="1" id="display_table">
      <tr class="TDHEAD">
      	<td width="7%">Date</td>
        <td width="8%">Telecaller</td>
        <td width="7%">Total calls</td>
        <td width="7%">Connected</td>
        <td width="7%">Not Connected</td>
        <td width="7%">Interested</td>
        <td width="8%">Not interested</td>
        <td width="8%">Non Users</td>
        <td width="8%">Demo Booked</td>
       	<td width="8%">Demo Achieved</td>
        <td width="8%">Service Call booked</td>
        <td width="8%">Service Call Achieved</td>
        <td width="8%%">Payout</td>
      </tr>
    <?
	/*$sqlchktentform="SELECT tent_form_id,starting_date_time,end_date_time,SUBSTRING(tent_form_id,3,5) as emp_code,DATE_FORMAT(SUBSTRING(tent_form_id,-14,8),'%Y-%m-%d') AS tent_form_date 
					FROM tent_form_details WHERE DATE_FORMAT(SUBSTRING(tent_form_id,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."' $emp_condition_tent ";
	$rschktentform=mysqli_query($link,$sqlchktentform);
	$countchktentform=mysqli_num_rows($rschktentform);
		if($countchktentform >0){
			$tent_date_array=array();
			while($rowchktentform=mysqli_fetch_assoc($rschktentform))
			{
				$tent_form_date=$rowchktentform['tent_form_date'];
				$tent_emp=$rowchktentform['emp_code'];
				${starting_time.$tent_form_date.$tent_emp}=substr($rowchktentform['starting_date_time'],11,8);
				${ending_time.$tent_form_date.$tent_emp}=substr($rowchktentform['end_date_time'],11,8);
				
				
				if(!in_array($tent_form_date,$tent_date_array))
				{
					array_push($tent_date_array,$tent_form_date);
				}
			}
		}*/
		//print_r($tent_form_date);
		$telecaller_date_array=array();
	$sqlchkktelecallerform="SELECT count(telecaller_form_id) as tot_telecaller,SUBSTRING(telecaller_form_id,3,5) as emp_code,
					DATE_FORMAT(SUBSTRING(telecaller_form_id,-14,8),'%Y-%m-%d') AS telecaller_form_date,SUM(CASE WHEN is_connected='yes' THEN 1 ELSE 0
						END ) AS total_connected,SUM(CASE WHEN is_connected='no' THEN 1 ELSE 0
						END ) AS total_not_connected,SUM(CASE WHEN service_interest='yes' THEN 1 ELSE 0
						END ) AS total_interest,SUM(CASE WHEN service_interest='no' THEN 1 ELSE 0
						END ) AS total_not_interest
					FROM telecaller_form_details WHERE DATE_FORMAT(SUBSTRING(telecaller_form_id,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."'  $emp_condition_caller 
					GROUP BY SUBSTRING(telecaller_form_id,3,5),DATE_FORMAT(SUBSTRING(telecaller_form_id,-14,8),'%d-%m-%Y')";
	$rschkktelecallerform=mysqli_query($link,$sqlchkktelecallerform);
	$countchkktelecallerform=mysqli_num_rows($rschkktelecallerform);
		if($countchkktelecallerform > 0){
			//$tent_date_array=array();
			while($rowchkktelecallerform=mysqli_fetch_assoc($rschkktelecallerform))
			{
				$telecaller_form_emp=$rowchkktelecallerform['emp_code'];
				$telecaller_form_date=$rowchkktelecallerform['telecaller_form_date'];
				${tot_telecaller_form.$telecaller_form_date.$telecaller_form_emp}=$rowchkktelecallerform['tot_telecaller'];
				${total_connected.$telecaller_form_date.$telecaller_form_emp}=$rowchkktelecallerform['total_connected'];
				${total_not_connected.$telecaller_form_date.$telecaller_form_emp}=$rowchkktelecallerform['total_not_connected'];
				${total_interest.$telecaller_form_date.$telecaller_form_emp}=$rowchkktelecallerform['total_interest'];
				${total_not_interest.$telecaller_form_date.$telecaller_form_emp}=$rowchkktelecallerform['total_not_interest'];
				if(!in_array($telecaller_form_date,$telecaller_date_array))
				{
					array_push($telecaller_date_array,$telecaller_form_date);
				}
			}
		}	
	
	$sql_emp = "SELECT EM.emp_code, EM.dns_emp_code, EM.emp_name FROM employee_master EM WHERE 1 $emp_condition ORDER BY EM.emp_name ASC";
	$res_emp = mysqli_query($link,$sql_emp);
	$cnt_emp=mysqli_num_rows($res_emp);
	if($cnt_emp >0){
	while($row_emp = mysqli_fetch_assoc($res_emp)){
		$dns_emp_code = $row_emp['dns_emp_code'];
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
			
			foreach($telecaller_date_array as $telecaller_date_val){
					echo "<tr>
						<td>".date('d-m-Y',strtotime($telecaller_date_val))."</td>
						<td>".$emp_name."</td>
						<td>".${tot_telecaller_form.$telecaller_date_val.$emp_code}."</td>
						<td>".${total_connected.$telecaller_date_val.$emp_code}."</td>
						<td>".${total_not_connected.$telecaller_date_val.$emp_code}."</td>
						<td>".${total_interest.$telecaller_date_val.$emp_code}."</td>
						<td>".${total_not_interest.$telecaller_date_val.$emp_code}."</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					  </tr>";
			}
	  }
	}
	else{
	echo "No Records";
   }

	?>
    </table>
    <br />
    <br>
<div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
//}
mysqli_close($link);
?>