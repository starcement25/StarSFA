<?php
 /*ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);*/
 
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
//require("include/config-email-setup.php");

$deviceId=$_REQUEST['deviceId'];
$versionCode=$_REQUEST['versionCode'];
$emp_code=$_REQUEST['emp_code'];
//$deviceId='911530150537029';
//$versionCode='5.4.5.6';
//$sqlquery="select * from employee_master";
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = APICALLLOGURL."/updateApp-6.0.2.php?nick_name=$nick_name&emp_code=$emp_code&deviceId=$deviceId&versionCode=$versionCode";
insertapilog($datetime,$emp_code,$url,$nick_name);

$sqlempname="SELECT emp_name,vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
$rsempname=mysqli_query($link,$sqlempname);
$rowempname=mysqli_fetch_assoc($rsempname);
$emp_name=$rowempname['emp_name'];
$vertical_value=$rowempname['vertical_value'];

if(vertical_fields=='yes' && (strtoupper($_REQUEST['nick_name'])=='EMAMI' || strtoupper($_REQUEST['nick_name'])=='EMAMIT')){
	$sqlselectappversion="SELECT * FROM app_version WHERE vertical_value='".$vertical_value."'";
}
else{
	$sqlselectappversion="SELECT * FROM app_version";
}
$rsselectappversion=mysqli_query($link,$sqlselectappversion);
$rowselectappversion=mysqli_fetch_assoc($rsselectappversion);
$app_version_latest=$rowselectappversion['version_code'];
$release_date=date('d/m/Y',strtotime($rowselectappversion['date']));
/*$countappversion=mysqli_num_rows($rsselectappversion);
if($countappversion>0)
{*/
	$sqlselect="SELECT * FROM app_updation WHERE device_id='".$deviceId."'";
	$rsselect=mysqli_query($link,$sqlselect);
	$count=mysqli_num_rows($rsselect);
	$rowselect=mysqli_fetch_assoc($rsselect);
	if($count>0)
	{
		if($versionCode > $rowselect['version_code'])
		{
			$sqlUpdate="UPDATE app_updation SET
						version_code='".$versionCode."',
						is_update='0'
						WHERE device_id='".$deviceId."'";
			if(mysqli_query($link,$sqlUpdate))
			{
				echo "2";
			}
			else
			{
				echo "3";
			}
			$date=gmdate('d',strtotime('+329 minute'));
			$month=gmdate('m',strtotime('+329 minute'));
			$year=gmdate('Y',strtotime('+329 minute'));
			
			$hour=gmdate('H',strtotime('+329 minute'));
			$minute=gmdate('i',strtotime('+329 minute'));
			$second=gmdate('s',strtotime('+329 minute'));
			$update_date=$date.'/'.$month.'/'.$year;
			$update_time=$hour.':'.$minute.':'.$second;
			
			if($nick_name=='RUPA')
			{
				$app_update_mail='';
			}
			else
			{
			  if(email_hierarchywise=='yes')
			  {
			   if(email_hierarchy_level==1)
		       {
				 $app_update_mail='';
			   }
			   else
			   {
				 $app_update_mail=ORDEREMAILRECIPENTS;
			   }
			  }
			  else
			  {
				$app_update_mail=ORDEREMAILRECIPENTS;
			  }
				//$app_update_mail=ORDEREMAILRECIPENTS;
			}
			$mailsubj="ACEdns - ".strtoupper($nick_name)." APP Version $versionCode released on $release_date has been successfully updated to $emp_name";
			$emailbody="<html><head><title>App Updation</title></head>
						<body>ACEdns - ".strtoupper($nick_name)." APP Version $versionCode released on $release_date has been successfully updated to $emp_name - $emp_code on $update_date and @ $update_time.</table><br /><br />Regards<br />
TEAM - ACEdns</body></html>";
			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=UTF-8\n";
			$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
							"Reply-To:".FROMEMAIL." \r\n" .
							"Bcc: ".BCCEMAIL." \r\n".
							'X-Mailer: PHP/' . phpversion();
			$sendmail=@mail($app_update_mail, $mailsubj, $emailbody, $headers,'-facedns@acedns.in');

		}
		if($rowselect['version_code']==$versionCode && $rowselect['is_update']==1)
		{
			echo "1-".$app_version_latest;
		}
		else if($versionCode < $rowselect['version_code']){ 																																																																																										
			echo "1-".$app_version_latest;
			}
		else
		{
			echo "0";
		}
	}
	else
	{
		$sqlInsert="INSERT INTO app_updation SET
					version_code='".$versionCode."',
					device_id='".$deviceId."',
					is_update='0'";
		if(mysqli_query($link,$sqlInsert))
		{
			echo "4".'/'.$app_version_latest;
		}
		else
		{
			echo "3";
		}
	}
//}
/*else
{
	echo "0";
}*/
	/*$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/updateApp-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);*/
mysqli_close($link);
?>
