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

$sqlquery="SELECT * FROM market_feedback_details WHERE nick_name='".$nick_name."'";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time;
		while($rowsmarketfeedbackdetails = mysqli_fetch_assoc($result))
		{
			$contents.="<data>";
			$contents .='<market_feedback_id><![CDATA['.mb_convert_encoding($rowsmarketfeedbackdetails['market_feedback_id'], 'UTF-8', 'UTF-8').']]></market_feedback_id>
						<user_id><![CDATA['.mb_convert_encoding($rowsmarketfeedbackdetails['user_id'], 'UTF-8', 'UTF-8').']]></user_id>
						<mf_group_enable><![CDATA['.mb_convert_encoding($rowsmarketfeedbackdetails['mf_group_enable'], 'UTF-8', 'UTF-8').']]></mf_group_enable>
						<mf_col1><![CDATA['.mb_convert_encoding($rowsmarketfeedbackdetails['mf_col1'], 'UTF-8', 'UTF-8').']]></mf_col1>
						<mf_col2><![CDATA['.mb_convert_encoding($rowsmarketfeedbackdetails['mf_col2'], 'UTF-8', 'UTF-8').']]></mf_col2>
						<mf_col3><![CDATA['.mb_convert_encoding($rowsmarketfeedbackdetails['mf_col3'], 'UTF-8', 'UTF-8').']]></mf_col3>
						<mf_col4><![CDATA['.mb_convert_encoding($rowsmarketfeedbackdetails['mf_col4'], 'UTF-8', 'UTF-8').']]></mf_col4>
						<mf_sub_menu_details><![CDATA['.mb_convert_encoding($rowsmarketfeedbackdetails['mf_sub_menu_details'], 'UTF-8', 'UTF-8').']]></mf_sub_menu_details>
					<mf_sub_menu_image><![CDATA['.mb_convert_encoding($rowsmarketfeedbackdetails['mf_sub_menu_image'], 'UTF-8', 'UTF-8').']]></mf_sub_menu_image>
					<mf_secondary_sales_option><![CDATA['.mb_convert_encoding($rowsmarketfeedbackdetails['mf_secondary_sales_option'], 'UTF-8', 'UTF-8').']]></mf_secondary_sales_option>	
					<mf_tagging><![CDATA['.mb_convert_encoding($rowsmarketfeedbackdetails['mf_tagging'], 'UTF-8', 'UTF-8').']]></mf_tagging>
					<mf_customer_branchwise><![CDATA['.mb_convert_encoding($rowsmarketfeedbackdetails['mf_customer_branchwise'], 'UTF-8', 'UTF-8').']]></mf_customer_branchwise>
					';	
			$contents.="</data>";
			//echo $cnt++;
		}
	}
	$contents .= "</recordset>";
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/market-feedback-details-incremental-6.0.4.php?nick_name=$nick_name&last_update_time=$last_update_time&incremental_download=$incremental_download&mode=$mode";
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
	mysqli_close($link);		
?>
