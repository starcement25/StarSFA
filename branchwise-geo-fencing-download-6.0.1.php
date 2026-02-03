<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");


$emp_code=$_REQUEST['emp_code'];
if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition=" emp_code='".$emp_code."'";
}
$sqlbranch="SELECT GROUP_CONCAT(DISTINCT branch_code SEPARATOR ',') AS branch_code FROM employee_master WHERE ".$emp_hierarchy_condition." ";
$rsbranch=mysqli_query($link,$sqlbranch);
$rowbranch=mysqli_fetch_assoc($rsbranch);
$branch_code=$rowbranch['branch_code'];

if($branch_code !='')
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
	$sqlquery="SELECT geo_fencing,branch_code FROM branchwise_geo_fencing WHERE FIND_IN_SET(branch_code,'".$branch_code."')  ";
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	$cnt=1;
	if($count>0){
		while($rowgeofencing = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowgeofencing['branch_code']!='')?$rowgeofencing['branch_code']: ' ')."^";
			$contents  .= (($rowgeofencing['geo_fencing']!='')?$rowgeofencing['geo_fencing']: ' ');
			
			$linecontents  .= $contents."\n";
			$cnt++;
		}
		$contentsrowcolumn=$count.'¥'.'2';
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
header("Content-type: application/text"); 
header("Content-Disposition: attachment; filename=branchwise_geo_fencing.txt");
print "$datacontents"; 		
?>
