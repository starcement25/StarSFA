<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_REQUEST['emp_code'];

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition='EM.emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="EM.emp_code='".$emp_code."'";
}

/*$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);*/

/*if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(BM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}*/
if($nick_name=='EMAMI' || $nick_name=='EMAMIT')
{
	$branch_condition=" AND acedns='Y'";
}
else
{
	$branch_condition="";
}
if(survey=='yes' && $nick_name!='EMAMI')
{
	/*$sqlquery="SELECT BM.branch_code,BM.branch_name,BM.comp_code,BM.HQ,BM.plant_name FROM 
				branch_master BM,employee_master EM WHERE FIND_IN_SET(BM.branch_code,EM.branch_code) AND  EM.emp_code='".$emp_code."' 
				ORDER BY BM.branch_name ASC";*/
	$sqlquery="SELECT DISTINCT BM.branch_code,BM.branch_name,BM.comp_code,BM.HQ,BM.plant_name,is_plant,BM.dns_state_code FROM 
				branch_master BM,employee_master EM WHERE FIND_IN_SET(BM.branch_code,EM.branch_code) AND ".$emp_hierarchy_condition." ORDER BY BM.branch_name ASC";			
}
else
{
	if($nick_name=='DNV')
	{
		$sqlquery="SELECT BM.branch_code,BM.branch_name,BM.comp_code,BM.HQ,BM.plant_name,BM.is_plant,BM.dns_state_code FROM branch_master BM,
					employee_master EM 
					WHERE 1 ".$branch_condition." AND FIND_IN_SET(BM.branch_code,EM.branch_code) AND EM.emp_code='".$emp_code."'  ORDER BY BM.branch_name ASC";
	}
	else
	{
		$sqlquery="SELECT BM.branch_code,BM.branch_name,BM.comp_code,BM.HQ,BM.plant_name,is_plant,BM.dns_state_code FROM branch_master BM 
				WHERE 1 ".$branch_condition." ORDER BY BM.branch_name ASC";
	}
}
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'7';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

		while($rowbranch = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowbranch['comp_code']!='')?$rowbranch['comp_code']: ' ')."^";
			$contents  .= (($rowbranch['branch_code']!='')?$rowbranch['branch_code']: ' ')."^";
			$contents  .= (($rowbranch['branch_name']!='')?$rowbranch['branch_name']: ' ')."^";
			$contents  .= (($rowbranch['HQ']!='')?$rowbranch['HQ']: ' ')."^";
			$contents  .= (($rowbranch['plant_name']!='')?$rowbranch['plant_name']: ' ')."^";
			$contents  .= (($rowbranch['is_plant']!='')?$rowbranch['is_plant']: ' ')."^";
			$contents .= (($rowbranch['dns_state_code']!='')?$rowbranch['dns_state_code']: ' ');

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
			$datacontents = '0'.'¥'.'7';
		}
	}

	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/branch-master-txt-6.0.5.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=branch_master.txt");
	print "$datacontents"; 	
	mysqli_close($link);	
?>
