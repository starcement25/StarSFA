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
    <form action="attendance_status_update.php" method="post">
    <input type="hidden" name="mode" value="att_status_update" />
    <table class="border" width="100%" style="border-collapse:collapse;" border="1">
    <tr><td colspan = '10' align = 'center' class = 'TDHEAD_SUB'>Attendance Dashboard From <?php echo date('d-m-Y',strtotime($start_date));?> to <?php echo date('d-m-Y',strtotime($end_date));?></td></tr>
      <tr class="TDHEAD">
      	<td>SI</td>
        <td>Emp Name</td>
        <td>Date</td>
        <td>Attendance Time</td>
        <td>Status</td>
        <td>Remarks</td>
      </tr>
    <?php
$sqlinformation="SELECT BM.branch_name,EM.emp_name,EM.emp_code,EM.dns_emp_code,EM.HQ,LO.trans_id,DATE_FORMAT(LO.date,'%T') AS time,EM.acedns,LO.latt,LO.longi,DATE_FORMAT(LO.date,'%d-%m-%Y') as date_att FROM  branch_master BM,location LO,employee_master EM WHERE EM.branch_code=BM.branch_code AND LO.emp_code=EM.emp_code 
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
		$emp_code=$rowinformation['emp_code'];
		$dns_emp_code=$rowinformation['dns_emp_code'];
		$date_att_formated=date('Y-m-d',strtotime($date_att));
		
		$emp_date_string=$emp_code.'_'.$date_att_formated;
		echo "<tr>
			<td>".$cnt."</td>
			<td>".$emp_name."</td>
			<td>".$date_att."</td>
			<td>".$check_in_time."</td>
			<td>";
			?>
			<select name="status_<?php echo $emp_date_string;?>" id="status_<?php echo $emp_date_string;?>">
                <option value="">Select</option>
               <option value="FULL DAY">FULL DAY</option>
               <option value="FULL DAY">HALF DAY</option>
                <option value="LATE">LATE</option>
                <option value="ABSENT">ABSENT</option>
                 <option value="PRESENT">PRESENT</option>
              </select>
			<?php
			echo "</td><td height=\"30\">"; ?>
            <input type="text" name="remarks_<?php echo $emp_date_string;?>" id="remarks_<?php echo $emp_date_string;?>" value="" />
            <?php
		  echo "</td></tr>";
		  echo "<input type=\"hidden\" name=\"emp_date[]\" value=".$emp_date_string.">";		  
		  $cnt++;
	  }
	  echo "<tr>
	  		<td colspan='4'></td>
			<td colspan='2' align='center' height=\"30\"><input type='submit' name='SUBMIT1' value='SUBMIT' /></td>
			</tr>";
	}
	else{
	echo "<tr><td colspan='10' align='center'>No Records</td><tr>";
	}
	?>
    </table>
    <br />
    <br>
<!--div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div-->
    <?php
mysqli_close($link);
?>


