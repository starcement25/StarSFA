<?php
include "edms_connection.php";
$server_url1 = "http://" . $_SERVER['SERVER_NAME']."/";
$employee_kyc_master = "employee_kyc_master";
$employee_kyc_data = array();
$the_emp_code = $_POST["emp_code"] ? addslashes(trim($_POST["emp_code"])) : "";
if($the_emp_code!=""){
$sqlall = "select * from $employee_kyc_master where (`emp_code`='$the_emp_code' or `customer_code`='$the_emp_code' or `dns_customer_code`='$the_emp_code')";
$resall = mysqli_query($link,$sqlall);
$totall = mysqli_num_rows($resall);
if($totall>0){
	$row2 = mysqli_fetch_assoc($resall);
	$ftc_emp_code = $row2["customer_code"];
	$ftc_whatsapp_no = $row2["whatsapp_no"];
	$ftc_dob = $row2["dob"];
	$ftc_dom = $row2["dom"];
	$ftc_email_id = $row2["email_id"];
	$employee_kyc_data = array("emp_code"=>$ftc_emp_code,"whatsapp_no"=>$ftc_whatsapp_no,"dob"=>$ftc_dob,"dom"=>$ftc_dom,"email_id"=>$ftc_email_id);
	$res_data = array("process_status"=>"YES","process_message"=>"KYC details successfully fetched.","employee_kyc_data"=>$employee_kyc_data);
}else{
	$res_data = array("process_status"=>"NO","process_message"=>"KYC details not saved yet.");
}
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong.");
}	
echo json_encode($res_data);
mysqli_close();
?>