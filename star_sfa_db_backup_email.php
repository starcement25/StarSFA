<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__.'/PHPMailer-test/src/PHPMailer.php';
require_once __DIR__.'/PHPMailer-test/src/SMTP.php';
require_once __DIR__.'/PHPMailer-test/src/Exception.php';
require 'vendor/autoload.php'; // Include Composer's autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// Retrieve POST data
//$toMail = $_POST['toMail'] ?? '';
$subject = $_POST['subject'] ?? '';
$body = $_POST['body'] ?? '';
$attachment = $_FILES['attachment'] ?? null;

$toMail ='centralcell@starcement.co.in';
$response = [
    'status' => 'error',
    'message' => 'Something went wrong'
];

if (empty($toMail) || empty($subject) || empty($body)) {
    $response['message'] = 'toMail, subject, and body are required';
    echo json_encode($response);
    exit();
}

// Setup PHPMailer
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    // $mail->Username   = 'acedns@coral.in'; // Your Gmail address
    // $mail->Password   = 'acedns12345'; // Your Gmail password or App Password
     $mail->Username   = 'emovesfa@gmail.com'; 
    $mail->Password   = 'koewplnyitgnzhrl'; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    // Enable verbose debug output
    // $mail->SMTPDebug = 2; // Debug output level (0 = off, 1 = client messages, 2 = client and server messages)
    $mail->Debugoutput = function($str, $level) {
        echo "Debug level $level; message: $str\n";
    };
    // Recipients
    //$mail->setFrom('acedns@coral.in', 'ACEDNS-STAR');
     $mail->setFrom('emovesfa@gmail.com', 'ACEDNS-STAR');
    $mail->addAddress($toMail); // Add a recipient

    // Attachments
    if ($attachment) {
        $mail->addAttachment($attachment['tmp_name'], $attachment['name']);
    }

    // Content
    $mail->isHTML(true); 
    $mail->Subject = $subject;
    $mail->Body    = $body;

    $mail->send();

    $response['status'] = 'success';
    $response['message'] = 'Email sent successfully';

} catch (Exception $e) {
    $response['message'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

echo json_encode($response);
