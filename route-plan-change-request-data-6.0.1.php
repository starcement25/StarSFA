<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_REQUEST['emp_code'];
//For checking employee menu access
	if(employeewise_hierarchy=='yes'){
		$employee_hierarchy=return_employee_hierarchy($emp_code);
	}
	else
	{
		$employee_hierarchy='';
	}

	$current_date=gmdate('Y-m-d',strtotime('+330 minute'));

	$sqlquery="SELECT *,DATE_FORMAT(visit_date,'%d-%m-%Y') AS visit_date FROM route_plan WHERE route_code <> '' 
			AND emp_code IN(".$employee_hierarchy.") AND emp_code!='".$emp_code."' AND is_approved='no' AND visit_date >='".$current_date."'";
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn=$count.'¥'.'11';
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
				$sqlempname="SELECT emp_name FROM employee_master WHERE emp_code='".$rowrouteplan['emp_code']."'";
				$rsempname=mysqli_query($link,$sqlempname);
				$rowempname=mysqli_fetch_assoc($rsempname);
				$emp_name=$rowempname['emp_name'];
				
				$contents  = (($rowrouteplan['route_plan_trans_id']!='')?$rowrouteplan['route_plan_trans_id']: ' ')."^";
				$contents  .= (($rowrouteplan['emp_code']!='')?$rowrouteplan['emp_code']: ' ')."^";
				$contents  .= (($rowrouteplan['route_code']!='')?$rowrouteplan['route_code']: ' ')."^";
				$contents  .= (($rowrouteplan['visit_date']!='')?$rowrouteplan['visit_date']: ' ')."^";
				$contents  .= (($rowrouteplan['create_date']!='')?$rowrouteplan['create_date']: ' ')."^";
				$contents  .= (($rowrouteplan['status']!='')?$rowrouteplan['status']: ' ')."^";
				$contents  .= (($rowrouteplan['distributor_code']!='')?$rowrouteplan['distributor_code']: ' ')."^";
				$contents  .= (($route_name!='')?preg_replace('/[\r\n]+/', '',$route_name): ' ')."^";
				$contents  .= (($rowrouteplan['working_with']!='')?$rowrouteplan['working_with']: ' ')."^";
				$contents  .= (($emp_name!='')?$emp_name: ' ')."^";
				$contents  .= (($rowrouteplan['remarks']!='')?$rowrouteplan['remarks']: ' ');
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
	$url = APICALLLOGURL."/route-plan-change-request-data-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=route_plan_change_request.txt");
	print "$datacontents";
	mysqli_close($link);
?>