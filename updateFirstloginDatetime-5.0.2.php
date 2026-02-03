<?php
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");
	require("include/config-email-setup.php");

	$emp_code=$_REQUEST['emp_code'];
	$date_time=$_REQUEST['downloaddate'];
	$date_time=str_replace('_',' ',$date_time);
	$device_id=$_REQUEST['device_id'];
	$days_first_login=$_REQUEST['days_first_login'];
	$db_version=$_REQUEST['db_version'];
	
	$sqlempname="SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsempname=mysqli_query($link,$sqlempname);
	$rowempname=mysqli_fetch_assoc($rsempname);
	$emp_name=$rowempname['emp_name'];

	if($days_first_login=='yes')
	{
		$sqlselect="SELECT loggedin_date_time FROM changepassword  WHERE emp_code='".$emp_code."'";
		$rsselect=mysqli_query($link,$sqlselect);
		$count=mysqli_num_rows($rsselect);
		$rowselect=mysqli_fetch_assoc($rsselect);
		
		$loggedin_date_time_database=substr($rowselect['loggedin_date_time'],0,10);
		$loggedin_date_time_request=substr($date_time,0,10);
		
		if($count>0 && $loggedin_date_time_database!=$loggedin_date_time_request)
		{
			$sqlUpdate="UPDATE changepassword SET
						loggedin_date_time='".$date_time."'
						WHERE emp_code='".$emp_code."'";
			if(mysqli_query($link,$sqlUpdate))
			{
				echo "1";
			}
			else
			{
				echo "0";
			}
		}
		else
		{
			echo "0";
		}
	}
	$sqlselect="SELECT is_update,db_version_code FROM table_structure_updation  WHERE device_id='".$device_id."' and emp_code='".$emp_code."'";
	$rsselect=mysqli_query($link,$sqlselect);
	$count=mysqli_num_rows($rsselect);
		
	if($count>0)
	{
		$rowselect=mysqli_fetch_assoc($rsselect);
		$is_update=$rowselect['is_update'];
		$existed_db_version_code=$rowselect['db_version_code'];
		if($is_update==1){
			$sqlselectversion="SELECT version_code,date  FROM db_version ";
			$rsselectversion=mysqli_query($link,$sqlselectversion);
			$rowselectversion=mysqli_fetch_assoc($rsselectversion);
			$versionCode=$rowselectversion['version_code'];
			$release_date=date('d/m/Y',strtotime($rowselectversion['date']));
			
			if($versionCode==$db_version)
			{
			$sqlUpdatetablestructure="UPDATE table_structure_updation SET
									db_version_code='".$versionCode."',
									is_update='0'
									WHERE device_id='".$device_id."' AND emp_code='".$emp_code."'";
			mysqli_query($link,$sqlUpdatetablestructure);
			
			$date=gmdate('d',strtotime('+329 minute'));
			$month=gmdate('m',strtotime('+329 minute'));
			$year=gmdate('Y',strtotime('+329 minute'));
			
			$hour=gmdate('H',strtotime('+329 minute'));
			$minute=gmdate('i',strtotime('+329 minute'));
			$second=gmdate('s',strtotime('+329 minute'));
			$update_date=$date.'/'.$month.'/'.$year;
			$update_time=$hour.':'.$minute.':'.$second;

			$mailsubj="ACEdns - ".strtoupper($nick_name)." DB Version $versionCode released on $release_date has been successfully updated to $emp_name";
			$emailbody="<html><head><title>Db Updation</title></head>
						<body>ACEdns - ".strtoupper($nick_name)." DB Version $versionCode released on $release_date has been successfully updated to $emp_name - $emp_code on $update_date @ $update_time.</table><br /><br />Regards<br />
TEAM - ACEdns</body></html>";
			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=UTF-8\n";
			$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
							"Reply-To:".FROMEMAIL." \r\n" .
							"Bcc: ".BCCEMAIL." \r\n".
							'X-Mailer: PHP/' . phpversion();
			$sendmail=@mail(ORDEREMAILRECIPENTS, $mailsubj, $emailbody, $headers,'-facedns@acedns.in');
		  }
		}	
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/updateFirstloginDatetime-5.0.2.php?nick_name=$nick_name&emp_code=$emp_code&date_time=$date_time&device_id=$device_id&days_first_login=$days_first_login&db_version=$db_version";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/updateFirstloginDatetime-5.0.2.php?nick_name=$nick_name&emp_code=$emp_code&date_time=$date_time&device_id=$device_id&days_first_login=$days_first_login&db_version=$db_version"."\r\n";
	$insertPos=0;  // variable for saving 
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
	
?>
