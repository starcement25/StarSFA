<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_REQUEST['emp_code'];

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition='OUM.emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="OUM.emp_code='".$emp_code."'";
}

if($emp_code=='C0007')
 {
	$sqlquery="SELECT  outlet_name,outlet_code FROM outlet_master WHERE 1  ORDER BY outlet_name ASC";
 }
 else
 {
	$sqlquery="SELECT OU.outlet_name,OU.outlet_code FROM outlet_master OU,outlet_user_master OUM 
			WHERE OU.outlet_code=OUM.outlet_code AND ".$emp_hierarchy_condition."";
 }
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'2';
	if($count>0){
		while($rowoutlet = mysqli_fetch_assoc($result))
		{
				$contents  = (($rowoutlet['outlet_code']!='')?$rowoutlet['outlet_code']: ' ')."^";
				$contents  .= (($rowoutlet['outlet_name']!='')?$rowoutlet['outlet_name']: ' ');
				
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	/*$contents .= "</recordset>";			
	echo $contents;	*/
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=outlet_master.txt");
	print "$datacontents"; 		
?>
