<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

$month=$_REQUEST['month'];
$month_year = explode("-",$month);
$monthvalue = date('m',strtotime($month_year[0]));
$year = $month_year[1];
$current_month_first_date=$year.'-'.$monthvalue.'-01';
$previous_month_first_date=date('Y-m-d',strtotime($current_month_first_date.' -1 MONTH'));
$previous_month=substr($previous_month_first_date,5,2);
$previous_year=substr($previous_month_first_date,0,4);

$dealer = $_REQUEST['dealer'];
$emp_code = $_REQUEST['emp_code'];
if($state != ''){
	if($state == 'all')
		$state_condition = " state!=''";
	else
		if(strtoupper($_SESSION['nick_name']) == 'HALDIRAM'){
			$state_condition = " FIND_IN_SET(".$state.", state)";
		}
		else
		{
			$state_condition = " state IN(".$state.") ";
		}
}
$cust_UOM_val_array=explode(",",cust_type_wise_UOM_val);
foreach($cust_UOM_val_array as $cust_UOM_val)
{
	$cust_UOM_val_split=explode("#",$cust_UOM_val);
	${cust_type_UOM_unit.$cust_UOM_val_split[0]}=$cust_UOM_val_split[1];
}
$sqldistributor="SELECT customer_name,cust_type FROM customer_master WHERE customer_code=".$dealer."";
$rsdistributor=mysqli_query($link,$sqldistributor);
$rowdistributor=mysqli_fetch_assoc($rsdistributor);
$customer_name=$rowdistributor['customer_name'];
$cust_type_db=$rowdistributor['cust_type'];

$sqllaststkaudit="SELECT DATE_FORMAT(SUBSTRING(transaction_id,-14,14),'%Y-%m-%d %H:%i:%s') AS last_stock_audit_date 
				 FROM stock_audit WHERE customer_code=".$dealer." AND 
				 SUBSTRING(transaction_id,-14,4)='".$year."' AND SUBSTRING(transaction_id,-10,2)='".$monthvalue."' 
				ORDER BY DATE_FORMAT(SUBSTRING(transaction_id,-14,14),'%Y-%m-%d %H:%i:%s') DESC LIMIT 0,1";
$rslaststkaudit=mysqli_query($link,$sqllaststkaudit);	
$rowlaststkaudit=mysqli_fetch_assoc($rslaststkaudit);
$last_stock_audit_date=$rowlaststkaudit['last_stock_audit_date'];
if($last_stock_audit_date!='')
{
	$last_stock_audit_date_heading=date('d-m-Y',strtotime($last_stock_audit_date));
}
else
{
	$last_stock_audit_date_heading=date('d-m-Y',strtotime($current_month_first_date));
}
$sqllaststkauditprevmonth="SELECT DATE_FORMAT(SUBSTRING(transaction_id,-14,14),'%Y-%m-%d %H:%i:%s') AS last_stock_audit_date_prev_month 
						FROM stock_audit WHERE customer_code=".$dealer." ' AND 
						SUBSTRING(transaction_id,-14,4)='".$previous_year."' AND SUBSTRING(transaction_id,-10,2)='".$previous_month."' 
						ORDER BY DATE_FORMAT(SUBSTRING(transaction_id,-14,14),'%Y-%m-%d %H:%i:%s') DESC LIMIT 0,1";
$rslaststkauditprevmonth=mysqli_query($link,$sqllaststkauditprevmonth);	
$rowlaststkauditprevmonth=mysqli_fetch_assoc($rslaststkauditprevmonth);
$last_stock_audit_date_prev_month=$rowlaststkauditprevmonth['last_stock_audit_date_prev_month'];
		
