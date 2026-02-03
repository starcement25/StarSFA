<?php
	if(order_approval_process=='yes'){
		$column_name = ",order_approval_email_id";
		$rowemail = $this->Userdb_model->email_id($nick_name,$column_name);
	}
	else{
		$column_name = "";
		$rowemail = $this->Userdb_model->email_id($nick_name,$column_name);
	}
	
	$admin_email_id = $rowemail['admin_email_id'];
	$account_email_id = $rowemail['account_email_id'];
	
	if(order_approval_process == 'yes'){
		$order_approval_email_id = $rowemail['order_approval_email_id'];
		define("ORDERAPPROVALEMAIL",$order_approval_email_id);
	}
	
	$corresponding_emails=$admin_email_id.','.$account_email_id;
	$emp_code = $this->get('emp_code');
	
	if(email_hierarchywise=='yes'  && ($emp_code!='C0007' && $emp_code!='C0005')){
		$employee_upper_hierarchy = return_employee_upper_hierarchy($emp_code);
		$email_hierarchy = '';
		$rowemailhierarchy = $this->Userdb_model->emp_hierarchy_email($employee_upper_hierarchy);
		foreach($rowemailhierarchy as $emailid){
			$email_hierarchy = $email_hierarchy.$emailid.',';
		}
		$email_hierarchy = substr($email_hierarchy,0,-1);
		if($nick_name=='RUPA'){
			$rowemailhierarchyatt = $this->Userdb_model->reporting_to($emp_code);
			$reporting_to_immediate = $rowemailhierarchyatt['reporting_to'];
			$reporting_to_immediate_val = "'".$reporting_to_immediate."'";
			
			$rowimmediatemail = $this->Userdb_model->emp_hierarchy_email($reporting_to_immediate_val);
			$mail_immediate=$rowimmediatemail['email'];
			
			define("ATTENDANCEEMAILRECIPENTS",$mail_immediate);
		}
		else{
			if(email_hierarchy_level == 1){
				define("ATTENDANCEEMAILRECIPENTS",'');
			}
			else{
				define("ATTENDANCEEMAILRECIPENTS",$email_hierarchy);
			}
		}
		
		if(email_hierarchy_level == 1){
			$rowemailhierarchyatt = $this->Userdb_model->reporting_to($emp_code);
			$reporting_to_immediate = $rowemailhierarchyatt['reporting_to'];
			
			$rowimmediatemail = $this->Userdb_model->immediate_email($reporting_to_immediate);
			$mail_immediate = $rowimmediatemail['email'];
			
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
		else{
			define("ORDEREMAILRECIPENTS",$email_hierarchy);
		    define("PAYMENTEMAILRECIPENTS",$email_hierarchy);
			define("PROSPECTEMAILRECIPENTS",$email_hierarchy);
			define("ROUTEPLANMAILRECIPENTS",$email_hierarchy);
			define("AUDITEMAILRECIPENTS",$email_hierarchy);
			define("SAUDAEMAILRECIPENTS",$email_hierarchy);
			define("SURVEYEMAILRECIPENTS",$email_hierarchy);
		}
	}
	else if(($emp_code!='C0007' && $emp_code!='C0005') && email_hierarchywise=='no'){
		if($nick_name!='IFPL' && $nick_name!='VIPL' && $nick_name!='JPHARMA'){
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
		else if($nick_name=='JPHARMA')
		{
			define("ORDEREMAILRECIPENTS",'reporting@microparkindia.com');
			define("PAYMENTEMAILRECIPENTS",'reporting@microparkindia.com');
			define("ATTENDANCEEMAILRECIPENTS",'reporting@microparkindia.com,hrd@microparkindia.com');
			define("PROSPECTEMAILRECIPENTS",'reporting@microparkindia.com');
			define("ROUTEPLANMAILRECIPENTS",'reporting@microparkindia.com');
			define("AUDITEMAILRECIPENTS",'reporting@microparkindia.com');
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
		define("SAUDAEMAILRECIPENTS",'');
		define("SURVEYEMAILRECIPENTS",'');
	}
	
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