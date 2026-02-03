<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
	$emp_code=$_REQUEST['emp_code'];
	if(email_hierarchywise=='yes'  && ($emp_code!='C0007' && $emp_code!='C0005'))
	{
		echo $employee_upper_hierarchy=return_employee_upper_hierarchy($emp_code);
		
	}
?>		
		