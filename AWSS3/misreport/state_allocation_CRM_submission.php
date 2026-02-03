<?php
ob_start();
session_start();
require("adminUtils.php");

$state = $_REQUEST['state'];
$employee = $_REQUEST['employee'];
$allocation_date = $_REQUEST['allocation_date'];
$state = rtrim($state,"");
$state_array = explode(",",$state);

/*$sqldelete="DELETE from emp_datewise_state_allocation WHERE emp_code=".$employee." AND allocation_date='".$allocation_date."' ";
mysql_query($sqldelete);*/

/*$sqlselallocation="SELECT emp_code FROM emp_datewise_route_allocation WHERE emp_code=".$employee." AND 
					route_code=".$route_value." AND allocation_date='".$allocation_date."'" ;
$rsselallocation=mysql_query($sqlselallocation);
if(mysql_num_rows($rsselallocation)==0)
{*/
//print_r($state_array);
foreach($state_array as $state_value){
		$state_value = ltrim($state_value," ");
		$state_value = rtrim($state_value," ");
		$state_value = ltrim($state_value,",");
					
	$sqlinsert="INSERT INTO emp_datewise_state_allocation 
				SET emp_code=".$employee.",
				state_code='".$state_value."',
				allocation_date='".$allocation_date."',
				download_time=CURRENT_TIMESTAMP()";
	if(mysql_query($sqlinsert))
	{
		//echo "<span style=\"font-weight:bold; font-size:14px; background-color=green;\">Route allocation successfull.</span>";
		$flag=1;
	}
}
/*}
else
{
	$flag=2;
}*/
/*else{
	
	//echo "<span style=\"font-weight:bold; font-size:14px; background-color=green;\">Employee is already allocated for the selected route on the selected date.</span>";
}*/
if($flag==1){
   echo "<span style=\"font-weight:bold; font-size:14px; background-color=green;\"></span>";
}
?>
