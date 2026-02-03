<?php
include "school_connection.php";
$employee_master = "employee_master";
$changepassword = "changepassword";
$res_data = array();
$phonenumber = $_POST["phonenumber"] ? trim($_POST["phonenumber"]) : "";
$deviceid = $_POST["deviceid"] ? trim($_POST["deviceid"]) : "";
if($phonenumber!="" && $deviceid!=""){


/*$check = "";
$verificationtoken='';
$emp_code='';
$newpassword='';
$templt_id = "1107161364809551631";*/


$sql_cust_data_ck = "select $employee_master.emp_code,$employee_master.emp_name,$changepassword.`newpassword`,
$changepassword.`deviceid`,$employee_master.`acedns`,$employee_master.`phone_no` from $employee_master left join $changepassword on  $employee_master.emp_code= $changepassword.emp_code where $employee_master.`phone_no`='$phonenumber'";
$res_cust_data_ck = mysqli_query($link,$sql_cust_data_ck);
$totres_cust_data_ck = mysqli_num_rows($res_cust_data_ck);
if($totres_cust_data_ck>0){
$row_cust_data_ck = mysqli_fetch_assoc($res_cust_data_ck);
$emp_code = $row_cust_data_ck["emp_code"];

$emp_name = $row_cust_data_ck["emp_name"];
$emp_phone_no = $row_cust_data_ck["phone_no"];
$emp_newpassword = $row_cust_data_ck["newpassword"];
$device_id_database = $row_cust_data_ck["deviceid"] ? trim($row_cust_data_ck["deviceid"]) : "";
$acedns = $row_cust_data_ck["acedns"];
if(strtoupper($acedns)=='Y'){
$sql_ckcptbl = "select emp_code from $changepassword where emp_code='$emp_code'";
$res_ckcptbl = mysqli_query($link,$sql_ckcptbl);
$totres_ckcptbl = mysqli_num_rows($res_ckcptbl);
if($totres_ckcptbl>0){
$sql_up_cptbl = "update $changepassword set deviceid='$deviceid' where emp_code='$emp_code'";
$res_up_cptbl = mysqli_query($link,$sql_up_cptbl);
}else{
$curr_date_time = date("Y-m-d H:i:s");
$sql_in_cptbl = "insert into $changepassword (emp_code,`newpassword`,`oldpassword`,`status`,deviceid) values ('$emp_code','1234','1234','true','1','$deviceid')";
$res_in_cptbl = mysqli_query($link,$sql_in_cptbl);
}
/*$otp_for_login = rand(1,9).rand(0,9).rand(0,9).rand(1,9);
if($phonenumber=="9233974090" || $phonenumber=="9831722939"){*/
$otp_for_login = "1020";
/*}*/
//$otp_for_login = rand(1,9).rand(0,9).rand(0,9).rand(1,9);
$otp_text = "OTP is ".$otp_for_login." for SCHOOL APP";
$otp_text ="Dear Customer, OTP to login to SCHOOL APP is ".$otp_for_login."";
//$sms_res ="";
/*if($phonenumber=="9233974090" || $phonenumber=="9831722939"){

}else{
$sms_res =send_sms_new($templt_id,$emp_phone_no,$otp_text);
}*/
//$templt_id='1707164383492982971';
//$sms_res =send_sms_new($templt_id,$emp_phone_no,$otp_text);

$sql_cust_otp_upd = "update $employee_master set `sms_otp`='$otp_for_login' where emp_code='$emp_code'";
$res_cust_otp_upd = mysqli_query($link,$sql_cust_otp_upd);


$res_data = array("process_status"=>"YES","process_message"=>"OTP has been sent to your mobile number.","emp_code"=>$emp_code,"emp_name"=>$emp_name,"newpassword"=>$emp_newpassword,"deviceid"=>$deviceid,"phonenumber"=>$emp_phone_no,"otp_text"=>$otp_for_login,"addr_display"=>"NO");
}else{
$res_data = array("process_status"=>"NO","process_message"=>"NOT LICENSED USER");
}		
}else{
$res_data = array("process_status"=>"NO","process_message"=>"NOT VALID USER");
}
}else{
$res_data = array("process_status"=>"NO","process_message"=>"All fields are mandatory.");
}

$json_encoded = json_encode($res_data);
echo $json_encoded;	

if($conn!=""){
mysqli_close($conn);
}
?>