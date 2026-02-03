<?php

//pending_orders upload - 4 hours
$urlpending = "https://centurycareapp.com/upload_pending_orders.php";
$chpending = curl_init();
curl_setopt($chpending, CURLOPT_URL, $urlpending);
curl_setopt($chpending, CURLOPT_TIMEOUT, 100);
curl_setopt($chpending, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($chpending, CURLOPT_RETURNTRANSFER, 1);
$resultpendingorders = curl_exec($chpending);
?>
