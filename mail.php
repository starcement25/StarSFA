<?php
require_once('class.phpmailer.php');
require_once('class.smtp.php');

$mail = new PHPMailer();

$mail->IsSMTP(); // telling the class to use SMTP
//$mail->Host       = "mail.forcepower.in"; // SMTP server (For gmail "mail.coral.in")
$mail->SMTPDebug  = "1";                     // enables SMTP debug information (for testing)
//$mail->Mailer = "smtp";                                           // 1 = errors and messages
                                           // 2 = messages only
$mail->SMTPAuth   = TRUE;                  // enable SMTP authentication
$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
//$mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server (For gmail "smtp.gmail.com")
$mail->Host       = "vps06.indiax.com";      
$mail->Port       = 587;                   // set the SMTP port for the GMAIL server (For gmail 465 )
$mail->Username   = "mail@acedns.in";  // GMAIL username
//$mail->Password   = "K)9UhsypBKOC";            // GMAIL password coral
$mail->Password   = "coral";            // GMAIL password coral
$subject='test nmail';
$bodyml='test nmail';

$mail->SetFrom('mail@acedns.in', 'Force Power');
$mail->Subject    = $subject;
$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
$mail->MsgHTML($bodyml);

//foreach($to_email_arr as $to_email_arr_val){
$mail->AddAddress("dipankarc@coral.in","Dipankar");
//}
$mlsts = $mail->Send();
if(!$mlsts) {
  echo "Mailer Error:
  
   " . $mail->ErrorInfo;
  $sts = "FALSE";
} else {
 echo $sts = "TRUE";
}		 