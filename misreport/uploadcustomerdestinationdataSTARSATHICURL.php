<?php
	//  define("SERVERREMOTE","103.87.174.95");
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
	$customer_code_array=array();
	$destination_code_array=array();
	foreach($_POST as $array_value) {
		foreach($array_value as $key=>$value) {
 	 		if($key=='customer_code_name') $customer_code_name=$value;
			if(!in_array($customer_code_name,$customer_code_array))
			{
				array_push($customer_code_array,$customer_code_name);
			}
			if($key=='destination_code_name')   	 $destination_code_name=$value;
			if(!isset(${destination_code.$customer_code_name}))
			{
				${destination_code.$customer_code_name}=array();
			}
			if(!in_array($destination_code_name,${destination_code.$customer_code_name}))
			{
			  if($destination_code_name!=''){
				array_push(${destination_code.$customer_code_name},$destination_code_name);
				}
			}
			if($key=='acedns')  		${acedns.$customer_code_name.$destination_code_name}=$value;
		}
	}
	foreach($customer_code_array as $customer_code_val){
	  foreach(${destination_code.$customer_code_val} as $destination_code_val)
		{
		$customer_code_name=$customer_code_val;
		$destination_code_name=$destination_code_val;
		$acedns=${acedns.$customer_code_val.$destination_code_val};
		
				$sqlcustnamechk="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($customer_code_name)."'";
				$rscustnamechk=mysqli_query($link,$sqlcustnamechk);
				$rowcustnamechk=mysqli_fetch_assoc($rscustnamechk);
				$customer_code=$rowcustnamechk['customer_code'];
				
				$sqldestinationnamechk="SELECT destination_code FROM destination_master WHERE dns_destination_code='".addslashes($destination_code_name)."'";
				$rsdestinationnamechk=mysqli_query($link,$sqldestinationnamechk);
				$rowdestinationnamechk=mysqli_fetch_assoc($rsdestinationnamechk);
				$destination_code=$rowdestinationnamechk['destination_code'];
				
				$sqlcustomerdestination="SELECT customer_code FROM customer_destination WHERE customer_code='".addslashes($customer_code)."' AND destination_code='".$destination_code."'";
				$rscustomerdestination=mysqli_query($link,$sqlcustomerdestination);
				$countcustomerdestination=mysqli_num_rows($rscustomerdestination);
				if($countcustomerdestination<1 )
					{
						$sqlcustomerdestination  = "insert into customer_destination ";
						$sqlcustomerdestination .= " SET customer_code='".$customer_code."'";
						$sqlcustomerdestination .= " ,destination_code='".$destination_code."'";
						$sqlcustomerdestination .= " ,acedns='".$acedns."'";
						$sqlcustomerdestination .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlcustomerdestination);
					}
					else
					{
						$sqlcustomerdestinationupd = "update customer_destination ";
						$sqlcustomerdestinationupd .= " SET acedns='".$acedns."'";
						$sqlcustomerdestinationupd .= " , download_time=CURRENT_TIMESTAMP() WHERE customer_code='".addslashes($customer_code)."' AND destination_code='".$destination_code."'";
						mysqli_query($link,$sqlcustomerdestinationupd);
					}
			   }
		}
	// define("SERVER","localhost");
	// define("USER","acedns_dnsprod");
	// define("PASSWORD","dnsprod1234#");
	// define("DB","acedns_STAR");
	// $link=mysqli_connect(SERVER,USER,PASSWORD,TRUE) or die("Database Connection Error.");
	$type ='localdb';
		include "saathi_connection.php";
	mysqli_select_db(DB,$link) or die("could not connect the database");
	$sqlupdate="UPDATE master_tables_update_info set need_update='no',download_time_no=CURRENT_TIMESTAMP() where table_name='customer_destination'";
	mysqli_query($link,$sqlupdate,$link);
?>

