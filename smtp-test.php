<?php
// check error
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__.'/PHPMailer-test/src/PHPMailer.php';
require __DIR__.'/PHPMailer-test/src/SMTP.php';
require __DIR__.'/PHPMailer-test/src/Exception.php';

$mail = new PHPMailer;

require __DIR__.'/vendor/autoload.php';


try {
    $mail->isSMTP();
    $mail->SMTPDebug=2;
    $mail->Host = "cloudmail2.up99plus.com";
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->Username = "starcement@cloudmail.up99plus.com";
    $mail->Password = "K2TTvLxATyULV2um"; 
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
    $mail->setFrom('starcement@cloudmail.up99plus.com', 'Star Cement');

    // Recipient
    $mail->addAddress("satyajitm@coral.in");

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Testing Mail';
    $mail->Body = 'Mail body';
    $mail->AltBody = 'Plain text version of the email';

    // Send the email
    $mail->send();

    // Print mail details
    echo "<pre>";
    print_r($mail);
    echo "</pre>";

    // check for success
    if ($mail->ErrorInfo) {
        echo "<script>alert('Mail could not be sent. Error: {$mail->ErrorInfo}');</script>";
    } else {
        echo "<script>alert('Mail sent successfully');</script>";
    }
    

    // echo "<script>alert('Mail sent successfully');</script>";
} catch (Exception $e) {
    echo "<script>alert('Mail could not be sent. Error: {$mail->ErrorInfo}');</script>";
}
?>