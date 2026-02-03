<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_REQUEST['emp_code'];

$sqlempvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
$rsempvertical=mysqli_query($link,$sqlempvertical);
$rowempvertical=mysqli_fetch_assoc($rsempvertical);
$emp_vertical_value=$rowempvertical['vertical_value'];

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition='EM.emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="EM.emp_code='".$emp_code."'";
}

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
	$login_condition=" AND UNIX_TIMESTAMP(BM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}

	if(vertical_fields=='yes'){
	$sqlquery="SELECT * FROM branch_route_freight WHERE acedns='Y' AND vertical_value='".$emp_vertical_value."'";
	}
	else
	{
		$sqlquery="SELECT * FROM branch_route_freight WHERE acedns='Y'";
	}
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'8';
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
			$contents  = (($rowbranch['branch_code']!='')?$rowbranch['branch_code']: ' ')."^";
			$contents  .= (($rowbranch['route_code']!='')?$rowbranch['route_code']: ' ')."^";
			$contents  .= (($rowbranch['freight']!='')?$rowbranch['freight']: ' ')."^";
			$contents  .= (($rowbranch['acedns']!='')?$rowbranch['acedns']: ' ')."^";
			$contents  .= (($rowbranch['date']!='')?$rowbranch['date']: ' ')."^";
			$contents  .= (($rowbranch['capacity']!='')?$rowbranch['capacity']: ' ')."^";
			$contents  .= (($rowbranch['transport_mode']!='')?$rowbranch['transport_mode']: ' ')."^";
			$contents  .= (($rowbranch['vertical_value']!='')?$rowbranch['vertical_value']: ' ');
			
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
			$datacontents = '0'.'¥'.'8';
		}
	}

	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/branch-route-freight-txt-6.0.3.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=branch_route_freight.txt");
	print "$datacontents"; 	
	mysqli_close($link);	
?>
