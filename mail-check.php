<?php 
	/*require("include/config-email-setup.php");
	$date=gmdate('d',strtotime('+329 minute'));
	$month=gmdate('m',strtotime('+329 minute'));
	$year=gmdate('Y',strtotime('+329 minute'));

	$hour=gmdate('H',strtotime('+329 minute'));
	$minute=gmdate('i',strtotime('+329 minute'));
	$second=gmdate('s',strtotime('+329 minute'));
	$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;  
   	//$email   ='export@tttextiles.com'; 
	//$subject ='TT mail checking again on'.$location_date;
	$email   ='dipankarc@coral.in'; 
	$subject ='Testing checkbox in mail';
	
	/*$message="<html><head><title>Order approval</title></head>
				<body>
				<form name='orderapproval' action='http://www.acedns.in/acednsproduct/update-order.php' method='POST'>
				<table wiidth='100%' border=1 style=background-color:AliceBlue>
				<tr>
				<th style='width:350px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
				CE'>Order Approval</span></strong></th>
				</tr>
				<tr>
					<td style='width:350px;text-align:center;min-height:91px;background-color:white;padding-left:10 px;'>
						<input type='radio' name='approval' value='approved' /> Approved
						<input type='radio' name='approval' value='notapproved' /> Not Approved
						
						<input type='submit' name='Submit' value='save' />
					</td>
				</tr>
				</table></form></body></html>";  */
	/*$message='testing';			 

			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=UTF-8\n";
			/*$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
						"Reply-To:".FROMEMAIL." \r\n" .
						'X-Mailer: PHP/' . phpversion();*/
	//echo $message;

	/*$flgSend=mail($email, $subject, $message, $headers);
	if($flgSend)
		{
			echo "success.";
		}
		else
		{
			echo "failure.";
		}*/
/*use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';*/
require_once('class.phpmailer.php');
require_once('class.smtp.php');

$mail = new PHPMailer();

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

/*$mail->isSMTP();                                      // Set mailer to use SMTP
//$mail->Host = 'mail.acedns.in';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'info@salesmpower.acedns.in';                 // SMTP username
$mail->Password = 'DyxiY0)R).@A';                           // SMTP password
//$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 587;                                    // TCP port to connect to

$mail->From = 'info@salesmpower.acedns.in';
$mail->FromName = 'Mailer';
$mail->addAddress('dipankarc@coral.in', 'Dipankar');     // Add a recipient
//$mail->addReplyTo('info@example.com', 'Information');
//$mail->addCC('cc@example.com');
//$mail->addBCC('acedns@coral.in');

//$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'Here is the subject for testing1';
$mail->Body    = 'This is the HTML message body for testing<b>in bold!</b>';
//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}*/
//$mail = new PHPMailer();
/*$mail->IsSMTP();
$mail->Mailer = "smtp";

//$mail->SMTPDebug  = 1;  
$mail->SMTPAuth   = TRUE;
$mail->SMTPSecure = 'tls';
//$mail->Host = 'smtp.gmail.com';
$mail->Port = 587;
//or more succinctly:
$mail->Host = 'smtp.gmail.com';
$mail->Username   = "dipankarc@coral.in";
$mail->Password   = "dipankarc1234";
//$mail->AddAddress("dipsome2006@gmail.com", "Dipankar");
//$mail->SetFrom("kkd@forcepower.in", "KKD");
//$mail->AddReplyTo("reply-to-email@domain", "reply-to-name");
//$mail->AddCC("cc-recipient-email@domain", "cc-recipient-name");
//$mail->Subject = "Test mail";
//$mail->IsHTML(true);
/*$mail->AddAddress("dipsome2006@gmail.com", "Dipankar");
$mail->SetFrom("kkd@forcepower.in", "KKD");
//$mail->AddReplyTo("reply-to-email@domain", "reply-to-name");
//$mail->AddCC("cc-recipient-email@domain", "cc-recipient-name");
$mail->Subject = "Test is Test Email sent via Gmail SMTP Server using PHP Mailer";*/
//$content = "<b>This is a Test Email sent via Gmail SMTP Server using PHP mailer class.</b>";

//$mail->MsgHTML($content); 
/*if(!$mail->Send()) {
  echo "Error while sending Email.";
  var_dump($mail);
} else {
  echo "Email sent successfully";
}*/
$mail->IsSMTP(); // telling the class to use SMTP
//$mail->Host       = "mail.forcepower.in"; // SMTP server (For gmail "mail.coral.in")
$mail->SMTPDebug  = "1";                     // enables SMTP debug information (for testing)
//$mail->Mailer = "smtp";                                           // 1 = errors and messages
                                           // 2 = messages only
$mail->SMTPAuth   = TRUE;                  // enable SMTP authentication
$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
//$mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server (For gmail "smtp.gmail.com")
$mail->Host       = "vps06.indiax.com";      
$mail->Port       = 465;                   // set the SMTP port for the GMAIL server (For gmail 465 )
$mail->Username   = "mail@acedns.in";  // GMAIL username
$mail->Password   = "K)9UhsypBKOC";            // GMAIL password
$subject='test nmail';
$bodyml='test nmail';

$mail->SetFrom('info@salesmpower.acedns.in', 'Force Power');
$mail->Subject    = $subject;
$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
$mail->MsgHTML($bodyml);

//foreach($to_email_arr as $to_email_arr_val){
$mail->AddAddress("dipankarc@coral.in","Dipankar");
//}
$mlsts = $mail->Send();
if(!$mlsts) {
  echo "Mailer Error: " . $mail->ErrorInfo;
  $sts = "FALSE";
} else {
 echo $sts = "TRUE";
}		 
?>