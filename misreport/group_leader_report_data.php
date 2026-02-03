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
	$emp_condition_group_l = "  AND SUBSTRING(group_l_form_id,3,5) IN('".$employee."')";
}
	?>
     <table class="border" width="100%" style="border-collapse:collapse;" border="1" id="display_table">
      <tr class="TDHEAD">
      	<td width="9%">Date</td>
        <td width="20%">Group Leader</td>
        <td width="10%">MFM time</td>
        <td width="10%">Tent Presence</td>
        <td width="10%">NFM Time</td>
        <td width="10%">Demo Achieved</td>
        <td width="10%">Sales Done</td>
        <td width="10%">Payout</td>
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
	$sqlchkkgroupform="SELECT group_l_form_id ,SUBSTRING(group_l_form_id,3,5) as emp_code,group_l_photo_datetime,tent_photo_datetime,night_meet_photo_datetime,demo_photo_datetime,
						DATE_FORMAT(SUBSTRING(group_l_form_id,-14,8),'%Y-%m-%d') AS group_l_form_date 
						FROM group_leader_form_details WHERE DATE_FORMAT(SUBSTRING(group_l_form_id,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."'  $emp_condition_group_l 
					ORDER BY DATE_FORMAT(SUBSTRING(group_l_form_id,-14,8),'%Y-%m-%d') DESC";
	$rschkgroupform=mysqli_query($link,$sqlchkkgroupform);
	$countchkgroupform=mysqli_num_rows($rschkgroupform);
		if($countchkgroupform > 0){
			$group_l_date_array=array();
			while($rowchkchkgroupform=mysqli_fetch_assoc($rschkgroupform))
			{
				$group_l_form_emp=$rowchkchkgroupform['emp_code'];
				$group_l_form_date=$rowchkchkgroupform['group_l_form_date'];
				$group_l_photo_datetime=$rowchkchkgroupform['group_l_photo_datetime'];
				${group_l_time.$group_l_form_date.$group_l_form_emp}=substr($group_l_photo_datetime,11,8);
				$tent_photo_datetime=$rowchkchkgroupform['tent_photo_datetime'];
				${tent_time.$group_l_form_date.$group_l_form_emp}=substr($tent_photo_datetime,11,8);
				$night_meet_photo_datetime=$rowchkchkgroupform['night_meet_photo_datetime'];
				${night_meet_time.$group_l_form_date.$group_l_form_emp}=substr($rowchkchkgroupform['night_meet_photo_datetime'],11,8);
				$demo_photo_datetime=$rowchkchkgroupform['demo_photo_datetime'];
				${demo_time.$group_l_form_date.$group_l_form_emp}=substr($rowchkchkgroupform['demo_photo_datetime'],11,8);
				
				if(!in_array($group_l_form_date,$group_l_date_array))
				{
					array_push($group_l_date_array,$group_l_form_date);
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
			
			foreach($group_l_date_array as $group_l_date_val){
					echo "<tr>
						<td>".date('d-m-Y',strtotime($group_l_date_val))."</td>
						<td>".$emp_name."</td>
						<td>".${group_l_time.$group_l_date_val.$emp_code}."</td>
						<td>".${tent_time.$group_l_date_val.$emp_code}."</td>
						<td>".${night_meet_time.$group_l_date_val.$emp_code}."</td>
						<td>".${demo_time.$group_l_date_val.$emp_code}."</td>
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