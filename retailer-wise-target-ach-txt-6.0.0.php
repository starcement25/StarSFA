<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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


	$sql_customer = "SELECT * FROM retailer_wise_target_ach where 
					customer_code IN(SELECT DISTINCT customer_code FROM customer_route_emp_relation  
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
		$customer_code = $row_customer['customer_code'];
		$customer_name = $row_customer['customer_name'];
		$start_date = $row_customer['start_date'];
		$end_date = $row_customer['end_date'];
		$value_slab_target = $row_customer['value_slab_target'];
		$value_slab_ach = $row_customer['value_slab_ach'];
		$sku_count_target = $row_customer['sku_count_target'];
		$sku_count_ach = $row_customer['sku_count_ach'];
		$apr_freq_target = $row_customer['apr_freq_target'];
		$apr_freq_ach = $row_customer['apr_freq_ach'];
		$may_freq_target = $row_customer['may_freq_target'];
		$may_freq_ach = $row_customer['may_freq_ach'];
		$jun_freq_target = $row_customer['jun_freq_target'];
		$jun_freq_ach = $row_customer['jun_freq_ach'];
		$acedns = $row_customer['acedns'];
			 
			$contents  = (($customer_code!='')?$customer_code: ' ')."^";
			$contents  .= (($customer_name!='')?trim(preg_replace('/[\r\n]+/', '',$customer_name)): ' ')."^";
			$contents  .= (($start_date!='')?$start_date: ' ')."^";
			$contents  .= (($end_date!='')?$end_date: ' ')."^";
			$contents  .= (($value_slab_target!='')?$value_slab_target: ' ')."^";
			$contents  .= (($value_slab_ach!='')?$value_slab_ach: ' ')."^";
			$contents  .= (($sku_count_target!='')?$sku_count_target: ' ')."^";
			$contents  .= (($sku_count_ach!='')?$sku_count_ach: ' ')."^";
			$contents  .= (($apr_freq_target!='')?$apr_freq_target: ' ')."^";
			$contents  .= (($apr_freq_ach!='')?$apr_freq_ach: ' ')."^";
			$contents  .= (($may_freq_target!='')?$may_freq_target: ' ')."^";
			$contents  .= (($may_freq_ach!='')?$may_freq_ach: ' ')."^";
			$contents  .= (($jun_freq_target!='')?$jun_freq_target: ' ')."^";
			$contents  .= (($jun_freq_ach!='')?$jun_freq_ach: ' ')."^";
			$contents  .= (($acedns!='')?$acedns: ' ');
			$linecontents  .= $contents."\n";
			$countappraisal++;
		}
			$contentsrowcolumn=$countappraisal.'¥'.'15';
			$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
		}
		else
		{
			$datacontents = '0'.'¥'.'0';
		}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/retailer-wise-target-ach-txt-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=retailer-wise-target-ach.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
