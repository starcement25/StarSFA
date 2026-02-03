<?php
// define("SERVER","localhost");
// define("USER","root");
// define("PASSWORD","Passw0rd123#$");
// //require("include/config-setup.php");
// define("DB","acedns_STAR");
// //define("DB","acedns_ARCHITA");
// $link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
// mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
require_once("sfa_connection.php");

$localDB = new sfa_connection();
$link = $localDB->conn;

$date = gmdate('d', strtotime('+330 minute'));
$month = gmdate('m', strtotime('+330 minute'));
$year = gmdate('Y', strtotime('+330 minute'));

$hour = gmdate('H', strtotime('+330 minute'));
$minute = gmdate('i', strtotime('+330 minute'));
$second = gmdate('s', strtotime('+330 minute'));
$location_date = $year . $month . $date . $hour . $minute . $second;

$sqlselemp = "SELECT emp_code,registrationid,birthday_customer,anniversary_customer FROM birth_anniversary_customer_list";
$rsselemp = mysqli_query($link, $sqlselemp);
while ($rowselemp = mysqli_fetch_assoc($rsselemp)) {
	$emp_code = $rowselemp['emp_code'];
	$registrationid = $rowselemp['registrationid'];
	$birthday_customer = $rowselemp['birthday_customer'];
	$birthday_customer_array = explode(",", $birthday_customer);
	$anniversary_customer = $rowselemp['anniversary_customer'];
	$anniversary_customer_array = explode(",", $anniversary_customer);

	$apiKey = 'AAAAUCNxsT8:APA91bE-MLw1RwEHr7sZrKRmRiipBv1NnW-p6Gz1ajW_vOESL7jM3Wx0Rd0z5sUJoEM3DVqo_OS_kN0aQwXt1v1aciNzAir0FAlbCkefWvGFTDjYI-Dm7mTELSwgwCoo9zhqnHEtBkE6';
	$collapseKey = rand();
	$sqlregdetails = "SELECT registrationid FROM changepassword WHERE emp_code='" . $emp_code . "'";
	$rsregdetails = mysqli_query($link, $sqlregdetails);
	$rowregdetails = mysqli_fetch_assoc($rsregdetails);
	$registrationid = $rowregdetails['registrationid'];
	//Title of the Notification.
	$title = "";
	if ($birthday_customer != '') {
		$notification_id = 'PN' . strtoupper($emp_code) . $location_date;
		$notification_type = 'birthday_notification';
		$birthdaymessage = "";

		foreach ($birthday_customer_array as $birthday_customer_val) {
			$sqlcustomername = "SELECT customer_name,cust_type FROM customer_master WHERE dns_customer_code='" . $birthday_customer_val . "'";
			$rscustomername = mysqli_query($link, $sqlcustomername);
			$rowcustomername = mysqli_fetch_assoc($rscustomername);
			$customer_name = $rowcustomername['customer_name'];
			$cust_type = $rowcustomername['cust_type'];

			$birthdaymessage .= ucfirst($cust_type) . " Code - $birthday_customer_val, " . ucfirst($cust_type) . " Name - $customer_name is having Birthday Today.<br /><br />";
		}
		//echo $birthdaymessage;
		//Creating the notification array.
		$notification = array('title' => $title, 'body' => $birthdaymessage);

		//This array contains, the token and the notification. The 'to' attribute stores the token.
		$data =
			array('notification_id' => $notification_id, 'notification_type' => $notification_type, 'sender_id' => 'CRONJOB', 'body' => $birthdaymessage);
		//$arrayToSend = array('to' => $registrationid, 'notification' => $notification, 'data'=>$data);
		$arrayToSend = array('to' => $registrationid, 'data' => $data);

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
			$successval = 0;
		} else {
			$successval = 1;
		}
		// Close connection
		curl_close($ch);
		if ($successval == 1) {
			$sqlnotificationmaster  = "INSERT INTO notification_master ";
			$sqlnotificationmaster .= " SET notification_id='" . $notification_id . "'";
			$sqlnotificationmaster .= " ,type_of_notification='" . $notification_type . "'";
			$sqlnotificationmaster .= " ,sender_id='CRONJOB'";
			$sqlnotificationmaster .= " ,message='" . $birthdaymessage . "'";
			$sqlnotificationmaster .= " ,transferred='YES'";
			mysqli_query($link, $sqlnotificationmaster) or die(mysqli_error() . " Error in notificatin insertion.");
			$sqlnotification  = "INSERT INTO notification_ack_relation ";
			$sqlnotification .= " SET notification_id='" . $notification_id . "'";
			$sqlnotification .= " ,receiver_id='" . $emp_code . "'";
			mysqli_query($link, $sqlnotification) or die(mysqli_error() . " Error in notification insertion.");
			$sqlupdate = "UPDATE birth_anniversary_customer_list SET is_notified='yes',notified_datetime=CURRENT_TIMESTAMP()
							WHERE emp_code='" . $emp_code . "'";
			mysqli_query($link, $sqlupdate) or die(mysqli_error() . " Error in notification update.");
		}
	}
	if ($anniversary_customer != '') {
		$notification_id = 'PN' . strtoupper($emp_code) . $location_date;
		$notification_type = 'anniversary_notification';
		$anniversarymessage = "";

		foreach ($anniversary_customer_array as $anniversary_customer_val) {
			$sqlcustomername = "SELECT customer_name,cust_type FROM customer_master WHERE dns_customer_code='" . $anniversary_customer_val . "'";
			$rscustomername = mysqli_query($link, $sqlcustomername);
			$rowcustomername = mysqli_fetch_assoc($rscustomername);
			$customer_name = $rowcustomername['customer_name'];
			$cust_type = $rowcustomername['cust_type'];
			$anniversarymessage .= ucfirst($cust_type) . " Code - $anniversary_customer_val, " . ucfirst($cust_type) . " Name - $customer_name is having Anniversary Today.<br /><br />";
		}
		//Creating the notification array.
		$notification = array('title' => $title, 'body' => $anniversarymessage);

		//This array contains, the token and the notification. The 'to' attribute stores the token.
		$data =
			array('notification_id' => $notification_id, 'notification_type' => $notification_type, 'sender_id' => 'CRONJOB', 'body' => $anniversarymessage);
		//$arrayToSend = array('to' => $registrationid, 'notification' => $notification, 'data'=>$data);
		$arrayToSend = array('to' => $registrationid, 'data' => $data);

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
			$successval = 0;
			
		} else {
			$successval = 1;
			
		}
		
		// Close connection
		curl_close($ch);
		if ($successval == 1) {
			$sqlnotificationmaster  = "INSERT INTO notification_master ";
			$sqlnotificationmaster .= " SET notification_id='" . $notification_id . "'";
			$sqlnotificationmaster .= " ,type_of_notification='" . $notification_type . "'";
			$sqlnotificationmaster .= " ,sender_id='CRONJOB'";
			$sqlnotificationmaster .= " ,message='" . $anniversarymessage . "'";
			$sqlnotificationmaster .= " ,transferred='YES'";
			
			mysqli_query($link, $sqlnotificationmaster) or die(mysqli_error() . " Error in notificatin insertion.");
			$sqlnotification  = "INSERT INTO notification_ack_relation ";
			$sqlnotification .= " SET notification_id='" . $notification_id . "'";
			$sqlnotification .= " ,receiver_id='" . $emp_code . "'";
			mysqli_query($link, $sqlnotification) or die(mysqli_error() . " Error in notification insertion.");
			$sqlupdate = "UPDATE birth_anniversary_customer_list SET is_notified='yes',notified_datetime=CURRENT_TIMESTAMP()
							WHERE emp_code='" . $emp_code . "'";
			mysqli_query($link, $sqlupdate) or die(mysqli_error() . " Error in notification update.");
		}
	}
	
}

echo "SUCCESS";

mysqli_close($link);
