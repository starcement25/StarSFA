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

$sqlquery="SELECT * FROM OTP_menu_details WHERE nick_name='".$nick_name."'";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
	if($count>0){
		//$date=date('Y-m-d');
		//$time=date('H:i:s');
		//$contentsdatetime = $date.'€'.$time;
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
			
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowsmenudetails = mysqli_fetch_assoc($result))
		{
				$contents.="<data>";
				$contents .='<menu_id><![CDATA['.mb_convert_encoding($rowsmenudetails['menu_id'], 'UTF-8', 'UTF-8').']]></menu_id>
							<user_id><![CDATA['.mb_convert_encoding($rowsmenudetails['user_id'], 'UTF-8', 'UTF-8').']]></user_id>
							<attendance><![CDATA['.mb_convert_encoding($rowsmenudetails['attendance'], 'UTF-8', 'UTF-8').']]></attendance>
							<activity_report><![CDATA['.mb_convert_encoding($rowsmenudetails['activity_report'], 'UTF-8', 'UTF-8').']]></activity_report>
							<transporter><![CDATA['.mb_convert_encoding($rowsmenudetails['transporter'], 'UTF-8', 'UTF-8').']]></transporter>
							<gate_keeper1><![CDATA['.mb_convert_encoding($rowsmenudetails['gate_keeper1'], 'UTF-8', 'UTF-8').']]></gate_keeper1>
							<despatch_in><![CDATA['.mb_convert_encoding($rowsmenudetails['despatch_in'], 'UTF-8', 'UTF-8').']]></despatch_in>
							<gate_keeper2><![CDATA['.mb_convert_encoding($rowsmenudetails['gate_keeper2'], 'UTF-8', 'UTF-8').']]></gate_keeper2>
							<weighbridge_in><![CDATA['.mb_convert_encoding($rowsmenudetails['weighbridge_in'], 'UTF-8', 'UTF-8').']]></weighbridge_in>
							<loading><![CDATA['.mb_convert_encoding($rowsmenudetails['loading'], 'UTF-8', 'UTF-8').']]></loading>
							<weighbridge_out><![CDATA['.mb_convert_encoding($rowsmenudetails['weighbridge_out'], 'UTF-8', 'UTF-8').']]></weighbridge_out>
							<despatch_out><![CDATA['.mb_convert_encoding($rowsmenudetails['despatch_out'], 'UTF-8', 'UTF-8').']]></despatch_out>
							<gate_keeper2_out><![CDATA['.mb_convert_encoding($rowsmenudetails['gate_keeper2_out'], 'UTF-8', 'UTF-8').']]></gate_keeper2_out>
							<security_out><![CDATA['.mb_convert_encoding($rowsmenudetails['security_out'], 'UTF-8', 'UTF-8').']]></security_out>
							<gate_keeper1_out><![CDATA['.mb_convert_encoding($rowsmenudetails['gate_keeper1_out'], 'UTF-8', 'UTF-8').']]></gate_keeper1_out>
							<last_update_time><![CDATA['.mb_convert_encoding($contentsdatetime, 'UTF-8', 'UTF-8').']]></last_update_time>
							';
				$contents.="</data>";
				//echo $cnt++;
		}
	}
	$contents .= "</recordset>";	
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/OTP-menu-details-incremental.php?nick_name=$nick_name&last_update_time=$last_update_time&incremental_download=$incremental_download&mode=$mode";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	echo $contents;
	mysqli_close($link);
?>