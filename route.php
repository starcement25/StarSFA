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
if($emp_code!='C0007'){
	$sqlquery="select * from route_master where ".$emp_hierarchy_condition." ORDER BY route_name ASC";
}
else
{
	$sqlquery="select * from route_master  ORDER BY route_name ASC";
}

$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	
	$contentsrowcolumn  =$count.'¥'.'2';
	if($count>0){
		while($rowroute = mysqli_fetch_assoc($result))
		{
			/*$contents.="<data>";
			$contents .='<route_code><![CDATA['.mb_convert_encoding($rowroute['route_code'], 'UTF-8', 'UTF-8').']]></route_code>
						<route_name><![CDATA['.mb_convert_encoding($rowroute['route_name'], 'UTF-8', 'UTF-8').']]></route_name>
						<emp_code><![CDATA['.mb_convert_encoding($rowroute['emp_code'], 'UTF-8', 'UTF-8').']]></emp_code>';
			$contents.="</data>";*/
			$contents  = (($rowroute['route_code']!='')?$rowroute['route_code']: ' ')."^";
			$contents  .= (($rowroute['route_name']!='')?$rowroute['route_name']: ' ');
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
	header("Content-Disposition: attachment; filename=route_master.txt");
	print "$datacontents"; 		
?>
