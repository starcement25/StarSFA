<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	define("DB","acedns_EMAMI");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");

	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	$hour=gmdate('H',strtotime('+330 minute'));
	$minute=gmdate('i',strtotime('+330 minute'));
	$second=gmdate('s',strtotime('+330 minute'));	
	$currentdate=$year.'-'.$month.'-'.$date;
	$sqllastwindowtime="SELECT time_to,time_from FROM RA_windowtime WHERE rate_released_date='".$currentdate."' AND last_window_time='yes'";
	$rslastwindowtime=mysqli_query($link,$sqllastwindowtime);
	$cntlastwindowtime=mysqli_num_rows($rslastwindowtime);
	if($cntlastwindowtime >0)
	{
		$rowlastwindowtime=mysqli_fetch_assoc($rslastwindowtime);
		$timeto_lastwindow=date('Y-m-d').' '.$rowlastwindowtime['time_to'];
	
		$condition=" AND DATE_FORMAT(SUBSTRING(RAD.bid_id,-14,8),'%Y%-%m-%d')='".$currentdate."' 
		AND UNIX_TIMESTAMP(NOW()) > UNIX_TIMESTAMP('".$timeto_lastwindow."')";
			
		$sqlquery="SELECT DISTINCT RAD.bid_id,RAD.customer_code,RAD.qty,RAD.bid_rate,PM.prod_desc,CM.phone_no,RAD.counter_bid_rate FROM RA_bid_rate_details RAD,
				customer_master CM,product_master PM WHERE  RAD.counter_bid='Y' AND RAD.bid_status='' AND RAD.prod_code=PM.dns_prod_code 
				AND RAD.customer_code=CM.customer_code AND RAD.sms_done='0' ".$condition." ORDER BY RAD.customer_code ASC";
		$rsquery=mysqli_query($link,$sqlquery);
		$cntquery=mysqli_num_rows($rsquery);
		if($cntquery > 0){
			$customer_code_array=array();
			$username="emami";
			$password="EAL2017";
			$sender="EMAGRO";

			while($rowquery=mysqli_fetch_assoc($rsquery))
			{
				$customer_code=$rowquery['customer_code'];
				$prod_desc=$rowquery['prod_desc'];
				$bid_rate=$rowquery['bid_rate'];
				$counter_bid_rate=$rowquery['counter_bid_rate'];
				if(!in_array($customer_code,$customer_code_array))
				{
					array_push($customer_code_array,$customer_code);
					${'prodstringrate'.$customer_code}='';
				}
				${'prodstringrate'.$customer_code}.=$prod_desc."-".$counter_bid_rate."\n";
			}
$smsstring="Dear Customer,\nThank you for participating in the Emami bidding process:\nFollowing bids you placed today are in counter, with the counter rates:\nSKU-Counter Bid Rate\n";			
			foreach($customer_code_array as $customercodeval)
			{
			   	$smsstringfinal=$smsstring.${'prodstringrate'.$customercodeval}."\nPlease contact your ASO to accept or reject the counter bid.\nTeam HBC";

				$sqlcustomerphone="SELECT phone_no FROM customer_master WHERE customer_code='".$customercodeval."' AND acedns='Y'";
				$rscustomerphone=mysqli_query($link,$sqlcustomerphone);
				$rowcustomerphone=mysqli_fetch_assoc($rscustomerphone);
				$customer_phone_no=$rowcustomerphone['phone_no'];
				//$customer_phone_no='7347604544';
				//$customer_phone_no='9874450813';
				  /*if(strlen($smsstringfinal)> 160)
					{
				  		$smsstringfinal=wordwrap($smsstringfinal, 160, "<br />");
						$smsstringfinalarray=explode('<br />',$smsstringfinal);
						foreach($smsstringfinalarray as $smsstringval)
						{
						  $Url = "http://websms.codez.in:8080/bulksms/bulksms?username=coz1-".$username."&password=".$password."&type=0&dlr=1&source=".$sender."&destination=91".$customer_phone_no."&message=".rawurlencode($smsstringval);
						  $ch = curl_init();
						  curl_setopt($ch, CURLOPT_URL, $Url);
						  curl_setopt($ch, CURLOPT_TIMEOUT, 20);
						  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
						  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						  $output = curl_exec($ch);
						  //print_r($output);
						  curl_close($ch);
						  sleep(1);
						  //exit();
						  /*$response = explode("|",$output);
						  
						  if(intval($response[0]) == 1701) {
							echo "Message: Success";
						  }
						  else {
							echo "Message: Fail (".$output.")";
						  }*/
						/*}
					}
					else
					{*/
				//$Url = "http://websms.codez.in:8080/bulksms/bulksms?username=coz1-".$username."&password=".$password."&type=0&dlr=1&source=".$sender."&destination=91".$customer_phone_no."&message=".rawurlencode($smsstringfinal);
				$Url="http://api.msg91.com/api/sendhttp.php?country=91&sender=EMAGRO&route=4&mobiles=".$customer_phone_no."&authkey=250054APHdwU5TNpm5c040582&message=".rawurlencode($smsstringfinal);
					  $ch = curl_init();
					  curl_setopt($ch, CURLOPT_URL, $Url);
					  curl_setopt($ch, CURLOPT_TIMEOUT, 20);
					  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
					  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					  $output = curl_exec($ch);
					  //print_r($output);
					  curl_close($ch);
					//}
					//$response = explode("|",$output);
			  		//if(intval($response[0]) == 1701) {
					$sqlupdate="UPDATE RA_bid_rate_details SET sms_done='1' WHERE customer_code='".$customercodeval."' AND 
								DATE_FORMAT(SUBSTRING(bid_id,-14,8),'%Y%-%m-%d')='".$currentdate."' AND sms_done='0' AND counter_bid='Y'";
					mysqli_query($link,$sqlupdate);
					//}
					//For insertion of sms log
					 $sms_date=$year.'-'.$month.'-'.$date;
					 $sms_time=$hour.':'.$minute.':'.$second;
					 $sqlinsertlog="INSERT INTO sms_log SET 
					 				sms_date='".$sms_date."',
									sms_time='".$sms_time."',
									sms_type='COUNTER BID',
									response='".$output."',
								    sms_body='".$smsstringfinal."',
									phone_no='".$customer_phone_no."',
									customer_code='".$customercodeval."'";
					 mysqli_query($link,$sqlinsertlog);				
				  	//}
				}
				echo 'SUCCESS';
			}
			else{
				echo 'NOT FOUND DATA';
			}
		}
		else
		{
			echo 'NOT FOUND TIME';
		}
	mysqli_close($link);
?>