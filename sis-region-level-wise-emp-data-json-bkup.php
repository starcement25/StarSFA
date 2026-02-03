<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' AND emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition=" AND emp_code='".$emp_code."'";
}
$countsis=0;
$monthval=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));
$year=$year-1;

	$sql_active_dealer_prev_month="SELECT CVD.emp_code,COUNT(DISTINCT CVD.customer_code) AS active_dealer,SUBSTRING(CVD.trans_id,-10,2) AS monthactive,SUM(OD.qty) AS tot_qty  FROM `customer_visit_details` CVD,order_details OD WHERE CVD.trans_id=OD.order_no AND CVD.trans_id LIKE 'O%' AND CVD.`cust_type`='Dealer' AND SUBSTRING(CVD.trans_id,-14,4)='".$year."' GROUP BY emp_code,customer_code,SUBSTRING(CVD.trans_id,-10,2) HAVING tot_qty >='20'";
	$rs_active_dealer_prev_month=mysqli_query($link,$sql_active_dealer_prev_month);
	while($row_active_dealer_prev_month=mysqli_fetch_assoc($rs_active_dealer_prev_month))
	{
		$emp_code_active=$row_active_dealer_prev_month['emp_code'];
		$active_dealer=$row_active_dealer_prev_month['active_dealer'];
		$month_active=$row_active_dealer_prev_month['monthactive'];
		if(substr($month_active,0,1)=='0') $month_active=substr($month_active,1,1);
		${active_dealer_month.$emp_code_active.$month_active}=${active_dealer_month.$emp_code_active.$month_active}+$active_dealer;
	}

