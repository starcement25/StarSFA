<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code = $_REQUEST['emp_code'];
if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition='emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="emp_code='".$emp_code."'";
}
$sql_select_mall_id = "SELECT mall_id FROM mall_emp_relation WHERE ".$emp_hierarchy_condition;
$res_select_mall_id = mysqli_query($link,$sql_select_mall_id);
$count=mysqli_num_rows($res_select_mall_id);
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	
	$hour=gmdate('H',strtotime('+330 minute'));
	$minute=gmdate('i',strtotime('+330 minute'));
	$second=gmdate('s',strtotime('+330 minute'));
	$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
	$countrowcolumn=0;
	while($row_select_mall_id = mysqli_fetch_assoc($res_select_mall_id))
	{
		$mall_id = $row_select_mall_id['mall_id'];
		$sql_select_mall_data = "SELECT * FROM mall_master WHERE mall_id = '".$mall_id."' AND status='enable'";
		$res_select_mall_data = mysqli_query($link,$sql_select_mall_data);
		while($row_select_mall_data = mysqli_fetch_assoc($res_select_mall_data))
		{
			$countrowcolumn++;
			$contents  = (($row_select_mall_data['mall_id']!='')?$row_select_mall_data['mall_id']: ' ')."^";
			$contents  .= (($row_select_mall_data['mall_name']!='')?$row_select_mall_data['mall_name']: ' ')."^";
			$contents  .= (($row_select_mall_data['address']!='')?$row_select_mall_data['address']: ' ')."^";
			$contents  .= (($row_select_mall_data['landmark']!='')?$row_select_mall_data['landmark']: ' ')."^";
			$contents  .= (($row_select_mall_data['area']!='')?$row_select_mall_data['area']: ' ')."^";
			$contents  .= (($row_select_mall_data['city']!='')?$row_select_mall_data['city']: ' ')."^";
			$contents  .= (($row_select_mall_data['pincode']!='')?$row_select_mall_data['pincode']: ' ')."^";
			$contents  .= (($row_select_mall_data['state']!='')?$row_select_mall_data['state']: ' ')."^";
			$contents  .= (($row_select_mall_data['country']!='')?$row_select_mall_data['country']: ' ')."^";
			$contents  .= (($row_select_mall_data['closed_on']!='')?$row_select_mall_data['closed_on']: ' ')."^";
			$contents  .= (($row_select_mall_data['std_code']!='')?$row_select_mall_data['std_code']: ' ')."^";
			$contents  .= (($row_select_mall_data['upcoming_event']!='')?$row_select_mall_data['upcoming_event']: ' ')."^";
			$contents  .= (($row_select_mall_data['type']!='')?$row_select_mall_data['type']: ' ')."^";
			$contents  .= (($row_select_mall_data['general_facility']!='')?$row_select_mall_data['general_facility']: ' ')."^";
			$contents  .= (($row_select_mall_data['street_number']!='')?$row_select_mall_data['street_number']: ' ');
			$linecontents  .= $contents."\n";
			
		}
	}
	if($countrowcolumn >0)
	{
	  $contentsrowcolumn  =$countrowcolumn.'¥'.'15';
	  $datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
	  $datacontents = '0'.'¥'.'0';
	}
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=mall_master.txt");
	print "$datacontents"; 	

?>