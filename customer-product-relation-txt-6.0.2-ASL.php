<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);

if(employeewise_hierarchy=='yes'){
  $employee_hierarchy=return_employee_hierarchy($emp_code);
  $emp_hierarchy_condition=' AND CM.emp_code IN('.$employee_hierarchy.')';
}
else
{
  $emp_hierarchy_condition=" AND CM.emp_code='".$emp_code."'";
}
if($incremental_download=='no')
{
	$login_condition=" AND PM.acedns='Y'";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(CPR.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}
if(strtoupper(substr($emp_code,0,1))=='E')
{
$sqlquery="SELECT DISTINCT CPR.customer_code,PM.prod_code,CPR.acedns,CPR.mcx_rate_parameter,CPR.TD,CPR.premium FROM customer_product_relation CPR 
			INNER JOIN 
			customer_route_emp_relation CM ON CPR.customer_code=CM.customer_code 
			INNER JOIN product_group_master PGM ON PGM.product_group_name=CPR.oil_category
			INNER JOIN product_master PM ON PM.product_group_code=PGM.product_group_code 
			WHERE PM.acedns='Y'".$login_condition.$emp_hierarchy_condition."";
}
if(strtoupper(substr($emp_code,0,1))=='B')
{
$sqlquery="SELECT DISTINCT CPR.customer_code,PM.prod_code,CPR.acedns,CPR.mcx_rate_parameter,CPR.TD,CPR.premium FROM customer_product_relation CPR 
			INNER JOIN 
			customer_broker_relation CM ON CPR.customer_code=CM.customer_code 
			INNER JOIN product_group_master PGM ON PGM.product_group_name=CPR.oil_category
			INNER JOIN product_master PM ON PM.product_group_code=PGM.product_group_code 
			WHERE PM.acedns='Y'".$login_condition." AND CM.broker_code='".$emp_code."'";
}
if(strtoupper(substr($emp_code,0,1))=='C')
{
$sqlquery="SELECT DISTINCT CPR.customer_code,PM.prod_code,CPR.acedns,CPR.mcx_rate_parameter,CPR.TD,CPR.premium FROM customer_product_relation CPR 
			INNER JOIN 
			customer_master CM ON CPR.customer_code=CM.customer_code 
			INNER JOIN product_group_master PGM ON PGM.product_group_name=CPR.oil_category
			INNER JOIN product_master PM ON PM.product_group_code=PGM.product_group_code 
			WHERE PM.acedns='Y'".$login_condition." AND CM.customer_code='".$emp_code."'";
}
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

	$contentsrowcolumn=$count.'¥'.'6';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		
		while($rowcustomerrel = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowcustomerrel['customer_code']!='')?$rowcustomerrel['customer_code']: ' ')."^";
			$contents  .= (($rowcustomerrel['prod_code']!='')?$rowcustomerrel['prod_code']: ' ')."^";
			$contents  .= (($rowcustomerrel['acedns']!='')?$rowcustomerrel['acedns']: ' ')."^";
			$contents  .= (($rowcustomerrel['mcx_rate_parameter']!='')?$rowcustomerrel['mcx_rate_parameter']: ' ')."^";
			$contents  .= (($rowcustomerrel['premium']!='')?$rowcustomerrel['premium']: ' ')."^";
			$contents  .= (($rowcustomerrel['TD']!='')?$rowcustomerrel['TD']: ' ');
			$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$last_update_time=str_replace('?','',$last_update_time);
		$data_download_time=str_replace('?','',$data_download_time);
		if(strtotime($data_download_time)>=strtotime($last_update_time))
		{
			$datacontents = '0'.'¥'.'0';
		}
		else
		{
			$datacontents = '0'.'¥'.'6';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/customer-product-relation-txt-6.0.2.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=customer_product_rel.txt");
	print "$datacontents"; 		
?>
