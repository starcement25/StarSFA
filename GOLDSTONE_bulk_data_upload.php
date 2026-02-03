<?php
//PUT SO files
$urlSO="https://dealers.blacktigercement.com/put_FTP_files_SO.php";
	$chSO = curl_init();
	curl_setopt($chSO, CURLOPT_URL, $urlSO);
	curl_setopt($chSO, CURLOPT_TIMEOUT, 100);
	curl_setopt($chSO, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($chSO, CURLOPT_RETURNTRANSFER, 1);
echo $resultSO=curl_exec($chSO);
//Read SO files
$urlReadSO="https://dealers.blacktigercement.com/read_FTP_files_SO.php";
	$chReadSO = curl_init();
	curl_setopt($chReadSO, CURLOPT_URL, $urlReadSO);
	curl_setopt($chReadSO, CURLOPT_TIMEOUT, 100);
	curl_setopt($chReadSO, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($chReadSO, CURLOPT_RETURNTRANSFER, 1);
echo $resultReadSO=curl_exec($chReadSO);
$urlReadcustomer="https://dealers.blacktigercement.com/upload_customer_files_data.php";
	$chReadcustomer = curl_init();
	curl_setopt($chReadcustomer, CURLOPT_URL, $urlReadcustomer);
	curl_setopt($chReadcustomer, CURLOPT_TIMEOUT, 100);
	curl_setopt($chReadcustomer, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($chReadcustomer, CURLOPT_RETURNTRANSFER, 1);
echo $resultReadcustomer=curl_exec($chReadcustomer);
$urlReadsaleregister="https://dealers.blacktigercement.com/upload_sale_register_data.php";
	$chReadsaleregister = curl_init();
	curl_setopt($chReadsaleregister, CURLOPT_URL, $urlReadsaleregister);
	curl_setopt($chReadsaleregister, CURLOPT_TIMEOUT, 100);
	curl_setopt($chReadsaleregister, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($chReadsaleregister, CURLOPT_RETURNTRANSFER, 1);
echo $resultReadsaleregister=curl_exec($chReadsaleregister);
$urlReadledger="https://dealers.blacktigercement.com/upload_ledger_files_data.php";
	$chReadledger = curl_init();
	curl_setopt($chReadledger, CURLOPT_URL, $urlReadledger);
	curl_setopt($chReadledger, CURLOPT_TIMEOUT, 100);
	curl_setopt($chReadledger, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($chReadledger, CURLOPT_RETURNTRANSFER, 1);
echo $resultReadledger=curl_exec($chReadledger);
$urlReadoffline="https://dealers.blacktigercement.com/offline_order_data_sync.php";
	$chReadoffline = curl_init();
	curl_setopt($chReadoffline, CURLOPT_URL, $urlReadoffline);
	curl_setopt($chReadoffline, CURLOPT_TIMEOUT, 100);
	curl_setopt($chReadoffline, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($chReadoffline, CURLOPT_RETURNTRANSFER, 1);
echo $resultReadoffline=curl_exec($chReadoffline);
?>	