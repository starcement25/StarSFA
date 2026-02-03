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
			if($key=='pg_status')  	 ${pg_status.$branch_code_name}=$value;
		}
	}
	foreach($branch_code_array as $branch_code_val){
		$branch_code_name=$branch_code_val;
		$pg_status=${pg_status.$branch_code_name};
		
				$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE dns_branch_code='".addslashes($branch_code_name)."'";
				$rsbranchnamechk=mysql_query($sqlbranchnamechk);
				$rowbranchnamechk=mysql_fetch_array($rsbranchnamechk);
				$branch_code=$rowbranchnamechk['branch_code'];

				$sqlbranchpg="SELECT branch_code FROM  branch_PGstatus WHERE branch_code='".addslashes($branch_code)."'";
				$rsbranchpg=mysql_query($sqlbranchpg);
				$countbranchpg=mysql_num_rows($rsbranchpg);
				if($countbranchpg<1 )
					{
						$sqlinsertbranchpg  = "insert into  branch_PGstatus ";
						$sqlinsertbranchpg .= " SET branch_code='".$branch_code."'";
						$sqlinsertbranchpg .= " ,pg_status='".$pg_status."'";
						$sqlinsertbranchpg .= " , download_time=CURRENT_TIMESTAMP()";
						mysql_query($sqlinsertbranchpg);				
					}
					else
					{
						$sqlbranchpgupd  = "update branch_PGstatus ";
						$sqlbranchpgupd .= " SET pg_status='".$pg_status."'";
						$sqlbranchpgupd .= " , download_time=CURRENT_TIMESTAMP() WHERE branch_code='".addslashes($branch_code)."'";
						mysql_query($sqlbranchpgupd);
					}
			}
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	define("DB","acedns_STAR");
	$link=mysql_connect(SERVER,USER,PASSWORD,TRUE) or die("Database Connection Error.");
	mysql_select_db(DB,$link) or die("could not connect the database");
	$sqlupdate="UPDATE master_tables_update_info set need_update='no',download_time_no=CURRENT_TIMESTAMP() where table_name='branch_pgstatus'";
	mysql_query($sqlupdate,$link);
?>