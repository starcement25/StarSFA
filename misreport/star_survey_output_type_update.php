<?php
/*define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","acedns_STAR");
mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);*/
$linksetupadmin=mysqli_connect("localhost","acedns_dnsprod","dnsprod1234") or die("Setup Database Connection Error.");
mysqli_select_db("acedns_acednsproduct",$linksetupadmin) or die("could not connect the setup database");

$sqlnickname="SELECT nick_name, remote_db_access FROM user_details WHERE nick_name='".$nick_name."'";
$rsnickname=mysqli_query($link,$sqlnickname,$linksetupadmin);
$row_nick_name = mysqli_fetch_assoc($rsnickname);
$cntnickname=mysqli_num_rows($rsnickname);
$remote_db_access = $row_nick_name['remote_db_access'];

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$sql_survey_output_rowid = "SELECT DISTINCT row_id FROM survey_output WHERE type = '' OR type = '0'";
$res_survey_output_rowid = mysqli_query($link,$sql_survey_output_rowid);
while($row_survey_output_rowid = mysqli_fetch_assoc($res_survey_output_rowid)){
	$row_id = $row_survey_output_rowid['row_id'];
	
	$sql_survey_input = "SELECT survey_sub_menu FROM survey_input WHERE row_id = '".$row_id."'";
	$res_survey_input = mysqli_query($link,$sql_survey_input);
	$row_survey_input = mysqli_fetch_assoc($res_survey_input);
	$survey_sub_menu = $row_survey_input['survey_sub_menu'];
	
	$sql_update_surveyoutput = "UPDATE survey_output SET type = '".$survey_sub_menu."' WHERE row_id = '".$row_id."'";
	$res_update_surveyoutput = mysqli_query($link,$sql_update_surveyoutput);
}
echo "Data Updated";
mysqli_close($link);
?>
