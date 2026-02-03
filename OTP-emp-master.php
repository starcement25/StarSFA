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
$sqlquery="SELECT emp_code,emp_name,sale_access,reporting_to,designation,vertical_value,branch_code,state,zone,acedns,lower_leaves 
				FROM employee_master WHERE 
			".$emp_hierarchy_condition." ORDER BY emp_name ASC";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'12';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

		while($rowemp = mysqli_fetch_assoc($result))
		{
				$sale_access=$rowemp['sale_access'];
				$contents  = (($rowemp['emp_code']!='')?$rowemp['emp_code']: ' ')."^";
				$contents  .= (($rowemp['emp_name']!='')?$rowemp['emp_name']: ' ')."^";
				$contents  .= (($sale_access!='')?$sale_access: ' ')."^";
				$contents  .= (($rowemp['reporting_to']!='')?$rowemp['reporting_to']: ' ')."^";
				$contents  .= (($level!='')?$level: ' ')."^";
				$contents  .= (($rowemp['designation']!='')?$rowemp['designation']: ' ')."^";
				$contents  .= (($rowemp['vertical_value']!='')?$rowemp['vertical_value']: ' ')."^";
				$contents  .= (($rowemp['branch_code']!='')?$rowemp['branch_code']: ' ')."^";
				$contents  .= (($rowemp['state']!='')?$rowemp['state']: ' ')."^";
				$contents  .= (($rowemp['zone']!='')?$rowemp['zone']: ' ')."^";
				$contents  .= (($rowemp['acedns']!='')?$rowemp['acedns']: ' ')."^";
				$contents  .= (($rowemp['lower_leaves']!='')?$rowemp['lower_leaves']: ' ');
				
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		//$datacontents = '0'.'¥'.'0';
		$last_update_time=str_replace('?','',$last_update_time);
		$data_download_time=str_replace('?','',$data_download_time);
		if(strtotime($data_download_time)>=strtotime($last_update_time))
		{
			$datacontents = '0'.'¥'.'0';
		}
		else
		{
			$datacontents = '0'.'¥'.'12';
		}
	}
	
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/OTP-emp-master.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=emp_master.txt");
	print "$datacontents"; 	
	mysqli_close($link);	
?>
