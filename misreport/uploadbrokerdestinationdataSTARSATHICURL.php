<?php
	// define("SERVERREMOTE","103.87.174.95");
	// define("USERREMOTE","starsaat_dnsprod");
	// define("PASSWORDREMOTE","dnsprod1234#");
	// define("DBREMOTE","starsaat_START");
	// $link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,TRUE) or die("Database Connection Error.");
	// mysqli_select_db(DBREMOTE,$link) or die("could not connect the database");
	$type ='remortdb';
	include "saathi_connection.php";
	//print_r($_POST);
	//$array_val=json_decode($_POST['postvar3']);
	//print_r($array_val);
	$broker_code_array=array();
	$destination_code_array=array();
	foreach($_POST as $array_value) {
		foreach($array_value as $key=>$value) {
 	 		if($key=='broker_code') $broker_code=$value;
			if(!in_array($broker_code,$broker_code_array))
			{
				array_push($broker_code_array,$broker_code);
			}
			if($key=='destination_code_name')   	 $destination_code_name=$value;
			if(!isset(${destination_code.$broker_code}))
			{
				${destination_code.$broker_code}=array();
			}
			if(!in_array($destination_code_name,${destination_code.$broker_code}))
			{
			  if($destination_code_name!=''){
				array_push(${destination_code.$broker_code},$destination_code_name);
				}
			}
			if($key=='acedns')  		${acedns.$broker_code.$destination_code_name}=$value;
		}
	}
	foreach($broker_code_array as $broker_code_val){
	  foreach(${destination_code.$broker_code_val} as $destination_code_val)
		{
		$broker_code=$broker_code_val;
		$destination_code_name=$destination_code_val;
		$acedns=${acedns.$broker_code_val.$destination_code_val};
		
				$sqlbrokernamechk="SELECT broker_id FROM broker_master WHERE dns_broker_id='".addslashes($broker_code)."'";
				$rsbrokernamechk=mysqli_query($link,$sqlbrokernamechk);
				$rowbrokernamechk=mysqli_fetch_assoc($rsbrokernamechk);
				$broker_id=$rowbrokernamechk['broker_id'];
				
				$sqldestinationnamechk="SELECT destination_code FROM destination_master WHERE dns_destination_code='".addslashes($destination_code_name)."'";
				$rsdestinationnamechk=mysqli_query($link,$sqldestinationnamechk);
				$rowdestinationnamechk=mysqli_fetch_assoc($rsdestinationnamechk);
				$destination_code=$rowdestinationnamechk['destination_code'];
				
				$sqlbrokerdestination="SELECT broker_id FROM sp_destination WHERE broker_id='".addslashes($broker_id)."' AND destination_code='".$destination_code."'";
				$rsbrokerdestination=mysqli_query($link,$sqlbrokerdestination);
				$countbrokerdestination=mysqli_num_rows($rsbrokerdestination);
				if($countbrokerdestination<1 )
					{
						$sqlbrokerdestination  = "insert into sp_destination ";
						$sqlbrokerdestination .= " SET broker_id='".$broker_id."'";
						$sqlbrokerdestination .= " ,destination_code='".$destination_code."'";
						$sqlbrokerdestination .= " ,acedns='".$acedns."'";
						$sqlbrokerdestination .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlbrokerdestination) or  array_push($error_array,"mysqli_error().
										Internal DATA execution problem on broker destination table.PLease contact aceDNS admin.");				
					}
					else
					{
						$sqlbrokerdestinationupd = "update sp_destination ";
						$sqlbrokerdestinationupd .= " SET acedns='".$acedns."'";
						$sqlbrokerdestinationupd .= " , download_time=CURRENT_TIMESTAMP() WHERE broker_id='".addslashes($broker_id)."' AND destination_code='".$destination_code."'";
						mysqli_query($link,$sqlbrokerdestinationupd);
					}
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
	$sqlupdate="UPDATE master_tables_update_info set need_update='no',download_time_no=CURRENT_TIMESTAMP() where table_name='broker_destination'";
	mysqli_query($link,$sqlupdate,$link);
?>