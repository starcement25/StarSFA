<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
$first_login=$_REQUEST['first_login'];

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' AND c1.emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition=" AND c1.emp_code='".$emp_code."'";
}
$sqlroute="SELECT route_code FROM route_master WHERE 1";
$rsroute=mysqli_query($link,$sqlroute);
$countroute=mysqli_num_rows($rsroute);
if(($first_login=='yes' && $emp_code!='C0007'))
{
	if($countroute>0){
	   $sqlquery="SELECT DISTINCT c1.customer_code,c1.credit_limit FROM customer_master c1,route_master r, employee_master em WHERE c1.acedns='Y' 
	   				".$emp_hierarchy_condition." OR ( r.emp_code = em.emp_code AND 	c1.route_code = r.route_code AND em.emp_code ='".$emp_code."' 
					AND c1.acedns='Y') ORDER BY c1.customer_name ASC";
	}
	else
	{
		$sqlquery="SELECT c1.customer_code,c1.credit_limit FROM customer_master c1, employee_master em WHERE c1.acedns='Y' ".$emp_hierarchy_condition." 
					ORDER BY c1.customer_name ASC";
	}
}
else if($first_login=='no' && $emp_code!='C0007')
{
	$sqlquery="SELECT c1.customer_code,c1.credit_limit FROM customer_master c1 WHERE 1 ".$emp_hierarchy_condition." ORDER BY c1.customer_name ASC";
}
else if($emp_code=='C0007')
{
	$sqlquery="SELECT customer_code,credit_limit FROM customer_master WHERE 1 ORDER BY customer_name ASC";
}
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

	$contentsrowcolumn=$count.'¥'.'2';
	if($count>0){
		while($rowsemp = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowsemp['customer_code']!='')?$rowsemp['customer_code']: ' ')."^";
			$contents  .= (($rowsemp['credit_limit']!='')?$rowsemp['credit_limit']: ' ');
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
	header("Content-Disposition: attachment; filename=customer_master_credit_limit.txt");
	print "$datacontents"; 		
?>
