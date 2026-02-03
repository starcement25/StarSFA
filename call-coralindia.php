<?php

$Url = "http://coralindia.com/coralsupport/api/xxxxx.php"; // asmx URL of WSDL
						
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $Url);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
	curl_setopt($ch, CURLOPT_TIMEOUT, 400);
	$response = curl_exec($ch);
	//$info = curl_getinfo($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	//$err = curl_error($ch);  
	curl_close($ch);
?>						