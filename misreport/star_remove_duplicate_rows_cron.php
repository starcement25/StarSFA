<?php
define("SERVERREMOTE","52.66.101.239");
define("USERREMOTE","root");
define("PASSWORDREMOTE","cmcl@123");
define("DB","acedns_STAR");

mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE);
mysqli_select_db(DB);

$sql_duplicate_row = "SELECT emp_code, operation_date, count(*) as value 
 FROM mis_details_emp_datewise
 GROUP BY emp_code, operation_date HAVING count(*)> 1";
$res_duplicate_row = mysqli_query($link,$sql_duplicate_row);
while($row_duplicate_row = mysqli_fetch_assoc($res_duplicate_row)){
	
	$emp_code = $row_duplicate_row['emp_code'];
	$operation_date = $row_duplicate_row['operation_date'];
	$value = $row_duplicate_row['value'];
	
	$sl_array = array();
	$sql_emp_details = "SELECT sl_no FROM mis_details_emp_datewise WHERE emp_code = '".$emp_code."' AND operation_date = '".$operation_date."'";
	$res_emp_details = mysqli_query($link,$sql_emp_details);
	while($row_emp_details = mysqli_fetch_assoc($res_emp_details)){
		$sl_array[] = $row_emp_details['sl_no'];
	}
	
	/*echo "<pre>";
	print_r($sl_array);
	echo "</pre>";*/
	
	array_pop($sl_array);
	
	/*echo "<pre>";
	print_r($sl_array);
	echo "</pre>";*/
	
	foreach($sl_array as $sl_val){
		$sql_del = "DELETE FROM mis_details_emp_datewise WHERE emp_code = '".$emp_code."' AND operation_date = '".$operation_date."' AND sl_no = '".$sl_val."'";
		$res_del = mysqli_query($link,$sql_del);
	}
	//die;
}
echo "Data deleted";
?>