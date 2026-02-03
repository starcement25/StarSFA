<?php
define("SERVER","103.242.119.68");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
$conn = mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db("acedns_STAR");

$count = 1;

$filename_location = 'E0239-loc.csv';
$filename_yc = 'E0239-yc.csv';

$file = fopen($filename_location,"r");
while(! feof($file))
{
	
	$file_loc = fgetcsv($file);
	
	if($file_loc[0] != 'emp_code' && $file_loc[1] != 'trans_id'){
		//print_r($file_loc);break;
		
		$sql_insert_loc = "INSERT INTO location SET 
										`emp_code` = '".$file_loc[0]."', 
										`trans_id` = '".$file_loc[1]."', 
										`date` = '".$file_loc[2]."', 
										`updatetime` = '".$file_loc[2]."', 
										`latt` = '".$file_loc[3]."', 
										`longi` = '".$file_loc[4]."', 
										`transferred` = 'NO'";
		mysql_query($sql_insert_loc);
	}

}
fclose($file);


$file = fopen($filename_yc,"r");
while(! feof($file))
{
	
	$file_yc = fgetcsv($file);
	
	if($file_yc[0] != 'yellow_card_no' && $file_yc[1] != 'customer_code'){
		//print_r($file_yc);break;
		$sql_insert_yc = "INSERT INTO yellow_card_details SET 
												`yellow_card_no` = '".$file_yc[0]."', 
												`customer_code` = '".$file_yc[1]."', 
												`challan_no` = '".$file_yc[2]."', 
												`challan_date` = '".$file_yc[3]."', 
												`qty` = '".$file_yc[4]."', 
												`qty_UOM` = '".$file_yc[5]."'";
		mysql_query($sql_insert_yc);
	}

}
fclose($file);

echo "Data Inserted";
?>