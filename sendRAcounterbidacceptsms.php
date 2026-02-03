<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	define("DB","acedns_EMAMI");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	
	$curentdatestring=date('Ymd');
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	$hour=gmdate('H',strtotime('+330 minute'));
	$minute=gmdate('i',strtotime('+330 minute'));
	$second=gmdate('s',strtotime('+330 minute'));
	$currentdate=$year.'-'.$month.'-'.$date;
	
	$condition=" AND UNIX_TIMESTAMP(NOW()) > UNIX_TIMESTAMP(DATE_FORMAT(SUBSTRING(RAD.counter_bid_id,-14,14),'%Y%-%m-%d %H:%i:%s') + INTERVAL 15 MINUTE)";
	$sqlquery="SELECT DISTINCT RAD.customer_code,RAD.bid_rate,CM.customer_name,PM.prod_desc,CM.phone_no,RAD.sms_done,RAD.prod_code,RAD.bid_id,RAD.counter_bid_rate 
	FROM RA_bid_rate_details RAD,customer_master CM,product_master PM WHERE RAD.bid_status='ACCEPT' AND RAD.counter_bid_id!='' AND 
	RAD.prod_code=PM.dns_prod_code AND RAD.customer_code=CM.customer_code AND RAD.sms_done='0' 
	AND SUBSTRING(RAD.bid_id,-14,8)='".$curentdatestring."'".$condition;
		$rsquery=mysqli_query($link,$sqlquery);
		$cntquery=mysqli_num_rows($rsquery);
		if($cntquery > 0){
			$username="emami";
			$password="EAL2017";
			$sender="EMAGRO";
			while($rowquery=mysqli_fetch_assoc($rsquery))
			{
				$customer_code=$rowquery['customer_code'];
				$prod_desc=$rowquery['prod_desc'];
				$bid_rate=$rowquery['bid_rate'];
				$counter_bid_rate=$rowquery['counter_bid_rate'];
				$customer_phone_no=$rowquery['phone_no'];
				$prod_code=$rowquery['prod_code'];
				$bid_id=$rowquery['bid_id'];
				//$customer_phone_no='7347604544';
				$skustring="SKU-Rate\n$prod_desc-$counter_bid_rate";
				$smsstring="Dear Customer,\nThank you for participating in the Emami bidding process\nCongratulations! Your bid has been accepted\n".$skustring."\nTeam HBC";
			//$Url = "http://websms.codez.in:8080/bulksms/bulksms?username=coz1-".$username."&password=".$password."&type=0&dlr=1&source=".$sender."&destination=91".$customer_phone_no."&message=".rawurlencode($smsstring);
			$Url="http://api.msg91.com/api/sendhttp.php?country=91&sender=EMAGRO&route=4&mobiles=".$customer_phone_no."&authkey=250054APHdwU5TNpm5c040582&message=".rawurlencode($smsstring);
			  $ch = curl_init();
			  curl_setopt($ch, CURLOPT_URL, $Url);
			  curl_setopt($ch, CURLOPT_TIMEOUT, 20);
			  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			  $output = curl_exec($ch);
			  //print_r($output);
			  curl_close($ch);
			  //$response = explode("|",$output);
			  //if(intval($response[0]) == 1701) {
					$sqlupdate="UPDATE RA_bid_rate_details SET sms_done='1' WHERE customer_code='".$customer_code."' AND 
								bid_id='".$bid_id."' AND prod_code='".$prod_code."' AND sms_done='0' AND bid_status='ACCEPT'";
					mysqli_query($link,$sqlupdate);
			 // }
				//For insertion of sms log
				 $sms_date=$year.'-'.$month.'-'.$date;
				 $sms_time=$hour.':'.$minute.':'.$second;
				 $sqlinsertlog="INSERT INTO sms_log SET 
								sms_date='".$sms_date."',
								sms_time='".$sms_time."',
								sms_type='ACCEPT BID',
								response='".$output."',
								sms_body='".$smsstring."',
								phone_no='".$customer_phone_no."',
								customer_code='".$customer_code."'";
				 mysqli_query($link,$sqlinsertlog);	
			  }
				echo 'SUCCESS';
			}
			else{
				echo 'NOT FOUND DATA';
			}
	mysqli_close($link);
?>