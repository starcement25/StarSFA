<?php
ob_start();
session_start();
require("adminUtils.php");

$employee = $_REQUEST['employee'];
$val = $_REQUEST['val'];

$employee_arg = str_replace("^","'",$employee);
$employee_arg = str_replace("#",",",$employee_arg);

if($val == 'T'){
	$today = date('Y-m-d');
	$date_condition = " SUBSTRING(LO.date,1,10) = '".$today."' ";
	$survey_output_date_condition = " AND SUBSTRING(survey_id,-14,8) = '".str_replace("-","",$today)."' ";
	$market_feedback_condition = " AND SUBSTRING(market_feedback_id,-14,8) = '".str_replace("-","",$today)."' ";
	
}
else if($val == 'MTD'){
	$current_month = date('Y-m');
	$date_condition = " SUBSTRING(LO.date,1,7) = '".$current_month."' ";
	$survey_output_date_condition = " AND SUBSTRING(survey_id,-14,6) = '".str_replace("-","",$current_month)."' ";
	$market_feedback_condition = " AND SUBSTRING(market_feedback_id,-14,6) = '".$current_month."' ";
}
else if($val == 'YTD'){
	$today = date('Y-m-d');
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	
	if($month>='04')
		$fiinancial_year=$year.'-04-01';
	else
		$fiinancial_year=($year-1).'-04-01';
	
	$date_condition = " (SUBSTRING(LO.date,1,10) BETWEEN '".$fiinancial_year."' AND '".$today."') ";
	
	$survey_output_date_condition = "  AND (SUBSTRING(survey_id,-14,8) BETWEEN '".str_replace("-","",$fiinancial_year)."' AND '".str_replace("-","",$today)."') ";
	$market_feedback_condition = "  AND (SUBSTRING(market_feedback_id,-14,8) BETWEEN '".str_replace("-","",$fiinancial_year)."' AND '".str_replace("-","",$today)."') ";
}
?>
<table class="border" style="border-collapse:collapse; width:100%;" border="1">
  <tr class="TDHEAD">
  	<td>SI</td>
    <td>Emp Code</td>
    <td>Emp Name</td>
    <td>Customer Visited</td>
    <td>Order Received</td>
    <td>No Transaction</td>
    <td>Stock Audit(Qty)</td>
    <td>KYC</td>
    <td>Brand Activity</td>
    <td>Technical Meet</td>
    <td>Site Visit</td>
    <td>Market Feedback</td>
    <td>Total Activity</td>
  </tr>
