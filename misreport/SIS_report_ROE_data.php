<?php
ob_start();
session_start();
require("adminUtils.php");

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

$year_month_split = explode("-",$month_data);
$monthNum  = $year_month_split[1];
$year = $year_month_split[0];
$monthName = date('M', mktime(0, 0, 0, $monthNum, 10));

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
$region = $_REQUEST['region'];
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

$header_string = "Region:".$region."&nbsp;&nbsp;Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Month: ".$monthName." ".$year;

$sql_dealer = "SELECT dns_customer_code, customer_name FROM customer_master WHERE customer_code = '".$rds_tag."'";
	$res_dealer = mysqli_query($link,$sql_dealer);
	$row_dealer = mysqli_fetch_assoc($res_dealer);
	$dealer_name = $row_dealer['customer_name'];
	$dns_customer_code = $row_dealer['dns_customer_code'];
	
$sql_sis_emp = "SELECT emp_code,emp_name,dns_emp_code,branch_code,level FROM employee_master WHERE level <>'' AND emp_code IN(".$employee.")";
$res_sis_emp = mysqli_query($link,$sql_sis_emp);
$sis_emp_check = mysqli_num_rows($res_sis_emp);
	?>
    <div id="display_final">
    <table width="100%" style="border-collapse:collapse;" border="1"  cellpadding="2px">
     <tr class="TDHEAD">
      	<td colspan="18"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD" align="center">
        <td>SI</td>
        <td>Employee Code</td>
        <td>Employee Name</td>
        <td>Base Volume TGT</td>
        <td>Volume ACHV</td>
        <td>ACHV %</td>
        <td>Monthly Unique Visit TGT</td>
        <td>Monthly Unique Visit ACHV</td>
        <td>ACHV %</td>
        <td>Dealer Appointment TGT</td>
        <td>Dealer Appointment ACHV</td>
        <td>ACHV %</td>
        <td>SIS %</td>
        <td>On Target Sales Incentive</td>
        <td>SIS Earned</td>
        <td>PENALTY %</td>
        <td>PENALTY Deducted</td>
        <td>Net SIS Earned</td>
      </tr>
    <?php
	if($sis_emp_check>0){
		$sis_count=1;
		$year=substr($month_data,0,4);
		$monthval=substr($month_data,5,2);
		if($monthval=='01'){
			$columnvolachv='jan_vol_achievement';
			$columnvoltgt='jan_vol_target';
		}
		if($monthval=='02'){
			$columnvolachv='feb_vol_achievement';
			$columnvoltgt='feb_vol_target';
		}
		if($monthval=='03'){
			 $columnvolachv='mar_vol_achievement';
			 $columnvoltgt='mar_vol_target';
		}
		if($monthval=='04'){ 
			$columnvolachv='apr_vol_achievement';
			$columnvoltgt='apr_vol_target';
		}
		if($monthval=='05'){ 
			$columnvolachv='may_vol_achievement';
			$columnvoltgt='may_vol_target';
		}
		if($monthval=='06'){
			 $columnvolachv='jun_vol_achievement';
			 $columnvoltgt='jun_vol_target';
		}
		if($monthval=='07'){
			 $columnvolachv='jul_vol_achievement';
			 $columnvoltgt='jul_vol_target';
		}
		if($monthval=='08'){
			 $columnvolachv='aug_vol_achievement';
			 $columnvoltgt='aug_vol_target';
		}
		if($monthval=='09'){ 
			$columnvolachv='sep_vol_achievement';
			$columnvoltgt='sep_vol_target';
			$columnvisitachv='sep_visit_achievement';
			$columnvisittgt='sep_visit_target';
			$columndealertgt='sep_dealer_target';
			$columndealerachv='sep_dealer_achievement';
			$columnincentive='sep_incentive';
			$columnl2penalty='sep_l2_penalty';
			$columnl3abovepenalty='sep_l3_above_penalty';
		}
		if($monthval=='10'){ 
			$columnvolachv='oct_vol_achievement';
			$columnvoltgt='oct_vol_target';
			$columnvisitachv='oct_visit_achievement';
			$columnvisittgt='oct_visit_target';
			$columndealertgt='oct_dealer_target';
			$columndealerachv='oct_dealer_achievement';
			$columnincentive='oct_incentive';
			$columnl2penalty='oct_l2_penalty';
			$columnl3abovepenalty='oct_l3_above_penalty';
		}
		if($monthval=='11'){
			 $columnvolachv='nov_vol_achievement';
			  $columnvoltgt='nov_vol_target';
		}
		if($monthval=='12'){ 
			$columnvolachv='dec_vol_achievement';
			 $columnvoltgt='dec_vol_target';
		}
	while($row_sis_emp = mysqli_fetch_assoc($res_sis_emp)){
		$emp_code = $row_sis_emp['emp_code'];
		$dns_emp_code = $row_sis_emp['dns_emp_code'];
		$emp_name = $row_sis_emp['emp_name'];
		$branch_code_all = $row_sis_emp['branch_code'];
		$branch_code_all_array=explode(',',$branch_code_all);
		$branch_code_string='';
		foreach($branch_code_all_array as $branch_code_val)
		{
			$sql_emp_branch = "SELECT dns_branch_code FROM branch_master WHERE branch_code='".$branch_code_val."'";
			$res_emp_branch = mysqli_query($link,$sql_emp_branch);
			$row_emp_branch = mysqli_fetch_assoc($res_emp_branch);
			$branch_emp_code = $row_emp_branch['dns_branch_code'];
			$branch_code_string=$branch_code_string.$branch_emp_code.',';
		}
		$branch_code_string=substr($branch_code_string,0,-1);
		
		$level = $row_sis_emp['level'];
		if(substr($level,1,1)=='2') $penaltycolumn=$columnl2penalty;
		if(substr($level,1,1)=='3') $penaltycolumn=$columnl3abovepenalty;
		
		$sqlbranchvoltgtachv="SELECT SUM($columnvoltgt) AS  base_vol_tgt,SUM($columnvolachv) AS  base_vol_achv FROM sis_branch_volume_target_ach  WHERE  
							FIND_IN_SET(branch_code,'".$branch_code_string."') AND year='".$year."'";
		$rsbranchvoltgtachv=mysqli_query($link,$sqlbranchvoltgtachv);
		$rowbranchvoltgtachv=mysqli_fetch_assoc($rsbranchvoltgtachv);
		$base_vol_tgt=$rowbranchvoltgtachv['base_vol_tgt'];
		$base_vol_achv=$rowbranchvoltgtachv['base_vol_achv'];
		if($base_vol_tgt!='' || $base_vol_achv!=''){
		if($base_vol_achv < $base_vol_tgt) 	$base_achv_percent='0';
		if($base_vol_achv==$base_vol_tgt) 	$base_achv_percent='100';
		if($base_vol_achv > $base_vol_tgt){ 
			$base_achv_percent=($base_vol_achv/$base_vol_tgt)*100;
			if($base_achv_percent > 150) $base_achv_percent='150';
			else 						  $base_achv_percent=$base_achv_percent;
		}
		$base_achv_percent=round($base_achv_percent,2);
		}
		else $base_achv_percent='';
		
		$sqllevelvisittgtachv="SELECT $columnvisittgt AS  unique_visit_tgt,$columnvisitachv AS unique_visit_achv FROM sis_level_wise_unique_visit_target_ach  WHERE  level='".$level."' AND year='".$year."'";
		$rslevelvisittgtachv=mysqli_query($link,$sqllevelvisittgtachv);
		$rowlevelvisittgtachv=mysqli_fetch_assoc($rslevelvisittgtachv);
		$unique_visit_tgt=$rowlevelvisittgtachv['unique_visit_tgt'];
		$unique_visit_achv=$rowlevelvisittgtachv['unique_visit_achv'];
		$unique_visit_achv_array=explode(',',$unique_visit_achv);
		$unique_visit_achv_array=array_unique($unique_visit_achv_array);
		//print_r($unique_visit_achv_array);
		$unique_visit_final_achv=count($unique_visit_achv_array)-1;
		if($unique_visit_final_achv==0) $unique_visit_final_achv='';
		
		if($unique_visit_tgt!='' || $unique_visit_final_achv!=''){
		if($unique_visit_final_achv < $unique_visit_tgt) 	$unique_visit_achv_percent='0';
		if($unique_visit_final_achv==$unique_visit_tgt) 	$unique_visit_achv_percent='100';
		if(($unique_visit_final_achv > $unique_visit_tgt) && ($unique_visit_final_achv-$unique_visit_tgt)<=10) $unique_visit_achv_percent='100';
		if(($unique_visit_final_achv > $unique_visit_tgt) && ($unique_visit_final_achv-$unique_visit_tgt) > 10 && ($unique_visit_final_achv-$unique_visit_tgt) <= 20) $unique_visit_achv_percent='125';
	
			if(($unique_visit_final_achv > $unique_visit_tgt)&&($unique_visit_final_achv-$unique_visit_tgt) >20) $unique_visit_achv_percent='150';
		}
		else $unique_visit_achv_percent='';
		
		$sqlbranchdealertgtachv="SELECT SUM($columndealertgt) AS  base_dealer_tgt,SUM($columndealerachv) AS  base_dealer_achv FROM sis_branch_dealer_volume_target_ach  WHERE  
							FIND_IN_SET(branch_code,'".$branch_code_string."') AND year='".$year."'";
		$rsbranchdealertgtachv=mysqli_query($link,$sqlbranchdealertgtachv);
		$rowbranchdealertgtachv=mysqli_fetch_assoc($rsbranchdealertgtachv);
		$base_dealer_tgt=$rowbranchdealertgtachv['base_dealer_tgt'];
		$base_dealer_achv=$rowbranchdealertgtachv['base_dealer_achv'];
		if($base_dealer_achv < $base_dealer_tgt) 	$base_dealer_achv_percent='0';
		if($base_dealer_achv==$base_dealer_tgt) 	$base_dealer_achv_percent='100';
		
		if($base_dealer_achv!='' || $base_dealer_tgt!=''){
		if(($base_dealer_achv > $base_dealer_tgt)&&($base_dealer_achv-$base_dealer_tgt)<=2) $base_dealer_achv_percent='100';
		if(($base_dealer_achv > $base_dealer_tgt)&&($base_dealer_achv-$base_dealer_tgt) >2 
		&&($base_dealer_achv-$base_dealer_tgt) <= 4) $base_dealer_achv_percent='125';
		
		if(($base_dealer_achv > $base_dealer_tgt) && ($base_dealer_achv-$base_dealer_tgt) > 4)  	$base_dealer_achv_percent='150';
		}
		else $base_dealer_achv_percent='';
		
		$sis_percent=(($base_achv_percent*40)/100)+(($unique_visit_achv_percent*20)/100)+(($base_dealer_achv_percent*40)/100);
		$sis_percent=round($sis_percent,2);
		
		
		$sqllevelincentive="SELECT $columnincentive AS  level_incentive FROM  sis_emp_level_wise_sales_incentive  WHERE  
							emp_level='".$level."' AND year='".$year."'";
		$rslevelincentive=mysqli_query($link,$sqllevelincentive);
		$rowlevelincentive=mysqli_fetch_assoc($rslevelincentive);
		$level_incentive=$rowlevelincentive['level_incentive'];
		
		$sis_earned=$sis_percent*$level_incentive;
		$sis_earned=round($sis_earned,2);
		
		
		$sqllevelpenalty="SELECT SUM($penaltycolumn) AS  penalty_val FROM sis_branch_level_wise_penalty  WHERE FIND_IN_SET(branch_code,'".$branch_code_string."') AND year='".$year."'";
		$rslevelpenalty=mysqli_query($link,$sqllevelpenalty);
		$rowlevelpenalty=mysqli_fetch_assoc($rslevelpenalty);
		$penalty_val=$rowlevelpenalty['penalty_val'];
		$penalty_deducted=$sis_earned*$penalty_val;
		$penalty_deducted=round($penalty_deducted,2);
		$net_SIS_earned=$sis_earned-$penalty_deducted;
	?>
      <tr>
        <td><?php echo $sis_count; ?></td>
        <td><?php echo $dns_emp_code; ?></td>
        <td><?php echo $emp_name; ?></td>
        <td align="right"><?php echo $base_vol_tgt; ?></td>
        <td align="right"><?php echo $base_vol_achv; ?></td>
        <td align="right"><?php echo $base_achv_percent; ?></td>
        <td align="right"><?php echo $unique_visit_tgt; ?></td>
        <td align="right"><?php echo $unique_visit_final_achv; ?></td>
        <td align="right"><?php echo $unique_visit_achv_percent; ?></td>
        <td align="right"><?php echo $base_dealer_tgt; ?></td>
        <td align="right"><?php echo $base_dealer_achv; ?></td>
        <td align="right"><?php echo $base_dealer_achv_percent; ?></td>
        <td align="right"><?php echo $sis_percent; ?></td>
        <td align="right"><?php echo $level_incentive; ?></td>
        <td align="right"><?php echo $sis_earned; ?></td>
        <td align="right"><?php echo $penalty_val; ?></td>
        <td align="right"><?php echo $penalty_deducted;?></td>
        <td align="right"><?php echo $net_SIS_earned;?></td>
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
