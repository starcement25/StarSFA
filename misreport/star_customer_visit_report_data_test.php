<?php
ob_start();
session_start();
require("adminUtils.php");

$month_data = $_REQUEST['month_data'];
$year_month_split = explode("-",$month_data);
$month_days = cal_days_in_month(CAL_GREGORIAN,$year_month_split[1],$year_month_split[0]);
$monthName = date('M', mktime(0, 0, 0, $year_month_split[1], 10));
$show_month = $monthName."-".$year_month_split[0];
$employee = $_REQUEST['employee'];
$employee_arg = str_replace("#",",",$employee);
$employee_arg = str_replace("^","'",$employee_arg);

$zone = $_REQUEST['zone'];
$state = $_REQUEST['state'];
$branch = $_REQUEST['branch'];
$department = $_REQUEST['department'];

if(strpos($zone,",") == FALSE)	$zone = str_replace("'","",$zone);
else								$zone = "All";

if(strpos($state,",") == FALSE)	$state = str_replace("'","",$state);
else								$state = "All";

if(strpos($branch,",") == FALSE)	$branch = str_replace("'","",$branch);
else								$branch = "All";

if(strpos($department,",") == FALSE)	$department = str_replace("'","",$department);
else									$department = "All";


if(strpos($employee,",") == FALSE){
	$new_emp_code = str_replace("'","",$employee);
	$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$new_emp_code."'";
	$res_emp_name = mysqli_query($link,$sql_emp_name);
	$row_emp_name = mysqli_fetch_assoc($res_emp_name);
	$new_emp_name = $row_emp_name['emp_name'];
}
else{
	$new_emp_name = "All";
}

function customer_count($emp_code,$cust_type){
	$emp_hierarchy = return_employee_hierarchy($emp_code);
	$sql_count = "SELECT COUNT(DISTINCT customer_code) AS cust_count FROM customer_master WHERE emp_code IN(".$emp_hierarchy.") AND cust_type = '".$cust_type."'";
	$res_count = mysqli_query($link,$sql_count);
	$row_count = mysqli_fetch_assoc($res_count);
	return $row_count['cust_count'];
}

function visit_customer_count($emp_code,$cust_type){
	//$emp_hierarchy = return_employee_hierarchy($emp_code);
	$sql_count = "SELECT COUNT(DISTINCT customer_code) AS cust_count FROM customer_visit_details WHERE emp_code = '".$emp_code."' AND cust_type = '".$cust_type."' AND SUBSTRING(trans_id,-14,6) = '".str_replace("-","",$_REQUEST['month_data'])."'";
	$res_count = mysqli_query($link,$sql_count);
	$row_count = mysqli_fetch_assoc($res_count);
	return $row_count['cust_count'];
}

function notvisit_customer_count($emp_code,$cust_type){
	$count = 0;
	//$emp_hierarchy = return_employee_hierarchy($emp_code);
	$sql_customer_master = "SELECT DISTINCT customer_code FROM customer_master WHERE emp_code = '".$emp_code."' AND cust_type = '".$cust_type."' AND customer_code NOT IN(SELECT DISTINCT customer_code FROM customer_visit_details WHERE emp_code = '".$emp_code."' AND cust_type = '".$cust_type."' AND SUBSTRING(trans_id,-14,6) = '".str_replace("-","",$_REQUEST['month_data'])."')";
	$res_customer_master = mysqli_query($link,$sql_customer_master);
	$row_count = mysqli_num_rows($res_customer_master);
	return $row_count;
}

