<?php
	ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");
	require("include/config-email-setup.php");

	$emp_code=$_REQUEST['emp_code'];
	$date_time=$_REQUEST['downloaddate'];
	$date_time=str_replace('_',' ',$date_time);
	$device_id=$_REQUEST['device_id'];
	$days_first_login=$_REQUEST['days_first_login'] ?? '';
	$db_version=$_REQUEST['db_version'];
	
	function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
	{
	  // convert from degrees to radians
	  $latFrom = deg2rad($latitudeFrom);
	  $lonFrom = deg2rad($longitudeFrom);
	  $latTo = deg2rad($latitudeTo);
	  $lonTo = deg2rad($longitudeTo);
	
	  $latDelta = $latTo - $latFrom;
	  $lonDelta = $lonTo - $lonFrom;
	  $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
		cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
	  return $angle * $earthRadius;
	}
	function getReverseGeo($latitude,$longitude)
	{
		// format this string with the appropriate latitude longitude
		$url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true";
		// make the HTTP request
		$data = @file_get_contents($url);
		// parse the json response
		$jsondata = json_decode($data,true);
		
		//print_r($jsondata);
		// if we get a formatted_address array and the status was OK, get the addres
		if(is_array($jsondata )&& $jsondata['status']=='OK')
		{
			  $addr = $jsondata['results']['0']['formatted_address'];
		}		
		return  $addr;	
	}
	function updateLatlong($emp_code)
	{
		//For check and update in the location table
		$sqlselectlatlong="SELECT * FROM location WHERE emp_code='".$emp_code."' AND latt='0' AND longi='0'";
		$rsselectlatlong=mysqli_query($link,$sqlselectlatlong) or die(mysqli_error()." Error in select zero latt longi: ".$sqlselectlatlong);
		$countselectlatlong=mysqli_num_rows($rsselectlatlong);
		
		if($countselectlatlong>0)
		{
			$sqllastlatlong="SELECT latt,longi FROM location WHERE emp_code='".$emp_code."' AND latt<>'0' AND longi<>'0' ORDER BY date DESC LIMIT 0,1";
			$rslastlatlong=mysqli_query($link,$sqllastlatlong) or die(mysqli_error()." Error in select last not zero latt longi: ".$sqllastlatlong);
			$rowlastlatlong=mysqli_fetch_assoc($rslastlatlong);
			$lastlatt=$rowlastlatlong['latt'];
			$lastlongi=$rowlastlatlong['longi'];
			
			$sqlupdatelatlong="UPDATE location set latt='".$lastlatt."',longi='".$lastlongi."' WHERE emp_code='".$emp_code."' AND latt='0' AND longi='0'";
			$rsupdatelatlong=mysqli_query($link,$sqlupdatelatlong) or die(mysqli_error()." Error in update zero latt longi: ".$sqlupdatelatlong);
		}
	}
	/*function fetch_corresponding_emails($operation_type,$area,$branch_code)
	{
		$correspondingemails='';
		
		$sqlfetchemails="SELECT mail_id FROM mail_access WHERE FIND_IN_SET( '".$branch_code."', branch_code ) >0 AND 
						FIND_IN_SET( '".$operation_type."', attributes ) >0 AND FIND_IN_SET( '".$area."', area ) >0";
		$rsfetchemails=mysqli_query($link,$sqlfetchemails) or die(mysqli_error().'Error in mail id fetch.');
		while($rowfetchemails=mysqli_fetch_assoc($rsfetchemails))
		{
			$correspondingemails=$correspondingemails.$rowfetchemails['mail_id'].',';
		}
		$correspondingemails=substr($correspondingemails,0,-1);
		return $correspondingemails;
	}*/
	$sqlempname="SELECT emp_name,branch_code,vertical_value,reporting_to FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsempname=mysqli_query($link,$sqlempname);
	$rowempname=mysqli_fetch_assoc($rsempname);
	$emp_name=$rowempname['emp_name'];
	$branch_code=$rowempname['branch_code'];
	$vertical_value=$rowempname['vertical_value'];
	$reporting_to_immediate=$rowempname['reporting_to'];
	
	$sqlselectversion="SELECT version_code,date  FROM db_version ";
	$rsselectversion=mysqli_query($link,$sqlselectversion);
	$rowselectversion=mysqli_fetch_assoc($rsselectversion);
	$versionCode=$rowselectversion['version_code'];
	$release_date=date('d/m/Y',strtotime($rowselectversion['date']));

	$sqlselect="SELECT is_update,db_version_code FROM table_structure_updation  WHERE device_id='".$device_id."' and emp_code='".$emp_code."'";
	$rsselect=mysqli_query($link,$sqlselect);
	$count=mysqli_num_rows($rsselect);
	if($count>0)
	{
		$rowselect=mysqli_fetch_assoc($rsselect);
		$is_update=$rowselect['is_update'];
		$existed_db_version_code=$rowselect['db_version_code'];
		if($is_update==1){
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
			if(email_hierarchywise=='yes')
			{
			 if(email_hierarchy_level==1)
		     {
				$db_update_mail='';
			 }
			 else
			 {
				 $db_update_mail=ORDEREMAILRECIPENTS;
			 }
			}
			else
			{
				$db_update_mail=ORDEREMAILRECIPENTS;
			}

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
			$sendmail=@mail($db_update_mail, $mailsubj, $emailbody, $headers,'-facedns@acedns.in');
		  }
		}	
	}
	else
	{
		$sqlInsert="INSERT INTO table_structure_updation SET
				    emp_code='".$emp_code."',
					db_version_code='".$versionCode."',
					device_id='".$device_id."',
					is_update='0'";
		mysqli_query($link,$sqlInsert);
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/updateFirstloginDatetime-6.0.2.php?nick_name=$nick_name&emp_code=$emp_code&date_time=$date_time&device_id=$device_id&days_first_login=$days_first_login&db_version=$db_version";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/updateFirstloginDatetime-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&date_time=$date_time&device_id=$device_id&days_first_login=$days_first_login&db_version=$db_version"."\r\n";
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
	mysqli_close($link);
?>
