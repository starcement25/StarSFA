<?php	
    define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_ARCHITA");
	//define("DB","acedns_ARCHITA");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	
	$hour=gmdate('H',strtotime('+330 minute'));
	$minute=gmdate('i',strtotime('+330 minute'));
	$second=gmdate('s',strtotime('+330 minute'));
	$location_date=$year.$month.$date.$hour.$minute.$second;

	$sqlselemphighlevel="SELECT emp_code,emp_name FROM employee_master WHERE emp_code IN(SELECT DISTINCT reporting_to FROM employee_master)";
	$rsselemphighlevel=mysqli_query($link,$sqlselemphighlevel);
	while($rowselemphighlevel=mysqli_fetch_assoc($rsselemphighlevel))
	{
		$emp_code=$rowselemphighlevel['emp_code'];
		$emp_name=$rowselemphighlevel['emp_name'];
		$notification_id='PN'.strtoupper($emp_code).$location_date;
		$notification_type='manager_activity';

		$apiKey='AAAAdCu4Fjw:APA91bHHl7RnWyOj4Pb42NBuPfJZQAkOlmxKCoGL9flYk8xfhsqMY7_YtOtBKXPHNgx5szKyD2T1HriSFZ5NHmcBu874v9uCym0VEQlpNYbuUOjHBUmaVvtIXXYu-FOjhuwUksA4Geob';
		$collapseKey=rand();
		$sqlregdetails="SELECT registrationid FROM changepassword WHERE emp_code='".$emp_code."'";
		$rsregdetails=mysqli_query($link,$sqlregdetails);
		$rowregdetails=mysqli_fetch_assoc($rsregdetails);
		$registrationid=$rowregdetails['registrationid'];
		//Title of the Notification.
		$title = "";
		//$message='There is an update please login to your APP & Press the notification button';
		$message="Manager review Notification @ $hour:$minute";
		//Creating the notification array.
		$notification = array('title' =>$title , 'body' => $message);
		
		//This array contains, the token and the notification. The 'to' attribute stores the token.
		$data= 
array('notification_id' =>$notification_id, 'notification_type' => $notification_type, 'sender_id' => 'CRONJOB', 'body' => $message); 
		//$arrayToSend = array('to' => $registrationid, 'notification' => $notification, 'data'=>$data);
		$arrayToSend = array('to' => $registrationid, 'data'=>$data);
		
        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = array(
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayToSend));
        // Execute post
        $result = curl_exec($ch);
        /*if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }*/
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($httpCode != 200) {    
			//request failed    
			$successval=0; 
		} 
		else
		{
			$successval=1;	
		}
        // Close connection
        curl_close($ch);
		if($successval==1)
		 {
				$sqlnotificationmaster  = "INSERT INTO notification_master ";
				$sqlnotificationmaster .= " SET notification_id='".$notification_id."'";
				$sqlnotificationmaster .= " ,type_of_notification='".$notification_type."'";
				$sqlnotificationmaster .= " ,sender_id='CRONJOB'";
				$sqlnotificationmaster .= " ,message='".$message."'";
				$sqlnotificationmaster .= " ,transferred='YES'";
				mysqli_query($link,$sqlnotificationmaster) or die(mysqli_error()." Error in notificatin insertion.");
				$sqlnotification  = "INSERT INTO notification_ack_relation ";
				$sqlnotification .= " SET notification_id='".$notification_id."'";
				$sqlnotification .= " ,receiver_id='".$emp_code."'";
				mysqli_query($link,$sqlnotification) or die(mysqli_error()." Error in notification insertion.");
		 }
	}
?>	
