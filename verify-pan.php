<?php
	//define("COOKIE_FILE", "cookie.txt");
	//$file = fopen("cookie.txt", 'r');
	//include('verify-pan.php');
	$file="cookie.txt";
	//$pan='CBWPD2243R';
	//$pan='AAAAAAAAAA';
	//$pan='ACTPD4701G';
	$pan=$_REQUEST['pan'];
	$captcha=$_REQUEST['captcha_code'];
	
	//$data=array('captchaCode' => $captcha,'panOfDeductee' => $pan);
	$data="captchaCode=".$captcha."&panOfDeductee=".$pan;
	/*$ch = curl_init("https://incometaxindiaefiling.gov.in/e-Filing/Services/KnowYourJurisdiction.html");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_COOKIE,1); 
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $file); 
    curl_setopt($ch, CURLOPT_COOKIEFILE, $file);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);*/
	$url="https://incometaxindiaefiling.gov.in/e-Filing/Services/KnowYourJurisdiction.html";
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_HEADER, 1);
	curl_setopt( $ch, CURLINFO_HEADER_OUT, 1);
	curl_setopt( $ch, CURLOPT_AUTOREFERER, 1);
	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt( $ch, CURLOPT_COOKIEJAR, $file);
	curl_setopt( $ch, CURLOPT_POST, 1);
	curl_setopt( $ch, CURLOPT_COOKIEFILE, $file); 
	curl_setopt( $ch, CURLOPT_REFERER, $url);
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Keep-Alive: 115',  'Connection: keep-alive',  'Content-type: application/x-www-form-urlencoded', 'Content-length: '.strlen($data)));
	curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:2.0.1) Gecko/20100101 Firefox/4.0.1');
	curl_setopt( $ch, CURLOPT_URL, $url);

	if(!curl_exec($ch))
	{
		echo 'Curl error: ' . curl_error($ch);
	}
	else
	{
		$response=@curl_exec($ch);
		print_r($response);
	}
	curl_close ($ch);	
?>