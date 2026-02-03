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

if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(BDF.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}
if(destination=='yes' && branch_wise_destination=='yes')
{
	if(employeewise_hierarchy=='yes'){
		$employee_hierarchy=return_employee_hierarchy($emp_code);
		$emp_hierarchy_condition='emp_code IN('.$employee_hierarchy.')';
	}
	else
	{
		$emp_hierarchy_condition="emp_code='".$emp_code."'";
	}
	$sqlempbranch="SELECT branch_code FROM employee_master WHERE ".$emp_hierarchy_condition;
	$rsempbranch=mysqli_query($link,$sqlempbranch);
	while($rowempbranch=mysqli_fetch_assoc($rsempbranch))
	{
		$branch_value=$rowempbranch['branch_code'];
		$branch_code=$branch_code.$branch_value.',';
	}
	$branch_value_array=explode(',',$branch_code);
	$branch_value_final = "'".implode("','", $branch_value_array)."'";
	$condition_branch=' AND branch_code IN ('.$branch_value_final.')';

	$sqlquery="SELECT DM.destination_code,DM.destination_name FROM destination_master DM,branch_destination_freight BDF
					WHERE DM.destination_code=BDF.destination_code ".$condition_branch.$login_condition." ORDER BY destination_name ASC";
}
else
{
	$sqlquery="SELECT BDF.destination_code,BDF.destination_name FROM destination_master BDF WHERE 1 ".$login_condition." ORDER BY BDF.destination_name ASC";
}
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'2';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

		while($rowdestination = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowdestination['destination_code']!='')?$rowdestination['destination_code']: ' ')."^";
			$contents  .= (($rowdestination['destination_name']!='')?$rowdestination['destination_name']: ' ');
			
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
			$datacontents = '0'.'¥'.'2';
		}
	}
	
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/destination-master-txt-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);


	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=destination_master.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
