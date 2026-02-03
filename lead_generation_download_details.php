<?php

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
$suid=$_REQUEST['suid'];

$sql_survey_output = "SELECT survey_id FROM survey_output WHERE  `value` LIKE '%".$suid."%' ORDER by SUBSTRING(survey_id,-14,14) DESC LIMIT 1";
		    $res_survey_output = mysqli_query($link,$sql_survey_output);
		    $row_survey_output = mysqli_fetch_assoc($res_survey_output);
            $suid = $row_survey_output['survey_id'];

$menu_id = "";
$sql_survey_output_chk = "SELECT survey_id,row_id,value FROM survey_output where survey_id = '".$suid."' AND row_id = 'RA486'";
//echo $sql_survey_output_chk;

$res_survey_output_hk = mysqli_query($link,$sql_survey_output_chk);
$count_chk=mysqli_num_rows($res_survey_output_hk);
if($count_chk > 0){
    $menu_id="RA514";
}else{
    $menu_id="RA515";
}

$sql_survey_output_chkc = "SELECT survey_id,row_id,value FROM survey_output where survey_id = '".$suid."'";
//echo $sql_survey_output_chk;

$res_sc = mysqli_query($link,$sql_survey_output_chkc);
$count_c=mysqli_num_rows($res_sc);


$sql_survey_input = "SELECT display_name,row_id FROM survey_input_lead WHERE menu_id = '".$menu_id."' AND acedns='Y' AND type<>'menu' ORDER BY display_order ASC";

$rsdis=mysqli_query($link,$sql_survey_input);
$count=mysqli_num_rows($rsdis);
if($count_c > 0){
while($rowdis = mysqli_fetch_assoc($rsdis)){
		$row_id = $rowdis['row_id'];
	
		
		$sql_survey_output = "SELECT survey_id,row_id,value FROM survey_output where survey_id = '".$suid."' AND row_id = '".$row_id."'";
		
		$res_survey_output = mysqli_query($link,$sql_survey_output);
		$row_survey_output = mysqli_fetch_assoc($res_survey_output);
        $value = $row_survey_output['value'];
        $display_name=$rowdis['display_name'];

        if($row_id=="RA486" || $row_id=="RA516"){
    		$commaList = explode(';', $value);
    	    $value = $commaList[0];
    	}
    	if($row_id=="RA531" || $row_id=="RA501"){
    	    $sql_emp = "SELECT emp_name FROM employee_master where emp_code = '".$row_id."'";
		$res_emp = mysqli_query($link,$sql_emp);
		$emp_count=mysqli_num_rows($res_emp);
		if($emp_count > 0){
		    $row_emp = mysqli_fetch_assoc($res_emp);
            $value = $row_emp['emp_name'];
		}
		
    	}

    
	$valuesurveydetails  = (($display_name!='')?$display_name: ' ')."^";
	$valuesurveydetails  .= (($value!='')?$value: ' ')."";
	
	
	$linecontents  .= $valuesurveydetails."\n";


}
$linesurveydetails = '';


 $contentsrowcolumn=$count.'¥'.'2';
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