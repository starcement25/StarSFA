<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("../include/functions.php");

$from_date_pre=$_REQUEST['fromDate'];
$from_date=date('Y-m-d',strtotime($from_date_pre));
$to_date_pre=$_REQUEST['toDate'];	
$to_date=date('Y-m-d',strtotime($to_date_pre));

if($_SESSION['admin_login']=="admin"){
	$emp_hierarchy='';
	$emp_hierarchy_condition='';
	$emp_hierarchy_condition_one='';
}
else
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition=' AND LO.emp_code IN('.$emp_hierarchy.')';
	$emp_hierarchy_condition_one=' AND EM.emp_code IN('.$emp_hierarchy.')';
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
      if(stk_audit=='yes' || retailer_care=='yes'){
			$stk_audit_customer_TR='<td width="12%" align="left" style="padding-left:20px;">No of Stock Audit</td>';
		}
		else
		{
			$stk_audit_customer_TR='';
			$stk_audit_customer_TD='';
		}
		if(strtoupper($_SESSION['nick_name'])=='ABDOST' || strtoupper($_SESSION['nick_name'])=='ABDOS')
		{
			$tonnage_column='<th width="12%" align="left" style="padding-left:20px;">Tonnage</th>';
		}
		else { $tonnage_column='';}		
		$tableval='
