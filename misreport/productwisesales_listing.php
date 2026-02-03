<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

if(strtoupper($_SESSION['admin_login']) == "ADMIN"){
	$emp_hierarchy = "";
	$emp_hierarchy_condition = "";
}
else{
	$emp_hierarchy = return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition = " AND SUBSTRING(OH.order_no,2,5) IN (".$emp_hierarchy.") ";
}

$today = date('Ymd');
$type = $_REQUEST['type'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

if($start_date != '' && $end_date != '')
	$date_condition = " AND (SUBSTRING(OH.order_no,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ";
else
	$date_condition = " AND SUBSTRING(OH.order_no,-14,8) = '".$today."' ";

if($type == 'customerlist'){
	
	$sql_customer_list = "SELECT DISTINCT OH.customer_code, CM.customer_name FROM order_header OH, customer_master CM WHERE order_no LIKE 'O%' ".$date_condition.$emp_hierarchy_condition." AND OH.customer_code = CM.customer_code ORDER BY CM.customer_name ASC";
	$res_customer_list = mysqli_query($link,$sql_customer_list);
	$total_customer_rows = mysqli_num_rows($res_customer_list);
	if($total_customer_rows>0){
		?>
        <div id="customer_seardiv"><input type="text" name="customer_name_search" id="customer_name_search" placeholder="Find Customer" onkeyup="name_search();" /></div>
        <table>
        <?php
		$res_customer_list = mysqli_query($link,$sql_customer_list);
		while($row_customer_list = mysqli_fetch_assoc($res_customer_list)){
			$cust_code = $row_customer_list['customer_code'];
			$cust_name = $row_customer_list['customer_name'];
			echo "<tr>
					<td align=\"left\"><span class=\"search_class\" id=\"$cust_code\" style=\"color:blue; font-weight:bold; cursor:pointer;\" onclick=\"productwisesales_customerwise('".$cust_code."','".$type."');\">$cust_name</span></td>
				  </tr>";
		}
		?>
        </table>
        <?php
	}
}
else if($type == 'arealist'){
	?>
    <div id="customer_seardiv"><input type="text" name="customer_name_search" id="customer_name_search" placeholder="Find Area" onkeyup="name_search();" /></div>
    <?php
	$route_code_array = array();
	echo "<table>";
	if(tagged_distributor_for_order == 'yes'){
		$sql_distributor_code = "SELECT DISTINCT RM.route_code, RM.route_name, OH.tag_distributor_code FROM order_header OH, route_master RM, customer_master CM WHERE CM.customer_code = OH.tag_distributor_code AND CM.route_code = RM.route_code ".$emp_hierarchy_condition.$date_condition." ORDER BY RM.route_name ASC";
		$res_distributor_code = mysqli_query($link,$sql_distributor_code);
		while($row_distributor_code = mysqli_fetch_assoc($res_distributor_code)){
			$route_code = $row_distributor_code['route_code'];
			$route_name = $row_distributor_code['route_name'];
			
			if(!in_array($route_code,$route_code_array)){
				array_push($route_code_array,$route_code);
				echo "<tr>
				<td align=\"left\"><span class=\"search_class\" id=\"$route_code\" style=\"color:blue; font-weight:bold; cursor:pointer;\" onclick=\"productwisesales_customerwise('".$route_code."','".$type."');\">$route_name</span></td>
			  </tr>";
			}
			
		}
	}
	else{
		$sql_area_list = "SELECT DISTINCT RM.route_code, RM.route_name FROM route_master RM, order_header OH, customer_master CM WHERE OH.customer_code = CM.customer_code AND CM.route_code = RM.route_code".$emp_hierarchy_condition.$date_condition;
		$res_area_list = mysqli_query($link,$sql_area_list);
		while($row_area_list = mysqli_fetch_assoc($res_area_list)){
			$route_code = $row_area_list['route_code'];
			$route_name = $row_area_list['route_name'];
			
			echo "<tr>
				<td align=\"left\"><span class=\"search_class\" id=\"$route_code\" style=\"color:blue; font-weight:bold; cursor:pointer;\" onclick=\"productwisesales_customerwise('".$route_code."','".$type."');\">$route_name</span></td>
			  </tr>";
		}
	}
	echo "</table>";
}
else if($type == 'emplist'){
	?>
    <div id="customer_seardiv"><input type="text" name="customer_name_search" id="customer_name_search" placeholder="Find Employee" onkeyup="name_search();" /></div>
    <?php
	echo "<table>";
	if(tagged_distributor_for_order == 'yes'){
		$sql_emp = "SELECT DISTINCT EM.emp_code, EM.emp_name FROM employee_master EM, order_header OH WHERE SUBSTRING(OH.order_no,2,5) = EM.emp_code AND OH.order_no LIKE 'O%' ".$emp_hierarchy_condition.$date_condition." ORDER BY EM.emp_name ASC";
		$res_emp = mysqli_query($link,$sql_emp);
		while($row_emp = mysqli_fetch_assoc($res_emp)){
			$emp_code = $row_emp['emp_code'];
			$emp_name = $row_emp['emp_name'];
			$distributor_name_span = "<span id=\"dist_$emp_code\">";
			
			if(modified_customer_emp_route == 'yes'){
				$sql_distributor = "SELECT DISTINCT CM.customer_name, CM.customer_code FROM customer_master CM, customer_route_emp_relation CERR WHERE CM.customer_code = CERR.customer_code AND CERR.emp_code = '".$emp_code."' AND (CM.cust_type = 'D' OR CM.cust_type = 'Dealer') ORDER BY CM.customer_name ASC";
			}
			else{
				$sql_distributor = "SELECT DISTINCT CM.customer_name, CM.customer_code FROM customer_master CM WHERE CM.emp_code = '".$emp_code."' AND (CM.cust_type = 'D' OR CM.cust_type = 'Dealer') ORDER BY CM.customer_name ASC";
			}
			
			$res_distributor = mysqli_query($link,$sql_distributor);
			while($row_distributor = mysqli_fetch_assoc($res_distributor)){
				$distributor_code = $row_distributor['customer_code'];
				$distributor_name = $row_distributor['customer_name'];
				
				$code_concat = $emp_code."^".$distributor_code;
				
				$distributor_name_span .= "&nbsp;&nbsp;&nbsp;&nbsp;<span style=\"font-weight:bold; color:blue; cursor:pointer;\" onclick=\"productwisesales_customerwise('".$code_concat."','".$type."');\">->$distributor_name</span><br>";
			}
			$distributor_name_span .= "</span>";
			
			echo "<tr>
				<td align=\"left\"><span class=\"search_class\" id=\"$emp_code\" style=\"font-weight:bold;\" >$emp_name</span><br>".$distributor_name_span."</td>
			  </tr>";
		}
	}
	else{
		$sql_emp = "SELECT DISTINCT EM.emp_code, EM.emp_name FROM employee_master EM, order_header OH WHERE SUBSTRING(OH.order_no,2,5) = EM.emp_code AND OH.order_no LIKE 'O%' ".$emp_hierarchy_condition.$date_condition." ORDER BY EM.emp_name ASC";
		$res_emp = mysqli_query($link,$sql_emp);
		while($row_emp = mysqli_fetch_assoc($res_emp)){
			$emp_code = $row_emp['emp_code'];
			$emp_name = $row_emp['emp_name'];
		
		echo "<tr>
				<td align=\"left\"><span class=\"search_class\" id=\"$emp_code\" style=\"color:blue; font-weight:bold; cursor:pointer;\" onclick=\"productwisesales_customerwise('".$emp_code."','".$type."');\">$emp_name</span></td>
			  </tr>";
		}
	}	
	echo "</table>";
}
else if($type == 'ditributorlist'){
	?>
    <div id="customer_seardiv"><input type="text" name="customer_name_search" id="customer_name_search" placeholder="Find Distributor/Retailer" onkeyup="name_search();" /></div>
    <?php
	$primary_customer_span = "<span id=\"toggle_span_primary\" hidden>";	
	$sql_primary_customer = "SELECT DISTINCT CM.customer_code, CM.customer_name FROM customer_master CM, order_header OH WHERE (CM.customer_code = OH.customer_code OR CM.customer_code = OH.tag_distributor_code) AND (CM.cust_type = 'D' OR CM.cust_type = 'Dealer') ".$emp_hierarchy_condition.$date_condition." AND OH.order_no LIKE 'O%' ORDER BY CM.customer_name ASC";
	$res_primary_customer = mysqli_query($link,$sql_primary_customer);
	while($row_primary_customer = mysqli_fetch_assoc($res_primary_customer)){
		$primary_cust_code = $row_primary_customer['customer_code'];
		$primary_cust_name = $row_primary_customer['customer_name'];
		
		$primary_customer_span .= "&nbsp;&nbsp;<span class=\"search_class\" id=\"$primary_cust_code\" style=\"color:blue; font-weight:bold; cursor:pointer;\" onclick=\"productwisesales_customerwise('".$primary_cust_code."','".$type."');\">->$primary_cust_name</span><br>";
	}
	$primary_customer_span .= "</span>";
	
	$secondary_customer_span = "<span id=\"toggle_span_secondary\" hidden>";
	$sql_secondary_customer = "SELECT DISTINCT OH.customer_code, CM.customer_name FROM customer_master CM, order_header OH WHERE CM.customer_code = OH.customer_code AND (CM.cust_type = 'R' OR CM.cust_type = 'Sub-Dealer') AND OH.order_no LIKE 'O%' ".$emp_hierarchy_condition.$date_condition." ORDER BY CM.customer_name ASC";
	$res_secondary_customer = mysqli_query($link,$sql_secondary_customer);
	while($row_secondary_customer = mysqli_fetch_assoc($res_secondary_customer)){
		$secondary_cust_name = $row_secondary_customer['customer_name'];
		$secondary_cust_code = $row_secondary_customer['customer_code'];
		
		$secondary_customer_span .= "&nbsp;&nbsp;<span class=\"search_class\" id=\"$secondary_cust_code\" style=\"color:blue; font-weight:bold; cursor:pointer;\" onclick=\"productwisesales_customerwise('".$secondary_cust_code."','".$type."');\">->$secondary_cust_name</span><br>";
	}
	$secondary_customer_span .= "</span>";
	echo "<table>
			<tr>
				<td><span id=\"primary_span\" style=\"font-weight:bold; cursor:pointer;\" onclick=\"toggle_span('toggle_span_primary','primary_span');\">+PRIMARY</span><br>$primary_customer_span<td>
			</tr>
			<tr>
				<td><span id=\"secondary_span\" style=\"font-weight:bold; cursor:pointer;\" onclick=\"toggle_span('toggle_span_secondary','secondary_span');\">+SECONDARY</span><br>$secondary_customer_span</td>
			</tr>
		  </table>";
}
mysqli_close($link);
?>
