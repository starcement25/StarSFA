<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
date_default_timezone_set("Asia/Kolkata");
$servername = "localhost";
$username = "acedns_dnsprod";
$password = "dnsprod1234#";
$db_name = "acedns_school";
$conn = mysqli_connect($servername, $username, $password);
if(!$conn){
   die('Could not connect: ' . mysqli_error());
}
$db_selected = mysqli_select_db($db_name, $conn);
if (!$db_selected) {
    die ('Can\'t connect to database : ' . mysqli_error());
}
define("APICALLLOGURL","http://salesmpower.acedns.in");
function insertapilog($datetime,$emp_code,$url,$nick_name)
	{
		//mysqli_select_db("acedns_".$nick_name);
		$sqlinsertapilog="INSERT INTO apicalllog SET date_time=CURRENT_TIMESTAMP,
						  emp_code='".$emp_code."',
						  url='".$url."'";
		mysqli_query($link,$sqlinsertapilog);				  
	}
?>