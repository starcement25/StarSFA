<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);
$sqlbranch="SELECT BM.branch_state FROM branch_master BM,employee_master EM  WHERE FIND_IN_SET(BM.branch_code,EM.branch_code)
			AND EM.emp_code='".$emp_code."'";
$rsbranch=mysqli_query($link,$sqlbranch);
while($rowbranch=mysqli_fetch_assoc($rsbranch))
{
	$branch_state=$branch_state.$rowbranch['branch_state'].',';
}
$branch_state=substr($branch_state,0,-1);

if($branch_state !='')
{
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	
	$hour=gmdate('H',strtotime('+330 minute'));
	$minute=gmdate('i',strtotime('+330 minute'));
	$second=gmdate('s',strtotime('+330 minute'));
	//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
	$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
	$linecontents='';
	if($incremental_download=='no')
	{
		$login_condition=" AND acedns='Y'";
	}
	else
	{
		$login_condition=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
	}
	  $sqlquery="SELECT gr_file_name,state,acedns,start_date,end_date FROM branchwise_goldenrules WHERE 
			FIND_IN_SET(state,'".$branch_state."') ".$login_condition." ORDER BY download_time DESC ";
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	$cnt=1;
	if($count>0){
		while($rowgoldenrule = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowgoldenrule['state']!='')?$rowgoldenrule['state']: ' ')."^";
			$contents  .= (($rowgoldenrule['gr_file_name']!='')?$rowgoldenrule['gr_file_name']: ' ')."^";
			$contents  .= (($rowgoldenrule['acedns']!='')?$rowgoldenrule['acedns']: ' ')."^";
			$contents  .= (($rowgoldenrule['start_date']!='')?$rowgoldenrule['start_date']: ' ')."^";
			$contents  .= (($rowgoldenrule['end_date']!='')?$rowgoldenrule['end_date']: ' ');
			
			$linecontents  .= $contents."\n";
			$cnt++;
		}
		$contentsrowcolumn=$count.'¥'.'5';
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
}
else
{
	if(strtoupper($nick_name)=='GOLDSTONE')
	{
	   	$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		$linecontents='';
		if($incremental_download=='no')
		{
			$login_condition=" AND acedns='Y'";
		}
		else
		{
			$login_condition=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
		}
	    $sqlquery="SELECT gr_file_name,state,acedns,start_date,end_date FROM branchwise_goldenrules WHERE 
			1 ".$login_condition." ORDER BY download_time DESC ";
			$result = mysqli_query($link,$sqlquery);
		$count=mysqli_num_rows($result);
		$cnt=1;
		if($count>0){
		while($rowgoldenrule = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowgoldenrule['state']!='')?$rowgoldenrule['state']: ' ')."^";
			$contents  .= (($rowgoldenrule['gr_file_name']!='')?$rowgoldenrule['gr_file_name']: ' ')."^";
			$contents  .= (($rowgoldenrule['acedns']!='')?$rowgoldenrule['acedns']: ' ')."^";
			$contents  .= (($rowgoldenrule['start_date']!='')?$rowgoldenrule['start_date']: ' ')."^";
			$contents  .= (($rowgoldenrule['end_date']!='')?$rowgoldenrule['end_date']: ' ');
			
			$linecontents  .= $contents."\n";
			$cnt++;
		}
		$contentsrowcolumn=$count.'¥'.'5';
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
}
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/branchwise-goldenrules-download-txt-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	
header("Content-type: application/text"); 
header("Content-Disposition: attachment; filename=golden_rule.txt");
print "$datacontents"; 		
?>