$header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Month:".$month_data;

	$count = 1;
	$emp_array = array();
	$sql_customer = "SELECT DISTINCT emp_code FROM customer_visit_details WHERE emp_code IN(".$employee.") AND SUBSTRING(trans_id,-14,6) = '".str_replace("-","",$month_data)."' ORDER BY emp_code, SUBSTRING(trans_id,-14,8) ASC";
	$res_customer = mysqli_query($link,$sql_customer);
	$total_customer = mysqli_num_rows($res_customer);
	
	if($total_customer>0){
		?>
        <table border="1" id="display_table_one" style="border-collapse:collapse;" class="border" width="100%" cellpadding="4">
          <tr class="TDHEAD_SUB">
          	<td></td>
            <td colspan="4" align="center">Mapped</td>
            <td colspan="4" align="center">Visited</td>
            <td colspan="4" align="center">Not Visited</td>
          </tr>
          <tr class="TDHEAD">
          	<td align="center">Emp</td>
            <td align="center">Dealer</td>
            <td align="center">Sub Dealer</td>
            <td align="center">Non Star</td>
            <td align="center">Total</td>
            <td align="center">Dealer</td>
            <td align="center">Sub Dealer</td>
            <td align="center">Non Star</td>
            <td align="center">Total</td>
            <td align="center">Dealer</td>
            <td align="center">Sub Dealer</td>
            <td align="center">Non Star</td>
            <td align="center">Total</td>
          </tr>
       <?php
		$res_customer = mysqli_query($link,$sql_customer);
		while($row_customer = mysqli_fetch_assoc($res_customer)){
			$emp_code = $row_customer['emp_code'];
			$customer_code = $row_customer['customer_code'];
			$cust_type = $row_customer['cust_type'];
			
			$sql_emp = "SELECT emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
			$res_emp = mysqli_query($link,$sql_emp);
			$row_emp = mysqli_fetch_assoc($res_emp);
			$emp_name = $row_emp['emp_name'];
			
			$mapped_dealer = customer_count($emp_code,'Dealer');
			$mapped_sub_dealer = customer_count($emp_code,'Sub Dealer');
			$mapped_non_star = customer_count($emp_code,'Non Star');
			
			$total_mapped = $mapped_dealer+$mapped_sub_dealer+$mapped_non_star;
			
			$visit_dealer = visit_customer_count($emp_code,'Dealer');
			$visit_sub_dealer = visit_customer_count($emp_code,'Sub Dealer');
			$visit_non_star = visit_customer_count($emp_code,'Non Star');
			
			$total_visited = $visit_dealer+$visit_sub_dealer+$visit_non_star;
			
			$notvisit_dealer = notvisit_customer_count($emp_code,'Dealer');
			$notvisit_sub_dealer = notvisit_customer_count($emp_code,'Sub Dealer');
			$notvisit_non_star = notvisit_customer_count($emp_code,'Non Star');
			
			$total_non_visit = $notvisit_dealer+$notvisit_sub_dealer+$notvisit_non_star;
			
			echo "<tr>
					<td>".$emp_name."</td>
					<td align=\"right\">".$mapped_dealer."</td>
					<td align=\"right\">".$mapped_sub_dealer."</td>
					<td align=\"right\">".$mapped_non_star."</td>
					<td align=\"right\" style=\"font-weight:bold;\">".$total_mapped."</td>
					<td align=\"right\">".$visit_dealer."</td>
					<td align=\"right\">".$visit_sub_dealer."</td>
					<td align=\"right\">".$visit_non_star."</td>
					<td align=\"right\" style=\"font-weight:bold;\">".$total_visited."</td>
					<td align=\"right\">".$notvisit_dealer."</td>
					<td align=\"right\">".$notvisit_sub_dealer."</td>
					<td align=\"right\">".$notvisit_non_star."</td>
					<td align=\"right\" style=\"font-weight:bold;\">".$total_non_visit."</td>
				  </tr>";
		}
		?>
        </table>
        <br /><br />
        <?php
	}
	
	$sql_customer = "SELECT DISTINCT emp_code, customer_code, customer_name, route_name, route_code, cust_type FROM customer_visit_details WHERE emp_code IN(".$employee.") AND SUBSTRING(trans_id,-14,6) = '".str_replace("-","",$month_data)."' ORDER BY emp_code, SUBSTRING(trans_id,-14,8) ASC";
	$res_customer = mysqli_query($link,$sql_customer);
	$total_customer = mysqli_num_rows($res_customer);
	if($total_customer>0){
		$count = 1;
		?>
		<table border="1" id="display_table" style="border-collapse:collapse;" class="border" width="100%">
          <tr class="TDHEAD_SUB">
          	<td colspan="8"><?php echo $header_string;  ?></td>
          </tr>
		  <tr class="TDHEAD" align="center">
			<td>SI</td>
			<td>Customer Name</td>
			<td>Type</td>
			<td>Route</td>
			<td>No Of Visit</td>
			<td>Visit Dates</td>
			<td>Productive Call</td>
			<td>Non Productive Call</td>
		  </tr>
		<?php
		$res_customer = mysqli_query($link,$sql_customer);
		while($row_customer = mysqli_fetch_assoc($res_customer)){
			$emp_code = $row_customer['emp_code'];
			$customer_code = $row_customer['customer_code'];
			$customer_name = $row_customer['customer_name'];
			$route_name = $row_customer['route_name'];
			$route_code = $row_customer['route_code'];
			$cust_type = $row_customer['cust_type'];
			
			if(!in_array($emp_code,$emp_array)){
				array_push($emp_array,$emp_code);
				$sql_emp = "SELECT emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
				$res_emp = mysqli_query($link,$sql_emp);
				$row_emp = mysqli_fetch_assoc($res_emp);
				$emp_name = $row_emp['emp_name'];
				
				echo "<tr class=\"TDHEAD_SUB\"><td colspan=\"8\" align=\"center\">".$emp_name."</td></tr>";
			}
			
			$productive_call = 0;
			$non_productive_call = 0;
			
			$date_array = array();
			$sql_transaction = "SELECT trans_id, DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%d-%m-%Y') AS date_select FROM customer_visit_details WHERE emp_code = '".$emp_code."' AND customer_code = '".$customer_code."' AND SUBSTRING(trans_id,-14,6) = '".str_replace("-","",$month_data)."'";
			$res_transaction = mysqli_query($link,$sql_transaction);
			while($row_transaction = mysqli_fetch_assoc($res_transaction)){
				$trans_id = $row_transaction['trans_id'];
				$date_select = $row_transaction['date_select'];
				
				$transid_substr = substr($trans_id,0,2);
				if($transid_substr == "OE" || $transid_substr == "SE" || $transid_substr == "PE" || $transid_substr == "MF"){
					$date_array[$date_select] = '1';
					$productive_call++;
					
				}
				else if( $transid_substr == "NS" || $transid_substr == "NO" || $transid_substr == "NC"){
					$date_array[$date_select] = '1';
					$non_productive_call++;
				}
			}
			
			$date_string = '';
			foreach($date_array as $key=>$val){
				$date_string .= $key.", ";
			}
			$date_string = trim($date_string);
			$date_string = rtrim($date_string,",");
			
			$total_visit = count($date_array);
			
			echo "<tr>
					<td>".$count."</td>
					<td>".$customer_name."</td>
					<td>".$cust_type."</td>
					<td>".$route_name."</td>
					<td align=\"right\">".$total_visit."</td>
					<td>".$date_string."</td>
					<td align=\"right\">".$productive_call."</td>
					<td align=\"right\">".$non_productive_call."</td>
				  </tr>";
			
			$count++;
		}
		?>
        </table>
        <?php
	}
	else{
		echo "<span style=\"color:red; font-weight:bold;\">No Record Found</span>";
	}
?>
