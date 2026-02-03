<?php
ob_start();
session_start();
require("adminUtils.php");

main();

function main(){
	?>
    <script src="dist/js/jquery.js"></script>
	<script src="dist/js/hoverIntent.js"></script>
    <script src="dist/js/superfish.js"></script>
    <script language="JavaScript" src="calendar3.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>
    <script type="text/javascript" src="jquery.highlight.js"></script>
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
    <!-- polyfiller file to detect and load polyfills -->
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
    <script>
      webshims.setOptions('waitReady', false);
      webshims.setOptions('forms-ext', {types: 'date'});
      webshims.polyfill('forms forms-ext');
    </script>
    <?php
	$mode = $_REQUEST['mode'];
	$start_date = $_REQUEST['start_date'];
	$end_date = $_REQUEST['end_date'];
	$employee = $_REQUEST['employee'];
	$empl = str_replace("'","",$empl);
	$empl_explode = explode(",",$empl);
	$employee_arg = str_replace(",","#",$employee);
	$employee_arg = str_replace("'","^",$employee_arg);
	
	?>
    <div id="report_tag" align="right" style="font-size:9px;" >* Total Field Activity = No of Customer visited + KYC + Brand Activity +Technical Meet + Site Visit + Market Feedback</div><br />
    <table width="90%" border="1" align="center" border="0" cellpadding="5" cellspacing="1" class="border" style="display: '';height: 150px;overflow-y: scroll;" id="todayAttDisplay">
    <tr class="TDHEAD" > 
        <td colspan="13" align="center"><strong>MIS Report</strong></td>
    </tr>
    <tr class="TDHEAD_SUB" style="text-align:left;"> 
        <td align="center"></td>
        <td align="left">Available Field <br /><span>Force</span></td>
        <td align="left">Present</td>
        <td align="left">Customer Visited</td>
        <?php if(order == 'yes'){?>
        <td align="left"><span>Order</span><br /> Received<br />(qty)</td>
        <?php } ?>
        <?php if(collection == 'yes'){?>
        <td align="left"><span>Collection</span><br /> Received(Rs/-)</td>
        <?php } ?>
        <td align="left"><span>No</span><br /> Transaction</td>
        <?php if(stk_audit=='yes'){?>
        <td align="left">Stock Audit<br /><span>(Qty)</span></td>
        <?php }?>
        <td>KYC</td>
        <td>Brand Activity</td>
        <td>Technical Meet</td>
        <td>Site Visit</td>
        <td>Market Feedback</td>
        <td>Total Field Activity</td>
    </tr> 
    <?php
	if($mode == 'datewise'){
		$employee_arg = str_replace("#",",",$employee);
		$employee_arg = str_replace("^","'",$employee_arg);
		
		function get_query_result($column_name,$start_date,$end_date){
			$employee_arg = str_replace("#",",",$_REQUEST['employee']);
			$employee_arg = str_replace("^","'",$employee_arg);
		
			$sql_result = "SELECT SUM($column_name) AS get_result FROM mis_details_emp_datewise WHERE emp_code IN (".$employee_arg.") AND (operation_date BETWEEN '".$start_date."' AND '".$end_date."')";
			$res_result = mysqli_query($link,$sql_result);
			$row_result = mysqli_fetch_assoc($res_result);
			$return_result = $row_result['get_result'];
			return $return_result;
		}
		
		$sqlfieldforce="SELECT COUNT(EM.emp_code) AS available_field_force FROM employee_master EM,changepassword CH 					WHERE CH.emp_code=EM.emp_code AND CH.is_licensed='1' AND SUBSTRING(EM.emp_code,1,1)!='C' AND EM.acedns = 'Y' AND EM.emp_code IN(".$employee_arg.")";
		$resfieldforce=mysqli_query($link,$sqlfieldforce) or die(mysqli_error()." Error in select field force: ".$sqlfieldforce);
		$rowfieldforce=mysqli_fetch_assoc($resfieldforce);
		$av_field_force=$rowfieldforce['available_field_force'];
		
		
		$sql_present = "SELECT COUNT(DISTINCT emp_code) AS present FROM mis_details_emp_datewise WHERE emp_code IN
		(".$employee_arg.") AND (operation_date BETWEEN '".$start_date."' AND '".$end_date."')";
		$res_present = mysqli_query($link,$sql_present);
		$row_present = mysqli_fetch_assoc($res_present);
		$present_data = $row_present['present'];
		
		$count_customer_array = array();
		$sql_customer_visit = "SELECT operation_date, emp_code, customer_visited FROM mis_details_emp_datewise WHERE emp_code IN(".$employee_arg.") AND (operation_date BETWEEN '".$start_date."' AND '".$end_date."')";
		$res_customer_visit = mysqli_query($link,$sql_customer_visit);
		while($row_customer_visit = mysqli_fetch_assoc($res_customer_visit)){
			$operation_date = $row_customer_visit['operation_date'];
			$emp_code = $row_customer_visit['emp_code'];
			$customer_visited = $row_customer_visit['customer_visited'];
			
			$customer_array = array();
			if($customer_visited != ''){
				if(strpos($customer_visited,",") == FALSE)
					$customer_array[] = $customer_visited;
				else{
					$customer_array = explode(",",$customer_visited);
				}
				
				foreach($customer_array  as $customer_value){
					$customer_index = $operation_date."^".$emp_code."^".$customer_value;
					$count_customer_array[$customer_index] = '1';
				}
			}
		}
		$no_customer_visit = count($count_customer_array);
		
		/*----------> Order Received Data <----------*/
		$order_received = get_query_result('order_received',$start_date,$end_date);
		
		/*----------> No Transaction Data <----------*/
		$no_transaction = get_query_result('no_transaction',$start_date,$end_date);
		
		/*----------> Stock Audit Data <----------*/
		$stock_audit = get_query_result('stock_audit',$start_date,$end_date);
		
		/*----------> KYC Data <----------*/
		$kyc = get_query_result('kyc',$start_date,$end_date);
		
		/*----------> Brand Data <----------*/
		$brand_activity = get_query_result('brand_activity',$start_date,$end_date);
		
		/*----------> Technical Meet Data <----------*/
		$technical_meet = get_query_result('technical_meet',$start_date,$end_date);
		
		/*----------> Site Visit Data <----------*/
		$site_visit = get_query_result('site_visit',$start_date,$end_date);
		
		/*----------> Site Visit Data <----------*/
		$market_feedback = get_query_result('market_feedback',$start_date,$end_date);
		
		/*---> Total Field Activity = No of Customer visited + KYC + Brand Activity +Technical Meet + Site Visit + Market Feedback <---*/
		$total_activity = ($no_customer_visit + $kyc + $brand_activity + $technical_meet + $site_visit + $market_feedback);
			
		echo "<tr>
				<td><a href=\"#\" style=\"color:blue;\" onclick=\"show_emp_data_details('".$mode."','".$employee."','".$start_date."','".$end_date."')\">Custom</a></td>
				<td align=\"right\">".$av_field_force."</td>
				<td align=\"right\">".$present_data."</td>
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
		?>
        <tr>
	<td colspan="13" align="right">
    <div align="center">
    	<div id="custom_date_div" style="width:60%;" >
        From:<input type="date" name="start_date" id="start_date_val" value="<?php echo $start_date; ?>" style="height:20px;" />
        To:<input type="date" name="end_date" id="end_date_val" value="<?php echo $end_date; ?>" style="height:20px;" />
        <input type="submit" name="submit" value="Submit" onClick="show_datewisedata('<?php echo $employee; ?>');" />
        </div>
    </div>
    </td>
</tr>
        <?php
		echo "</table>";
	}
	else{	
	function get_query_result($column_name){
		$sql_result = "SELECT SUM($column_name) AS get_result FROM mis_data_details WHERE emp_code IN (".$_REQUEST['employee'].")";
		$res_result = mysqli_query($link,$sql_result);
		$row_result = mysqli_fetch_assoc($res_result);
		$return_result = $row_result['get_result'];
		return $return_result;
	}
	
	$sqlfieldforce="SELECT COUNT(EM.emp_code) AS available_field_force FROM employee_master EM,changepassword CH 
					WHERE CH.emp_code=EM.emp_code AND CH.is_licensed='1' AND SUBSTRING(EM.emp_code,1,1)!='C' AND EM.acedns = 'Y' AND EM.emp_code IN(".$employee.")";
	$resfieldforce=mysqli_query($link,$sqlfieldforce) or die(mysqli_error()." Error in select field force: ".$sqlfieldforce);
	$rowfieldforce=mysqli_fetch_assoc($resfieldforce);
	$av_field_force=$rowfieldforce['available_field_force'];
						
	/*--------> Today Data Fetch <--------*/					
	for($i=1;$i<=3;$i++){
		if($i == 1){
			$date = date('Ymd');
			$order_header_date_cond = " SUBSTRING(order_no,-14,8) = '".$date."' ";
			$stock_audit_date_cond = " SUBSTRING(transaction_id,-14,8) = '".$date."' ";
			$market_feedback_date_cond = " SUBSTRING(market_feedback_id,-14,8) = '".$date."' ";
			
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
			
			$sl_value='TODAY';
			$val='TDY';
		}
		
		if($i == 2){
			$current_month = date('Ym');
			$order_header_date_cond = " SUBSTRING(order_no,-14,6) = '".$current_month."' ";
			$stock_audit_date_cond = " SUBSTRING(transaction_id,-14,6) = '".$current_month."' ";
			$market_feedback_date_cond = " SUBSTRING(market_feedback_id,-14,6) = '".$current_month."' ";
								
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
			
			$sl_value='MTD';
			$val='MTD';
		}
		
		if($i == 3){
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
			
			$sl_value='YTD';
			$val='YTD';
		}
		
		/*----------> Total Customer Visit <----------*/
		$customer_code_array = array();
		
		$sql_order_header_customer = "SELECT SUBSTRING(order_no,-19,5) AS emp_code, customer_code, SUBSTRING(order_no,-14,8) AS oh_date FROM order_header WHERE SUBSTRING(order_no,-19,5) IN(".$employee.") AND ".$order_header_date_cond." GROUP BY SUBSTRING(order_no,-19,5), customer_code, SUBSTRING(order_no,-14,8)";
		$res_order_header_customer = mysqli_query($link,$sql_order_header_customer);
		while($row_order_header_customer = mysqli_fetch_assoc($res_order_header_customer)){
			$emp_code = $row_order_header_customer['emp_code'];
			$order_header_customer_code = $row_order_header_customer['customer_code'];
			$location_date = $row_order_header_customer['oh_date'];
			$customer_concat_date = $emp_code."^".$order_header_customer_code."^".$location_date;
			/*if(!in_array($customer_concat_date,$customer_code_array))
				array_push($customer_code_array,$customer_concat_date);*/
			$customer_code_array[$customer_concat_date] = '1';
		}
		
		$sql_stock_audit_customer = "SELECT SUBSTRING(transaction_id,-19,5) AS emp_code, customer_code, SUBSTRING(transaction_id,-14,8) AS sa_date FROM stock_audit WHERE SUBSTRING(transaction_id,-19,5) IN(".$employee.") AND ".$stock_audit_date_cond." GROUP BY SUBSTRING(transaction_id,-19,5), customer_code, SUBSTRING(transaction_id,-14,8)";
		$res_stock_audit_customer = mysqli_query($link,$sql_stock_audit_customer);
		while($row_stock_audit_customer = mysqli_fetch_assoc($res_stock_audit_customer)){
			$emp_code = $row_stock_audit_customer['emp_code'];
			$stock_audit_customer = $row_stock_audit_customer['customer_code'];
			$location_date = $row_stock_audit_customer['sa_date'];
			$customer_concat_date = $emp_code."^".$stock_audit_customer."^".$location_date;
			/*if(!in_array($customer_concat_date,$customer_code_array))
				array_push($customer_code_array,$customer_concat_date);*/
			$customer_code_array[$customer_concat_date] = '1';
		}
							
		$sql_market_feedback = "SELECT SUBSTRING(market_feedback_id,-19,5) AS emp_code, customer_code, SUBSTRING(market_feedback_id,-14,8) AS mf_date FROM market_feedback WHERE SUBSTRING(market_feedback_id,3,5) IN (".$employee.") AND ".$market_feedback_date_cond." GROUP BY SUBSTRING(market_feedback_id,-19,5), customer_code, SUBSTRING(market_feedback_id,-14,8)";
		$res_market_feedback = mysqli_query($link,$sql_market_feedback);
		while($row_market_feedback = mysqli_fetch_assoc($res_market_feedback)){
			$emp_code = $row_market_feedback['emp_code'];
			$market_feedback_customer = $row_market_feedback['customer_code'];
			$market_feedback_date = $row_market_feedback['mf_date'];
			$customer_concat_date = $emp_code."^".$market_feedback_customer."^".$market_feedback_date;
			/*if(!in_array($market_concat_date,$customer_code_array))
				array_push($customer_code_array,$market_concat_date);*/
			$customer_code_array[$customer_concat_date] = '1';
		}
		$no_customer_visit = count($customer_code_array);
		//unset($customer_code_array);
		
		/*----------> Employee Present Data <----------*/
		//$present_data = get_query_result($present_col);
		$sql_present_data = "SELECT COUNT(emp_code) FROM mis_data_details WHERE emp_code IN(".$employee.") AND $present_col>0";
		$res_present_data = mysqli_query($link,$sql_present_data);
		$row_present_data = mysqli_fetch_assoc($res_present_data);
		$present_data = $row_present_data['COUNT(emp_code)'];
		
		/*----------> Customer Visit Data <----------*/
		//$customer_visited = get_query_result($customer_visited_col);
		
		/*----------> Order Received Data <----------*/
		$order_received = get_query_result($order_received_col);
		
		/*----------> No Transaction Data <----------*/
		$no_transaction = get_query_result($no_transaction_col);
		
		/*----------> Stock Audit Data <----------*/
		$stock_audit = get_query_result($stock_audit_col);
		
		/*----------> KYC Data <----------*/
		$kyc = get_query_result($kyc_col);
		
		/*----------> Brand Data <----------*/
		$brand_activity = get_query_result($brand_activity_col);
		
		/*----------> Technical Meet Data <----------*/
		$technical_meet = get_query_result($technical_meet_col);
		
		/*----------> Site Visit Data <----------*/
		$site_visit = get_query_result($site_visit_col);
		
		/*----------> Site Visit Data <----------*/
		$market_feedback = get_query_result($market_feedback_col);
		
		/*---> Total Field Activity = No of Customer visited + KYC + Brand Activity +Technical Meet + Site Visit + Market Feedback <---*/
		$total_activity = ($no_customer_visit + $kyc + $brand_activity + $technical_meet + $site_visit + $market_feedback);
		
		echo "<tr>
				<td><a href=\"#\" style=\"color:blue;\" onclick=\"show_mis_details('".$val."','".$employee_arg."')\">".$sl_value."</a></td>
				<td align=\"right\">".$av_field_force."</td>
				<td align=\"right\">".$present_data."</td>
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
		
	}
	
?>
<tr>
	<td colspan="13" align="right">
    	<div align="center">
        <div id="custom_date_div" style="width:60%;" hidden >
        From:<input type="date" name="start_date" id="start_date_val" value="<?php echo $start_date; ?>" style="height:20px;" />
        To:<input type="date" name="end_date" id="end_date_val" value="<?php echo $end_date; ?>" style="height:20px;" />
        <input type="submit" name="submit" value="Submit" onClick="show_datewisedata('<?php echo $employee_arg; ?>');" />
        </div>
        </div>
        <input type="button" value="Custom" onclick="show_date_range_control();" />
        
    </td>
</tr>
</table>
<?php
	
	}
}
?>