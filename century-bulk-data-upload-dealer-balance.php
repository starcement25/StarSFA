<?php
//Update dealer balance
$urldealerbalance="https://centurycareapp.com/upload_dealer_balance_confirm_log.php";
	$chdealerbalance = curl_init();
	curl_setopt($chdealerbalance, CURLOPT_URL, $urldealerbalance);
	curl_setopt($chdealerbalance, CURLOPT_TIMEOUT, 1800);
	curl_setopt($chdealerbalance, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($chdealerbalance, CURLOPT_RETURNTRANSFER, 1);
$resultdealerbalance=curl_exec($chdealerbalance);
	echo 'SUCCESS';
?>	