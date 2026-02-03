<?php
	$type ='remortdb';
	include "saathi_connection.php";
	
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
		  $rsdestinationnamechk=mysqli_query($link,$sqldestinationnamechk);
		  $countdestinationnamechk=mysqli_num_rows($rsdestinationnamechk);
			
			if($countdestinationnamechk<1)
			{
				$sqlmaxdestinationcode="SELECT MAX(destination_code) AS max_destination_code FROM  destination_master WHERE 1";
				$rsmaxdestinationcode=mysqli_query($link,$sqlmaxdestinationcode);
				$rowmaxdestinationcode=mysqli_fetch_assoc($rsmaxdestinationcode);
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
				$sqldestination .= "   destination_code='".mysqli_real_escape_string($max_destination_code)."'";
				$sqldestination .= " , dns_destination_code='".mysqli_real_escape_string($dns_destination_code)."'";
				$sqldestination .= " , destination_name='".mysqli_real_escape_string($destination_name)."'";
				$sqldestination .= " , ex_for_type='".mysqli_real_escape_string($ex_for_type)."'";
				$sqldestination .= " , download_time=CURRENT_TIMESTAMP()";
				mysqli_query($link,$sqldestination) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Destination master.csv.Please check.");
				$emp_code='';
			}
			else
			{
				$rowdestinationnamechk=mysqli_fetch_assoc($rsdestinationnamechk);
				$destination_code=$rowdestinationnamechk['destination_code'];
	
				$sqldestination  = "UPDATE destination_master SET ";
				$sqldestination .= "  dns_destination_code='".mysqli_real_escape_string($dns_destination_code)."',
									  destination_name='".addslashes($destination_name)."',
									  ex_for_type='".addslashes($ex_for_type)."',
									download_time=CURRENT_TIMESTAMP() WHERE destination_code='".mysqli_real_escape_string($destination_code)."'";
				mysqli_query($link,$sqldestination) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Destination master.csv.Please check.");
			}
		}
		$type ='localdb';
		include "saathi_connection.php";
		$sqlupdate="UPDATE master_tables_update_info set need_update='no',download_time_no=CURRENT_TIMESTAMP() where table_name='destination_master'";
		mysqli_query($link,$sqlupdate,$link);
?>
