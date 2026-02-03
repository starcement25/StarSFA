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
      	<td width="10%">Date</td>
        <td width="15%">Executive</td>
        <td width="10%">No of Door Knocking</td>
        <td width="9%">Demo Achieved</td>
        <td width="9%">Tent In time</td>
        <td width="9%">Tent Out time</td>
        <td width="8%">Data collected</td>
        <td width="10%">Total </td>
       	<td width="10%">Total Demo Achieved</td>
        <td width="10%%">Payout</td>
      </tr>
    <?
	$sqlchktentform="SELECT tent_form_id,starting_date_time,end_date_time,SUBSTRING(tent_form_id,3,5) as emp_code,DATE_FORMAT(SUBSTRING(tent_form_id,-14,8),'%Y-%m-%d') AS tent_form_date 
					FROM tent_form_details WHERE DATE_FORMAT(SUBSTRING(tent_form_id,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."' $emp_condition_tent ";
	$rschktentform=mysql_query($sqlchktentform);
	$countchktentform=mysql_num_rows($rschktentform);
		if($countchktentform >0){
			$tent_date_array=array();
			while($rowchktentform=mysql_fetch_array($rschktentform))
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
		}
		//print_r($tent_form_date);
	$sqlchkknockingform="SELECT count(knocking_form_id) as tot_knocking,SUBSTRING(knocking_form_id,3,5) as emp_code,DATE_FORMAT(SUBSTRING(knocking_form_id,-14,8),'%Y-%m-%d') AS knocking_form_date 
					FROM knocking_form_details WHERE DATE_FORMAT(SUBSTRING(knocking_form_id,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."'  $emp_condition_knocking 
					GROUP BY SUBSTRING(knocking_form_id,3,5),DATE_FORMAT(SUBSTRING(knocking_form_id,-14,8),'%d-%m-%Y')";
	$rschkknockingform=mysql_query($sqlchkknockingform);
	$countchkknockingform=mysql_num_rows($rschkknockingform);
		if($countchkknockingform > 0){
			//$tent_date_array=array();
			while($rowchkknockingform=mysql_fetch_array($rschkknockingform))
			{
				$knocking_emp=$rowchkknockingform['emp_code'];
				$knocking_form_date=$rowchkknockingform['knocking_form_date'];
				${tot_knocking.$knocking_form_date.$knocking_emp}=$rowchkknockingform['tot_knocking'];
			}
		}	
	
	$sql_emp = "SELECT EM.emp_code, EM.dns_emp_code, EM.emp_name FROM employee_master EM WHERE 1 $emp_condition ORDER BY EM.emp_name ASC";
	$res_emp = mysql_query($sql_emp);
	$cnt_emp=mysql_num_rows($res_emp);
	if($cnt_emp >0){
	while($row_emp = mysql_fetch_array($res_emp)){
		$dns_emp_code = $row_emp['dns_emp_code'];
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
			
			foreach($tent_date_array as $tent_date_val){
				$demo_ach = 0;
				if(${starting_time.$tent_date_val.$emp_code}!='')
				{
					echo "<tr>
						<td>".date('d-m-Y',strtotime($tent_date_val))."</td>
						<td>".$emp_name."</td>
						<td>".${tot_knocking.$tent_date_val.$emp_code}."</td>
						<td>".$demo_ach."</td>
						<td>".${starting_time.$tent_date_val.$emp_code}."</td>
						<td>".${ending_time.$tent_date_val.$emp_code}."</td>
						<td></td>
						<td>".${tot_knocking.$tent_date_val.$emp_code}."</td>
						<td>0</td>
						<td></td>
					  </tr>";
				}
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
mysql_close($link);
?>


