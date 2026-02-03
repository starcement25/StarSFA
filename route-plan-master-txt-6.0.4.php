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

//For checking employee menu access
if(joint_work=='yes')
{
	$menu_access_array=array();
	$sqlmenuaccess="SELECT not_accessible_menu FROM menu_access WHERE emp_code='".$emp_code."'";
	$rsmenuaccess=mysqli_query($link,$sqlmenuaccess);
	$countmenuaccess=mysqli_num_rows($rsmenuaccess);
	$menu_access_array=array();
	if($countmenuaccess >0)
	{
		while($rowmenuaccess=mysqli_fetch_assoc($rsmenuaccess))
		{
			array_push($menu_access_array,$rowmenuaccess['not_accessible_menu']);
		}
	}
	
	if(employeewise_hierarchy=='yes'){
		$employee_hierarchy=return_employee_hierarchy($emp_code);
	}
	else
	{
		$employee_hierarchy='';
	}
}

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
if($nick_name=='STAR'){
	$visit_date=date('Y-m-d');
	$sqlquery="SELECT *,DATE_FORMAT(visit_date,'%d-%m-%Y') AS visit_date FROM route_plan WHERE emp_code='".$emp_code."'";
}
else{
	if(joint_work=='yes' && !in_array('joint_work',$menu_access_array))
	{
		$sqlquery="SELECT *,DATE_FORMAT(visit_date,'%d-%m-%Y') AS visit_date FROM route_plan WHERE route_code <> '' 
		AND emp_code IN(".$employee_hierarchy.") ".$login_condition;
	}
	else
	{
		$sqlquery="SELECT *,DATE_FORMAT(visit_date,'%d-%m-%Y') AS visit_date FROM route_plan 
		WHERE route_code <> '' AND route_code IN(SELECT route_code FROM route_master) AND emp_code='".$emp_code."'".$login_condition;
	}
}
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn=$count.'¥'.'9';
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
				$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$current_route_code."'";
				$rsroutename=mysqli_query($link,$sqlroutename);
				$rowroutename=mysqli_fetch_assoc($rsroutename);
				$route_name=$rowroutename['route_name'];
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
				$contents  .= (($rowrouteplan['distributor_code']!='')?$rowrouteplan['distributor_code']: ' ')."^";
				$contents  .= (($route_name!='')?preg_replace('/[\r\n]+/', '',$route_name): ' ')."^";
				$contents  .= (($rowrouteplan['working_with']!='')?$rowrouteplan['working_with']: ' ');
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
	$url = APICALLLOGURL."/route-plan-master-txt-6.0.4.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=route_plan_master.txt");
	print "$datacontents";
	mysqli_close($link);
?>