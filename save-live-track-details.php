<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
//require("include/config-setup.php");
define("DB","acedns_MAGIK");
$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
mysqli_select_db(DB,$link) or die("could not connect the database");

$live_tracking_latt_long = "live_tracking_latt_long";

$latt = $_REQUEST["latt"];
$long = $_REQUEST["long"];
if($latt!='' && $long!='')
{
	$sqlinsert="INSERT INTO $live_tracking_latt_long SET latt='".addslashes($latt)."',`long`='".addslashes($long)."',update_time=CURRENT_TIMESTAMP()";
	$rsinsert = mysqli_query($link,$sqlinsert);
	$res_data = array("process_status"=>"YES","process_message"=>"Tracking data saved successfully");
}else{
		$res_data = array("process_status"=>"NO","process_message"=>"Need Proper Latt Long");
}
echo json_encode($res_data);
mysqli_close();
?>