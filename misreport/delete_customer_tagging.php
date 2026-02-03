<?php
ob_start();
session_start();
require("adminUtils.php");

$emp_code = $_REQUEST['emp_code'];

if($emp_code != ''){
	$sqlchkemp="SELECT emp_code FROM employee_master WHERE dns_emp_code='".$emp_code."' and acedns='Y'";
	$rschkemp=mysqli_query($link,$sqlchkemp);
	$coutchkemp=mysqli_num_rows($rschkemp);
	if($coutchkemp==0)
	{
		echo 'Tagged employee not exists';
	}
	else
	{
		$rowchkemp=mysqli_fetch_assoc($rschkemp);
		$emp_code_internal=$rowchkemp['emp_code'];
		$sql_del_prev_tagging = "DELETE  FROM market_survey_tagging WHERE emp_code = '".$emp_code_internal."'";
		$res_del_prev_tagging = mysqli_query($link,$sql_del_prev_tagging);
		
		echo "All the existing deletion done";
	}
}
?>