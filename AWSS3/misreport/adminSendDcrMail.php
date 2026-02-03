<?php
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");
	require("include/functions.php");
	require("include/config-email-setup.php");

	$emp_code=$_REQUEST['emp_code'];
	
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
	function updateLatlong($emp_code)
	{
		//For check and update in the location table
		$sqlselectlatlong="SELECT * FROM location WHERE emp_code='".$emp_code."' AND latt='0' AND longi='0'";
		$rsselectlatlong=mysql_query($sqlselectlatlong) or die(mysql_error()." Error in select zero latt longi: ".$sqlselectlatlong);
		$countselectlatlong=mysql_num_rows($rsselectlatlong);
		
		if($countselectlatlong>0)
		{
			$sqllastlatlong="SELECT latt,longi FROM location WHERE emp_code='".$emp_code."' AND latt<>'0' AND longi<>'0' ORDER BY date DESC LIMIT 0,1";
			$rslastlatlong=mysql_query($sqllastlatlong) or die(mysql_error()." Error in select last not zero latt longi: ".$sqllastlatlong);
			$rowlastlatlong=mysql_fetch_array($rslastlatlong);
			$lastlatt=$rowlastlatlong['latt'];
			$lastlongi=$rowlastlatlong['longi'];
			
			$sqlupdatelatlong="UPDATE location set latt='".$lastlatt."',longi='".$lastlongi."' WHERE emp_code='".$emp_code."' AND latt='0' AND longi='0'";
			$rsupdatelatlong=mysql_query($sqlupdatelatlong) or die(mysql_error()." Error in update zero latt longi: ".$sqlupdatelatlong);
		}
	}
	/*function fetch_corresponding_emails($operation_type,$area,$branch_code)
	{
		$correspondingemails='';
		
		$sqlfetchemails="SELECT mail_id FROM mail_access WHERE FIND_IN_SET( '".$branch_code."', branch_code ) >0 AND 
						FIND_IN_SET( '".$operation_type."', attributes ) >0 AND FIND_IN_SET( '".$area."', area ) >0";
		$rsfetchemails=mysql_query($sqlfetchemails) or die(mysql_error().'Error in mail id fetch.');
		while($rowfetchemails=mysql_fetch_array($rsfetchemails))
		{
			$correspondingemails=$correspondingemails.$rowfetchemails['mail_id'].',';
		}
		$correspondingemails=substr($correspondingemails,0,-1);
		return $correspondingemails;
	}*/
	$sqlempname="SELECT emp_name,branch_code,vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsempname=mysql_query($sqlempname);
	$rowempname=mysql_fetch_array($rsempname);
	$emp_name=$rowempname['emp_name'];
	$branch_code=$rowempname['branch_code'];
	$vertical_value=$rowempname['vertical_value'];

		if(need_DCR=='yes')
		{
		$operation_type_dcr='DCR';
		//$curdateserver=gmdate('Y-m-d',strtotime('+330 minute'));
		$dateprevious=date('Y-m-d', strtotime($_REQUEST['date']));
		if(DCR_map=='no')
		{
			updateLatlong($emp_code);
		}
		$sqlpoint="(SELECT latt, longi,trans_id,DATE_FORMAT(date,'%H:%i:%s') as trantime FROM location WHERE emp_code ='".$emp_code."' AND 
					DATE LIKE '%".$dateprevious."%' AND (SUBSTRING(trans_id,1,1) IN('A','O','P','D') OR SUBSTRING(trans_id,1,2) IN('NO','NC')) ORDER BY DATE_FORMAT(date,'%Y-%m-%d %H:%i:%s') ASC)";
		$rspoint=mysql_query($sqlpoint);
		$countpoint=mysql_num_rows($rspoint);
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
			$total_amount_rawdata_array=array();
			while($rowpoint=mysql_fetch_array($rspoint))
			{
				$point=$rowpoint['latt'].','.$rowpoint['longi'];
				//$markersArr[]="markers=color:red|label:$pointlabel|$point";
				if($pointlabel==1 || $countpoint==$pointlabel){
					$markersArr[]="markers=color:green|label:|$point";
				}
				else 
				{
					$markersArr[]="markers=color:red|label:|$point";
				}
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
						/*if(mrp=='yes'){ $mrp_salerate_condition=" SUM(OD.qty*MRP.mrp) AS order_value";}
						if(sale_rate=='yes' && sale_rate_input_dropdown=='dropdown'){$mrp_salerate_condition=" SUM(OD.qty*MRP.sale_rate) AS order_value";}*/
						$sqlcustomer="SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_order_received,SUM(amount) AS order_value FROM 
									  order_header OH,customer_master CM,order_details OD
									  WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."' 
									  AND OH.order_no=OD.order_no GROUP BY OD.order_no
									  UNION
									  SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_order_received,SUM(amount) AS order_value FROM 
									  order_header OH,prospective_customer_master CM,order_details OD
									  WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."' 
									  AND OH.order_no=OD.order_no GROUP BY OD.order_no";
						$rscustomer=mysql_query($sqlcustomer) or die(mysql_error()." Error in select customer: ".$sqlcustomer);
						
					   /*if(mrp=='yes' || (sale_rate=='yes' && sale_rate_input_dropdown=='dropdown')){
							$sqlordervalue="SELECT SUM(am FROM order_details OD,mrp MRP WHERE OD.sku_code=MRP.product_code 
											AND OD.order_no='".$order_no."' GROUP BY OD.order_no";
							$rsordervalue=mysql_query($sqlordervalue);
							$rowordervalue=mysql_fetch_array($rsordervalue);
							$order_value=$rowordervalue['order_value'];
						//exit();	
						}
						else
						{
							$order_value=0;
						}*/
						$rowcustomer=mysql_fetch_array($rscustomer);
						$order_value=$rowcustomer['order_value'];
						$customer_name=$rowcustomer['customer_name'];
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
						$rscustomerpayment=mysql_query($sqlcustomerpayment) or die(mysql_error()." Error in select customer payment: ".$sqlcustomerpayment);
						
						$rowcustomerpayment=mysql_fetch_array($rscustomerpayment);
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
							$rscustomernoorder=mysql_query($sqlcustomernoorder) or die(mysql_error()." Error in select customer for no order: ".$sqlcustomernoorder);
							$rowcustomernoorder=mysql_fetch_array($rscustomernoorder);
					
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
							$rscustomernocollection=mysql_query($sqlcustomernocollection) or die(mysql_error()." 
														Error in select customer for no collection: ".$sqlcustomernocollection);
							$rowcustomernocollection=mysql_fetch_array($rscustomernocollection);
							
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
						$rsprospective=mysql_query($sqlprospective) or die(mysql_error()." 
										Error in select prospective customer or mechanic: ".$sqlprospective);
						$rowprospective=mysql_fetch_array($rsprospective);
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
			if(branch_vertical_operation_wise_email=='yes')
			{
				$operation_type='DCR';
				$DCR_email=fetch_corresponding_emails($operation_type,$vertical_value,$branch_code);
				if($nick_name=='AMPL')
				{
					$DCR_email=$DCR_email.',rajuyk@automotiveml.com,vaman@automotiveml.com';
				}
			}
			else
			{
				if(email_hierarchywise=='yes')
				{
					if($nick_name=='STAR')
					{
						$DCR_email='';
					}
					else
					{	
					 $DCR_email=$email_hierarchy.','.$corresponding_emails;
					} 
				}
				else
				{
					$DCR_email=$corresponding_emails;
				}
				if($nick_name=='TT')
				{
					$DCR_email=$DCR_email.',sjain@tttextiles.com,jyoti@tttextiles.com,manojkalra@tttextiles.com,bcjain@tttextiles.com,yogesh@tttextiles.com';
				}
				if($nick_name=='LALANI')
				{ 
					$DCR_email=$DCR_email.',ritesh@lalaniinfotech.in';	
				}
				if($nick_name=='NAPTC')
				{
					$DCR_email=$DCR_email.',rajesh@purbanchal.in';
				}
			}
			//echo $DCR_email;
			//$DCR_email='dipankarc@coral.in';
			$datesubject=date('d-m-Y', strtotime($_REQUEST['date']));
			if($nick_name=='RUPA')
			{
				$subject='DSR of '.$emp_name.' on '.$datesubject.' for '.$nick_name;
				$bodytext='DSR';
			}
			else
			{
				$subject='DCR of '.$emp_name.' on '.$datesubject.' for '.$nick_name;
				$bodytext='DCR';
			}
			if(route_plan=='yes')
			{
				
				$visit_date=date('Y-m-d', strtotime($datesubject));
				$sqlrouteplandetails="SELECT GROUP_CONCAT(DISTINCT RM.route_name SEPARATOR ',') AS route_name_details FROM route_plan RP,route_master RM 
									WHERE RP.route_code=RM.route_code AND RP.visit_date ='".$visit_date."' AND RP.emp_code='".$emp_code."'";
				$rsrouteplandetails=mysql_query($sqlrouteplandetails);
				$rowrouteplandetails=mysql_fetch_array($rsrouteplandetails);
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
					<span style='font-size:10pt;font-family:Arial CE'>".$bodytext.":&nbsp;".$emp_name."</span></strong></th>
					<th style='width:70px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>&nbsp;</span></strong></th>
					<th style='width:60px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>&nbsp;</span></strong></th>
					<th style='min-height:21px;text-align:right;' colspan='2'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>DATE:&nbsp;".$datesubject."</span></strong></th>
					</tr>".$routeplantext."</table><br />";
			if(DCR_map=='yes'){		
			$body.='<img src="https://maps.googleapis.com/maps/api/staticmap?size=700x600&path=color:0xff0000ff|weight:2'.$pathpoints.'&sensor=false&'.$markers.'&key=AIzaSyBhJB9maJFpMdTZ_JXAbB7HBX4H8oDFURo" alt=""><br /> <br />';
			}
			for($i=0;$i<count($pointcoords);$i++)
			{
				$point=($i+1);
				
				$Arrpointcoordsfrom =explode(',',$pointcoords[$i]);
				$latitudeFromaddress=$Arrpointcoordsfrom[0];
				$longitudeFromaddress=$Arrpointcoordsfrom[1];
				
				//$address=getReverseGeo($latitudeFrom,$longitudeFrom);
				
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
				/*if(DCR_map=='yes')
				{
					$address=getReverseGeo($latitudeFromaddress,$longitudeFromaddress);
					$bodydetailsaddress="<td style='width:150px;text-align:left;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$address."</span>&nbsp;</td>";
				}
				else $bodydetailsaddress="";*/
				$bodydetailsaddress="";
				$bodydetails.="<tr><td style='width:50px;text-align:left;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$point."</span>&nbsp;</td>	
								<td style='width:100px;text-align:left;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$customer_name_array[$i]."</span>&nbsp;</td>".$bodydetailsaddress."	
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
			/*if(DCR_map=='yes')
			{
				$bodyaddress="<th style='width:150px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Address</span></strong></th>";
			}
			else $bodyaddress="";*/
			$bodyaddress="";
			$body.="<table border=1 style=background-color:AliceBlue>
					<tr>
					<th style='width:50px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Point</span></strong></th>
					<th style='width:100px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Customer Name</span></strong></th>".$bodyaddress."
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
			
			$headersdcr  = "MIME-Version: 1.0\r\n";
			$headersdcr .= "Content-type: text/html; charset=UTF-8\n";
			$headersdcr .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
						"Reply-To:".FROMEMAIL." \r\n" .
						"Bcc: ".BCCEMAIL." \r\n" .
						'X-Mailer: PHP/' . phpversion();
			
			if(mail($DCR_email, $subject, $body, $headersdcr,$spam_filter))
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
				}
		}//End of count if
		echo $emp_name;
	}
?>
