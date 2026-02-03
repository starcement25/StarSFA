<?php
define("SERVERREMOTE","52.66.101.239");
define("USERREMOTE","root");
define("PASSWORDREMOTE","cmcl@123");
define("DB","acedns_STAR");

mysql_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE);
mysql_select_db(DB);

$today = date('Y-m-d');
$curdateone = date('Ymd');

/*----> SELECT DISTINCT DATE FROM LOCATION ACCORDING TO FINANCIAL YEAR (1ST APRIL)<----*/
$sql_location_date = "SELECT DISTINCT SUBSTRING(date,1,10) AS distinct_date FROM `location` WHERE SUBSTRING(date,1,10)>='2016-04-01' AND SUBSTRING(date,1,10) != '".$today."'";
$res_location_date = mysql_query($sql_location_date);
while($row_location_date = mysql_fetch_array($res_location_date)){
	$location_date = $row_location_date['distinct_date'];
	
	/*----> SELECT EMPLOYEE DETAILS <----*/
	$sql_emp_code = "SELECT emp_code FROM employee_master ORDER BY emp_code";
	$res_emp_code = mysql_query($sql_emp_code);
	while($row_emp_code = mysql_fetch_array($res_emp_code)){
		$emp_code = $row_emp_code['emp_code'];
				
		/*----> CHECKS FOR EMPLOYEE ATTENDANCE <----*/
		$attend = 0;
		$sql_present = "SELECT emp_code FROM location WHERE emp_code = '".$emp_code."' AND trans_id LIKE 'A%' AND SUBSTRING(date,1,10) = '".$location_date."'";
		$res_present = mysql_query($sql_present);
		$present_check = mysql_num_rows($res_present);
		if($present_check>0)
			$attend = 1;
		
		/*----> UPDATE mis_details_emp_datewise SET present COLUMN VALUE <----*/	
		$sql_update = "UPDATE mis_details_emp_datewise SET present = '".$attend."' WHERE emp_code = '".$emp_code."' AND operation_date = '".$location_date."'";
		$res_update = mysql_query($sql_update);

	}
}
?>