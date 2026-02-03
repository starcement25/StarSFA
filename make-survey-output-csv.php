<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234");
	
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db("acedns_LIPL",$link) or die("could not connect the database");

//$count = 1;
$sql_survey_output = "SELECT survey_id,row_id,value FROM survey_output WHERE row_id IN('RA143','RA145') ORDER BY row_id ASC";
$res_survey_output = mysqli_query($link,$sql_survey_output);
$survey_id_array=array();
while($row_survey_output = mysqli_fetch_assoc($res_survey_output)){
	$survey_id = $row_survey_output['survey_id'];
	$row_id = $row_survey_output['row_id'];
	if(!in_array($survey_id,$survey_id_array))
	{
		array_push($survey_id_array,$survey_id);
	}
	if($row_id=='RA143')
	{
		${area.$row_id.$survey_id}=$row_survey_output['value'];
	}
	if($row_id=='RA145')
	{
		${pin.$row_id.$survey_id}=$row_survey_output['value'];
	}
}
$linesurveydetails = ''; 
foreach($survey_id_array as $survey_id_val)
{
	$row_id_area='RA143';
	$row_id_pin='RA145';
	$valuesurveydetails  = $survey_id_val.",";
	$valuesurveydetails .= ${area.$row_id_area.$survey_id_val}.",";
	$valuesurveydetails .= ${pin.$row_id_pin.$survey_id_val}.",";
	
	$linesurveydetails  .= $valuesurveydetails."\r\n"; 
}
$destination = "lipl-bkup/survey_output_pinarea.csv";
//@unlink($destination);
$file = fopen($destination, "w+");
fputs($file, $linesurveydetails);
fclose($file);
?>