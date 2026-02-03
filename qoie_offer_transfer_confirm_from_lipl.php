<?php
include "connection.php";
$table_name = "r_offer_table";
$body = $_POST["confirm_data"];
$modified_datetime = date("Y-m-d H:i:s");
$survey_id_strin = "";
$xml=simplexml_load_string($body) or die("Error: Cannot create object");
if(count($xml)>0){
	foreach($xml as $xml_key=>$xml_val){
		//$survey_id_strin .= "'".trim($xml_val->survey_id)."',";
		$the_survey_id = $xml_val->survey_id ? trim($xml_val->survey_id) : "";
		$the_offer_id = $xml_val->offer_id ? trim($xml_val->offer_id) : "";
$sql1 = "update $table_name set `transfer_flag`='YES',`modified_datetime`='$modified_datetime' where `survey_id`='$the_survey_id' and `offer_id`='$the_offer_id'";
$res1 = mysqli_query($link,$sql1);
		
	}
echo "Data Updated";
}else{
echo "No New Data Updated";
}
mysqli_close();
?>