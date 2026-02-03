<?php	
    //-----------------------------Definition of email setup attributes--------------------------//
	$sqlemail="SELECT admin_email_id,account_email_id FROM company_master WHERE comp_name='".$nick_name."'";
	$rsemail=mysqli_query($link,$sqlemail);
	$rowemail=mysqli_fetch_assoc($rsemail);
	
	$admin_email_id=$rowemail['admin_email_id'];
	$account_email_id=$rowemail['account_email_id'];
	$corresponding_emails=$admin_email_id.','.$account_email_id;
	//$corresponding_emails='';
	
	$emp_code=$_REQUEST['emp_code'];
	if(email_hierarchywise=='yes'  && $emp_code!='C0007')
	{
		$sqlemailhierarchy="SELECT email FROM employee_master WHERE emp_code=(SELECT reporting_to FROM employee_master WHERE emp_code='".$emp_code."')";
		$rsemailhierarchy=mysqli_query($link,$sqlemailhierarchy);
		$rowemailhierarchy=mysqli_fetch_assoc($rsemailhierarchy);
		$email_hierarchy=$rowemailhierarchy['email'];
		define("ORDEREMAILRECIPENTS",$email_hierarchy);
		define("PAYMENTEMAILRECIPENTS",$email_hierarchy);
		define("ATTENDANCEEMAILRECIPENTS",$email_hierarchy);
		define("PROSPECTEMAILRECIPENTS",$email_hierarchy);
		define("ROUTEPLANMAILRECIPENTS",$email_hierarchy);
		define("AUDITEMAILRECIPENTS",$email_hierarchy);
	}
	else if($emp_code!='C0007' && email_hierarchywise=='no'){
		if($nick_name!='IFPL' && $nick_name!='VIPL' && $nick_name!='RUPA'){
			define("ORDEREMAILRECIPENTS",$corresponding_emails);
			define("PAYMENTEMAILRECIPENTS",$corresponding_emails);
			define("ATTENDANCEEMAILRECIPENTS",$corresponding_emails);
			define("PROSPECTEMAILRECIPENTS",$corresponding_emails);
			define("ROUTEPLANMAILRECIPENTS",$corresponding_emails);
			define("AUDITEMAILRECIPENTS",$corresponding_emails);
		}
		else if($nick_name=='VIPL'){
			define("ORDEREMAILRECIPENTS",'report@vibrantinfocom.net');
			define("PAYMENTEMAILRECIPENTS",'report@vibrantinfocom.net');
			define("ATTENDANCEEMAILRECIPENTS",'report@vibrantinfocom.net');
			define("PROSPECTEMAILRECIPENTS",'report@vibrantinfocom.net');
			define("ROUTEPLANMAILRECIPENTS",'report@vibrantinfocom.net');
		}
		else if($nick_name=='RUPA')
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
		}
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
	}
	define("FROMEMAIL","acedns@coral.in");
	define("FROMTAG","aceDNS");
	define("BCCEMAIL","dipankarc@coral.in,acedns@coral.in");
	define("DCREMAILRECIPENTS",$corresponding_emails);
	define("LOYALTYEMAILRECIPENTS",'dipankarc@coral.in,ak@coral.in');
	//define("REPORTEMAILRECIPENTS","dipankarc@coral.in");
?>