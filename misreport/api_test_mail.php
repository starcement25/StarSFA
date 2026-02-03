<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'include/PHPMailer/src/PHPMailer.php';
require 'include/PHPMailer/src/SMTP.php';
require 'include/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// echo 7878;
// die;
try {
    $mail = new PHPMailer(true); // `true` enables exception mode

    // SMTP settings
    $mail->isSMTP();
    $mail->SMTPDebug  = 2;                  // 0 = off, 1 = commands, 2 = data and response
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'test.sbinfowaves@gmail.com';  // your email
    $mail->Password   = 'dzltchhdafyfnqhh';            // Gmail app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Sender & Recipient
    $mail->setFrom('starsaathi@starcement.co.in', 'Starsaathi');
    $mail->addAddress('souvik@yopmail.com');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email';
    $mail->Body    = 'This is a <b>test email</b> sent using PHPMailer v6+';

    $mail->send();
    echo '✅ Email sent successfully!';
} catch (Exception $e) {
    echo "❌ Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
