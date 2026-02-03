<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', '-1');
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' CRER.emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition=" CRER.emp_code='".$emp_code."'";
}

$sqlquery="SELECT DISTINCT CPOD.customer_code,CPOD.prod_code,CPOD.jan_plan,CPOD.jan_purchase,CPOD.feb_plan,CPOD.feb_purchase,CPOD.mar_plan,
CPOD.mar_purchase,CPOD.apr_plan,CPOD.apr_purchase,CPOD.may_plan,CPOD.may_purchase,CPOD.jun_plan,
CPOD.jun_purchase,CPOD.jul_plan,CPOD.jul_purchase,CPOD.aug_plan,CPOD.aug_purchase,CPOD.sep_plan,
CPOD.sep_purchase,CPOD.oct_plan,CPOD.oct_purchase,CPOD.nov_plan,CPOD.nov_purchase,CPOD.dec_plan,CPOD.dec_purchase 
FROM customer_product_wise_orderplan_details CPOD,customer_route_emp_relation CRER WHERE CPOD.customer_code = CRER.customer_code  AND ".$emp_hierarchy_condition."";

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
		$countorderplan=0;
		while($roworderplan = mysqli_fetch_assoc($result))
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
				$column_purchase=strtolower($month_abrev).'_purchase';
				$column_plan=strtolower($month_abrev).'_plan';

				$contents  = (($roworderplan['customer_code']!='')?$roworderplan['customer_code']: ' ')."^";
				$contents  .= (($roworderplan['prod_code']!='')?$roworderplan['prod_code']: ' ')."^";
				$contents  .= $i."^";
				$contents  .= (($roworderplan[$column_purchase]!='')?$roworderplan[$column_purchase]: ' ')."^";
				$contents  .= (($roworderplan[$column_plan]!='')?$roworderplan[$column_plan]: ' ');
				$linecontents  .= $contents."\n";
				$countorderplan++;	
			}
		}
		$contentsrowcolumn=$countorderplan.'¥'.'5';

		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/cust-product-wise-orderplan.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=customer_prod_wise_orderplan.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
