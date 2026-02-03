<?php
	define("SERVERREMOTE","103.87.174.95");
	define("USERREMOTE","starsaat_dnsprod");
	define("PASSWORDREMOTE","dnsprod1234#");
	define("DBREMOTE","starsaat_START");
		
	$link=mysql_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,TRUE) or die("Database Connection Error.");
	mysql_select_db(DBREMOTE,$link) or die("could not connect the database");
	
	//print_r($_POST);
	//$array_val=json_decode($_POST['postvar3']);
	//print_r($array_val);
	$dns_branch_code_array=array();
	foreach($_POST as $array_value) {
		foreach($array_value as $key=>$value) {
 	 		if($key=='dns_branch_code') $dns_branch_code=$value;
			if(!in_array($dns_branch_code,$dns_branch_code_array))
			{
				array_push($dns_branch_code_array,$dns_branch_code);
			}
			
			if($key=='branch_location')   	 ${branch_location.$dns_branch_code}=$value;
			if($key=='branch_name') 	   	 ${branch_name.$dns_branch_code}=$value;
			if($key=='branch_state') 	    ${branch_state.$dns_branch_code}=$value;
			if($key=='comp_code') 		   ${comp_code.$dns_branch_code}=$value;
			if($key=='branch_email_id')     ${branch_email_id.$dns_branch_code}=$value;
			if($key=='alternative_email_id') ${alternative_email_id.$dns_branch_code}=$value;
			if($key=='acedns') 	   		  ${acedns.$dns_branch_code}=$value;
		}
	}
	foreach($dns_branch_code_array as $dns_branch_code_val){
		$dns_branch_code=$dns_branch_code_val;
		$branch_location=${branch_location.$dns_branch_code_val};
		$branch_name=${branch_name.$dns_branch_code_val};
		$branch_state=${branch_state.$dns_branch_code_val};
		$comp_code=${comp_code.$dns_branch_code_val};
		$branch_email_id=${branch_email_id.$dns_branch_code_val};
		$alternative_email_id=${alternative_email_id.$dns_branch_code_val};
		$acedns=${acedns.$dns_branch_code_val};
			
			$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$dns_branch_code."'";
			$rsbranchnamechk=mysql_query($sqlbranchnamechk);
			$countbranchnamechk=mysql_num_rows($rsbranchnamechk);
			if($countbranchnamechk<1)
			{
				$sqlmaxbranchcode="SELECT MAX(branch_code) AS max_branch_code FROM  branch_master WHERE 1";
				$rsmaxbranchcode=mysql_query($sqlmaxbranchcode);
				$rowmaxbranchcode=mysql_fetch_array($rsmaxbranchcode);
				$max_branch_code=$rowmaxbranchcode['max_branch_code'];
				
				if($max_branch_code=='')
				{
					$max_branch_code='B0001';
				}
				else
				{
					$max_branch_code++;
				}
			
				$sqlbranch  = "insert into branch_master SET ";
				$sqlbranch .= "  	branch_code='".mysql_real_escape_string($max_branch_code)."'";
				$sqlbranch .= " , dns_branch_code='".mysql_real_escape_string($dns_branch_code)."'";
				$sqlbranch .= " , branch_name='".mysql_real_escape_string($branch_name)."'";
				$sqlbranch .= " , branch_state='".mysql_real_escape_string($branch_state)."'";
				$sqlbranch .= " , branch_location='".mysql_real_escape_string($branch_location)."'";
				$sqlbranch .= " , comp_code='".mysql_real_escape_string($comp_code)."'";
				$sqlbranch .= " , branch_email_id='".mysql_real_escape_string($branch_email_id)."'";
				$sqlbranch .= " , alternative_email_id='".mysql_real_escape_string($alternative_email_id)."'";
				$sqlbranch .= " , download_time=CURRENT_TIMESTAMP()";
			}
			else
			{
				$rowbranchnamechk=mysql_fetch_array($rsbranchnamechk);
				$branch_code=$rowbranchnamechk['branch_code'];

				$sqlbranch  = "UPDATE branch_master SET ";
				$sqlbranch .= "  	dns_branch_code='".mysql_real_escape_string($dns_branch_code)."'";
				$sqlbranch .= " , branch_location='".mysql_real_escape_string($branch_location)."'";
				$sqlbranch .= " , branch_name='".mysql_real_escape_string($branch_name)."'";
				$sqlbranch .= " , branch_state='".mysql_real_escape_string($branch_state)."'";
				$sqlbranch .= " , comp_code='".mysql_real_escape_string($comp_code)."'";
				$sqlbranch .= " , branch_email_id='".mysql_real_escape_string($branch_email_id)."'";
				$sqlbranch .= " , download_time=CURRENT_TIMESTAMP()";
				$sqlbranch .= " , alternative_email_id='".mysql_real_escape_string($alternative_email_id)."' 
								WHERE branch_code='".$branch_code."'";
			}
			mysql_query($sqlbranch);
	   }
		define("SERVER","localhost");
		define("USER","acedns_dnsprod");
		define("PASSWORD","dnsprod1234#");
		define("DB","acedns_STAR");
		$link=mysql_connect(SERVER,USER,PASSWORD,TRUE) or die("Database Connection Error.");
		mysql_select_db(DB,$link) or die("could not connect the database");
		$sqlupdate="UPDATE master_tables_update_info set need_update='no',download_time_no=CURRENT_TIMESTAMP() where table_name='branch_master'";
		mysql_query($sqlupdate,$link);
?>
