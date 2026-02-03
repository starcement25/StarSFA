<?php	
    //-----------------------------Definition of email setup attributes--------------------------//
	require("functions.php");
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
		$email_hierarchy=substr($email_hierarchy,0,-1);
		if($nick_name=='KARMA')
		{
			$email_hierarchy_array=explode(',',$email_hierarchy);
			define("ATTENDANCEEMAILRECIPENTS",$email_hierarchy);
			define("ORDEREMAILRECIPENTS",$email_hierarchy_array[0].','.$email_hierarchy_array[1]);
			define("PAYMENTEMAILRECIPENTS",$email_hierarchy_array[0].','.$email_hierarchy_array[1]);
			define("ROUTEPLANMAILRECIPENTS",$email_hierarchy_array[0]);
			define("AUDITEMAILRECIPENTS",$email_hierarchy_array[0]);
		}
		else
		{
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
				if(email_hierarchy_level==1)
				{
				  define("ATTENDANCEEMAILRECIPENTS",'');
				}
				else
				{
					define("ATTENDANCEEMAILRECIPENTS",$email_hierarchy.','.$corresponding_emails);
				}
			}
	
			if(email_hierarchy_level==1)
			{
				$sqlemailhierarchyatt="SELECT reporting_to FROM employee_master WHERE emp_code ='".$emp_code."'";
				$rsemailhierarchyatt=mysql_query($sqlemailhierarchyatt);
				$rowemailhierarchyatt=mysql_fetch_array($rsemailhierarchyatt);
				$reporting_to_immediate=$rowemailhierarchyatt['reporting_to'];
				
				$sqlimmediatemail="SELECT email FROM employee_master WHERE FIND_IN_SET(emp_code, '".$reporting_to_immediate."')";
				$rsimmediatemail=mysql_query($sqlimmediatemail);
				$rowimmediatemail=mysql_fetch_array($rsimmediatemail);
				$mail_immediate=$rowimmediatemail['email'];
				
				if($nick_name=='PARLE')
				{
					define("ATTENDANCEEMAILRECIPENTS",$mail_immediate);
					define("ORDEREMAILRECIPENTS",$mail_immediate);
					define("PAYMENTEMAILRECIPENTS",$mail_immediate);
					define("PROSPECTEMAILRECIPENTS",$mail_immediate);
					define("ROUTEPLANMAILRECIPENTS",$mail_immediate);
					define("AUDITEMAILRECIPENTS",$mail_immediate);
					define("SURVEYEMAILRECIPENTS",$mail_immediate);
				}
				else
				{
					define("ORDEREMAILRECIPENTS",$corresponding_emails.','.$mail_immediate);
					define("PAYMENTEMAILRECIPENTS",$corresponding_emails);
					define("PROSPECTEMAILRECIPENTS",$corresponding_emails);
					define("ROUTEPLANMAILRECIPENTS",$corresponding_emails);
					define("AUDITEMAILRECIPENTS",$corresponding_emails);
					define("SAUDAEMAILRECIPENTS",$corresponding_emails);
					define("SURVEYEMAILRECIPENTS",$corresponding_emails);
				}
			}
			else
			{
				define("ORDEREMAILRECIPENTS",$email_hierarchy.','.$corresponding_emails);
				define("PAYMENTEMAILRECIPENTS",$email_hierarchy.','.$corresponding_emails);
				define("PROSPECTEMAILRECIPENTS",$email_hierarchy.','.$corresponding_emails);
				define("ROUTEPLANMAILRECIPENTS",$email_hierarchy.','.$corresponding_emails);
				define("AUDITEMAILRECIPENTS",$email_hierarchy.','.$corresponding_emails);
				define("SAUDAEMAILRECIPENTS",$email_hierarchy.','.$corresponding_emails);
				define("SURVEYEMAILRECIPENTS",$email_hierarchy.','.$corresponding_emails);
			}
	   }
	}
	else if(($emp_code!='C0007' && $emp_code!='C0005') && email_hierarchywise=='no'){
		if($nick_name!='IFPL' && $nick_name!='VIPL' && $nick_name!='JPHARMA' && $nick_name!='SKIPPER'){
			define("ORDEREMAILRECIPENTS",$corresponding_emails);
			define("PAYMENTEMAILRECIPENTS",$corresponding_emails);
			define("ATTENDANCEEMAILRECIPENTS",$corresponding_emails);
			define("PROSPECTEMAILRECIPENTS",$corresponding_emails);
			define("ROUTEPLANMAILRECIPENTS",$corresponding_emails);
			define("AUDITEMAILRECIPENTS",$corresponding_emails);
			define("SAUDAEMAILRECIPENTS",$corresponding_emails);
			define("SURVEYEMAILRECIPENTS",$corresponding_emails);
		}
		else if($nick_name=='SKIPPER')
		{
			$sqlempemail="SELECT email,zone,branch_code FROM employee_master WHERE emp_code='".$_REQUEST['emp_code']."'";
			$rsempemail=mysql_query($sqlempemail);
			$rowempemail=mysql_fetch_array($rsempemail);
			$emp_mail=$rowempemail['email'];
			$emp_zone=$rowempemail['zone'];
			$branch_code=$rowempemail['branch_code'];
			
			$sqldnsbranchcode="SELECT dns_branch_code FROM branch_master WHERE branch_code='".$branch_code."'";
			$rsdnsbranchcode=mysql_query($sqldnsbranchcode);
			$rowdnsbranchcode=mysql_fetch_array($rsdnsbranchcode);
			$dns_branch_code=$rowdnsbranchcode['dns_branch_code'];
			
			define("PAYMENTEMAILRECIPENTS",$corresponding_emails.','.$emp_mail);
			define("ATTENDANCEEMAILRECIPENTS",$corresponding_emails);
			define("PROSPECTEMAILRECIPENTS",$corresponding_emails);
			define("ROUTEPLANMAILRECIPENTS",$corresponding_emails);
			define("AUDITEMAILRECIPENTS",$corresponding_emails);
			define("SAUDAEMAILRECIPENTS",$corresponding_emails);
			define("SURVEYEMAILRECIPENTS",$corresponding_emails);
			define("NEWCUSTOMEREMAILRECIPENTS",$corresponding_emails.','.$emp_mail);
			define("NOORDEREMAILRECIPENTS",$corresponding_emails.','.$emp_mail);
			
			if(strtoupper($emp_zone)=='North'){
				define("ORDEREMAILRECIPENTS",$corresponding_emails.','.$emp_mail.','.'yogesh.goswami@skipperlimited.com,bhajan.singh@skipperlimited.com');
			}
			else if(strtoupper($emp_zone)=='NORTH-2'){
				define("ORDEREMAILRECIPENTS",$corresponding_emails.','.$emp_mail.','.'yogesh.goswami@skipperlimited.com,anurag.sharma@skipperlimited.com');
			}
			else if(strtoupper($emp_zone)=='EAST'){
				define("ORDEREMAILRECIPENTS",$corresponding_emails.','.$emp_mail.','.'pvc@skipperlimited.com');
			}
			else if(strtoupper($emp_zone)=='SOUTH'){
				define("ORDEREMAILRECIPENTS",$corresponding_emails.','.$emp_mail.','.'hyd.salescoordinator@skipperlimited.com');
			}
			else if(strtoupper($emp_zone)=='WEST'){
				define("ORDEREMAILRECIPENTS",$corresponding_emails.','.$emp_mail.','.'pvcgujarat@skipperlimited.com');
			}
			else
			{
				define("ORDEREMAILRECIPENTS",$corresponding_emails.','.$emp_mail);
			}
			if($dns_branch_code=='NR01')
			{
				$final_mailid=ORDEREMAILRECIPENTS.','.'yogesh.goswami@skipperlimited.com';
				define("ORDEREMAILRECIPENTS",$final_mailid);
			}
		}
		else if($nick_name=='VIPL'){
			define("ORDEREMAILRECIPENTS",'report@vibrantinfocom.net');
			define("PAYMENTEMAILRECIPENTS",'report@vibrantinfocom.net');
			define("ATTENDANCEEMAILRECIPENTS",'report@vibrantinfocom.net');
			define("PROSPECTEMAILRECIPENTS",'report@vibrantinfocom.net');
			define("ROUTEPLANMAILRECIPENTS",'report@vibrantinfocom.net');
			define("AUDITEMAILRECIPENTS",'report@vibrantinfocom.net');
		}
		else if($nick_name=='JPHARMA')
		{
			define("ORDEREMAILRECIPENTS",'reporting@microparkindia.com');
			define("PAYMENTEMAILRECIPENTS",'reporting@microparkindia.com');
			define("ATTENDANCEEMAILRECIPENTS",'reporting@microparkindia.com,hrd@microparkindia.com');
			define("PROSPECTEMAILRECIPENTS",'reporting@microparkindia.com');
			define("ROUTEPLANMAILRECIPENTS",'reporting@microparkindia.com');
			define("AUDITEMAILRECIPENTS",'reporting@microparkindia.com');
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
	define("FROMEMAIL","acedns@acedns.in");
	define("FROMTAG","acednspro");
	if($nick_name=='SMOTO' || $nick_name=='VDIST'){
		//define("BCCEMAIL","dipankarc@coral.in,mc@coral.in,acedns@coral.in");
		define("BCCEMAIL","mc@coral.in,acedns@coral.in");
	}
	else if($nick_name=='AMPL')
	{
		//define("BCCEMAIL","dipankarc@coral.in,rajuyk@automotiveml.com,acedns@coral.in");
		define("BCCEMAIL","rajuyk@automotiveml.com,acedns@coral.in");
	}
	else
	{
		//define("BCCEMAIL","dipankarc@coral.in,acedns@coral.in");
		define("BCCEMAIL","acedns@coral.in");
	}
	define("DCREMAILRECIPENTS",$corresponding_emails);
	define("LOYALTYEMAILRECIPENTS",$corresponding_emails);
	//define("LOYALTYEMAILRECIPENTS",'acedns@coral.in');
	//define("REPORTEMAILRECIPENTS","dipankarc@coral.in");
	define("TOUREMAILRECIPENTS",$corresponding_emails);
?>