$sql_product_details = "SELECT  prod_code,prod_desc,UOM1,UOM2,UOM3,conversion_factor,conversion_factor_two FROM product_master WHERE acedns='Y' ORDER BY prod_desc ASC";				
$res_product_details = mysqli_query($link,$sql_product_details);
$cnt_product_details=mysqli_num_rows($res_product_details);
if($cnt_product_details >0)
{
	$count = 1;
	$prod_code_array=array();
	$prod_desc_array=array();
	$conversion_factor_two_array=array();
	$UOM_array=array();
	$stk_audit_1st_array=array();
	$stk_audit_last_array=array();
	echo "<div class='tbl-header'><table width='90%' border='0' style='border-collapse:collapse;' class='border datatable' cellpadding='4'><thead>";
	echo "<tr class='TDHEAD'><td colspan='10' align='center'>Stock Analysis of $customer_name of $month</td></tr>";
	echo "<tr class='TDHEAD_SUB' align=\"center\">
			<td width='7%'>SL NO</td>
			<td width='20%'>SKU</td>
			<td width='10%'>Opening Stock</td>
			<td width='9%'>Sum of purchase before $last_stock_audit_date_heading</td>
			<td width='9%'>Last stock audit as on $last_stock_audit_date_heading</td>
			<td width='9%'>Sum of purchase after $last_stock_audit_date_heading</td>
			<td width='9%'>Sale</td>
			<td width='9%'>Stock</td>
			<td width='9%'>UOM</td>
			<td width='9%'>Secondary sales of SR</td>
		  </tr></thead></table></div><div class='tbl-content'><table class='BORDER datatable' cellpadding='0' cellspacing='0' border='1'>
      <tbody>";
	while($row_product_details=mysqli_fetch_assoc($res_product_details)){
		$prod_code=$row_product_details['prod_code'];
		$prod_desc=$row_product_details['prod_desc'];
		$UOM1=$row_product_details['UOM1'];
		$UOM2=$row_product_details['UOM2'];
		$UOM3=$row_product_details['UOM3'];
		$prod_desc=$row_product_details['prod_desc'];
		$conversion_factor=$row_product_details['conversion_factor'];
		$conversion_factor_two=$row_product_details['conversion_factor_two'];
		
		$sql1ststkaudit="SELECT quantity FROM stock_audit WHERE customer_code=".$dealer." AND product_code='".$prod_code."' AND 
						SUBSTRING(transaction_id,-14,4)='".$year."' AND SUBSTRING(transaction_id,-10,2)='".$monthvalue."' 
						ORDER BY DATE_FORMAT(SUBSTRING(transaction_id,-14,14),'%Y-%m-%d %H:%i:%s') ASC LIMIT 0,1";
		$rs1ststkaudit=mysqli_query($link,$sql1ststkaudit);	
		$row1ststkaudit=mysqli_fetch_assoc($rs1ststkaudit);
		$stkauditqty1st=$row1ststkaudit['quantity'];
		$sqllaststkaudit="SELECT quantity
						FROM stock_audit WHERE customer_code=".$dealer." AND product_code='".$prod_code."' AND 
						SUBSTRING(transaction_id,-14,4)='".$year."' AND SUBSTRING(transaction_id,-10,2)='".$monthvalue."' 
						ORDER BY DATE_FORMAT(SUBSTRING(transaction_id,-14,14),'%Y-%m-%d %H:%i:%s') DESC LIMIT 0,1";
		$rslaststkaudit=mysqli_query($link,$sqllaststkaudit);	
		$rowlaststkaudit=mysqli_fetch_assoc($rslaststkaudit);
		$stkauditqtylast=$rowlaststkaudit['quantity'];
		
		if(!in_array($prod_code,$prod_code_array))
		{
			array_push($prod_code_array,$prod_code);
			array_push($prod_desc_array,$prod_desc);
			array_push($conversion_factor_two_array,$conversion_factor_two);
			array_push($stk_audit_1st_array,$stkauditqty1st);
			array_push($stk_audit_last_array,$stkauditqtylast);
			array_push($UOM_array,$${cust_type_UOM_unit.$cust_type_db});
			${puchaseqtytilllatstkadt.$prod_code}=0;
			${puchaseqtyafterlatstkadt.$prod_code}=0;	
		}
		$sql_purchase = "SELECT PD.invoice_no,PD.invoice_date,PD.qty,PD.rate,PD.amount FROM purchase_details PD WHERE 
					PD.distributor_code=".$dealer." AND SUBSTRING(PD.invoice_date,-10,4)='".$year."' 
					AND SUBSTRING(PD.invoice_date,-5,2)='".$monthvalue."' AND PD.prod_code='".$prod_code."' ORDER BY PD.invoice_date ASC";
		$rs_purchase=mysqli_query($link,$sql_purchase);
		while($row_purchase=mysqli_fetch_assoc($rs_purchase))
		{			
			$invoice_no=$row_purchase['invoice_no'];
			$invoice_date=$row_purchase['invoice_date'];
			$qty=$row_purchase['qty'];
			$rate=$row_purchase['rate'];
			$amount=$row_purchase['amount'];
		
			if($invoice_date < substr($last_stock_audit_date,0,10))
			{
				
				${puchaseqtytilllatstkadt.$prod_code}=${puchaseqtytilllatstkadt.$prod_code}+$qty;
			}
			else
			{
				${puchaseqtyafterlatstkadt.$prod_code}=${puchaseqtyafterlatstkadt.$prod_code}+$qty;
			}
		}
	 }
	 for($i=0;$i<count($prod_code_array);$i++)
	 {
	   $sqllaststkauditprevmonth="SELECT quantity
						FROM stock_audit WHERE customer_code=".$dealer." AND product_code='".$prod_code_array[$i]."' AND 
						SUBSTRING(transaction_id,-14,4)='".$previous_year."' AND SUBSTRING(transaction_id,-10,2)='".$previous_month."' 
						ORDER BY DATE_FORMAT(SUBSTRING(transaction_id,-14,14),'%Y-%m-%d %H:%i:%s') DESC LIMIT 0,1";
		$rslaststkauditprevmonth=mysqli_query($link,$sqllaststkauditprevmonth);	
		$rowlaststkauditprevmonth=mysqli_fetch_assoc($rslaststkauditprevmonth);
		$stkauditqtylastprevmonth=$rowlaststkauditprevmonth['quantity'];
		//$last_stock_audit_date_prev_month=$rowlaststkauditprevmonth['last_stock_audit_date_prev_month'];
		
		$sql_last_month_purchase = "SELECT PD.qty,PD.invoice_date  FROM purchase_details PD
				WHERE PD.distributor_code=".$dealer." AND SUBSTRING(PD.invoice_date,-10,4)='".$previous_year."' 
				AND SUBSTRING(PD.invoice_date,-5,2)='".$previous_month."' AND PD.prod_code='".$prod_code_array[$i]."' GROUP BY PD.prod_code";
		$rs_last_month_purchase=mysqli_query($link,$sql_last_month_purchase);	
		$row_last_month_purchase=mysqli_fetch_assoc($rs_last_month_purchase);
		$prev_month_invoice_date=$row_last_month_purchase['invoice_date'];
		if($prev_month_invoice_date<=$last_stock_audit_date_prev_month)
		{
			${puchaseqtytilllatstkadtprev.$prod_code_array[$i]}=${puchaseqtytilllatstkadtprev.$prod_code_array[$i]}+$row_last_month_purchase['qty'];
		}
		else
		{
			${puchaseqtyafterlatstkadtprev.$prod_code_array[$i]}=${puchaseqtyafterlatstkadtprev.$prod_code_array[$i]}+$row_last_month_purchase['qty'];
		}

		//$prev_month_purchase=$row_last_month_purchase['total_prev_month_purchase'];
		$opening_stock=${puchaseqtyafterlatstkadtprev.$prod_code_array[$i]}+$stkauditqtylastprevmonth;
		
		$sqltotalsecondarysales="SELECT SUM(visit_qty) AS total_secondary_sales FROM prev_order_counting_master POCM,customer_master CM WHERE POCM.customer_code=CM.customer_code AND CM.cust_type='R' AND SUBSTRING(POCM.visit_date,1,4)='".$year."' AND 
		SUBSTRING(POCM.visit_date,6,2)='".$monthvalue."' AND POCM.product_code='".$prod_code_array[$i]."' AND CM.customer_code IN(SELECT customer_code FROM customer_master WHERE acedns='Y' AND rds_tag=".$dealer." AND cust_type='R')";
		$rstotalsecondarysales=mysqli_query($link,$sqltotalsecondarysales);	
		$rowtotalsecondarysales=mysqli_fetch_assoc($rstotalsecondarysales);
		$totalsecondarysales=$rowtotalsecondarysales['total_secondary_sales'];
		
		$sale=($opening_stock+${puchaseqtytilllatstkadt.$prod_code_array[$i]})-$stk_audit_last_array[$i];
		//$stock=$opening_stock+${puchaseqtytilllatstkadt.$prod_code_array[$i]}+${puchaseqtyafterlatstkadt.$prod_code_array[$i]}-$sale;
		$stock=$opening_stock+${puchaseqtytilllatstkadt.$prod_code_array[$i]}+${puchaseqtyafterlatstkadt.$prod_code_array[$i]}-$sale;
		if($sale < 0)
		{
			$sale=0;
		}
		if($stock < 0)
		{
			$stock=0;
		}
		
		if($opening_stock ==0 || $opening_stock =='')	$opening_stock='-';
		else					  						 $opening_stock=number_format($opening_stock,0);
		if(${puchaseqtytilllatstkadt.$prod_code_array[$i]} ==0)	${puchaseqtytilllatstkadt.$prod_code_array[$i]}='-';
		else					  ${puchaseqtytilllatstkadt.$prod_code_array[$i]}=number_format(${puchaseqtytilllatstkadt.$prod_code_array[$i]},0);
		if($stk_audit_last_array[$i] ==0)	$stk_audit_last_array[$i]='-';
		else					  $stk_audit_last_array[$i]=number_format($stk_audit_last_array[$i],0);
		if(${puchaseqtyafterlatstkadt.$prod_code_array[$i]} ==0)	${puchaseqtyafterlatstkadt.$prod_code_array[$i]}='-';
		else					  ${puchaseqtyafterlatstkadt.$prod_code_array[$i]}=number_format(${puchaseqtyafterlatstkadt.$prod_code_array[$i]},0);
		if($sale ==0)			 $sale='-';
		else					  $sale=number_format($sale,0);
		if($stock ==0)			$stock='-';
		else					  $stock=number_format($stock,0);
		if($totalsecondarysales ==0)	$totalsecondarysales='-';
		else					  $totalsecondarysales=number_format($totalsecondarysales,0);
		
		if($opening_stock!='-' || ${puchaseqtytilllatstkadt.$prod_code_array[$i]}!='-' || $stk_audit_last_array[$i]!='-' || 
		${puchaseqtyafterlatstkadt.$prod_code_array[$i]}!='-' || $sale!='-' || $stock!='-' || $totalsecondarysales!='-')
		{
		echo "<tr>
				<td >".$count."</td>
				<td >".$prod_desc_array[$i]."</td>
				<td  style=\"text-align:right\">".$opening_stock."</td>
				<td  style=\"text-align:right\">".${puchaseqtytilllatstkadt.$prod_code_array[$i]}."</td>
				<td style=\"text-align:right\" >".$stk_audit_last_array[$i]."</td>
				<td style=\"text-align:right\" >".${puchaseqtyafterlatstkadt.$prod_code_array[$i]}."</td>
				<td style=\"text-align:right\">".$sale."</td>
				<td style=\"text-align:right\" >".$stock."</td>
				<td style=\"text-align:right\" >".$UOM_array[$i]."</td>
				<td style=\"text-align:right\">".round(($totalsecondarysales/$conversion_factor_two_array[$i]),2)."</td>
			  </tr>";
		$count++;
		}
	 }
	echo "</tbody></table> 
</div><br />";
	?>
    <!--div style="width:70%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
      <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
	</div-->
    <?php
}
if($count==1)
{
	echo "<tr valign=\"top\"><td><font color='red'><strong>No records found</strong></font></td></tr></table></div>";
}
mysqli_close($link);
?>