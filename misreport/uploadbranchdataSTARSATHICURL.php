<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	//echo"<pre>";print_r('ss');die;
	// define("SERVERREMOTE","103.87.174.95");
	// define("USERREMOTE","starsaat_dnsprod");
	// define("PASSWORDREMOTE","dnsprod1234#");
	// define("DBREMOTE","starsaat_START");
	$type ='remortdb';
	include "saathi_connection.php";
	//mysqli_select_db(DBREMOTE,$link) or die("could not connect the database");
//echo"<pre>";print_r($_POST);die;

	//print_r($_POST);
	//$array_val=json_decode($_POST['postvar3']);
	//print_r($array_val);
	$dns_branch_code_array=array();
	foreach($_POST as $array_value) {
		foreach($array_value as $key=>$value) {
 	 		if($key=='dns_branch_code') $dns_branch_code=$value;
			if(!in_array($dns_branch_code,$dns_branch_code_array))
			{
				array_push($dns_branch_code_array,$dns_branch_code);
			}
			
			if($key=='branch_location')   	 ${'branch_location'.$dns_branch_code}=$value;
			if($key=='branch_name') 	   	 ${'branch_name'.$dns_branch_code}=$value;
			if($key=='branch_state') 	    ${'branch_state'.$dns_branch_code}=$value;
			if($key=='comp_code') 		   ${'comp_code'.$dns_branch_code}=$value;
			if($key=='branch_email_id')     ${'branch_email_id'.$dns_branch_code}=$value;
			if($key=='alternative_email_id') ${'alternative_email_id'.$dns_branch_code}=$value;
			if($key=='acedns') 	   		  ${'acedns'.$dns_branch_code}=$value;
		}
	}
	foreach($dns_branch_code_array as $dns_branch_code_val){
		$dns_branch_code=$dns_branch_code_val;
		$branch_location=${'branch_location'.$dns_branch_code_val};
		$branch_name=${'branch_name'.$dns_branch_code_val};
		$branch_state=${'branch_state'.$dns_branch_code_val};
		$comp_code=${'comp_code'.$dns_branch_code_val};
		$branch_email_id=${'branch_email_id'.$dns_branch_code_val};
		$alternative_email_id=${'alternative_email_id'.$dns_branch_code_val};
		$acedns=${'acedns'.$dns_branch_code_val};
			
			$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$dns_branch_code."'";
			$rsbranchnamechk=mysqli_query($link,$sqlbranchnamechk);
			$countbranchnamechk=mysqli_num_rows($rsbranchnamechk);
			if($countbranchnamechk<1)
			{
				$sqlmaxbranchcode="SELECT MAX(branch_code) AS max_branch_code FROM  branch_master WHERE 1";
				$rsmaxbranchcode=mysqli_query($link,$sqlmaxbranchcode);
				$rowmaxbranchcode=mysqli_fetch_assoc($rsmaxbranchcode);
				$max_branch_code=$rowmaxbranchcode['max_branch_code'];
				
				if($max_branch_code=='')
				{
					$max_branch_code='B0001';
				}
				else
				{
					$max_branch_code++;
				}
			
				$sqlbranch  = "insert into branch_master SET ";
				$sqlbranch .= "  	branch_code='".mysqli_real_escape_string($link,$max_branch_code)."'";
				$sqlbranch .= " , dns_branch_code='".mysqli_real_escape_string($link,$dns_branch_code)."'";
				$sqlbranch .= " , branch_name='".mysqli_real_escape_string($link,$branch_name)."'";
				$sqlbranch .= " , branch_state='".mysqli_real_escape_string($link,$branch_state)."'";
				$sqlbranch .= " , branch_location='".mysqli_real_escape_string($link,$branch_location)."'";
				$sqlbranch .= " , comp_code='".mysqli_real_escape_string($link,$comp_code)."'";
				$sqlbranch .= " , branch_email_id='".mysqli_real_escape_string($link,$branch_email_id)."'";
				$sqlbranch .= " , alternative_email_id='".mysqli_real_escape_string($link,$alternative_email_id)."'";
				$sqlbranch .= " , download_time=CURRENT_TIMESTAMP()";
			}
			else
			{
				$rowbranchnamechk=mysqli_fetch_assoc($rsbranchnamechk);
				$branch_code=$rowbranchnamechk['branch_code'];

				$sqlbranch  = "UPDATE branch_master SET ";
				$sqlbranch .= "  	dns_branch_code='".mysqli_real_escape_string($link,$dns_branch_code)."'";
				$sqlbranch .= " , branch_location='".mysqli_real_escape_string($link,$branch_location)."'";
				$sqlbranch .= " , branch_name='".mysqli_real_escape_string($link,$branch_name)."'";
				$sqlbranch .= " , branch_state='".mysqli_real_escape_string($link,$branch_state)."'";
				$sqlbranch .= " , comp_code='".mysqli_real_escape_string($link,$comp_code)."'";
				$sqlbranch .= " , branch_email_id='".mysqli_real_escape_string($link,$branch_email_id)."'";
				$sqlbranch .= " , download_time=CURRENT_TIMESTAMP()";
				$sqlbranch .= " , alternative_email_id='".mysqli_real_escape_string($link,$alternative_email_id)."' 
								WHERE branch_code='".$branch_code."'";
			}
			mysqli_query($link,$sqlbranch);
	   }
		/*define("SERVER","localhost");
		define("USER","acedns_dnsprod");
		define("PASSWORD","dnsprod1234#");
		define("DB","acedns_STAR");*/

		$type ='localdb';
		include "saathi_connection.php";
		//mysqli_select_db(DB,$link) or die("could not connect the database");
		$sqlupdate="UPDATE master_tables_update_info set need_update='no',download_time_no=CURRENT_TIMESTAMP() where table_name='branch_master'";
		mysqli_query($link,$sqlupdate);
?>
