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

//$nick_name=$_POST['nick_name'];
$sqlquery="SELECT * FROM user_details WHERE nick_name='".$nick_name."'";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time;
		$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
		while($rowsuserdetails = mysqli_fetch_assoc($result))
		{
				$contents.="<data>";
				$contents .='<user_id><![CDATA['.mb_convert_encoding($rowsuserdetails['user_id'], 'UTF-8', 'UTF-8').']]></user_id>
							<name><![CDATA['.mb_convert_encoding($rowsuserdetails['name'], 'UTF-8', 'UTF-8').']]></name>
							<address><![CDATA['.mb_convert_encoding($rowsuserdetails['address'], 'UTF-8', 'UTF-8').']]></address>
							<phone_no><![CDATA['.mb_convert_encoding($rowsuserdetails['phone_no'], 'UTF-8', 'UTF-8').']]></phone_no>
							<email><![CDATA['.mb_convert_encoding($rowsuserdetails['email'], 'UTF-8', 'UTF-8').']]></email>
							<license_key><![CDATA['.mb_convert_encoding($rowsuserdetails['license_key'], 'UTF-8', 'UTF-8').']]></license_key>
							<no_users><![CDATA['.mb_convert_encoding($rowsuserdetails['no_users'], 'UTF-8', 'UTF-8').']]></no_users>
							<nick_name><![CDATA['.mb_convert_encoding($rowsuserdetails['nick_name'], 'UTF-8', 'UTF-8').']]></nick_name>
							<no_of_branches><![CDATA['.mb_convert_encoding($rowsuserdetails['no_of_branches'], 'UTF-8', 'UTF-8').']]></no_of_branches>
							<email_hierarchywise><![CDATA['.mb_convert_encoding($rowsuserdetails['email_hierarchywise'], 'UTF-8', 'UTF-8').']]></email_hierarchywise>
							<vertical_fields><![CDATA['.mb_convert_encoding($rowsuserdetails['vertical_fields'], 'UTF-8', 'UTF-8').']]></vertical_fields>
							<vertical_fields_value><![CDATA['.mb_convert_encoding($rowsuserdetails['vertical_fields_value'], 'UTF-8', 'UTF-8').']]></vertical_fields_value>
							<previous_stock><![CDATA['.mb_convert_encoding($rowsuserdetails['previous_stock'], 'UTF-8', 'UTF-8').']]></previous_stock>
							<last_update_time><![CDATA['.mb_convert_encoding($contentsdatetime, 'UTF-8', 'UTF-8').']]></last_update_time>
							<multiple_prospect><![CDATA['.mb_convert_encoding($rowsuserdetails['multiple_prospect'], 'UTF-8', 'UTF-8').']]></multiple_prospect>
							<multiple_prospect_value><![CDATA['.mb_convert_encoding($rowsuserdetails['multiple_prospect_value'], 'UTF-8', 'UTF-8').']]></multiple_prospect_value>
							<stock_audit_scan><![CDATA['.mb_convert_encoding($rowsuserdetails['stock_audit_scan'], 'UTF-8', 'UTF-8').']]></stock_audit_scan>
							<stock_audit_rate><![CDATA['.mb_convert_encoding($rowsuserdetails['stock_audit_rate'], 'UTF-8', 'UTF-8').']]></stock_audit_rate>
							<location_drag_drop ><![CDATA['.mb_convert_encoding($rowsuserdetails['location_drag_drop'], 'UTF-8', 'UTF-8').']]></location_drag_drop>
							';
				$contents.="</data>";
				//echo $cnt++;
		}
		$contents .= "</recordset>";
		
		$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
		$url = APICALLLOGURL."/user-details-incremental-6.0.1.php?nick_name=$nick_name&
last_update_time=$last_update_time&incremental_download=$incremental_download&mode=$mode";
		insertapilog($datetime,$emp_code,$url,$nick_name);
		
		/*$config = 'api_calllog.txt';
		$file=fopen($config,"r+");
		$date = date("F j, Y");
		$time = date("H:i:s");
		$newuser ="[$date $time]"."http://www.coralindia.com/dev/acednsproduct/user-details-incremental-6.0.0.php?nick_name=$nick_name&last_update_time=$last_update_time&incremental_download=$incremental_download&mode=$mode"."\r\n";
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
	}
	else
	{
		echo '0';
	}
	mysqli_close($link);
?>
