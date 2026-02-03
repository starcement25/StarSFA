<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	$mode = $_REQUEST['mode'];
	
	disphtml("main();");
ob_end_flush();

function main()
{	
	$emp_code=$_REQUEST['emp_code'];
	$mode=$_REQUEST['mode'];
	$page=$_REQUEST['page'];
	$order_received = $_REQUEST['order_received'];
	
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
			$rslastlatlong=mysql_query($sqlupdatelatlong) or die(mysql_error()." Error in update zero latt longi: ".$sqlupdatelatlong);
		}
	}
	/*if(DCR_map=='no')
	{
		updateLatlong($emp_code);
	}*/
	$sqlemployee="SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsemployee=mysql_query($sqlemployee);
	$rowemployee=mysql_fetch_array($rsemployee);
	$curdateserver=gmdate('Y-m-d',strtotime('+329 minute'));
	$dateprevious=date('Y-m-d', strtotime("-1 days,$curdateserver "));
	$requiredate=$_REQUEST['requiredate'];
	if($requiredate!='')
	{
		$curdateserver=date('Y-m-d',strtotime($requiredate));
	}
	
	$emp_name=$rowemployee['emp_name'];
	$pointcoords=array();
	$customer_name_array=array();
	$customer_code_array=array();
	$activity_array=array();
	$total_qty_array=array();
	$total_amount_array=array();
	$remarks_array=array();
	$trantime_array=array();
	$markersArr[]='';
	$pathpointsArr[]='';
	$pointlabel=1;

	 if(check_in_out=='no')
	  {
		/*$sqlpoint="SELECT latt, longi,trans_id,DATE_FORMAT(date,'%H:%i:%s') as trantime FROM location WHERE emp_code ='".$emp_code."' AND 
					DATE LIKE '%".$dateprevious."%' ORDER BY DATE_FORMAT(DATE,'%Y-%m-%d %H:%i:%s') ASC ";*/
		$sqlpoint="(SELECT latt, longi,trans_id,DATE_FORMAT(date,'%H:%i:%s') as trantime FROM location WHERE emp_code ='".$emp_code."' 
					AND (DATE LIKE '%".$curdateserver."%' OR trans_id IN(SELECT trans_id FROM 
					check_in_out_details WHERE SUBSTRING(check_in_time,1,10)='".$curdateserver."')) 
					AND (SUBSTRING(trans_id,1,1) IN('A','O','P','D') OR SUBSTRING(trans_id,1,2) IN('NO','NC','CI')) 
					ORDER BY DATE_FORMAT(date,'%Y-%m-%d %H:%i:%s') ASC) ";			
		$rspoint=mysql_query($sqlpoint);
		$countpoint=mysql_num_rows($rspoint);
		if($countpoint>0)
		{
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
				$pathpointsArr[]=$point;
				$pathpoints=implode('|',$pathpointsArr);
				$pathpoints=substr($pathpoints,1);
				
				$trans_id=$rowpoint['trans_id'];
				$operation_type=substr($trans_id,0,1);
				$time=$rowpoint['trantime'];
				if($operation_type=='A')
				{
					$activity='Attendance';
					$customer_name='----';
					$total_amount='----';
					$total_qty='----';
					$operation_type_no='';
				}
				if($operation_type=='O')
					{
						$order_no=$trans_id;
						$sqlcustomer="SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_order_received,OH.d_instruction,
									  OH.transaction_type FROM 
									  order_header OH,customer_master CM,order_details OD
									  WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."' 
									  AND OH.order_no=OD.order_no GROUP BY OD.order_no
									  UNION
									  SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_order_received,OH.d_instruction,OH.transaction_type FROM 
									  order_header OH,prospective_customer_master CM,order_details OD
									  WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."' 
									  AND OH.order_no=OD.order_no GROUP BY OD.order_no";
						$rscustomer=mysql_query($sqlcustomer) or die(mysql_error()." Error in select customer: ".$sqlcustomer);
						$rowcustomer=mysql_fetch_array($rscustomer);
						$customer_name=$rowcustomer['customer_name'];
						$total_qty=$rowcustomer['total_order_received'];
						$transaction_type=$rowcustomer['transaction_type'];
						$total_amount='----';
						$instruction=$rowcustomer['d_instruction'];
						if($transaction_type =='TO')
						{
							$activity='Telephonic Order';
						}
						else
						{
							$activity='Order';
						}
					}
					if($operation_type=='P')
					{
						$activity='Payment';
						$receipt_id=$trans_id;
						$sqlcustomerpayment="SELECT CM.customer_name,CM.customer_code,SUM(PD.amount) AS total_collection_received,PH.p_remark 
											FROM payment_header PH,customer_master CM,payment_details PD
											WHERE CM.customer_code=PH.customer_code AND PH.receipt_id=PD.receipt_id AND 
											PH.receipt_id='".$receipt_id."' GROUP BY PD.receipt_id
											UNION
											SELECT CM.customer_name,CM.customer_code,SUM(PD.amount) AS total_collection_received,PH.p_remark 
											FROM payment_header PH,prospective_customer_master CM,payment_details PD
											WHERE CM.customer_code=PH.customer_code AND PH.receipt_id=PD.receipt_id 
											AND PH.receipt_id='".$receipt_id."' GROUP BY PD.receipt_id";
						$rscustomerpayment=mysql_query($sqlcustomerpayment) or die(mysql_error()." Error in select customer payment: ".$sqlcustomerpayment);
						
						$rowcustomerpayment=mysql_fetch_array($rscustomerpayment);
						$customer_name=$rowcustomerpayment['customer_name'];
						$total_amount='Rs/- '.number_format($rowcustomerpayment['total_collection_received'],2);
						$total_qty='----';
						$instruction=$rowcustomerpayment['p_remark'];
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
								$rscustomernoorder=mysql_query($sqlcustomernoorder) or die(mysql_error()." Error in select customer for no order: ".$sqlcustomernoorder);
								$rowcustomernoorder=mysql_fetch_array($rscustomernoorder);
						
								$customer_name=$rowcustomernoorder['customer_name'];
								$total_amount='----';
								$total_qty='----';
								$instruction=$rowcustomernoorder['d_instruction'];
							}
						if($operation_type_no=='C')
						{
							$activity='No Collection';
							$receipt_id=$trans_id;
							$sqlcustomernocollection="SELECT CM.customer_name,CM.customer_code,PH.p_remark FROM payment_header PH,customer_master CM 
													   WHERE CM.customer_code=PH.customer_code AND PH.receipt_id='".$receipt_id."'
													   UNION
													   SELECT CM.customer_name,CM.customer_code,PH.p_remark FROM payment_header PH,prospective_customer_master CM 
													   WHERE CM.customer_code=PH.customer_code AND PH.receipt_id='".$receipt_id."'";
							$rscustomernocollection=mysql_query($sqlcustomernocollection) or die(mysql_error()." 
														Error in select customer for no collection: ".$sqlcustomernocollection);
							$rowcustomernocollection=mysql_fetch_array($rscustomernocollection);
							
							$customer_name=$rowcustomernocollection['customer_name'];
							$total_amount='----';
							$total_qty='----';
							$instruction=$rowcustomernocollection['p_remark'];
						}
					}
					if($operation_type=='C')
					{
						$operation_type_no=substr($trans_id,1,1);
						if($operation_type_no=='I')
							{
								$activity='Check In Check Out';
								$ci_trans_id=$trans_id;
								$sqlcustomercheckinout="SELECT CM.customer_name,CM.customer_code,DATE_FORMAT(check_in_time,'%H:%i:%s') as checkintime,
														DATE_FORMAT(check_out_time,'%H:%i:%s') as checkouttime,remarks,SUBSTRING(check_in_time,1,10) AS  checkindate 
														FROM check_in_out_details CIO,customer_master CM 
														WHERE CM.customer_code=CIO.customer_code AND CIO.trans_id='".$ci_trans_id."'";
								$rscustomercheckinout=mysql_query($sqlcustomercheckinout) or die(mysql_error()." Error in select customer for checkin out: ".$sqlcustomercheckinout);
								$rowcustomercheckinout=mysql_fetch_array($rscustomercheckinout);
						
								$customer_name=$rowcustomercheckinout['customer_name'];
								$total_amount='----';
								$total_qty='----';
								$time="<b>IN:</b> $rowcustomercheckinout[checkintime] \n <b>OUT:</b> $rowcustomercheckinout[checkouttime]";
								$instruction=$rowcustomercheckinout['remarks'];
							}
					}
					if($operation_type=='D')
					{
						$operation_type_no=substr($trans_id,1,1);
						$sqlprospective="SELECT PCH.customer_name,PCH.remarks FROM prospective_customer_header PCH WHERE PCH.trans_id='".$trans_id."'";
						$rsprospective=mysql_query($sqlprospective) or die(mysql_error()." 
										Error in select prospective customer or mechanic: ".$sqlprospective);
						$rowprospective=mysql_fetch_array($rsprospective);
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
							$instruction=$rowprospective['remarks'];	
					}					
					if($operation_type_no=='I')
					{
						if($rowcustomercheckinout['checkindate']==$curdateserver){
							array_push($pointcoords,$point);
							array_push($customer_name_array,$customer_name);
							array_push($trantime_array,$time);
							array_push($total_qty_array,$total_qty);
							array_push($total_amount_array,$total_amount);
							array_push($activity_array,$activity);
							array_push($remarks_array,$instruction);

						}
					}
					else
					{
						array_push($pointcoords,$point);
						array_push($customer_name_array,$customer_name);
						array_push($trantime_array,$time);
						array_push($total_qty_array,$total_qty);
						array_push($total_amount_array,$total_amount);
						array_push($activity_array,$activity);
						array_push($remarks_array,$instruction);

					}
				$pointlabel++;
			}
			//echo $markers;
			//print_r($pointcoords);
			//echo $pathpoints;
			//echo '"http://maps.googleapis.com/maps/api/staticmap?size=700x600&path=color:0xff0000ff|weight:2|'.$pathpoints.'&sensor=false&'.$markers.'"';
			$subject='DCR of '.$emp_name.' on '.$datesubject;
			if(DCR_map=='yes'){	
			$body='<img src="https://maps.googleapis.com/maps/api/staticmap?size=700x600&path=color:0xff0000ff|weight:2|'.$pathpoints.'&sensor=false&'.$markers.'&key=AIzaSyBhJB9maJFpMdTZ_JXAbB7HBX4H8oDFURo" alt=""><br /> <br />';
			}
			
			for($i=0;$i<count($pointcoords);$i++)
			{
				$point=($i+1);
				
				/*$Arrpointcoordsfrom =explode(',',$pointcoords[$i]);
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
				}*/
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
								<span style='font-family:Arial CE;font-size:10pt'>".$activity_array[$i]."</span>&nbsp;</td>
								<td style='width:60px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$total_qty_array[$i]."</span>&nbsp;</td>
								<td style='width:60px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$total_amount_array[$i]."</span>&nbsp;</td>
								<td style='width:70px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$trantime_array[$i]."</span>&nbsp;</td>
								<td style='width:150px;text-align:left;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$remarks_array[$i]."</span>&nbsp;</td>			
							  </tr>";
			}
		  }
		}//End for check in out no
		else{
			/*$sqlpoint="SELECT latt, longi,trans_id,DATE_FORMAT(date,'%H:%i:%s') as trantime FROM location WHERE emp_code ='".$emp_code."' AND 
			DATE LIKE '%".$dateprevious."%' ORDER BY DATE_FORMAT(DATE,'%Y-%m-%d %H:%i:%s') ASC ";*/
		echo $sqlpoint="(SELECT LO.latt, LO.longi,LO.trans_id,DATE_FORMAT(LO.date,'%H:%i:%s') as trantime,CIO.customer_code,CIO.check_in_time,
						CIO.check_out_time,CIO.remarks FROM location LO
					    LEFT JOIN check_in_out_details CIO ON LO.trans_id=CIO.trans_id
				       WHERE LO.emp_code ='".$emp_code."' AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d')='".$curdateserver."'  
					   AND (SUBSTRING(LO.trans_id,1,1) IN('A','O','P','D') OR SUBSTRING(LO.trans_id,1,2) IN('NO','NC','CI')) 
					   ORDER BY SUBSTRING(LO.trans_id,1,1) ASC,DATE_FORMAT(LO.date,'%Y-%m-%d %H:%i:%s') ASC) ";
		$rspoint=mysql_query($sqlpoint);
		$countpoint=mysql_num_rows($rspoint);
		$pointcoords=array();
		$customer_name_array=array();
		$activity_array=array();
		$total_qty_array=array();
		$total_amount_array=array();
		$remarks_array=array();
		$trantime_array=array();
		$markersArr[]='';
		$pathpointsArr[]='';
		$pointlabel=1;
		$check_in_out_customer_array=array();
		if($countpoint>0)
		{
			while($rowpoint=mysql_fetch_array($rspoint))
			{
				$trans_id=$rowpoint['trans_id'];
				$operation_type=substr($trans_id,0,1);
				if($operation_type=='A')
				{
					$activity='Attendance';
					$customer_name='----';
					$total_amount='----';
					$total_qty='----';
					$instruction='---';
					$operation_type_no='';
					$time=$rowpoint['trantime'];					
					$point=$rowpoint['latt'].','.$rowpoint['longi'];
					$customer_name='---';
					$customer_code='---';
					//$markersArr[]="markers=color:red|label:$pointlabel|$point";
					if($pointlabel==1 || $countpoint==$pointlabel){
						$markersArr[]="markers=color:green|label:|$point";
					}
					else 
					{
						$markersArr[]="markers=color:red|label:|$point";
					}
					$markers=implode('&',$markersArr);
					$pathpointsArr[]=$point;
					$pathpoints=implode('|',$pathpointsArr);
					$pathpoints=substr($pathpoints,1);
					
					array_push($pointcoords,$point);
					array_push($customer_name_array,$customer_name);
					array_push($customer_code_array,$customer_code);
					array_push($trantime_array,$time);
					array_push($total_qty_array,$total_qty);
					array_push($total_amount_array,$total_amount);
					array_push($activity_array,$activity);
					array_push($remarks_array,$instruction);

					$pointlabel++;
				}
				if($operation_type=='C')
				{
					$instruction='';
					$customer_code=$rowpoint['customer_code'];
					if(!in_array($customer_code,$check_in_out_customer_array))
					{
						$check_in_date_time=$rowpoint['check_in_time'];
						$check_out_date_time=$rowpoint['check_out_time'];
						$check_in_date=substr($check_in_date_time,0,10);
						if($check_in_date==$curdateserver){
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
						$pathpointsArr[]=$point;
						$pathpoints=implode('|',$pathpointsArr);
						$pathpoints=substr($pathpoints,1);
						
						$sqlcustomer="SELECT customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
						$rscustomer=mysql_query($sqlcustomer);
						$rowcustomer=mysql_fetch_array($rscustomer);
						$customer_name=$rowcustomer['customer_name'];
						$sqlcheckinout="SELECT check_in_time,check_out_time,remarks FROM check_in_out_details 
									  WHERE customer_code='".$customer_code."' AND DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d')='".$curdateserver."' 
									 AND SUBSTRING(trans_id,3,5) = '".$emp_code."' ORDER BY check_in_time ASC";
						$rscheckinout=mysql_query($sqlcheckinout) or die(mysql_error()." Error in select check in out details ".$sqlcheckinout);
						$countcheckinout=mysql_num_rows($rscheckinout);
						if($countcheckinout >0)
						{
							$time='';
							$remarks_str='';
							while($rowcheckinout=mysql_fetch_array($rscheckinout))
							{
								$check_in_time=substr($rowcheckinout['check_in_time'],11,8);
								$check_out_time=substr($rowcheckinout['check_out_time'],11,8);
								$time=$time."<b>IN:</b> $check_in_time \n <b>OUT:</b> $check_out_time"."<br /><br />";
								$remarks=$rowcheckinout['remarks'];
								if($remarks!='')
								{
								$remarks_str=$remarks_str.$remarks."<br /><br />";
								}
							}
						}

						$sql_order_details = "SELECT OH.customer_code,OH.TD,SUM(OD.qty) as total_qty,
											DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') as order_date,
											GROUP_CONCAT(DISTINCT OH.d_instruction SEPARATOR '#') AS order_remarks
											FROM order_header OH INNER JOIN order_details OD 
											WHERE OH.order_no=OD.order_no AND OH.customer_code='".$customer_code."' AND  
											DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d')='".$curdateserver."' 
											AND SUBSTRING(OH.order_no,2,5) = '".$emp_code."' AND OH.transaction_type!='TO' 
											GROUP BY OH.customer_code";
						$res_order_details = mysql_query($sql_order_details);
						$order_rows = mysql_num_rows($res_order_details);
						$row_order_details = mysql_fetch_array($res_order_details);
						$total_qty = $row_order_details['total_qty'];
						if($total_qty > 0)   $total_qty= $total_qty;
						else 				  $total_qty= '----';	
						$order_date = $row_order_details['order_date'];
						$order_remarks=$row_order_details['order_remarks'];
						
						echo $sql_payment_details = "SELECT SUM(amount) as collection_amount,p_remark FROM payment_header WHERE customer_code = '".$customer_code."' 
												AND DATE_FORMAT(SUBSTRING(receipt_id,-14,8),'%Y-%m-%d')='".$curdateserver."' 
												AND SUBSTRING(receipt_id,2,5) = '".$emp_code."' AND sale_type!='TC'
												GROUP BY customer_code";
						$res_payment_details = mysql_query($sql_payment_details);
						$row_payment_details = mysql_fetch_array($res_payment_details);
						$collection_amount = $row_payment_details['collection_amount'];
						$p_remark=$row_payment_details['p_remark'];
	
						if($collection_amount > 0)   $total_amount= 'Rs/- '.number_format($collection_amount,2);
						else   							$total_amount= '----';
								$instructioncnt=1;	
						if($remarks!=''){
							$instruction=$instructioncnt.'.'.$remarks_str;
							$instructioncnt++;
						}
						if($order_remarks!=''){
						 $order_remarks_arr=explode("#",$order_remarks);
						 foreach($order_remarks_arr as $order_remarks_val)
						 {	
							if($order_remarks_val!='')
							{
							$instruction.='<br />'.$instructioncnt.'.'.$order_remarks_val;
							$instructioncnt++;
							}
						 }
						}
						if($p_remark!=''){
						$instruction.='<br />'.$instructioncnt.'.'.$p_remark;
						$instructioncnt++;
						}
						
						if(check_in_out=='yes' && check_in_out_menu_access=='')
		 				{
							$activity='Check in check out';
						}
						else
						{
							if($total_qty > 0 || $total_amount > 0)
							{
								$activity='Productive';
							}
							else $activity='Non Productive';
						}
						
						array_push($pointcoords,$point);
						array_push($customer_name_array,$customer_name);
						array_push($customer_code_array,$customer_code);
						array_push($trantime_array,$time);
						array_push($total_qty_array,$total_qty);
						array_push($total_amount_array,$total_amount);
						array_push($activity_array,$activity);
						array_push($remarks_array,$instruction);
	
						$pointlabel++;
						}
						array_push($check_in_out_customer_array,$customer_code);
					}
				}
				if($operation_type=='O')
					{
					   $sqlchkorder="SELECT order_no FROM order_header WHERE order_no='".$trans_id."' AND transaction_type='TO'";
					   $rschkorder=mysql_query($sqlchkorder);
					   $cntchkorder=mysql_num_rows($rschkorder);
					   if($cntchkorder > 0)
					   {
						$order_no=$trans_id;
						$sqlcustomer="SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_order_received,OH.d_instruction
									  FROM 
									  order_header OH,customer_master CM,order_details OD
									  WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."' 
									  AND OH.order_no=OD.order_no GROUP BY OD.order_no
									  UNION
									  SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_order_received,OH.d_instruction FROM 
									  order_header OH,prospective_customer_master CM,order_details OD
									  WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."' 
									  AND OH.order_no=OD.order_no GROUP BY OD.order_no";
						$rscustomer=mysql_query($sqlcustomer) or die(mysql_error()." Error in select customer: ".$sqlcustomer);
						$rowcustomer=mysql_fetch_array($rscustomer);
						$customer_name=$rowcustomer['customer_name'];
						$total_qty=$rowcustomer['total_order_received'];
						$total_amount='----';
						$instruction=$rowcustomer['d_instruction'];
						$activity='Telephonic Order';
						$time=$rowpoint['trantime'];
						
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
						$pathpointsArr[]=$point;
						$pathpoints=implode('|',$pathpointsArr);
						$pathpoints=substr($pathpoints,1);
						array_push($pointcoords,$point);
						array_push($customer_name_array,$customer_name);
						array_push($customer_code_array,$customer_code);
						array_push($trantime_array,$time);
						array_push($total_qty_array,$total_qty);
						array_push($total_amount_array,$total_amount);
						array_push($activity_array,$activity);
						array_push($remarks_array,$instruction);

						$pointlabel++;
					   }
					}
					if($operation_type=='P')
					{
					   $sqlchkpayment="SELECT receipt_id FROM payment_header WHERE receipt_id='".$trans_id."' AND sale_type='TC'";
					   $rschkpayment=mysql_query($sqlchkpayment);
					   $cntchkpayment=mysql_num_rows($rschkpayment);
					   if($cntchkpayment > 0)
					   {
						$activity='Telephonic Payment';
						$receipt_id=$trans_id;
						$sqlcustomerpayment="SELECT CM.customer_name,CM.customer_code,SUM(PD.amount) AS total_collection_received,PH.p_remark 
											FROM payment_header PH,customer_master CM,payment_details PD
											WHERE CM.customer_code=PH.customer_code AND PH.receipt_id=PD.receipt_id AND 
											PH.receipt_id='".$receipt_id."' GROUP BY PD.receipt_id
											UNION
											SELECT CM.customer_name,CM.customer_code,SUM(PD.amount) AS total_collection_received,PH.p_remark 
											FROM payment_header PH,prospective_customer_master CM,payment_details PD
											WHERE CM.customer_code=PH.customer_code AND PH.receipt_id=PD.receipt_id 
											AND PH.receipt_id='".$receipt_id."' GROUP BY PD.receipt_id";
						$rscustomerpayment=mysql_query($sqlcustomerpayment) or die(mysql_error()." Error in select customer payment: ".$sqlcustomerpayment);
						
						$rowcustomerpayment=mysql_fetch_array($rscustomerpayment);
						$customer_name=$rowcustomerpayment['customer_name'];
						$total_amount='Rs/- '.number_format($rowcustomerpayment['total_collection_received'],2);
						$total_qty='----';
						$instruction=$rowcustomerpayment['p_remark'];
						$time=$rowpoint['trantime'];

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
						$pathpointsArr[]=$point;
						$pathpoints=implode('|',$pathpointsArr);
						$pathpoints=substr($pathpoints,1);
						array_push($pointcoords,$point);
						array_push($customer_name_array,$customer_name);
						array_push($customer_code_array,$customer_code);
						array_push($trantime_array,$time);
						array_push($total_qty_array,$total_qty);
						array_push($total_amount_array,$total_amount);
						array_push($activity_array,$activity);
						array_push($remarks_array,$instruction);

						$pointlabel++;
					   }
					}
					/*if($operation_type=='C')
					{
						$operation_type_no=substr($trans_id,1,1);
						if($operation_type_no=='I')
							{
								$activity='Check In Check Out';
								$ci_trans_id=$trans_id;
								$sqlcustomercheckinout="SELECT CM.customer_name,CM.customer_code,DATE_FORMAT(check_in_time,'%H:%i:%s') as checkintime,
														DATE_FORMAT(check_out_time,'%H:%i:%s') as checkouttime,remarks,SUBSTRING(check_in_time,1,10) AS  checkindate 
														FROM check_in_out_details CIO,customer_master CM 
														WHERE CM.customer_code=CIO.customer_code AND CIO.trans_id='".$ci_trans_id."'";
								$rscustomercheckinout=mysql_query($sqlcustomercheckinout) or die(mysql_error()." Error in select customer for checkin out: ".$sqlcustomercheckinout);
								$rowcustomercheckinout=mysql_fetch_array($rscustomercheckinout);
						
								$customer_name=$rowcustomercheckinout['customer_name'];
								$total_amount='----';
								$total_qty='----';
								$time="<b>IN:</b> $rowcustomercheckinout[checkintime] \n <b>OUT:</b> $rowcustomercheckinout[checkouttime]";
								$instruction=$rowcustomercheckinout['remarks'];
							}
					}*/
				
					}
			//echo $markers;
			//print_r($pointcoords);
			//echo $pathpoints;
			//echo '"http://maps.googleapis.com/maps/api/staticmap?size=700x600&path=color:0xff0000ff|weight:2|'.$pathpoints.'&sensor=false&'.$markers.'"';
			$subject='DCR of '.$emp_name.' on '.$datesubject;
			if(DCR_map=='yes'){	
			//echo "https://maps.googleapis.com/maps/api/staticmap?size=700x600&path=color:0xff0000ff|weight:2|'.$pathpoints.'&sensor=false&'.$markers.'&key=AIzaSyBhJB9maJFpMdTZ_JXAbB7HBX4H8oDFURo";
			$body='<img src="https://maps.googleapis.com/maps/api/staticmap?size=700x600&path=color:0xff0000ff|weight:2|'.substr($pathpoints,1).'&sensor=false&'.$markers.'&key=AIzaSyBhJB9maJFpMdTZ_JXAbB7HBX4H8oDFURo" alt=""><br /> <br />';
			}
			
			for($i=0;$i<count($customer_code_array);$i++)
			{
				$point=($i+1);
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
								<span style='font-family:Arial CE;font-size:10pt'>".$activity_array[$i]."</span>&nbsp;</td>
								<td style='width:60px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$total_qty_array[$i]."</span>&nbsp;</td>
								<td style='width:60px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$total_amount_array[$i]."</span>&nbsp;</td>
								<td style='width:70px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$trantime_array[$i]."</span>&nbsp;</td>
								<td style='width:150px;text-align:left;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$remarks_array[$i]."</span>&nbsp;</td>			
							  </tr>";
			}
		}
			
	  }//End for check in out yes
	  
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
					<span style='font-size:10pt;font-family:Arial CE'>Activity</span></strong></th>
					<th style='width:60px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Qty</span></strong></th>
					<th style='width:60px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Amount</span></strong></th>
					<th style='width:90px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Time</span></strong></th>
					<th style='width:150px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Remarks</span></strong></th>
					</tr>
					".$bodydetails."</table>";
		if($_REQUEST['page']=='attendance')
		{
			$backurl='adminAttendanceTracker.php';
		}
		else if($_REQUEST['page']=='misreporthierarchy')
		{
			$backurl='adminMisReportEmphierarchy.php';
		}
		else
		{
			$backurl='adminMisReport.php';
		}
