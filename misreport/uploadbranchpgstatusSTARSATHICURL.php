<?php
	/*define("SERVERREMOTE","103.87.174.95");
	define("USERREMOTE","starsaat_dnsprod");
	define("PASSWORDREMOTE","dnsprod1234#");
	define("DBREMOTE","starsaat_START");
		
	$link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,TRUE) or die("Database Connection Error.");
	mysqli_select_db(DBREMOTE,$link) or die("could not connect the database");
	*/
	$type ='remortdb';
	include "saathi_connection.php";
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
				$rsbranchnamechk=mysqli_query($link,$sqlbranchnamechk);
				$rowbranchnamechk=mysqli_fetch_assoc($rsbranchnamechk);
				$branch_code=$rowbranchnamechk['branch_code'];

				$sqlbranchpg="SELECT branch_code FROM  branch_PGstatus WHERE branch_code='".addslashes($branch_code)."'";
				$rsbranchpg=mysqli_query($link,$sqlbranchpg);
				$countbranchpg=mysqli_num_rows($rsbranchpg);
				if($countbranchpg<1 )
					{
						$sqlinsertbranchpg  = "insert into  branch_PGstatus ";
						$sqlinsertbranchpg .= " SET branch_code='".$branch_code."'";
						$sqlinsertbranchpg .= " ,pg_status='".$pg_status."'";
						$sqlinsertbranchpg .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlinsertbranchpg);				
					}
					else
					{
						$sqlbranchpgupd  = "update branch_PGstatus ";
						$sqlbranchpgupd .= " SET pg_status='".$pg_status."'";
						$sqlbranchpgupd .= " , download_time=CURRENT_TIMESTAMP() WHERE branch_code='".addslashes($branch_code)."'";
						mysqli_query($link,$sqlbranchpgupd);
					}
			}
	// define("SERVER","localhost");
	// define("USER","acedns_dnsprod");
	// define("PASSWORD","dnsprod1234#");
	// define("DB","acedns_STAR");
	// $link=mysqli_connect(SERVER,USER,PASSWORD,TRUE) or die("Database Connection Error.");
	// mysqli_select_db(DB,$link) or die("could not connect the database");
	$type ='localdb';
	include "saathi_connection.php";
	$sqlupdate="UPDATE master_tables_update_info set need_update='no',download_time_no=CURRENT_TIMESTAMP() where table_name='branch_pgstatus'";
	mysqli_query($link,$sqlupdate,$link);
?>