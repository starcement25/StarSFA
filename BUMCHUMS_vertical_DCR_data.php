<?php
ob_start();
define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	require("include/config-setup.php");
	
	define("DB","acedns_RUPA");
	
	//require("include/dbcon.php");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
$employee = $_REQUEST['employee'];
$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	$todaydate =$year.'-'.$month.'-'.$date;
	$numericprevdate=date('Y-m-d', strtotime("-10 days,$todaydate "));
$sqlemp="SELECT emp_name FROM employee_master WHERE emp_code='".$employee."'";
$rsemp=mysqli_query($link,$sqlemp) or die(mysqli_error()." Error in select employee name and code : ".$sqlemp);
$rowemp=mysqli_fetch_assoc($rsemp);
$emp_name=$rowemp['emp_name'];

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

		$sqlpoint="SELECT latt, longi,trans_id,DATE_FORMAT(date,'%d-%m-%Y %H:%i:%s') as trantime FROM location WHERE emp_code ='".$employee."' 
					AND SUBSTRING(date,1,10) >='".$numericprevdate."' 
					AND (SUBSTRING(trans_id,1,1) IN('A','O','P','D') OR SUBSTRING(trans_id,1,2) IN('NO','NC','CI')) 
					ORDER BY DATE_FORMAT(date,'%Y-%m-%d %H:%i:%s') ASC ";			
		$rspoint=mysqli_query($link,$sqlpoint);
		$countpoint=mysqli_num_rows($rspoint);
		if($countpoint>0)
		{
			while($rowpoint=mysqli_fetch_assoc($rspoint))
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
						$rscustomer=mysqli_query($link,$sqlcustomer) or die(mysqli_error()." Error in select customer: ".$sqlcustomer);
						$rowcustomer=mysqli_fetch_assoc($rscustomer);
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
						$rscustomerpayment=mysqli_query($link,$sqlcustomerpayment) or die(mysqli_error()." Error in select customer payment: ".$sqlcustomerpayment);
						
						$rowcustomerpayment=mysqli_fetch_assoc($rscustomerpayment);
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
								$rscustomernoorder=mysqli_query($link,$sqlcustomernoorder) or die(mysqli_error()." Error in select customer for no order: ".$sqlcustomernoorder);
								$rowcustomernoorder=mysqli_fetch_assoc($rscustomernoorder);
						
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
							$rscustomernocollection=mysqli_query($link,$sqlcustomernocollection) or die(mysqli_error()." 
														Error in select customer for no collection: ".$sqlcustomernocollection);
							$rowcustomernocollection=mysqli_fetch_assoc($rscustomernocollection);
							
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
								$rscustomercheckinout=mysqli_query($link,$sqlcustomercheckinout) or die(mysqli_error()." Error in select customer for checkin out: ".$sqlcustomercheckinout);
								$rowcustomercheckinout=mysqli_fetch_assoc($rscustomercheckinout);
						
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
			
			for($i=0;$i<count($pointcoords);$i++)
			{
				$point=($i+1);
				
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
		
			$bodyaddress="";
			$body.="<table border=1 style=background-color:AliceBlue width=\"100%\">
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
					<span style='font-size:10pt;font-family:Arial CE'>Date Time</span></strong></th>
					<th style='width:150px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Remarks</span></strong></th>
					</tr>
					".$bodydetails."</table>";
		
?>
<table width="95%" align="center" cellpadding="2" cellspacing="2" border="0" >
	<tr>
		<td valign="top" >
            <table width="90%" align="center" border="0" class="border" cellpadding="5" cellspacing="1">
				<tr class="TDHEAD"> 
					<td>DCR of <?php echo $emp_name;?> From <?php echo date('d-m-Y',strtotime($numericprevdate));?>
	 to <?php echo date('d-m-Y',strtotime($todaydate));?></td>
				</tr>
				<tr> 
					<td align="center"><?php echo $body;?></td>
                </tr>
            </table>
        </td>
     </tr>               
 </table>
 <br /><br />

    <p>

    <div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;

    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >

</div> 
<?php
mysqli_close($link);
?>



