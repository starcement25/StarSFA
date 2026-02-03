<?php
ob_start();
session_start();
require("adminUtils.php");

$state = $_REQUEST['state'];
$route = $_REQUEST['route'];
$employee = $_REQUEST['employee'];
$allocation_date = $_REQUEST['allocation_date'];
$distributor=$_REQUEST['distributor'];

$route = rtrim($route,"");
$route_array = explode(",",$route);

$sqldelete="DELETE from emp_datewise_route_allocation WHERE emp_code=".$employee." AND allocation_date='".$allocation_date."' ";
mysql_query($sqldelete);
foreach($route_array as $route_value){
		$route_value = ltrim($route_value," ");
		$route_value = rtrim($route_value," ");
		$route_value = ltrim($route_value,",");

/*$sqlselallocation="SELECT emp_code FROM emp_datewise_route_allocation WHERE emp_code=".$employee." AND 
					route_code=".$route_value." AND allocation_date='".$allocation_date."'" ;
$rsselallocation=mysql_query($sqlselallocation);
if(mysql_num_rows($rsselallocation)==0)
{*/					
	$sqlinsert="INSERT INTO emp_datewise_route_allocation 
				SET emp_code=".$employee.",
				route_code='".$route_value."',
				distributor_code=".$distributor.",
				allocation_date='".$allocation_date."',
				download_time=CURRENT_TIMESTAMP()";
	if(mysql_query($sqlinsert))
	{
		//echo "<span style=\"font-weight:bold; font-size:14px; background-color=green;\">Route allocation successfull.</span>";
		$flag=1;
	}
/*}
else
{
	$flag=2;
}*/
/*else{
	
	//echo "<span style=\"font-weight:bold; font-size:14px; background-color=green;\">Employee is already allocated for the selected route on the selected date.</span>";
}*/
}
if($flag==1){
	echo "<span style=\"font-weight:bold; font-size:14px; background-color=green;\">Route allocation successfull.</span>";
}
?>
