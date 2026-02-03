<?php
/* ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);*/
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
//$suid=$_REQUEST['suid'];


$sql_survey_output_chkc = "SELECT * FROM emp_mtl_mapping where emp_code = '".$emp_code."'";
//echo $sql_survey_output_chkc;

$res_sc = mysqli_query($link,$sql_survey_output_chkc);
$count_c=mysqli_num_rows($res_sc);


if($count_c > 0){
while($rowdis = mysqli_fetch_assoc($res_sc)){
		$mtl_no = $rowdis['mtl_no'];
		$emp_code = $rowdis['emp_code'];
	
		

        
    
	$valuesurveydetails  = (($emp_code!='')?$emp_code: ' ')."^";
	$valuesurveydetails  .= (($mtl_no!='')?$mtl_no: ' ')."";
	
	
	$linecontents  .= $valuesurveydetails."\n";


}
$linesurveydetails = '';


 $contentsrowcolumn=$count_c.'¥'.'2';
$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
}
else
	{
		
			$datacontents = '0'.'¥'.'2';
	}
	
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=leade_genaration_approval.txt");
	print "$datacontents"; 	
	mysqli_close($link);	
?>