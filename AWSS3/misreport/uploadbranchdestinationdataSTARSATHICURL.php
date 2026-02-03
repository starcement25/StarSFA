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
			if($key=='destination_code_name')   	 $destination_code_name=$value;
			if(!isset(${destination_code.$branch_code_name}))
			{
				${destination_code.$branch_code_name}=array();
			}
			if(!in_array($destination_code_name,${destination_code.$branch_code_name}))
			{
			  if($destination_code_name!=''){
				array_push(${destination_code.$branch_code_name},$destination_code_name);
				}
			}
			if($key=='acedns')  		${acedns.$branch_code_name.$destination_code_name}=$value;
		}
	}
	foreach($branch_code_array as $branch_code_val){
	  foreach(${destination_code.$branch_code_val} as $destination_code_val)
		{
		$branch_code_name=$branch_code_val;
		$destination_code_name=$destination_code_val;
		$acedns=${acedns.$branch_code_val.$destination_code_val};
		
				$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE dns_branch_code='".addslashes($branch_code_name)."'";
				$rsbranchnamechk=mysql_query($sqlbranchnamechk);
				$rowbranchnamechk=mysql_fetch_array($rsbranchnamechk);
				$branch_code=$rowbranchnamechk['branch_code'];
				
				$sqldestinationnamechk="SELECT destination_code FROM destination_master WHERE dns_destination_code='".addslashes($destination_code_name)."'";
				$rsdestinationnamechk=mysql_query($sqldestinationnamechk);
				$rowdestinationnamechk=mysql_fetch_array($rsdestinationnamechk);
				$destination_code=$rowdestinationnamechk['destination_code'];

				$sqlbranchdestinationfreight="SELECT branch_code FROM branch_destination_freight WHERE branch_code='".addslashes($branch_code)."' 
											AND destination_code='".$destination_code."'";
				$rsbranchdestinationfreight=mysql_query($sqlbranchdestinationfreight);
				$countbranchdestinationfreight=mysql_num_rows($rsbranchdestinationfreight);
				if($countbranchdestinationfreight<1 )
					{
						$sqlbranchdestinationfreight  = "insert into branch_destination_freight ";
						$sqlbranchdestinationfreight .= " SET branch_code='".$branch_code."'";
						$sqlbranchdestinationfreight .= " ,destination_code='".$destination_code."'";
						$sqlbranchdestinationfreight .= " ,acedns='".$acedns."'";
						$sqlbranchdestinationfreight .= " , download_time=CURRENT_TIMESTAMP()";
						mysql_query($sqlbranchdestinationfreight) or  array_push($error_array,"mysql_error().
										Internal DATA execution problem on depot destination freight table.PLease contact aceDNS admin.");				
					}
					else
					{
						$sqlbranchdestinationfreightupd  = "update branch_destination_freight ";
						$sqlbranchdestinationfreightupd .= " SET acedns='".$acedns."'";
						$sqlbranchdestinationfreightupd .= " , download_time=CURRENT_TIMESTAMP() WHERE branch_code='".addslashes($branch_code)."' 
															AND destination_code='".$destination_code."'";
						mysql_query($sqlbranchdestinationfreightupd);
						
					}
		}
	 }
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	define("DB","acedns_STAR");
	$link=mysql_connect(SERVER,USER,PASSWORD,TRUE) or die("Database Connection Error.");
	mysql_select_db(DB,$link) or die("could not connect the database");
	$sqlupdate="UPDATE master_tables_update_info set need_update='no',download_time_no=CURRENT_TIMESTAMP() where table_name='branch_destination_freight'";
	mysql_query($sqlupdate,$link);
?>

