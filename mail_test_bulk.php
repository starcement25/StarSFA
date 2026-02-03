<?php
error_reporting(E_STRICT);
set_time_limit(0);
/*date_default_timezone_set("Asia/Kolkata");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;	
function send_the_mail($to_email,$subject,$bodyml){

//require_once('class.phpmailer.php');
//require_once('class.smtp.php');
require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';	

$sts = "FALSE";
$to_email_arr = array();
$to_email = $to_email ? trim($to_email) : "";
$subject = $subject ? trim($subject) : "";
$bodyml = $bodyml ? trim($bodyml) : "";
if($to_email!="" && $subject!="" && $bodyml!=""){
$to_email_arr = explode(",",$to_email);
if(count($to_email_arr)>0){
$mail             = new PHPMailer();
$bodyml             = $bodyml;
$mail->IsSMTP(); // telling the class to use SMTP
$mail->SMTPDebug = 1; 				   // enables SMTP debug information (for testing)
//$mail->SMTPKeepAlive = true;	
$mail->SMTPAuth   = true;                  // enable SMTP authentication
$mail->SMTPSecure = "tls";                 // sets the prefix to the servier
$mail->Host       = "cloudmail2.up99plus.com"; 		// sets GMAIL as the SMTP server (For gmail "mail.coral.in")
$mail->Port       = 587;     				// set the SMTP port for the GMAIL server (For gmail 465 )
$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
$mail->Username   = "starcement@cloudmail.up99plus.com";
$mail->Password   = "K2TTvLxATyULV2um";
$mail->SetFrom('starcement@cloudmail.up99plus.com', 'Starsaathi');
$mail->Subject    = $subject;
$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
$mail->MsgHTML($bodyml);
foreach($to_email_arr as $to_email_arr_val){
	if(trim($to_email_arr_val)!=""){
	if (filter_var(trim($to_email_arr_val), FILTER_VALIDATE_EMAIL)) {
		$mail->AddAddress(trim($to_email_arr_val), $to_email_arr_val);
	}
	}
}
$mlsts = $mail->Send();
if(!$mlsts) {
  $sts = "FALSE";
} else {
 $sts = "TRUE";
}
// show all phpmailer configurations
/*echo "<pre>";
print_r($mail);
echo "</pre>";*/
	//$mail->SmtpClose();
/*}
}
return $sts;
}*/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
function send_the_mail($to_email, $subject, $bodyml){
error_reporting(E_STRICT);
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");
require __DIR__.'/phpmailer_new/PHPMailer.php';
require __DIR__.'/phpmailer_new/SMTP.php';
require __DIR__.'/phpmailer_new/Exception.php';
    
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
   $mail->SMTPSecure = "tls";                 // sets the prefix to the servier
$mail->Host       = "cloudmail2.up99plus.com"; 		// sets GMAIL as the SMTP server (For gmail "mail.coral.in")

    //$mail->Port = 25;
	$mail->Port       = 587;     				// set the SMTP port for the GMAIL server (For gmail 465 )
$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
    $mail->SMTPAuth = true;
    $mail->Username = "starcement@cloudmail.up99plus.com";
    $mail->Password = "K2TTvLxATyULV2um"; // Insert your actual password here
    //$mail->SMTPSecure = false; // Explicit TLS encryption is not used
    //$mail->SMTPAutoTLS = false; // Disable automatic TLS upgrade
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
    //$mail->Body = $bodyml;
	$mail->MsgHTML($bodyml);
    $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
    // Send the email
    $mail->send();    
    $sts = "TRUE";
} catch (Exception $e) {
    $sts = "FALSE";
}
echo "<pre>";
print_r($mail);
echo "</pre>";

        }
    }
    return $sts;
}
//$to_email = "suranjitd@coral.in";
//$to_email = "manishranjan@starcement.co.in";
// $to_email = "dipankarc@coral.in";
$to_email = "dipankarc@coral.in";
$subject = "Test Subject STAR SFA";
$bodyml = "Test Message STAR SFA";
$start_time = date("Y-m-d H:i:s");
$mres = send_the_mail($to_email,$subject,$bodyml);
$end_time = date("Y-m-d H:i:s");
echo $mres;
// show start and end time
echo "<br>Start Time : ".$start_time;
echo "<br>End Time : ".$end_time;
// total time taken
$start_time = strtotime($start_time);
$end_time = strtotime($end_time);
$total_time = $end_time - $start_time;
echo "<br>Total Time : ".$total_time." Seconds";
//echo $order_date = date("Y-m-d H:i:s");
?>