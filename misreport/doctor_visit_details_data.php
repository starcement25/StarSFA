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
	if(strtoupper($_SESSION['admin_login'])=="ADMIN"){
		$emp_condition = '';
	}
	else{
		$emp_hierarchy_value=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_condition = " AND SUBSTRING(DV.trans_id,3,5) IN(".$emp_hierarchy_value.") ";
	}
}
else{
	$emp_condition = " AND SUBSTRING(DV.trans_id,3,5) IN('".$employee."') ";
}
if($start_date!='' && $end_date!='')
{
	$date_condition=" AND SUBSTRING(DV.trans_id,-14,8) >= '".str_replace("-","",$start_date)."' 
		AND SUBSTRING(DV.trans_id,-14,8) <='".str_replace("-","",$end_date)."'";
	//$date_condition=" AND SUBSTRING(ACJ.create_date,1,10) >= '".$start_date."' AND SUBSTRING(ACJ.create_date,1,10) <='".$end_date."'";	
}
else
{
	$date_condition='';
}
	?>
    <table class="border" width="100%" style="border-collapse:collapse;" border="1">
    <tr><td colspan = '7' align = 'center' class = 'TDHEAD_SUB'>Doctor visit report for the period 
	<?php echo date('d-m-Y',strtotime($start_date));?> to <?php echo date('d-m-Y',strtotime($end_date));?></td></tr>
      <tr class="TDHEAD">
      	<td>Date</td>
        <td>Emp Name</td>
        <td>Doctor Name</td>
        <td>Route Name</td>
        <td>Remarks</td>
        <td>Other Remarks</td>
        <td>Met with</td>
      </tr>
    <?
	$sql_doctor_visit = "SELECT DV.remarks,DV.other_remarks,DV.met_with,DATE_FORMAT(SUBSTRING(DV.trans_id,-14,14),'%d-%m-%Y %H:%i:%s') AS doctor_visit_date,EM.emp_name,CM.customer_name,RM.route_name 
				FROM doctor_visit_details DV,employee_master EM,customer_master CM,route_master RM WHERE SUBSTRING(DV.trans_id,3,5)=EM.emp_code AND CM.customer_code=DV.customer_code AND CM.route_code=RM.route_code $emp_condition $date_condition ORDER BY DATE_FORMAT(SUBSTRING(DV.trans_id,-14,14),'%d-%m-%Y %H:%i:%s') DESC";
	$res_doctor_visit = mysqli_query($link,$sql_doctor_visit);
	$cnt_doctor_visit=mysqli_num_rows($res_doctor_visit);
	if($cnt_doctor_visit >0){
	while($row_doctor_visit = mysqli_fetch_assoc($res_doctor_visit)){
		$doctor_visit_date = $row_doctor_visit['doctor_visit_date'];
		$emp_name = $row_doctor_visit['emp_name'];
		$customer_name= $row_doctor_visit['customer_name'];
		$route_name= $row_doctor_visit['route_name'];
		$remarks= $row_doctor_visit['remarks'];
		$other_remarks= $row_doctor_visit['other_remarks'];
		$met_with= $row_doctor_visit['met_with'];
		
		echo "<tr>
			<td>".$doctor_visit_date."</td>
			<td>".$emp_name."</td>
			<td>".$customer_name."</td>
			<td>".$route_name."</td>
			<td>".$remarks."</td>
			<td>".$other_remarks."</td>
			<td>".$met_with."</td>
		  </tr>";
	  }
	}
	else{
	echo "<tr><td colspan='6' align='center'>No Records</td><tr>";
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


