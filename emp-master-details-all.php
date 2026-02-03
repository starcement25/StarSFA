<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$json_array = array();
$sqlquery="SELECT emp_code,dns_emp_code,emp_name,sale_access,reporting_to,designation,vertical_value,email,phone_no,state,
			zone,acedns,District,DOJ 
			FROM employee_master WHERE sale_access='attendance' ORDER BY emp_code ASC";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	if($count>0){
		while($rowemp = mysqli_fetch_assoc($result))
		{
				$response[$rowemp['emp_code']]= array(
					"emp_code"=>$rowemp['emp_code'],
					"dns_emp_code"=>$rowemp['dns_emp_code'],
					"emp_name"=>$rowemp['emp_name'],
					"sale_access"=>$rowemp['sale_access'],
					"designation"=>$rowemp['designation'],
					"vertical_value"=>$rowemp['vertical_value'],
					"email"=>$rowemp['email'],
					"phone_no"=>$rowemp['phone_no'],
					"state"=>$rowemp['state'],
					"zone"=>$rowemp['zone'],
					"District"=>$rowemp['District'],
					"DOJ"=>$rowemp['DOJ'],
				);
			$json_array['emp_details'] = $response;		
		}
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	echo json_encode($json_array);; 	
	mysqli_close($link);	
?>