$sqllevel="SELECT level,emp_name,emp_code,branch_code,region FROM employee_master WHERE level!='' and level!='0' and sale_access='primary' $emp_hierarchy_condition ";
$rslevel=mysqli_query($link,$sqllevel);
while($rowlevel=mysqli_fetch_assoc($rslevel))
{
	$level=$rowlevel['level'];
	$emp_name=$rowlevel['emp_name'];
	$emp_code_lower=$rowlevel['emp_code'];
	$branch_code_all = $rowlevel['branch_code'];
	$branch_code_all_array=explode(',',$branch_code_all);
	$branch_code_string='';
	$region=$rowlevel['region'];
	foreach($branch_code_all_array as $branch_code_val)
	{
		$sql_emp_branch = "SELECT dns_branch_code FROM branch_master WHERE branch_code='".$branch_code_val."'";
		$res_emp_branch = mysqli_query($link,$sql_emp_branch);
		$row_emp_branch = mysqli_fetch_assoc($res_emp_branch);
		$branch_emp_code = $row_emp_branch['dns_branch_code'];
		$branch_code_string=$branch_code_string.$branch_emp_code.',';
	}
	$branch_code_string=substr($branch_code_string,0,-1);
	$month_array=array();
	/*if($monthval=='01') $columname='jan_vol_achievement';
	if($monthval=='02') $columname='feb_vol_achievement';
	if($monthval=='03') $columname='mar_vol_achievement';
	if($monthval=='04') $columname='apr_vol_achievement';
	if($monthval=='05') $columname='may_vol_achievement';
	if($monthval=='06') $columname='jun_vol_achievement';
	if($monthval=='07') $columname='jul_vol_achievement';
	if($monthval=='08') $columname='aug_vol_achievement';
	if($monthval=='09') $columname='sep_vol_achievement';
	if($monthval=='10') $columname='oct_vol_achievement';
	if($monthval=='11') $columname='nov_vol_achievement';
	if($monthval=='12') $columname='dec_vol_achievement';*/

	$sql_branch_vol_tgt_ach = "SELECT jan_vol_target,feb_vol_target,mar_vol_target,apr_vol_target,may_vol_target,jun_vol_target,jul_vol_target,aug_vol_target,sep_vol_target,oct_vol_target,nov_vol_target,dec_vol_target,jan_vol_achievement,feb_vol_achievement,mar_vol_achievement,apr_vol_achievement,may_vol_achievement,jun_vol_achievement,jul_vol_achievement,aug_vol_achievement,sep_vol_achievement,oct_vol_achievement,nov_vol_achievement,dec_vol_achievement FROM  sis_branch_volume_target_ach WHERE FIND_IN_SET(branch_code,'".$branch_code_string."') AND year='".$year."'";
	$res_branch_vol_tgt_ach = mysqli_query($link,$sql_branch_vol_tgt_ach);
	$count=mysqli_num_rows($res_branch_vol_tgt_ach);
	//print_r($vertical_array);
	//if($count>0){
		
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		while($row_branch_vol_tgt_ach = mysqli_fetch_assoc($res_branch_vol_tgt_ach)){
			$sep_vol_target= $row_branch_vol_tgt_ach['sep_vol_target'];
			$oct_vol_target= $row_branch_vol_tgt_ach['oct_vol_target'];
			$nov_vol_target= $row_branch_vol_tgt_ach['nov_vol_target'];
			$dec_vol_target= $row_branch_vol_tgt_ach['dec_vol_target'];
			$jan_vol_target= $row_branch_vol_tgt_ach['jan_vol_target'];
			$feb_vol_target= $row_branch_vol_tgt_ach['feb_vol_target'];
			$mar_vol_target= $row_branch_vol_tgt_ach['mar_vol_target'];
			$apr_vol_target= $row_branch_vol_tgt_ach['apr_vol_target'];
			$may_vol_target= $row_branch_vol_tgt_ach['may_vol_target'];
			$jun_vol_target= $row_branch_vol_tgt_ach['jun_vol_target'];
			$jul_vol_target= $row_branch_vol_tgt_ach['jul_vol_target'];
			$aug_vol_target= $row_branch_vol_tgt_ach['aug_vol_target'];
			$sep_vol_achievement= $row_branch_vol_tgt_ach['sep_vol_achievement'];
			$oct_vol_achievement= $row_branch_vol_tgt_ach['oct_vol_achievement'];
			$nov_vol_achievement= $row_branch_vol_tgt_ach['nov_vol_achievement'];
			$dec_vol_achievement= $row_branch_vol_tgt_ach['dec_vol_achievement'];
			$jan_vol_achievement= $row_branch_vol_tgt_ach['jan_vol_achievement'];
			$feb_vol_achievement= $row_branch_vol_tgt_ach['feb_vol_achievement'];
			$mar_vol_achievement= $row_branch_vol_tgt_ach['mar_vol_achievement'];
			$apr_vol_achievement= $row_branch_vol_tgt_ach['apr_vol_achievement'];
			$may_vol_achievement= $row_branch_vol_tgt_ach['may_vol_achievement'];
			$jun_vol_achievement= $row_branch_vol_tgt_ach['jun_vol_achievement'];
			$jul_vol_achievement= $row_branch_vol_tgt_ach['jul_vol_achievement'];
			$aug_vol_achievement= $row_branch_vol_tgt_ach['aug_vol_achievement'];
			
			if(($sep_vol_target >0 || $sep_vol_achievement >0))
			{
				$monthcur='9';
				${month_vol_target.$monthcur.$emp_code_lower}=$sep_vol_target;
				${month_vol_achievement.$monthcur.$emp_code_lower}=$sep_vol_achievement;
				array_push($month_array,$monthcur);
			}
			if($oct_vol_target >0 || $oct_vol_achievement >0)
			{
				$monthcur='10';
				${month_vol_target.$monthcur.$emp_code_lower}=$oct_vol_target;
				${month_vol_achievement.$monthcur.$emp_code_lower}=$oct_vol_achievement;
				array_push($month_array,$monthcur);
			}
			if($nov_vol_target >0 || $nov_vol_achievement >0)
			{
				$monthcur='11';
				${month_vol_target.$monthcur.$emp_code_lower}=$nov_vol_target;
				${month_vol_achievement.$monthcur.$emp_code_lower}=$nov_vol_achievement;
				array_push($month_array,$monthcur);
			}
			if($dec_vol_target >0 || $dec_vol_achievement >0)
			{
				$monthcur='12';
				${month_vol_target.$monthcur.$emp_code_lower}=$dec_vol_target;
				${month_vol_achievement.$monthcur.$emp_code_lower}=$dec_vol_achievement;
				array_push($month_array,$monthcur);
			}
			if($jan_vol_target >0 || $jan_vol_achievement >0)
			{
				$monthcur='1';
				${month_vol_target.$monthcur.$emp_code_lower}=$jan_vol_target;
				${month_vol_achievement.$monthcur.$emp_code_lower}=$jan_vol_achievement;
				array_push($month_array,$monthcur);
			}
			if($feb_vol_target >0 || $feb_vol_achievement >0)
			{
				$monthcur='2';
				${month_vol_target.$monthcur.$emp_code_lower}=$feb_vol_target;
				${month_vol_achievement.$monthcur.$emp_code_lower}=$feb_vol_achievement;
				array_push($month_array,$monthcur);
			}
			if($mar_vol_target >0 || $mar_vol_achievement >0)
			{
				$monthcur='3';
				${month_vol_target.$monthcur.$emp_code_lower}=$mar_vol_target;
				${month_vol_achievement.$monthcur.$emp_code_lower}=$mar_vol_achievement;
				array_push($month_array,$monthcur);
			}
			if($apr_vol_target >0 || $apr_vol_achievement >0)
			{
				$monthcur='4';
				${month_vol_target.$monthcur.$emp_code_lower}=$apr_vol_target;
				${month_vol_achievement.$monthcur.$emp_code_lower}=$apr_vol_achievement;
				array_push($month_array,$monthcur);
			}
			if($may_vol_target >0 || $may_vol_achievement >0)
			{
				$monthcur='5';
				${month_vol_target.$monthcur.$emp_code_lower}=$may_vol_target;
				${month_vol_achievement.$monthcur.$emp_code_lower}=$may_vol_achievement;
				array_push($month_array,$monthcur);
			}
			if($jun_vol_target >0 || $jun_vol_achievement >0)
			{
				$monthcur='6';
				${month_vol_target.$monthcur.$emp_code_lower}=$jun_vol_target;
				${month_vol_achievement.$monthcur.$emp_code_lower}=$jun_vol_achievement;
				array_push($month_array,$monthcur);
			}
			if($jul_vol_target >0 || $jul_vol_achievement >0)
			{
				$monthcur='7';
				${month_vol_target.$monthcur.$emp_code_lower}=$jul_vol_target;
				${month_vol_achievement.$monthcur.$emp_code_lower}=$jul_vol_achievement;
				array_push($month_array,$monthcur);
			}
			if($aug_vol_target >0 || $aug_vol_achievement >0)
			{
				$monthcur='8';
				${month_vol_target.$monthcur.$emp_code_lower}=$aug_vol_target;
				${month_vol_achievement.$monthcur.$emp_code_lower}=$aug_vol_achievement;
				array_push($month_array,$monthcur);
			}
		}
		
		$sqllevelvisittgtachv="SELECT jan_visit_target,feb_visit_target,mar_visit_target,apr_visit_target,may_visit_target,jun_visit_target,jul_visit_target,aug_visit_target,sep_visit_target,oct_visit_target,nov_visit_target,dec_visit_target,jan_visit_achievement,feb_visit_achievement,mar_visit_achievement,apr_visit_achievement,may_visit_achievement,jun_visit_achievement,jul_visit_achievement,aug_visit_achievement,sep_visit_achievement,oct_visit_achievement,nov_visit_achievement,dec_visit_achievement FROM sis_level_wise_unique_visit_target_ach  WHERE  level='".$level."' AND year='".$year."'";
		$rslevelvisittgtachv=mysqli_query($link,$sqllevelvisittgtachv);
		while($rowlevelvisittgtachv=mysqli_fetch_assoc($rslevelvisittgtachv)){
		$sep_visit_target= $rowlevelvisittgtachv['sep_visit_target'];
		$oct_visit_target= $rowlevelvisittgtachv['oct_visit_target'];
		$nov_visit_target= $rowlevelvisittgtachv['nov_visit_target'];
		$dec_visit_target= $rowlevelvisittgtachv['dec_visit_target'];
		$jan_visit_target= $rowlevelvisittgtachv['jan_visit_target'];
		$feb_visit_target= $rowlevelvisittgtachv['feb_visit_target'];
		$mar_visit_target= $rowlevelvisittgtachv['mar_visit_target'];
		$apr_visit_target= $rowlevelvisittgtachv['apr_visit_target'];
		$may_visit_target= $rowlevelvisittgtachv['may_visit_target'];
		$jun_visit_target= $rowlevelvisittgtachv['jun_visit_target'];
		$jul_visit_target= $rowlevelvisittgtachv['jul_visit_target'];
		$aug_visit_target= $rowlevelvisittgtachv['aug_visit_target'];
		$sep_visit_achievement= $rowlevelvisittgtachv['sep_visit_achievement'];
		$oct_visit_achievement= $rowlevelvisittgtachv['oct_visit_achievement'];
		$nov_visit_achievement= $rowlevelvisittgtachv['nov_visit_achievement'];
		$dec_visit_achievement= $rowlevelvisittgtachv['dec_visit_achievement'];
		$jan_visit_achievement= $rowlevelvisittgtachv['jan_visit_achievement'];
		$feb_visit_achievement= $rowlevelvisittgtachv['feb_visit_achievement'];
		$mar_visit_achievement= $rowlevelvisittgtachv['mar_visit_achievement'];
		$apr_visit_achievement= $rowlevelvisittgtachv['apr_visit_achievement'];
		$may_visit_achievement= $rowlevelvisittgtachv['may_visit_achievement'];
		$jun_visit_achievement= $rowlevelvisittgtachv['jun_visit_achievement'];
		$jul_visit_achievement= $rowlevelvisittgtachv['jul_visit_achievement'];
		$aug_visit_achievement= $rowlevelvisittgtachv['aug_visit_achievement'];
			
		$sep_visit_achv_array=explode(',',$sep_visit_achievement);
		$sep_visit_achv_array=array_unique($sep_visit_achv_array);
		//print_r($unique_visit_achv_array);
		$unique_visit_final_achv_sep=count($sep_visit_achv_array)-1;
		if($unique_visit_final_achv_sep==0) $unique_visit_final_achv_sep='';
		
		$oct_visit_achv_array=explode(',',$oct_visit_achievement);
		$oct_visit_achv_array=array_unique($oct_visit_achv_array);
		//print_r($unique_visit_achv_array);
		$unique_visit_final_achv_oct=count($oct_visit_achv_array)-1;
		if($unique_visit_final_achv_oct==0) $unique_visit_final_achv_oct='';
		
		$nov_visit_achv_array=explode(',',$nov_visit_achievement);
		$nov_visit_achv_array=array_unique($nov_visit_achv_array);
		//print_r($unique_visit_achv_array);
		$unique_visit_final_achv_nov=count($nov_visit_achv_array)-1;
		if($unique_visit_final_achv_nov==0) $unique_visit_final_achv_nov='';
		
		$dec_visit_achv_array=explode(',',$dec_visit_achievement);
		$dec_visit_achv_array=array_unique($dec_visit_achv_array);
		//print_r($unique_visit_achv_array);
		$unique_visit_final_achv_dec=count($dec_visit_achv_array)-1;
		if($unique_visit_final_achv_dec==0) $unique_visit_final_achv_dec='';
		
		$jan_visit_achv_array=explode(',',$jan_visit_achievement);
		$jan_visit_achv_array=array_unique($jan_visit_achv_array);
		//print_r($unique_visit_achv_array);
		$unique_visit_final_achv_jan=count($jan_visit_achv_array)-1;
		if($unique_visit_final_achv_jan==0) $unique_visit_final_achv_jan='';
		
		$feb_visit_achv_array=explode(',',$feb_visit_achievement);
		$feb_visit_achv_array=array_unique($feb_visit_achv_array);
		//print_r($unique_visit_achv_array);
		$unique_visit_final_achv_feb=count($feb_visit_achv_array)-1;
		if($unique_visit_final_achv_feb==0) $unique_visit_final_achv_feb='';
		
		$mar_visit_achv_array=explode(',',$mar_visit_achievement);
		$mar_visit_achv_array=array_unique($mar_visit_achv_array);
		//print_r($unique_visit_achv_array);
		$unique_visit_final_achv_mar=count($mar_visit_achv_array)-1;
		if($unique_visit_final_achv_mar==0) $unique_visit_final_achv_mar='';
		
		$apr_visit_achv_array=explode(',',$apr_visit_achievement);
		$apr_visit_achv_array=array_unique($apr_visit_achv_array);
		//print_r($unique_visit_achv_array);
		$unique_visit_final_achv_apr=count($apr_visit_achv_array)-1;
		if($unique_visit_final_achv_apr==0) $unique_visit_final_achv_apr='';
		
		$may_visit_achv_array=explode(',',$may_visit_achievement);
		$may_visit_achv_array=array_unique($may_visit_achv_array);
		//print_r($unique_visit_achv_array);
		$unique_visit_final_achv_may=count($may_visit_achv_array)-1;
		if($unique_visit_final_achv_may==0) $unique_visit_final_achv_may='';
		
		$jun_visit_achv_array=explode(',',$jun_visit_achievement);
		$jun_visit_achv_array=array_unique($jun_visit_achv_array);
		//print_r($unique_visit_achv_array);
		$unique_visit_final_achv_jun=count($jun_visit_achv_array)-1;
		if($unique_visit_final_achv_jun==0) $unique_visit_final_achv_jun='';
		
		$jul_visit_achv_array=explode(',',$jul_visit_achievement);
		$jul_visit_achv_array=array_unique($jul_visit_achv_array);
		//print_r($unique_visit_achv_array);
		$unique_visit_final_achv_jul=count($jul_visit_achv_array)-1;
		if($unique_visit_final_achv_jul==0) $unique_visit_final_achv_jul='';
		
		$aug_visit_achv_array=explode(',',$aug_visit_achievement);
		$aug_visit_achv_array=array_unique($aug_visit_achv_array);
		//print_r($unique_visit_achv_array);
		$unique_visit_final_achv_aug=count($aug_visit_achv_array)-1;
		if($unique_visit_final_achv_aug==0) $unique_visit_final_achv_aug='';
		
		if($sep_visit_target >0 || $unique_visit_final_achv_sep >0)
			{
				$monthcur='9';
				${month_visit_target.$monthcur.$emp_code_lower}=$sep_visit_target;
				${month_visit_achievement.$monthcur.$emp_code_lower}=$unique_visit_final_achv_sep;
				if(!in_array($monthcur,$month_array))
				{
					array_push($month_array,$monthcur);
				}
			}
		if($oct_visit_target >0 || $unique_visit_final_achv_oct >0)
			{
				$monthcur='10';
				${month_visit_target.$monthcur.$emp_code_lower}=$oct_visit_target;
				${month_visit_achievement.$monthcur.$emp_code_lower}=$unique_visit_final_achv_oct;
				if(!in_array($monthcur,$month_array))
				{
					array_push($month_array,$monthcur);
				}
			}
		if($nov_visit_target >0 || $unique_visit_final_achv_nov >0)
			{
				$monthcur='11';
				${month_visit_target.$monthcur.$emp_code_lower}=$nov_visit_target;
				${month_visit_achievement.$monthcur.$emp_code_lower}=$unique_visit_final_achv_nov;
				if(!in_array($monthcur,$month_array))
				{
					array_push($month_array,$monthcur);
				}
			}
		if($dec_visit_target >0 || $unique_visit_final_achv_dec >0)
			{
				$monthcur='12';
				${month_visit_target.$monthcur.$emp_code_lower}=$dec_visit_target;
				${month_visit_achievement.$monthcur.$emp_code_lower}=$unique_visit_final_achv_dec;
				if(!in_array($monthcur,$month_array))
				{
					array_push($month_array,$monthcur);
				}
			}
		if($jan_visit_target >0 || $unique_visit_final_achv_jan >0)
			{
				$monthcur='1';
				${month_visit_target.$monthcur.$emp_code_lower}=$jan_visit_target;
				${month_visit_achievement.$monthcur.$emp_code_lower}=$unique_visit_final_achv_jan;
				if(!in_array($monthcur,$month_array))
				{
					array_push($month_array,$monthcur);
				}
			}
		if($feb_visit_target >0 || $unique_visit_final_achv_feb >0)
			{
				$monthcur='2';
				${month_visit_target.$monthcur.$emp_code_lower}=$feb_visit_target;
				${month_visit_achievement.$monthcur.$emp_code_lower}=$unique_visit_final_achv_feb;
				if(!in_array($monthcur,$month_array))
				{
					array_push($month_array,$monthcur);
				}
			}
		if($mar_visit_target >0 || $unique_visit_final_achv_mar >0)
			{
				$monthcur='3';
				${month_visit_target.$monthcur.$emp_code_lower}=$mar_visit_target;
				${month_visit_achievement.$monthcur.$emp_code_lower}=$unique_visit_final_achv_mar;
				if(!in_array($monthcur,$month_array))
				{
					array_push($month_array,$monthcur);
				}
			}
		if($apr_visit_target >0 || $unique_visit_final_achv_apr >0)
			{
				$monthcur='4';
				${month_visit_target.$monthcur.$emp_code_lower}=$apr_visit_target;
				${month_visit_achievement.$monthcur.$emp_code_lower}=$unique_visit_final_achv_apr;
				if(!in_array($monthcur,$month_array))
				{
					array_push($month_array,$monthcur);
				}
			}
		if($may_visit_target >0 || $unique_visit_final_achv_may >0)
			{
				$monthcur='5';
				${month_visit_target.$monthcur.$emp_code_lower}=$may_visit_target;
				${month_visit_achievement.$monthcur.$emp_code_lower}=$unique_visit_final_achv_may;
				if(!in_array($monthcur,$month_array))
				{
					array_push($month_array,$monthcur);
				}
			}
		if($jun_visit_target >0 || $unique_visit_final_achv_jun >0)
			{
				$monthcur='6';
				${month_visit_target.$monthcur.$emp_code_lower}=$jun_visit_target;
				${month_visit_achievement.$monthcur.$emp_code_lower}=$unique_visit_final_achv_jun;
				if(!in_array($monthcur,$month_array))
				{
					array_push($month_array,$monthcur);
				}
			}
		if($jul_visit_target >0 || $unique_visit_final_achv_jul >0)
			{
				$monthcur='7';
				${month_visit_target.$monthcur.$emp_code_lower}=$jul_visit_target;
				${month_visit_achievement.$monthcur.$emp_code_lower}=$unique_visit_final_achv_jul;
				if(!in_array($monthcur,$month_array))
				{
					array_push($month_array,$monthcur);
				}
			}
		if($aug_visit_target >0 || $unique_visit_final_achv_aug >0)
			{
				$monthcur='8';
				${month_visit_target.$monthcur.$emp_code_lower}=$aug_visit_target;
				${month_visit_achievement.$monthcur.$emp_code_lower}=$unique_visit_final_achv_aug;
				if(!in_array($monthcur,$month_array))
				{
					array_push($month_array,$monthcur);
				}
			}			
									
		}
			$sql_branch_dealer_tgt_ach = "SELECT jan_dealer_target,feb_dealer_target,mar_dealer_target,apr_dealer_target,may_dealer_target,jun_dealer_target,jul_dealer_target,aug_dealer_target,sep_dealer_target,oct_dealer_target,nov_dealer_target,dec_dealer_target,jan_dealer_achievement,feb_dealer_achievement,mar_dealer_achievement,apr_dealer_achievement,may_dealer_achievement,jun_dealer_achievement,jul_dealer_achievement,aug_dealer_achievement,sep_dealer_achievement,oct_dealer_achievement,nov_dealer_achievement,dec_dealer_achievement FROM  sis_branch_dealer_volume_target_ach WHERE FIND_IN_SET(branch_code,'".$branch_code_string."') AND year='".$year."'";
	$res_branch_dealer_tgt_ach = mysqli_query($link,$sql_branch_dealer_tgt_ach);
	//print_r($vertical_array);
		
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		while($row_branch_dealer_tgt_ach = mysqli_fetch_assoc($res_branch_dealer_tgt_ach)){
			$sep_dealer_target= $row_branch_dealer_tgt_ach['sep_dealer_target'];
			$oct_dealer_target= $row_branch_dealer_tgt_ach['oct_dealer_target'];
			$nov_dealer_target= $row_branch_dealer_tgt_ach['nov_dealer_target'];
			$dec_dealer_target= $row_branch_dealer_tgt_ach['dec_dealer_target'];
			$jan_dealer_target= $row_branch_dealer_tgt_ach['jan_dealer_target'];
			$feb_dealer_target= $row_branch_dealer_tgt_ach['feb_dealer_target'];
			$mar_dealer_target= $row_branch_dealer_tgt_ach['mar_dealer_target'];
			$apr_dealer_target= $row_branch_dealer_tgt_ach['apr_dealer_target'];
			$may_dealer_target= $row_branch_dealer_tgt_ach['may_dealer_target'];
			$jun_dealer_target= $row_branch_dealer_tgt_ach['jun_dealer_target'];
			$jul_dealer_target= $row_branch_dealer_tgt_ach['jul_dealer_target'];
			$aug_dealer_target= $row_branch_dealer_tgt_ach['aug_dealer_target'];
			$sep_dealer_achievement= $row_branch_dealer_tgt_ach['sep_dealer_achievement'];
			$oct_dealer_achievement= $row_branch_dealer_tgt_ach['oct_dealer_achievement'];
			$nov_dealer_achievement= $row_branch_dealer_tgt_ach['nov_dealer_achievement'];
			$dec_dealer_achievement= $row_branch_dealer_tgt_ach['dec_dealer_achievement'];
			$jan_dealer_achievement= $row_branch_dealer_tgt_ach['jan_dealer_achievement'];
			$feb_dealer_achievement= $row_branch_dealer_tgt_ach['feb_dealer_achievement'];
			$mar_dealer_achievement= $row_branch_dealer_tgt_ach['mar_dealer_achievement'];
			$apr_dealer_achievement= $row_branch_dealer_tgt_ach['apr_dealer_achievement'];
			$may_dealer_achievement= $row_branch_dealer_tgt_ach['may_dealer_achievement'];
			$jun_dealer_achievement= $row_branch_dealer_tgt_ach['jun_dealer_achievement'];
			$jul_dealer_achievement= $row_branch_dealer_tgt_ach['jul_dealer_achievement'];
			$aug_dealer_achievement= $row_branch_dealer_tgt_ach['aug_dealer_achievement'];
			
			if($sep_dealer_target >0 || $sep_dealer_achievement >0)
			{
				$monthcur='9';
				${month_dealer_target.$monthcur.$emp_code_lower}=$sep_dealer_target;
				${month_dealer_achievement.$monthcur.$emp_code_lower}=$sep_dealer_achievement;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($oct_dealer_target >0 || $oct_dealer_achievement >0)
			{
				$monthcur='10';
				${month_dealer_target.$monthcur.$emp_code_lower}=$oct_dealer_target;
				${month_dealer_achievement.$monthcur.$emp_code_lower}=$oct_dealer_achievement;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($nov_dealer_target >0 || $nov_dealer_achievement >0)
			{
				$monthcur='11';
				${month_dealer_target.$monthcur.$emp_code_lower}=$nov_dealer_target;
				${month_dealer_achievement.$monthcur.$emp_code_lower}=$nov_dealer_achievement;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($dec_dealer_target >0 || $dec_dealer_achievement >0)
			{
				$monthcur='12';
				${month_dealer_target.$monthcur.$emp_code_lower}=$dec_dealer_target;
				${month_dealer_achievement.$monthcur.$emp_code_lower}=$dec_dealer_achievement;
				array_push($month_array,$monthcur);
			}
			if($jan_dealer_target >0 || $jan_dealer_achievement >0)
			{
				$monthcur='1';
				${month_dealer_target.$monthcur.$emp_code_lower}=$jan_dealer_target;
				${month_dealer_achievement.$monthcur.$emp_code_lower}=$jan_dealer_achievement;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($feb_dealer_target >0 || $feb_dealer_achievement >0)
			{
				$monthcur='2';
				${month_dealer_target.$monthcur.$emp_code_lower}=$feb_dealer_target;
				${month_dealer_achievement.$monthcur.$emp_code_lower}=$feb_dealer_achievement;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($mar_dealer_target >0 || $mar_dealer_achievement >0)
			{
				$monthcur='3';
				${month_dealer_target.$monthcur.$emp_code_lower}=$mar_dealer_target;
				${month_dealer_achievement.$monthcur.$emp_code_lower}=$mar_dealer_achievement;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($apr_dealer_target >0 || $apr_dealer_achievement >0)
			{
				$monthcur='4';
				${month_dealer_target.$monthcur.$emp_code_lower}=$apr_dealer_target;
				${month_dealer_achievement.$monthcur.$emp_code_lower}=$apr_dealer_achievement;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($may_dealer_target >0 || $may_dealer_achievement >0)
			{
				$monthcur='5';
				${month_dealer_target.$monthcur.$emp_code_lower}=$may_dealer_target;
				${month_dealer_achievement.$monthcur.$emp_code_lower}=$may_dealer_achievement;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($jun_dealer_target >0 || $jun_dealer_achievement >0)
			{
				$monthcur='6';
				${month_dealer_target.$monthcur.$emp_code_lower}=$jun_dealer_target;
				${month_dealer_achievement.$monthcur.$emp_code_lower}=$jun_dealer_achievement;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($jul_dealer_target >0 || $jul_dealer_achievement >0)
			{
				$monthcur='7';
				${month_dealer_target.$monthcur.$emp_code_lower}=$jul_dealer_target;
				${month_dealer_achievement.$monthcur.$emp_code_lower}=$jul_dealer_achievement;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($aug_dealer_target >0 || $aug_dealer_achievement >0)
			{
				$monthcur='8';
				${month_dealer_target.$monthcur.$emp_code_lower}=$aug_dealer_target;
				${month_dealer_achievement.$monthcur.$emp_code_lower}=$aug_dealer_achievement;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
		}
		$sqllevelincentive="SELECT oct_incentive AS  level_incentive FROM  sis_emp_level_wise_sales_incentive  WHERE  
							emp_level='".$level."' AND year='".$year."'";
		$rslevelincentive=mysqli_query($link,$sqllevelincentive);
		$rowlevelincentive=mysqli_fetch_assoc($rslevelincentive);
		$level_incentive=$rowlevelincentive['level_incentive'];
		
		if(substr($level,1,1)=='2')
		{
		$sql_level_penalty_l2 = "SELECT jan_l2_penalty,feb_l2_penalty,mar_l2_penalty,apr_l2_penalty,may_l2_penalty,jun_l2_penalty,jul_l2_penalty,aug_l2_penalty,sep_l2_penalty,oct_l2_penalty,nov_l2_penalty,dec_l2_penalty FROM sis_branch_level_wise_penalty WHERE FIND_IN_SET(branch_code,'".$branch_code_string."') AND year='".$year."'";
		$res_level_penalty_l2 = mysqli_query($link,$sql_level_penalty_l2);
		while($row_level_penalty_l2 = mysqli_fetch_assoc($res_level_penalty_l2)){
			$sep_l2_penalty= $row_level_penalty_l2['sep_l2_penalty'];
			$oct_l2_penalty= $row_level_penalty_l2['oct_l2_penalty'];
			$nov_l2_penalty= $row_level_penalty_l2['nov_l2_penalty'];
			$dec_l2_penalty= $row_level_penalty_l2['dec_l2_penalty'];
			$jan_l2_penalty= $row_level_penalty_l2['jan_l2_penalty'];
			$feb_l2_penalty= $row_level_penalty_l2['feb_l2_penalty'];
			$mar_l2_penalty= $row_level_penalty_l2['mar_l2_penalty'];
			$apr_l2_penalty= $row_level_penalty_l2['apr_l2_penalty'];
			$may_l2_penalty= $row_level_penalty_l2['may_l2_penalty'];
			$jun_l2_penalty= $row_level_penalty_l2['jun_l2_penalty'];
			$jul_l2_penalty= $row_level_penalty_l2['jul_l2_penalty'];
			$aug_l2_penalty= $row_level_penalty_l2['aug_l2_penalty'];
			
			if($sep_l2_penalty >0)
			{
				$monthcur='9';
				${month_l2_penalty.$monthcur.$emp_code_lower}=$sep_l2_penalty;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($oct_l2_penalty >0)
			{
				$monthcur='10';
				${month_l2_penalty.$monthcur.$emp_code_lower}=$oct_l2_penalty;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($nov_l2_penalty >0)
			{
				$monthcur='11';
				${month_l2_penalty.$monthcur.$emp_code_lower}=$nov_l2_penalty;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($dec_l2_penalty >0)
			{
				$monthcur='12';
				${month_l2_penalty.$monthcur.$emp_code_lower}=$dec_l2_penalty;
				array_push($month_array,$monthcur);
			}
			if($jan_l2_penalty >0)
			{
				$monthcur='1';
				${month_l2_penalty.$monthcur.$emp_code_lower}=$jan_l2_penalty;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($feb_l2_penalty >0)
			{
				$monthcur='2';
				${month_l2_penalty.$monthcur.$emp_code_lower}=$feb_l2_penalty;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($mar_l2_penalty >0)
			{
				$monthcur='3';
				${month_l2_penalty.$monthcur.$emp_code_lower}=$mar_l2_penalty;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($apr_l2_penalty >0)
			{
				$monthcur='4';
				${month_l2_penalty.$monthcur.$emp_code_lower}=$apr_l2_penalty;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($may_l2_penalty >0)
			{
				$monthcur='5';
				${month_l2_penalty.$monthcur.$emp_code_lower}=$may_l2_penalty;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($jun_l2_penalty >0)
			{
				$monthcur='6';
				${month_l2_penalty.$monthcur.$emp_code_lower}=$jun_l2_penalty;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($jul_l2_penalty >0)
			{
				$monthcur='7';
				${month_l2_penalty.$monthcur.$emp_code_lower}=$jul_l2_penalty;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($aug_l2_penalty >0)
			{
				$monthcur='8';
				${month_l2_penalty.$monthcur.$emp_code_lower}=$aug_l2_penalty;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
		 }
		}
		
		if(substr($level,1,1)>='3')
		{
		$sql_level_penalty_l2 = "SELECT jan_l3_above_penalty,feb_l3_above_penalty,mar_l3_above_penalty,apr_l3_above_penalty,may_l3_above_penalty,jun_l3_above_penalty,jul_l3_above_penalty,aug_l3_above_penalty,sep_l3_above_penalty,oct_l3_above_penalty,nov_l3_above_penalty,dec_l3_above_penalty FROM  sis_branch_dealer_volume_target_ach WHERE FIND_IN_SET(branch_code,'".$branch_code_string."') AND year='".$year."'";
		$res_level_penalty_l2 = mysqli_query($link,$sql_level_penalty_l2);
		while($row_level_penalty_l2 = mysqli_fetch_assoc($res_level_penalty_l2)){
			$sep_l3_above_penalty= $row_branch_dealer_tgt_ach['sep_l3_above_penalty'];
			$oct_l3_above_penalty= $row_branch_dealer_tgt_ach['oct_l3_above_penalty'];
			$nov_l3_above_penalty= $row_branch_dealer_tgt_ach['nov_l3_above_penalty'];
			$dec_l3_above_penalty= $row_branch_dealer_tgt_ach['dec_l3_above_penalty'];
			$jan_l3_above_penalty= $row_branch_dealer_tgt_ach['jan_l3_above_penalty'];
			$feb_l3_above_penalty= $row_branch_dealer_tgt_ach['feb_l3_above_penalty'];
			$mar_l3_above_penalty= $row_branch_dealer_tgt_ach['mar_l3_above_penalty'];
			$apr_l3_above_penalty= $row_branch_dealer_tgt_ach['apr_l3_above_penalty'];
			$may_l3_above_penalty= $row_branch_dealer_tgt_ach['may_l3_above_penalty'];
			$jun_l3_above_penalty= $row_branch_dealer_tgt_ach['jun_l3_above_penalty'];
			$jul_l3_above_penalty= $row_branch_dealer_tgt_ach['jul_l3_above_penalty'];
			$aug_l3_above_penalty= $row_branch_dealer_tgt_ach['aug_l3_above_penalty'];
			
			if($sep_l3_above_penalty >0)
			{
				$monthcur='9';
				${month_l3_above_penalty.$monthcur.$emp_code_lower}=$sep_l3_above_penalty;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($oct_l3_above_penalty >0)
			{
				$monthcur='10';
				${month_l3_above_penalty.$monthcur.$emp_code_lower}=$oct_l3_above_penalty;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($nov_l3_above_penalty >0)
			{
				$monthcur='11';
				${month_l3_above_penalty.$monthcur.$emp_code_lower}=$nov_l3_above_penalty;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($dec_l3_above_penalty >0)
			{
				$monthcur='12';
				${month_l3_above_penalty.$monthcur.$emp_code_lower}=$dec_l3_above_penalty;
				array_push($month_array,$monthcur);
			}
			if($jan_l3_above_penalty >0)
			{
				$monthcur='1';
				${month_l3_above_penalty.$monthcur.$emp_code_lower}=$jan_l3_above_penalty;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($feb_l3_above_penalty >0)
			{
				$monthcur='2';
				${month_l3_above_penalty.$monthcur.$emp_code_lower}=$feb_l3_above_penalty;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($mar_l3_above_penalty >0)
			{
				$monthcur='3';
				${month_l3_above_penalty.$monthcur.$emp_code_lower}=$mar_l3_above_penalty;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($apr_l3_above_penalty >0)
			{
				$monthcur='4';
				${month_l3_above_penalty.$monthcur.$emp_code_lower}=$apr_l3_above_penalty;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($may_l3_above_penalty >0)
			{
				$monthcur='5';
				${month_l3_above_penalty.$monthcur.$emp_code_lower}=$may_l3_above_penalty;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($jun_l3_above_penalty >0)
			{
				$monthcur='6';
				${month_l3_above_penalty.$monthcur.$emp_code_lower}=$jun_l3_above_penalty;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($jul_l3_above_penalty >0)
			{
				$monthcur='7';
				${month_l3_above_penalty.$monthcur.$emp_code_lower}=$jul_l3_above_penalty;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
			if($aug_l3_above_penalty >0)
			{
				$monthcur='8';
				${month_l3_above_penalty.$monthcur.$emp_code_lower}=$aug_l3_above_penalty;
				if(!in_array($monthcur,$month_array))
				{
				array_push($month_array,$monthcur);
				}
			}
		}
		}
		foreach($month_array as $mnthval)
		{
			
			$base_volume=${month_vol_target.$mnthval.$emp_code_lower};
			$volume_achv=${month_vol_achievement.$mnthval.$emp_code_lower};
			if($base_volume!='' || $volume_achv!=''){
			if($volume_achv < $base_volume) 	$ach_percent='0';
			if($base_volume==$volume_achv) 	$ach_percent='100';
			if($volume_achv > $base_volume) 	$ach_percent='150';
			
			if($volume_achv < $base_volume) 	$ach_percent='0';
			if($base_volume==$volume_achv) 	$ach_percent='100';
			if($volume_achv > $base_volume){ 
				$ach_percent=($volume_achv/$base_volume)*100;
				if($ach_percent > 150) $ach_percent='150';
				else 				   $ach_percent=$ach_percent;
			}
			$ach_percent=round($ach_percent,2);
			}
			else $ach_percent='';
			$monthly_unique_visit_target=${month_visit_target.$mnthval.$emp_code_lower};
			$monthly_unique_visit_acv=${month_visit_achievement.$mnthval.$emp_code_lower};
			$dealer_appointment_tgt=${month_dealer_target.$mnthval.$emp_code_lower};
			$dealer_appointment_achv=${month_dealer_achievement.$mnthval.$emp_code_lower};
			if($mnthval=='1') $mnthvalprev='12';
			else 			$mnthvalprev=$mnthval-1;
			$active_dealer_prev_month=${active_dealer_month.$emp_code_lower.$mnthvalprev};
			$active_dealer_current_month=${active_dealer_month.$emp_code_lower.$mnthval};
			
			if($monthly_unique_visit_target!='' || $monthly_unique_visit_acv!=''){
				if($monthly_unique_visit_acv < $monthly_unique_visit_target) 	$unique_visit_achv_percent='0';
				if($monthly_unique_visit_acv==$monthly_unique_visit_target) 	$unique_visit_achv_percent='100';
				if(($monthly_unique_visit_acv > $monthly_unique_visit_target) && ($monthly_unique_visit_acv-$monthly_unique_visit_target)<=10) $unique_visit_achv_percent='100';
				if(($monthly_unique_visit_acv > $monthly_unique_visit_target) && ($monthly_unique_visit_acv-$monthly_unique_visit_target) > 10 && ($monthly_unique_visit_acv-$monthly_unique_visit_target) <= 20) $unique_visit_achv_percent='125';
			
					if(($monthly_unique_visit_acv > $monthly_unique_visit_target)&&($monthly_unique_visit_acv-$monthly_unique_visit_target) >20) $unique_visit_achv_percent='150';
				}
			else $unique_visit_achv_percent=0;
			//$sis_earned=0;
			if(strtoupper($region)=='ROE')
			{
				if($dealer_appointment_achv < $dealer_appointment_tgt) 	$base_dealer_achv_percent='0';
					if($dealer_appointment_achv==$dealer_appointment_tgt) 	$base_dealer_achv_percent='100';
					
					if($dealer_appointment_achv!='' || $dealer_appointment_tgt!=''){
					if(($dealer_appointment_achv > $dealer_appointment_tgt)&&($dealer_appointment_achv-$dealer_appointment_tgt)<=2) $base_dealer_achv_percent='100';
					if(($dealer_appointment_achv > $dealer_appointment_tgt)&&($dealer_appointment_achv-$dealer_appointment_tgt) >2 
					&&($dealer_appointment_achv-$dealer_appointment_tgt) <= 4) $dealer_appointment_achv_percent='125';
					
					if(($dealer_appointment_achv > $dealer_appointment_tgt) && ($dealer_appointment_achv-$dealer_appointment_tgt) > 4)  	$base_dealer_achv_percent='150';
				}
				else $base_dealer_achv_percent=0;
				
				$sis_percent=(($ach_percent*40)/100)+(($unique_visit_achv_percent*20)/100)+(($base_dealer_achv_percent*40)/100);
				$sis_percent=round($sis_percent,2);
				
				$sis_earned=$sis_percent*$level_incentive;
				$sis_earned=round($sis_earned,2);
			}
			//For NE
			if(strtoupper($region)=='NE')
			{
	if($active_dealer_current_month < $active_dealer_prev_month) 	$dealer_achv_percent='0';
	if(($active_dealer_current_month==$active_dealer_prev_month)  && $active_dealer_current_month!='') $dealer_achv_percent='100';
		
		if(($active_dealer_current_month > $active_dealer_prev_month)&&($active_dealer_current_month-$active_dealer_prev_month)<=5) $dealer_achv_percent='100';
				if(($active_dealer_current_month > $active_dealer_prev_month)&&($active_dealer_current_month-$active_dealer_prev_month)<=5) $dealer_achv_percent='100';
			if(($active_dealer_current_month > $active_dealer_prev_month)&&($active_dealer_current_month-$active_dealer_prev_month) >5 
			&&($active_dealer_current_month-$active_dealer_prev_month) <= 10) $dealer_achv_percent='120';
			
			if(($active_dealer_current_month > $active_dealer_prev_month) && ($active_dealer_current_month-$active_dealer_prev_month) > 10)  	$dealer_achv_percent='150';	
		
			$sis_percent=(($ach_percent*55)/100)+(($unique_visit_achv_percent*15)/100)+(($dealer_achv_percent*30)/100);
			$sis_percent=round($sis_percent,2);	
			$sis_earned=$sis_percent*$level_incentive;
			$sis_earned=round($sis_earned,2);
			}
			if(substr($level,1,1)=='2') $penalty_val=${month_l2_penalty.$mnthval.$emp_code_lower};
			if(substr($level,1,1)>='3') $penalty_val=${month_l3_above_penalty.$mnthval.$emp_code_lower};
			
			$penalty_deducted=$sis_earned*$penalty_val;
			$penalty_deducted=round($penalty_deducted,2);
			$net_SIS_earned=$sis_earned-$penalty_deducted;

			//$penalty_deducted=0;
			
			//$net_sis_earned=0;
			$countsis++;
			$res_data[] = array("emp_code"=>$emp_code_lower,"emp_name"=>$emp_name,"year"=>$year,"month"=>$mnthval,"base_volume"=>$base_volume,"volume_achv"=>$volume_achv,"achv_percent"=>$ach_percent,"monthly_unique_visit_target"=>$monthly_unique_visit_target,"monthly_unique_visit_achv"=>$monthly_unique_visit_acv,"dealer_appointment_tgt"=>$dealer_appointment_tgt,"dealer_appointment_achv"=>$dealer_appointment_achv,"active_dealer_cur_month"=>$active_dealer_current_month,"active_dealer_prev_month"=>$active_dealer_prev_month,"sis_earned"=>$sis_earned,"penalty_deducted"=>$penalty_deducted,"net_sis_earned"=>$net_SIS_earned);
		}
	//}
}
	if($countsis > 0)
		{
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
			
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			$countcolumns='16';
			$contentsdatetime =$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;

			$res_data_final = array("process_status"=>"YES","process_message"=>"Success.","countrows"=>$countsis,"countcolumns"=>$countcolumns,"datetime"=>$contentsdatetime,"datavalue"=>$res_data );
		}
		else
		{
			$res_data_final = array("process_status"=>"NO","process_message"=>"Something Went Wrong." );
		}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/sis-region-level-wise-emp-data-json.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	/*header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=tech-meet-approval.txt");
	print "$datacontents"; */
	echo json_encode($res_data_final);
	mysqli_close($link);		
?>