<table width="90%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 150px;overflow-y: scroll;display:block;">
    <tr class="TDHEAD" > 
        <td colspan="9" align="center"><strong>From: '.$from_date_pre.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;To: '. $to_date_pre.'</strong></td>
    </tr>
    <tr class="TDHEAD_SUB"> 
         <td width="5%" align="center">Sl</td>
		<td width="15%" align="left" style="padding-left:20px;">Emp code</td>
		<td width="20%" align="left" style="padding-left:20px;">Name</td>
		<td width="12%" align="left" style="padding-left:20px;">No. of Customer Visit</td>
		<td width="12%" align="left" style="padding-left:20px;">Total Order Received(Rs/-)</td>'.$tonnage_column.'
		<td width="12%" align="left" style="padding-left:20px;">Total Collection(Rs/-)</td>
		<td width="" align="left" style="padding-left:20px;">No Transaction</td>';
   
   		if(sale=='no' && instruction=='yes')
		{
			$date_condition=" AND DATE_FORMAT(LO.date,'%Y-%m-%d') BETWEEN '".$from_date."' AND '".$to_date."'";
		}
		else
		{
			$date_condition=" AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d') BETWEEN '".$from_date."' AND '".$to_date."'";
		}
		$tableval .= $stk_audit_customer_TR.'</tr>';
	
	$sqlinformation="SELECT EM.emp_name,EM.emp_code,LO.trans_id,count(LO.trans_id) AS no_of_visit FROM 
						location LO,employee_master EM WHERE LO.emp_code=EM.emp_code AND (SUBSTRING(LO.trans_id,1,1) IN
						('O','P','S') OR SUBSTRING(LO.trans_id,1,2) IN('NO','NC','CI')) AND SUBSTRING(LO.trans_id,1,2) NOT IN('PA') AND SUBSTRING(trans_id,1,2) NOT IN('SU') AND SUBSTRING(EM.emp_code,1,1)!='C' ".$emp_hierarchy_condition.$date_condition." GROUP BY EM.emp_code ORDER BY EM.emp_name ASC ";
		$resinformation=mysql_query($sqlinformation) or die(mysql_error()." Error in select transaction information: ".$sqlinformation);
        $cnt=$GLOBALS[start]+1;
        while($rowinformation=mysql_fetch_array($resinformation))
        {
            $trans_id=$rowinformation['trans_id'];
			$operation_type=substr($trans_id,0,1);
			$emp_name=$rowinformation['emp_name'];
			$emp_code=$rowinformation['emp_code'];
			$customer_code_array=array();
			$customer_code_notrans_array=array();
			
		$sql_order_header_customer = "SELECT SUBSTRING(order_no,-19,5) AS emp_code, customer_code, SUBSTRING(order_no,-14,8) AS oh_date 
		FROM order_header WHERE ( SUBSTRING(order_no,2,5)='".$emp_code."' OR  SUBSTRING(order_no,3,5)='".$emp_code."')
		AND DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y-%m-%d') BETWEEN '".$from_date."' AND '".$to_date."' AND customer_code IN(SELECT customer_code FROM customer_master)
		GROUP BY SUBSTRING(order_no,-19,5), customer_code, SUBSTRING(order_no,-14,8) 
		ORDER BY SUBSTRING(order_no,-14,8) DESC,SUBSTRING(customer_code,-14,8) DESC";
		$res_order_header_customer = mysql_query($sql_order_header_customer);
		//$yttdcount=1;
		while($row_order_header_customer = mysql_fetch_array($res_order_header_customer)){
			$emp_code = $row_order_header_customer['emp_code'];
			$order_header_customer_code = $row_order_header_customer['customer_code'];
			$location_date = $row_order_header_customer['oh_date'];
			$customer_concat_date = $emp_code."^".$order_header_customer_code."^".$location_date;

			$customer_code_array[$customer_concat_date] = '1';
		}
	$sql_payment_header_customer = "SELECT SUBSTRING(receipt_id,-19,5) AS emp_code, customer_code, SUBSTRING(receipt_id,-14,8) 
										AS ph_date FROM payment_header WHERE 
										( SUBSTRING(receipt_id,2,5)='".$emp_code."' OR  SUBSTRING(receipt_id,3,5)='".$emp_code."')
										AND DATE_FORMAT(SUBSTRING(receipt_id,-14,8),'%Y-%m-%d') BETWEEN '".$from_date."' AND '".$to_date."' AND customer_code IN(SELECT customer_code FROM customer_master)
										GROUP BY SUBSTRING(receipt_id,-19,5), customer_code, SUBSTRING(receipt_id,-14,8)";
		$res_payment_header_customer = mysql_query($sql_payment_header_customer);
		while($row_payment_header_customer = mysql_fetch_array($res_payment_header_customer)){
			$emp_code = $row_payment_header_customer['emp_code'];
			$payment_header_customer_code = $row_payment_header_customer['customer_code'];
			$location_date = $row_payment_header_customer['ph_date'];
			$customer_concat_date = $emp_code."^".$payment_header_customer_code."^".$location_date;
			$customer_code_array[$customer_concat_date] = '1';
		}
		if(check_in_out=='yes')
		{
			$sql_checkin_out_customer = "SELECT SUBSTRING(trans_id,-19,5) AS emp_code, customer_code, SUBSTRING(trans_id,-14,8) 
											AS ci_date FROM check_in_out_details WHERE SUBSTRING(trans_id,-19,5)='".$emp_code."'
											AND DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d') BETWEEN '".$from_date."' AND '".$to_date."'
											AND customer_code IN(SELECT customer_code FROM customer_master)
										GROUP BY SUBSTRING(trans_id,-19,5), customer_code, SUBSTRING(trans_id,-14,8)";
			$res_checkin_out_customer = mysql_query($sql_checkin_out_customer);
			while($row_checkin_out_customer = mysql_fetch_array($res_checkin_out_customer)){
				$emp_code = $row_checkin_out_customer['emp_code'];
				$check_in_out_customer_code = $row_checkin_out_customer['customer_code'];
				$location_date = $row_checkin_out_customer['ci_date'];
				$customer_concat_date = $emp_code."^".$check_in_out_customer_code."^".$location_date;
				$customer_code_array[$customer_concat_date] = '1';
			}
		}
			if(sale_rate=='yes')
			{
			$sqltotalorder="SELECT SUM(OD.qty) AS total_order_received,SUM(OD.amount) AS total_amount_received,SUM(OD.qty*OD.sale_rate) AS total_amount
							FROM order_details OD,location LO,order_header OH,customer_master CM
							WHERE LO.emp_code='".$emp_code."' AND LO.trans_id LIKE 'O%' 
							AND LO.trans_id=OD.order_no AND OH.order_no=OD.order_no AND OH.customer_code=CM.customer_code ".$date_condition."";
			$rstotalorder=mysql_query($sqltotalorder) or die(mysql_error()." Error in total order received: ".$sqltotalorder);
				$rowtotalorder=mysql_fetch_array($rstotalorder);
				$total_amount_received=$rowtotalorder['total_amount'];				
			}
			if(mrp=='yes')
			{
				if(strtoupper($_SESSION['nick_name'])=='ABDOS')
				{
					$sqltotalorder="SELECT OD.qty,OD.weightage,OD.amount,OD.UOM,PM.conversion_factor,OD.sku_code
							FROM order_details OD,location LO,order_header OH,customer_master CM,product_master PM
							WHERE LO.emp_code='".$emp_code."' AND LO.trans_id LIKE 'O%' 
							AND LO.trans_id=OD.order_no AND OH.order_no=OD.order_no AND OH.customer_code=CM.customer_code 
							AND PM.prod_code=OD.sku_code ".$date_condition."";
					$rstotalorder=mysql_query($sqltotalorder) or die(mysql_error()." Error in select order: ".$sqltotalorder);
					$total_order_received=0;
					$total_amount_received=0;
					$weightage_val=0;
					while($rowtotalorder=mysql_fetch_array($rstotalorder))
					{
						//echo $emp_code.'----------------------';
						$UOM_order=$rowtotalorder['UOM'];
						$prod_code=$rowtotalorder['sku_code'];
						if(strtoupper($UOM_order)!='CASE' || $UOM_order=='')
						{
							$conversion_factor=$rowtotalorder['conversion_factor'];
							$qtyconverted=$rowtotalorder['qty']/$conversion_factor;
							
							$sqlselmrp="SELECT mrp FROM mrp WHERE product_code='".$prod_code."' AND UPPER(UOM)='CASE'";
							$rsselmrp=mysql_query($sqlselmrp);
							$rowselmrp=mysql_fetch_array($rsselmrp);
							$mrp=$rowselmrp['mrp'];
							$amount=$qtyconverted*$mrp;
							$qty=$qtyconverted;
							$total_order_received=$total_order_received+$qty;
							$total_amount_received=$total_amount_received+$amount;
						}
						else
						{
							$amount=$rowtotalorder['amount'];
							$qty=$rowtotalorder['qty'];
							$total_order_received=$total_order_received+$qty;
							$total_amount_received=$total_amount_received+$amount;
						}
						//echo '---------------------<br />';
						$weightage_val=$weightage_val+$rowtotalorder['weightage'];
						}
						$weightage_val=round(($weightage_val/1000),3);
						$tonnage_row='<td align="right" valign="top" style="padding-left:20px;BORDER: #A92A61 1px solid;">'.$weightage_val.'</td>';
					}
					else
					{
				$sqltotalorder="SELECT ROUND(SUM(OD.amount),2) AS total_order_received,SUM(OD.qty*MRP.mrp) AS total_amount FROM order_details OD, 
							location LO,order_header OH,customer_master CM,mrp MRP WHERE OD.order_no = LO.trans_id AND LO.trans_id LIKE 'O%' 
							AND LO.emp_code = '".$emp_code."' AND OD.sku_code=MRP.product_code AND OH.order_no=OD.order_no AND 
							OH.customer_code=CM.customer_code ".$date_condition;
					$rstotalorder=mysql_query($sqltotalorder) or die(mysql_error()." Error in total order received: ".$sqltotalorder);
				$rowtotalorder=mysql_fetch_array($rstotalorder);
				$total_amount_received=$rowtotalorder['total_amount'];
					}
			}
		
			$sqltotalcollection="SELECT SUM(PD.amount) AS total_collection_received
								FROM payment_details PD,location LO
								WHERE  LO.emp_code='".$emp_code."' 
								AND LO.trans_id LIKE 'P%' AND LO.trans_id=PD.receipt_id ".$date_condition."";
			$rstotalcollection=mysql_query($sqltotalcollection) or die(mysql_error()." Error in total collection received: ".$sqltotalcollection);
			$rowtotalcollection=mysql_fetch_array($rstotalcollection);
			if(strtoupper($_SESSION['nick_name'])=='ABDOS')
			{
				$sqlnotransaction="SELECT LO.trans_id,SUBSTRING(OH.order_no,-14,8) AS no_trans_date,OH.customer_code,LO.emp_code
									FROM location LO,order_header OH,customer_master CM WHERE  LO.emp_code='".$emp_code."' 
									AND (LO.trans_id LIKE 'NO%' OR LO.trans_id LIKE 'NC%') AND OH.order_no=LO.trans_id AND OH.customer_code=CM.customer_code AND DATE_FORMAT(LO.date,'%Y-%m-%d') BETWEEN '".$from_date."' AND '".$to_date."'";
				$rsnotransaction=mysql_query($sqlnotransaction) or die(mysql_error()." Error in total no transaction: ".$sqlnotransaction);
				while($rownotransaction = mysql_fetch_array($rsnotransaction)){
					$emp_code = $rownotransaction['emp_code'];
					$no_trans_customer_code = $rownotransaction['customer_code'];
					$no_trans_date = $rownotransaction['no_trans_date'];
					$customer_concat_notrans_date = $emp_code."^".$no_trans_customer_code."^".$no_trans_date;
		
					$customer_code_notrans_array[$customer_concat_notrans_date] = '1';
				}
				$no_transaction_count=count($customer_code_notrans_array);
			}
			else
			{
				$sqlnotransaction="SELECT COUNT(LO.trans_id)AS total_no_transaction
								FROM location LO,order_header OH,customer_master CM WHERE  LO.emp_code='".$emp_code."' 
								AND (LO.trans_id LIKE 'NO%' OR LO.trans_id LIKE 'NC%') AND OH.order_no=LO.trans_id AND OH.customer_code=CM.customer_code AND DATE_FORMAT(LO.date,'%Y-%m-%d') BETWEEN '".$from_date."' AND '".$to_date."'";
				$rsnotransaction=mysql_query($sqlnotransaction) or die(mysql_error()." Error in total no transaction: ".$sqlnotransaction);
				$rownotransaction=mysql_fetch_array($rsnotransaction);
				$no_transaction_count=$rownotransaction['total_no_transaction'];
			}
			$sqlstockaudit="SELECT COUNT(LO.trans_id)AS total_stk_audit
								FROM location LO WHERE  LO.emp_code='".$emp_code."' 
								AND (LO.trans_id LIKE 'SE%')  AND DATE_FORMAT(LO.date,'%Y-%m-%d') BETWEEN '".$from_date."' AND '".$to_date."'";
			$rsstockaudit=mysql_query($sqlstockaudit) or die(mysql_error()." Error in total no transaction: ".$sqlstockaudit);
			$rowstockaudit=mysql_fetch_array($rsstockaudit);
			if(stk_audit=='yes' || retailer_care=='yes'){
			$stk_audit_customer_TD="<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
			".$rowstockaudit['total_stk_audit']."</td>";
			}
			$rowval.="<tr> 
                    <td align=\"center\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$cnt++."</td>
                    <td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\"><a href=\"javascript:void(0)\" 
					onClick=\"javascript:showEmployeeWisedisplay('".$emp_code."','YC','activity')\"  style=\"color:#930;font-weight:bold;\">".$emp_code."</a></td>
                    <td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$emp_name."</td>
					<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".count($customer_code_array)."</td>
					<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".number_format(round($total_amount_received,2),2)."</td>".$tonnage_row."
					<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".number_format($rowtotalcollection['total_collection_received'],2)."</td>
					<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$no_transaction_count."</td>".$stk_audit_customer_TD."
              </tr>";
        }
$tablevalend='</table>';				
$finalval=$tableval.$rowval.$tablevalend;

echo $finalval;
mysql_close($link);
?>