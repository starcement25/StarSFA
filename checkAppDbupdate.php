<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

$deviceId=$_POST['deviceId'];
$versionCode=$_POST['versionCode'];
$emp_code=$_REQUEST['emp_code'];
//$deviceId='865676022575600';
//$versionCode='5.2.8.7';
//$sqlquery="select * from employee_master";

$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));

$contents =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second;

$sqlselectappversion="SELECT * FROM app_version";
$rsselectappversion=mysqli_query($link,$sqlselectappversion);
$rowselectappversion=mysqli_fetch_assoc($rsselectappversion);
$app_version_latest=$rowselectappversion['version_code'];
$release_date=date('d/m/Y',strtotime($rowselectappversion['date']));
/*$countappversion=mysqli_num_rows($rsselectappversion);
if($countappversion>0)
{*/
	$sqlselect="SELECT * FROM app_updation  WHERE device_id='".$deviceId."'";
	$rsselect=mysqli_query($link,$sqlselect);
	$count=mysqli_num_rows($rsselect);
	$rowselect=mysqli_fetch_assoc($rsselect);
	
	if($count>0)
	{
		if($versionCode > $rowselect['version_code'] )
		{
			$sqlUpdate="UPDATE app_updation SET version_code='".$versionCode."',is_update='0' WHERE device_id='".$deviceId."'";
			mysqli_query($link,$sqlUpdate);
			
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
			
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			$update_date=$date.'/'.$month.'/'.$year;
			$update_time=$hour.':'.$minute.':'.$second;
			
			if($nick_name=='RUPA')
			{
				$app_update_mail='';
			}
			else
			{
				$app_update_mail=ORDEREMAILRECIPENTS;
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
			$is_update='0';
		}
		if($rowselect['version_code']==$versionCode && $rowselect['is_update']==1)
		{
			$is_update='1';
		}
		else if($versionCode < $rowselect['version_code']){ 																																																																																										
			$sqlUpdate="UPDATE app_updation SET version_code='".$versionCode."',
						is_update='0' WHERE device_id='".$deviceId."'";
			if(mysqli_query($link,$sqlUpdate))
			{
				$is_update='0';	
			}
		 }
		else
		{
			$is_update='0';
		}
	}
	else
	{
		$is_update='0';
	}
	$sqlselectversion="SELECT version_code  FROM db_version ";
	$rsselectversion=mysqli_query($link,$sqlselectversion,$link);
	$rowselectversion=mysqli_fetch_assoc($rsselectversion);
	$versionCodedb=$rowselectversion['version_code'];
	
	$sqlselect="SELECT * FROM table_structure_updation  WHERE device_id='".$device_id."' and emp_code='".$emp_code."'";
	$rsselect=mysqli_query($link,$sqlselect);
	$countdbupdate=mysqli_num_rows($rsselect);
	
	if($countdbupdate >0)
	{
		$rowselect=mysqli_fetch_assoc($rsselect);
		$is_update_existing_db=$rowselect['is_update'];
		$user_db_version_code=$rowselect['db_version_code'];
		
		if($is_update_existing_db==1)
		{
			$is_db_update='1';
		}
	}
	else
	{
		$is_db_update='0';
	}

	if($is_update=='1' && $is_db_update=='0')
	{
		$response='1'.'-'.$app_version_latest;
	}
	else if($is_update=='1' && $is_db_update=='1')
	{
		$response='3'.'-'.$app_version_latest;
	}
	else if($is_update=='0' && $is_db_update=='1')
	{
		$response='2';
	}
	else
	{
		$response='0';
	}
	echo $contents.'#'.$response;

   $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/checkAppDbupdate.php?nick_name=$nick_name&emp_code=$emp_code&deviceId=$deviceId&versionCode=$versionCode";
insertapilog($datetime,$emp_code,$url,$nick_name);
?>
