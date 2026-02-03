<?php
$access_token=$POST['access_token'];
$access_token='eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJtb2JpbGVOdW1iZXIiOiI5ODcxNzA5NjgwIiwiaWF0IjoxNjcwOTM0MzIwLCJleHAiOjE2NzA5Mzc5MjB9.W_L4U5tyNKds4xjn397gCPeH46BSiqFEy-QbMnVjj_0g09w6YOrRnstYgdc4wTfpUh2VAy4A-0qU8BQ6LlIBew';
$url_ck1 = 'https://hellotournament.pokermoogley.com:5051/gameMgmt/verifyAtk';

$useragent = $_SERVER['HTTP_USER_AGENT'];
$headr = array();
//$headr[] = 'Content-length: 0';
$headr[] = 'Content-type: application/json';
$headr[] = 'Authorization: LEpClrF9zbfxFm58';
$payload = json_encode( array( "access_token"=> $access_token ) );
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