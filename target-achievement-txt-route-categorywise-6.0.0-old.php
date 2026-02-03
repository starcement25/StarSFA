<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition=" emp_code='".$emp_code."'";
}

	   if(employeewise_hierarchy=='yes'){
			$employee_hierarchy=return_employee_hierarchy($emp_code);
			$emp_hierarchy_condition='SCW.emp_code IN('.$employee_hierarchy.')';
		}
		else
		{
			$emp_hierarchy_condition="SCW.emp_code='".$emp_code."'";
		}
	
	$sqlquery="SELECT DISTINCT SCW.emp_code,SCW.emp_name,SCW.route_code,SCW.route_name,SCW.product_category, SCW.jan_31_target,SCW.jan_31_achievement,SCW.feb_28_target,SCW.feb_28_achievement,SCW.mar_31_target,
			SCW.mar_31_achievement,SCW.apr_30_target,SCW.apr_30_achievement,SCW.may_31_target,SCW.may_31_achievement,SCW.jun_30_target,
			SCW.jun_30_achievement,SCW.jul_31_target,SCW.jul_31_achievement,SCW.aug_31_target,SCW.aug_31_achievement,SCW.sep_30_target,
			SCW.sep_30_achievement,SCW.oct_31_target,SCW.oct_31_achievement,SCW.nov_30_target,SCW.nov_30_achievement,SCW.dec_31_target,SCW.dec_31_achievement 
			FROM self_appraisal_route_product_group_wise SCW WHERE  ".$emp_hierarchy_condition."";
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	
		if($count>0){
			$emp_roue_array=array();
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
			/*$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
			
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";*/
				$emp_code_db=$rowsappraisal['emp_code'];
				$emp_name=$rowsappraisal['emp_name'];
				$route_code=$rowsappraisal['route_code'];
				$route_name=$rowsappraisal['route_name'];
				$product_category=$rowsappraisal['product_category'];
				${'emp_name'.$emp_code_db.$route_code}=$emp_name;
				${'route_name'.$emp_code_db.$route_code}=$route_name;
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
					/*$contents  = (($rowsappraisal['customer_code']!='')?$rowsappraisal['customer_code']: ' ')."^";
					$contents  .= (($rowsappraisal['customer_name']!='')?trim(preg_replace('/[\r\n]+/', '',$rowsappraisal['customer_name'])): ' ')."^";
					$contents  .= ' '."^";
					$contents  .= ' '."^";
					$contents  .= ' '."^";
					$contents  .= $i."^";
					$contents  .= (($column_target!='')?round($column_target,0): ' ')."^";
					$contents  .= (($column_achievement!='')?round($column_achievement,0): ' ');*/
					if($i==9)
					{
					echo ${'target'.$emp_code_db.$route_code.$product_category.$i}=$column_target;
					}
					${'achievement'.$emp_code_db.$route_code.$product_category.$i}=$column_achievement;
					$emp_route_string=$emp_code_db.'#'.$route_code;
					
					if(!in_array($emp_route_string,$emp_roue_array))
					{
						array_push($emp_roue_array,$emp_route_string);
					}
					
					/*$contents  = 'E0082'."^";
					$contents  .= 'Test FP'."^";
					$contents  .= 'RT/10'."^";
					$contents  .= 'Burdwan'."^";
					$contents  .= '1'."^";
					$contents  .= 'Burdwan'."^";
					$contents  .= '400'."^";
					$contents  .= '200'."^";
					$contents  .= '350'."^";
					$contents  .= '210'."^";
					$contents  .= '190'."^";
					$contents  .= '159'."^";
					$contents  .= $i;*/
					
					//$linecontents  .= $contents."\n";
					//$countappraisal++;	
				}
			}
			//print_r($emp_roue_array);
			echo ${'target'.'E0066'.'RT/8'.'DTS'.'9'};
			foreach($emp_roue_array as $emp_roue_val)
			{
				$emp_route_val_array=explode('#',$emp_roue_val);
				$emp_code=$emp_route_val_array[0];
				$route_code=$emp_route_val_array[1];
				for($i=1;$i<=12;$i++)
				{
					$contents  = (($emp_code!='')?$emp_code: ' ')."^";
					$contents  .= ((${'emp_name'.$emp_code.$route_code}!='')?trim(preg_replace('/[\r\n]+/', '',${'emp_name'.$emp_code.$route_code})): ' ')."^";
					$contents  .= (($route_code!='')?$route_code: ' ')."^";
					$contents  .= ((${'route_name'.$emp_code.$route_code}!='')?trim(preg_replace('/[\r\n]+/', '',${'route_name'.$emp_code.$route_code})): ' ')."^";
					$contents  .= ' '."^";
					$contents  .= ' '."^";
					for($k=1;$k<=3;$k++)
					{
						if($k==1)
						{
						 $product_category='DTS';
						}
						if($k==2)
						{
							$product_category='DW';
						}
						if($k==3)
						{
							$product_category='Other';
						}
						//echo ${'target'.$emp_code.$route_code.$product_category.$i};
						
						$contents  .= ((${'target'.$emp_code.$route_code.$product_category.$i}!='')?round(${'target'.$emp_code.$route_code.$product_category.$i},0): ' ')."^";
						$contents  .= ((${'achievement'.$emp_code.$route_code.$product_category.$i}!='')?round(${'achievement'.$emp_code.$route_code.$product_category.$i},0): ' ')."^";
					}
					$contents  .= $i;
					$linecontents  .= $contents."\n";
					$countappraisal++;
				}
			}
			$contentsrowcolumn=$countappraisal.'¥'.'13';
	
			$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
		}
		else
		{
			$datacontents = '0'.'¥'.'0';
		}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/target-achievement-txt-route-categorywise-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	//header("Content-type: application/text"); 
	//header("Content-Disposition: attachment; filename=route_product_category_wise_target_ach.txt");
	//print "$datacontents"; 
	mysqli_close($link);		
?>
