<?php
 
 session_start();
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");

$emp_code=$_REQUEST['emp_code'];
$sqlemp="SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
$rsemp=mysqli_query($link,$sqlemp) or die(mysqli_error()." Error in select employee name and code : ".$sqlemp);
$rowemp=mysqli_fetch_assoc($rsemp);
$emp_name=$rowemp['emp_name'];

$mode=$_REQUEST['mode'];
$page=$_REQUEST['page'];
if($mode=='T')
{
	$date=date('Y-m-d');
	if($date!='' && sale=='no' && instruction=='yes')
	{
		$date_condition ="  AND DATE_FORMAT(date,'%Y-%m-%d') LIKE '%".$date."%'";
	}
	else
	{
		$date_condition ="  AND DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d') LIKE '%".$date."%'";
	}
}
if($mode=='Y')
{
	$date=date('Y-m-d', strtotime("-1 days,$curdate "));
	if(sale=='no' && instruction=='yes')
	{
		$date_condition ="  AND DATE_FORMAT(date,'%Y-%m-%d') LIKE '%".$date."%'";
	}
	else
	{
		$date_condition ="  AND DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d') LIKE '%".$date."%'";
	}
}
if($mode=='YC')
{
	$from_date=date('Y-m-d',strtotime($_REQUEST['from_date']));
	$to_date=date('Y-m-d',strtotime($_REQUEST['to_date']));
	if(sale=='no' && instruction=='yes')
	{
		$date_condition=" AND DATE_FORMAT(date,'%Y-%m-%d') BETWEEN '".$from_date."' AND '".$to_date."'";
	}
	else
	{
		$date_condition=" AND DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d') BETWEEN '".$from_date."' AND '".$to_date."'";
	}
}
if($mode=='MTD')
{
	if(sale=='no' && instruction=='yes')
	{
		$date_condition=" AND YEAR(date) = YEAR(CURDATE()) AND MONTH(date) = MONTH(CURDATE()) AND DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y%-%m-%d') <=DATE_FORMAT(NOW(),'%Y%-%m-%d')";
	}
	else
	{
		$date_condition=" AND YEAR(DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d')) = YEAR(CURDATE()) 
						AND MONTH(DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d')) = MONTH(CURDATE()) ";
	}
}
if($mode=='YTD')
{
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	
	$hour=gmdate('H',strtotime('+330 minute'));
	$minute=gmdate('i',strtotime('+330 minute'));
	$second=gmdate('s',strtotime('+330 minute'));
	
	if($month>='04'){
		$fiinancial_year=$year.'-04-01';
	}
	else
	{
		$fiinancial_year=($year-1).'-04-01';
	}

	if(sale=='no' && instruction=='yes')
	{
		//$date_condition=" AND YEAR(LO.date) = YEAR(CURDATE())";
		$date_condition=" AND DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y%-%m-%d') <=DATE_FORMAT(NOW(),'%Y%-%m-%d') 
					AND DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y%-%m-%d') >='".$fiinancial_year."'";
	}
	else
	{
		//$date_condition=" AND YEAR(DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d')) = YEAR(CURDATE())";
		$date_condition=" AND DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y%-%m-%d') <=DATE_FORMAT(NOW(),'%Y%-%m-%d') 
					AND DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y%-%m-%d') >='".$fiinancial_year."'";
	}
}
?>
<style type="text/css">
.TDHEAD{
	FONT-FAMILY: Verdana;
	FONT-SIZE : 11px;
	FONT-WEIGHT: bold;
	COLOR: #FFFFFF;
	BACKGROUND-COLOR: #A92A61;/*#92C006;*/
}

.TDHEAD_SUB{
	FONT-FAMILY: Verdana;
	FONT-SIZE : 11px;
	FONT-WEIGHT: bold;
	BACKGROUND-COLOR:#c0c8b0;
}

.border{
	BORDER: #A92A61/*#80A537*/ 1px solid;
}
TD{
	FONT-FAMILY: Verdana;
	FONT-SIZE : 11px;
}

</style>
<?php 
$date_array=array();
		if(stk_audit=='yes' || retailer_care=='yes'){
			$stk_audit_customer_TR='<td width="11%" align="left" style="padding-left:20px;">Stock Audit</td>';
			$stk_audit_customer_TD="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">--</td>";
		}
		else
		{
			$stk_audit_customer_TR='';
			$stk_audit_customer_TD='';
		}
		if(sauda_allocation=='yes')
		{
			$order_sauda_text='Sauda';
			$order_sauda_text_one='Booked';
		}
		else
		{
			$order_sauda_text='Order';
			$order_sauda_text_one='Received';	
		}

	if(sauda_allocation=='yes' && sauda_depot_wise=='yes')
	{
		$branch_name_TR='<th width="12%" align="left" style="padding-left:20px;">Depot</td>';
	}
	if(strtoupper($_SESSION['nick_name'])=='ABDOST' || strtoupper($_SESSION['nick_name'])=='ABDOS')
	{
		$tonnage_column='<th width="15%" align="left" style="padding-left:20px;">Tonnage</th>';
	}
	else { $tonnage_column='';}		
$tableval='
<table width="90%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 350px;overflow-y: scroll;display:block;">
    <tr class="TDHEAD" > 
        <td colspan="8" align="center"><strong>Employee Name: '.$emp_name.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Employee Code: '. $emp_code.'</strong></td>
    </tr>
    <tr class="TDHEAD_SUB" style=\"position:fixed;\"> 
         <td width="5%" align="center">Sl</td>
		<th width="12%" align="left" style="padding-left:20px;">Customer code</td>
		<th width="18%" align="left" style="padding-left:20px;">Name</td>'.$branch_name_TR.'
		<th width="16%" align="left" style="padding-left:20px;">'.$order_sauda_text.' '.$order_sauda_text_one.' Quantity<br /><span style="padding-left:20px;"></span></td>'.$tonnage_column.'
		<td width="17%" align="left" style="padding-left:20px;">Order Amount</td>';
		
		if(collection == 'yes'){
			$tableval .= '<th width="18%" align="left" style="padding-left:20px;">Collection Received<br /><span style="padding-left:20px;">(Rs/-)</span></td>';
		}
		if(DCR_map=='yes')
		{
			$locatestring='<td width="" align="left" style="padding-left:20px;">Locate</td>';
		}
		else
		{
			$locatestring='';
		}
		$tableval .= $stk_audit_customer_TR.$locatestring.'</tr>';
		if(check_in_out=='yes' && check_in_out_menu_access=='')
		 {
			$sqltrans="SELECT *,DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%d-%m-%Y') AS date FROM location WHERE (SUBSTRING(trans_id,1,1) IN
						('O','P','S') OR SUBSTRING(trans_id,1,2) IN('NO','NC','CI')) AND SUBSTRING(trans_id,1,2) NOT IN('PA','SU')  
		 			AND emp_code='".$emp_code."'".$date_condition." ORDER BY DATE_FORMAT(SUBSTRING(trans_id,-14,14),'%Y-%m-%d %h:%i:%s') DESC ";

		 }
		 else
		 {
		  $sqltrans="SELECT *,DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%d-%m-%Y') AS date FROM location WHERE (SUBSTRING(trans_id,1,1) IN
						('O','P','S') OR SUBSTRING(trans_id,1,2) IN('NO','NC')) AND SUBSTRING(trans_id,1,2) NOT IN('PA','CI','SU')  
		 			AND emp_code='".$emp_code."'".$date_condition." ORDER BY DATE_FORMAT(SUBSTRING(trans_id,-14,14),'%Y-%m-%d %h:%i:%s') DESC ";
		 }
		 $restrans=mysqli_query($link,$sqltrans) or die(mysqli_error()." Error in select transaction id for employee wise activity: ".$sqltrans);
         $cnt=$GLOBALS['start']+1;
		$rowval=""; 
        while($rowtrans=mysqli_fetch_assoc($restrans))
        {
		  $trans_id=$rowtrans['trans_id'];
		  $latt=$rowtrans['latt'];
		  $longi=$rowtrans['longi'];
		  $operation_type=substr($trans_id,0,1);
		  if($mode!='T' && !in_array($rowtrans['date'],$date_array))
			{
				array_push($date_array,$rowtrans['date']);
				
				if(need_DCR == 'yes')
					{
						$rowval_DCR="-----"."<a href=\"adminDCR.php?emp_code=$emp_code&mode=$mode&page=$page&requiredate=$rowtrans[date]\"style=\"color:#930;font-weight:bold;\">DCR</a>"."";
						$rowval_DCR_mail="-----"."<a href=\"javascript:void(0)\"   style=\"color:#930;font-weight:bold;\" onClick=\"javascript:sendDcrMail('".$emp_code."','".strtoupper($_SESSION['nick_name'])."','".$rowtrans['date']."');\">MAIL DCR</a>";
					}
					else
					{
						$rowval_DCR='';
						$rowval_DCR_mail="";
					}
				$rowval.="<tr> <td valign=\"top\" align=\"center\" colspan=\"6\"><strong>".$rowtrans['date'].$rowval_DCR.$rowval_DCR_mail."</strong></td></tr>";
			}
		  
          if($operation_type=='O')
			{
				$order_no=$trans_id;
				
				$sqlorderheader="SELECT customer_code FROM order_header WHERE order_no='".$order_no."'";
				$rsorderheader=mysqli_query($link,$sqlorderheader) or die(mysqli_error()." Error in select order: ".$sqlorderheader);
				$roworderheader=mysqli_fetch_assoc($rsorderheader);
				$customer_code=$roworderheader['customer_code'];
				
				/*if(substr($customer_code,0,1)=='N')
				{
					$sqlcustomer="SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_order_received FROM 
								  order_header OH,prospective_customer_master CM,order_details OD
								  WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."' AND OH.order_no=OD.order_no 
								   GROUP BY OD.order_no";
				}
				else
				{*/
					if(sale=='no' && sauda_allocation=='no'){
						if(strtoupper($_SESSION['nick_name'])=='ABDOS')
							{
								$sqlcustomer="SELECT CM.customer_name,CM.customer_code,OD.qty,OD.weightage,OD.amount,OD.UOM,OD.sku_code,PM.conversion_factor FROM 
								  order_header OH,customer_master CM,order_details OD,product_master PM
								  WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."' AND OH.order_no=OD.order_no AND OD.sku_code=PM.prod_code ";
								$rscustomer=mysqli_query($link,$sqlcustomer) or die(mysqli_error()." Error in select customer: ".$sqlcustomer);
								$total_order_received=0;
								$total_amount_received=0;
								$weightage_val=0;
								while($rowcustomer=mysqli_fetch_assoc($rscustomer))
								{
									//echo $emp_code.'----------------------';
									$customer_name=$rowcustomer['customer_name'];
									$UOM_order=$rowcustomer['UOM'];
									$prod_code=$rowcustomer['sku_code'];
										if(strtoupper($UOM_order)!='CASE' || $UOM_order=='')
										{
											$conversion_factor=$rowcustomer['conversion_factor'];
											$qtyconverted=$rowcustomer['qty']/$conversion_factor;
											
											$sqlselmrp="SELECT mrp FROM mrp WHERE product_code='".$prod_code."' AND UPPER(UOM)='CASE'";
											$rsselmrp=mysqli_query($link,$sqlselmrp);
											$rowselmrp=mysqli_fetch_assoc($rsselmrp);
											$mrp=$rowselmrp['mrp'];
											$amount=$qtyconverted*$mrp;
											$qty=$qtyconverted;
											$total_order_received=$total_order_received+$qty;
											$total_amount_received=$total_amount_received+$amount;
										}
										else
										{
											$amount=$rowcustomer['amount'];
											$qty=$rowcustomer['qty'];
											$total_order_received=$total_order_received+$qty;
											$total_amount_received=$total_amount_received+$amount;
										}
									//echo '---------------------<br />';
									$weightage_val=$weightage_val+$rowcustomer['weightage'];
									
									}
									$weightage_val=round(($weightage_val/1000),3);
									$tonnage_row='<td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;">'.$weightage_val.'</td>';
								}
								else
								{
								$sqlcustomer="SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_order_received, SUM(OD.amount) AS total_amount_received FROM 
								  order_header OH,customer_master CM,order_details OD
								  WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."' AND OH.order_no=OD.order_no 
								   GROUP BY OD.order_no";
								 $rscustomer=mysqli_query($link,$sqlcustomer) or die(mysqli_error()." Error in select customer: ".$sqlcustomer);
								$rowcustomer=mysqli_fetch_assoc($rscustomer);
								//$customer_code=$rowcustomer['customer_code'];
								$customer_name=$rowcustomer['customer_name'];
								$total_order_received=$rowcustomer['total_order_received'];
								$total_amount_received=$rowcustomer['total_amount_received'];
								$tonnage_row='';
								}
					}
					else if(sauda_allocation=='yes' && sauda_depot_wise=='yes')
					{
						$sqlcustomer="SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_order_received, SUM(OD.amount) AS total_amount_received ,BM.branch_name FROM 
									  order_header OH,customer_master CM,order_details OD,branch_master BM
									  WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."' AND OH.order_no=OD.order_no 
									  AND OH.branch_code=BM.branch_code GROUP BY OD.order_no UNION SELECT VM.vendor_name 
									  AS customer_name,VM.vendor_code AS customer_code,SUM(OD.qty) AS total_order_received,BM.branch_name FROM 
									  order_header OH,vendor_master VM,order_details OD,branch_master BM
									  WHERE VM.vendor_code=OH.customer_code AND OH.branch_code=BM.branch_code AND OH.order_no='".$order_no."' AND OH.order_no=OD.order_no 
									  GROUP BY OD.order_no";
						$rscustomer=mysqli_query($link,$sqlcustomer) or die(mysqli_error()." Error in select customer: ".$sqlcustomer);
						$rowcustomer=mysqli_fetch_assoc($rscustomer);
						//$customer_code=$rowcustomer['customer_code'];
						$customer_name=$rowcustomer['customer_name'];
						$total_order_received=$rowcustomer['total_order_received'];
						$total_amount_received=$rowcustomer['total_amount_received'];
					}
					else
					{
						$sqlcustomer="SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_order_received, SUM(OD.amount) AS total_amount_received FROM 
									  order_header OH,customer_master CM,order_details OD
									  WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."' AND OH.order_no=OD.order_no 
									  GROUP BY OD.order_no UNION SELECT VM.vendor_name 
									  AS customer_name,VM.vendor_code AS customer_code,SUM(OD.qty) AS total_order_received FROM 
									  order_header OH,vendor_master VM,order_details OD
									  WHERE VM.vendor_code=OH.customer_code AND OH.order_no='".$order_no."' AND OH.order_no=OD.order_no 
									  GROUP BY OD.order_no";
						$rscustomer=mysqli_query($link,$sqlcustomer) or die(mysqli_error()." Error in select customer: ".$sqlcustomer);
						$rowcustomer=mysqli_fetch_assoc($rscustomer);
						//$customer_code=$rowcustomer['customer_code'];
						$customer_name=$rowcustomer['customer_name'];
						$total_order_received=$rowcustomer['total_order_received'];
						$total_amount_received=$rowcustomer['total_amount_received'];
					}
				//}
					/*if(sauda_allocation=='yes' && sauda_depot_wise=='yes')
					{
						$branch_name=$rowcustomer['branch_name'];
						$branch_name_TD="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$branch_name."</td>";
					}*/
					if(DCR_map=='yes')
					{
					  if($latt==0 && $longi==0)
					  {
						  $locateval="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;font-weight:bold;\">GPS TURNED OFF</td>";
					  }
					  else
					  {
						$locateval="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
							<a href=\"customerLocate.php?trans_id=$trans_id&customer_code=$customer_code&
							emp_code=$emp_code&date=$date&from_date=$from_date&to_date=$to_date&mode=$mode&page=$page&state=".urlencode($state)."&emp_type=".urlencode($emp_type)."&employee_lev_one=$employee_lev_one&modehierarchy=$modehierarchy\" style=\"color:#000000;font-weight:normal;\">Locate</a></td>";
					  }
					}
					else $locateval='';

				if($customer_name != ''){
					$rowval.="<tr> 
							<td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
							<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
							<a href=\"javascript:void(0)\" onClick=\"javascript:populateOrderDetails('".$trans_id."','".$customer_code."')\" 	style=\"color:#000000;font-weight:normal;\">".$customer_code."</a></td>
							<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$customer_name."</td>".$branch_name_TD."
							<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".round($total_order_received,3)."</td>".$tonnage_row."
							<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".round($total_amount_received,3)."</td>";
						
						if(collection == 'yes'){	
							$rowval.= "<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">--</td>";
						}
						
						$rowval.= $stk_audit_customer_TD.$locateval."
							
				  		</tr>";
				}
				
			}
		if($operation_type=='P')
		{
			$receipt_id=$trans_id;
			
			$sqlpaymentheader="SELECT customer_code,sale_type FROM payment_header WHERE receipt_id='".$receipt_id."'";
			$rspaymentheader=mysqli_query($link,$sqlpaymentheader) or die(mysqli_error()." Error in select payment header: ".$sqlpaymentheader);
			$rowpaymentheader=mysqli_fetch_assoc($rspaymentheader);
			$customer_code=$rowpaymentheader['customer_code'];
			$sale_type=$rowpaymentheader['sale_type'];
				
			if(sale=='no'){
				$sqlcustomerpayment="SELECT CM.customer_name,CM.customer_code,SUM(PD.amount) AS total_collection_received 
							FROM payment_header PH,customer_master CM,payment_details PD
							WHERE CM.customer_code=PH.customer_code AND PH.receipt_id=PD.receipt_id 
							AND PH.receipt_id='".$receipt_id."' GROUP BY PD.receipt_id";
			}
			else
			{
				$sqlcustomerpayment="SELECT CM.customer_name,CM.customer_code,SUM(PD.amount) AS total_collection_received 
							FROM payment_header PH,customer_master CM,payment_details PD
							WHERE CM.customer_code=PH.customer_code AND PH.receipt_id=PD.receipt_id 
							AND PH.receipt_id='".$receipt_id."' GROUP BY PD.receipt_id UNION 
							SELECT VM.vendor_name AS customer_name,VM.vendor_code AS customer_code,SUM(PD.amount) AS total_collection_received 
							FROM payment_header PH,vendor_master VM,payment_details PD
							WHERE VM.vendor_code=PH.customer_code AND PH.receipt_id=PD.receipt_id 
							AND PH.receipt_id='".$receipt_id."' GROUP BY PD.receipt_id
							";
			}
			
			$rscustomerpayment=mysqli_query($link,$sqlcustomerpayment) or die(mysqli_error()." Error in select customer payment: ".$sqlcustomerpayment);
			$rowcustomerpayment=mysqli_fetch_assoc($rscustomerpayment);
			$customer_code=$rowcustomerpayment['customer_code'];
			$customer_name=$rowcustomerpayment['customer_name'];
			
			if($customer_name != ''){
				if(DCR_map=='yes')
					{
						if($latt==0 && $longi==0)
					  	{
							$locateval="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;font-weight:bold;\">GPS TURNED OFF</td>";
						}
						else
						{
						$locateval="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
							<a href=\"customerLocate.php?trans_id=$trans_id&customer_code=$customer_code&
							emp_code=$emp_code&date=$date&from_date=$from_date&to_date=$to_date&mode=$mode&page=$page\" style=\"color:#000000;font-weight:normal;\">Locate</a></td>";
						}
					}
					else $locateval='';
				$rowval.="<tr> 
							<td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
							<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\"><a href=\"javascript:void(0)\" onClick=\"javascript:populateCollectionDetails('".$trans_id."','".$customer_code."','".$sale_type."')\" 	style=\"color:#000000;font-weight:normal;\">".$customer_code."</a></td>
							<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$customer_name."</td>
							<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">--</td>
							<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">--</td>
							<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".number_format($rowcustomerpayment['total_collection_received'],2)."</td>
							".$stk_audit_customer_TD.$locateval."
							
				  		</tr>";
			}
		} 
		if($operation_type=='S')
		{
				$stk_counting_trans_id=$trans_id;
				$sqlcustomer="SELECT CM.customer_name,CM.customer_code  FROM 
							  stock_audit SA,customer_master CM
							  WHERE CM.customer_code=SA.customer_code AND SA.transaction_id='".$stk_counting_trans_id."' 
							  GROUP BY SA.transaction_id";
				$rscustomer=mysqli_query($link,$sqlcustomer) or die(mysqli_error()." Error in select customer: ".$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rscustomer);
				$customer_code=$rowcustomer['customer_code'];
				$customer_name=$rowcustomer['customer_name'];
				
				$sqlstkaudit="SELECT SUM(SA.quantity)AS total_stk_audit FROM stock_audit SA 
							WHERE SA.transaction_id='".$stk_counting_trans_id."' GROUP BY SA.transaction_id";
				$rsstkaudit=mysqli_query($link,$sqlstkaudit) or die(mysqli_error()." Error in total stk audit: ".$sqlstkaudit);
				$rowstkaudit=mysqli_fetch_assoc($rsstkaudit);
				$stk_audit=$rowstkaudit['total_stk_audit'];
				
				if($customer_name != ''){
					if(DCR_map=='yes')
					{
						if($latt==0 && $longi==0)
					  	{
							$locateval="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;font-weight:bold;\">GPS TURNED OFF</td>";
						}
						else
						{
							$locateval="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
							<a href=\"customerLocate.php?trans_id=$trans_id&customer_code=$customer_code&
							emp_code=$emp_code&date=$date&from_date=$from_date&to_date=$to_date&mode=$mode&page=$page\" style=\"color:#000000;font-weight:normal;\">Locate</a></td>";
						}
					}
					else $locateval='';

					$rowval.="<tr> 
						<td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
						<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
						<a href=\"javascript:void(0)\" onClick=\"javascript:populateAuditDetails('".$trans_id."','".$customer_code."')\" 	style=\"color:#000000;font-weight:normal;\">".$customer_code."</a></td>
						<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$customer_name."</td>
						<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">--</td>
						<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">--</td>";
						if(collection == 'yes'){	
							$rowval.= "<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">--</td>";
						}
						$rowval.="<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".round($stk_audit,3)."</td>".$locateval."
						
					</tr>";
				}
		
			
		}
		if($operation_type=='N')
		{
			$operation_type_no=substr($trans_id,1,1);
			if($operation_type_no=='O')
				{
					$order_no=$trans_id;
					$sqlcustomernoorder="SELECT CM.customer_name,CM.customer_code FROM order_header OH,customer_master CM 
										WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."'";
					$rscustomernoorder=mysqli_query($link,$sqlcustomernoorder) or die(mysqli_error()." Error in select customer for no order: ".$sqlcustomernoorder);
					$rowcustomernoorder=mysqli_fetch_assoc($rscustomernoorder);
			
					$customer_code=$rowcustomernoorder['customer_code'];
					$customer_name=$rowcustomernoorder['customer_name'];
					
					if($customer_name != ''){
						if(DCR_map=='yes')
						{
							if($latt==0 && $longi==0)
							{
								$locateval="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;font-weight:bold;\">GPS TURNED OFF</td>";
							}
							else
							{
							$locateval="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
								<a href=\"customerLocate.php?trans_id=$trans_id&customer_code=$customer_code&
								emp_code=$emp_code&date=$date&from_date=$from_date&to_date=$to_date&mode=$mode&page=$page\" style=\"color:#000000;font-weight:normal;\">Locate</a></td>";
							}
						}
					else $locateval='';
						$rowval.="<tr> 
								<td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
								<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$customer_code."</td>
								<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$customer_name."</td>
								<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">--</td>
								<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">--</td>";
								if(collection == 'yes'){	
							$rowval.= "<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">--</td>";
							}
								$rowval.= $stk_audit_customer_TD.$locateval."
								
							</tr>";
					}
					
				}
				if($operation_type_no=='C')
				{
					$receipt_id=$trans_id;
					$sqlcustomernocollection="SELECT CM.customer_name,CM.customer_code FROM payment_header PH,customer_master CM 
											   WHERE CM.customer_code=PH.customer_code AND PH.receipt_id='".$receipt_id."'";
					$rscustomernocollection=mysqli_query($link,$sqlcustomernocollection) or die(mysqli_error()." 
												Error in select customer for no collection: ".$sqlcustomernocollection);
					$rowcustomernocollection=mysqli_fetch_assoc($rscustomernocollection);
			
					$customer_code=$rowcustomernocollection['customer_code'];
					$customer_name=$rowcustomernocollection['customer_name'];
					
					if($customer_name != ''){
						if(DCR_map=='yes')
						{
							if($latt==0 && $longi==0)
							{
								$locateval="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;font-weight:bold;\">GPS TURNED OFF</td>";
							}
							else
							{
							$locateval="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
								<a href=\"customerLocate.php?trans_id=$trans_id&customer_code=$customer_code&
								emp_code=$emp_code&date=$date&from_date=$from_date&to_date=$to_date&mode=$mode&page=$page\" style=\"color:#000000;font-weight:normal;\">Locate</a></td>";
							}
						}
					else $locateval='';
						$rowval.="<tr> 
								<td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
								<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$customer_code."</td>
								<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$customer_name."</td>
								<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">--</td>
								<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">--</td>";
								if(collection == 'yes'){	
							$rowval.= "<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">--</td>";
							}
								$rowval.= $stk_audit_customer_TD.$locateval."
								
							</tr>";
					}
					
				}
		 }
		 if($operation_type=='C')
		 {
			$operation_type_no=substr($trans_id,1,1);
			if($operation_type_no=='I')
				{
					$ci_trans_id=$trans_id;
					$sqlcustomercheckinout="SELECT CM.customer_name,CM.customer_code,DATE_FORMAT(SUBSTRING(CIO.check_in_time,1,10),'%d-%m-%Y') AS  checkindate FROM check_in_out_details CIO,customer_master CM 
										WHERE CM.customer_code=CIO.customer_code AND CIO.trans_id='".$ci_trans_id."'";
					$rscustomercheckinout=mysqli_query($link,$sqlcustomercheckinout) or die(mysqli_error()." Error in select customer for checkin out: ".$sqlcustomercheckinout);
					$rowcustomercheckinout=mysqli_fetch_assoc($rscustomercheckinout);
			
					$customer_code=$rowcustomercheckinout['customer_code'];
					$customer_name=$rowcustomercheckinout['customer_name'];
					
					if($customer_name != '' && $rowtrans['date']==$rowcustomercheckinout['checkindate']){
						if(DCR_map=='yes')
						{
							if($latt==0 && $longi==0)
							{
								$locateval="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;font-weight:bold;\">GPS TURNED OFF</td>";
							}
							else
							{
							$locateval="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
								<a href=\"customerLocate.php?trans_id=$trans_id&customer_code=$customer_code&
								emp_code=$emp_code&date=$date&from_date=$from_date&to_date=$to_date&mode=$mode&page=$page\" style=\"color:#000000;font-weight:normal;\">Locate</a></td>";
							}
						}
						else $locateval='';
						$rowval.="<tr> 
								<td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
								<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$customer_code."</td>
								<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$customer_name."</td>
								<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">--</td>
								<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">--</td>";
								if(collection == 'yes'){	
							$rowval.= "<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">--</td>";
							}
								$rowval.=$stk_audit_customer_TD.$locateval."
							</tr>";
					}
					
				}
		 }
      }
$tablevalend='</table>';				
$finalval=$tableval.$rowval.$tablevalend;

echo $finalval;
mysqli_close($link);
?>