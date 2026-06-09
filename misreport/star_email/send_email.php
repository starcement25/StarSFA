<?php


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__.'/vendor/autoload.php';

function send_the_mail($to_email, $subject, $bodyml){
//error_reporting(E_STRICT);
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");
require __DIR__.'/PHPMailer-test/src/PHPMailer.php';
require __DIR__.'/PHPMailer-test/src/SMTP.php';
require __DIR__.'/PHPMailer-test/src/Exception.php';
    
    $sts = "FALSE";
    $to_email_arr = array();
    $to_email = $to_email ? trim($to_email) : "";
    $subject = $subject ? trim($subject) : "";
    $bodyml = $bodyml ? trim($bodyml) : "";
    if ($to_email != "" && $subject != "" && $bodyml != "") {
        $to_email_arr = explode(",", $to_email);
        if (count($to_email_arr) > 0) {
//$mail = new PHPMailer(true);
$mail = new PHPMailer;
try {
    // SMTP configuration
   $mail->isSMTP();
    //$mail->SMTPDebug=2;
    $mail->Host = "cloudmail2.up99plus.com";
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->Username = "starcement@cloudmail.up99plus.com";
    $mail->Password = "Nh26sjqgWk"; 
    // $mail->SMTPSecure = 'tls'; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
    // $mail->SMTPSecure = false; // Explicit TLS encryption is not used
    $mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
    // Sender information


    // Sender information
    $mail->setFrom('starcement@cloudmail.up99plus.com', 'Star Cement');


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
       // $mail->addAddress("satyajitm@coral.com");

    // Content
    /*$mail->isHTML(true);
    $mail->Subject = 'Testing Mail from SFA';
    $mail->Body = 'Mail body';
    $mail->AltBody = 'Plain text version of the email';

    // Send the email
    $mail->send();*/
    $sts = "TRUE";
} catch (Exception $e) {
    echo $e;
    $sts = "FALSE";
}

        }
    }
    return $sts;
}

$to_email = "satyajitm@coral.in,coolsatya7662@gmail.com,satyajitmridha66@gmail.com";
$subject = "Star SFA - Complaint Report";
$bodyml = "Test Mail Body from Star SFA";
$resml = send_the_mail($to_email, $subject, $bodyml);
echo $resml;


?>