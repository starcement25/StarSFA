<?php
require("include/config.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);

$sqlaccessperiod="select DATE_FORMAT(access_start_date,'%d-%m-%Y') AS access_start_date,
				DATE_FORMAT(access_end_date,'%d-%m-%Y') AS access_end_date,period FROM route_plan_access_period where emp_code='".$emp_code."'";
$resultaccessperiod = mysqli_query($link,$sqlaccessperiod);
$countaccessperiod=mysqli_num_rows($resultaccessperiod);
if($countaccessperiod>0)
{
	$rowaccessperiod = mysqli_fetch_assoc($resultaccessperiod);
	$contentsaccessperiod=$rowaccessperiod['access_start_date'].'µ'.$rowaccessperiod['access_end_date'].'µ'.$rowaccessperiod['period'];
}
else
{
	$contentsaccessperiod='';
}
if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(create_date) > UNIX_TIMESTAMP('".$last_update_time."')";
}
/*$sqlquery="SELECT *,DATE_FORMAT(visit_date,'%d-%m-%Y') AS visit_date FROM route_plan WHERE emp_code='".$emp_code."' AND visit_date LIKE '%".$route_plan_visit_date_year.'-'.$route_plan_visit_date_month."%' AND visit_date>='".$visit_date."'";*/
$sqlquery="SELECT *,DATE_FORMAT(visit_date,'%d-%m-%Y') AS visit_date FROM route_plan WHERE emp_code='".$emp_code."'".$login_condition;
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn=$count.'¥'.'7';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

		while($rowrouteplan = mysqli_fetch_assoc($result))
		{
				$visit_date=$rowrouteplan['visit_date'];
				$visit_date_final=date('Y-m-d',strtotime($visit_date));
				$trans_id=$rowrouteplan['route_plan_trans_id'];
				$current_route_code=$rowrouteplan['route_code'];
				/*$sqlqueryprevroute="SELECT prev_route_code FROM route_plan_log WHERE emp_code='".$emp_code."' AND 
									visit_date='".$visit_date_final."' AND route_plan_trans_id='".$trans_id."' AND 
									current_route_code='".$current_route_code."' 
									AND prev_route_code !='' AND created_by!='USER'";
				$resultprevroute = mysqli_query($link,$sqlqueryprevroute);
				$cntprevroute=mysqli_num_rows($resultprevroute);
				$rowprevroute=mysqli_fetch_assoc($resultprevroute);
				if($cntprevroute>0)
				{
					$prev_route_code=$rowprevroute['prev_route_code'];
				}
				else
				{
					$prev_route_code='';
				}*/
				$contents  = (($rowrouteplan['route_plan_trans_id']!='')?$rowrouteplan['route_plan_trans_id']: ' ')."^";
				$contents  .= (($rowrouteplan['emp_code']!='')?$rowrouteplan['emp_code']: ' ')."^";
				$contents  .= (($rowrouteplan['route_code']!='')?$rowrouteplan['route_code']: ' ')."^";
				$contents  .= (($rowrouteplan['visit_date']!='')?$rowrouteplan['visit_date']: ' ')."^";
				$contents  .= (($rowrouteplan['create_date']!='')?$rowrouteplan['create_date']: ' ')."^";
				$contents  .= (($rowrouteplan['status']!='')?$rowrouteplan['status']: ' ')."^";
				$contents  .= (($rowrouteplan['distributor_code']!='')?$rowrouteplan['distributor_code']: ' ');
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime."\n".$contentsaccessperiod."\n".str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = $contentsaccessperiod."\n".'0'.'¥'.'0';
	}
	/*$contents .= "</recordset>";			
	echo $contents;*/
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/route-plan-master-txt-6.0.2.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=route_plan_master.txt");
	print "$datacontents";
	mysqli_close($link);
?>