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
	$sql_customer = "SELECT customer_code,customer_name,dns_customer_code FROM customer_master where 
					cust_type!='R' AND customer_code IN(SELECT DISTINCT customer_code FROM customer_route_emp_relation  
					WHERE ".$emp_hierarchy_condition.")  ORDER BY customer_name ASC";
	$res_customer = mysqli_query($link,$sql_customer);
	$count=mysqli_num_rows($res_customer);
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
		while($row_customer = mysqli_fetch_assoc($res_customer)){
		$dns_customer_code = $row_customer['dns_customer_code'];
		$customer_code = $row_customer['customer_code'];
		$customer_name = $row_customer['customer_name'];
			 foreach($vertical_array as $vertical_val)
			 {
				$start_date=$year.'-04-01';
				$end_date=($year+1).'-03-31';
				$sqlachivementval="SELECT SUM(amount) AS total_amount,SUBSTRING(invoice_date,6,2) As month
								  FROM 
								purchase_details WHERE  distributor_code='".$customer_code."' AND vertical_value='".$vertical_val."' 
								AND invoice_date 
								BETWEEN '".$start_date."' AND '".$end_date."' GROUP BY  SUBSTRING(invoice_date,6,2) 
								ORDER BY SUBSTRING(invoice_date,6,2) ASC";
				$rsachivementval=mysqli_query($link,$sqlachivementval);				
				while($rowachivementval=mysqli_fetch_assoc($rsachivementval))
				{
					$total_amount=$rowachivementval['total_amount'];
					$monthval=$rowachivementval['month'];
					${'achievement_val'.$dns_customer_code.$vertical_val.$monthval}=$total_amount;
				}
				$sqlqueryappraisal="SELECT SCW.product_group_code,SCW.prod_code,SCW.jan_31_target,SCW.jan_31_achievement,SCW.feb_28_target,SCW.feb_28_achievement,SCW.feb_29_achievement,
								SCW.mar_31_target,SCW.mar_31_achievement,SCW.apr_30_target,SCW.apr_30_achievement,SCW.may_31_target,SCW.may_31_achievement,
								SCW.jun_30_target,SCW.jun_30_achievement,SCW.jul_31_target,SCW.jul_31_achievement,SCW.aug_31_target,SCW.aug_31_achievement,
								SCW.sep_30_target,SCW.sep_30_achievement,SCW.oct_31_target,SCW.oct_31_achievement,SCW.nov_30_target,SCW.nov_30_achievement,SCW.dec_31_target,
								SCW.dec_31_achievement FROM self_appraisal_summary SCW WHERE 
								SCW.customer_code = '".$dns_customer_code."' AND SCW.vertical='".$vertical_val."'";
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
							$column_achievement=floor(${'achievement_val'.$dns_customer_code.$vertical_val.$i});
							$contents  = (($dns_customer_code!='')?$dns_customer_code: ' ')."^";
							$contents  .= (($customer_name!='')?trim(preg_replace('/[\r\n]+/', '',$customer_name)): ' ')."^";
							$contents  .= (($vertical_val!='')?$vertical_val: ' ')."^";
							$contents  .= (($rowsappraisal['product_group_code']!='')?$rowsappraisal['product_group_code']: ' ')."^";
							$contents  .= (($rowsappraisal['prod_code']!='')?$rowsappraisal['prod_code']: ' ')."^";
							$contents  .= $i."^";
							$contents  .= (($column_target!='')?$column_target: ' ')."^";
							$contents  .= (($column_achievement!='')?$column_achievement: ' ');
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
		  if(strtoupper($nick_name)=='STAR')
		  {
			  /*$sqlquery="SELECT DISTINCT SCW.customer_code,CMA.customer_name,'',SCW.jan_31_target,SCW.jan_31_achievement,SCW.feb_28_target,SCW.feb_28_achievement,
			SCW.mar_31_target,SCW.mar_31_achievement,SCW.apr_30_target,SCW.apr_30_achievement,SCW.may_31_target,SCW.may_31_achievement,
			SCW.jun_30_target,SCW.jun_30_achievement,SCW.jul_31_target,SCW.jul_31_achievement,SCW.aug_31_target,SCW.aug_31_achievement,
			SCW.sep_30_target,SCW.sep_30_achievement,SCW.oct_31_target,SCW.oct_31_achievement,SCW.nov_30_target,SCW.nov_30_achievement,SCW.dec_31_target,
			SCW.dec_31_achievement FROM self_appraisal_customer_wise SCW,customer_route_emp_relation CM,customer_master CMA WHERE 
			SCW.customer_code = CMA.dns_customer_code AND CMA.customer_code=CM.customer_code AND CMA.cust_type='Dealer' AND CM.acedns='Y' AND ".$emp_hierarchy_condition."";*/
			 $sqlquery="SELECT DISTINCT CM.customer_code,CMA.customer_name,CMA.cust_type,'',SCW.jan_31_target,SCW.jan_31_achievement,SCW.feb_28_target,SCW.feb_28_achievement,
			SCW.mar_31_target,SCW.mar_31_achievement,SCW.apr_30_target,SCW.apr_30_achievement,SCW.may_31_target,SCW.may_31_achievement,
			SCW.jun_30_target,SCW.jun_30_achievement,SCW.jul_31_target,SCW.jul_31_achievement,SCW.aug_31_target,SCW.aug_31_achievement,
			SCW.sep_30_target,SCW.sep_30_achievement,SCW.oct_31_target,SCW.oct_31_achievement,SCW.nov_30_target,SCW.nov_30_achievement,SCW.dec_31_target,
			SCW.dec_31_achievement,SCW.jan_prev_y_target,SCW.jan_prev_y_achievement,SCW.feb_prev_y_target,SCW.feb_prev_y_achievement,SCW.mar_prev_y_target,SCW.mar_prev_y_achievement,SCW.apr_prev_y_target,SCW.apr_prev_y_achievement,SCW.may_prev_y_target,SCW.may_prev_y_achievement,SCW.jun_prev_y_target,
SCW.jun_prev_y_achievement,SCW.jul_prev_y_target,SCW.jul_prev_y_achievement,SCW.aug_prev_y_target,SCW.aug_prev_y_achievement,SCW.sep_prev_y_target,SCW.sep_prev_y_achievement,SCW.oct_prev_y_target,SCW.oct_prev_y_achievement,SCW.nov_prev_y_target,SCW.nov_prev_y_achievement,SCW.dec_prev_y_target,
SCW.dec_prev_y_achievement FROM self_appraisal_customer_wise SCW,customer_route_emp_relation CM,customer_master CMA WHERE 
			SCW.customer_code = CMA.dns_customer_code AND CMA.customer_code=CM.customer_code AND 
			CMA.cust_type IN('Dealer','Exclusive Dealer') AND CM.acedns='Y' AND ".$emp_hierarchy_condition.""; 
		  }
		  else
		  {
		  $sqlquery="SELECT DISTINCT SCW.customer_code,CMA.customer_name,'',SCW.jan_31_target,SCW.jan_31_achievement,SCW.feb_28_target,SCW.feb_28_achievement,SCW.feb_29_achievement,
			SCW.mar_31_target,SCW.mar_31_achievement,SCW.apr_30_target,SCW.apr_30_achievement,SCW.may_31_target,SCW.may_31_achievement,
			SCW.jun_30_target,SCW.jun_30_achievement,SCW.jul_31_target,SCW.jul_31_achievement,SCW.aug_31_target,SCW.aug_31_achievement,
			SCW.sep_30_target,SCW.sep_30_achievement,SCW.oct_31_target,SCW.oct_31_achievement,SCW.nov_30_target,SCW.nov_30_achievement,SCW.dec_31_target,
			SCW.dec_31_achievement FROM self_appraisal_customer_wise SCW,customer_route_emp_relation CM,customer_master CMA WHERE 
			SCW.customer_code = CMA.dns_customer_code AND CMA.customer_code=CM.customer_code AND  ".$emp_hierarchy_condition."";
		  }
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
			$sqlquery="SELECT DISTINCT SCW.customer_code,CMA.customer_name,'', SCW.jan_31_target,SCW.jan_31_achievement,SCW.feb_28_target,SCW.feb_28_achievement,SCW.feb_29_achievement,SCW.mar_31_target,
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
	//echo $sqlquery;
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
						 if(strtoupper($nick_name)=='SUPERSHAKTI')
		  				{
							$column_achievement=$rowsappraisal['feb_29_achievement'];
						}
						else
						{
						$column_achievement=$rowsappraisal['feb_28_achievement'];
						}
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
					$contents  .= ' '."^";
					$contents  .= ' '."^";
					$contents  .= $i."^";
					$contents  .= (($column_target!='')?round($column_target,0): ' ')."^";
					$contents  .= (($column_achievement!='')?round($column_achievement,0): ' ')."^";
					if(previous_year=='yes')
					{
					 $contents  .= (($column_prev_target!='')?round($column_prev_target,0): ' ')."^";
					 $contents  .= (($coumn_prev_achievement!='')?round($coumn_prev_achievement,0): ' ');
					}
					$linecontents  .= $contents."\n";
					$countappraisal++;	
				}
			}
			if(previous_year=='yes')
			{
			 $contentsrowcolumn=$countappraisal.'¥'.'10';
			}
			else
			{
				$contentsrowcolumn=$countappraisal.'¥'.'8';
				
			}
	
			$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
		}
		else
		{
			$datacontents = '0'.'¥'.'0';
		}
	 }
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/target-achievement-txt-customerwise-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=customer_wise_target_ach.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
