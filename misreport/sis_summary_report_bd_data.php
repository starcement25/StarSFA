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

$sql_sis_emp = "SELECT dns_emp_code,branch_code FROM employee_master WHERE emp_code IN(".$employee.")";
$res_sis_emp = mysqli_query($link,$sql_sis_emp);
$sis_emp_count = mysqli_num_rows($res_sis_emp);
$emp_string='';
while($row_sis_emp=mysqli_fetch_assoc($res_sis_emp))
{
	$emp_string=$emp_string."'".$row_sis_emp['dns_emp_code']."'".',';
}
$emp_string=substr($emp_string,0,-1);

$header_id = "0";
    $sqlsissummary="SELECT * FROM  BD_SIS_summary WHERE emp_code IN(".$emp_string.") AND month_year='".addslashes($month_data_processed)."'";
	$rssissummary=mysqli_query($link,$sqlsissummary);
	$countsissummary=mysqli_num_rows($rssissummary);
	while($row_summary = mysqli_fetch_assoc($rssissummary)){
			$header_id=$row_summary['header_id'];
	}
	
	$sqlsisheader="SELECT * FROM  BD_SIS_summary_header WHERE header_id='".$header_id."'";
	$rssisheader=mysqli_query($link,$sqlsisheader);
	$countsisheadery=mysqli_num_rows($rssisheader);
	while($row_header = mysqli_fetch_assoc($rssisheader)){
	    
	      $emp_code=$row_header['emp_code'];
	    $name=$row_header['name'];
	    $month_year =$row_header['month_year'];
	    $parameter_1=$row_header['parameter_1'];
	    $sales_volume_TGT=$row_header['sales_volume_TGT'];
	    $sales_volume_ACTUAL=$row_header['sales_volume_ACTUAL'];
	    $sis_slab_percent=$row_header['sis_slab_percent'];
	    $Premium_sales_conversion_target=$row_header['Premium_sales_conversion_target'];
	    $sales_volume_SCORE_percent=$row_header['sales_volume_SCORE_percent'];
	    $parameter_2=$row_header['parameter_2'];
	    $monthly_unique_visit_TGT=$row_header['monthly_unique_visit_TGT'];
	    $monthly_unique_visit_ACH=$row_header['monthly_unique_visit_ACH'];
	    $activities_sis_slab_percent=$row_header['activities_sis_slab_percent'];
	    $activities=$row_header['activities'];
	    $monthly_unique_visit_SCORE_percent=$row_header['monthly_unique_visit_SCORE_percent'];
	    $parameter_3=$row_header['parameter_3'];
	    $dealer_appointment_ACTUAL=$row_header['dealer_appointment_ACTUAL'];
	    $dealer_appointment_sis_slab_percent=$row_header['dealer_appointment_sis_slab_percent'];
	    $influencer_registration=$row_header['influencer_registration'];
	    $dealer_appointment_SCORE_percent=$row_header['dealer_appointment_SCORE_percent'];
	    $parameter_4=$row_header['parameter_4'];
	    $active_dealer_count_TGT=$row_header['active_dealer_count_TGT'];
			$active_dealer_count_ACH=$row_header['active_dealer_count_ACH'];
	    $active_dealer_count_sis_slab_percent=$row_header['active_dealer_count_sis_slab_percent'];
	    $active_dealer_growth=$row_header['active_dealer_growth'];
	    $active_dealer_count_SCORE_percent=$row_header['active_dealer_count_SCORE_percent'];
	    
	    $paramiter_five=$row_header['paramiter_five'];
	    	$five_TGT=$row_header['five_TGT'];
			$five_ACH=$row_header['five_ACH'];
	    $five_sis_slab_percent=$row_header['five_sis_slab_percent'];
	    $active_influencer_growth=$row_header['active_influencer_growth'];
	    $five_SCORE_percent=$row_header['five_SCORE_percent'];
	    $paramiter_six=$row_header['paramiter_six'];
	    $six_ACTUAL=$row_header['six_ACTUAL'];
	    $six_slab_percent=$row_header['six_slab_percent'];
	    $six_SCORE_percent=$row_header['six_SCORE_percent'];
	    $earning_score_percent=$row_header['earning_score_percent'];
	    $penalty_percent=$row_header['penalty_percent'];
	    $final_score_percent=$row_header['final_score_percent'];
	    $remarks=$row_header['remarks'];
	   
		
			
			
			
			
	}

	?>
    <table class="border" width="100%">
      <tr class="TDHEAD">
      	<td colspan="50"><?php echo $header_string; ?></td>
      </tr>
    </table>
    <br /><br />
    <div id="display_final">
    <table width="100%" style="border-collapse:collapse;" border="1"  cellpadding="2px">
      <tr class="TDHEAD" align="center">
        <td>SI</td>
        <td><?php echo $emp_code; ?></td>
        <td><?php echo $name; ?></td>
        <td><?php echo $month_year; ?></td>
        <td>Branch</td>
        <td><?php echo $parameter_1; ?></td>
        <td><?php echo $sales_volume_TGT; ?></td>
        <td><?php echo $sales_volume_ACTUAL; ?></td>
        <td><?php echo $sis_slab_percent; ?></td>
        <td><?php echo $Premium_sales_conversion_target; ?></td>
        <td><?php echo $sales_volume_SCORE_percent; ?></td>
        <td><?php echo $parameter_2; ?></td>
        <td><?php echo $monthly_unique_visit_TGT; ?></td>
        <td><?php echo $monthly_unique_visit_ACH; ?></td>
        <td><?php echo $activities_sis_slab_percent; ?></td>
        <td><?php echo $activities; ?></td>
        <td><?php echo $monthly_unique_visit_SCORE_percent; ?></td>
        <td><?php echo $parameter_3; ?></td>
        <td><?php echo $dealer_appointment_ACTUAL; ?></td>
        <td><?php echo $dealer_appointment_sis_slab_percent; ?></td>
        <td><?php echo $influencer_registration; ?></td>
        <td><?php echo $dealer_appointment_SCORE_percent; ?></td>
        <td><?php echo $parameter_4; ?></td>
        <td><?php echo $active_dealer_count_TGT; ?></td>
        <td><?php echo $active_dealer_count_ACH; ?></td>
        <td><?php echo $active_dealer_count_sis_slab_percent; ?></td>
        <td><?php echo $active_dealer_growth; ?></td>
        <td><?php echo $active_dealer_count_SCORE_percent; ?></td>
        
        <td><?php echo $paramiter_five; ?></td>
        <td><?php echo $five_TGT; ?></td>
        <td><?php echo $five_ACH; ?></td>
        <td><?php echo $five_sis_slab_percent; ?></td>
        <td><?php echo $active_influencer_growth; ?></td>
        <td><?php echo $five_SCORE_percent; ?></td>
        
        <td><?php echo $paramiter_six; ?></td>
        <td><?php echo $six_ACTUAL; ?></td>
        <td><?php echo $six_slab_percent; ?></td>
        <td><?php echo $six_SCORE_percent; ?></td>
        <td><?php echo $earning_score_percent; ?></td>
        <td><?php echo $penalty_percent; ?></td>
        <td><?php echo $final_score_percent; ?></td>
		 <td><?php echo $remarks; ?></td>
      </tr>
    <?php
	$sqlsissummary="SELECT * FROM  BD_SIS_summary WHERE emp_code IN(".$emp_string.") AND month_year='".addslashes($month_data_processed)."'";
	$rssissummary=mysqli_query($link,$sqlsissummary);
	$countsissummary=mysqli_num_rows($rssissummary);
	if($countsissummary>0){
		$sis_count=1;
	while($row_summary = mysqli_fetch_assoc($rssissummary)){
			$emp_code=$row_summary['emp_code'];
			$name = $row_summary['name'];
			$month_year=$row_summary['month_year'];
			$parameter_1=$row_summary['parameter_1'];
			$sales_volume_TGT=$row_summary['sales_volume_TGT'];
			$sales_volume_ACTUAL=$row_summary['sales_volume_ACTUAL'];
			$sis_slab_percent=$row_summary['sis_slab_percent'];
			$premium_sales_conversion_target=$row_summary['Premium_sales_conversion_target'];
			$sales_volume_SCORE_percent=$row_summary['sales_volume_SCORE_percent'];
			$parameter_2=$row_summary['parameter_2'];
			$monthly_unique_visit_TGT=$row_summary['monthly_unique_visit_TGT'];
			$monthly_unique_visit_ACH=$row_summary['monthly_unique_visit_ACH'];
			$activities_sis_slab_percent=$row_summary['activities_sis_slab_percent'];
			$activities=$row_summary['activities'];
			$monthly_unique_visit_SCORE_percent=$row_summary['monthly_unique_visit_SCORE_percent'];
			$parameter_3=$row_summary['parameter_3'];
			$dealer_appointment_ACTUAL=$row_summary['dealer_appointment_ACTUAL'];
			$dealer_appointment_sis_slab_percent=$row_summary['dealer_appointment_sis_slab_percent'];
			$influencer_registration=$row_summary['influencer_registration'];
			$dealer_appointment_SCORE_percent=$row_summary['dealer_appointment_SCORE_percent'];
			$parameter_4=$row_summary['parameter_4'];
			$active_dealer_count_TGT=$row_summary['active_dealer_count_TGT'];
			$active_dealer_count_ACH=$row_summary['active_dealer_count_ACH'];
			$active_dealer_count_sis_slab_percent=$row_summary['active_dealer_count_sis_slab_percent'];
			$active_dealer_growth=$row_summary['active_dealer_growth'];
			$active_dealer_count_SCORE_percent=$row_summary['active_dealer_count_SCORE_percent'];
			
			$paramiter_five=$row_summary['paramiter_five'];
			$five_TGT=$row_summary['five_TGT'];
			$five_ACH=$row_summary['five_ACH'];
			$five_sis_slab_percent=$row_summary['five_sis_slab_percent'];
			$active_influencer_growth=$row_summary['active_influencer_growth'];
			$five_SCORE_percent=$row_summary['five_SCORE_percent'];
			
			$paramiter_six=$row_summary['paramiter_six'];
			$six_ACTUAL=$row_summary['six_ACTUAL'];
			$six_slab_percent=$row_summary['six_slab_percent'];
			$six_SCORE_percent=$row_summary['six_SCORE_percent'];
			
			
			$earning_score_percent=$row_summary['earning_score_percent'];
			$penalty_percent=$row_summary['penalty_percent'];
			$final_score_percent=$row_summary['final_score_percent'];
			$remarks=$row_summary['remarks'];
			
			
			$sql_branch= "SELECT branch_code FROM employee_master WHERE dns_emp_code ='".$emp_code."'";
			//echo $sql_branch;
        $res_branch_code = mysqli_query($link,$sql_branch);
        $row_branch_code = mysqli_fetch_assoc($res_branch_code);
        $branch_code=$row_branch_code['branch_code'];
        $branch_name = "";
        if(strpos($branch_code,',')!=false){
				$branch_code_Arr=explode(',',$branch_code);
				
				for($cn=0;$cn<count($branch_code_Arr);$cn++)
				{
					$sql_branch= "SELECT branch_name FROM `branch_master` where branch_code='".$branch_code_Arr[$cn]."'";
			//echo $sql_branch;
                    $res_branch = mysqli_query($link,$sql_branch);
                    $row_branch = mysqli_fetch_assoc($res_branch);
                    if($row_branch['branch_name']!=""){
                    $branch_name=$row_branch['branch_name'].",".$branch_name." ";
                        
                    }
				}
        }else{
            $sql_branch= "SELECT branch_name FROM `branch_master` where branch_code='".$branch_code."'";
			//echo $sql_branch;
                    $res_branch = mysqli_query($link,$sql_branch);
                    $row_branch = mysqli_fetch_assoc($res_branch);
                    if($row_branch['branch_name']!=""){
                    $branch_name=$row_branch['branch_name'].",".$branch_name;
                    }
        }
			
			//$branch_name=substr($branch_name,0,-1);
		
	?>
      <tr>
        <td><?php echo $sis_count; ?></td>
        <td><?php echo $emp_code; ?></td>
        <td><?php echo $name; ?></td>
        <td><?php echo $month_year; ?></td>
        <td><?php echo $branch_name; ?></td>
        <td><?php echo $parameter_1; ?></td>
        <td align="right"><?php echo $sales_volume_TGT; ?></td>
        <td align="right"><?php echo $sales_volume_ACTUAL; ?></td>
        <td align="right"><?php echo $sis_slab_percent; ?></td>
        <td align="right"><?php echo $premium_sales_conversion_target; ?></td>
        <td align="right"><?php echo $sales_volume_SCORE_percent; ?></td>
        <td align="right"><?php echo $parameter_2; ?></td>
        <td align="right"><?php echo $monthly_unique_visit_TGT; ?></td>
        <td align="right"><?php echo $monthly_unique_visit_ACH; ?></td>
        <td align="right"><?php echo $activities_sis_slab_percent; ?></td>
        <td align="right"><?php echo $activities; ?></td>
        <td align="right"><?php echo $monthly_unique_visit_SCORE_percent; ?></td>
        <td align="right"><?php echo $parameter_3; ?></td>
        <td align="right"><?php echo $dealer_appointment_ACTUAL; ?></td>
        <td align="right"><?php echo $dealer_appointment_sis_slab_percent; ?></td>
        <td align="right"><?php echo $influencer_registration; ?></td>
        <td align="right"><?php echo $dealer_appointment_SCORE_percent; ?></td>
        
        <td align="right"><?php echo $parameter_4; ?></td>
        <td align="right"><?php echo $active_dealer_count_TGT; ?></td>
        <td align="right"><?php echo $active_dealer_count_ACH; ?></td>
        <td align="right"><?php echo $active_dealer_count_sis_slab_percent; ?></td>
        <td align="right"><?php echo $active_dealer_growth; ?></td>
        <td align="right"><?php echo $active_dealer_count_SCORE_percent; ?></td>
        
         <td align="right"><?php echo $paramiter_five; ?></td>
        <td align="right"><?php echo $five_TGT; ?></td>
        <td align="right"><?php echo $five_ACH; ?></td>
        <td align="right"><?php echo $five_sis_slab_percent; ?></td>
        <td align="right"><?php echo $active_influencer_growth; ?></td>
        <td align="right"><?php echo $five_SCORE_percent; ?></td>
        
         <td align="right"><?php echo $paramiter_six; ?></td>
        <td align="right"><?php echo $six_ACTUAL; ?></td>
        <td align="right"><?php echo $six_slab_percent; ?></td>
        <td align="right"><?php echo $six_SCORE_percent; ?></td>
        
        <td align="right"><?php echo $earning_score_percent; ?></td>
        <td align="right"><?php echo $penalty_percent; ?></td>
        <td align="right"><?php echo $final_score_percent; ?></td>
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
