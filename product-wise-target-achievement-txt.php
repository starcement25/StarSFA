<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' SAPW.emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition=" SAPW.emp_code='".$emp_code."'";
}

$sqlquery="SELECT DISTINCT SAPW.prod_code,PM.prod_desc,SAPW.emp_code, SAPW.jan_31_target,SAPW.jan_31_achievement,SAPW.feb_28_target,SAPW.feb_28_achievement,SAPW.mar_31_target,
SAPW.mar_31_achievement,SAPW.apr_30_target,SAPW.apr_30_achievement,SAPW.may_31_target,SAPW.may_31_achievement,SAPW.jun_30_target,
SAPW.jun_30_achievement,SAPW.jul_31_target,SAPW.jul_31_achievement,SAPW.aug_31_target,SAPW.aug_31_achievement,SAPW.sep_30_target,
SAPW.sep_30_achievement,SAPW.oct_31_target,SAPW.oct_31_achievement,SAPW.nov_30_target,SAPW.nov_30_achievement,SAPW.dec_31_target,
SAPW.dec_31_achievement,SAPW.jan_prev_y_target,SAPW.jan_prev_y_achievement,SAPW.feb_prev_y_target,SAPW.feb_prev_y_achievement,SAPW.mar_prev_y_target,SAPW.mar_prev_y_achievement,SAPW.apr_prev_y_target,SAPW.apr_prev_y_achievement,SAPW.may_prev_y_target,SAPW.may_prev_y_achievement,SAPW.jun_prev_y_target,
SAPW.jun_prev_y_achievement,SAPW.jul_prev_y_target,SAPW.jul_prev_y_achievement,SAPW.aug_prev_y_target,SAPW.aug_prev_y_achievement,SAPW.sep_prev_y_target,SAPW.sep_prev_y_achievement,SAPW.oct_prev_y_target,SAPW.oct_prev_y_achievement,SAPW.nov_prev_y_target,SAPW.nov_prev_y_achievement,SAPW.dec_prev_y_target,
SAPW.dec_prev_y_achievement FROM self_appraisal_product_wise SAPW,product_master PM WHERE SAPW.prod_code = PM.prod_code  AND ".$emp_hierarchy_condition."";

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
				if(previous_year=='yes')
				{
					$column_prev_target=strtolower($month_abrev).'_prev_y_target';
					$column_prev_target=$rowsappraisal[$column_prev_target];
					$coumn_prev_achievement=strtolower($month_abrev).'_prev_y_achievement';
					$coumn_prev_achievement=$rowsappraisal[$coumn_prev_achievement];
				}
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
				
				$contents  = (($rowsappraisal['prod_code']!='')?$rowsappraisal['prod_code']: ' ')."^";
				$contents  .= (($rowsappraisal['prod_desc']!='')?trim(preg_replace('/[\r\n]+/', '',$rowsappraisal['prod_desc'])): ' ')."^";
				$contents  .= (($rowsappraisal['emp_code']!='')?$rowsappraisal['emp_code']: ' ')."^";
				$contents  .= $i."^";
				$contents  .= (($column_target!='')?$column_target: ' ')."^";
				$contents  .= (($column_achievement!='')?$column_achievement: ' ')."^";
				$contents  .= (($column_prev_target!='')?$column_prev_target: ' ')."^";
				$contents  .= (($coumn_prev_achievement!='')?$coumn_prev_achievement: ' ');
				$linecontents  .= $contents."\n";
				$countappraisal++;	
			}
		}
		$contentsrowcolumn=$countappraisal.'¥'.'8';

		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/product-wise-target-achievement-txt.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=product_wise_target_ach.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
