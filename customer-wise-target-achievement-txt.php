<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];

$sqlempfunctionality="SELECT functionality,functionality_rel_val FROM employee_master WHERE emp_code='".$emp_code."'";
$rsempfunctionality=mysqli_query($link,$sqlempfunctionality);
$rowempfunctionality=mysqli_fetch_assoc($rsempfunctionality);
$functionality=$rowempfunctionality['functionality'];
$functionality_rel_val=$rowempfunctionality['functionality_rel_val'];
if(strtoupper($functionality)=='DOS'){
if(providing_code=='yes')
{
	if(modified_customer_emp_route=='yes')
	{
	  $sqlquery="SELECT DISTINCT SCW.customer_code,CMA.customer_name,'',SCW.jan_31_target,SCW.jan_31_achievement,SCW.feb_28_target,SCW.feb_28_achievement,
		SCW.mar_31_target,SCW.mar_31_achievement,SCW.apr_30_target,SCW.apr_30_achievement,SCW.may_31_target,SCW.may_31_achievement,
		SCW.jun_30_target,SCW.jun_30_achievement,SCW.jul_31_target,SCW.jul_31_achievement,SCW.aug_31_target,SCW.aug_31_achievement,
		SCW.sep_30_target,SCW.sep_30_achievement,SCW.oct_31_target,SCW.oct_31_achievement,SCW.nov_30_target,SCW.nov_30_achievement,SCW.dec_31_target,
		SCW.dec_31_achievement FROM self_appraisal_customer_wise SCW,customer_route_emp_relation CM,customer_master CMA WHERE 
		SCW.customer_code = CMA.dns_customer_code AND CMA.customer_code=CM.customer_code AND CMA.customer_code='".$functionality_rel_val."'";
	}
}
else
{
	if(modified_customer_emp_route=='yes')
	{
		$sqlquery="SELECT DISTINCT SCW.customer_code,CMA.customer_name,'', SCW.jan_31_target,SCW.jan_31_achievement,SCW.feb_28_target,SCW.feb_28_achievement,SCW.mar_31_target,
		SCW.mar_31_achievement,SCW.apr_30_target,SCW.apr_30_achievement,SCW.may_31_target,SCW.may_31_achievement,SCW.jun_30_target,
		SCW.jun_30_achievement,SCW.jul_31_target,SCW.jul_31_achievement,SCW.aug_31_target,SCW.aug_31_achievement,SCW.sep_30_target,
		SCW.sep_30_achievement,SCW.oct_31_target,SCW.oct_31_achievement,SCW.nov_30_target,SCW.nov_30_achievement,SCW.dec_31_target,SCW.dec_31_achievement 
		FROM self_appraisal_customer_wise SCW,customer_route_emp_relation CM,customer_master CMA WHERE 
		SCW.customer_code = CMA.customer_code AND CMA.customer_code=CM.customer_code AND CMA.customer_code='".$functionality_rel_val."'";
	}
}

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
				$contents  = (($rowsappraisal['customer_code']!='')?$rowsappraisal['customer_code']: ' ')."^";
				$contents  .= (($rowsappraisal['customer_name']!='')?trim(preg_replace('/[\r\n]+/', '',$rowsappraisal['customer_name'])): ' ')."^";
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
}
else
{
if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition='CM.emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="CM.emp_code='".$emp_code."'";
}
if(providing_code=='yes')
{
	if(modified_customer_emp_route=='yes')
	{
	  $sqlquery="SELECT DISTINCT SCW.customer_code,CMA.customer_name,'',SCW.jan_31_target,SCW.jan_31_achievement,SCW.feb_28_target,SCW.feb_28_achievement,
		SCW.mar_31_target,SCW.mar_31_achievement,SCW.apr_30_target,SCW.apr_30_achievement,SCW.may_31_target,SCW.may_31_achievement,
		SCW.jun_30_target,SCW.jun_30_achievement,SCW.jul_31_target,SCW.jul_31_achievement,SCW.aug_31_target,SCW.aug_31_achievement,
		SCW.sep_30_target,SCW.sep_30_achievement,SCW.oct_31_target,SCW.oct_31_achievement,SCW.nov_30_target,SCW.nov_30_achievement,SCW.dec_31_target,
		SCW.dec_31_achievement FROM self_appraisal_customer_wise SCW,customer_route_emp_relation CM,customer_master CMA WHERE 
		SCW.customer_code = CMA.dns_customer_code AND CMA.customer_code=CM.customer_code AND  ".$emp_hierarchy_condition."";
	}
	else
	{
		$sqlquery="SELECT DISTINCT SCW.customer_code,CM.customer_name,'', SCW.jan_31_target,SCW.jan_31_achievement,SCW.feb_28_target,SCW.feb_28_achievement,SCW.mar_31_target,
		SCW.mar_31_achievement,SCW.apr_30_target,SCW.apr_30_achievement,SCW.may_31_target,SCW.may_31_achievement,SCW.jun_30_target,
		SCW.jun_30_achievement,SCW.jul_31_target,SCW.jul_31_achievement,SCW.aug_31_target,SCW.aug_31_achievement,SCW.sep_30_target,
		SCW.sep_30_achievement,SCW.oct_31_target,SCW.oct_31_achievement,SCW.nov_30_target,SCW.nov_30_achievement,SCW.dec_31_target,SCW.dec_31_achievement 
		FROM self_appraisal_customer_wise SCW,customer_master CM WHERE 
		SCW.customer_code = CM.dns_customer_code AND ".$emp_hierarchy_condition."";
	}
}
else
{
	if(modified_customer_emp_route=='yes')
	{
		$sqlquery="SELECT DISTINCT SCW.customer_code,CMA.customer_name,'', SCW.jan_31_target,SCW.jan_31_achievement,SCW.feb_28_target,SCW.feb_28_achievement,SCW.mar_31_target,
		SCW.mar_31_achievement,SCW.apr_30_target,SCW.apr_30_achievement,SCW.may_31_target,SCW.may_31_achievement,SCW.jun_30_target,
		SCW.jun_30_achievement,SCW.jul_31_target,SCW.jul_31_achievement,SCW.aug_31_target,SCW.aug_31_achievement,SCW.sep_30_target,
		SCW.sep_30_achievement,SCW.oct_31_target,SCW.oct_31_achievement,SCW.nov_30_target,SCW.nov_30_achievement,SCW.dec_31_target,SCW.dec_31_achievement 
		FROM self_appraisal_customer_wise SCW,customer_route_emp_relation CM,customer_master CMA WHERE 
		SCW.customer_code = CMA.customer_code AND CMA.customer_code=CM.customer_code AND ".$emp_hierarchy_condition."";
	}
	else
	{
		$sqlquery="SELECT DISTINCT SCW.customer_code,CM.customer_name,'', SCW.jan_31_target,SCW.jan_31_achievement,SCW.feb_28_target,SCW.feb_28_achievement,SCW.mar_31_target,
		SCW.mar_31_achievement,SCW.apr_30_target,SCW.apr_30_achievement,SCW.may_31_target,SCW.may_31_achievement,SCW.jun_30_target,
		SCW.jun_30_achievement,SCW.jul_31_target,SCW.jul_31_achievement,SCW.aug_31_target,SCW.aug_31_achievement,SCW.sep_30_target,
		SCW.sep_30_achievement,SCW.oct_31_target,SCW.oct_31_achievement,SCW.nov_30_target,SCW.nov_30_achievement,SCW.dec_31_target,SCW.dec_31_achievement 
		FROM self_appraisal_customer_wise SCW,customer_master CM WHERE 
		SCW.customer_code = CM.customer_code AND ".$emp_hierarchy_condition."";
	}
}

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
				$contents  = (($rowsappraisal['customer_code']!='')?$rowsappraisal['customer_code']: ' ')."^";
				$contents  .= (($rowsappraisal['customer_name']!='')?trim(preg_replace('/[\r\n]+/', '',$rowsappraisal['customer_name'])): ' ')."^";
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
}

	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/customer-wise-target-achievement-txt.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=customer_wise_target_ach.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
