<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
function send_the_mail($to_email, $subject, $bodyml){
//error_reporting(E_STRICT);
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");
require __DIR__.'/phpmailer/PHPMailer.php';
require __DIR__.'/phpmailer/SMTP.php';
require __DIR__.'/phpmailer/Exception.php';
    
    $sts = "FALSE";
    $to_email_arr = array();
    $to_email = $to_email ? trim($to_email) : "";
    $subject = $subject ? trim($subject) : "";
    $bodyml = $bodyml ? trim($bodyml) : "";
    if ($to_email != "" && $subject != "" && $bodyml != "") {
        $to_email_arr = explode(",", $to_email);
        if (count($to_email_arr) > 0) {
$mail = new PHPMailer(true);
try {
    // SMTP configuration
    $mail->isSMTP();
    $mail->Host = "cloudmail2.up99plus.com";
    $mail->Port = 25;
    $mail->SMTPAuth = true;
    $mail->Username = "starcement@cloudmail.up99plus.com";
    $mail->Password = "K2TTvLxATyULV2um"; // Insert your actual password here
    $mail->SMTPSecure = false; // Explicit TLS encryption is not used
    $mail->SMTPAutoTLS = true; // Disable automatic TLS upgrade
    // Sender information
    $mail->setFrom('starcement@cloudmail.up99plus.com', 'Starsaathi');
    // Recipient
	foreach ($to_email_arr as $to_email_arr_val) {
	if (trim($to_email_arr_val) != "") {
	if (filter_var(trim($to_email_arr_val), FILTER_VALIDATE_EMAIL)) {
	$mail->addAddress(trim($to_email_arr_val), $to_email_arr_val);
	}
	}
	}
			
    // Content
    $mail->isHTML(true);
    $mail->Subject = $subject;
	$mail->MsgHTML($bodyml);
    $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
    // Send the email
    $mail->send();    
    $sts = "TRUE";
} catch (Exception $e) {
    echo $e;
    $sts = "FALSE";
}

        }
    }
    return $sts;
}

$to_email = "satyajitm@coral.in";
$subject = "Test Mail Subject from Star Saathi";
$bodyml = "Test Mail Body from Star Saathi";
$resml = send_the_mail($to_email, $subject, $bodyml);
echo $resml;
?>
