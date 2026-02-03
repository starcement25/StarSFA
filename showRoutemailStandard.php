	<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234");
	define("FROMEMAIL","acedns@acedns.in");
	define("FROMTAG","aceDNS");
	define("BCCEMAIL","dipankarc@coral.in,acedns@coral.in");
	//require("include/config-setup.php");
	
	$linksetupDCR=mysqli_connect(SERVER,USER,PASSWORD) or die("Setup Database Connection Error.");
	mysqli_select_db("acedns_acednsproduct",$linksetupDCR) or die("could not connect the setup database");
	
	$sqlDCRdetails="SELECT nick_name,branch_vertical_operation_wise_email FROM user_details WHERE need_DCR='yes' AND DCR_time >= (NOW() - INTERVAL 30 minute)
  					AND DCR_time  <= (NOW() + INTERVAL 30 minute) AND nick_name='JPHARMA'";
	$rsDCRdetails=mysqli_query($link,$sqlDCRdetails,$linksetupDCR);
	while($rowDCRdetails=mysqli_fetch_assoc($rsDCRdetails))
	{
		$nick_name_DCR[]=$rowDCRdetails['nick_name'];
		$branch_vertical_operation_wise_email[]=$rowDCRdetails['branch_vertical_operation_wise_email'];
	}
	$sqlmenudetails="SELECT route_plan FROM menu_details WHERE nick_name='JPHARMA'";
	$rsmenudetails=mysqli_query($link,$sqlmenudetails,$linksetupDCR);
	$rowmenudetails=mysqli_fetch_assoc($rsmenudetails);
	$route_plan=$rowmenudetails['route_plan'];

	mysqli_close($linksetupDCR);
	
	function connecttodb($servername,$dbname,$dbuser,$dbpassword)
	{
		$dbnamefinal="acedns_".$dbname;
		$link=mysqli_connect($servername,$dbuser,$dbpassword,TRUE) or die("Database Connection Error.");
		mysqli_select_db($dbnamefinal,$link) or die("could not connect the database for invalid nick name");
		return $link;
	}
	//print_r($nick_name_DCR);
	
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
	for($k=0;$k< count($nick_name_DCR);$k++)
	{
		${link.$k} = connecttodb(SERVER,$nick_name_DCR[$k],USER,PASSWORD);
	}
	function updateLatlong($emp_code,$link)
	{
		//For check and update in the location table
		$sqlselectlatlong="SELECT * FROM location WHERE emp_code='".$emp_code."' AND latt='0' AND longi='0'";
		$rsselectlatlong=mysqli_query($link,$sqlselectlatlong,$link) or die(mysqli_error()." Error in select zero latt longi: ".$sqlselectlatlong);
		$countselectlatlong=mysqli_num_rows($rsselectlatlong);
		
		if($countselectlatlong>0)
		{
			$sqllastlatlong="SELECT latt,longi FROM location WHERE emp_code='".$emp_code."' AND latt<>'0' AND longi<>'0' ORDER BY date DESC LIMIT 0,1";
			$rslastlatlong=mysqli_query($link,$sqllastlatlong,$link) or die(mysqli_error()." Error in select last not zero latt longi: ".$sqllastlatlong);
			$rowlastlatlong=mysqli_fetch_assoc($rslastlatlong);
			$lastlatt=$rowlastlatlong['latt'];
			$lastlongi=$rowlastlatlong['longi'];
			
			$sqlupdatelatlong="UPDATE location set latt='".$lastlatt."',longi='".$lastlongi."' WHERE emp_code='".$emp_code."' AND latt='0' AND longi='0'";
			$rsupdatelatlong=mysqli_query($link,$sqlupdatelatlong,$link) or die(mysqli_error()." Error in update zero latt longi: ".$sqlupdatelatlong);
		}
	}
	function fetch_corresponding_emails($operation_type,$area,$branch_code,$link)
	{
		$correspondingemails='';
		
		$sqlfetchemails="SELECT mail_id FROM mail_access WHERE FIND_IN_SET( '".$branch_code."', branch_code ) >0 AND 
						FIND_IN_SET( '".$operation_type."', attributes ) >0 AND FIND_IN_SET( '".$area."', area ) >0";
		$rsfetchemails=mysqli_query($link,$sqlfetchemails,$link) or die(mysqli_error().'Error in mail id fetch.');
		while($rowfetchemails=mysqli_fetch_assoc($rsfetchemails))
		{
			$correspondingemails=$correspondingemails.$rowfetchemails['mail_id'].',';
		}
		$correspondingemails=substr($correspondingemails,0,-1);
		return $correspondingemails;
	}
	
	for($DCRcount=0;$DCRcount< count($nick_name_DCR);$DCRcount++){
		$nick_name=$nick_name_DCR[$DCRcount];
		if($nick_name=='AMPL' || $nick_name=='TT')
		{
		  $spam_filter='-facedns@coral.in';
		}
		else
		{
		  $spam_filter='-facedns@acedns.in';
		}
		${branch_vertical_operation_wise_email.$DCRcount}=$branch_vertical_operation_wise_email[$DCRcount];
		$sqlemail="SELECT admin_email_id,account_email_id FROM company_master WHERE comp_name='".$nick_name_DCR[$DCRcount]."'";
		$rsemail=mysqli_query($link,$sqlemail,${link.$DCRcount});
		$rowemail=mysqli_fetch_assoc($rsemail);
		
		$admin_email_id=$rowemail['admin_email_id'];
		$account_email_id=$rowemail['account_email_id'];
		$corresponding_emails=$admin_email_id.','.$account_email_id;
		
		$sqlemployee="SELECT emp_code,emp_name,branch_code,vertical_value FROM employee_master WHERE emp_code !='C0007'";
		$rsemployee=mysqli_query($link,$sqlemployee,${link.$DCRcount});
		$curdateserver=gmdate('Y-m-d',strtotime('+329 minute'));
		$dateprevious=date('Y-m-d', strtotime("-1 days,$curdateserver "));
		//$dateprevious=date('Y-m-d');
		while($rowemployee=mysqli_fetch_assoc($rsemployee))
		{
			$emp_code=$rowemployee['emp_code'];
			$emp_name=$rowemployee['emp_name'];
			$branch_code=$rowemployee['branch_code'];
			$vertical_value=$rowemployee['vertical_value'];
			$operation_type_dcr='DCR';
			updateLatlong($emp_code,${link.$DCRcount});
			
			/*$sqlpoint="SELECT latt, longi,trans_id,DATE_FORMAT(date,'%H:%i:%s') as trantime FROM location WHERE emp_code ='".$emp_code."' AND 
						DATE LIKE '%".$dateprevious."%' ORDER BY DATE_FORMAT(DATE,'%Y-%m-%d %H:%i:%s') ASC ";*/
			$sqlpoint="(SELECT latt, longi,trans_id,DATE_FORMAT(date,'%H:%i:%s') as trantime FROM location WHERE emp_code ='".$emp_code."' AND 
						DATE LIKE '%".$dateprevious."%' AND (SUBSTRING(trans_id,1,1) IN('A','O','P','D') OR SUBSTRING(trans_id,1,2) IN('NO','NC')) ORDER BY DATE_FORMAT(date,'%Y-%m-%d %H:%i:%s') ASC)";			
			$rspoint=mysqli_query($link,$sqlpoint,${link.$DCRcount});
			$countpoint=mysqli_num_rows($rspoint);
			$pointcoords=array();
			$customer_name_array=array();
			$activity_array=array();
			$total_qty_array=array();
			$total_amount_array=array();
			$trantime_array=array();
			$pathpointsArr=array();
			$order_value_array=array();
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
						$order_value='----';
					}
					if($operation_type=='O')
						{
							$activity='Order';
							$order_no=$trans_id;
							$sqlcustomer="SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_order_received,SUM(amount) AS order_value FROM 
										  order_header OH,customer_master CM,order_details OD
										  WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."' 
										  AND OH.order_no=OD.order_no GROUP BY OD.order_no
										  UNION
										  SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_order_received,SUM(amount) AS order_value FROM 
										  order_header OH,prospective_customer_master CM,order_details OD
										  WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."' 
										  AND OH.order_no=OD.order_no GROUP BY OD.order_no";
							$rscustomer=mysqli_query($link,$sqlcustomer,${link.$DCRcount}) or die(mysqli_error()." Error in select customer: ".$sqlcustomer);
							
							$rowcustomer=mysqli_fetch_assoc($rscustomer);
							$customer_name=$rowcustomer['customer_name'];
							$order_value=$rowcustomer['order_value'];
							$total_qty=$rowcustomer['total_order_received'];
							$total_order_value=$total_order_value+$order_value;
							$total_amount='----';
							$order_value=$order_value;
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
							$rscustomerpayment=mysqli_query($link,$sqlcustomerpayment,${link.$DCRcount}) or die(mysqli_error()." Error in select customer payment: ".$sqlcustomerpayment);
							
							$rowcustomerpayment=mysqli_fetch_assoc($rscustomerpayment);
							$customer_name=$rowcustomerpayment['customer_name'];
							$total_amount_rawdata=$total_amount_rawdata+$rowcustomerpayment['total_collection_received'];
							$total_amount='Rs/- '.number_format($rowcustomerpayment['total_collection_received'],2);
							$total_qty='----';
							$order_value='----';
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
									$rscustomernoorder=mysqli_query($link,$sqlcustomernoorder,${link.$DCRcount}) or die(mysqli_error()." Error in select customer for no order: ".$sqlcustomernoorder);
									$rowcustomernoorder=mysqli_fetch_assoc($rscustomernoorder);
							
									$customer_name=$rowcustomernoorder['customer_name'];
									$total_amount='----';
									$total_qty='----';
									$order_value='----';
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
								$rscustomernocollection=mysqli_query($link,$sqlcustomernocollection,${link.$DCRcount}) or die(mysqli_error()." 
															Error in select customer for no collection: ".$sqlcustomernocollection);
								$rowcustomernocollection=mysqli_fetch_assoc($rscustomernocollection);
								
								$customer_name=$rowcustomernocollection['customer_name'];
								$total_amount='----';
								$total_qty='----';
								$order_value='----';
							}
						}
						if($operation_type=='D')
						{
							$operation_type_no=substr($trans_id,1,1);
							$sqlprospective="SELECT PCH.customer_name FROM prospective_customer_header PCH WHERE PCH.trans_id='".$trans_id."'";
							$rsprospective=mysqli_query($link,$sqlprospective,${link.$DCRcount}) or die(mysqli_error()." 
											Error in select prospective customer or mechanic: ".$sqlprospective);
							$rowprospective=mysqli_fetch_assoc($rsprospective);
							$customer_name=$rowprospective['customer_name'];
							if($operation_type_no=='M')
								{
									$activity='Visit Mechanic';
									$total_amount='----';
									$total_qty='----';
									$order_value='----';
								}
							if($operation_type_no=='C')
								{
									$activity='Visit Customer';
									$total_amount='----';
									$total_qty='----';
									$order_value='----';
								}	
						}
						
						array_push($customer_name_array,$customer_name);
						array_push($trantime_array,$time);
						array_push($total_qty_array,$total_qty);
						array_push($total_amount_array,$total_amount);
						array_push($activity_array,$activity);
						array_push($order_value_array,$order_value);
			
					$pointlabel++;
				}
				//echo $markers;
				//echo $pathpoints;
				//print_r($pointcoords);
				
				if(${branch_vertical_operation_wise_email.$DCRcount}=='yes')
				{
					$operation_type='DCR';
					$DCR_email=fetch_corresponding_emails($operation_type,$vertical_value,$branch_code,${link.$DCRcount});
					if($nick_name=='AMPL')
					{
						$DCR_email=$DCR_email.',rajuyk@automotiveml.com,vaman@automotiveml.com';
					}
				}
				else
				{
					$DCR_email=$corresponding_emails;
					if($nick_name=='TT')
					{
						$DCR_email=$DCR_email.',sjain@tttextiles.com,jyoti@tttextiles.com,manojkalra@tttextiles.com,bcjain@tttextiles.com';
					}
					if($nick_name=='LALANI')
					{ 
						$DCR_email=$DCR_email.',ritesh@lalaniinfotech.in';	
					}
					if($nick_name=='NAPTC')
					{
						$DCR_email=$DCR_email.',rajesh@purbanchal.in';
					}
					if($nick_name=='JPHARMA')
					{
						$DCR_email='shankar.mehadia@microparkindia.com,nikunj.mehadia@microparkindia.com,shreyas.mehadia@microparkindia.com,hrd@microparkindia.com';
					}
				}
				
				//$DCR_email='kuntald@coral.in';
				//echo $DCR_email;
				$datesubject=date('d-m-Y', strtotime("-1 days,$curdateserver "));
				$subject='DCR of '.$emp_name.' on '.$datesubject.' for '.$nick_name;
				if($route_plan=='yes')
				{
					$visit_date=date('Y-m-d', strtotime($datesubject));
					$sqlrouteplandetails="SELECT GROUP_CONCAT(DISTINCT RM.route_name SEPARATOR ',') AS route_name_details FROM route_plan RP,route_master RM 
										WHERE RP.route_code=RM.route_code AND RP.visit_date ='".$visit_date."' AND RP.emp_code='".$emp_code."'";
					$rsrouteplandetails=mysqli_query($link,$sqlrouteplandetails);
					$rowrouteplandetails=mysqli_fetch_assoc($rsrouteplandetails);
					$route_name_details=$rowrouteplandetails['route_name_details'];				
					$routeplantext="<br /><tr><th style='min-height:21px;text-align:left;' colspan=9><strong>
						<span style='font-size:10pt;font-family:Arial CE'>Submitted Route Plan:&nbsp;".$route_name_details."</span></strong></th></tr>";
				}
				else
				{
					$routeplantext='';
				}
				$body="<table>
						<tr>
						<th style='min-height:21px;text-align:center;' colspan='4'><strong>
						<span style='font-size:10pt;font-family:Arial CE'>DCR:&nbsp;".$emp_name."</span></strong></th>
						<th style='width:70px;min-height:21px;text-align:center'><strong>
						<span style='font-size:10pt;font-family:Arial CE'>&nbsp;</span></strong></th>
						<th style='width:60px;min-height:21px;text-align:center'><strong>
						<span style='font-size:10pt;font-family:Arial CE'>&nbsp;</span></strong></th>
						<th style='min-height:21px;text-align:right;' colspan='2'><strong>
						<span style='font-size:10pt;font-family:Arial CE'>DATE:&nbsp;".$datesubject."</span></strong></th>
						</tr>".$routeplantext."</table><br />";
				$body.='<img src="http://maps.googleapis.com/maps/api/staticmap?size=700x600&path=color:0xff0000ff|weight:2'.$pathpoints.'&sensor=false&'.$markers.'" alt=""><br /> <br />';
				
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
								    <span style='font-family:Arial CE;font-size:10pt'>".@number_format($order_value_array[$i],2)."</span>&nbsp;</td>
									<td style='width:60px;text-align:center;min-height:21px;background-color:white'>
									<span style='font-family:Arial CE;font-size:10pt'>".$total_amount_array[$i]."</span>&nbsp;</td>
									<td style='width:70px;text-align:center;min-height:21px;background-color:white'>
									<span style='font-family:Arial CE;font-size:10pt'>".$trantime_array[$i]."</span>&nbsp;</td>			
								  </tr>";
				   $total_qty_val=$total_qty_val+$total_qty_array[$i];				  
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
						<span style='font-size:10pt;font-family:Arial CE'>Order Value</span></strong></th>
						<th style='width:60px;min-height:21px;text-align:center'><strong>
						<span style='font-size:10pt;font-family:Arial CE'>Amount</span></strong></th>
						<th style='width:70px;min-height:21px;text-align:center'><strong>
						<span style='font-size:10pt;font-family:Arial CE'>Time</span></strong></th>
						</tr>
						".$bodydetails."<tr><td style='width:50px;min-height:21px;text-align:center' colspan='5'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Total</span></strong></td><td style='width:60px;text-align:center;min-height:21px;background-color:white'>
					<span style='font-family:Arial CE;font-size:10pt'>".$total_qty_val."</span>&nbsp;</td>
					<td style='width:60px;text-align:center;min-height:21px;background-color:white'>
					<span style='font-family:Arial CE;font-size:10pt'>Rs/- ".number_format($total_order_value,2)."</span>&nbsp;</td>
					<td style='width:60px;text-align:center;min-height:21px;background-color:white'>
					<span style='font-family:Arial CE;font-size:10pt'>Rs/- ".number_format($total_amount_rawdata,2)."</span>&nbsp;</td><td style='width:70px;text-align:center;min-height:21px;background-color:white'>
					<span style='font-family:Arial CE;font-size:10pt'></span>&nbsp;</td></tr></table><br /><br /><br />Powered By aceDNS";
				//echo $emp_name.'<br />-----------'.$body.'<br /><br /><br />';		
				
				$headers  = "MIME-Version: 1.0\r\n";
				$headers .= "Content-type: text/html; charset=UTF-8\n";
				$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
							"Reply-To:".FROMEMAIL." \r\n" .
							"Bcc: ".BCCEMAIL." \r\n" .
							'X-Mailer: PHP/' . phpversion();
				
				if(mail($DCR_email, $subject, $body, $headers,$spam_filter))
					{
						$pointcoords='';
						unset($pointcoords);
						$customer_name_array='';
						unset($customer_name_array);
						$activity_array='';
						unset($activity_array);
						$total_qty_array='';
						unset($total_qty_array);
						$order_value_array='';
						unset($order_value_array);
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
						$total_qty_val='';
						$total_order_value='';
						$total_amount_rawdata='';
					}
			}//End of count if
		}//End of employee while loop
	mysqli_close(${link.$DCRcount});
}
?>
