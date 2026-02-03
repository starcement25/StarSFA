<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_REQUEST['emp_code'];
$operation_type=$_REQUEST['operation_type'];
$sqlmenuaccess="SELECT accessible_menu FROM OTP_menu_access WHERE emp_code='".$emp_code."'";
$rsmenuaccess=mysqli_query($link,$sqlmenuaccess);
$countmenuaccess=mysqli_num_rows($rsmenuaccess);
$menu_access_array=array();
if($countmenuaccess >0)
{
	while($rowmenuaccess=mysqli_fetch_assoc($rsmenuaccess))
	{
		array_push($menu_access_array,$rowmenuaccess['accessible_menu']);
	}
}
if(in_array('gate_keeper_out',$menu_access_array) && $operation_type=='gate_keeper_out') // Start of Gate Keeper 2 out
{
	$sqlquery="SELECT checklist FROM OTP_checklists WHERE operation_type='gate_keeper2_out'";
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
		$dns_route_code='';
		while($rowchecklist = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowchecklist['checklist']!='')?$rowchecklist['checklist']: ' ');
			
			$linecontents  .= $contents."\n";
		}
		$contentsrowcolumn=$count.'¥'.'1';
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
			$datacontents = '0'.'¥'.'1';
		}
	}
}
if(in_array('security_out',$menu_access_array) && $operation_type=='security_out') // Start of security_out
{
	$sqlquery="SELECT checklist FROM OTP_checklists WHERE operation_type='security_out'";
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
		$dns_route_code='';
		while($rowchecklist = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowchecklist['checklist']!='')?$rowchecklist['checklist']: ' ');
			
			$linecontents  .= $contents."\n";
		}
		$contentsrowcolumn=$count.'¥'.'1';
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
			$datacontents = '0'.'¥'.'1';
		}
	}
}

$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = APICALLLOGURL."/OTP-checklist-download.php?nick_name=$nick_name&emp_code=$emp_code&operation_type=$operation_type";
insertapilog($datetime,$emp_code,$url,$nick_name);
header("Content-type: application/text"); 
header("Content-Disposition: attachment; filename=checklist.txt");
print "$datacontents"; 
mysqli_close($link);		
?>
