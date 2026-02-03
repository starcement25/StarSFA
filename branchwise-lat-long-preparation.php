<?php
	// define("SERVER","localhost");
	// define("USER","root");
	// define("PASSWORD","Passw0rd123#$");
	// //require("include/config-setup.php");
	// define("DB","acedns_STAR");
	// $link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	// mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
require_once("sfa_connection.php");

$localDB = new sfa_connection();
$link = $localDB->conn;

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
		//$prev_date = date('Y-m-d', strtotime($currentdate .' -1 day'));
		$sqlcustomer="SELECT customer_code FROM customer_master WHERE branch_code IN(SELECT branch_code FROM branch_master
			 WHERE dns_branch_code IN('A107','A109','A108','A110','A116','A113','A112','A106','A104','B005','A114','A105','A103','B002','C001','C002'))
			 ORDER BY customer_code ASC";
		$rscustomer=mysqli_query($link,$sqlcustomer);
		
		while($rowcustomer=mysqli_fetch_assoc($rscustomer))
		{
			
			$customer_code=$rowcustomer['customer_code'];
			$sqltransid="SELECT customer_code,trans_id FROM customer_visit_details WHERE customer_code='".$customer_code."'
						ORDER BY SUBSTRING(trans_id,-14,14) DESC LIMIT 0,1";
			$rstransid=mysqli_query($link,$sqltransid);
			$rowtransid=mysqli_fetch_assoc($rstransid);
			$trans_id=$rowtransid['trans_id'];

			$sqllatlongaccurate="SELECT latt,longi FROM location WHERE trans_id='".$trans_id."'";
			$rslatlongaccurate=mysqli_query($link,$sqllatlongaccurate);
			$rowlatlongaccurate=mysqli_fetch_assoc($rslatlongaccurate);
			$latt_accurate=$rowlatlongaccurate['latt'];
			$longi_accurate=$rowlatlongaccurate['longi'];

			$sqlcustomerupdate  = "UPDATE customer_master SET ";
			$sqlcustomerupdate .= "  	base_latt='".mysqli_real_escape_string($link,$latt_accurate)."'";
			$sqlcustomerupdate .= "  ,base_longi='".mysqli_real_escape_string($link,$longi_accurate)."'";
			//$sqlcustomerupdate .= "  ,download_time=CURRENT_TIMESTAMP()";
			$sqlcustomerupdate .= "  WHERE customer_code='".$customer_code."'";
			// echo 	$sqlcustomerupdate;
			// die;
			mysqli_query($link,$sqlcustomerupdate);
			
			$sqlupdatecustomerroute="UPDATE customer_route_emp_relation SET
									download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$customer_code."'";
									
			mysqli_query($link,$sqlupdatecustomerroute);
			
		}
		echo "SUCCESS";
	mysqli_close($link);
?>
