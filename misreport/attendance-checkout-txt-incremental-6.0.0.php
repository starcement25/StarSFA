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
	$login_condition=" ";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(updatetime) > UNIX_TIMESTAMP('".$last_update_time."') ";
}
if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_val_rds=' AND (emp_code IN('.$employee_hierarchy.'))';
}
else
{
	$emp_val_rds=" AND emp_code='".$emp_code."'";
}
$sqllocation = "SELECT emp_code,trans_id,date FROM location WHERE 
			(trans_id LIKE 'A%' OR trans_id LIKE 'CH%' OR trans_id LIKE 'WO%' OR trans_id LIKE 'LR%') ".$emp_val_rds.$login_condition;
$resultlocation = mysqli_query($link,$sqllocation);
$count=mysqli_num_rows($resultlocation);

$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

	if($count>0){
		$contentsrowcolumn=$count.'¥'.'3';
		while($rowlocation = mysqli_fetch_assoc($resultlocation))
		{
			$emp_code=$rowlocation['emp_code'];
			$trans_id=$rowlocation['trans_id'];
			$date=$rowlocation['date'];
			$latt=$rowlocation['latt'];
			$longi=$rowlocation['longi'];
			
				$contents = (($emp_code!='')?$emp_code: ' ')."^";
				$contents .= (($trans_id!='')?$trans_id: ' ')."^";
				$contents .= (($date!='')?$date: ' ');
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/attendance-checkout-txt-incremental-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=attendance-checkout.txt");
	print "$datacontents"; 		
?>
