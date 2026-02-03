<?php
ob_start();
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	$curdate=$year.'-'.$month.'-'.$date;
	$transdate=date('Y-m-d',strtotime(substr($trans_id,-14,8)));
	if($month < 4)
	{
		$financial_year_start=($year-1).'-04-01';
		$financial_year_end=$year.'-03-31';
	}
	else
	{
		$financial_year_start=$year.'-04-01';
		$financial_year_end=($year+1).'-03-31';
	}
	echo $financial_year_start;
	echo $financial_year_end;

exit();
/*$stringsms="EAL confirms Sauda booking of:HBM - R5 10 Case@Rs.926.46,HBM Gold - RG01 5 Case@Rs.1021.9,HBM - Y12 15 Case@Rs.882.14,HBM - G13 20 Case@Rs.930.93, with Sri Sumathinatha Traders.Sri Sumathinatha Traders agreed to lift sauda within 21 days from sauda date. Freight + Taxes extra as applicable . For any deviation, please contact Mr Anirudha Nayak @ 9007227866 in 24 hours from receipt of this SMS.";*/
$stringsms="EAL confirms Sauda booking of:HBM - G13 20 Case@Rs.930.93, with Sri Sumathinatha Traders.Sri Sumathinatha Traders agreed to lift sauda within 21 days from sauda date. Freight %2B Taxes extra as applicable . For any deviation, please contact Mr Anirudha Nayak @ 9007227866 in 24 hours from receipt of this SMS.";
//$finalstringsms=wordwrap($stringsms, 160, "<br />");
$finalstringsms=chunk_split($stringsms, 160, "<br />");
$finalstringsmsarray=explode('<br />',$finalstringsms);
//print_r($finalstringsmsarray);

foreach($finalstringsmsarray as $stringval)
{
	//echo strlen($stringval).'<br />';
	echo urlencode($stringval);
	
	/*$url="http://smslive.in/push/default.aspx?user=e_agro&pws=emagro123&Receipent=9836361358&sms=".rawurlencode($stringval)."";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	curl_exec($ch);
	sleep(3);*/
}
/*$stringsms="Sumathinatha Traders.Sri Sumathinatha Traders agreed to lift sauda within 21 days from sauda date. For any deviation, please contact Mr Anirudha Nayak @ 9007227866 ";
$url="http://smslive.in/push/default.aspx?user=e_agro&pws=emagro123&Receipent=9474335413&sms=".urlencode($stringsms)."";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	curl_exec($ch);*/

?>