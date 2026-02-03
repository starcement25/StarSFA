<?php
require("include/config.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}

$sqlquery="SELECT * FROM survey_form_details WHERE nick_name='".$nick_name."'";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time;
		while($rowssurveyformdetails = mysqli_fetch_assoc($result))
		{
			$contents.="<data>";
			$contents .='<survey_form_id><![CDATA['.mb_convert_encoding($rowssurveyformdetails['survey_form_id'], 'UTF-8', 'UTF-8').']]></survey_form_id>
						<user_id><![CDATA['.mb_convert_encoding($rowssurveyformdetails['user_id'], 'UTF-8', 'UTF-8').']]></user_id>
						<survey_menu><![CDATA['.mb_convert_encoding($rowssurveyformdetails['survey_menu'], 'UTF-8', 'UTF-8').']]></survey_menu>
						<survey_type><![CDATA['.mb_convert_encoding($rowssurveyformdetails['survey_type'], 'UTF-8', 'UTF-8').']]></survey_type>
						<survey_type_details><![CDATA['.mb_convert_encoding($rowssurveyformdetails['survey_type_details'], 'UTF-8', 'UTF-8').']]></survey_type_details>
						<mall_survey_relation><![CDATA['.mb_convert_encoding($rowssurveyformdetails['mall_survey_relation'], 'UTF-8', 'UTF-8').']]></mall_survey_relation>';	
			$contents.="</data>";
			//echo $cnt++;
		}
	}
	$contents .= "</recordset>";
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/survey-form-details-incremental-6.0.0.php?nick_name=$nick_name&last_update_time=$last_update_time&incremental_download=$incremental_download&mode=$mode";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/survey-form-details-incremental.php?nick_name=$nick_name&last_update_time=$last_update_time&incremental_download=$incremental_download&mode=$mode"."\r\n";
	$insertPos=0;  // variable for saving //Users position
	while (!feof($file)) {
		$line=fgets($file);
		if (strpos($line, 'http://')!==false) {
			$insertPos=ftell($file);
			$newline =  $newuser;
		}
		else
		{
			$newline.=$line;   // append existing data with new data of user
		}
	}
	fseek($file,$insertPos);   // move pointer to the file position where we saved above 
	fwrite($file, $newline);
	fclose($file);*/
				
	echo $contents;		
?>
