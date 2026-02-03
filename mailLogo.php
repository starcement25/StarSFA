<?php
define("FROMEMAIL","acedns@coral.in");
	define("FROMTAG","aceDNS");
	define("BCCEMAIL","acedns@coral.in");
$email_to='dipankarc@coral.in';
	$mailsubj="AceDns Logo";
	$messagestringmail="<HTML><BODY><a href='http://www.coralindia.com/dev/acednsproduct/app_update/ACEdns.apk'><img src='http://www.coralindia.com/dev/acednsproduct/logo/acednslogo.png' border='0'></img></a></BODY></HTML>";
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=UTF-8\n";
	$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
				"Reply-To:".FROMEMAIL." \r\n" .
				"Bcc: ".BCCEMAIL." \r\n".
				'X-Mailer: PHP/' . phpversion();
	@mail($email_to, $mailsubj, $messagestringmail, $headers);
?>

