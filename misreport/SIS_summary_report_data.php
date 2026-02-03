<?php
ob_start();
session_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
$current_date = date('Y-m-d');
$month_date = date('Y-m');
$current_month = date('m');
if($current_month == '01' || $current_month == '02' || $current_month == '03'){
	$previous_year = date('Y', strtotime('-1 year'));
	$previous_year_date = $previous_year."-04-01";
}
else{
	$previous_year_date = date('Y-04-01');
}
$employee = $_REQUEST['employee'];
$employee_arg = str_replace(",","#",$employee);
$employee_arg = str_replace("'","^",$employee_arg);

$subdealer = $_REQUEST['subdealer'];
//$start_date = $_REQUEST['start_date'];
//$end_date = $_REQUEST['end_date'];
$month_data = $_REQUEST['month_data'];
$monthNum=substr($month_data,4,2);
$year=substr($month_data,0,4);
$monthName = date('M', mktime(0, 0, 0, $monthNum, 10));
$month_data_processed=$monthName.'-'.substr($year,2,2);


if($_SESSION['admin_login']=="admin"){
	$emp_hierarchy='';
	$emp_hierarchy_condition='';
	$emp_hierarchy_condition_one='';
}
else
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition_one=' AND LO.emp_code IN('.$employee.')';
}
$zone = $_REQUEST['zone'];
$state = $_REQUEST['state'];
$branch = $_REQUEST['branch'];
$department = $_REQUEST['department'];

if(strpos($region,",") == FALSE)	$region = str_replace("'","",$region);
else								$region = "All";

if(strpos($zone,",") == FALSE)	$zone = str_replace("'","",$zone);
else								$zone = "All";

if(strpos($state,",") == FALSE)	$state = str_replace("'","",$state);
else								$state = "All";

if(strpos($branch,",") == FALSE)	$branch = str_replace("'","",$branch);
else								$branch = "All";

if(strpos($department,",") == FALSE)	$department = str_replace("'","",$department);
else									$department = "All";


if(strpos($employee,",") == FALSE){
	$new_emp_code = str_replace("'","",$employee);
	$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$new_emp_code."'";
	$res_emp_name = mysqli_query($link,$sql_emp_name);
	$row_emp_name = mysqli_fetch_assoc($res_emp_name);
	$new_emp_name = $row_emp_name['emp_name'];
}
else{
	$new_emp_name = "All";
}

$sql_emp_branch = "SELECT branch_code FROM employee_master WHERE emp_code IN(".$employee.")";
$res_emp_branch = mysqli_query($link,$sql_emp_branch);
$row_emp_branch = mysqli_fetch_assoc($res_emp_branch);
$branch_emp_code = $row_emp_branch['branch_code'];

$sql_branch_name = "SELECT branch_name FROM branch_master WHERE branch_code = '".$branch_emp_code."'";
$res_branch_name = mysqli_query($link,$sql_branch_name);
$row_branch_name = mysqli_fetch_assoc($res_branch_name);
$branch_name = $row_branch_name['branch_name'];

$header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Month: ".$monthName." ".$year;

