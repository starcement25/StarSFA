<?php
	define("SERVERREMOTE","103.87.174.95");
	define("USERREMOTE","starsaat_dnsprod");
	define("PASSWORDREMOTE","dnsprod1234#");
	define("DBREMOTE","starsaathi_STARS");
		
	$link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,TRUE) or die("Database Connection Error.");
	mysqli_select_db(DBREMOTE,$link) or die("could not connect the database");
	
	//print_r($_POST);
	//$array_val=json_decode($_POST['postvar3']);
	//print_r($array_val);
	$dns_broker_code_array=array();
	foreach($_POST as $array_value) {
		foreach($array_value as $key=>$value) {
 	 		if($key=='dns_broker_id') $dns_broker_id=$value;
			if(!in_array($dns_broker_id,$dns_broker_code_array))
			{
				array_push($dns_broker_code_array,$dns_broker_id);
			}
			if($key=='broker_name')   	 ${broker_name.$dns_broker_id}=$value;
			if($key=='contact_person')  ${contact_person.$dns_broker_id}=$value;
			if($key=='mail_id')  		 ${mail_id.$dns_broker_id}=$value;
			if($key=='phone_no')  		${phone_no.$dns_broker_id}=$value;
			if($key=='acedns')  		  ${acedns.$dns_broker_id}=$value;
		}
	}
	foreach($dns_broker_code_array as $dns_broker_code_val){
		$dns_broker_id=$dns_broker_code_val;
		$broker_name=${broker_name.$dns_broker_code_val};
		$contact_person=${contact_person.$dns_broker_code_val};
		$mail_id=${mail_id.$dns_broker_code_val};
		$phone_no=${phone_no.$dns_broker_code_val};
		$acedns=${acedns.$dns_broker_code_val};
		
		  $sqlbrokerchk="SELECT broker_id FROM broker_master WHERE dns_broker_id='".addslashes($dns_broker_id)."'";
		  $rsbrokerchk=mysqli_query($link,$sqlbrokerchk);
				$countbrokerchk=mysqli_num_rows($rsbrokerchk);
				$rowbrokerchk=mysqli_fetch_assoc($rsbrokerchk);
				$csv_row_count=$rec_count+1;
				if($countbrokerchk<1)
				{
					$sqlmaxbrokercode="SELECT MAX(broker_id) AS max_broker_id FROM  broker_master WHERE 1";
					$rsmaxbrokercode=mysqli_query($link,$sqlmaxbrokercode);
					$rowmaxbrokercode=mysqli_fetch_assoc($rsmaxbrokercode);
					$max_broker_id=$rowmaxbrokercode['max_broker_id'];
					
					if($max_broker_id=='')
					{
						$max_broker_id='BR0001';
					}
					else
					{
						$max_broker_id++;
					}

					$sqlbroker  = "insert into broker_master SET ";
					$sqlbroker .= "  broker_id='".mysqli_real_escape_string($max_broker_id)."'";
					$sqlbroker .= "  ,broker_name='".mysqli_real_escape_string($broker_name)."'";
					$sqlbroker .= " , dns_broker_id='".mysqli_real_escape_string($dns_broker_id)."'";
					$sqlbroker .= " , contact_person='".mysqli_real_escape_string($contact_person)."'";
					$sqlbroker .= " , mail_id='".mysqli_real_escape_string($mail_id)."'";
					$sqlbroker .= " , phone_no='".mysqli_real_escape_string($phone_no)."'";
					$sqlbroker .= " , acedns='".mysqli_real_escape_string($acedns)."'";
					mysqli_query($link,$sqlbroker) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in Broker master.csv.Please check.");
					
					$sqlchkchangepassword="SELECT customer_code FROM changepassword WHERE customer_code='".$max_broker_id."'";
						$rschkchangepassword=mysqli_query($link,$sqlchkchangepassword);
						$countchkchangepassword=mysqli_num_rows($rschkchangepassword);
						if($countchkchangepassword==0)
						{
							$sqlcp  = "insert into changepassword ";
							$sqlcp .= " SET customer_code='".$max_broker_id."'";
							$sqlcp .= " , dns_customer_code='".$dns_broker_id."'";
							$sqlcp .= " , newpassword='1234'";
							$sqlcp .= " , oldpassword='1234'"; 
							$sqlcp .= " , status='true'";
							$sqlcp .= " , is_licensed='1'"; 
							mysqli_query($link,$sqlcp) or  array_push($error_array,"mysqli_error().Internal DATA execution problem on password table.PLease contact aceDNS admin.");

						}
				}
				else
				{
					$broker_id_db=$rowbrokerchk['broker_id'];
					$sqlupdatebroker  = "UPDATE broker_master SET ";
					$sqlupdatebroker .= "  broker_name='".mysqli_real_escape_string($broker_name)."'";
					$sqlupdatebroker .= " , contact_person='".mysqli_real_escape_string($contact_person)."'";
					$sqlupdatebroker .= " , mail_id='".mysqli_real_escape_string($mail_id)."'";
					$sqlupdatebroker .= " , phone_no='".mysqli_real_escape_string($phone_no)."'";
					$sqlupdatebroker .= " , dns_broker_id='".mysqli_real_escape_string($dns_broker_id)."'";
					$sqlupdatebroker .= " , acedns='".mysqli_real_escape_string($acedns)."',download_time=CURRENT_TIMESTAMP()";
					$sqlupdatebroker .= " WHERE broker_id='".mysqli_real_escape_string($broker_id_db)."'";
					mysqli_query($link,$sqlupdatebroker) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in Broker master.csv.Please check.");
				}		
			}
		/*define("SERVER","localhost");
		define("USER","acedns_dnsprod");
		define("PASSWORD","dnsprod1234#");
		define("DB","acedns_STAR");
		$link=mysqli_connect(SERVER,USER,PASSWORD,TRUE) or die("Database Connection Error.");
		mysqli_select_db(DB,$link) or die("could not connect the database");
		$sqlupdate="UPDATE master_tables_update_info set need_update='no',download_time_no=CURRENT_TIMESTAMP() where table_name='broker_master'";*/
		mysqli_query($link,$sqlupdate,$link);
?>
