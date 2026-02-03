<?php
//$nick_name_string='NAPTC,KUNJ';
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
require("include/config-setup.php");

define("DB","acedns_NAPTC");

//require("include/dbcon.php");
$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");

require("include/config-email-setup.php");

$date=date('Y-m-d');
$date=date('Y-m-d', strtotime("-1 days,$date "));
$empvalue=array();
$emptotaltrans=array();
$final_order_qty=0;
$final_collection_amount=0;
$emp_wise_total_trans=0;
$countloop=1;

$messagestring='<html><body>
				<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0" >
				<tr> 
					<td colspan="4" align="left"><strong>DAILY REPORT: '.date('d/m/Y',strtotime($date)).'</strong></td>
				</tr><br />
				<tr>
					<td>
						<table width="98%" align="center" cellpadding="5" cellspacing="2">';
									  
$sqltrans="SELECT EM.emp_name,EM.emp_code,LO.trans_id,DATE_FORMAT(LO.date,'%T') AS time FROM location LO,employee_master EM 
			WHERE EM.emp_code=LO.emp_code AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$date."%' 
			ORDER BY EM.emp_name ASC,LO.date DESC ";
$restrans=mysqli_query($link,$sqltrans) or die(mysqli_error()." Error in select transaction id: ".$sqltrans);
$count=mysqli_num_rows($restrans);
if($count>0)
{
	while($rectrans = mysqli_fetch_assoc($restrans)) 
	{ 
		$trans_id=$rectrans['trans_id'];
		$operation_type=substr($trans_id,0,1);
		
		if(!in_array($rectrans['emp_name'],$empvalue))
		{
			array_push($empvalue,$rectrans['emp_name']);
			$sqlempwisetotaltrans="SELECT COUNT(trans_id) AS total_trans FROM location WHERE emp_code='".$rectrans['emp_code']."' 
									AND DATE_FORMAT(date,'%Y-%m-%d') LIKE '%".$date."%'";
			$rsempwisetotaltrans=mysqli_query($link,$sqlempwisetotaltrans);
			$recempwisetotaltrans=mysqli_fetch_assoc($rsempwisetotaltrans);
			$emp_wise_total_trans=$emp_wise_total_trans+$recempwisetotaltrans['total_trans'];
			array_push($emptotaltrans,$emp_wise_total_trans);
			
			$sqlempwisetotalorder="SELECT SUM(OD.qty) AS total_order_received
							FROM order_details OD,location LO
							WHERE LO.emp_code='".$rectrans['emp_code']."' 
							AND LO.trans_id LIKE 'O%' AND LO.trans_id=OD.order_no 
							AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$date."%'";
			$rsempwisetotalorder=mysqli_query($link,$sqlempwisetotalorder);
			$recempwisetotalorder=mysqli_fetch_assoc($rsempwisetotalorder);
			$emp_wise_total_order=$recempwisetotalorder['total_order_received'];
			
			$sqlempwisetotalcollection="SELECT SUM(PD.amount) AS total_collection_received
										FROM payment_details PD,location LO
										WHERE  LO.emp_code='".$rectrans['emp_code']."' 
										AND LO.trans_id LIKE 'P%' AND LO.trans_id=PD.receipt_id 
										AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$date."%'";
			$rsempwisetotalcollection=mysqli_query($link,$sqlempwisetotalcollection);
			$recempwisetotalcollection=mysqli_fetch_assoc($rsempwisetotalcollection);
			$emp_wise_total_collection=$recempwisetotalcollection['total_collection_received'];
			
			$sqlempwiseattendance="SELECT DATE_FORMAT(date,'%T') AS time FROM location WHERE emp_code='".$rectrans['emp_code']."' 
									AND trans_id LIKE '%A%' AND DATE_FORMAT(date,'%Y-%m-%d') LIKE '%".$date."%'";
			$rsempwiseattendance=mysqli_query($link,$sqlempwiseattendance);
			$recempwiseattendance=mysqli_fetch_assoc($rsempwiseattendance);
			$attendance_time=$recempwiseattendance['time'];
						
									
			if(count($empvalue)>1)
			{
				$messagestring.='<br />';
			}
			if($attendance_time!='')
			{
				$messagestring.='<tr>
									<td align="left" colspan="6" style="BORDER: #000000 1px solid;">
									<strong>'.$rectrans['emp_name'].' - Attendance @ '.$attendance_time.'</strong></td>
								</tr>';
			}
			else
			{
				$messagestring.='<tr>
									<td align="left" colspan="6" style="BORDER: #000000 1px solid;">
									<strong>'.$rectrans['emp_name'].' - No Attendance Given</strong></td>
								</tr>';
			}
			if(($attendance_time!='' && $emp_wise_total_trans>1) || ($attendance_time=='' && $emp_wise_total_trans>0)){
			$messagestring.='<tr>
								<td align="left" width="36%"  style="BORDER: #000000 1px solid;">Customer</td>
								<td align="left" width="14%" style="BORDER: #000000 1px solid;">Time</td>
								<td align="left" width="12%" style="BORDER: #000000 1px solid;">Order Qty</td>
								<td align="left" width="14%" style="BORDER: #000000 1px solid;">Collection</td>
								<td align="left" width="" style="BORDER: #000000 1px solid;">Cheque/Cash</td>
								<td align="left" width="12%" style="BORDER: #000000 1px solid;">Cheque Date</td>
							 </tr>';
			}
		}
		else
		{
			$messagestring.='';
		}

		switch($operation_type)
		{
			case 'O':

				$order_flag=1;
				$order_no=$trans_id;
				$sqlcustomer="SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_qty FROM order_header OH,customer_master CM,
								order_details OD
							  WHERE CM.customer_code=OH.customer_code AND OD.order_no=OH.order_no  AND OH.order_no='".$order_no."' GROUP BY OD.order_no";
				
				$rscustomer=mysqli_query($link,$sqlcustomer) or die(mysqli_error()." Error in select customer: ".$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$final_order_qty=$final_order_qty+$rowcustomer['total_qty'];
				
				$messagestring.='<tr>
									<td align="left" style="BORDER: #000000 1px solid;">'.$rowcustomer['customer_name'].'</td>
									<td align="left" style="BORDER: #000000 1px solid;">'.$rectrans['time'].'</td>
									<td align="right" style="BORDER: #000000 1px solid;">'.$rowcustomer['total_qty'].'</td>
									<td align="right" style="BORDER: #000000 1px solid;">---</td>
									<td align="right" style="BORDER: #000000 1px solid;">---</td>
									<td align="left" style="BORDER: #000000 1px solid;">---</td>
							 	</tr>';
				break;
			 
			 case 'P':
			 
				$payment_flag=1;
				$receipt_id=$trans_id;
				$sqlcustomerpayment="SELECT CM.customer_name,CM.customer_code,SUM(PD.amount) AS amount,
									PH.cash_cheque,PH.cheque_no,DATE_FORMAT(PH.date,'%d-%m-%Y') as cheque_date 
									FROM payment_header PH,customer_master CM,payment_details PD
									WHERE CM.customer_code=PH.customer_code AND PH.receipt_id=PD.receipt_id 
									AND PH.receipt_id='".$receipt_id."' GROUP BY PD.receipt_id";
				$rscustomerpayment=mysqli_query($link,$sqlcustomerpayment) or die(mysqli_error()." Error in select customer payment: ".$sqlcustomerpayment);
				$rowcustomerpayment=mysqli_fetch_assoc($rscustomerpayment);
				$cash_cheque=$rowcustomerpayment['cash_cheque'];
				$final_collection_amount=$final_collection_amount+$rowcustomerpayment['amount'];
				
					if($cash_cheque=='CASH')
					{
						$messagestring.='<tr>
											<td align="left" style="BORDER: #000000 1px solid;">'.$rowcustomerpayment['customer_name'].'</td>
											<td align="left" style="BORDER: #000000 1px solid;">'.$rectrans['time'].'</td>
											<td align="right" style="BORDER: #000000 1px solid;">---</td>
											<td align="right" style="BORDER: #000000 1px solid;">'.number_format($rowcustomerpayment['amount'],2).'</td>
											<td align="right" style="BORDER: #000000 1px solid;">CASH</td>
											<td align="left" style="BORDER: #000000 1px solid;">---</td>
										</tr>';
					}
					else
					{
						$cheque_no=$rowcustomerpayment['cheque_no'];
						$cheque_date=$rowcustomerpayment['cheque_date'];
						$messagestring.='<tr>
											<td align="left" style="BORDER: #000000 1px solid;">'.$rowcustomerpayment['customer_name'].'</td>
											<td align="left" style="BORDER: #000000 1px solid;">'.$rectrans['time'].'</td>
											<td align="right" style="BORDER: #000000 1px solid;">---</td>
											<td align="right" style="BORDER: #000000 1px solid;">'.number_format($rowcustomerpayment['amount'],2).'</td>
											<td align="right" style="BORDER: #000000 1px solid;">'.$cheque_no.'</td>
											<td align="left" style="BORDER: #000000 1px solid;">'.$cheque_date.'</td>
										</tr>';
					}
									
				break;
			case 'N':
				$operation_type_no=substr($trans_id,1,1);
				if($operation_type_no=='O')
				{
					$order_no=$trans_id;
					$sqlcustomernoorder="SELECT CM.customer_name,CM.customer_code FROM order_header OH,customer_master CM 
										WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."'";
					$rscustomernoorder=mysqli_query($link,$sqlcustomernoorder) or die(mysqli_error()." Error in select customer for no order: ".$sqlcustomernoorder);
					$rowcustomernoorder=mysqli_fetch_assoc($rscustomernoorder);
					
					$messagestring.='<tr>
										<td align="left" style="BORDER: #000000 1px solid;">'.$rowcustomernoorder['customer_name'].'</td>
										<td align="left" style="BORDER: #000000 1px solid;">'.$rectrans['time'].'</td>
										<td align="right" style="BORDER: #000000 1px solid;">---</td>
										<td align="right" style="BORDER: #000000 1px solid;">---</td>
										<td align="right" style="BORDER: #000000 1px solid;">---</td>
										<td align="left" style="BORDER: #000000 1px solid;">---</td>
									</tr>';
				}
				if($operation_type_no=='C')
				{
					$receipt_id=$trans_id;
					$sqlcustomernocollection="SELECT CM.customer_name,CM.customer_code FROM payment_header PH,customer_master CM 
											   WHERE CM.customer_code=PH.customer_code AND PH.receipt_id='".$receipt_id."'";
					$rscustomernocollection=mysqli_query($link,$sqlcustomernocollection) or die(mysqli_error()." 
												Error in select customer for no collection: ".$sqlcustomernocollection);
					$rowcustomernocollection=mysqli_fetch_assoc($rscustomernocollection);
					
					$messagestring.='<tr>
										<td align="left" style="BORDER: #000000 1px solid;">'.$rowcustomernocollection['customer_name'].'</td>
										<td align="left" style="BORDER: #000000 1px solid;">'.$rectrans['time'].'</td>
										<td align="right" style="BORDER: #000000 1px solid;">---</td>
										<td align="right" style="BORDER: #000000 1px solid;">---</td>
										<td align="right" style="BORDER: #000000 1px solid;">---</td>
										<td align="left" style="BORDER: #000000 1px solid;">---</td>
									</tr>';
				}
				break;
		}
			
			if(in_array($countloop,$emptotaltrans) && ($order_flag==1 || $payment_flag==1))
			{
				$messagestring.= '<tr>
										<td align="left" style="BORDER: #000000 1px solid;" colspan="2"><strong>Total</strong></td>
										<td align="right" style="BORDER: #000000 1px solid;">'.$emp_wise_total_order.'</td>
										<td align="right" style="BORDER: #000000 1px solid;">'.number_format($emp_wise_total_collection,2).'</td>
										<td align="right" style="BORDER: #000000 1px solid;"></td>
										<td align="left" style="BORDER: #000000 1px solid;"></td>
									</tr>';
			}
		$countloop++;							
	} 
	if($order_flag==1 || $payment_flag==1)
	{
	$messagestring.= '<br /><tr>
						<td align="left" style="BORDER: #000000 1px solid;" colspan="2"><strong>Grand Total</strong></td>
						<td align="right" style="BORDER: #000000 1px solid;">'.$final_order_qty.'</td>
						<td align="right" style="BORDER: #000000 1px solid;">'.number_format($final_collection_amount,2).'</td>
						<td align="right" style="BORDER: #000000 1px solid;"></td>
						<td align="left" style="BORDER: #000000 1px solid;"></td>
					</tr>
					</table></td></tr></table>';
	}
}
else
{
	$messagestring.='<tr>
						<td align="left" style="BORDER: #000000 1px solid;" colspan="2"><strong>No operations performed</strong></td>
					</tr></table></td></tr></table>';
}
$messagestring.= '<br><br><table  width="60%" align="left" border="0" cellpadding="5" cellspacing="2">
					<tr><td >Powered By aceDNS</td></tr><tr><td>&nbsp;</td></table></body></html>';
//************************ Send Email ****************************************************//
	$email_to='sales@purbanchal.in';
	//$email_to='';
	$mailsubj="GULF - NAPTC - aceDNS Daily Sales Report";
	
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=UTF-8\n";
	$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
				"Reply-To:".FROMEMAIL." \r\n" .
				"Bcc: ".BCCEMAIL." \r\n".
				'X-Mailer: PHP/' . phpversion();
	@mail($email_to, $mailsubj, $messagestring, $headers);
	mysqli_close($link);
	
	//-------------- For NAPTC End-------------------------------------------------------------------------------------------------
	/*define("DBONE","acedns_KUNJ");

//require("include/dbcon.php");
$linkone=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
mysqli_select_db(DBONE,$linkone) or die("could not connect the database for invalid nick name");
$messagestringmail='';
$empvalue=array();
$emptotaltrans=array();
$final_order_qty=0;
$final_collection_amount=0;
$emp_wise_total_trans=0;
$countloop=1;

$messagestringmail='<html><body>
				<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0" >
				<tr> 
					<td colspan="4" align="left"><strong>DAILY REPORT: '.date('d/m/Y',strtotime($date)).'</strong></td>
				</tr><br />
				<tr>
					<td>
						<table width="98%" align="center" cellpadding="5" cellspacing="2">';
									  
$sqltrans="SELECT EM.emp_name,EM.emp_code,LO.trans_id,DATE_FORMAT(LO.date,'%T') AS time FROM location LO,employee_master EM 
			WHERE EM.emp_code=LO.emp_code AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$date."%' 
			ORDER BY EM.emp_name ASC,LO.date DESC ";
$restrans=mysqli_query($link,$sqltrans) or die(mysqli_error()." Error in select transaction id: ".$sqltrans);
$count=mysqli_num_rows($restrans);
if($count>0)
{
	while($rectrans = mysqli_fetch_assoc($restrans)) 
	{ 
		$trans_id=$rectrans['trans_id'];
		$operation_type=substr($trans_id,0,1);
		
		if(!in_array($rectrans['emp_name'],$empvalue))
		{
			array_push($empvalue,$rectrans['emp_name']);
			$sqlempwisetotaltrans="SELECT COUNT(trans_id) AS total_trans FROM location WHERE emp_code='".$rectrans['emp_code']."' 
									AND DATE_FORMAT(date,'%Y-%m-%d') LIKE '%".$date."%'";
			$rsempwisetotaltrans=mysqli_query($link,$sqlempwisetotaltrans);
			$recempwisetotaltrans=mysqli_fetch_assoc($rsempwisetotaltrans);
			$emp_wise_total_trans=$emp_wise_total_trans+$recempwisetotaltrans['total_trans'];
			array_push($emptotaltrans,$emp_wise_total_trans);
			
			$sqlempwisetotalorder="SELECT SUM(OD.qty) AS total_order_received
							FROM order_details OD,location LO
							WHERE LO.emp_code='".$rectrans['emp_code']."' 
							AND LO.trans_id LIKE 'O%' AND LO.trans_id=OD.order_no 
							AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$date."%'";
			$rsempwisetotalorder=mysqli_query($link,$sqlempwisetotalorder);
			$recempwisetotalorder=mysqli_fetch_assoc($rsempwisetotalorder);
			$emp_wise_total_order=$recempwisetotalorder['total_order_received'];
			
			$sqlempwisetotalcollection="SELECT SUM(PD.amount) AS total_collection_received
										FROM payment_details PD,location LO
										WHERE  LO.emp_code='".$rectrans['emp_code']."' 
										AND LO.trans_id LIKE 'P%' AND LO.trans_id=PD.receipt_id 
										AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$date."%'";
			$rsempwisetotalcollection=mysqli_query($link,$sqlempwisetotalcollection);
			$recempwisetotalcollection=mysqli_fetch_assoc($rsempwisetotalcollection);
			$emp_wise_total_collection=$recempwisetotalcollection['total_collection_received'];
			
			$sqlempwiseattendance="SELECT DATE_FORMAT(date,'%T') AS time FROM location WHERE emp_code='".$rectrans['emp_code']."' 
									AND trans_id LIKE '%A%' AND DATE_FORMAT(date,'%Y-%m-%d') LIKE '%".$date."%'";
			$rsempwiseattendance=mysqli_query($link,$sqlempwiseattendance);
			$recempwiseattendance=mysqli_fetch_assoc($rsempwiseattendance);
			$attendance_time=$recempwiseattendance['time'];
						
									
			if(count($empvalue)>1)
			{
				$messagestring.='<br />';
			}
			if($attendance_time!='')
			{
				$messagestringmail.='<tr>
									<td align="left" colspan="6" style="BORDER: #000000 1px solid;">
									<strong>'.$rectrans['emp_name'].' - Attendance @ '.$attendance_time.'</strong></td>
								</tr>';
			}
			else
			{
				$messagestringmail.='<tr>
									<td align="left" colspan="6" style="BORDER: #000000 1px solid;">
									<strong>'.$rectrans['emp_name'].' - No Attendance Given</strong></td>
								</tr>';
			}
			if(($attendance_time!='' && $emp_wise_total_trans>1) || ($attendance_time=='' && $emp_wise_total_trans>0)){
			$messagestringmail.='<tr>
								<td align="left" width="36%"  style="BORDER: #000000 1px solid;">Customer</td>
								<td align="left" width="14%" style="BORDER: #000000 1px solid;">Time</td>
								<td align="left" width="12%" style="BORDER: #000000 1px solid;">Order Qty</td>
								<td align="left" width="14%" style="BORDER: #000000 1px solid;">Collection</td>
								<td align="left" width="" style="BORDER: #000000 1px solid;">Cheque/Cash</td>
								<td align="left" width="12%" style="BORDER: #000000 1px solid;">Cheque Date</td>
							 </tr>';
			}
		}
		else
		{
			$messagestringmail.='';
		}

		switch($operation_type)
		{
			case 'O':

				$order_flag=1;
				$order_no=$trans_id;
				$sqlcustomer="SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_qty FROM order_header OH,customer_master CM,
								order_details OD
							  WHERE CM.customer_code=OH.customer_code AND OD.order_no=OH.order_no  AND OH.order_no='".$order_no."' GROUP BY OD.order_no";
				
				$rscustomer=mysqli_query($link,$sqlcustomer) or die(mysqli_error()." Error in select customer: ".$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$final_order_qty=$final_order_qty+$rowcustomer['total_qty'];
				
				$messagestringmail.='<tr>
									<td align="left" style="BORDER: #000000 1px solid;">'.$rowcustomer['customer_name'].'</td>
									<td align="left" style="BORDER: #000000 1px solid;">'.$rectrans['time'].'</td>
									<td align="right" style="BORDER: #000000 1px solid;">'.$rowcustomer['total_qty'].'</td>
									<td align="right" style="BORDER: #000000 1px solid;">---</td>
									<td align="right" style="BORDER: #000000 1px solid;">---</td>
									<td align="left" style="BORDER: #000000 1px solid;">---</td>
							 	</tr>';
				break;
			 
			 case 'P':
			 
				$payment_flag=1;
				$receipt_id=$trans_id;
				$sqlcustomerpayment="SELECT CM.customer_name,CM.customer_code,SUM(PD.amount) AS amount,
									PH.cash_cheque,PH.cheque_no,DATE_FORMAT(PH.date,'%d-%m-%Y') as cheque_date 
									FROM payment_header PH,customer_master CM,payment_details PD
									WHERE CM.customer_code=PH.customer_code AND PH.receipt_id=PD.receipt_id 
									AND PH.receipt_id='".$receipt_id."' GROUP BY PD.receipt_id";
				$rscustomerpayment=mysqli_query($link,$sqlcustomerpayment) or die(mysqli_error()." Error in select customer payment: ".$sqlcustomerpayment);
				$rowcustomerpayment=mysqli_fetch_assoc($rscustomerpayment);
				$cash_cheque=$rowcustomerpayment['cash_cheque'];
				$final_collection_amount=$final_collection_amount+$rowcustomerpayment['amount'];
				
					if($cash_cheque=='CASH')
					{
						$messagestringmail.='<tr>
											<td align="left" style="BORDER: #000000 1px solid;">'.$rowcustomerpayment['customer_name'].'</td>
											<td align="left" style="BORDER: #000000 1px solid;">'.$rectrans['time'].'</td>
											<td align="right" style="BORDER: #000000 1px solid;">---</td>
											<td align="right" style="BORDER: #000000 1px solid;">'.number_format($rowcustomerpayment['amount'],2).'</td>
											<td align="right" style="BORDER: #000000 1px solid;">CASH</td>
											<td align="left" style="BORDER: #000000 1px solid;">---</td>
										</tr>';
					}
					else
					{
						$cheque_no=$rowcustomerpayment['cheque_no'];
						$messagestringmail.='<tr>
											<td align="left" style="BORDER: #000000 1px solid;">'.$rowcustomerpayment['customer_name'].'</td>
											<td align="left" style="BORDER: #000000 1px solid;">'.$rectrans['time'].'</td>
											<td align="right" style="BORDER: #000000 1px solid;">---</td>
											<td align="right" style="BORDER: #000000 1px solid;">'.number_format($rowcustomerpayment['amount'],2).'</td>
											<td align="right" style="BORDER: #000000 1px solid;">'.$cheque_no.'</td>
											<td align="left" style="BORDER: #000000 1px solid;">'.$cheque_date.'</td>
										</tr>';
					}
									
				break;
			case 'N':
				$operation_type_no=substr($trans_id,1,1);
				if($operation_type_no=='O')
				{
					$order_no=$trans_id;
					$sqlcustomernoorder="SELECT CM.customer_name,CM.customer_code FROM order_header OH,customer_master CM 
										WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."'";
					$rscustomernoorder=mysqli_query($link,$sqlcustomernoorder) or die(mysqli_error()." Error in select customer for no order: ".$sqlcustomernoorder);
					$rowcustomernoorder=mysqli_fetch_assoc($rscustomernoorder);
					
					$messagestringmail.='<tr>
										<td align="left" style="BORDER: #000000 1px solid;">'.$rowcustomernoorder['customer_name'].'</td>
										<td align="left" style="BORDER: #000000 1px solid;">'.$rectrans['time'].'</td>
										<td align="right" style="BORDER: #000000 1px solid;">---</td>
										<td align="right" style="BORDER: #000000 1px solid;">---</td>
										<td align="right" style="BORDER: #000000 1px solid;">---</td>
										<td align="left" style="BORDER: #000000 1px solid;">---</td>
									</tr>';
				}
				if($operation_type_no=='C')
				{
					$receipt_id=$trans_id;
					$sqlcustomernocollection="SELECT CM.customer_name,CM.customer_code FROM payment_header PH,customer_master CM 
											   WHERE CM.customer_code=PH.customer_code AND PH.receipt_id='".$receipt_id."'";
					$rscustomernocollection=mysqli_query($link,$sqlcustomernocollection) or die(mysqli_error()." 
												Error in select customer for no collection: ".$sqlcustomernocollection);
					$rowcustomernocollection=mysqli_fetch_assoc($rscustomernocollection);
					
					$messagestringmail.='<tr>
										<td align="left" style="BORDER: #000000 1px solid;">'.$rowcustomernocollection['customer_name'].'</td>
										<td align="left" style="BORDER: #000000 1px solid;">'.$rectrans['time'].'</td>
										<td align="right" style="BORDER: #000000 1px solid;">---</td>
										<td align="right" style="BORDER: #000000 1px solid;">---</td>
										<td align="right" style="BORDER: #000000 1px solid;">---</td>
										<td align="left" style="BORDER: #000000 1px solid;">---</td>
									</tr>';
				}
				break;
		}
			
			if(in_array($countloop,$emptotaltrans) && ($order_flag==1 || $payment_flag==1))
			{
				$messagestringmail.= '<tr>
										<td align="left" style="BORDER: #000000 1px solid;" colspan="2"><strong>Total</strong></td>
										<td align="right" style="BORDER: #000000 1px solid;">'.$emp_wise_total_order.'</td>
										<td align="right" style="BORDER: #000000 1px solid;">'.number_format($emp_wise_total_collection,2).'</td>
										<td align="right" style="BORDER: #000000 1px solid;"></td>
										<td align="left" style="BORDER: #000000 1px solid;"></td>
									</tr>';
			}
		$countloop++;							
	} 
	if($order_flag==1 || $payment_flag==1)
	{
	$messagestringmail.= '<br /><tr>
						<td align="left" style="BORDER: #000000 1px solid;" colspan="2"><strong>Grand Total</strong></td>
						<td align="right" style="BORDER: #000000 1px solid;">'.$final_order_qty.'</td>
						<td align="right" style="BORDER: #000000 1px solid;">'.number_format($final_collection_amount,2).'</td>
						<td align="right" style="BORDER: #000000 1px solid;"></td>
						<td align="left" style="BORDER: #000000 1px solid;"></td>
					</tr>
					</table></td></tr></table>';
	}
}
else
{
	$messagestringmail.='<tr>
						<td align="left" style="BORDER: #000000 1px solid;" colspan="2"><strong>No operations performed</strong></td>
					</tr></table></td></tr></table>';
}
$messagestringmail.= '<br><br><table  width="60%" align="left" border="0" cellpadding="5" cellspacing="2">
					<tr><td >Powered By aceDNS</td></tr><tr><td>&nbsp;</td></table></body></html>';
//************************ Send Email ****************************************************//*/
	/*if($nick_name_string_Array[$count]=='NAPTC'){
	$email_to='sales@purbanchal.in';
	$mailsubj="GULF - NAPTC - aceDNS Daily Sales Report";
	}*/
	/*$email_to='kunj.rachit@gmail.com';
	//$email_to='';
	$mailsubj="KUNJ - aceDNS Daily Sales Report";
	
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=UTF-8\n";
	$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
				"Reply-To:".FROMEMAIL." \r\n" .
				"Bcc: ".BCCEMAIL." \r\n".
				'X-Mailer: PHP/' . phpversion();
	@mail($email_to, $mailsubj, $messagestringmail, $headers);
	mysqli_close($linkone);*/
	
//------------------------------------------ For KUNJ End-------------------------------------------------------------------------------------------------
	/*define("DBTWO","acedns_SETHS");

//require("include/dbcon.php");
$linktwo=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
mysqli_select_db(DBTWO,$linktwo) or die("could not connect the database for invalid nick name");
$messagestringmail='';
$empvalue=array();
$emptotaltrans=array();
$final_order_qty=0;
$final_collection_amount=0;
$emp_wise_total_trans=0;
$countloop=1;

$messagestringmail='<html><body>
				<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0" >
				<tr> 
					<td colspan="4" align="left"><strong>DAILY REPORT: '.date('d/m/Y',strtotime($date)).'</strong></td>
				</tr><br />
				<tr>
					<td>
						<table width="98%" align="center" cellpadding="5" cellspacing="2">';
									  
$sqltrans="SELECT EM.emp_name,EM.emp_code,LO.trans_id,DATE_FORMAT(LO.date,'%T') AS time FROM location LO,employee_master EM 
			WHERE EM.emp_code=LO.emp_code AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$date."%' 
			ORDER BY EM.emp_name ASC,LO.date DESC ";
$restrans=mysqli_query($link,$sqltrans) or die(mysqli_error()." Error in select transaction id: ".$sqltrans);
$count=mysqli_num_rows($restrans);
if($count>0)
{
	while($rectrans = mysqli_fetch_assoc($restrans)) 
	{ 
		$trans_id=$rectrans['trans_id'];
		$operation_type=substr($trans_id,0,1);
		
		if(!in_array($rectrans['emp_name'],$empvalue))
		{
			array_push($empvalue,$rectrans['emp_name']);
			$sqlempwisetotaltrans="SELECT COUNT(trans_id) AS total_trans FROM location WHERE emp_code='".$rectrans['emp_code']."' 
									AND DATE_FORMAT(date,'%Y-%m-%d') LIKE '%".$date."%'";
			$rsempwisetotaltrans=mysqli_query($link,$sqlempwisetotaltrans);
			$recempwisetotaltrans=mysqli_fetch_assoc($rsempwisetotaltrans);
			$emp_wise_total_trans=$emp_wise_total_trans+$recempwisetotaltrans['total_trans'];
			array_push($emptotaltrans,$emp_wise_total_trans);
			
			$sqlempwisetotalorder="SELECT SUM(OD.qty) AS total_order_received
							FROM order_details OD,location LO
							WHERE LO.emp_code='".$rectrans['emp_code']."' 
							AND LO.trans_id LIKE 'O%' AND LO.trans_id=OD.order_no 
							AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$date."%'";
			$rsempwisetotalorder=mysqli_query($link,$sqlempwisetotalorder);
			$recempwisetotalorder=mysqli_fetch_assoc($rsempwisetotalorder);
			$emp_wise_total_order=$recempwisetotalorder['total_order_received'];
			
			$sqlempwisetotalcollection="SELECT SUM(PD.amount) AS total_collection_received
										FROM payment_details PD,location LO
										WHERE  LO.emp_code='".$rectrans['emp_code']."' 
										AND LO.trans_id LIKE 'P%' AND LO.trans_id=PD.receipt_id 
										AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$date."%'";
			$rsempwisetotalcollection=mysqli_query($link,$sqlempwisetotalcollection);
			$recempwisetotalcollection=mysqli_fetch_assoc($rsempwisetotalcollection);
			$emp_wise_total_collection=$recempwisetotalcollection['total_collection_received'];
			
			$sqlempwiseattendance="SELECT DATE_FORMAT(date,'%T') AS time FROM location WHERE emp_code='".$rectrans['emp_code']."' 
									AND trans_id LIKE '%A%' AND DATE_FORMAT(date,'%Y-%m-%d') LIKE '%".$date."%'";
			$rsempwiseattendance=mysqli_query($link,$sqlempwiseattendance);
			$recempwiseattendance=mysqli_fetch_assoc($rsempwiseattendance);
			$attendance_time=$recempwiseattendance['time'];
						
									
			if(count($empvalue)>1)
			{
				$messagestring.='<br />';
			}
			if($attendance_time!='')
			{
				$messagestringmail.='<tr>
									<td align="left" colspan="5" style="BORDER: #000000 1px solid;">
									<strong>'.$rectrans['emp_name'].' - Attendance @ '.$attendance_time.'</strong></td>
								</tr>';
			}
			else
			{
				$messagestringmail.='<tr>
									<td align="left" colspan="5" style="BORDER: #000000 1px solid;">
									<strong>'.$rectrans['emp_name'].' - No Attendance Given</strong></td>
								</tr>';
			}
			if(($attendance_time!='' && $emp_wise_total_trans>1) || ($attendance_time=='' && $emp_wise_total_trans>0)){
			$messagestringmail.='<tr>
								<td align="left" width="45%"  style="BORDER: #000000 1px solid;">Customer</td>
								<td align="left" width="15%" style="BORDER: #000000 1px solid;">Time</td>
								<td align="left" width="12%" style="BORDER: #000000 1px solid;">Order Qty</td>
								<td align="left" width="16%" style="BORDER: #000000 1px solid;">Collection</td>
								<td align="left" width="" style="BORDER: #000000 1px solid;">Cheque/Cash</td>
							 </tr>';
			}
		}
		else
		{
			$messagestringmail.='';
		}

		switch($operation_type)
		{
			case 'O':

				$order_flag=1;
				$order_no=$trans_id;
				$sqlcustomer="SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_qty FROM order_header OH,customer_master CM,
								order_details OD
							  WHERE CM.customer_code=OH.customer_code AND OD.order_no=OH.order_no  AND OH.order_no='".$order_no."' GROUP BY OD.order_no";
				
				$rscustomer=mysqli_query($link,$sqlcustomer) or die(mysqli_error()." Error in select customer: ".$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$final_order_qty=$final_order_qty+$rowcustomer['total_qty'];
				
				$messagestringmail.='<tr>
									<td align="left" style="BORDER: #000000 1px solid;">'.$rowcustomer['customer_name'].'</td>
									<td align="left" style="BORDER: #000000 1px solid;">'.$rectrans['time'].'</td>
									<td align="right" style="BORDER: #000000 1px solid;">'.$rowcustomer['total_qty'].'</td>
									<td align="right" style="BORDER: #000000 1px solid;">---</td>
									<td align="right" style="BORDER: #000000 1px solid;">---</td>
							 	</tr>';
				break;
			 
			 case 'P':
			 
				$payment_flag=1;
				$receipt_id=$trans_id;
				$sqlcustomerpayment="SELECT CM.customer_name,CM.customer_code,SUM(PD.amount) AS amount,
									PH.cash_cheque,PH.cheque_no
									FROM payment_header PH,customer_master CM,payment_details PD
									WHERE CM.customer_code=PH.customer_code AND PH.receipt_id=PD.receipt_id 
									AND PH.receipt_id='".$receipt_id."' GROUP BY PD.receipt_id";
				$rscustomerpayment=mysqli_query($link,$sqlcustomerpayment) or die(mysqli_error()." Error in select customer payment: ".$sqlcustomerpayment);
				$rowcustomerpayment=mysqli_fetch_assoc($rscustomerpayment);
				$cash_cheque=$rowcustomerpayment['cash_cheque'];
				$final_collection_amount=$final_collection_amount+$rowcustomerpayment['amount'];
				
					if($cash_cheque=='CASH')
					{
						$messagestringmail.='<tr>
											<td align="left" style="BORDER: #000000 1px solid;">'.$rowcustomerpayment['customer_name'].'</td>
											<td align="left" style="BORDER: #000000 1px solid;">'.$rectrans['time'].'</td>
											<td align="right" style="BORDER: #000000 1px solid;">---</td>
											<td align="right" style="BORDER: #000000 1px solid;">'.number_format($rowcustomerpayment['amount'],2).'</td>
											<td align="right" style="BORDER: #000000 1px solid;">CASH</td>
										</tr>';
					}
					else
					{
						$cheque_no=$rowcustomerpayment['cheque_no'];
						$messagestringmail.='<tr>
											<td align="left" style="BORDER: #000000 1px solid;">'.$rowcustomerpayment['customer_name'].'</td>
											<td align="left" style="BORDER: #000000 1px solid;">'.$rectrans['time'].'</td>
											<td align="right" style="BORDER: #000000 1px solid;">---</td>
											<td align="right" style="BORDER: #000000 1px solid;">'.number_format($rowcustomerpayment['amount'],2).'</td>
											<td align="right" style="BORDER: #000000 1px solid;">'.$cheque_no.'</td>
										</tr>';
					}
									
				break;
			case 'N':
				$operation_type_no=substr($trans_id,1,1);
				if($operation_type_no=='O')
				{
					$order_no=$trans_id;
					$sqlcustomernoorder="SELECT CM.customer_name,CM.customer_code FROM order_header OH,customer_master CM 
										WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."'";
					$rscustomernoorder=mysqli_query($link,$sqlcustomernoorder) or die(mysqli_error()." Error in select customer for no order: ".$sqlcustomernoorder);
					$rowcustomernoorder=mysqli_fetch_assoc($rscustomernoorder);
					
					$messagestringmail.='<tr>
										<td align="left" style="BORDER: #000000 1px solid;">'.$rowcustomernoorder['customer_name'].'</td>
										<td align="left" style="BORDER: #000000 1px solid;">'.$rectrans['time'].'</td>
										<td align="right" style="BORDER: #000000 1px solid;">---</td>
										<td align="right" style="BORDER: #000000 1px solid;">---</td>
										<td align="right" style="BORDER: #000000 1px solid;">---</td>
									</tr>';
				}
				if($operation_type_no=='C')
				{
					$receipt_id=$trans_id;
					$sqlcustomernocollection="SELECT CM.customer_name,CM.customer_code FROM payment_header PH,customer_master CM 
											   WHERE CM.customer_code=PH.customer_code AND PH.receipt_id='".$receipt_id."'";
					$rscustomernocollection=mysqli_query($link,$sqlcustomernocollection) or die(mysqli_error()." 
												Error in select customer for no collection: ".$sqlcustomernocollection);
					$rowcustomernocollection=mysqli_fetch_assoc($rscustomernocollection);
					
					$messagestringmail.='<tr>
										<td align="left" style="BORDER: #000000 1px solid;">'.$rowcustomernocollection['customer_name'].'</td>
										<td align="left" style="BORDER: #000000 1px solid;">'.$rectrans['time'].'</td>
										<td align="right" style="BORDER: #000000 1px solid;">---</td>
										<td align="right" style="BORDER: #000000 1px solid;">---</td>
										<td align="right" style="BORDER: #000000 1px solid;">---</td>
									</tr>';
				}
				break;
		}
			
			if(in_array($countloop,$emptotaltrans) && ($order_flag==1 || $payment_flag==1))
			{
				$messagestringmail.= '<tr>
										<td align="left" style="BORDER: #000000 1px solid;" colspan="2"><strong>Total</strong></td>
										<td align="right" style="BORDER: #000000 1px solid;">'.$emp_wise_total_order.'</td>
										<td align="right" style="BORDER: #000000 1px solid;">'.number_format($emp_wise_total_collection,2).'</td>
										<td align="right" style="BORDER: #000000 1px solid;"></td>
									</tr>';
			}
		$countloop++;							
	} 
	if($order_flag==1 || $payment_flag==1)
	{
	$messagestringmail.= '<br /><tr>
						<td align="left" style="BORDER: #000000 1px solid;" colspan="2"><strong>Grand Total</strong></td>
						<td align="right" style="BORDER: #000000 1px solid;">'.$final_order_qty.'</td>
						<td align="right" style="BORDER: #000000 1px solid;">'.number_format($final_collection_amount,2).'</td>
						<td align="right" style="BORDER: #000000 1px solid;"></td>
					</tr>
					</table></td></tr></table>';
	}
}
else
{
	$messagestringmail.='<tr>
						<td align="left" style="BORDER: #000000 1px solid;" colspan="2"><strong>No operations performed</strong></td>
					</tr></table></td></tr></table>';
}
$messagestringmail.= '<br><br><table  width="60%" align="left" border="0" cellpadding="5" cellspacing="2">
					<tr><td >Powered By aceDNS</td></tr><tr><td>&nbsp;</td></table></body></html>';*/
//************************ Send Email ****************************************************//
	/*$email_to='clrathi.unitex@gmail.com,coralak@gmail.com,ashokrathi@universalpoplin.com';
	//$email_to='';
	$mailsubj="SETHS - aceDNS Daily Sales Report";
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=UTF-8\n";
	$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
				"Reply-To:".FROMEMAIL." \r\n" .
				"Bcc: ".BCCEMAIL." \r\n".
				'X-Mailer: PHP/' . phpversion();
	@mail($email_to, $mailsubj, $messagestringmail, $headers);
	mysqli_close($linktwo);*/

?>	