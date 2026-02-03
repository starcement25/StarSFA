<?php
define("SERVER","103.242.119.68");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_EMAMI");
$conn = mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);

$count = 1;
$array = array();
$filename = 'emami-scale.csv';
$file = fopen($filename,"r");
while(! feof($file))
{
	$file1=fgetcsv($file);
		
	if($file1[0] != 'dns_emp_code' && $file1[1] != 'emp_code'){
		
		$emp_code = $file1[1];
		$level = $file1[6];
		
		$sql_update_emp_master = "UPDATE employee_master SET level = '".$level."', download_time = current_timestamp WHERE emp_code = '".$emp_code."'";
		mysqli_query($link,$sql_update_emp_master);
	}
}

echo "Successfully Updated";
?>