<?php
$count = 1;
$date_array = array();
$sql_get_date = "SELECT DISTINCT SUBSTRING(LO.date,1,10) AS date_select FROM location LO WHERE ".$date_condition." ORDER BY date_select ASC";
$res_get_date = mysql_query($sql_get_date);
while($row_get_date = mysql_fetch_array($res_get_date)){
	$date_select = $row_get_date['date_select'];
	
	$sql_select_emp_details = "SELECT emp_code, dns_emp_code, emp_name FROM employee_master WHERE emp_code IN (".$employee_arg.") ORDER BY emp_name";
	$res_select_emp_details = mysql_query($sql_select_emp_details);
	while($row_select_emp_details = mysql_fetch_array($res_select_emp_details)){
		$emp_code = $row_select_emp_details['emp_code'];
		$dns_emp_code = $row_select_emp_details['dns_emp_code'];
		$emp_name = $row_select_emp_details['emp_name'];
		
		/*----------> Total Customer Visit <----------*/
		/*$sqlcustomervisit="SELECT COUNT(LO.trans_id) AS no_visit  FROM location LO WHERE (SUBSTRING(LO.trans_id,1,1) IN
	('O','P','S') OR SUBSTRING(LO.trans_id,1,2) IN('NO','NC')) AND SUBSTRING(LO.trans_id,1,2) NOT IN('PA')  
	AND SUBSTRING(LO.trans_id,1,2) NOT IN('SU') AND SUBSTRING(LO.emp_code,1,1)!='C' AND LO.emp_code = '".$emp_code."' AND SUBSTRING(LO.date,1,10) = '".$date_select."'";
		$rescustomervisit=mysql_query($sqlcustomervisit) or die(mysql_error()." Error in select customer visit: ".$sqlcustomervisit);
		$rowcustomervisit=mysql_fetch_array($rescustomervisit);
		$no_customer_visit=$rowcustomervisit['no_visit'];
		if($no_customer_visit == 0)
			$show_no_customer_visit = "-";
		else
			$show_no_customer_visit = $no_customer_visit;*/
		$customer_code_array = array();
		$sql_order_header_customer = "SELECT OH.customer_code FROM order_header OH, location LO WHERE SUBSTRING(OH.order_no,-19,5) = '".$emp_code."' AND OH.order_no = LO.trans_id AND SUBSTRING(LO.date,1,10) = '".$date_select."'";
		$res_order_header_customer = mysql_query($sql_order_header_customer);
		while($row_order_header_customer = mysql_fetch_array($res_order_header_customer)){
			$order_header_customer_code = $row_order_header_customer['customer_code'];
			if(!in_array($order_header_customer_code,$customer_code_array))
				array_push($customer_code_array,$order_header_customer_code);
		}
		
		$sql_collection_customer = "SELECT PH.customer_code FROM payment_header PH, location LO WHERE SUBSTRING(PH.receipt_id,-19,5) = '".$emp_code."' AND PH.receipt_id = LO.trans_id AND SUBSTRING(LO.date,1,10) = '".$date_select."'";
		$res_collection_customer = mysql_query($sql_collection_customer);
		while($row_collection_customer = mysql_fetch_array($res_collection_customer)){
			$collection_customer_code = $row_collection_customer['customer_code'];
			if(!in_array($collection_customer_code,$customer_code_array))
				array_push($customer_code_array,$collection_customer_code);
		}
		
		$sql_stock_audit_customer = "SELECT SA.customer_code FROM stock_audit SA, location LO WHERE SUBSTRING(SA.transaction_id,-19,5) = '".$emp_code."' AND SA.transaction_id = LO.trans_id AND SUBSTRING(LO.date,1,10) = '".$date_select."'";
		$res_stock_audit_customer = mysql_query($sql_stock_audit_customer);
		while($row_stock_audit_customer = mysql_fetch_array($res_stock_audit_customer)){
			$stock_audit_customer = $row_stock_audit_customer['customer_code'];
			if(!in_array($stock_audit_customer,$customer_code_array))
				array_push($customer_code_array,$stock_audit_customer);
		}
		
		
		$sql_market_feedback = "SELECT customer_code FROM market_feedback WHERE SUBSTRING(market_feedback_id,3,5) = '".$emp_code."' AND SUBSTRING(market_feedback_id,-14,8) = '".str_replace("-","",$date_select)."'";
		$res_market_feedback = mysql_query($sql_market_feedback);
		while($row_market_feedback = mysql_fetch_array($res_market_feedback)){
			$market_feedback_customer = $row_market_feedback['customer_code'];
			if(!in_array($market_feedback_customer,$customer_code_array))
				array_push($customer_code_array,$market_feedback_customer);
		}
				
		
		$no_customer_visit = count($customer_code_array);
		if($no_customer_visit == 0)
			$show_no_customer_visit = "-";
		else
			$show_no_customer_visit = $no_customer_visit;
		
		
		
		/*----------> Total Order <----------*/
		$sqltotalorder="SELECT SUM(OD.qty) AS total_order_received
						FROM order_details OD,location LO
						WHERE  LO.trans_id LIKE 'O%' AND LO.trans_id=OD.order_no AND SUBSTRING(LO.emp_code,1,1)!='C' AND LO.emp_code = '".$emp_code."' AND SUBSTRING(LO.date,1,10) = '".$date_select."'";
		$rstotalorder=mysql_query($sqltotalorder) or die(mysql_error()." Error in total order received: ".$sqltotalorder);
		$rowtotalorder=mysql_fetch_array($rstotalorder);
		$total_order_qty=$rowtotalorder['total_order_received'];
		if($total_order_qty == 0)
			$show_total_order_qty = "-";
		else
			$show_total_order_qty = $total_order_qty;
		
		
		
		/*----------> Total No Transaction <----------*/
		$sqlnotransaction="SELECT COUNT(LO.trans_id)AS total_no_transaction
							FROM location LO WHERE (LO.trans_id LIKE 'NO%' OR LO.trans_id LIKE 'NC%') 
							AND SUBSTRING(LO.emp_code,1,1)!='C' AND LO.emp_code = '".$emp_code."' AND SUBSTRING(LO.date,1,10) = '".$date_select."'";
		$rsnotransaction=mysql_query($sqlnotransaction) or die(mysql_error()." Error in total no transaction: ".$sqlnotransaction);
		$rownotransaction=mysql_fetch_array($rsnotransaction);
		$no_transaction=$rownotransaction['total_no_transaction'];
		if($no_transaction == 0)
			$show_no_transaction = "-";
		else
			$show_no_transaction = $no_transaction;
		
		
		
		
		/*----------> Total Stock Audit <----------*/
		$sqlnostkaudit="SELECT SUM(SA.quantity)AS total_stk_audit FROM location LO,stock_audit SA 
								WHERE SA.transaction_id=LO.trans_id AND (LO.trans_id LIKE 'S%') 
								AND SUBSTRING(LO.emp_code,1,1)!='C' AND LO.emp_code = '".$emp_code."' AND SUBSTRING(LO.date,1,10) = '".$date_select."'";
		$rsnostkaudit=mysql_query($sqlnostkaudit) or die(mysql_error()." Error in total no stk audit: ".$sqlnostkaudit);
		$rownostkaudit=mysql_fetch_array($rsnostkaudit);
		$no_stk_audit=$rownostkaudit['total_stk_audit'];
		if($no_stk_audit == 0)
			$show_no_stk_audit = "-";
		else
			$show_no_stk_audit = $no_stk_audit;
		
		
		
		/*----------> Total KYC <----------*/
		$sql_KYC_count = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type = 'KYC' AND SUBSTRING(survey_id,3,5) = '".$emp_code."' AND SUBSTRING(survey_id,-14,8) = '".str_replace("-","",$date_select)."'";
		$res_KYC_count = mysql_query($sql_KYC_count);
		$row_KYC_count = mysql_fetch_array($res_KYC_count);
		$KYC_count = $row_KYC_count['COUNT(DISTINCT survey_id)'];
		if($KYC_count == 0)
			$show_KYC_count = "-";
		else
			$show_KYC_count = $KYC_count;
		
		
		/*----------> Total Branding <----------*/
		$sql_brand_count = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type = 'Branding' AND SUBSTRING(survey_id,3,5) = '".$emp_code."' AND SUBSTRING(survey_id,-14,8) = '".str_replace("-","",$date_select)."'";
		$res_brand_count = mysql_query($sql_brand_count);
		$row_brand_count = mysql_fetch_array($res_brand_count);
		$brand_count = $row_brand_count['COUNT(DISTINCT survey_id)'];
		if($brand_count == 0)
			$show_brand_count = "-";
		else
			$show_brand_count = $brand_count;
		
		
		/*----------> Total Technical Meets <----------*/
		$sql_technical_count = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type = 'Technical Meets' AND SUBSTRING(survey_id,3,5) = '".$emp_code."' AND SUBSTRING(survey_id,-14,8) = '".str_replace("-","",$date_select)."'";
		$res_technical_count = mysql_query($sql_technical_count);
		$row_technical_count = mysql_fetch_array($res_technical_count);
		$technical_count = $row_technical_count['COUNT(DISTINCT survey_id)'];
		if($technical_count == 0)
			$show_technical_count = "-";
		else
			$show_technical_count = $technical_count;
		
		
		/*----------> Total Site Visit <----------*/
		$sql_site_visit = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE type = 'Site Visit' AND SUBSTRING(survey_id,3,5) = '".$emp_code."' AND SUBSTRING(survey_id,-14,8) = '".str_replace("-","",$date_select)."'";
		$res_site_visit = mysql_query($sql_site_visit);
		$row_site_visit = mysql_fetch_array($res_site_visit);
		$site_visit_count = $row_site_visit['COUNT(DISTINCT survey_id)'];
		if($site_visit_count == 0)
			$show_site_visit_count = "-";
		else
			$show_site_visit_count = $site_visit_count;
		
		
		/*----------> Total Market Feedback <----------*/
		$sql_market_feedback = "SELECT COUNT(DISTINCT market_feedback_id) FROM market_feedback WHERE SUBSTRING(market_feedback_id,3,5) = '".$emp_code."' AND SUBSTRING(market_feedback_id,-14,8) = '".str_replace("-","",$date_select)."'";
		$res_market_feedback = mysql_query($sql_market_feedback);
		$row_market_feedback = mysql_fetch_array($res_market_feedback);
		$market_feedback_count = $row_market_feedback['COUNT(DISTINCT market_feedback_id)'];
		if($market_feedback_count == 0)
			$show_market_feedback_count = "-";
		else
			$show_market_feedback_count = $market_feedback_count;
		
		$total_activity = ($no_customer_visit + $KYC_count + $brand_count + $technical_count + $site_visit_count + $market_feedback_count);
		
		if($total_activity == 0)
			$show_total_activity = "-";
		else
			$show_total_activity = $total_activity;
		
		if($no_customer_visit != 0 || $total_order_qty != 0 || $no_transaction != 0 || $KYC_count != 0 || $brand_count != 0 || $technical_count != 0 || $site_visit_count != 0 || $market_feedback_count != 0){
		if(!in_array($date_select,$date_array)){
			array_push($date_array,$date_select);
			echo "<tr><td class=\"TDHEAD_SUB\" align=\"center\" colspan=\"13\">".date('d-m-Y',strtotime($date_select))."</td></tr>";
		}
		
		echo "<tr>
				<td>".$count."</td>
				<td>".$emp_code."</td>
				<td>".$emp_name."</td>
				<td align=\"right\">".$show_no_customer_visit."</td>
				<td align=\"right\">".$show_total_order_qty."</td>
				<td align=\"right\">".$show_no_transaction."</td>
				<td align=\"right\">".$show_no_stk_audit."</td>
				<td align=\"right\">".$show_KYC_count."</td>
				<td align=\"right\">".$show_brand_count."</td>
				<td align=\"right\">".$show_technical_count."</td>
				<td align=\"right\">".$show_site_visit_count."</td>
				<td align=\"right\">".$show_market_feedback_count."</td>
				<td align=\"right\">".$show_total_activity."</td>
			 </tr>";
		$count++;
		}
		
	}
}
?>
</table>
<br />
<div style="width:100%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>