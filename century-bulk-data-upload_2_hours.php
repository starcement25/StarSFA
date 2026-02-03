<?php

//pending_orders upload - 2 hours

/*$urlpending = "https://centurycareapp.com/upload_pending_orders.php";
$chpending = curl_init();
curl_setopt($chpending, CURLOPT_URL, $urlpending);
curl_setopt($chpending, CURLOPT_TIMEOUT, 100);
curl_setopt($chpending, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($chpending, CURLOPT_RETURNTRANSFER, 1);
$resultpendingorders = curl_exec($chpending);*/

//primary sales upload - 2 Hours
$urlprimary = "https://centurycareapp.com/upload_primary_sales_data.php";
$chprimary = curl_init();
curl_setopt($chprimary, CURLOPT_URL, $urlprimary);
curl_setopt($chprimary, CURLOPT_TIMEOUT, 100);
curl_setopt($chprimary, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($chprimary, CURLOPT_RETURNTRANSFER, 1);
$resultprimary = curl_exec($chprimary);

$urlprimarypdf = "https://centurycareapp.com/upload_primary_sales_data_modified.php";
$chprimarypdf = curl_init();
curl_setopt($chprimarypdf, CURLOPT_URL, $urlprimarypdf);
curl_setopt($chprimarypdf, CURLOPT_TIMEOUT, 100);
curl_setopt($chprimarypdf, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($chprimarypdf, CURLOPT_RETURNTRANSFER, 1);
$resultprimarypdf = curl_exec($chprimarypdf);

//Dealers data upload - 2 Hours
$urldealer = "https://centurycareapp.com/upload_dealer_files_data.php";
$chdealer = curl_init();
curl_setopt($chdealer, CURLOPT_URL, $urldealer);
curl_setopt($chdealer, CURLOPT_TIMEOUT, 100);
curl_setopt($chdealer, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($chdealer, CURLOPT_RETURNTRANSFER, 1);
$resultdealer = curl_exec($chdealer);
//Upload ledger files - 2 Hours
$urlledger = "https://centurycareapp.com/upload_ledger_balance_data.php";
$chledger = curl_init();
curl_setopt($chledger, CURLOPT_URL, $urlledger);
curl_setopt($chledger, CURLOPT_TIMEOUT, 100);
curl_setopt($chledger, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($chledger, CURLOPT_RETURNTRANSFER, 1);
$resultledger = curl_exec($chledger);

$subject = "CENTURY sync done on " . $location_date . ' hrs.';
$mailbody = "<html><head><title>Century Sync</title></head>
					<body>Sync done on Primary Sales, Dealer Data, Ledger Details
<br /><br /><table align='left'>
							<tr>
								<td><strong>Primary Sales: $resultprimary </strong></td>
							</tr>
							<tr>
								<td><strong>Dealer Data: $resultdealer </strong></td>
							</tr>
							<tr>
								<td><strong>Ledger Details: $resultledger </strong></td>
							</tr>

							<tr>
								<td><strong>Pending Order Details: N/A </strong></td>
							</tr>

						</table> </body></html>";
$sendto = 'manik.paul@centuryply.com,chandan.das@centuryply.com,sps@centuryply.com,gopal.chatterjee@centuryply.com,mriduj@coral.in,dipankarc@coral.in,pushpalsamanta@forcepower.in';
//$sendto='dipankarc@coral.in';					
$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\n";
$headers .= "From: cronscript<info@salesmpower.acedns.in> \r\n" .
	"Reply-To:info@salesmpower.acedns.in \r\n" .
	'X-Mailer: PHP/' . phpversion();
if (($resultprimary == '' || $resultprimary == 0 || $resultledger == '' || $resultledger == 0 || $resultpendingorders == ''  || $resultpendingorders == 0 || $resultdealer == '' || $resultdealer == 0)  && $hour == '07') {
	if (mail($sendto, $subject, $mailbody, $headers, '-finfo@salesmpower.acedns.in')) {
		echo 'SUCCESS';
	} else {
		echo 'FAILURE';
	}
}
?>