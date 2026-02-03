<?php
require("include/config.php");
require("include/dbcon.php");
require("include/functions.php");

$sqlquery="SELECT emp_code,dns_emp_code,emp_name FROM employee_master WHERE emp_code NOT IN (SELECT emp_code FROM customer_master GROUP BY emp_code)";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	if($count>0){
		while($rowbranch = mysqli_fetch_assoc($result))
		{
			$emp_code=$rowbranch['emp_code'];
			$employee_hierarchy=return_employee_hierarchy($emp_code);
			$emp_hierarchy_condition=' emp_code IN('.$employee_hierarchy.')';
			
			$sqlcustomerchk="SELECT COUNT(customer_code) as total_customer FROM customer_master WHERE ".$emp_hierarchy_condition;
			$rscustomerchk=mysqli_query($link,$sqlcustomerchk);
			$rowcustomerchk=mysqli_fetch_assoc($rscustomerchk);
			$total_customer=$rowcustomerchk['total_customer'];
			
			if($total_customer <1)
			{
				$contents  = (($emp_code!='')?$emp_code: ' ').",";
				$contents  .= (($rowbranch['dns_emp_code']!='')?$rowbranch['dns_emp_code']: ' ').",";
				$contents  .= (($rowbranch['emp_name']!='')?$rowbranch['emp_name']: ' ').",";

				$linecontents  .= $contents."\r\n";
			}
		}
		$datacontents = $linecontents;
	}

	header("Content-type: application/octet-stream"); 
	header("Content-Disposition: inline; filename=Employee master.csv"); 
	print "$datacontents"; 
?>
