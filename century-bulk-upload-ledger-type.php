<?php
//Update ledger type
$urlledgertype="https://centurycareapp.com/update_ledger_type.php";
	$chledgertype = curl_init();
	curl_setopt($chledgertype, CURLOPT_URL, $urlledgertype);
	curl_setopt($chledgertype, CURLOPT_TIMEOUT, 100);
	curl_setopt($chledgertype, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($chledgertype, CURLOPT_RETURNTRANSFER, 1);
$resultledgertype=curl_exec($chledgertype);

$urlsms="https://centurycareapp.com/send_os_sms_against_due_date.php";
	$chsms = curl_init();
	curl_setopt($chsms, CURLOPT_URL, $urlsms);
	curl_setopt($chsms, CURLOPT_TIMEOUT, 100);
	curl_setopt($chsms, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($chsms, CURLOPT_RETURNTRANSFER, 1);
$resultsms=curl_exec($chsms);

$urlnotification="https://centurycareapp.com/send_os_pn_against_due_date.php";
	$chnotification = curl_init();
	curl_setopt($chnotification, CURLOPT_URL, $urlnotification);
	curl_setopt($chnotification, CURLOPT_TIMEOUT, 100);
	curl_setopt($chnotification, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($chnotification, CURLOPT_RETURNTRANSFER, 1);
$resultnotification=curl_exec($chnotification);


	echo 'SUCCESS';
?>	