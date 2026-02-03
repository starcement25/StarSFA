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
	$emp_condition_knocking = "  AND SUBSTRING(knocking_form_id,3,5) IN('".$employee."')";
}
	?>
     <table class="border" width="100%" style="border-collapse:collapse;" border="1" id="display_table">
      <tr class="TDHEAD">
      	<td width="9%">Date</td>
         <td width="15%">Executive Name</td>
        <td width="15%">GL Name</td>
        <td width="7%">Knocking</td>
        <td width="10%">Demo Booked</td>
        <td width="10%">Demo Achieved</td>
        <td width="10%">Sales Done ( qty)</td>
        <td width="7%">Sales Value</td>
        <td width="10%">Booking Done (Qty)</td>
        <td width="7%">Payout</td>
      </tr>
    <?
	$sqlchkknockingform="SELECT count(knocking_form_id) as tot_knocking,SUBSTRING(knocking_form_id,3,5) as emp_code,DATE_FORMAT(SUBSTRING(knocking_form_id,-14,8),'%Y-%m-%d') AS knocking_form_date 
					FROM knocking_form_details WHERE DATE_FORMAT(SUBSTRING(knocking_form_id,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."'  $emp_condition_knocking 
					GROUP BY SUBSTRING(knocking_form_id,3,5),DATE_FORMAT(SUBSTRING(knocking_form_id,-14,8),'%d-%m-%Y')";
	$rschkknockingform=mysqli_query($link,$sqlchkknockingform);
	$countchkknockingform=mysqli_num_rows($rschkknockingform);
		if($countchkknockingform > 0){
			$knocking_form_date_array=array();
			while($rowchkknockingform=mysqli_fetch_assoc($rschkknockingform))
			{
				$knocking_emp=$rowchkknockingform['emp_code'];
				$knocking_form_date=$rowchkknockingform['knocking_form_date'];
				${tot_knocking.$knocking_form_date.$knocking_emp}=$rowchkknockingform['tot_knocking'];
				
				if(!in_array($knocking_form_date,$knocking_form_date_array))
				{
					array_push($knocking_form_date_array,$knocking_form_date);
				}
			}
		}
	
	$sql_gl = "SELECT emp_name FROM employee_master WHERE designation='Group Leader'";
	$res_gl = mysqli_query($link,$sql_gl);
	$cnt_gl=mysqli_num_rows($res_gl);
	if($cnt_gl >0){
	$row_gl = mysqli_fetch_assoc($res_gl);
	$gl_name=$row_gl['emp_name'];
	}
			
	$sql_emp = "SELECT EM.emp_code, EM.dns_emp_code, EM.emp_name FROM employee_master EM WHERE 1 $emp_condition ORDER BY EM.emp_name ASC";
	$res_emp = mysqli_query($link,$sql_emp);
	$cnt_emp=mysqli_num_rows($res_emp);
	if($cnt_emp >0){
	while($row_emp = mysqli_fetch_assoc($res_emp)){
		$dns_emp_code = $row_emp['dns_emp_code'];
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		
			
			foreach($knocking_form_date_array as $knocking_form_date_val){
					echo "<tr>
						<td>".date('d-m-Y',strtotime($knocking_form_date_val))."</td>
						<td>".$emp_name."</td>
						<td>".$gl_name."</td>
						<td>".${tot_knocking.$knocking_form_date_val.$emp_code}."</td>
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