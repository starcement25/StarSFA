<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];

/*$sqlempfunctionality="SELECT functionality,functionality_rel_val FROM employee_master WHERE emp_code='".$emp_code."'";
$rsempfunctionality=mysqli_query($link,$sqlempfunctionality);
$rowempfunctionality=mysqli_fetch_assoc($rsempfunctionality);
$functionality_rel_val=$rowempfunctionality['functionality_rel_val'];*/
if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition='emp_code IN('.$employee_hierarchy.')';
	$employee_hierarchy_array=explode(",",$employee_hierarchy);
}
else
{
	$emp_hierarchy_condition="emp_code='".$emp_code."'";
}
$sqlempcust="SELECT CM.customer_code,CM.retailer_app,CM.cust_type,CM.rds_tag FROM customer_route_emp_relation CRR,customer_master CM
				WHERE CM.customer_code=CRR.customer_code AND CRR.emp_code='".$emp_code."' AND CRR.acedns='Y'";
$rsempcust=mysqli_query($link,$sqlempcust); 
$rowempcust=mysqli_fetch_assoc($rsempcust);
$customer_code_emp=$rowempcust['customer_code'];
$retailer_app=$rowempcust['retailer_app'];
$cust_type=$rowempcust['cust_type'];
$rds_tag=$rowempcust['rds_tag'];
if($cust_type!='D')
{
if($retailer_app=='yes' && $rds_tag!='' && $cust_type=='R')
	{
		$sqlempcust="SELECT customer_code FROM customer_route_emp_relation WHERE acedns='Y' AND 
				customer_code IN(SELECT customer_code FROM customer_master WHERE rds_tag='".$rds_tag."' AND acedns='Y' AND cust_type='R')";
		$rsempcust=mysqli_query($link,$sqlempcust); 
		while($rowempcust=mysqli_fetch_assoc($rsempcust))
		{
			$customer_code=$customer_code."'".$rowempcust['customer_code']."'".',';
		}
		$customer_code=substr($customer_code,0,-1);
	}
	else
	{
		$sqlempcust="SELECT customer_code FROM customer_route_emp_relation WHERE ".$emp_hierarchy_condition." AND acedns='Y'";
		$rsempcust=mysqli_query($link,$sqlempcust); 
		while($rowempcust=mysqli_fetch_assoc($rsempcust))
		{
			$customer_code=$customer_code."'".$rowempcust['customer_code']."'".',';
		}
		$customer_code=substr($customer_code,0,-1);
	}

$sqlquery="SELECT * FROM customer_product_billing WHERE customer_code IN (".$customer_code.")";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

	if($count>0){
		$contentsrowcolumn=$count.'¥'.'5';
		while($rowbilling = mysqli_fetch_assoc($result))
		{
			if($retailer_app=='yes' && $rds_tag!='' && $cust_type=='R')
			{
				$customer_code=$customer_code_emp;
			}
			else
			{
				$customer_code=$rowbilling['customer_code'];
			}
			$prod_code=$rowbilling['prod_code'];
			$IMEI=$rowbilling['IMEI'];
			$stock_out_date=$rowbilling['stock_out_date'];
			$invoice_date=$rowbilling['invoice_date'];
			
			if($prod_code!='')
			{
				$contents  = (($customer_code!='')?$customer_code: ' ')."^";
				$contents  .= (($prod_code!='')?$prod_code: ' ')."^";
				$contents  .= (($IMEI!='')?$IMEI: ' ')."^";
				$contents  .= (($stock_out_date!='')?$stock_out_date: ' ')."^";
				$contents  .= (($invoice_date!='')?$invoice_date: ' ');
				$linecontents  .= $contents."\n";
			}
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
}
else
{
	$datacontents = '0'.'¥'.'0';
}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/billing-information-txt-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=billing_information.txt");
	print "$datacontents"; 		
?>
