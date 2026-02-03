<?php
ob_start();
session_start();
require("adminUtils.php");

$employee = $_REQUEST['employee'];
$type = $_REQUEST['type'];
$month_data = $_REQUEST['month_data'];
$emp_hierarchy = return_employee_hierarchy($employee);
$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$employee."'";
$res_emp_name = mysqli_query($link,$sql_emp_name);
$row_emp_name = mysqli_fetch_assoc($res_emp_name);
$new_emp_name = $row_emp_name['emp_name'];

function customer_count($emp_code,$cust_type){
	$emp_hierarchy = return_employee_hierarchy($emp_code);
	$sql_count = "SELECT COUNT(DISTINCT CM.customer_code) AS cust_count FROM customer_master CM,customer_route_emp_relation CRR 
				WHERE CM.customer_code=CRR.customer_code AND CRR.emp_code IN(".$emp_hierarchy.")  AND CRR.acedns='Y' AND CM.cust_type = '".$cust_type."'";
	$res_count = mysqli_query($link,$sql_count);
	$row_count = mysqli_fetch_assoc($res_count);
	return $row_count['cust_count'];
}

function visit_customer_count($emp_code,$cust_type){
	$emp_hierarchy = return_employee_hierarchy($emp_code);
	$sql_count = "SELECT COUNT(DISTINCT CVD.customer_code) AS cust_count FROM customer_visit_details CVD,customer_master CM 
				WHERE CVD.customer_code=CM.customer_code AND CVD.emp_code = '".$emp_code."' AND CVD.cust_type = '".$cust_type."' 
				AND SUBSTRING(CVD.trans_id,-14,6) = '".str_replace("-","",$_REQUEST['month_data'])."'";
	$res_count = mysqli_query($link,$sql_count);
	$row_count = mysqli_fetch_assoc($res_count);
	return $row_count['cust_count'];
}

