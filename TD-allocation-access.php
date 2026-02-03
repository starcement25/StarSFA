<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_REQUEST['emp_code'];

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition='emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="emp_code='".$emp_code."'";
}

$sqlquery="SELECT emp_code,designation,flag,get_allocation FROM TD_allocation_access WHERE ".$emp_hierarchy_condition."";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'4';
	if($count>0){
		while($rowdesignation = mysqli_fetch_assoc($result))
		{
				$contents  = (($rowdesignation['emp_code']!='')?$rowdesignation['emp_code']: ' ')."^";
				$contents  .= (($rowdesignation['designation']!='')?$rowdesignation['designation']: ' ')."^";
				$contents  .= (($rowdesignation['flag']!='')?$rowdesignation['flag']: ' ')."^";
				$contents  .= (($rowdesignation['get_allocation']!='')?$rowdesignation['get_allocation']: ' ');
				
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=TD_allocation_access.txt");
	print "$datacontents"; 	
	mysqli_close($link);
?>
