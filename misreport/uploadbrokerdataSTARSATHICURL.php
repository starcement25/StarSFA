<?php

// define("SERVERREMOTE","103.87.174.95");
// 	define("USERREMOTE","starsaat_dnsprod");
// 	define("PASSWORDREMOTE","dnsprod1234#");
// 	define("DBREMOTE","starsaathi_STARS");
		
// 	$link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,DBREMOTE) or die("Database Connection Error.");
	//mysqli_select_db(DBREMOTE,$link) or die("could not connect the database");
	$type ='remortdb';
	include "saathi_connection.php";
	//print_r($_POST);
	//$array_val=json_decode($_POST['postvar3']);
	//print_r($array_val);
	//echo"<pre>";print_r($_POST);die;
	$dns_broker_code_array=array();
	foreach($_POST as $array_value) {
		foreach($array_value as $key=>$value) {
 	 		if($key=='dns_broker_id') $dns_broker_id=$value;
			if(!in_array($dns_broker_id,$dns_broker_code_array))
			{
				array_push($dns_broker_code_array,$dns_broker_id);
			}
			if($key=='broker_name')   	 ${'broker_name'.$dns_broker_id}=$value;
			if($key=='contact_person')  ${'contact_person'.$dns_broker_id}=$value;
			if($key=='mail_id')  		 ${'mail_id'.$dns_broker_id}=$value;
			if($key=='phone_no')  		${'phone_no'.$dns_broker_id}=$value;
			if($key=='acedns')  		  ${'acedns'.$dns_broker_id}=$value;
		}
	}
	foreach($dns_broker_code_array as $dns_broker_code_val){
		$dns_broker_id=$dns_broker_code_val;
		$broker_name=${'broker_name'.$dns_broker_code_val};
		$contact_person=${'contact_person'.$dns_broker_code_val};
		$mail_id=${'mail_id'.$dns_broker_code_val};
		$phone_no=${'phone_no'.$dns_broker_code_val};
		$acedns=${'acedns'.$dns_broker_code_val};
		
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
					$sqlbroker .= "  broker_id='".addslashes($max_broker_id)."'";
					$sqlbroker .= "  ,broker_name='".addslashes($broker_name)."'";
					$sqlbroker .= " , dns_broker_id='".addslashes($dns_broker_id)."'";
					$sqlbroker .= " , contact_person='".addslashes($contact_person)."'";
					$sqlbroker .= " , mail_id='".addslashes($mail_id)."'";
					$sqlbroker .= " , phone_no='".addslashes($phone_no)."'";
					$sqlbroker .= " , acedns='".addslashes($acedns)."'";
					mysqli_query($link,$sqlbroker) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in Broker master.csv.Please check.");
					//echo"<pre>";print_r($sqlbroker);die;
				}
				else
				{
					$broker_id_db=$rowbrokerchk['broker_id'];
					$sqlupdatebroker  = "UPDATE broker_master SET ";
					$sqlupdatebroker .= "  broker_name='".addslashes($broker_name)."'";
					$sqlupdatebroker .= " , contact_person='".addslashes($contact_person)."'";
					$sqlupdatebroker .= " , mail_id='".addslashes($mail_id)."'";
					$sqlupdatebroker .= " , phone_no='".addslashes($phone_no)."'";
					$sqlupdatebroker .= " , dns_broker_id='".addslashes($dns_broker_id)."'";
					$sqlupdatebroker .= " , acedns='".addslashes($acedns)."',download_time=CURRENT_TIMESTAMP()";
					$sqlupdatebroker .= " WHERE broker_id='".addslashes($broker_id_db)."'";
					mysqli_query($link,$sqlupdatebroker) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in Broker master.csv.Please check.");
					//echo"<pre>";print_r($sqlbroker);die;

				}		
			}
mysqli_close($link);
		$type ='localdb';
		include "saathi_connection.php";
		//mysqli_select_db(DB,$link) or die("could not connect the database");
		$sqlupdate="UPDATE master_tables_update_info set need_update='no',download_time_no=CURRENT_TIMESTAMP() where table_name='broker_master'";
		mysqli_query($link,$sqlupdate);
mysqli_close($link);
?>
