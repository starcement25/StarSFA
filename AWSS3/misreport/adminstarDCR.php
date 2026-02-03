<?php
ob_start();
session_start();
require("adminUtils.php");
	
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
	if(DCR_map=='no')
	{
		updateLatlong($emp_code);
	}
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
		
		/*$sqlpoint="SELECT latt, longi,trans_id,DATE_FORMAT(date,'%H:%i:%s') as trantime FROM location WHERE emp_code ='".$emp_code."' AND 
					DATE LIKE '%".$dateprevious."%' ORDER BY DATE_FORMAT(DATE,'%Y-%m-%d %H:%i:%s') ASC ";*/
		$sqlpoint="(SELECT latt, longi,trans_id,DATE_FORMAT(date,'%H:%i:%s') as trantime FROM location WHERE emp_code ='".$emp_code."' 
					AND (DATE LIKE '%".$curdateserver."%' OR trans_id IN(SELECT trans_id FROM check_in_out_details WHERE SUBSTRING(check_in_time,1,10)='".$curdateserver."')) 
					AND (SUBSTRING(trans_id,1,1) IN('A','O','P','D') OR SUBSTRING(trans_id,1,2) IN('NO','NC','CI')) 
					ORDER BY DATE_FORMAT(date,'%Y-%m-%d %H:%i:%s') ASC) ";			
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
		if($countpoint>0)
		{
			while($rowpoint=mysql_fetch_array($rspoint))
			{
				$point=$rowpoint['latt'].','.$rowpoint['longi'];
				$markersArr[]="markers=color:red|label:$pointlabel|$point";
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
						$activity='Order';
						$order_no=$trans_id;
						$sqlcustomer="SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_order_received,OH.d_instruction FROM 
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
			$subject='DCR of '.$emp_name.' on '.$datesubject;
			$body='<img src="http://maps.googleapis.com/maps/api/staticmap?size=700x600&path=color:0xff0000ff|weight:2|'.$pathpoints.'&sensor=false&'.$markers.'" alt=""><br /> <br />';
			
			for($i=0;$i<count($pointcoords);$i++)
			{
				$point=($i+1);
				
				$Arrpointcoordsfrom =explode(',',$pointcoords[$i]);
				$latitudeFrom=$Arrpointcoordsfrom[0];
				$longitudeFrom=$Arrpointcoordsfrom[1];
				
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
				$bodydetails.="<tr><td style='width:50px;text-align:left;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$point."</span>&nbsp;</td>	
								<td style='width:100px;text-align:left;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$customer_name_array[$i]."</span>&nbsp;</td>	
								<td style='width:150px;text-align:left;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$remarks_array[$i]."</span>&nbsp;</td>
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
					<span style='font-size:10pt;font-family:Arial CE'>Remarks</span></strong></th>
					<th style='width:70px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Distance</span></strong></th>
					<th style='width:70px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Activity</span></strong></th>
					<th style='width:60px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Qty</span></strong></th>
					<th style='width:60px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Amount</span></strong></th>
					<th style='width:90px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Time</span></strong></th>
					</tr>
					".$bodydetails."</table>";
		}//End of count if
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
				<!--<tr> 
					<td align="right" class="ERR" width="95%"><a href="javascript:void(0);" style="color: #e40000" 
                    onclick="javascript:window.location='<?=$backurl?>?from_date=<?php echo $_REQUEST['from_date']?>&to_date=<?php echo $_REQUEST['to_date']?>&emp_code=<?php echo $_REQUEST['emp_code']?>&mode=<?php echo $_REQUEST['mode']?>&page=<?php echo $_REQUEST['page']?>&state=<?php echo $_REQUEST['state'];?>&emp_type=<?php echo $_REQUEST['emp_type'];?>&employee_lev_one=<?php echo $_REQUEST['employee_lev_one'];?>&modehierarchy=<?php echo $_REQUEST['modehierarchy'];?>'"><img src="images/back.png" alt="back" /></a></td>
					<td align="right" width="5%"><a href="<?=$url;?>" title=" Refresh the page"><img border="0" src="images/icon_reload.gif"></a></td>
				</tr>-->
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