$sql_sis_emp = "SELECT dns_emp_code FROM employee_master WHERE emp_code IN(".$employee.")";
$res_sis_emp = mysqli_query($link,$sql_sis_emp);
$sis_emp_count = mysqli_num_rows($res_sis_emp);
$emp_string='';
while($row_sis_emp=mysqli_fetch_assoc($res_sis_emp))
{
	$emp_string=$emp_string."'".$row_sis_emp['dns_emp_code']."'".',';
}
$emp_string=substr($emp_string,0,-1);

	?>
    <table class="border" width="100%">
      <tr class="TDHEAD">
      	<td colspan="28"><?php echo $header_string; ?></td>
      </tr>
    </table>
    <br /><br />
    <div id="display_final">
    <table width="100%" style="border-collapse:collapse;" border="1"  cellpadding="2px">
      <tr class="TDHEAD" align="center">
        <td>SI</td>
        <td>Employee Code</td>
        <td>Employee Name</td>
        <td>Month-Year</td>
        <td>Parameter 1</td>
        <td>TGT</td>
        <td>ACH</td>
        <td>ACH(%)</td>
        <td>WGT(%)</td>
        <td>SCORE(%)</td>
        <td>Parameter 2</td>
        <td>TGT</td>
        <td>ACH</td>
        <td>ACH(%)</td>
        <td>WGT(%)</td>
        <td>SCORE(%)</td>
        
        
        <td>Parameter 3</td>
        <td>TGT</td>
        <td>ACH</td>
        <td>ACH(%)</td>
        <td>WGT(%)</td>
        <td>SCORE(%)</td>
        
        <td>Parameter 4</td>
        <td>TGT</td>
        <td>ACH</td>
        <td>ACH(%)</td>
        <td>WGT(%)</td>
        <td>SCORE(%)</td>
        
        <td>Parameter 5</td>
        <td>TGT</td>
        <td>ACH</td>
        <td>ACH(%)</td>
        <td>WGT(%)</td>
        <td>SCORE(%)</td>
        
        <td>Parameter 6</td>
        <td>TGT</td>
        <td>ACH</td>
        <td>ACH(%)</td>
        <td>WGT(%)</td>
        <td>SCORE(%)</td>
        
        <td>Earning Score%</td>
        <td>Penalty(%)</td>
        <td>Final score(%)</td>
        <td>OTSI(Rs.)</td>
        <td>SIS Earning for the month(Rs.)</td>
        <td>Remarks</td>
      </tr>
    <?php
	$sqlsissummary="SELECT * FROM SIS_summary WHERE emp_code IN(".$emp_string.") AND month_year='".addslashes($month_data_processed)."'";
	$rssissummary=mysqli_query($link,$sqlsissummary);
	$countsissummary=mysqli_num_rows($rssissummary);
	if($countsissummary>0){
		$sis_count=1;
	while($rowsissummary = mysqli_fetch_assoc($rssissummary)){
		$emp_code = $rowsissummary['emp_code'];
		$name = $rowsissummary['name'];
		$month_year = $rowsissummary['month_year'];
		$sales_volume_MT = $rowsissummary['sales_volume_MT'];
		$sales_volume_TGT = $rowsissummary['sales_volume_TGT'];
		$sales_volume_ACH = $rowsissummary['sales_volume_ACH'];
		$sales_volume_ACH_percent = $rowsissummary['sales_volume_ACH_percent'];
		$sales_volume_WGT_percent = $rowsissummary['sales_volume_WGT_percent'];
		$sales_volume_SCORE_percent = $rowsissummary['sales_volume_SCORE_percent'];
		$monthly_unique_visit = $rowsissummary['monthly_unique_visit'];
		$monthly_unique_visit_TGT = $rowsissummary['monthly_unique_visit_TGT'];
		$monthly_unique_visit_ACH = $rowsissummary['monthly_unique_visit_ACH'];
		$monthly_unique_visit_ACH_percent = $rowsissummary['monthly_unique_visit_ACH_percent'];
		$monthly_unique_visit_WGT_percent = $rowsissummary['monthly_unique_visit_WGT_percent'];
		$monthly_unique_visit_SCORE_percent = $rowsissummary['monthly_unique_visit_SCORE_percent'];
		$dealer_appointment = $rowsissummary['dealer_appointment'];
		$dealer_appointment_TGT = $rowsissummary['dealer_appointment_TGT'];
		$dealer_appointment_ACH = $rowsissummary['dealer_appointment_ACH'];
		$dealer_appointment_ACH_percent = $rowsissummary['dealer_appointment_ACH_percent'];
		$dealer_appointmen_WGT_percent = $rowsissummary['dealer_appointmen_WGT_percent'];
		$dealer_appointment_SCORE_percent = $rowsissummary['dealer_appointment_SCORE_percent'];
		$earning_score_percent = $rowsissummary['earning_score_percent'];
		$penalty_percent = $rowsissummary['penalty_percent'];
		$final_score_percent = $rowsissummary['final_score_percent'];
		$OTSI = $rowsissummary['OTSI'];
		$SIS_earning_month = $rowsissummary['SIS_earning_month'];
		$remarks = $rowsissummary['remarks'];
		
		
		
		$active_dealer_count = $rowsissummary['active_dealer_count'];
		$active_dealer_count_TGT = $rowsissummary['active_dealer_count_TGT'];
		$active_dealer_count_ACH = $rowsissummary['active_dealer_count_ACH'];
		$active_dealer_count_ACH_percent = $rowsissummary['active_dealer_count_ACH_percent'];
		$active_dealer_count_WGT_percent = $rowsissummary['active_dealer_count_WGT_percent'];
		$active_dealer_count_SCORE_percent = $rowsissummary['active_dealer_count_SCORE_percent'];
	
		
		$paramiter_five = $rowsissummary['paramiter_five'];
		$five_TGT = $rowsissummary['five_TGT'];
		$five_ACH = $rowsissummary['five_ACH'];
		$five_ACH_percent = $rowsissummary['five_ACH_percent'];
		$five_WGT_percent = $rowsissummary['five_WGT_percent'];
		$five_SCORE_percent = $rowsissummary['five_SCORE_percent'];
		
		$paramiter_six = $rowsissummary['paramiter_six'];
		$six_TGT = $rowsissummary['six_TGT'];
		$six_ACH = $rowsissummary['six_ACH'];
		$six_ACH_percent = $rowsissummary['six_ACH_percent'];
		$six_WGT_percent = $rowsissummary['six_WGT_percent'];
		$six_SCORE_percent = $rowsissummary['six_SCORE_percent'];
		 
		
		
		
		
	?>
      <tr>
        <td><?php echo $sis_count; ?></td>
        <td><?php echo $emp_code; ?></td>
        <td><?php echo $name; ?></td>
        <td><?php echo $month_year; ?></td>
        <td><?php echo $sales_volume_MT; ?></td>
        <td align="right"><?php echo $sales_volume_TGT; ?></td>
        <td align="right"><?php echo $sales_volume_ACH; ?></td>
        <td align="right"><?php echo $sales_volume_ACH_percent; ?></td>
        <td align="right"><?php echo $sales_volume_WGT_percent; ?></td>
        <td align="right"><?php echo $sales_volume_SCORE_percent; ?></td>
        <td align="right"><?php echo $monthly_unique_visit; ?></td>
        <td align="right"><?php echo $monthly_unique_visit_TGT; ?></td>
        <td align="right"><?php echo $monthly_unique_visit_ACH; ?></td>
        <td align="right"><?php echo $monthly_unique_visit_ACH_percent; ?></td>
        <td align="right"><?php echo $monthly_unique_visit_WGT_percent; ?></td>
        <td align="right"><?php echo $monthly_unique_visit_SCORE_percent; ?></td>
        <td align="right"><?php echo $dealer_appointment; ?></td>
        <td align="right"><?php echo $dealer_appointment_TGT; ?></td>
        <td align="right"><?php echo $dealer_appointment_ACH; ?></td>
        <td align="right"><?php echo $dealer_appointment_ACH_percent; ?></td>
        <td align="right"><?php echo $dealer_appointmen_WGT_percent; ?></td>
        <td align="right"><?php echo $dealer_appointment_SCORE_percent; ?></td>
        
	
        
        <td align="right"><?php echo $active_dealer_count; ?></td>
        <td align="right"><?php echo $active_dealer_count_TGT; ?></td>
        <td align="right"><?php echo $active_dealer_count_ACH; ?></td>
        <td align="right"><?php echo $active_dealer_count_ACH_percentt; ?></td>
        <td align="right"><?php echo $active_dealer_count_WGT_percent; ?></td>
        <td align="right"><?php echo $active_dealer_count_SCORE_percent; ?></td>
        
        
         <td align="right"><?php echo $paramiter_five; ?></td>
        <td align="right"><?php echo $five_TGT; ?></td>
        <td align="right"><?php echo $five_ACH; ?></td>
        <td align="right"><?php echo $five_ACH_percent; ?></td>
        <td align="right"><?php echo $five_WGT_percent; ?></td>
        <td align="right"><?php echo $five_SCORE_percent; ?></td>
        
        
        
         <td align="right"><?php echo $paramiter_six; ?></td>
        <td align="right"><?php echo $six_TGT; ?></td>
        <td align="right"><?php echo $six_ACH; ?></td>
        <td align="right"><?php echo $six_ACH_percent; ?></td>
        <td align="right"><?php echo $six_WGT_percent; ?></td>
        <td align="right"><?php echo $six_SCORE_percent; ?></td>
        
        
        
        <td align="right"><?php echo $earning_score_percent; ?></td>
        <td align="right"><?php echo $penalty_percent; ?></td>
        <td align="right"><?php echo $final_score_percent; ?></td>
        <td align="right"><?php echo $OTSI; ?></td>
        <td align="right"><?php echo $SIS_earning_month; ?></td>
        <td align="right"><?php echo $remarks; ?></td>
      </tr>
      <?php 
	  	$sis_count++;
	  }
	}
else{
	echo "<tr><td colspan='18'  align='center'>No Records Found</td></tr>";
}
?>
</table>
   </div>
    <br />
    <br>
<div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
