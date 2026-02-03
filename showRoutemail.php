<?php
	$nick_name='VIPL';
	define("SERVER","216.237.114.58");
	define("USER","coralweb");
	define("PASSWORD","coral5071");
	
	require("include/config-setup.php");
	
	define("DB","$nick_name");
	
	//require("include/dbcon.php");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	
	require("include/config-email-setup.php");

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
		$url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true";
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
	function updateLatlong($emp_code)
	{
		//For check and update in the location table
		$sqlselectlatlong="SELECT * FROM location WHERE emp_code='".$emp_code."' AND latt='0' AND longi='0'";
		$rsselectlatlong=mysqli_query($link,$sqlselectlatlong) or die(mysqli_error()." Error in select zero latt longi: ".$sqlselectlatlong);
		$countselectlatlong=mysqli_num_rows($rsselectlatlong);
		
		if($countselectlatlong>0)
		{
			$sqllastlatlong="SELECT latt,longi FROM location WHERE emp_code='".$emp_code."' AND latt<>'0' AND longi<>'0' ORDER BY date DESC LIMIT 0,1";
			$rslastlatlong=mysqli_query($link,$sqllastlatlong) or die(mysqli_error()." Error in select last not zero latt longi: ".$sqllastlatlong);
			$rowlastlatlong=mysqli_fetch_assoc($rslastlatlong);
			$lastlatt=$rowlastlatlong['latt'];
			$lastlongi=$rowlastlatlong['longi'];
			
			$sqlupdatelatlong="UPDATE location set latt='".$lastlatt."',longi='".$lastlongi."' WHERE emp_code='".$emp_code."' AND latt='0' AND longi='0'";
			$rslastlatlong=mysqli_query($link,$sqlupdatelatlong) or die(mysqli_error()." Error in update zero latt longi: ".$sqlupdatelatlong);
		}
	}
	
	$sqlemployee="SELECT emp_code,emp_name FROM employee_master";
	$rsemployee=mysqli_query($link,$sqlemployee);
	$curdateserver=gmdate('Y-m-d',strtotime('+329 minute'));
	$dateprevious=date('Y-m-d', strtotime("-1 days,$curdateserver "));
	//$dateprevious=date('Y-m-d');
	while($rowemployee=mysqli_fetch_assoc($rsemployee))
	{
		$emp_code=$rowemployee['emp_code'];
		$emp_name=$rowemployee['emp_name'];
		$operation_type_dcr='DCR';
		updateLatlong($emp_code);
		
		/*$sqlpoint="SELECT latt, longi,trans_id,DATE_FORMAT(date,'%H:%i:%s') as trantime FROM location WHERE emp_code ='".$emp_code."' AND 
					DATE LIKE '%".$dateprevious."%' ORDER BY DATE_FORMAT(DATE,'%Y-%m-%d %H:%i:%s') ASC ";*/
		$sqlpoint="(SELECT latt, longi,trans_id,DATE_FORMAT(date,'%H:%i:%s') as trantime FROM location WHERE emp_code ='".$emp_code."' AND 
					DATE LIKE '%".$dateprevious."%' ORDER BY DATE_FORMAT(date,'%Y-%m-%d %H:%i:%s') ASC)";			
		$rspoint=mysqli_query($link,$sqlpoint);
		$countpoint=mysqli_num_rows($rspoint);
		$pointcoords=array();
		$customer_name_array=array();
		$activity_array=array();
		$total_qty_array=array();
		$total_amount_array=array();
		$trantime_array=array();
		$pathpointsArr=array();
		$markersArr[]='';
		$pathpointsArr[]='';
		$pointlabel=1;
		if($countpoint>0)
		{
			while($rowpoint=mysqli_fetch_assoc($rspoint))
			{
				$point=$rowpoint['latt'].','.$rowpoint['longi'];
				$markersArr[]="markers=color:red|label:$pointlabel|$point";
				$markers=implode('&',$markersArr);
				//$pathpointsArr[]=$point;
				array_push($pathpointsArr,$point);
				$pathpoints=implode('|',$pathpointsArr);
				//$pathpoints=substr($pathpoints,1);
				array_push($pointcoords,$point);
				
				$trans_id=$rowpoint['trans_id'];
				$operation_type=substr($trans_id,0,1);
				$time=$rowpoint['trantime'];
				if($operation_type=='A')
				{
					$activity='Attendance';
					$customer_name='----';
					$total_amount='----';
					$total_qty='----';
				}
				if($operation_type=='O')
					{
						$activity='Order';
						$order_no=$trans_id;
						$sqlcustomer="SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_order_received FROM 
									  order_header OH,customer_master CM,order_details OD
									  WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."' 
									  AND OH.order_no=OD.order_no GROUP BY OD.order_no
									  UNION
									  SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_order_received FROM 
									  order_header OH,prospective_customer_master CM,order_details OD
									  WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."' 
									  AND OH.order_no=OD.order_no GROUP BY OD.order_no";
						$rscustomer=mysqli_query($link,$sqlcustomer) or die(mysqli_error()." Error in select customer: ".$sqlcustomer);
						
						$rowcustomer=mysqli_fetch_assoc($rscustomer);
						$customer_name=$rowcustomer['customer_name'];
						$total_qty=$rowcustomer['total_order_received'];
						$total_amount='----';
					}
					if($operation_type=='P')
					{
						$activity='Payment';
						$receipt_id=$trans_id;
						$sqlcustomerpayment="SELECT CM.customer_name,CM.customer_code,SUM(PD.amount) AS total_collection_received 
											FROM payment_header PH,customer_master CM,payment_details PD
											WHERE CM.customer_code=PH.customer_code AND PH.receipt_id=PD.receipt_id AND 
											PH.receipt_id='".$receipt_id."' GROUP BY PD.receipt_id
											UNION
											SELECT CM.customer_name,CM.customer_code,SUM(PD.amount) AS total_collection_received 
											FROM payment_header PH,prospective_customer_master CM,payment_details PD
											WHERE CM.customer_code=PH.customer_code AND PH.receipt_id=PD.receipt_id 
											AND PH.receipt_id='".$receipt_id."' GROUP BY PD.receipt_id";
						$rscustomerpayment=mysqli_query($link,$sqlcustomerpayment) or die(mysqli_error()." Error in select customer payment: ".$sqlcustomerpayment);
						
						$rowcustomerpayment=mysqli_fetch_assoc($rscustomerpayment);
						$customer_name=$rowcustomerpayment['customer_name'];
						$total_amount='Rs/- '.number_format($rowcustomerpayment['total_collection_received'],2);
						$total_qty='----';
					}
					if($operation_type=='N')
					{
						$operation_type_no=substr($trans_id,1,1);
						if($operation_type_no=='O')
							{
								$activity='No Order';
								$order_no=$trans_id;
								$sqlcustomernoorder="SELECT CM.customer_name,CM.customer_code FROM order_header OH,customer_master CM 
													WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."'
													UNION
													SELECT CM.customer_name,CM.customer_code FROM order_header OH,prospective_customer_master CM 
													WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."'";
								$rscustomernoorder=mysqli_query($link,$sqlcustomernoorder) or die(mysqli_error()." Error in select customer for no order: ".$sqlcustomernoorder);
								$rowcustomernoorder=mysqli_fetch_assoc($rscustomernoorder);
						
								$customer_name=$rowcustomernoorder['customer_name'];
								$total_amount='----';
								$total_qty='----';
							}
						if($operation_type_no=='C')
						{
							$activity='No Collection';
							$receipt_id=$trans_id;
							$sqlcustomernocollection="SELECT CM.customer_name,CM.customer_code FROM payment_header PH,customer_master CM 
													   WHERE CM.customer_code=PH.customer_code AND PH.receipt_id='".$receipt_id."'
													   UNION
													   SELECT CM.customer_name,CM.customer_code FROM payment_header PH,prospective_customer_master CM 
													   WHERE CM.customer_code=PH.customer_code AND PH.receipt_id='".$receipt_id."'";
							$rscustomernocollection=mysqli_query($link,$sqlcustomernocollection) or die(mysqli_error()." 
														Error in select customer for no collection: ".$sqlcustomernocollection);
							$rowcustomernocollection=mysqli_fetch_assoc($rscustomernocollection);
							
							$customer_name=$rowcustomernocollection['customer_name'];
							$total_amount='----';
							$total_qty='----';
						}
					}
					if($operation_type=='D')
					{
						$operation_type_no=substr($trans_id,1,1);
						$sqlprospective="SELECT PCH.customer_name FROM prospective_customer_header PCH WHERE PCH.trans_id='".$trans_id."'";
						$rsprospective=mysqli_query($link,$sqlprospective) or die(mysqli_error()." 
										Error in select prospective customer or mechanic: ".$sqlprospective);
						$rowprospective=mysqli_fetch_assoc($rsprospective);
						$customer_name=$rowprospective['customer_name'];
						if($operation_type_no=='M')
							{
								$activity='Visit Mechanic';
								$total_amount='----';
								$total_qty='----';
							}
						if($operation_type_no=='C')
							{
								$activity='Visit Customer';
								$total_amount='----';
								$total_qty='----';
							}	
					}
					
					array_push($customer_name_array,$customer_name);
					array_push($trantime_array,$time);
					array_push($total_qty_array,$total_qty);
					array_push($total_amount_array,$total_amount);
					array_push($activity_array,$activity);
		
				$pointlabel++;
			}
			//echo $markers;
			//echo $pathpoints;
			//print_r($pointcoords);
			$DCR_email=DCREMAILRECIPENTS;

			//$DCR_email='dipsome2006@gmail.com';
			$datesubject=date('d-m-Y', strtotime("-1 days,$curdateserver "));
			$subject='DCR of '.$emp_name.' on '.$datesubject.' for'.$nick_name;
			$body='<img src="http://maps.googleapis.com/maps/api/staticmap?size=700x600&path=color:0xff0000ff|weight:2'.$pathpoints.'&sensor=false&'.$markers.'" alt=""><br /> <br />';
			
			for($i=0;$i<count($pointcoords);$i++)
			{
				$point=($i+1);
				
				$Arrpointcoordsfrom =explode(',',$pointcoords[$i]);
				$latitudeFrom=$Arrpointcoordsfrom[0];
				$longitudeFrom=$Arrpointcoordsfrom[1];
				
				$address=getReverseGeo($latitudeFrom,$longitudeFrom);
				
				if($i==0)
				{
					$distancetxt='---------';
				}
				else
				{
					$Arrpointcoordsfrom =explode(',',$pointcoords[$i-1]);
					$latitudeFrom=$Arrpointcoordsfrom[0];
					$longitudeFrom=$Arrpointcoordsfrom[1];
					
					$Arrpointcoordsto =explode(',',$pointcoords[$i]);
					$latitudeTo=$Arrpointcoordsto[0];
					$longitudeTo=$Arrpointcoordsto[1];
					$distance=round((haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000))/1000,2);
					$distancetxt=$distance." k.m";
				}
				$bodydetails.="<tr><td style='width:50px;text-align:left;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$point."</span>&nbsp;</td>	
								<td style='width:100px;text-align:left;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$customer_name_array[$i]."</span>&nbsp;</td>	
								<td style='width:150px;text-align:left;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$address."</span>&nbsp;</td>
								<td style='width:70px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$distancetxt."</span>&nbsp;</td>
								<td style='width:70px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$activity_array[$i]."</span>&nbsp;</td>
								<td style='width:60px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$total_qty_array[$i]."</span>&nbsp;</td>
								<td style='width:60px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$total_amount_array[$i]."</span>&nbsp;</td>
								<td style='width:70px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$trantime_array[$i]."</span>&nbsp;</td>			
							  </tr>";
			}
			
			$body.="<table border=1 style=background-color:AliceBlue>
					<tr>
					<th style='width:50px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Point</span></strong></th>
					<th style='width:100px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Customer Name</span></strong></th>
					<th style='width:150px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Address</span></strong></th>
					<th style='width:70px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Distance</span></strong></th>
					<th style='width:70px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Activity</span></strong></th>
					<th style='width:60px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Qty</span></strong></th>
					<th style='width:60px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Amount</span></strong></th>
					<th style='width:70px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Time</span></strong></th>
					</tr>
					".$bodydetails."</table><br /><br /><br />Powered By aceDNS";
			//echo $emp_name.'<br />-----------'.$body.'<br /><br /><br />';		
			
			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=UTF-8\n";
			$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
						"Reply-To:".FROMEMAIL." \r\n" .
						"Bcc: ".BCCEMAIL." \r\n" .
						'X-Mailer: PHP/' . phpversion();
			
			if(mail($DCR_email, $subject, $body, $headers,'-facedns@coral.in'))
				{
					$pointcoords='';
					unset($pointcoords);
					$customer_name_array='';
					unset($customer_name_array);
					$activity_array='';
					unset($activity_array);
					$total_qty_array='';
					unset($total_qty_array);
					$total_amount_array='';
					unset($total_amount_array);
					$trantime_array='';
					unset($trantime_array);
					$markersArr='';
					unset($markersArr);
					$pathpointsArr='';
					unset($pathpointsArr);
					$body='';
					$bodydetails='';
				}
		}//End of count if
	}//End of employee while loop
mysqli_close($link);
?>
