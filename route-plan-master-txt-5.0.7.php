<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
$visit_date=$_REQUEST['current_date'];
//$emp_code='E0002';
//$visit_date='2014-05-13';
$route_plan_visit_date_month=substr($visit_date,5,2);
$route_plan_visit_date_year=substr($visit_date,0,4);

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition=" emp_code='".$emp_code."'";
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
$sqlquery="SELECT *,DATE_FORMAT(visit_date,'%d-%m-%Y') AS visit_date FROM route_plan WHERE ".$emp_hierarchy_condition." 
			AND visit_date LIKE '%".$route_plan_visit_date_year.'-'.$route_plan_visit_date_month."%' AND visit_date>='".$visit_date."'";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn=$count.'¥'.'6';
	if($count>0){
		while($rowrouteplan = mysqli_fetch_assoc($result))
		{
				$visit_date=$rowrouteplan['visit_date'];
				$visit_date_final=date('Y-m-d',strtotime($visit_date));
				$trans_id=$rowrouteplan['route_plan_trans_id'];
				$current_route_code=$rowrouteplan['route_code'];
				$route_plan_emp_code=$rowrouteplan['emp_code'];
				$sqlqueryprevroute="SELECT prev_route_code FROM route_plan_log WHERE emp_code='".$route_plan_emp_code."' AND 
									visit_date='".$visit_date_final."' AND route_plan_trans_id='".$trans_id."' AND current_route_code='".$current_route_code."' 
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
				}
				/*$contents.="<data>";
				$contents .='
				<route_plan_trans_id><![CDATA['.mb_convert_encoding($rowrouteplan['route_plan_trans_id'], 'UTF-8', 'UTF-8').']]></route_plan_trans_id>
				<emp_code><![CDATA['.mb_convert_encoding($rowrouteplan['emp_code'], 'UTF-8', 'UTF-8').']]></emp_code>
				<route_code><![CDATA['.mb_convert_encoding($rowrouteplan['route_code'], 'UTF-8', 'UTF-8').']]></route_code>
				<visit_date><![CDATA['.mb_convert_encoding($visit_date_final, 'UTF-8', 'UTF-8').']]></visit_date>
				<create_date><![CDATA['.mb_convert_encoding($rowrouteplan['create_date'], 'UTF-8', 'UTF-8').']]></create_date>
				';
				$contents.="</data>";
				//echo $cnt++;*/
				$contents  = (($rowrouteplan['route_plan_trans_id']!='')?$rowrouteplan['route_plan_trans_id']: ' ')."^";
				$contents  .= (($rowrouteplan['emp_code']!='')?$rowrouteplan['emp_code']: ' ')."^";
				$contents  .= (($rowrouteplan['route_code']!='')?$rowrouteplan['route_code']: ' ')."^";
				$contents  .= (($rowrouteplan['visit_date']!='')?$rowrouteplan['visit_date']: ' ')."^";
				$contents  .= (($rowrouteplan['create_date']!='')?$rowrouteplan['create_date']: ' ')."^";
				$contents  .= (($prev_route_code!='')?$prev_route_code: ' ');
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsaccessperiod."\n".str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = $contentsaccessperiod."\n".'0'.'¥'.'0';
	}
	/*$contents .= "</recordset>";			
	echo $contents;*/
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=route_plan_master.txt");
	print "$datacontents";
?>