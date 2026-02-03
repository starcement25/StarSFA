<?php	
    //-----------------------------Definition of email setup attributes--------------------------//
	//require("functions.php");
	$sqlemail="SELECT admin_email_id,account_email_id FROM company_master WHERE comp_name='".$nick_name."'";
	if(order_approval_process=='yes')
	{
		$sqlemail="SELECT admin_email_id,account_email_id,order_approval_email_id FROM company_master WHERE comp_name='".$nick_name."'";
	}
	$rsemail=mysql_query($sqlemail);
	$rowemail=mysql_fetch_array($rsemail);
	
	$admin_email_id=$rowemail['admin_email_id'];
	$account_email_id=$rowemail['account_email_id'];
	if(order_approval_process=='yes')
	{
		$order_approval_email_id=$rowemail['order_approval_email_id'];
		define("ORDERAPPROVALEMAIL",$order_approval_email_id);
	}
	$corresponding_emails=$admin_email_id.','.$account_email_id;
	//$corresponding_emails='';
	
	$emp_code=$_REQUEST['emp_code'];
	if(email_hierarchywise=='yes'  && ($emp_code!='C0007' && $emp_code!='C0005'))
	{
		$employee_upper_hierarchy=return_employee_upper_hierarchy($emp_code);
		//$emp_hierarchy_condition='c1.emp_code IN('.$employee_hierarchy.')';
		$sqlemailhierarchy="SELECT email FROM employee_master WHERE emp_code IN (".$employee_upper_hierarchy.")";
		$rsemailhierarchy=mysql_query($sqlemailhierarchy);
		$email_hierarchy='';
		while($rowemailhierarchy=mysql_fetch_array($rsemailhierarchy))
		{
			$email_hierarchy=$email_hierarchy.$rowemailhierarchy['email'].',';
		}
		if($nick_name=='RUPA')
		{
			$sqlemailhierarchyatt="SELECT reporting_to FROM employee_master WHERE emp_code ='".$emp_code."'";
			$rsemailhierarchyatt=mysql_query($sqlemailhierarchyatt);
			$rowemailhierarchyatt=mysql_fetch_array($rsemailhierarchyatt);
			$reporting_to_immediate=$rowemailhierarchyatt['reporting_to'];
			
			$sqlimmediatemail="SELECT email FROM employee_master WHERE emp_code ='".$reporting_to_immediate."'";
			$rsimmediatemail=mysql_query($sqlimmediatemail);
			$rowimmediatemail=mysql_fetch_array($rsimmediatemail);
			$mail_immediate=$rowimmediatemail['email'];
			
			define("ATTENDANCEEMAILRECIPENTS",$mail_immediate);
		}
		else
		{
			define("ATTENDANCEEMAILRECIPENTS",$email_hierarchy);
		}
		$email_hierarchy=substr($email_hierarchy,0,-1);
		define("ORDEREMAILRECIPENTS",$email_hierarchy);
		define("PAYMENTEMAILRECIPENTS",$email_hierarchy);
		define("PROSPECTEMAILRECIPENTS",$email_hierarchy);
		define("ROUTEPLANMAILRECIPENTS",$email_hierarchy);
		define("AUDITEMAILRECIPENTS",$email_hierarchy);
		define("SAUDAEMAILRECIPENTS",$email_hierarchy);
		define("SURVEYEMAILRECIPENTS",$email_hierarchy);
	}
	else if(($emp_code!='C0007' && $emp_code!='C0005') && email_hierarchywise=='no'){
		if($nick_name!='IFPL' && $nick_name!='VIPL'){
			define("ORDEREMAILRECIPENTS",$corresponding_emails);
			define("PAYMENTEMAILRECIPENTS",$corresponding_emails);
			define("ATTENDANCEEMAILRECIPENTS",$corresponding_emails);
			define("PROSPECTEMAILRECIPENTS",$corresponding_emails);
			define("ROUTEPLANMAILRECIPENTS",$corresponding_emails);
			define("AUDITEMAILRECIPENTS",$corresponding_emails);
			define("SAUDAEMAILRECIPENTS",$corresponding_emails);
			define("SURVEYEMAILRECIPENTS",$corresponding_emails);
		}
		else if($nick_name=='VIPL'){
			define("ORDEREMAILRECIPENTS",'report@vibrantinfocom.net');
			define("PAYMENTEMAILRECIPENTS",'report@vibrantinfocom.net');
			define("ATTENDANCEEMAILRECIPENTS",'report@vibrantinfocom.net');
			define("PROSPECTEMAILRECIPENTS",'report@vibrantinfocom.net');
			define("ROUTEPLANMAILRECIPENTS",'report@vibrantinfocom.net');
			define("AUDITEMAILRECIPENTS",'report@vibrantinfocom.net');
		}
		/*else if($nick_name=='RUPA')
		{
			if($emp_code=='1259')
			{
				define("ORDEREMAILRECIPENTS",'aditya@eurofashions.in');
				define("PAYMENTEMAILRECIPENTS",'aditya@eurofashions.in');
				define("ATTENDANCEEMAILRECIPENTS",'aditya@eurofashions.in');
				define("PROSPECTEMAILRECIPENTS",'aditya@eurofashions.in');
				define("ROUTEPLANMAILRECIPENTS",'aditya@eurofashions.in');
			}
			else if($emp_code=='1261')
			{
				define("ORDEREMAILRECIPENTS",'aman@eurojeans.in,manish@rupa.co.in');
				define("PAYMENTEMAILRECIPENTS",'aman@eurojeans.in,manish@rupa.co.in');
				define("ATTENDANCEEMAILRECIPENTS",'aman@eurojeans.in,manish@rupa.co.in');
				define("PROSPECTEMAILRECIPENTS",'aman@eurojeans.in,manish@rupa.co.in');
				define("ROUTEPLANMAILRECIPENTS",'aman@eurojeans.in,manish@rupa.co.in');
			}
			else if($emp_code=='1257')
			{
				define("ORDEREMAILRECIPENTS",'pankaj@bumchums.in');
				define("PAYMENTEMAILRECIPENTS",'pankaj@bumchums.in');
				define("ATTENDANCEEMAILRECIPENTS",'pankaj@bumchums.in');
				define("PROSPECTEMAILRECIPENTS",'pankaj@bumchums.in');
				define("ROUTEPLANMAILRECIPENTS",'pankaj@bumchums.in');
			}
			else
			{
				define("ORDEREMAILRECIPENTS",$corresponding_emails);
				define("PAYMENTEMAILRECIPENTS",$corresponding_emails);
				define("ATTENDANCEEMAILRECIPENTS",$corresponding_emails);
				define("PROSPECTEMAILRECIPENTS",$corresponding_emails);
				define("ROUTEPLANMAILRECIPENTS",$corresponding_emails);
				define("AUDITEMAILRECIPENTS",$corresponding_emails);
			}
		}*/
		else
		{
			define("ORDEREMAILRECIPENTS",'amit.karmakar@imperialfragrances.com,sudip.sarkar@imperialfragrances.com');
			define("PAYMENTEMAILRECIPENTS",'amit.karmakar@imperialfragrances.com,sudip.sarkar@imperialfragrances.com');
			define("ATTENDANCEEMAILRECIPENTS",'anusmita.karmakar@imperialfragrances.com');
			define("PROSPECTEMAILRECIPENTS",'anusmita.karmakar@imperialfragrances.com');
			define("ROUTEPLANMAILRECIPENTS",'anusmita.karmakar@imperialfragrances.com');
		}
	}
	else{
		define("ORDEREMAILRECIPENTS",'');
		define("PAYMENTEMAILRECIPENTS",'');
		define("ATTENDANCEEMAILRECIPENTS",'');
		define("PROSPECTEMAILRECIPENTS",'');
		define("ROUTEPLANMAILRECIPENTS",'');
		define("AUDITEMAILRECIPENTS",'');
		define("SAUDAEMAILRECIPENTS",'');
		define("SURVEYEMAILRECIPENTS",'');
	}
	//define("FROMEMAIL","acedns@coral.in");
	define("FROMEMAIL","acednspro@acedns.in");
	define("FROMTAG","acednspro");
	if($nick_name=='SMOTO' || $nick_name=='VDIST'){
		define("BCCEMAIL","dipankarc@coral.in,mc@coral.in,acedns@coral.in");
	}
	else if($nick_name=='AMPL')
	{
		define("BCCEMAIL","dipankarc@coral.in,rajuyk@automotiveml.com,acedns@coral.in");
	}
	else
	{
		define("BCCEMAIL","dipankarc@coral.in,acedns@coral.in");
	}
	define("DCREMAILRECIPENTS",$corresponding_emails);
	define("LOYALTYEMAILRECIPENTS",$corresponding_emails);
	//define("LOYALTYEMAILRECIPENTS",'acedns@coral.in');
	//define("REPORTEMAILRECIPENTS","dipankarc@coral.in");
	define("TOUREMAILRECIPENTS",$corresponding_emails);
?>