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

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition='emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="emp_code='".$emp_code."'";
}


$sqlbranch="SELECT branch_code FROM employee_master WHERE ".$emp_hierarchy_condition."";
$rsbranch=mysqli_query($link,$sqlbranch);
$branc_code_array=array();
while($rowbranch=mysqli_fetch_assoc($rsbranch))
{
	$branch_code=$rowbranch['branch_code'];
	if(!in_array($branch_code,$branc_code_array))
	{
		$branch_code_list=$branch_code_list."'".$branch_code."'".',';
		array_push($branc_code_array,$branch_code);
	}
}
$branch_code_list=substr($branch_code_list,0,-1);

$sqlquery="SELECT emp_code,emp_name,sale_access FROM employee_master WHERE branch_code IN($branch_code_list) ORDER BY emp_name ASC";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'3';
	if($count>0){
		while($rowemp = mysqli_fetch_assoc($result))
		{
				$contents  = (($rowemp['emp_code']!='')?$rowemp['emp_code']: ' ')."^";
				$contents  .= (($rowemp['emp_name']!='')?$rowemp['emp_name']: ' ')."^";
				$contents  .= (($rowemp['sale_access']!='')?$rowemp['sale_access']: ' ');
				
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=emp_master.txt");
	print "$datacontents"; 		
?>
