<?php
$mobileNumber=$POST['mobileNumber'];
$authToken=$POST['authToken'];
$mobileNumber='9871709680';
$authToken='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJutih';

$url_ck1 = 'https://hellotournament.pokermoogley.com:5051/verifyUserGame';

$useragent = $_SERVER['HTTP_USER_AGENT'];
$headr = array();
//$headr[] = 'Content-length: 0';
$headr[] = 'Content-type: application/json';
$headr[] = 'Authorization: LEpClrF9zbfxFm58';
$payload = json_encode( array( "mobileNumber"=> $mobileNumber,"authToken"=> $authToken ) );
$ch_sheader = curl_init();
curl_setopt($ch_sheader, CURLOPT_URL,$url_ck1);
curl_setopt($ch_sheader, CURLOPT_HTTPHEADER,$headr);
curl_setopt($ch_sheader, CURLOPT_POST,true);
curl_setopt( $ch_sheader, CURLOPT_POSTFIELDS, $payload );
curl_setopt($ch_sheader, CURLOPT_RETURNTRANSFER,true);
//curl_setopt($ch_sheader, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch_sheader, CURLOPT_USERAGENT, $useragent);
$body_for_mcode = curl_exec($ch_sheader);
//echo "<pre>";
$info = curl_getinfo($ch_sheader);
//print_r($info);
curl_close($ch_sheader);
//return $body_for_mcode;
print_r($body_for_mcode);




?>