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
	$branch_code_array=array();
	$destination_code_array=array();
	foreach($_POST as $array_value) {
		foreach($array_value as $key=>$value) {
 	 		if($key=='branch_code_name') $branch_code_name=$value;
			if(!in_array($branch_code_name,$branch_code_array))
			{
				array_push($branch_code_array,$branch_code_name);
			}
			if($key=='dump_code')   	 $dump_code=$value;
			if(!isset(${dump_code.$branch_code_name}))
			{
				${dump_code.$branch_code_name}=array();
			}
			if(!in_array($dump_code,${dump_code.$branch_code_name}))
			{
			  if($dump_code!=''){
				array_push(${dump_code.$branch_code_name},$dump_code);
				}
			}
			if($key=='dump_name')  	 ${dump_name.$branch_code_name.$dump_code}=$value;
			if($key=='acedns')  		${acedns.$branch_code_name.$dump_code}=$value;
			if($key=='is_plant')  	  ${is_plant.$branch_code_name.$dump_code}=$value;
		}
	}
	foreach($branch_code_array as $branch_code_val){
	  foreach(${dump_code.$branch_code_val} as $dump_code_val)
		{
		$branch_code_name=$branch_code_val;
		$dum_code=$dump_code_val;
		$dump_name=${dump_name.$branch_code_name.$dum_code};
		$acedns=${acedns.$branch_code_val.$dum_code};
		$is_plant=${is_plant.$branch_code_val.$dum_code};
		
				$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE dns_branch_code='".addslashes($branch_code_name)."'";
				$rsbranchnamechk=mysql_query($sqlbranchnamechk);
				$rowbranchnamechk=mysql_fetch_array($rsbranchnamechk);
				$branch_code=$rowbranchnamechk['branch_code'];

				$sqlbranchdump="SELECT branch_code FROM  branch_dump WHERE branch_code='".addslashes($branch_code)."' 
											AND dump_code='".$dum_code."'";
				$rsbranchdump=mysql_query($sqlbranchdump);
				$countbranchdump=mysql_num_rows($rsbranchdump);
				if($countbranchdump<1 )
					{
						$sqlinsertbranchdump  = "insert into  branch_dump ";
						$sqlinsertbranchdump .= " SET branch_code='".$branch_code."'";
						$sqlinsertbranchdump .= " ,dump_code='".$dum_code."'";
						$sqlinsertbranchdump .= " ,dump_name='".$dump_name."'";
						$sqlinsertbranchdump .= " ,acedns='".$acedns."'";
						$sqlinsertbranchdump .= " ,is_plant='".$is_plant."'";
						$sqlinsertbranchdump .= " , download_time=CURRENT_TIMESTAMP()";
						mysql_query($sqlinsertbranchdump);				
					}
					else
					{
						$sqlbranchdumpupd  = "update branch_dump ";
						$sqlbranchdumpupd .= " SET acedns='".$acedns."'";
						$sqlbranchdumpupd .= " ,is_plant='".$is_plant."'";
						$sqlbranchdumpupd .= " , download_time=CURRENT_TIMESTAMP() WHERE branch_code='".addslashes($branch_code)."' 
															AND dump_code='".$dum_code."'";
						mysql_query($sqlbranchdumpupd);
						
					}
			}
	 }
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	define("DB","acedns_STAR");
	$link=mysql_connect(SERVER,USER,PASSWORD,TRUE) or die("Database Connection Error.");
	mysql_select_db(DB,$link) or die("could not connect the database");
	$sqlupdate="UPDATE master_tables_update_info set need_update='no',download_time_no=CURRENT_TIMESTAMP() where table_name='branch_dump'";
	mysql_query($sqlupdate,$link);
?>