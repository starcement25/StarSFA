<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition='emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="emp_code='".$emp_code."'";
}

  if(achievement_sale_value_wise=='yes')
   {
	$vertical_array=array();
	$sqlseldistvertical="SELECT DISTINCT vertical_value FROM product_master WHERE acedns='Y' ORDER BY vertical_value ASC";
	$rsseldistvertical=mysqli_query($link,$sqlseldistvertical);	
	while($rowseldistvertical=mysqli_fetch_assoc($rsseldistvertical))
	{
		if(!in_array($rowseldistvertical['vertical_value'],$vertical_array))
		{
			array_push($vertical_array,$rowseldistvertical['vertical_value']);
		}
	}

	$sql_emp = "SELECT emp_code, dns_emp_code, emp_name FROM employee_master WHERE ".$emp_hierarchy_condition."  ORDER BY emp_name ASC";
	$res_emp = mysqli_query($link,$sql_emp);
	$count=mysqli_num_rows($res_emp);
	//print_r($vertical_array);
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
		while($row_emp = mysqli_fetch_assoc($res_emp)){
		$dns_emp_code = $row_emp['dns_emp_code'];
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
				foreach($vertical_array as $vertical_val)
				{
					$start_date=$year.'-04-01';
					$end_date=($year+1).'-03-31';
					$sqlachivementval="SELECT SUM(POCM.amount) AS total_amount,SUBSTRING(POCM.visit_date,6,2) As month
									  FROM 
									prev_order_counting_master POCM,product_master PM WHERE  POCM.product_code=PM.prod_code AND 
									SUBSTRING(POCM.order_no,2,5)='".$emp_code."' AND PM.vertical_value='".$vertical_val."' 
									AND SUBSTRING(POCM.visit_date,1,10) 
									BETWEEN '".$start_date."' AND '".$end_date."' AND POCM.cust_type='R' GROUP BY  SUBSTRING(POCM.visit_date,6,2) 
									ORDER BY SUBSTRING(POCM.visit_date,6,2) ASC";
					$rsachivementval=mysqli_query($link,$sqlachivementval);				
					while($rowachivementval=mysqli_fetch_assoc($rsachivementval))
					{
						$total_amount=$rowachivementval['total_amount'];
						$monthval=$rowachivementval['month'];
						${achievement_val.$emp_code.$vertical_val.$monthval}=$total_amount;
					}
					$sqlqueryappraisal="SELECT SEW.product_group_code,SEW.prod_code,SEW.jan_31_target,SEW.jan_31_achievement,SEW.feb_28_target,SEW.feb_28_achievement,SEW.mar_31_target,
								SEW.mar_31_achievement,SEW.apr_30_target,SEW.apr_30_achievement,SEW.may_31_target,SEW.may_31_achievement,SEW.jun_30_target,
								SEW.jun_30_achievement,SEW.jul_31_target,SEW.jul_31_achievement,SEW.aug_31_target,SEW.aug_31_achievement,SEW.sep_30_target,
								SEW.sep_30_achievement,SEW.oct_31_target,SEW.oct_31_achievement,SEW.nov_30_target,SEW.nov_30_achievement,SEW.dec_31_target,SEW.dec_31_achievement 
								FROM self_appraisal_emp_wise SEW WHERE SEW.emp_code = '".$dns_emp_code."' AND SEW.vertical='".$vertical_val."'";
					$rsqueryappraisal=mysqli_query($link,$sqlqueryappraisal);
					$rowsappraisal=mysqli_fetch_assoc($rsqueryappraisal);

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
								}
								if($i==2)
								{
									$column_target=$rowsappraisal['feb_28_target'];
								}
								if($i==3)
								{
									$column_target=$rowsappraisal['mar_31_target'];
								}
								if($i==4)
								{
									$column_target=$rowsappraisal['apr_30_target'];
								}
								if($i==5)
								{
									$column_target=$rowsappraisal['may_31_target'];
								}
								if($i==6)
								{
									$column_target=$rowsappraisal['jun_30_target'];
								}
								if($i==7)
								{
									$column_target=$rowsappraisal['jul_31_target'];
								}
								if($i==8)
								{
									$column_target=$rowsappraisal['aug_31_target'];
								}
								if($i==9)
								{
									$column_target=$rowsappraisal['sep_30_target'];
								}
								if($i==10)
								{
									$column_target=$rowsappraisal['oct_31_target'];
								}
								if($i==11)
								{
									$column_target=$rowsappraisal['nov_30_target'];
								}
								if($i==12)
								{
									$column_target=$rowsappraisal['dec_31_target'];
								}
								$column_achievement=${achievement_val.$emp_code.$vertical_val.$i};
								$contents  = (($emp_code!='')?$emp_code: ' ')."^";
								$contents  .= (($emp_name!='')?trim(preg_replace('/[\r\n]+/', '',$emp_name)): ' ')."^";
								$contents  .= (($vertical_val!='')?$vertical_val: ' ')."^";
								$contents  .= (($rowsappraisal['product_group_code']!='')?$rowsappraisal['product_group_code']: ' ')."^";
								$contents  .= (($rowsappraisal['prod_code']!='')?$rowsappraisal['prod_code']: ' ')."^";
								$contents  .= $i."^";
								$contents  .= (($column_target!='')?round($column_target,0): ' ')."^";
								$contents  .= (($column_achievement!='')?round($column_achievement,0): ' ');
								$linecontents  .= $contents."\n";
								$countappraisal++;	
						       }
							 }//end vertical for each
						}
		 		$contentsrowcolumn=$countappraisal.'¥'.'8';
				$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
			}
			else
			{
				$datacontents = '0'.'¥'.'0';
			}
     }

	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/target-achievement-txt-empwise-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=emp_wise_target_ach.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
