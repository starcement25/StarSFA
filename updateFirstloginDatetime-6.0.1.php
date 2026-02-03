<?php
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");
	require("include/config-email-setup.php");

	$emp_code=$_REQUEST['emp_code'];
	$date_time=$_REQUEST['downloaddate'];
	$date_time=str_replace('_',' ',$date_time);
	$device_id=$_REQUEST['device_id'];
	$days_first_login=$_REQUEST['days_first_login'];
	$db_version=$_REQUEST['db_version'];
	
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
			$rsupdatelatlong=mysqli_query($link,$sqlupdatelatlong) or die(mysqli_error()." Error in update zero latt longi: ".$sqlupdatelatlong);
		}
	}
	/*function fetch_corresponding_emails($operation_type,$area,$branch_code)
	{
		$correspondingemails='';
		
		$sqlfetchemails="SELECT mail_id FROM mail_access WHERE FIND_IN_SET( '".$branch_code."', branch_code ) >0 AND 
						FIND_IN_SET( '".$operation_type."', attributes ) >0 AND FIND_IN_SET( '".$area."', area ) >0";
		$rsfetchemails=mysqli_query($link,$sqlfetchemails) or die(mysqli_error().'Error in mail id fetch.');
		while($rowfetchemails=mysqli_fetch_assoc($rsfetchemails))
		{
			$correspondingemails=$correspondingemails.$rowfetchemails['mail_id'].',';
		}
		$correspondingemails=substr($correspondingemails,0,-1);
		return $correspondingemails;
	}*/
	$sqlempname="SELECT emp_name,branch_code,vertical_value,reporting_to FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsempname=mysqli_query($link,$sqlempname);
	$rowempname=mysqli_fetch_assoc($rsempname);
	$emp_name=$rowempname['emp_name'];
	$branch_code=$rowempname['branch_code'];
	$vertical_value=$rowempname['vertical_value'];
	$reporting_to_immediate=$rowempname['reporting_to'];
			
	if($days_first_login=='yes')
	{
		$sqlselect="SELECT loggedin_date_time FROM changepassword  WHERE emp_code='".$emp_code."'";
		$rsselect=mysqli_query($link,$sqlselect);
		$count=mysqli_num_rows($rsselect);
		$rowselect=mysqli_fetch_assoc($rsselect);
		
		$loggedin_date_time_database=substr($rowselect['loggedin_date_time'],0,10);
		$loggedin_date_time_request=substr($date_time,0,10);
		
		if($count>0 && $loggedin_date_time_database!=$loggedin_date_time_request)
		{
			$sqlUpdate="UPDATE changepassword SET
						loggedin_date_time='".$date_time."'
						WHERE emp_code='".$emp_code."'";
			if(mysqli_query($link,$sqlUpdate))
			{
				echo "1";
			}
			else
			{
				echo "0";
			}
		}
		else
		{
			echo "0";
		}
		if(need_DCR=='yes' && $nick_name!='JPHARMA' && DCR_checkout=='no')
		{
		$operation_type_dcr='DCR';
		$curdateserver=gmdate('Y-m-d',strtotime('+330 minute'));
		$dateprevious=date('Y-m-d', strtotime("-1 days,$curdateserver "));
		updateLatlong($emp_code);
		$sqlpoint="(SELECT latt, longi,trans_id,DATE_FORMAT(date,'%H:%i:%s') as trantime FROM location WHERE emp_code ='".$emp_code."' AND 
					DATE LIKE '%".$dateprevious."%' AND (SUBSTRING(trans_id,1,1) IN('A','O','P','D') OR SUBSTRING(trans_id,1,2) IN('NO','NC')) ORDER BY DATE_FORMAT(date,'%Y-%m-%d %H:%i:%s') ASC)";			
		$rspoint=mysqli_query($link,$sqlpoint);
		$countpoint=mysqli_num_rows($rspoint);
		$pointcoords=array();
		$customer_name_array=array();
		$d_instruction_array=array();
		$activity_array=array();
		$total_qty_array=array();
		$total_amount_array=array();
		$trantime_array=array();
		$pathpointsArr=array();
		$order_value_array=array();
		$markersArr[]='';
		$pathpointsArr[]='';
		$pointlabel=1;
		$sqlnotes="(SELECT latt, longi,trans_id,DATE_FORMAT(date,'%H:%i:%s') as trantime FROM location WHERE emp_code ='".$emp_code."' AND 
					DATE LIKE '%".$dateprevious."%' AND SUBSTRING(trans_id,1,2) IN('NI') ORDER BY DATE_FORMAT(date,'%Y-%m-%d %H:%i:%s') ASC)";
		$rsnotes=mysqli_query($link,$sqlnotes);
		$countnotes=mysqli_num_rows($rsnotes);				
		if($countpoint>0 || $countnotes > 0)
		{
			$total_amount_rawdata_array=array();
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
				if($operation_type=='A' && $operation_type!='N')
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
						$sqlcustomer="SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_order_received,SUM(amount) AS order_value,
										OH.d_instruction FROM order_header OH,customer_master CM,order_details OD
									  WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."' 
									  AND OH.order_no=OD.order_no GROUP BY OD.order_no
									  UNION
									  SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_order_received,SUM(amount) AS order_value,
									  OH.d_instruction FROM 
									  order_header OH,prospective_customer_master CM,order_details OD
									  WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."' 
									  AND OH.order_no=OD.order_no GROUP BY OD.order_no";
						$rscustomer=mysqli_query($link,$sqlcustomer) or die(mysqli_error()." Error in select customer: ".$sqlcustomer);
						
						/*if(mrp=='yes' || (sale_rate=='yes' && sale_rate_input_dropdown=='dropdown')){
							$sqlordervalue="SELECT '".$mrp_salerate_condition."' FROM order_details OD,mrp MRP WHERE OD.sku_code=MRP.product_code 
											AND OD.order_no='".$order_no."' GROUP BY OD.order_no";
							$rsordervalue=mysqli_query($link,$sqlordervalue);
							$rowordervalue=mysqli_fetch_assoc($rsordervalue);
							$order_value=$rowordervalue['order_value'];
						}
						else
						{
							$order_value=0;
						}*/
						$rowcustomer=mysqli_fetch_assoc($rscustomer);
						$customer_name=$rowcustomer['customer_name'];
						$order_value=$rowcustomer['order_value'];
						$total_qty=$rowcustomer['total_order_received'];
						$total_order_value=$total_order_value+$order_value;

						$total_amount='----';
						$order_value=$order_value;
						$d_instruction=$rowcustomer['d_instruction'];
					}
					if($operation_type=='P')
					{
						$activity='Payment';
						$receipt_id=$trans_id;
						$sqlcustomerpayment="SELECT CM.customer_name,CM.customer_code,SUM(PD.amount) AS total_collection_received,
						                    PH.d_instruction FROM payment_header PH,customer_master CM,payment_details PD
											WHERE CM.customer_code=PH.customer_code AND PH.receipt_id=PD.receipt_id AND 
											PH.receipt_id='".$receipt_id."' GROUP BY PD.receipt_id
											UNION
											SELECT CM.customer_name,CM.customer_code,SUM(PD.amount) AS total_collection_received,PH.d_instruction 
											FROM payment_header PH,prospective_customer_master CM,payment_details PD
											WHERE CM.customer_code=PH.customer_code AND PH.receipt_id=PD.receipt_id 
											AND PH.receipt_id='".$receipt_id."' GROUP BY PD.receipt_id";
						$rscustomerpayment=mysqli_query($link,$sqlcustomerpayment) or die(mysqli_error()." Error in select customer payment: ".$sqlcustomerpayment);
						
						$rowcustomerpayment=mysqli_fetch_assoc($rscustomerpayment);
						$customer_name=$rowcustomerpayment['customer_name'];
						$total_amount_rawdata=$total_amount_rawdata+$rowcustomerpayment['total_collection_received'];
						$total_amount='Rs/- '.number_format($rowcustomerpayment['total_collection_received'],2);
						$total_qty='----';
						$order_value='----';
						$d_instruction=$rowcustomerpayment['d_instruction'];
					}
					if($operation_type=='N')
					{
						$operation_type_no=substr($trans_id,1,1);
						if($operation_type_no=='O')
						{
							$activity='No Order';
							$order_no=$trans_id;
							$sqlcustomernoorder="SELECT CM.customer_name,CM.customer_code,OH.d_instruction FROM order_header OH,customer_master CM 
												WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."'
												UNION
												SELECT CM.customer_name,CM.customer_code,OH.d_instruction FROM order_header OH,prospective_customer_master CM 
												WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."'";
							$rscustomernoorder=mysqli_query($link,$sqlcustomernoorder) or die(mysqli_error()." Error in select customer for no order: ".$sqlcustomernoorder);
							$rowcustomernoorder=mysqli_fetch_assoc($rscustomernoorder);

					
							$customer_name=$rowcustomernoorder['customer_name'];
							$total_amount='----';
							$total_qty='----';
							$order_value='----';
							$d_instruction=$rowcustomernoorder['d_instruction'];
						}
						if($operation_type_no=='C')
						{
							$activity='No Collection';
							$receipt_id=$trans_id;
							$sqlcustomernocollection="SELECT CM.customer_name,CM.customer_code,PH.d_instruction FROM payment_header PH,customer_master CM 
													   WHERE CM.customer_code=PH.customer_code AND PH.receipt_id='".$receipt_id."'
													   UNION
													   SELECT CM.customer_name,CM.customer_code,PH.d_instruction FROM payment_header PH,prospective_customer_master CM 
													   WHERE CM.customer_code=PH.customer_code AND PH.receipt_id='".$receipt_id."'";
							$rscustomernocollection=mysqli_query($link,$sqlcustomernocollection) or die(mysqli_error()." 
														Error in select customer for no collection: ".$sqlcustomernocollection);
							$rowcustomernocollection=mysqli_fetch_assoc($rscustomernocollection);
							
							$customer_name=$rowcustomernocollection['customer_name'];
							$total_amount='----';
							$total_qty='----';
							$order_value='----';
							$d_instruction=$rowcustomernocollection['d_instruction'];
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
					array_push($d_instruction_array,$d_instruction);
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
				  if(email_hierarchy_level==1)
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
				$DCR_email=$DCR_email.',sjain@ttlimited.co.in,jyoti@ttlimited.co.in,manojkalra@ttlimited.co.in,bcjain@ttlimited.co.in,yogesh@ttlimited.co.in';
				}
				if($nick_name=='LALANI')
				{ 
					$DCR_email=$DCR_email.',ritesh@lalaniinfotech.in';	
				}
				if($nick_name=='NAPTC')
				{
					$DCR_email=$DCR_email.',rajesh@purbanchal.in';
				}
				if($nick_name=='PARLE')
				{
					$sqlimmediatemail="SELECT email FROM employee_master WHERE FIND_IN_SET(emp_code, '".$reporting_to_immediate."')";
					$rsimmediatemail=mysqli_query($link,$sqlimmediatemail);
					$rowimmediatemail=mysqli_fetch_assoc($rsimmediatemail);
					$mail_immediate=$rowimmediatemail['email'];
					
					$DCR_email=$mail_immediate;
				}
			}
			
			//$DCR_email='kuntald@coral.in';
			$datesubject=date('d-m-Y', strtotime("-1 days,$curdateserver "));
			if($nick_name=='RUPA')
			{
				$subject='DSR of '.$emp_name.' on '.$datesubject.' for '.$nick_name;
			}
			else
			{
				$subject='DCR of '.$emp_name.' on '.$datesubject.' for '.$nick_name;
			}
			if($nick_name=='RUPA')
			{
				$bodytext='DSR';
			}
			else
			{
				$bodytext='DCR';
			}
			if(route_plan=='yes')
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
					<span style='font-size:10pt;font-family:Arial CE'>".$bodytext.":&nbsp;".$emp_name."</span></strong></th>
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
								<span style='font-family:Arial CE;font-size:10pt'>".$d_instruction_array[$i]."</span>&nbsp;</td>
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
			if(notes_and_info=='yes')
			{
				$datepreviousnotesinfo=date('Ymd', strtotime("-1 days,$curdateserver "));
				//$datecurrentnotesinfo=date('Ymd');
				$sqlnotesinfo="SELECT feedback FROM notes_info_details WHERE SUBSTRING(notes_info_id,3,5)='".$emp_code."' AND 
								SUBSTRING(notes_info_id,-14,8)='".$datepreviousnotesinfo."'";
				$rsnotesinfo=mysqli_query($link,$sqlnotesinfo);
				$countnotesinfo=mysqli_num_rows($rsnotesinfo);
				while($rownotesinfo=mysqli_fetch_assoc($rsnotesinfo))
				{
					$note_info_details_string.="<tr><td style='width:400px;text-align:left;min-height:21px;background-color:white'>
					<span style='font-family:Arial CE;font-size:10pt'>".$rownotesinfo['feedback']."</span>&nbsp;</td></tr>";
				}
				if($countnotesinfo >0)
				{
					$note_info_string="<table><tr><td style='width:400px;text-align:left;min-height:21px;background-color:white'>
					<span style='font-family:Arial CE;font-size:10pt'><u><b>N.B:</b></u></span>&nbsp;</td></tr>".$note_info_details_string."</table>";
				}
				else
				{
					$note_info_string='';
				}
			}
			else
			{
				$note_info_string='';
			}
			$body.="<table border=1 style=background-color:AliceBlue>
					<tr>
					<th style='width:50px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Point</span></strong></th>
					<th style='width:100px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Customer Name</span></strong></th>
					<th style='width:150px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Remarks</span></strong></th>
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
								<span style='font-family:Arial CE;font-size:10pt'></span>&nbsp;</td></tr></table>".$note_info_string."
								<br /><br /><br />Powered By aceDNS";
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
					$d_instruction_array='';
					unset($d_instruction_array);
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
	}
	}
	$sqlselect="SELECT is_update,db_version_code FROM table_structure_updation  WHERE device_id='".$device_id."' and emp_code='".$emp_code."'";
	$rsselect=mysqli_query($link,$sqlselect);
	$count=mysqli_num_rows($rsselect);
		
	if($count>0)
	{
		$rowselect=mysqli_fetch_assoc($rsselect);
		$is_update=$rowselect['is_update'];
		$existed_db_version_code=$rowselect['db_version_code'];
		if($is_update==1){
			$sqlselectversion="SELECT version_code,date  FROM db_version ";
			$rsselectversion=mysqli_query($link,$sqlselectversion);
			$rowselectversion=mysqli_fetch_assoc($rsselectversion);
			$versionCode=$rowselectversion['version_code'];
			$release_date=date('d/m/Y',strtotime($rowselectversion['date']));
			
			if($versionCode==$db_version)
			{
			$sqlUpdatetablestructure="UPDATE table_structure_updation SET
									db_version_code='".$versionCode."',
									is_update='0'
									WHERE device_id='".$device_id."' AND emp_code='".$emp_code."'";
			mysqli_query($link,$sqlUpdatetablestructure);
			
			$date=gmdate('d',strtotime('+329 minute'));
			$month=gmdate('m',strtotime('+329 minute'));
			$year=gmdate('Y',strtotime('+329 minute'));
			
			$hour=gmdate('H',strtotime('+329 minute'));
			$minute=gmdate('i',strtotime('+329 minute'));
			$second=gmdate('s',strtotime('+329 minute'));
			$update_date=$date.'/'.$month.'/'.$year;
			$update_time=$hour.':'.$minute.':'.$second;

			if(email_hierarchywise=='yes')
			{
			 if(email_hierarchy_level==1)
		     {
				$db_update_mail='';
			 }
			 else
			 {
				 $db_update_mail=ORDEREMAILRECIPENTS;
			 }
			}
			else
			{
				$db_update_mail=ORDEREMAILRECIPENTS;
			}
			$mailsubj="ACEdns - ".strtoupper($nick_name)." DB Version $versionCode released on $release_date has been successfully updated to $emp_name";
			$emailbody="<html><head><title>Db Updation</title></head>
						<body>ACEdns - ".strtoupper($nick_name)." DB Version $versionCode released on $release_date has been successfully updated to $emp_name - $emp_code on $update_date @ $update_time.</table><br /><br />Regards<br />
TEAM - ACEdns</body></html>";
			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=UTF-8\n";
			$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
							"Reply-To:".FROMEMAIL." \r\n" .
							"Bcc: ".BCCEMAIL." \r\n".
							'X-Mailer: PHP/' . phpversion();
			$sendmail=@mail($db_update_mail, $mailsubj, $emailbody, $headers,'-facedns@acedns.in');
		  }
		}	
	}
	//Start For updation of emp data download log
	$sqlemplogchk="SELECT is_download FROM emp_data_download_log WHERE emp_code='".$emp_code."'";
	$rsemplogchk=mysqli_query($link,$sqlemplogchk);
	$countemplogchk=mysqli_num_rows($rsemplogchk);
	if($countemplogchk>0)
	{
		$rowemplogchk=mysqli_fetch_assoc($rsemplogchk);
		$is_download=$rowemplogchk['is_download'];
		if($is_download=='yes')
		{
			$sqlupdateemplog="UPDATE emp_data_download_log SET is_download='no',
							is_download_time=CURRENT_TIMESTAMP() WHERE emp_code='".$emp_code."'";
			mysqli_query($link,$sqlupdateemplog);	
		}
	}
	//End for updation of emp data download log
	
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/updateFirstloginDatetime-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code&date_time=$date_time&device_id=$device_id&days_first_login=$days_first_login&db_version=$db_version";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/updateFirstloginDatetime-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&date_time=$date_time&device_id=$device_id&days_first_login=$days_first_login&db_version=$db_version"."\r\n";
	$insertPos=0;  // variable for saving 
	while (!feof($file)) {
		$line=fgets($file);
		if (strpos($line, 'http://')!==false) {
			$insertPos=ftell($file);
			$newline =  $newuser;
		}
		else
		{
			$newline.=$line;   // append existing data with new data of user
		}
	}
	fseek($file,$insertPos);   // move pointer to the file position where we saved above 
	fwrite($file, $newline);
	fclose($file);*/	
	
?>
