<?php
require("include/config.php");
require("include/dbcon.php");

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
	$login_condition=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}
$sqlquery="SELECT * FROM route_customer_plan WHERE SUBSTRING(route_plan_trans_id,3,5)='".$emp_code."' ".$login_condition."";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn=$count.'¥'.'5';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

		while($rowroutecustomerplan = mysqli_fetch_assoc($result))
		{
				$visit_date=date('d-m-Y',strtotime($rowroutecustomerplan['visit_date']));
				$trans_id=$rowroutecustomerplan['route_plan_trans_id'];
				$current_route_code=$rowroutecustomerplan['route_code'];
				$customer_code=$rowroutecustomerplan['customer_code'];
				$status=$rowroutecustomerplan['status'];
				
				$contents  = (($trans_id!='')?$trans_id: ' ')."^";
				$contents  .= (($current_route_code!='')?$current_route_code: ' ')."^";
				$contents  .= (($visit_date!='')?$visit_date: ' ')."^";
				$contents  .= (($customer_code!='')?$customer_code: ' ')."^";
				$contents  .= (($status!='')?$status: ' ');
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime."\n".str_replace("\r","",$linecontents);
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

	/*$contents .= "</recordset>";			
	echo $contents;*/
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/route-customer-plan-master-txt-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=route_customer_plan_master.txt");
	print "$datacontents";
	mysqli_close($link);
?>