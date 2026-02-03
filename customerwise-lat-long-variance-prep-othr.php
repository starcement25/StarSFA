<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_GOLDSTONE");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");

	function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
	{
	  // convert from degrees to radians
	  $latFrom = deg2rad($latitudeFrom);
	  $lonFrom = deg2rad($longitudeFrom);
	  $latTo = deg2rad($latitudeTo);
	  $lonTo = deg2rad($longitudeTo);
	
	  $latDelta = $latTo - $latFrom;
	  $lonDelta = $lonTo - $lonFrom;
	  $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
		cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
	  return $angle * $earthRadius;
	}
	function getReverseGeo($latitude,$longitude)
	{
		// format this string with the appropriate latitude longitude
		$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true&key=AIzaSyBhJB9maJFpMdTZ_JXAbB7HBX4H8oDFURo";
		// make the HTTP request
		$data = @file_get_contents($url);
		// parse the json response
		$jsondata = json_decode($data,true);
		
		//print_r($jsondata);
		// if we get a formatted_address array and the status was OK, get the addres
		if(is_array($jsondata )&& $jsondata['status']=='OK')
		{
			  $addr = $jsondata['results']['0']['formatted_address'];
		}		
		return  $addr;	
	}
		//echo $date=date('Y-m-d H:i:s');
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		$currentdate =$year.'-'.$month.'-'.$date;
		/*$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		echo $contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";*/
		$prev_date = date('Y-m-d', strtotime($currentdate .' -1 day'));
		/*$sqlcustomer="SELECT customer_code,trans_id FROM customer_visit_details WHERE trans_id IN(SELECT trans_id FROM location 
			 WHERE emp_code IN('E0682','E0555') AND date like '%".$prev_date."%')";*/
		$sqlcustomer="SELECT customer_code,customer_name,dns_customer_code FROM customer_master ORDER BY customer_name ASC";	 
		$rscustomer=mysqli_query($link,$sqlcustomer);
		$orderno='';
		$order_date='';
		$mf_stk_audit_id='';
		$mf_date='';
		while($rowcustomer=mysqli_fetch_assoc($rscustomer))
		{
			$customer_code=$rowcustomer['customer_code'];
			//$trans_id=$rowcustomer['trans_id'];
			$sqllatestor="SELECT order_no,DATE_FORMAT(SUBSTRING(order_no,-14,14),'%Y-%m-%d %H:%i:%s') as order_date FROM order_header WHERE 
							customer_code='".$customer_code."' ORDER BY DATE_FORMAT(SUBSTRING(order_no,-14,14),'%Y-%m-%d %H:%i:%s') ASC LIMIT 0,1";
			$rslatestor=mysqli_query($link,$sqllatestor);
			$rowlatestor=mysqli_fetch_assoc($rslatestor);
			$orderno=$rowlatestor['order_no'];
			$order_date=$rowlatestor['order_date'];
							
			$sqllatestmar="SELECT mf_stk_audit_id,DATE_FORMAT(SUBSTRING(mf_stk_audit_id,-14,14),'%Y-%m-%d %H:%i:%s') as mf_date FROM mf_stk_audit_header WHERE 
						customer_code='".$customer_code."' ORDER BY DATE_FORMAT(SUBSTRING(mf_stk_audit_id,-14,14),'%Y-%m-%d %H:%i:%s') ASC LIMIT 0,1";
			$rslatestmar=mysqli_query($link,$sqllatestmar);
			$rowlatestmar=mysqli_fetch_assoc($rslatestmar);
			$mf_stk_audit_id=$rowlatestmar['mf_stk_audit_id'];
			$mf_date=$rowlatestmar['mf_date'];
			if($orderno !='' && $mf_stk_audit_id!='')
			{
				if(strtotime($order_date) >= strtotime($mf_date))
				{
					$first_trans=$mf_stk_audit_id;
				}
				else
				{
					$first_trans=$orderno;
				}
			}
			else if($orderno !='')
			{
				$first_trans=$orderno;
			}
			else if($mf_stk_audit_id !='')
			{
				$first_trans=$mf_stk_audit_id;
			}
									
			$sqllatlongaccurate="SELECT latt,longi FROM location WHERE trans_id='".$first_trans."'";
			$rslatlongaccurate=mysqli_query($link,$sqllatlongaccurate);
			$rowlatlongaccurate=mysqli_fetch_assoc($rslatlongaccurate);
			$latt_accurate=$rowlatlongaccurate['latt'];
			$longi_accurate=$rowlatlongaccurate['longi'];
			//exit();
			/*$sqldnscustomercode="SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
			$rsdnscustomercode=mysqli_query($link,$sqldnscustomercode);
			$rowdnscustomercode=mysqli_fetch_assoc($rsdnscustomercode);
			$dns_customer_code=$rowdnscustomercode['dns_customer_code'];*/
			$customer_name=$rowcustomer['customer_name'];
			$dns_customer_code=$rowcustomer['dns_customer_code'];
			$sqlselecttranslatlong="SELECT latt,longi,DATE_FORMAT(date,'%Y-%m-%d %H:%i:%s') as trans_date 
									FROM location WHERE trans_id IN(SELECT order_no FROM order_header WHERE 
									customer_code='".$customer_code."' AND DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y%-%m-%d') >= '2019-04-01' 
									UNION ALL SELECT mf_stk_audit_id FROM mf_stk_audit_header WHERE 
									customer_code='".$customer_code."' AND DATE_FORMAT(SUBSTRING(mf_stk_audit_id,-14,8),'%Y%-%m-%d') >= '2019-04-01')";
			$rsselecttranslatlong=mysqli_query($link,$sqlselecttranslatlong);
			while($rowselecttranslatlong=mysqli_fetch_assoc($rsselecttranslatlong))
			{
				$latt_taken=$rowselecttranslatlong['latt'];
				$longi_taken=$rowselecttranslatlong['longi'];
				$trans_date=$rowselecttranslatlong['trans_date'];
				$distance_m=haversineGreatCircleDistance($latt_accurate, $longi_accurate, $latt_taken, $longi_taken, $earthRadius = 6371000);
				$distance_degree=rad2deg($distance_m/6371000);
				
				$sqlinsertcustomerwise_lat_long_variance="INSERT INTO customerwise_lat_long_variance2 
															SET customer_code='".$dns_customer_code."',
															customer_name='".$customer_name."',
															trans_date='".$trans_date."',
															lat_base='".$latt_accurate."',
															longi_base='".$longi_accurate."',
															lat_taken='".$latt_taken."',
															longi_taken='".$longi_taken."',
															variance_degree='".$distance_degree."',
															variance_meter='".$distance_m."',
															download_time=CURRENT_TIMESTAMP()";
				mysqli_query($link,$sqlinsertcustomerwise_lat_long_variance);											
			}
		}
		echo "SUCCESS";
	mysqli_close($link);
?>
