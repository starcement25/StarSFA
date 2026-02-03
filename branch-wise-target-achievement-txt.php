<?php

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition='EM.emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="EM.emp_code='".$emp_code."'";
}

$sqlquery="SELECT DISTINCT SBW.branch_code, BM.branch_name,'', SBW.jan_31_target,SBW.jan_31_achievement,SBW.feb_28_target,SBW.feb_28_achievement,SBW.mar_31_target,
SBW.mar_31_achievement,SBW.apr_30_target,SBW.apr_30_achievement,SBW.may_31_target,SBW.may_31_achievement,SBW.jun_30_target,
SBW.jun_30_achievement,SBW.jul_31_target,SBW.jul_31_achievement,SBW.aug_31_target,SBW.aug_31_achievement,SBW.sep_30_target,
SBW.sep_30_achievement,SBW.oct_31_target,SBW.oct_31_achievement,SBW.nov_30_target,SBW.nov_30_achievement,SBW.dec_31_target,SBW.dec_31_achievement 
FROM self_appraisal_branch_wise SBW, branch_master BM,employee_master EM  WHERE SBW.branch_code = BM.branch_code AND EM.branch_code=BM.branch_code AND EM.acedns='Y' AND ".$emp_hierarchy_condition."";

$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		$countappraisal=0;
		while($rowsappraisal = mysqli_fetch_assoc($result))
		{
			for($i=1;$i<=12;$i++)
			{
				if(strlen($i)==1)
				{
					$i='0'.$i;
				}
				$month = date("$i"); // Current month
				$first_date='01'.'-'.$month.'-'.$year;
				$month_abrev=date('M',strtotime($first_date));
				$days = cal_days_in_month(CAL_GREGORIAN,$month,$year);
				$column_target=strtolower($month_abrev).'_'.$days.'_target';
				$coumn_achievement=strtolower($month_abrev).'_'.$days.'_achievement';

				if($i==1)
				{
					$column_target=$rowsappraisal['jan_31_target'];
					$column_achievement=$rowsappraisal['jan_31_achievement'];
				}
				if($i==2)
				{
					$column_target=$rowsappraisal['feb_28_target'];
					$column_achievement=$rowsappraisal['feb_28_achievement'];
				}
				if($i==3)
				{
					$column_target=$rowsappraisal['mar_31_target'];
					$column_achievement=$rowsappraisal['mar_31_achievement'];
				}
				if($i==4)
				{
					$column_target=$rowsappraisal['apr_30_target'];
					$column_achievement=$rowsappraisal['apr_30_achievement'];
				}
				if($i==5)
				{
					$column_target=$rowsappraisal['may_31_target'];
					$column_achievement=$rowsappraisal['may_31_achievement'];
				}
				if($i==6)
				{
					$column_target=$rowsappraisal['jun_30_target'];
					$column_achievement=$rowsappraisal['jun_30_achievement'];
				}
				if($i==7)
				{
					$column_target=$rowsappraisal['jul_31_target'];
					$column_achievement=$rowsappraisal['jul_31_achievement'];
				}
				if($i==8)
				{
					$column_target=$rowsappraisal['aug_31_target'];
					$column_achievement=$rowsappraisal['aug_31_achievement'];
				}
				if($i==9)
				{
					$column_target=$rowsappraisal['sep_30_target'];
					$column_achievement=$rowsappraisal['sep_30_achievement'];
				}
				if($i==10)
				{
					$column_target=$rowsappraisal['oct_31_target'];
					$column_achievement=$rowsappraisal['oct_31_achievement'];
				}
				if($i==11)
				{
					$column_target=$rowsappraisal['nov_30_target'];
					$column_achievement=$rowsappraisal['nov_30_achievement'];
				}
				if($i==12)
				{
					$column_target=$rowsappraisal['dec_31_target'];
					$column_achievement=$rowsappraisal['dec_31_achievement'];
				}
				
				$contents  = (($rowsappraisal['branch_code']!='')?$rowsappraisal['branch_code']: ' ')."^";
				$contents  .= (($rowsappraisal['branch_name']!='')?trim(preg_replace('/[\r\n]+/', '',$rowsappraisal['branch_name'])): ' ')."^";
				$contents  .= ' '."^";
				$contents  .= $i."^";
				$contents  .= (($column_target!='')?$column_target: ' ')."^";
				$contents  .= (($column_achievement!='')?$column_achievement: ' ');
				$linecontents  .= $contents."\n";
				$countappraisal++;	
			}
		}
		$contentsrowcolumn=$countappraisal.'¥'.'6';

		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/branch-wise-target-achievement-txt.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=branch_wise_target_ach.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
