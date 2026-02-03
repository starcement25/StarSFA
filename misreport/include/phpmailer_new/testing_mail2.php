<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
function send_the_mail($to_email, $subject, $bodyml){
require __DIR__.'/phpmailer/PHPMailer.php';
require __DIR__.'/phpmailer/SMTP.php';
require __DIR__.'/phpmailer/Exception.php';
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
    $mail->SMTPAutoTLS = false; // Disable automatic TLS upgrade
    // Sender information
    $mail->setFrom('starcement@cloudmail.up99plus.com', 'Star Cement');
    // Recipient
    $mail->addAddress("suranjitd@coral.in","suranjitd@coral.in");
    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Testing Mail';
    $mail->Body = 'Mail body';
    $mail->AltBody = 'Plain text version of the email';
    // Send the email
    $mail->send();
    
    echo "<script>alert('Mail sent successfully');</script>";
} catch (Exception $e) {
    echo "<script>alert('Mail could not be sent. Error: {$mail->ErrorInfo}');</script>";
}

}

$to_email = "suranjitd@coral.in";
$subject = "Test Mail Subject from Star Saathi";
$bodyml = "Test Mail Body from Star Saathi";
$resml = send_the_mail($to_email, $subject, $bodyml);
echo $resml;

?>
