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
	$dns_destination_code_array=array();
	foreach($_POST as $array_value) {
		foreach($array_value as $key=>$value) {
 	 		if($key=='dns_destination_code') $dns_destination_code=$value;
			if(!in_array($dns_destination_code,$dns_destination_code_array))
			{
				array_push($dns_destination_code_array,$dns_destination_code);
			}
			if($key=='destination_name')   	 ${destination_name.$dns_destination_code}=$value;
			if($key=='ex_for_type')   	 	  ${ex_for_type.$dns_destination_code}=$value;
		}
	}
	foreach($dns_destination_code_array as $dns_destination_code_val){
		$dns_destination_code=$dns_destination_code_val;
		$destination_name=${destination_name.$dns_destination_code_val};
		$ex_for_type=${ex_for_type.$dns_destination_code_val};
		
		  $sqldestinationnamechk="SELECT destination_code FROM destination_master WHERE dns_destination_code='".addslashes($dns_destination_code)."'";
		  $rsdestinationnamechk=mysql_query($sqldestinationnamechk);
		  $countdestinationnamechk=mysql_num_rows($rsdestinationnamechk);
			
			if($countdestinationnamechk<1)
			{
				$sqlmaxdestinationcode="SELECT MAX(destination_code) AS max_destination_code FROM  destination_master WHERE 1";
				$rsmaxdestinationcode=mysql_query($sqlmaxdestinationcode);
				$rowmaxdestinationcode=mysql_fetch_array($rsmaxdestinationcode);
				$max_destination_code=$rowmaxdestinationcode['max_destination_code'];
				
				if($max_destination_code=='')
				{
					$max_destination_code='D0001';
				}
				else
				{
					$max_destination_code++;
				}
			
				$sqldestination  = "insert into destination_master SET ";
				$sqldestination .= "   destination_code='".mysql_real_escape_string($max_destination_code)."'";
				$sqldestination .= " , dns_destination_code='".mysql_real_escape_string($dns_destination_code)."'";
				$sqldestination .= " , destination_name='".mysql_real_escape_string($destination_name)."'";
				$sqldestination .= " , ex_for_type='".mysql_real_escape_string($ex_for_type)."'";
				$sqldestination .= " , download_time=CURRENT_TIMESTAMP()";
				mysql_query($sqldestination) or array_push($error_array,"mysql_error().Duplicate key @row $csv_row_count in Destination master.csv.Please check.");
				$emp_code='';
			}
			else
			{
				$rowdestinationnamechk=mysql_fetch_array($rsdestinationnamechk);
				$destination_code=$rowdestinationnamechk['destination_code'];
	
				$sqldestination  = "UPDATE destination_master SET ";
				$sqldestination .= "  dns_destination_code='".mysql_real_escape_string($dns_destination_code)."',
									  destination_name='".addslashes($destination_name)."',
									  ex_for_type='".addslashes($ex_for_type)."',
									download_time=CURRENT_TIMESTAMP() WHERE destination_code='".mysql_real_escape_string($destination_code)."'";
				mysql_query($sqldestination) or array_push($error_array,"mysql_error().Duplicate key @row $csv_row_count in Destination master.csv.Please check.");
			}
		}
		define("SERVER","localhost");
		define("USER","acedns_dnsprod");
		define("PASSWORD","dnsprod1234#");
		define("DB","acedns_STAR");
		$link=mysql_connect(SERVER,USER,PASSWORD,TRUE) or die("Database Connection Error.");
		mysql_select_db(DB,$link) or die("could not connect the database");
		$sqlupdate="UPDATE master_tables_update_info set need_update='no',download_time_no=CURRENT_TIMESTAMP() where table_name='destination_master'";
		mysql_query($sqlupdate,$link);
?>
