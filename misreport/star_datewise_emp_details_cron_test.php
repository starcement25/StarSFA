<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_STAR");
date_default_timezone_set("Asia/Kolkata");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);

$today = date('Y-m-d');
$dateprevious=date('Y-m-d', strtotime("-1 days,$today "));
$curdateone = date('Ymd');

/*$sql_delete="DELETE FROM `mis_details_emp_datewise` WHERE (`operation_date` BETWEEN '2017-01-05' AND '2017-01-08')";
mysqli_query($link,$sql_delete);*/

/*----> SELECT DISTINCT DATE FROM LOCATION ACCORDING TO FINANCIAL YEAR (1ST APRIL)<----*/
$sql_location_date = "SELECT DISTINCT SUBSTRING(date,1,10) AS distinct_date FROM `location` WHERE SUBSTRING(date,1,10) = '2017-01-05'";
$res_location_date = mysqli_query($link,$sql_location_date);
while($row_location_date = mysqli_fetch_assoc($res_location_date)){
	$location_date = $row_location_date['distinct_date'];
	
	/*----> SELECT EMPLOYEE DETAILS <----*/
	$sql_emp_code = "SELECT emp_code, reporting_to, sale_access FROM employee_master ORDER BY emp_code";
	$res_emp_code = mysqli_query($link,$sql_emp_code);
	while($row_emp_code = mysqli_fetch_assoc($res_emp_code)){
		$emp_code = $row_emp_code['emp_code'];
		$reporting_to = $row_emp_code['reporting_to'];
		$sale_access = $row_emp_code['sale_access'];
		
		/*----> CHECKS FOR EMPLOYEE ATTENDANCE <----*/
		$sql_present = "SELECT emp_code FROM location WHERE emp_code = '".$emp_code."' AND trans_id LIKE 'A%' AND SUBSTRING(date,1,10) = '".$location_date."'";
		$res_present = mysqli_query($link,$sql_present);
		$present_check = mysqli_num_rows($res_present);
		if($present_check>0){
			echo $emp_code."<br>";
		}
	}
}
?>