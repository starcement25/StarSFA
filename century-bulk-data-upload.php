<?php
//primary sales upload
// $urlprimary="https://centurycareapp.com/upload_primary_sales_data.php";
// 	$chprimary = curl_init();
// 	curl_setopt($chprimary, CURLOPT_URL, $urlprimary);
// 	curl_setopt($chprimary, CURLOPT_TIMEOUT, 100);
// 	curl_setopt($chprimary, CURLOPT_FOLLOWLOCATION, 1);
// 	curl_setopt($chprimary, CURLOPT_RETURNTRANSFER, 1);
// $resultprimary=curl_exec($chprimary);
$resultprimary='';
//Dealers data upload
$urldealer="https://centurycareapp.com/upload_dealer_files_data.php";
	$chdealer = curl_init();
	curl_setopt($chdealer, CURLOPT_URL, $urldealer);
	curl_setopt($chdealer, CURLOPT_TIMEOUT, 100);
	curl_setopt($chdealer, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($chdealer, CURLOPT_RETURNTRANSFER, 1);
$resultdealer=curl_exec($chdealer);
//Copy the PDF files 
$urlcopyPDF="https://centurycareapp.com/delete_PDF_files.php";
	$chcopyPDF = curl_init();
	curl_setopt($chcopyPDF, CURLOPT_URL, $urlcopyPDF);
	curl_setopt($chcopyPDF, CURLOPT_TIMEOUT, 60);
	curl_setopt($chcopyPDF, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($chcopyPDF, CURLOPT_RETURNTRANSFER, 1);
$resultcopyPDF=curl_exec($chcopyPDF);
//Upload ledger files
$urlledger="https://centurycareapp.com/upload_ledger_balance_data.php";
	$chledger = curl_init();
	curl_setopt($chledger, CURLOPT_URL, $urlledger);
	curl_setopt($chledger, CURLOPT_TIMEOUT, 100);
	curl_setopt($chledger, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($chledger, CURLOPT_RETURNTRANSFER, 1);
$resultledger=curl_exec($chledger);
//Upload Dealer Ageing
$urlageing="https://centurycareapp.com/upload_dealer_ageing_data.php";
	$chageing = curl_init();
	curl_setopt($chageing, CURLOPT_URL, $urlageing);
	curl_setopt($chageing, CURLOPT_TIMEOUT, 70);
	curl_setopt($chageing, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($chageing, CURLOPT_RETURNTRANSFER, 1);
$resultageing=curl_exec($chageing);
//Update ledger type
$urlledgertype="https://centurycareapp.com/update_ledger_type.php";
	$chledgertype = curl_init();
	curl_setopt($chledgertype, CURLOPT_URL, $urlledgertype);
	curl_setopt($chledgertype, CURLOPT_TIMEOUT, 60);
	curl_setopt($chledgertype, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($chledgertype, CURLOPT_RETURNTRANSFER, 1);
$resultledgertype=curl_exec($chledgertype);
//Upload pending orders
$urlpendingorders="https://centurycareapp.com/uplaod_pending_orders_test.php";
	$chpendingorders = curl_init();
	curl_setopt($chpendingorders, CURLOPT_URL, $urlpendingorders);
	curl_setopt($chpendingorders, CURLOPT_TIMEOUT, 100);
	curl_setopt($chpendingorders, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($chpendingorders, CURLOPT_RETURNTRANSFER, 1);
$resultpendingorders=curl_exec($chpendingorders);
//$resultpendingorders=0;
/*$urlpendingordersfinal="https://centurycareapp.com/upload_pending_orders.php";
	$chpendingordersfinal = curl_init();
	curl_setopt($chpendingordersfinal, CURLOPT_URL, $urlpendingordersfinal);
	curl_setopt($chpendingordersfinal, CURLOPT_TIMEOUT, 30);
	curl_setopt($chpendingordersfinal, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($chpendingordersfinal, CURLOPT_RETURNTRANSFER, 1);
$resultpendingordersfinal=curl_exec($chpendingordersfinal);*/
$urlopening="https://centurycareapp.com/upload_opening_balance_data.php";
	$chopening = curl_init();
	curl_setopt($chopening, CURLOPT_URL, $urlopening);
	curl_setopt($chopening, CURLOPT_TIMEOUT, 100);
	curl_setopt($chopening, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($chopening, CURLOPT_RETURNTRANSFER, 1);
$resultopening=curl_exec($chopening);

$urllob="https://centurycareapp.com/sheduler_Lob.php";
	$chlob = curl_init();
	curl_setopt($chlob, CURLOPT_URL, $urllob);
	curl_setopt($chlob, CURLOPT_TIMEOUT, 100);
	curl_setopt($chlob, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($chlob, CURLOPT_RETURNTRANSFER, 1);
$resultlob=curl_exec($chlob);

$urldealerappfiles="https://centurycareapp.com/read_FTP_files.php";
	$chdealerappfiles = curl_init();
	curl_setopt($chdealerappfiles, CURLOPT_URL, $urldealerappfiles);
	curl_setopt($chdealerappfiles, CURLOPT_TIMEOUT, 100);
	curl_setopt($chdealerappfiles, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($chdealerappfiles, CURLOPT_RETURNTRANSFER, 1);
$resultdealerappfiles=curl_exec($chdealerappfiles);
$urlpanelfiles="https://centurycareapp.com/read_FTP_files_panel_subdealer.php";
	$chpanelfiles = curl_init();
	curl_setopt($chpanelfiles, CURLOPT_URL, $urlpanelfiles);
	curl_setopt($chpanelfiles, CURLOPT_TIMEOUT, 100);
	curl_setopt($chpanelfiles, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($chpanelfiles, CURLOPT_RETURNTRANSFER, 1);
$resultpanelfiles=curl_exec($chpanelfiles);
//Sending mail
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$location_date=$date.'-'.$month.'-'.$year.'@'.$hour.':'.$minute.':'.$second;

$subject="CENTURY sync done on ".$location_date.' hrs.';
$mailbody = "<html><head><title>Century Sync</title></head>
					<body>Sync done on Primary sales , Dealer data , Ledger data and Pending orders. 
					Also the PDF files moved to the respective folders. All the files processed data counts are mentioned below.<br /><br /><table align='left'>
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
								<td><strong>Pending Orders: $resultpendingorders </strong></td>
							</tr>
							<tr>
								<td><strong>PDF files moved: $resultcopyPDF </strong></td>
							</tr>
						</table> </body></html>";
$sendto='manik.paul@centuryply.com,chandan.das@centuryply.com,sps@centuryply.com,gopal.chatterjee@centuryply.com,mriduj@coral.in,dipankarc@coral.in';
//$sendto='dipankarc@coral.in';					
$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\n";
$headers .= "From: cronscript<info@salesmpower.acedns.in> \r\n" .
			"Reply-To:info@salesmpower.acedns.in \r\n" .
			'X-Mailer: PHP/' . phpversion();
if(($resultprimary=='' || $resultprimary==0 || $resultledger=='' || $resultledger==0 || $resultpendingorders==''  || $resultpendingorders==0 || $resultdealer=='' || $resultdealer==0)  && $hour=='07') 
{
	if(mail($sendto, $subject, $mailbody, $headers,'-finfo@salesmpower.acedns.in'))
	{
		echo 'SUCCESS';
	}
	else
	{
		echo 'FAILURE';
	}
}
?>	