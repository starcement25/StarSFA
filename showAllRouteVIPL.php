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
		$sqlorder="SELECT OH.order_no, OH.customer_code FROM `order_header` OH, location LO
					WHERE LO.trans_id = OH.order_no GROUP BY OH.customer_code";
		$rsorder=mysqli_query($link,$sqlorder);
		$order_no_string='';
		$customer_code_string='';			
		while($roworder=mysqli_fetch_assoc($rsorder))
			{
				$order_no_string=$order_no_string.$roworder['order_no'].',';
				$customer_code_string=$customer_code_string.$roworder['customer_code'].',';
			}
		$order_no_string_final=substr($order_no_string,0,-1);
		$customer_code_string_final=substr($customer_code_string,0,-1);
		$customer_code_string_array=explode(',',$customer_code_string_final);
		$customer_code_string = "'".implode("','", $customer_code_string_array)."'";
		
		$sqlpayment="SELECT PH.receipt_id, PH.customer_code FROM `payment_header` PH, location LO
					WHERE LO.trans_id = PH.receipt_id AND PH.customer_code NOT IN (".$customer_code_string.")";
		$rspayment=mysqli_query($link,$sqlpayment);	
		while($rowpayment=mysqli_fetch_assoc($rspayment))
			{
				$order_no_string_final=$order_no_string_final.$rowpayment['receipt_id'].',';
				$customer_code_string_final=$customer_code_string_final.$rowpayment['customer_code'].',';
			}
		$order_no_string_final=substr($order_no_string,0,-1);
		$customer_code_string_final=substr($customer_code_string,0,-1);	
		$order_no_string_array=explode(',',$order_no_string_final);
		$order_no_string = "'".implode("','", $order_no_string_array)."'";
		
		//echo count($order_no_string_array);	
		
		//exit();	
		//echo strlen("http://maps.googleapis.com/maps/api/staticmap?size=700x600&path=color:0xff0000ff|weight:2|22.5,88.34|22.52,88.35|22.52,88.35|22.54,88.35|22.54,88.35|22.51,88.4|22.52,88.36|22.51,88.35|22.53,88.35|22.54,88.35|22.54,88.35|22.5,88.34|22.5,88.34|22.5,88.34|22.5,88.34|22.5,88.34|22.5,88.34|22.51,88.32|22.46,88.31|22.47,88.31|22.51,88.33|22.51,88.32|22.52,88.37|22.52,88.36|22.5,88.37|22.48,88.34|22.51,88.32|22.46,88.3|22.46,88.3|22.47,88.31|22.5,88.32|22.5,88.32|22.5,88.32|22.5,88.32|22.52,88.36|22.49,88.34|22.51,88.35|22.49,88.32|22.49,88.32|22.49,88.32|22.53,88.35|22.55,88.35|22.55,88.35|22.53,88.35|22.51,88.35|22.51,88.35|22.51,88.35|22.5,88.34|22.5,88.34&sensor=false&&markers=color:red||22.5,88.34&markers=color:red||22.52,88.35&markers=color:red||22.52,88.35&markers=color:red||22.54,88.35&markers=color:red||22.54,88.35&markers=color:red||22.51,88.4&markers=color:red||22.52,88.36&markers=color:red||22.51,88.35&markers=color:red||22.53,88.35&markers=color:red||22.54,88.35&markers=color:red||22.54,88.35&markers=color:red||22.5,88.34&markers=color:red||22.5,88.34&markers=color:red||22.5,88.34&markers=color:red||22.5,88.34&markers=color:red||22.5,88.34&markers=color:red||22.5,88.34&markers=color:red||22.51,88.32&markers=color:red||22.46,88.31&markers=color:red||22.47,88.31&markers=color:red||22.51,88.33&markers=color:red||22.51,88.32&markers=color:red||22.52,88.37&markers=color:red||22.52,88.36&markers=color:red||22.5,88.37&markers=color:red||22.48,88.34&markers=color:red||22.51,88.32&markers=color:red||22.46,88.3&markers=color:red||22.46,88.3&markers=color:red||22.47,88.31&markers=color:red||22.5,88.32&markers=color:red||22.5,88.32&markers=color:red||22.5,88.32&markers=color:red||22.5,88.32&markers=color:red||22.52,88.36&markers=color:red||22.49,88.34&markers=color:red||22.51,88.35&markers=color:red||22.49,88.32&markers=color:red||22.49,88.32&markers=color:red||22.49,88.32&markers=color:red||22.53,88.35&markers=color:red||22.55,88.35&markers=color:red||22.55,88.35&markers=color:red||22.53,88.35&markers=color:red||22.51,88.35&markers=color:red||22.51,88.35&markers=color:red||22.51,88.35&markers=color:red||22.5,88.34&markers=color:red||22.5,88.34");	
		
		$sqlpoint="SELECT latt, longi FROM location WHERE trans_id IN (".$order_no_string.") LIMIT 0,46";			
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
				$point=round($rowpoint['latt'],2).','.round($rowpoint['longi'],2);
				//$point=$rowpoint['latt'].','.$rowpoint['longi'];
				$markersArr[]="markers=color:red||$point";
				$markers=implode('&',$markersArr);
				//$pathpointsArr[]=$point;
				array_push($pathpointsArr,$point);
				$pathpoints=implode('|',$pathpointsArr);
				//$pathpoints=substr($pathpoints,1);
				array_push($pointcoords,$point);
				
				$pointlabel++;
			}
			//echo $markers;
			//echo $pathpoints;
			//print_r($pointcoords);
			/*$DCR_email=DCREMAILRECIPENTS;*/

			$DCR_email='';
			$datesubject=date('d-m-Y', strtotime("-1 days,$curdateserver "));
			$subject='VIPL 32 points plotted in staticmap';
			$body='<img src="http://maps.googleapis.com/maps/api/staticmap?size=700x600&path=color:0xff0000ff|weight:2'.$pathpoints.'&sensor=false&'.$markers.'" alt=""><br /> <br />';
			
			/*for($i=0;$i<count($pointcoords);$i++)
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
					".$bodydetails."</table><br /><br /><br />Powered By aceDNS";*/
			$body.="########### 32 points ######################";		
			echo $body;		
			
			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=UTF-8\n";
			$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
						"Reply-To:".FROMEMAIL." \r\n" .
						"Bcc: ".BCCEMAIL." \r\n" .
						'X-Mailer: PHP/' . phpversion();
			
			//if(mail($DCR_email, $subject, $body, $headers,'-facedns@coral.in'))
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
mysqli_close($link);
?>
