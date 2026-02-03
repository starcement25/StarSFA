<?php
ob_start();
session_start();
require("adminUtils.php");

main();

function main(){
	
	$mode = $_REQUEST['mode'];
	$start_date = $_REQUEST['start_date'];
	$end_date = $_REQUEST['end_date'];
	$employee = $_REQUEST['employee'];
	$val = $_REQUEST['val'];

	$employee_arg = str_replace("^","'",$employee);
	$employee_arg = str_replace("#",",",$employee_arg);
	?>
    <table class="border" style="border-collapse:collapse; width:100%;" border="1">
      <tr class="TDHEAD">
        <td>SI</td>
        <?php
		if($mode == 'datewise'){
			?>
        <td>Date</td>
            <?php
		}
		?>
        <td width="6%">Emp Code</td>
        <td width="15%">Emp Name</td>
        <td>Customer<br /> Visited</td>
        <td>Order<br /> Received</td>
        <td>No<br /> Transaction</td>
        <td>Stock<br /> Audit(Qty)</td>
        <td width="8%">KYC</td>
        <td>Brand<br /> Activity</td>
        <td>Technical<br /> Meet</td>
        <td>Site<br /> Visit</td>
        <td>Market<br /> Feedback</td>
        <td>Total<br /> Activity</td>
      </tr>
    <?php
	
	if($mode == 'datewise'){
		$employee_arg = str_replace("#",",",$employee);
		$employee_arg = str_replace("^","'",$employee_arg);
		$count = 1;
		$sql_distinct_date = "SELECT DISTINCT operation_date FROM mis_details_emp_datewise WHERE emp_code IN (".$employee_arg.") AND (operation_date BETWEEN '".$start_date."' AND '".$end_date."') ORDER BY operation_date";
		$res_distinct_date = mysql_query($sql_distinct_date);
		while($row_distinct_date = mysql_fetch_array($res_distinct_date)){
			$operation_date = $row_distinct_date['operation_date'];
			echo "<tr><td class='TDHEAD_SUB' colspan='14' align='center'>".date('d-m-Y',strtotime($operation_date))."</td></tr>";
			
			$sql_emp_details = "SELECT * FROM mis_details_emp_datewise WHERE operation_date = '".$operation_date."' AND emp_code IN(".$employee_arg.") AND present = 1";
			$res_emp_details = mysql_query($sql_emp_details);
			while($row_emp_details = mysql_fetch_array($res_emp_details)){
				$emp_code = $row_emp_details['emp_code'];
				$present_data = $row_emp_details['present'];
				
				$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
				$res_emp_name = mysql_query($sql_emp_name);
				$row_emp_name = mysql_fetch_array($res_emp_name);
				$emp_name = $row_emp_name['emp_name'];
				
				$count_customer_array = array();
				$sql_customer_visit = "SELECT customer_visited FROM mis_details_emp_datewise WHERE emp_code = '".$emp_code."' AND operation_date = '".$operation_date."'";
				$res_customer_visit = mysql_query($sql_customer_visit);
				$row_customer_visit = mysql_fetch_array($res_customer_visit);
				$customer_visited = $row_customer_visit['customer_visited'];
					
				$customer_array = array();
				if($customer_visited != ''){
					if(strpos($customer_visited,",") == FALSE)
						$customer_array[] = $customer_visited;
					else{
						$customer_array = explode(",",$customer_visited);
					}
					
					foreach($customer_array  as $customer_value){
						$count_customer_array[$customer_value] = '1';
					}
				}
				$no_customer_visit = count($count_customer_array);
				
				$order_received = $row_emp_details['order_received'];
				$no_transaction = $row_emp_details['no_transaction'];
				$stock_audit = $row_emp_details['stock_audit'];
				$kyc = $row_emp_details['kyc'];
				$brand_activity = $row_emp_details['brand_activity'];
				$technical_meet = $row_emp_details['technical_meet'];
				$site_visit = $row_emp_details['site_visit'];
				$market_feedback = $row_emp_details['market_feedback'];
				
				$total_activity = ($no_customer_visit + $kyc + $brand_activity + $technical_meet + $site_visit);
				
				if($present_data != 0 || $no_customer_visit != 0 || $order_received != 0 || $no_transaction != 0 || $stock_audit != 0 || $kyc != 0 || $brand_activity != 0 || $technical_meet != 0 || $site_visit != 0 || $market_feedback != 0){
					echo "<tr>
					<td align=\"right\">".$count."</td>
					<td>".date('d-m-Y',strtotime($operation_date))."</td>
					<td>".$emp_code."</td>
					<td>".$emp_name."</td>
					<td align=\"right\">".$no_customer_visit."</td>
					<td align=\"right\">".number_format($order_received,2)."</td>
					<td align=\"right\">".$no_transaction."</td>
					<td align=\"right\">".number_format($stock_audit,2)."</td>
					<td align=\"right\">".$kyc."</td>
					<td align=\"right\">".$brand_activity."</td>
					<td align=\"right\">".$technical_meet."</td>
					<td align=\"right\">".$site_visit."</td>
					<td align=\"right\">".$market_feedback."</td>
					<td align=\"right\">".$total_activity."</td>
				  </tr>";
				$count++;
				}
			}
		}
		echo "</table>";
		?>
         <br />
<div style="width:100%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
        <?php
	}
	else{
		if($val == 'TDY'){
		$date = date('Ymd');
		$order_header_date_cond = " SUBSTRING(order_no,-14,8) = '".$date."' ";
		$stock_audit_date_cond = " SUBSTRING(transaction_id,-14,8) = '".$date."' ";
		$market_feedback_date_cond = " SUBSTRING(market_feedback_id,-14,8) = '".$date."' ";
		$all_trans_date_cond = " SUBSTRING(trans_id,-14,8) = '".$date."' ";
				
		$present_col = 'present_tdy';
		$customer_visited_col = 'customer_visited_tdy';
		$order_received_col = 'order_received_tdy';
		$no_transaction_col = 'no_transaction_tdy';
		$stock_audit_col = 'stock_audit_tdy';
		$kyc_col = 'kyc_tdy';
		$brand_activity_col = 'brand_activity_tdy';
		$technical_meet_col = 'technical_meet_tdy';
		$site_visit_col = 'site_visit_tdy';
		$market_feedback_col = 'market_feedback_tdy';
	}
	else if($val == 'MTD'){
		$current_month = date('Ym');
		$order_header_date_cond = " SUBSTRING(order_no,-14,6) = '".$current_month."' ";
		$stock_audit_date_cond = " SUBSTRING(transaction_id,-14,6) = '".$current_month."' ";
		$market_feedback_date_cond = " SUBSTRING(market_feedback_id,-14,6) = '".$current_month."' ";
		$all_trans_date_cond = " SUBSTRING(trans_id,-14,6) = '".$current_month."' ";
				
		$present_col = 'present_mtd';
		$customer_visited_col = 'customer_visited_mtd';
		$order_received_col = 'order_received_mtd';
		$no_transaction_col = 'no_transaction_mtd';
		$stock_audit_col = 'stock_audit_mtd';
		$kyc_col = 'kyc_mtd';
		$brand_activity_col = 'brand_activity_mtd';
		$technical_meet_col = 'technical_meet_mtd';
		$site_visit_col = 'site_visit_mtd';
		$market_feedback_col = 'market_feedback_mtd';
	}
	else if($val == 'YTD'){
		$year = date('Y');
		$month = date('m');
		$end_date = date('Ymd');
		if($month>='04'){
			$fiinancial_year=$year.'0401';
		}
		else{
			$fiinancial_year=($year-1).'0401';
		}
		$order_header_date_cond = " (SUBSTRING(order_no,-14,8) BETWEEN '".$fiinancial_year."' AND '".$end_date."') ";
		$stock_audit_date_cond = " (SUBSTRING(transaction_id,-14,8) BETWEEN '".$fiinancial_year."' AND '".$end_date."') ";
		$market_feedback_date_cond = " (SUBSTRING(market_feedback_id,-14,8) BETWEEN '".$fiinancial_year."' AND '".$end_date."') ";
		$all_trans_date_cond = " (SUBSTRING(trans_id,-14,8) BETWEEN '".$fiinancial_year."' AND '".$end_date."') ";
				
		$present_col = 'present_ytd';
		$customer_visited_col = 'customer_visited_ytd';
		$order_received_col = 'order_received_ytd';
		$no_transaction_col = 'no_transaction_ytd';
		$stock_audit_col = 'stock_audit_ytd';
		$kyc_col = 'kyc_ytd';
		$brand_activity_col = 'brand_activity_ytd';
		$technical_meet_col = 'technical_meet_ytd';
		$site_visit_col = 'site_visit_ytd';
		$market_feedback_col = 'market_feedback_ytd';
	}
	
	$count = 1;
	$sql_result = "SELECT emp_code, $present_col, $order_received_col, $no_transaction_col, $stock_audit_col, $kyc_col, $brand_activity_col, $technical_meet_col, $site_visit_col, $market_feedback_col FROM mis_data_details WHERE emp_code IN(".$employee_arg.")";
	$res_result = mysql_query($sql_result);
	$total_result = mysql_num_rows($res_result);
	if($total_result>0){
	
		$res_result = mysql_query($sql_result);
		while($row_result = mysql_fetch_array($res_result)){
			$emp_code = $row_result['emp_code'];
			$present_data = $row_result[$present_col];
			//$customer_visited = $row_result[$customer_visited_col];
			$order_received = $row_result[$order_received_col];
			$no_transaction = $row_result[$no_transaction_col];
			$stock_audit = $row_result[$stock_audit_col];
			$kyc = $row_result[$kyc_col];
			$brand_activity = $row_result[$brand_activity_col];
			$technical_meet = $row_result[$technical_meet_col];
			$site_visit = $row_result[$site_visit_col];
			$market_feedback = $row_result[$market_feedback_col];
			
			/*----------> Total Customer Visit <----------*/
			$customer_code_array = array();
			/*$sql_order_header_customer = "SELECT customer_code, SUBSTRING(order_no,-14,8) AS oh_date FROM order_header WHERE SUBSTRING(order_no,-19,5) = '".$emp_code."' AND ".$order_header_date_cond." GROUP BY customer_code, SUBSTRING(order_no,-14,8)";
			$res_order_header_customer = mysql_query($sql_order_header_customer);
			while($row_order_header_customer = mysql_fetch_array($res_order_header_customer)){
				$order_header_customer_code = $row_order_header_customer['customer_code'];
				$location_date = $row_order_header_customer['oh_date'];
				$customer_concat_date = $order_header_customer_code."^".$location_date;*/
				/*if(!in_array($customer_concat_date,$customer_code_array))
					array_push($customer_code_array,$customer_concat_date);*/
				/*$customer_code_array[$customer_concat_date] = '1';
			}
			
			$sql_stock_audit_customer = "SELECT customer_code, SUBSTRING(transaction_id,-14,8) AS sa_date FROM stock_audit WHERE SUBSTRING(transaction_id,-19,5) = '".$emp_code."' AND ".$stock_audit_date_cond." GROUP BY customer_code, SUBSTRING(transaction_id,-14,8)";
			$res_stock_audit_customer = mysql_query($sql_stock_audit_customer);
			while($row_stock_audit_customer = mysql_fetch_array($res_stock_audit_customer)){
				$stock_audit_customer = $row_stock_audit_customer['customer_code'];
				$location_date = $row_stock_audit_customer['sa_date'];
				$customer_concat_date = $stock_audit_customer."^".$location_date;*/
				/*if(!in_array($customer_concat_date,$customer_code_array))
					array_push($customer_code_array,$customer_concat_date);*/
				/*$customer_code_array[$customer_concat_date] = '1';
			}
								
			$sql_market_feedback = "SELECT customer_code, SUBSTRING(market_feedback_id,-14,8) AS mf_date FROM market_feedback WHERE SUBSTRING(market_feedback_id,3,5) = '".$emp_code."' AND ".$market_feedback_date_cond." GROUP BY customer_code, SUBSTRING(market_feedback_id,-14,8)";
			$res_market_feedback = mysql_query($sql_market_feedback);
			while($row_market_feedback = mysql_fetch_array($res_market_feedback)){
				$market_feedback_customer = $row_market_feedback['customer_code'];
				$market_feedback_date = $row_market_feedback['mf_date'];
				$customer_concat_date = $market_feedback_customer."^".$market_feedback_date;*/
				/*if(!in_array($market_concat_date,$customer_code_array))
					array_push($customer_code_array,$market_concat_date);*/
				/*$customer_code_array[$customer_concat_date] = '1';
			}*/
			$sql_all_trans_customer = "SELECT customer_code, SUBSTRING(trans_id,-14,8) AS sa_date FROM customer_visit_details 
									WHERE SUBSTRING(trans_id,-19,5) = '".$emp_code."' AND ".$all_trans_date_cond." 
									GROUP BY customer_code, SUBSTRING(trans_id,-14,8)";
			$res_all_trans_customer = mysql_query($sql_all_trans_customer);
			//$no_customer_visit = count($customer_code_array);
			$no_customer_visit=mysql_num_rows($res_all_trans_customer);
			//unset($customer_code_array);
			
			$total_activity = ($no_customer_visit + $kyc + $brand_activity + $technical_meet + $site_visit);
			
			$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
			$res_emp_name = mysql_query($sql_emp_name);
			$row_emp_name = mysql_fetch_array($res_emp_name);
			$emp_name = $row_emp_name['emp_name'];
			
			if($present_data != 0 || $customer_visited != 0 || $order_received != 0 || $no_transaction != 0 || $stock_audit != 0 || $kyc != 0 || $brand_activity != 0 || $technical_meet != 0 || $site_visit != 0 || $market_feedback != 0){
			
				echo "<tr>
						<td align=\"right\">".$count."</td>
						<td>".$emp_code."</td>
						<td>".$emp_name."</td>
						<td align=\"right\">".$no_customer_visit."</td>
						<td align=\"right\">".number_format($order_received,2)."</td>
						<td align=\"right\">".$no_transaction."</td>
						<td align=\"right\">".number_format($stock_audit,2)."</td>
						<td align=\"right\">".$kyc."</td>
						<td align=\"right\">".$brand_activity."</td>
						<td align=\"right\">".$technical_meet."</td>
						<td align=\"right\">".$site_visit."</td>
						<td align=\"right\">".$market_feedback."</td>
						<td align=\"right\">".$total_activity."</td>
					  </tr>";
				$count++;
			}
		}
		?>
		</table>
		<br />
	<div style="width:100%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
		<input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
	</div>
		<?php
	}
	else{
		echo "No Records";
	}
	}
}
?>
