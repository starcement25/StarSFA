<?php

// check error
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

  set_time_limit(1000);
ini_set('memory_limit', '-1');
//set_time_limit(0);
ob_start();
 define("SERVER","localhost");
	define("USER","root");
	define("PASSWORD","Passw0rd123#$");
	//require("include/config-setup.php");
	define("DB","acedns_STAR");
	//define("DB","acedns_ARCHITA");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db($link,DB) or die("could not connect the database for invalid nick name");
ob_end_flush();	
header('Content-type: text/html; charset=utf-8');
mysqli_set_charset($link,"UTF8");
$t_att_checkout_info = "t_att_checkout_info";
$res_data = array();
$attendance_data = array();
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$curr_date = date("Y-m-d");
$before_30_day_date = date('Y-m-d',strtotime("-30 days"));
$username = $_REQUEST["username"] ? addslashes(trim($_REQUEST["username"])) :"";
$password = $_REQUEST["password"] ? addslashes(trim($_REQUEST["password"])) :"";
$start_date = $_REQUEST["start_date"] ? addslashes(trim($_REQUEST["start_date"])) :"";
$end_date = $_REQUEST["end_date"] ? addslashes(trim($_REQUEST["end_date"])) : "";
if($username=="STARDATA" && $password=="123456"){
if($start_date!="" && $end_date!=""){
	
	$sqlall = "select * FROM  $t_att_checkout_info WHERE Entry_Date >='".$start_date."' AND Entry_Date <='".$end_date."'
			 ORDER BY Entry_Date DESC,emp_name ASC";
	$resall = mysqli_query($link,$sqlall);
	$totall = mysqli_num_rows($resall);
	if($totall>0){
	while($row11=mysqli_fetch_assoc($resall)){
		$Emp_Id = $row11["Emp_Id"] ? trim($row11["Emp_Id"]) : "";
		$Emp_Name = $row11["Emp_Name"];
		$Entry_Date = $row11["Entry_Date"];
		$CheckIN = $row11["CheckIN"];
		$CheckOUT = $row11["CheckOUT"];
		$attendance_data[] = array("Emp_Id"=>$Emp_Id,"Emp_Name"=>$Emp_Name,"Entry_Date"=>$Entry_Date,"CheckIN"=>$CheckIN,"CheckOUT"=>$CheckOUT);
	}
$res_data = array("process_status"=>"Success.","fetched_data"=>$attendance_data);
}else{	
	$res_data = array("process_status"=>"Failure","process_message"=>"No data found for the provided date range");
}
}else{	
	$res_data = array("process_status"=>"Failure","process_message"=>"provide valid start date and end date.");
}
}
else{	
	$res_data = array("process_status"=>"Failure","process_message"=>"Provide Valid username and Password.");
}		
echo json_encode($res_data);
mysqli_close();
?>