function notvisit_customer_count($emp_code,$cust_type){
	$count = 0;
	//$emp_hierarchy = return_employee_hierarchy($emp_code);
	/*$sql_customer_master = "SELECT DISTINCT customer_code FROM customer_master WHERE emp_code = '".$emp_code."' AND cust_type = '".$cust_type."' AND customer_code NOT IN(SELECT DISTINCT customer_code FROM customer_visit_details WHERE emp_code = '".$emp_code."' AND cust_type = '".$cust_type."' AND SUBSTRING(trans_id,-14,6) = '".str_replace("-","",$_REQUEST['month_data'])."')";*/
	$emp_hierarchy = return_employee_hierarchy($emp_code);
	$sql_customer_master = "SELECT DISTINCT CM.customer_code FROM customer_route_emp_relation CRR,customer_master CM WHERE CRR.emp_code IN(".$emp_hierarchy.") AND CM.cust_type = '".$cust_type."' AND CRR.customer_code=CM.customer_code AND CRR.acedns='Y' AND CM.customer_code NOT IN(SELECT DISTINCT customer_code FROM customer_visit_details WHERE emp_code = '".$emp_code."' AND cust_type = '".$cust_type."' AND SUBSTRING(trans_id,-14,6) = '".str_replace("-","",$_REQUEST['month_data'])."')";
	$res_customer_master = mysqli_query($link,$sql_customer_master);
	$row_count = mysqli_num_rows($res_customer_master);
	return $row_count;
}

	$count = 1;
	$emp_array = array();
	if($type=='visited'){
		$header_string = $new_emp_name.' visited customer list';
			$sql_customer = "SELECT DISTINCT CVD.emp_code, CVD.customer_code, CVD.customer_name, CVD.route_name, CVD.route_code, CM.cust_type 
					FROM customer_visit_details CVD,customer_master CM,customer_route_emp_relation CRR WHERE CRR.customer_code=CM.customer_code AND CVD.customer_code=CRR.customer_code 
					AND CVD.emp_code IN(".$emp_hierarchy.") AND SUBSTRING(CVD.trans_id,-14,6) = '".str_replace("-","",$month_data)."' AND 
					CM.cust_type IN('Dealer','Sub Dealer','Non Star') 
				GROUP BY CM.customer_code ORDER BY CVD.emp_code, SUBSTRING(CVD.trans_id,-14,8) ASC";
		$res_customer = mysqli_query($link,$sql_customer);
	$total_customer = mysqli_num_rows($res_customer);
	if($total_customer>0){
		$count = 1;
		?>
        <input type="hidden" id="display_table_val" value="visited" />
        <input type="hidden" id="employee_val" value="<?php echo $employee;?>" />

		<table border="1" id="display_table_visited" style="border-collapse:collapse;" class="border" width="100%">
          <tr class="TDHEAD_SUB">
          	<td colspan="9"><?php echo $header_string;  ?></td>
          </tr>
		  <tr class="TDHEAD" align="center">
			<td>SI</td>
            <td>Customer Code</td>
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
			
			//echo "<tr class=\"TDHEAD_SUB\"><td colspan=\"9\" align=\"center\">".$new_emp_name."</td></tr>";
			$sql_contact = "SELECT dns_customer_code FROM customer_master WHERE customer_code = '".$customer_code."'";
			$res_contact = mysqli_query($link,$sql_contact);
			$row_contact = mysqli_fetch_assoc($res_contact);
			$dns_customer_code = $row_contact['dns_customer_code'];
			
			$productive_call = 0;
			$non_productive_call = 0;
			
			$date_array = array();
			//$customer_array_productive=array();
			//$customer_array_nonproductive=array();
			${customer_visit_unique_array.$customer_code}=array();
			$sql_transaction = "SELECT * FROM(SELECT trans_id, DATE_FORMAT(SUBSTRING(trans_id,-14,14),'%d-%m-%Y %H:%i:%s') AS date_select,DATE_FORMAT(SUBSTRING(trans_id,-14,14),'%d-%m-%Y') AS date_select_date 
			FROM customer_visit_details WHERE emp_code IN(".$emp_hierarchy.")  AND customer_code = '".$customer_code."' 
			AND SUBSTRING(trans_id,-14,6) = '".str_replace("-","",$month_data)."'  ORDER BY DATE_FORMAT(SUBSTRING(trans_id,-14,14),'%Y-%m-%d %H:%i:%s') DESC) AS SAT GROUP BY 3";
			$res_transaction = mysqli_query($link,$sql_transaction);
			while($row_transaction = mysqli_fetch_assoc($res_transaction)){
				$trans_id = $row_transaction['trans_id'];
				$date_select = $row_transaction['date_select'];
				$date_select_date = $row_transaction['date_select_date'];
				$transid_substr = substr($trans_id,0,2);
				$transid_substr_first_string=substr($trans_id,0,1);
				if($transid_substr == "OE" || $transid_substr == "SE" || $transid_substr == "PE" ){
					$date_array[$date_select] = '1';
					//$customer_array_productive[$date_select]='1';
					$productive_call++;
				}
				else if( $transid_substr == "NS" || $transid_substr == "NO" || $transid_substr == "NC"){
					$date_array[$date_select] = '1';
					//$customer_array_nonproductive[$date_select]='1';
					$non_productive_call++;
				}
				if($transid_substr_first_string=='M'){
					$date_array[$date_select] = '1';
					//$customer_array_productive[$date_select]='1';
					$productive_call++;
				}
				if(!in_array($date_select_date,${customer_visit_unique_array.$customer_code}))
				{
					array_push(${customer_visit_unique_array.$customer_code},$date_select_date);
				}
			}
			$date_string = '';
			foreach($date_array as $key=>$val){
				$date_string .= $key.", ";
			}
			$date_string = trim($date_string);
			$date_string = rtrim($date_string,",");
			
			$total_visit = count(explode(',',$date_string));
			
			echo "<tr>
					<td>".$count."</td>
					<td>".$dns_customer_code."</td>
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
  }
  if($type=='nonvisited'){
	  $header_string = $new_emp_name.' non visited customer list';
		$sqlcustomer="SELECT CM.dns_customer_code,CM.customer_name,CM.cust_type,
						GROUP_CONCAT(DISTINCT RM.route_name SEPARATOR ';') AS route_name,CM.branch_code
						FROM customer_master CM INNER JOIN customer_route_emp_relation CRR INNER JOIN route_master RM 
					WHERE CM.customer_code=CRR.customer_code AND CRR.route_code=RM.route_code AND 
					CRR.emp_code IN(".$emp_hierarchy.") AND 
					CM.cust_type IN('Dealer','Sub Dealer','Non Star') AND CM.customer_code NOT IN
(SELECT DISTINCT CVD.customer_code FROM customer_visit_details CVD,customer_master CM WHERE CM.customer_code=CVD.customer_code AND CVD.emp_code IN(".$emp_hierarchy.") AND CM.cust_type IN('Dealer','Sub Dealer','Non Star') AND SUBSTRING(CVD.trans_id,-14,6) = '".str_replace("-","",$_REQUEST['month_data'])."') GROUP BY CM.dns_customer_code ORDER BY CM.cust_type ASC";
			/*$sql_customer = "SELECT DISTINCT emp_code, customer_code, customer_name, route_name, route_code, cust_type FROM customer_visit_details WHERE emp_code IN('".$employee."') AND SUBSTRING(trans_id,-14,6) = '".str_replace("-","",$month_data)."' ORDER BY emp_code, SUBSTRING(trans_id,-14,8) ASC";*/
			//exit();
		$res_customer = mysqli_query($link,$sqlcustomer);
		$total_customer = mysqli_num_rows($res_customer);
	  if($total_customer>0){
		$count = 1;
		?>
        <input type="hidden" id="display_table_val" value="nonvisited" />
        <input type="hidden" id="employee_val" value="<?php echo $employee;?>" />
		<table border="1" id="display_table_nonvisited" style="border-collapse:collapse;" class="border" width="100%">
          <tr class="TDHEAD_SUB">
          	<td colspan="9"><?php echo $header_string;  ?></td>
          </tr>
		  <tr class="TDHEAD" align="center">
			<td>SI</td>
            <td>Customer Code</td>
			<td>Customer Name</td>
            <td>Branch</td>
			<td>Route</td>
            <td>Category</td>
		  </tr>
		<?php
		while($row_customer = mysqli_fetch_assoc($res_customer)){
			$customer_code = $row_customer['customer_code'];
			$customer_name = $row_customer['customer_name'];
			$route_name = $row_customer['route_name'];
			$branch_code = $row_customer['branch_code'];
			//$branch_name = $row_customer['branch_name'];
			$sqlbranch="SELECT branch_name FROM branch_master WHERE branch_code='".$branch_code."'";
			$rsbranch=mysqli_query($link,$sqlbranch);
			$rowbranch=mysqli_fetch_assoc($rsbranch);
			$branch_name=$rowbranch['branch_name'];
			$cust_type = $row_customer['cust_type'];
			$dns_customer_code = $row_customer['dns_customer_code'];
			
			echo "<tr>
					<td>".$count."</td>
					<td>".$dns_customer_code."</td>
					<td>".$customer_name."</td>
					<td>".$branch_name."</td>
					<td>".$route_name."</td>
					<td >".$cust_type."</td>
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
  }
?>
