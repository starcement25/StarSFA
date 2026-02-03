<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_REQUEST['emp_code'];

/*if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' AND emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition=" AND emp_code='".$emp_code."'";
}*/
if($emp_code!='C0007'){
	$sqlquery="SELECT emp_code,emp_name,month,district,customer_code,customer_name,volume_target,volume_achievement,collection_target,collection_achievement 
			FROM emp_target_achievement WHERE emp_code='".$emp_code."'";
}
else
{
	$sqlquery="SELECT emp_code,emp_name,month,district,customer_code,customer_name,volume_target,volume_achievement,collection_target,collection_achievement 
			FROM emp_target_achievement WHERE 1";
}
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$contentsrowcolumn=$count.'¥'.'10';
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time."\n";
		while($rowemptarget = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowemptarget['emp_code']!='')?$rowemptarget['emp_code']: ' ')."^";
			$contents .= (($rowemptarget['emp_name']!='')?$rowemptarget['emp_name']: ' ')."^";
			$contents  .= (($rowemptarget['month']!='')?$rowemptarget['month']: ' ')."^";
			$contents  .= (($rowemptarget['district']!='')?$rowemptarget['district']: ' ')."^";
			$contents  .= (($rowemptarget['customer_code']!='')?$rowemptarget['customer_code']: ' ')."^";
			$contents  .= (($rowemptarget['customer_name']!='')?$rowemptarget['customer_name']: ' ')."^";
			$contents  .= (($rowemptarget['volume_target']!='')?$rowemptarget['volume_target']: ' ')."^";
			$contents  .= (($rowemptarget['volume_achievement']!='')?$rowemptarget['volume_achievement']: ' ')."^";
			$contents  .= (($rowemptarget['collection_target']!='')?$rowemptarget['collection_target']: ' ')."^";
			$contents  .= (($rowemptarget['collection_achievement']!='')?$rowemptarget['collection_achievement']: ' ');
			$linecontents  .= $contents."\n";	
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	/*$contents .= "</recordset>";			
	echo $contents;*/
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=emp_target_achievement.txt");
	print "$datacontents";
	mysqli_close($link);		
?>
