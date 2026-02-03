<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
date_default_timezone_set("Asia/Kolkata");
$servername = "localhost";
$username = "stradtes_tm";
$password = "X(YEr@qYsZ%b";
$db_name = "stradtes_TM";

$conn = mysqli_connect($servername, $username, $password,$db_name);
if (mysqli_connect_errno()){
die("Failed to connect to MySQL: " . mysqli_connect_error().mysqli_connect_errno());
}
$folder_path = "psrmap"; 
   
// List of name of files inside 
// specified folder 
$files = glob($folder_path.'/*');  
   
// Deleting all the files in the list 
foreach($files as $file) { 
    echo $file.'<br />';
	$file_parts=explode("/",$file);
	$rec_count = 0;
	$ins_count = 0;
	$err = "";
	//echo $file_parts[1];
	$lines = file("/home/stradtestnow/public_html/palsons/psrmap/".$file_parts[1]);
	/*echo '<pre>';
	print_r($lines);
	echo '<pre>';*/
	/*$sqldelete="DELETE FROM ledger_balance_details WHERE UPPER(cust_class)='SALT'";
	$rsdelete=mysqli_query($link,$sqldelete);*/
	$line='';
	foreach($lines as $line)
	{
		$i = 0;
		$char = substr($line, $i, 1);
		$value ="";
		$data=array();
		$double_coute_found = false;
		if($rec_count>=1)
		{ 
			while($char!="")
			{
				if($double_coute_found && $char=="\"")
				{
					$double_coute_found = false;
	
					$i++;
	
					$char = substr($line, $i, 1);
	
					continue;
				}
				if(!$double_coute_found && $char=="\"")
				{  
					$double_coute_found = true;
	
					$i++;
	
					$char = substr($line, $i, 1);
	
					continue;
				}
				if($char=="," && !$double_coute_found)
				{
	
					//$data[]=$value;
					array_push($data,$value);
	
					$value = "";
				}
				else 
				{
				$value .= $char;
	
				}
				$i++;
				$char = substr($line, $i, 1);
			} //end of while
	
			//$data[]=$value;
			array_push($data,$value);
		  //print_r($data);
			$csv_row_count=$rec_count+1;
			  	$status=trim($data[0]);
				$customer_code=trim($data[3]);
				$mis_head_quater=trim($data[5]);
				$sqlchkpsr="SELECT GROUP_CONCAT(psr_code SEPARATOR ',') AS final_psr FROM HQ_psr_mapping WHERE 
								 mis_head_quater='".addslashes($mis_head_quater)."'";				
				$rschkchkpsr=mysqli_query($conn,$sqlchkpsr);
				
				$chkpsr=mysqli_num_rows($rschkchkpsr);
				$csv_row_count=$rec_count+1;
				if($chkpsr >0)
				{
					$rowchkchkpsr=mysqli_fetch_assoc($rschkchkpsr);
					$HQ_psr_code=$rowchkchkpsr['final_psr'];
					//print_r($HQ_psr_code);
					$HQ_psr_array=explode(",",$HQ_psr_code);
					
				  $sqlcustomerchk="SELECT psr_code FROM customer_master WHERE `Customer Code`='".addslashes($customer_code)."' ";
				  $rscustomerchk=mysqli_query($conn,$sqlcustomerchk);
				  $countcustomerchk=mysqli_num_rows($rscustomerchk);
				  $rowcustomerchk=mysqli_fetch_array($rscustomerchk);
					  $psr_customer=$rowcustomerchk['psr_code'];
					  $psr_customer_array=explode(",",$psr_customer);
					 // print_r($psr_customer_array);
					  //echo count($psr_customer_array);
					  foreach( $HQ_psr_array as $HQ_psr_val)
					  {
						  if(!in_array($HQ_psr_val,$psr_customer_array))
						  {
							$HQ_psr_val=ltrim($HQ_psr_val, "0");
							if(count($psr_customer_array)<1)
							{
							$sqludate  = "UPDATE   customer_master SET ";
							$sqludate .= "   psr_code='".addslashes($HQ_psr_val)."'";
							echo $sqludate .= "  , mis_head_quater='".addslashes($mis_head_quater)."' WHERE `Customer Code`='".addslashes($customer_code)."'";
							mysqli_query($conn,$sqludate);
							}
							else
							{
							$sqludate  = "UPDATE   customer_master SET ";
							$sqludate .= "   psr_code=CONCAT(psr_code,',','$HQ_psr_val')";
							echo $sqludate .= "  , mis_head_quater=CONCAT(mis_head_quater,',','$mis_head_quater') WHERE `Customer Code`='".addslashes($customer_code)."'";
							mysqli_query($conn,$sqludate);

							}
						  }
					}
			}
		}
	   $rec_count++;
	}
	echo $successval=' <br /> Success <br />';
} 
?>