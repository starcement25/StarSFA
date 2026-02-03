<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_REQUEST['emp_code'];

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
}
else
{
	$employee_hierarchy="";
}

$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);

$sqlbranch="SELECT GROUP_CONCAT(branch_code SEPARATOR ',') AS branch_code FROM employee_master 
			WHERE emp_code IN(".$employee_hierarchy.")";
$rsbranch=mysqli_query($link,$sqlbranch);
$rowbranch=mysqli_fetch_assoc($rsbranch);
$branch_code=$rowbranch['branch_code'];

if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}

	$sqlquery="SELECT DISTINCT * FROM branch_destination_freight WHERE acedns='Y' AND FIND_IN_SET(branch_code,'".$branch_code."') ".$login_condition;
	//exit();
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'4';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

		while($rowbranchdestination = mysqli_fetch_assoc($result))
		{
			$sqldestinationexfor="SELECT ex_for_type FROM destination_master WHERE 
								destination_code='".$rowbranchdestination['destination_code']."'";
			$rsdestinationexfor=mysqli_query($link,$sqldestinationexfor);
			$rowdestinationexfor=mysqli_fetch_assoc($rsdestinationexfor);					
			$contents  = (($rowbranchdestination['branch_code']!='')?$rowbranchdestination['branch_code']: ' ')."^";
			$contents  .= (($rowbranchdestination['destination_code']!='')?$rowbranchdestination['destination_code']: ' ')."^";
			$contents  .= (($rowdestinationexfor['ex_for_type']!='')?$rowdestinationexfor['ex_for_type']: ' ')."^";
			$contents  .= (($rowbranchdestination['acedns']!='')?$rowbranchdestination['acedns']: ' ');
			
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
			$datacontents = '0'.'¥'.'4';
		}
	}

	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/branch-destination-incremental-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=branch_destination.txt");
	print "$datacontents"; 	
	mysqli_close($link);	
?>
