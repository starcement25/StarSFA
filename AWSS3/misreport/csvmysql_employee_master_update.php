<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","acedns_PARLE");

mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB);

$count = 1;
$record = 1;
$array = array();
$filename = 'Employee Master.csv';
$file = fopen($filename,"r");
while(! feof($file))
{
	$file1=fgetcsv($file);
	
	if($count != 1){
		/*echo "<pre>";
		print_r($file1);
		echo "</pre>";
		exit();*/
		
		$emp_code = $file1[0];
		$emp_name = $file1[1];
		$branch_code = $file1[2];
		$vertical_value = $file1[3];
		$reporting_to = $file1[4];
		$email = $file1[5];
		$phone = $file1[6];
		$sale_access = $file1[7];
		$designation = $file1[8];
		$HQ = $file1[9];
		$State = $file1[10];
		$zone = $file1[11];
		$acedns = $file1[12];
		
		/*if($emp_code == '')
		break;*/
		$sql_empcode_exist_check = "SELECT emp_code FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_empcode_exist_check = mysql_query($sql_empcode_exist_check);
		$total_rows = mysql_num_rows($res_empcode_exist_check);
		if($total_rows>0){
			$sql_reporting = "SELECT emp_code FROM employee_master WHERE emp_name = '".$reporting_to."'";
			$res_reporting = mysql_query($sql_reporting);
			$row_reporting = mysql_fetch_array($res_reporting);
			$reporting_to_emp = $row_reporting['emp_code'];
			
			echo $sql = "UPDATE employee_master SET 
											`emp_name` = '".$emp_name."', 
											`acedns` = 'Y', 
											`branch_code` = '".$branch_code."', 
											`reporting_to` = '".$reporting_to_emp."', 
											`vertical_value` = '".$vertical_value."', 
											`email` = '".$email."', 
											`phone_no` = '".$phone."', 
											`HQ` = '".$HQ."', 
											`sale_access` = '".$sale_access."', 
											`designation` = '".$designation."', 
											`state` = '".$State."', 
											`zone` = '".$zone."', 
											`app_access` = 'Y',`download_time` = current_timestamp 
											WHERE emp_code = '".$emp_code."'";
		}
		else{
			$sql = "INSERT INTO INTO employee_master SET 
											`emp_code` = '".$emp_code."', 
											`emp_name` = '".$emp_name."', 
											`acedns` = 'Y', 
											`branch_code` = '".$branch_code."', 
											`reporting_to` = '".$reporting_to_emp."', 
											`vertical_value` = '".$vertical_value."', 
											`email` = '".$email."', 
											`phone_no` = '".$phone."', 
											`HQ` = '".$HQ."', 
											`sale_access` = '".$sale_access."', 
											`designation` = '".$designation."', 
											`state` = '".$State."', 
											`zone` = '".$zone."', 
											`app_access` = 'Y',
											`download_time` = current_timestamp";
		}
		$res = mysql_query($sql);
		//echo "<br><br>";
	}
	$count++;
}
echo "Updated Successfully";
?>