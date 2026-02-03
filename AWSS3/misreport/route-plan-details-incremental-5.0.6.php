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

$sqlquery="SELECT * FROM route_plan_details WHERE nick_name='".$nick_name."'";
$result = mysql_query($sqlquery);
$count=mysql_num_rows($result);
	$cnt=1;
	$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time;
		while($rowrouteplandetails = mysql_fetch_array($result))
		{
				$contents.="<data>";
				$contents .='<route_plan_id><![CDATA['.mb_convert_encoding($rowrouteplandetails['route_plan_id'], 'UTF-8', 'UTF-8').']]></route_plan_id>
							<user_id><![CDATA['.mb_convert_encoding($rowrouteplandetails['user_id'], 'UTF-8', 'UTF-8').']]></user_id>
							<route_plan_access_period><![CDATA['.mb_convert_encoding($rowrouteplandetails['route_plan_access_period'], 'UTF-8', 'UTF-8').']]></route_plan_access_period>
							<route_plan_deviation><![CDATA['.mb_convert_encoding($rowrouteplandetails['route_plan_deviation'], 'UTF-8', 'UTF-8').']]></route_plan_deviation>
							<route_plan_approval><![CDATA['.mb_convert_encoding($rowrouteplandetails['route_plan_approval'], 'UTF-8', 'UTF-8').']]></route_plan_approval>
							<route_plan_flow><![CDATA['.mb_convert_encoding($rowrouteplandetails['route_plan_flow'], 'UTF-8', 'UTF-8').']]></route_plan_flow>
							<last_update_time><![CDATA['.mb_convert_encoding($contentsdatetime, 'UTF-8', 'UTF-8').']]></last_update_time>
							';
				$contents.="</data>";
				//echo $cnt++;
		}
	}
	$contents .= "</recordset>";	
	
	$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/route-plan-details-incremental-5.0.6.php?nick_name=$nick_name&last_update_time=$last_update_time&incremental_download=$incremental_download&mode=$mode"."\r\n";
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
	fclose($file);
			
	echo $contents;		
?>
