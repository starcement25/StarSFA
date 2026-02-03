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
	$emp_hierarchy_condition='emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="emp_code='".$emp_code."'";
}
if($incremental_download=='no')
{
	$login_condition=" AND acedns!='N'";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}
 $sqlbranchcode="SELECT branch_code FROM employee_master WHERE emp_code='".$emp_code."'";
  $rsbranchcode=mysqli_query($link,$sqlbranchcode);
  $rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
  $branch_code=$rowbranchcode['branch_code'];
/*$sqlquery="SELECT f_code,emp_code,facilitator_name,f_type,firm_name,f_address,f_pin,f_area,f_sub_area,mobile_no,email_id,dob,
		annniversary,acedns,branch_code,check_in_date,f_category FROM facilitator_master WHERE 
			".$emp_hierarchy_condition.$login_condition." ORDER BY facilitator_name ASC";*/
$sqlquery="SELECT f_code,emp_code,facilitator_name,f_type,firm_name,f_address,f_pin,f_area,f_sub_area,mobile_no,email_id,dob,
		annniversary,acedns,branch_code,check_in_date,f_category,f_city,f_state,designation,nature_of_work  
		FROM facilitator_master WHERE branch_code='".$branch_code."'
			".$login_condition." ORDER BY facilitator_name ASC";			
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'21';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

		while($rowfaclitator = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowfaclitator['f_code']!='')?$rowfaclitator['f_code']: ' ')."^";
			$contents  .= (($rowfaclitator['emp_code']!='')?$rowfaclitator['emp_code']: ' ')."^";
			$contents  .= (($rowfaclitator['facilitator_name']!='')?trim(preg_replace('/[\r\n]+/', '',$rowfaclitator['facilitator_name'])): ' ')."^";
			$contents  .= (($rowfaclitator['f_type']!='')?$rowfaclitator['f_type']: ' ')."^";
			$contents  .= (($rowfaclitator['firm_name']!='')?trim(preg_replace('/[\r\n]+/', '',$rowfaclitator['firm_name'])): ' ')."^";
			$contents  .= (($rowfaclitator['f_address']!='')?trim(preg_replace('/[\r\n]+/', '',$rowfaclitator['f_address'])): ' ')."^";
			$contents  .= (($rowfaclitator['f_pin']!='')?trim(preg_replace('/[\r\n]+/', '',$rowfaclitator['f_pin'])): ' ')."^";
			$contents  .= (($rowfaclitator['f_area']!='')?trim(preg_replace('/[\r\n]+/', '',$rowfaclitator['f_area'])): ' ')."^";
			$contents  .= (($rowfaclitator['f_sub_area']!='')?trim(preg_replace('/[\r\n]+/', '',$rowfaclitator['f_sub_area'])): ' ')."^";
			$contents  .= (($rowfaclitator['mobile_no']!='')?trim(preg_replace('/[\r\n]+/', '',$rowfaclitator['mobile_no'])): ' ')."^";
			$contents  .= (($rowfaclitator['email_id']!='')?trim(preg_replace('/[\r\n]+/', '',$rowfaclitator['email_id'])): ' ')."^";
			$contents  .= (($rowfaclitator['dob']!='')?str_replace("-","/",$rowfaclitator['dob']): ' ')."^";
			$contents  .= (($rowfaclitator['annniversary']!='')?$rowfaclitator['annniversary']: ' ')."^";
			$contents  .= (($rowfaclitator['acedns']!='')?$rowfaclitator['acedns']: ' ')."^";
			$contents  .= (($rowfaclitator['branch_code']!='')?$rowfaclitator['branch_code']: ' ')."^";
			$contents  .= (($rowfaclitator['check_in_date']!='')?$rowfaclitator['check_in_date']: ' ')."^";
			$contents  .= (($rowfaclitator['f_category']!='')?$rowfaclitator['f_category']: ' ')."^";
			$contents  .= (($rowfaclitator['f_city']!='')?trim(preg_replace('/[\r\n]+/', '',$rowfaclitator['f_city'])): ' ')."^";
			$contents  .= (($rowfaclitator['f_state']!='')?trim(preg_replace('/[\r\n]+/', '',$rowfaclitator['f_state'])): ' ')."^";
			$contents  .= (($rowfaclitator['designation']!='')?trim(preg_replace('/[\r\n]+/', '',$rowfaclitator['designation'])): ' ')."^";
			$contents  .= (($rowfaclitator['nature_of_work']!='')?$rowfaclitator['nature_of_work']: ' ');
			
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
			$datacontents = '0'.'¥'.'21';
		}
	}
	
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/facilitator-master-txt-incremental-6.0.3.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=facilitator_master.txt");
	print "$datacontents"; 	
	mysqli_close($link);	
?>