?>
<table width="60%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Employee Direct Call Report</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
            <table width="90%" align="center" border="0" cellpadding="5" cellspacing="1">
            <?php if($order_received != 'true'){?>
				<tr> 
					<td align="right" class="ERR" width="95%"><a href="javascript:void(0);" style="color: #e40000" 
                    onclick="javascript:window.location='<?=$backurl?>?from_date=<?php echo $_REQUEST['from_date']?>&to_date=<?php echo $_REQUEST['to_date']?>&emp_code=<?php echo $_REQUEST['emp_code']?>&mode=<?php echo $_REQUEST['mode']?>&page=<?php echo $_REQUEST['page']?>&state=<?php echo $_REQUEST['state'];?>&emp_type=<?php echo $_REQUEST['emp_type'];?>&employee_lev_one=<?php echo $_REQUEST['employee_lev_one'];?>&modehierarchy=<?php echo $_REQUEST['modehierarchy'];?>'"><img src="images/back.png" alt="back" /></a></td>
					<td align="right" width="5%"></td>
				</tr>
             <?php } else{?>
             <tr>
             	<td colspan="2"  align="right"><input type="button" value="Close" onclick="close_window();"</td>
             </tr>
             <?php } ?>
			</table>
            <br /> <br /> <br />
            <table width="90%" align="center" border="0" class="border" cellpadding="5" cellspacing="1">
				<tr class="TDHEAD"> 
					<td>DCR of <?php echo $emp_name;?> <?php if($requiredate!='')
	{?> on <?php echo $requiredate;}?></td>
				</tr>
				<tr> 
					<td align="center"><?php echo $body;?></td>
                </tr>
            </table>
        </td>
     </tr>               
 </table> 
 <script>
function close_window(){
	window.close();
}
 </script>                  
 <?php }?>                   

