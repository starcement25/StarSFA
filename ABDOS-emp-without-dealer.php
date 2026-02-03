<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");


$sqlquery="SELECT emp_code FROM employee_master WHERE app_access='Y'";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	if($count>0){
		$emp_code_list='';
		while($rowemp = mysqli_fetch_assoc($result))
		{
			$employee_hierarchy=return_employee_hierarchy($rowemp['emp_code']);
			$emp_hierarchy_condition=' emp_code IN('.$employee_hierarchy.')';
			$sqlemphierarchy="SELECT customer_code FROM customer_master WHERE cust_type='D' AND  ".$emp_hierarchy_condition."";
			$rsemphierarchy=mysqli_query($link,$sqlemphierarchy);
			$cntemphierarchy=mysqli_num_rows($rsemphierarchy);
			if($cntemphierarchy ==0)
			{
				$emp_code_list=$emp_code_list.$rowemp['emp_code'].',';
			}
		}
		echo $emp_code_list=substr($emp_code_list,0,-1);
	}
?>
