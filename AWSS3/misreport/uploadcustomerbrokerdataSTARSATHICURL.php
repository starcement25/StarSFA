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

	$customer_code_array=array();
	$broker_code_array=array();
	foreach($_POST as $array_value) {
		foreach($array_value as $key=>$value) {
 	 		if($key=='customer_code') $customer_code=$value;
			if(!in_array($customer_code,$customer_code_array))
			{
				array_push($customer_code_array,$customer_code);
			}
			if($key=='broker_code')   	 $broker_code=$value;
			if(!isset(${broker_code.$customer_code}))
			{
				${broker_code.$customer_code}=array();
			}
			if(!in_array($broker_code,${broker_code.$customer_code}))
			{
			  if($broker_code!=''){
				array_push(${broker_code.$customer_code},$broker_code);
				}
			}
			if($key=='customer_name')  ${customer_name.$customer_code.$broker_code}=$value;
			if($key=='broker_name')   	${broker_name.$customer_code.$broker_code}=$value;
			if($key=='acedns')   	 	 ${acedns.$customer_code.$broker_code}=$value;
			if($key=='mapped_broker')   	${mapped_broker.$customer_code.$broker_code}=$value;
		}
	}
	foreach($customer_code_array as $customer_code_val){
	  foreach(${broker_code.$customer_code_val} as $broker_code_val)
		{
		$dns_customer_code=$customer_code_val;
		$dns_broker_code=$broker_code_val;
		$customer_name=${customer_name.$customer_code_val.$broker_code_val};
		$broker_name=${broker_name.$customer_code_val.$broker_code_val};
		$acedns=${acedns.$customer_code_val.$broker_code_val};
		$mapped_broker=${mapped_broker.$customer_code_val.$broker_code_val};
		
				$sqlcustomercode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($dns_customer_code)."'";
				$rscustomercode=mysql_query($sqlcustomercode);
				$rowcustomercode=mysql_fetch_array($rscustomercode);
				$customer_code_db=$rowcustomercode['customer_code'];
				
				$sqlbrokerchk="SELECT broker_id FROM broker_master WHERE dns_broker_id='".addslashes($dns_broker_code)."'";
				$rsbrokerchk=mysql_query($sqlbrokerchk);
				$rowbrokerchk=mysql_fetch_array($rsbrokerchk);
				$broker_id=$rowbrokerchk['broker_id'];
				
				$sqlcustomerbroker="SELECT customer_code,acedns FROM customer_broker_relation WHERE customer_code='".$customer_code_db."' 
									AND borker_code='".$broker_id."'";
				$rscustomerbroker=mysql_query($sqlcustomerbroker);
				$countcustomerbroker=mysql_num_rows($rscustomerbroker);
				if($countcustomerbroker <1)
				{
					$sqlinsertcustomerbroker="INSERT INTO customer_broker_relation ";
					$sqlinsertcustomerbroker .= " SET customer_code='".$customer_code_db."'";
					$sqlinsertcustomerbroker .= " ,broker_code='".$broker_id."'";
					$sqlinsertcustomerbroker .= " ,acedns='Y'";
					$sqlinsertcustomerbroker .= " ,	mapped_broker='".$mapped_broker."'";
					$sqlinsertcustomerbroker .= " ,download_time=CURRENT_TIMESTAMP()";
					mysql_query($sqlinsertcustomerbroker);
				}
				else
				{
					//For acedns  Y
					$sqlupdatecustomerbroker="UPDATE customer_broker_relation SET acedns='".$acedns."',download_time=CURRENT_TIMESTAMP() WHERE 
										customer_code='".$customer_code."' AND borker_code='".$borker_code."'";
					$rsupdatecustomerbroker=mysql_query($sqlupdatecustomerbroker);
				}
		}
	 }
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	define("DB","acedns_STAR");
	$link=mysql_connect(SERVER,USER,PASSWORD,TRUE) or die("Database Connection Error.");
	mysql_select_db(DB,$link) or die("could not connect the database");
	$sqlupdate="UPDATE master_tables_update_info set need_update='no',download_time_no=CURRENT_TIMESTAMP() where table_name='customer_broker_relation'";
	mysql_query($sqlupdate,$link);
?>
