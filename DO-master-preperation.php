<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
$nick_name=$_REQUEST['nick_name'];
if(strtoupper($nick_name)=='ASL'){
	define("DB","acedns_ASL");
}
mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);
require("include/functions.php");
	$sauda_no=$_REQUEST['sauda_no'];
	//$sauda_no='FTE000220190924121327';
	$emp_sauda=substr($sauda_no,2,5);
	${bargain.$sauda_no}=array();
	$sku_list_array=array();
	$sqlselectskulist="SELECT sku_code FROM sauda_details WHERE sauda_no='".$sauda_no."'";
	$rsselectskulist=mysqli_query($link,$sqlselectskulist);
	while($rowselectskulist=mysqli_fetch_assoc($rsselectskulist))
	{
		$sku_code_sauda=$rowselectskulist['sku_code'];
		array_push($sku_list_array,$sku_code_sauda);
	}
	$sqlmaxchronologicalno="SELECT dns_sauda_no FROM DO_master ORDER BY  DATE_FORMAT(SUBSTRING(sauda_no,-14,14),'%Y-%m-%d %H:%i:%s') DESC LIMIT 0,1";
	  $rsmaxchronologicalno=mysqli_query($link,$sqlmaxchronologicalno);
	  $rowmaxchronologicalno=mysqli_fetch_assoc($rsmaxchronologicalno);
	  $maxchronologicalno=$rowmaxchronologicalno['dns_sauda_no'];
	  $maxchronologicalnoparts=explode("/",$maxchronologicalno);
	  $maxchronological=$maxchronologicalnoparts[2]+1;

	$sql_bargain_data = "SELECT DISTINCT SH.sauda_no,SH.customer_code,SH.branch_code,SD.sku_code,SD.qty,SD.sale_rate,SD.amount,SH.DO_done,
					SH.transaction_type,SD.freight_charge,SD.additional_TD,SD.additional_premium
			  FROM sauda_header SH,sauda_details SD WHERE SH.sauda_no=SD.sauda_no AND SH.sauda_no='".$sauda_no."'";
	$rs_bargain_data = mysqli_query($link,$sql_bargain_data) or die(mysqli_error()." Error in bargain data fetch: ".$sql_bargain_data);
	$linesaudaheader = '';
	while($rec_bargain_data = mysqli_fetch_assoc($rs_bargain_data))
	{
		$customer_code=$rec_bargain_data['customer_code'];
		$branch_code=$rec_bargain_data['branch_code'];
		$sku_code=$rec_bargain_data['sku_code'];
		$qty=$rec_bargain_data['qty'];
		$sale_rate = $rec_bargain_data['sale_rate'];
		$amount = $rec_bargain_data['amount'];
		$DO_done = $rec_bargain_data['DO_done'];
		$transaction_type = $rec_bargain_data['transaction_type'];
		$freight_charge = $rec_bargain_data['freight_charge'];
		$additional_TD = $rec_bargain_data['additional_TD'];
		$additional_premium = $rec_bargain_data['additional_premium'];
		if(!in_array($sku_code,${bargain.$sauda_no}))
		{
		$sqlparentsku="SELECT PUCM.mapped_prod_code FROM product_master PM,product_unit_coversion_matrix PUCM 
						WHERE PUCM.prod_code=PM.dns_prod_code AND PUCM.acedns='Y' AND PM.prod_code='".$sku_code."'";
		$rsparentsku=mysqli_query($link,$sqlparentsku);
		$rowparentsku=mysqli_fetch_assoc($rsparentsku);
		$mapped_prod_code=$rowparentsku['mapped_prod_code'];	
		
		$sqlselchildprods="SELECT PM.prod_code,PM.dns_prod_code,PM.UOM1,PM.conversion_factor,PM.conversion_factor_two FROM product_master PM,product_unit_coversion_matrix PUCM 
						WHERE PUCM.prod_code=PM.dns_prod_code AND PUCM.acedns='Y' AND PUCM.mapped_prod_code='".$mapped_prod_code."'";
		$rschildprods=mysqli_query($link,$sqlselchildprods);
		while($rowchildprods = mysqli_fetch_assoc($rschildprods))
		{
			$child_prod_code=$rowchildprods['prod_code'];
			$child_dns_prod_code=$rowchildprods['dns_prod_code'];
			
			if(in_array($child_prod_code,$sku_list_array) && $child_prod_code==$sku_code)
			{
				$customer_code=$customer_code;
				$branch_code=$branch_code;
				$sku_code=$sku_code;
				$qty=$qty;
				$sale_rate = $sale_rate;
				$amount = $amount;
				$DO_done = $DO_done;
				$transaction_type = $transaction_type;
				$mapped_prod_code=$mapped_prod_code;
				$freight_charge=$freight_charge;
			}
			else if(in_array($child_prod_code,$sku_list_array) && $child_prod_code!=$sku_code)
			{
				$customer_code=$customer_code;
				$branch_code=$branch_code;
				$sku_code=$child_prod_code;
				$sqlselectchilddetails="SELECT qty,sale_rate,amount,freight_charge FROM sauda_details WHERE 
										sauda_no='".$sauda_no."' AND sku_code='".$child_prod_code."'";
				$rsselectchilddetails=mysqli_query($link,$sqlselectchilddetails);	
				$rowselectchilddetails=mysqli_fetch_assoc($rsselectchilddetails);
				$qty=$rowselectchilddetails['qty'];
				$sale_rate = $rowselectchilddetails['sale_rate'];
				$amount = $rowselectchilddetails['amount'];
				$DO_done = $DO_done;
				$transaction_type = $transaction_type;
				$mapped_prod_code=$mapped_prod_code;					
				$freight_charge=$rowselectchilddetails['freight_charge'];
			}
			else
			{
				$customer_code=$customer_code;
				$branch_code=$branch_code;
				$sku_code=$child_prod_code;
				$qty=0;
				$sqlrate="SELECT sale_rate FROM industrial_rate WHERE product_code='".$child_prod_code."' and acedns='Y'";
				$rsrate=mysqli_query($link,$sqlrate);
				$recrate=mysqli_fetch_assoc($rsrate);
				$sale_rate=$recrate['sale_rate'];
				$amount=0;
				$DO_done=$DO_done;
				$transaction_type=$transaction_type;
				$mapped_prod_code=$mapped_prod_code;
				//For child freight charge
				if($transaction_type=='FOR PLANT')
				{
				$sqlselcapacity="SELECT transport_mode,loadability_ton,route_code FROM customer_master WHERE customer_code='".$customer_code."'";
				$rsselcapacity=mysqli_query($link,$sqlselcapacity);
				$rowselcapacity=mysqli_fetch_assoc($rsselcapacity);
				$transport_mode=$rowselcapacity['transport_mode'];
				$loadability_ton=$rowselcapacity['loadability_ton'];
				$route_code=$rowselcapacity['route_code'];
				$sqlbranchroutefreight="SELECT freight FROM branch_route_freight WHERE branch_code='".$branch_code."' 
										AND route_code='".$route_code."' AND transport_mode='".$transport_mode."' 
										AND capacity='".$loadability_ton."' AND acedns='Y' ORDER BY download_time DESC LIMIT 0,1";
				$rsbranchroutefreight=mysqli_query($link,$sqlbranchroutefreight);
				$rowbranchroutefreight=mysqli_fetch_assoc($rsbranchroutefreight);
				$freight=$rowbranchroutefreight['freight'];
				$sqlloaddistribution="SELECT qty_truck_load FROM load_distribution WHERE transport_mode='".$transport_mode."' 
									AND truck_load='".$loadability_ton."' AND prod_code='".$child_dns_prod_code."' ORDER BY datetime DESC LIMIT 0,1";
				$rsloadistribution=mysqli_query($link,$sqlloaddistribution);
				$rowloaddistribution=mysqli_fetch_assoc($rsloadistribution);
				$qty_truck_load=$rowloaddistribution['qty_truck_load'];
				$freight_charge=round(($freight/$qty_truck_load),2);
				}
				else
				{
					$freight_charge=0;
				}
				//End for child freight charge
			  }
			  $UOM1=$rowchildprods['UOM1'];
			  $conversion_factor=$rowchildprods['conversion_factor'];
			  $conversion_factor_two=$rowchildprods['conversion_factor_two'];
			  if(strtoupper($UOM1)=='LOOSE')
				{
					$qty_MT=round($qty,3);
				}
				if(strtoupper($UOM1)=='CASE')
				{
					$qty_MT=round(($qty*$conversion_factor_two),3);
				}
			  /*if(!in_array($child_prod_code,${bargain.$sauda_no}))
			  {*/
				$sqlselDObargain="SELECT sauda_no,sku_code FROM DO_master WHERE sauda_no='".$sauda_no."' AND sku_code='".$child_prod_code."'";
				$rsselDObargain=mysqli_query($link,$sqlselDObargain);
				$cntselDObargain=mysqli_num_rows($rsselDObargain);
				if($cntselDObargain ==0)
				{
					$sqldnsbranch="SELECT dns_branch_code FROM branch_master WHERE branch_code='".$branch_code."'";
					$rsdnsbranch=mysqli_query($link,$sqldnsbranch);
					$rowdnsbranch=mysqli_fetch_assoc($rsdnsbranch);
					$dns_branch_code=$rowdnsbranch['dns_branch_code'];
					$sauda_no_date_time=date('dmY',strtotime(substr($sauda_no,7,8)));
					
					$dns_sauda_no='BR/'.$dns_branch_code."/".$maxchronological.'/'.$sauda_no_date_time;
					$totalqty=$totalqty+$qty;
					$totalamount=$totalamount+$amount;
				$sqlinsertDomaster="INSERT INTO DO_master SET sauda_no='".$sauda_no."',
									customer_code='".$customer_code."',
									dns_sauda_no='".$dns_sauda_no."',
									branch_code='".$branch_code."',
									sku_code='".$child_prod_code."',
									mapped_sku_code='".$mapped_prod_code."',
									qty='".$qty."',
									qty_MT='".$qty_MT."',
									sale_rate='".$sale_rate."',
									freight_charge='".$freight_charge."',
									amount='".$amount."',
									incoterms='".$transaction_type."',
									additional_TD='".$additional_TD."',
									additional_premium='".$additional_premium."',
									status='".$DO_done."',
									download_time=CURRENT_TIMESTAMP()";
				mysqli_query($link,$sqlinsertDomaster);
				array_push(${bargain.$sauda_no},$child_prod_code);
			 }
		}//Inner while
	}// End of if loop of sku checking in bargain array
  }//Outer while
  //For Sending Notification to Employee
	$notification_id='PN'.$emp_sauda.substr($sauda_no,-14,14);
	$notification_type='Individual';
	$apiKey='AAAAdCu4Fjw:APA91bHHl7RnWyOj4Pb42NBuPfJZQAkOlmxKCoGL9flYk8xfhsqMY7_YtOtBKXPHNgx5szKyD2T1HriSFZ5NHmcBu874v9uCym0VEQlpNYbuUOjHBUmaVvtIXXYu-FOjhuwUksA4Geob';
	$collapseKey=rand();
	$title = "";
	$message="Your bargain has been booked with Bargain no-$dns_sauda_no dated-".date('d-m-Y',strtotime(substr($sauda_no,-14,8)))." approximate value  (in Rs.)". round($totalamount,2)." with total quantity $totalqty For any amendments please rectify  immediately by  informing CRM Team through email or SMS. - Thank you for choosing ASL";
	//This array contains, the token and the notification. The 'to' attribute stores the token.
	$data= array('sauda_no' =>$sauda_no,'notification_id' =>$notification_id, 'notification_type' => $notification_type, 'sender_id' => 'ASL', 'body' => $message); 
	$employee_upper_hierarchy=return_employee_upper_hierarchy($emp_sauda);
	$employee_upper_hierarchy=$employee_upper_hierarchy.','."'".$emp_sauda."'";
	$sqlreghierarchy="SELECT registrationid,emp_code FROM changepassword WHERE emp_code IN (".$employee_upper_hierarchy.")";
	$rsreghierarchy=mysqli_query($link,$sqlreghierarchy);
	while($rowreghierarchy=mysqli_fetch_assoc($rsreghierarchy))
	{
		$registrationid=$rowreghierarchy['registrationid'];
		$upper_emp_code=$rowreghierarchy['emp_code'];
		//$arrayToSend = array('to' => $registrationid, 'notification' => $notification, 'data'=>$data);
		$arrayToSend = array('to' => $registrationid, 'data'=>$data);
		// Set POST variables
		$url = 'https://fcm.googleapis.com/fcm/send';
		$headers = array(
			'Authorization: key=' . $apiKey,
			'Content-Type: application/json'
		);
		// Open connection
		$ch = curl_init();
		// Set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
		// Disabling SSL Certificate support temporarly
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayToSend));
	
		// Execute post
		$result = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($httpCode==200)
		{
			$sqlnotificationmaster  = "INSERT INTO notification_master ";
			$sqlnotificationmaster .= " SET notification_id='".$notification_id."'";
			$sqlnotificationmaster .= " ,type_of_notification='".$notification_type."'";
			$sqlnotificationmaster .= " ,sender_id='ASL'";
			$sqlnotificationmaster .= " ,message='".$message."'";
			$sqlnotificationmaster .= " ,transferred='YES'";
			if(mysqli_query($link,$sqlnotificationmaster))
			{
				$sqlnotification  = "INSERT INTO notification_ack_relation ";
				$sqlnotification .= " SET notification_id='".$notification_id."'";
				$sqlnotification .= " ,receiver_id='".$$upper_emp_code."'";
				mysqli_query($link,$sqlnotification);
			}
		}
	}
?>
