<?php
/*require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
	$emp_code=$_REQUEST['emp_code'];
	if(email_hierarchywise=='yes'  && ($emp_code!='C0007' && $emp_code!='C0005'))
	{
		echo $employee_upper_hierarchy=return_employee_upper_hierarchy($emp_code);
		
	}*/
$to_email='dipankarc@coral.in';
$subject='TEST SFA MAIL';
$body='TEST SFA MAIL';
$spam_filter='-fsalesmpower.acedns.in';
$headers  = "MIME-Version: 1.0\r\n";
				$headers .= "Content-type: text/html; charset=UTF-8\n";
				$headers .= "From: INFOPROD<info@salesmpower.acedns.in> \r\n" .
							"Reply-To:info@salesmpower.acedns.in> \r\n" .
							'X-Mailer: PHP/' . phpversion();

echo mail($to_email, $subject, $body, $headers,$spam_filter);
?>		
		