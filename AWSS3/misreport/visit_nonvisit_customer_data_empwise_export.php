<?php
ob_start();
session_start();
require("adminUtils.php");

$employee = $_REQUEST['employee'];
$employee = str_replace("'","",$employee);
$type = $_REQUEST['type'];
$month_data = $_REQUEST['month_data'];
$emp_hierarchy = return_employee_hierarchy($employee);
$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$employee."'";
$res_emp_name = mysql_query($sql_emp_name);
$row_emp_name = mysql_fetch_array($res_emp_name);
$new_emp_name = $row_emp_name['emp_name'];


	$count = 1;
	$emp_array = array();
	$table_data='';
	//echo $type;
	if($type=='visited'){
		$header_string = ucfirst($new_emp_name).' visited customer list';
			$sql_customer = "SELECT DISTINCT CVD.emp_code, CVD.customer_code, CVD.customer_name, CVD.route_name, CVD.route_code, CM.cust_type FROM customer_visit_details CVD,customer_master CM,customer_route_emp_relation CRR WHERE CRR.customer_code=CM.customer_code AND CVD.customer_code=CRR.customer_code 
				AND CVD.emp_code IN(".$emp_hierarchy.") AND SUBSTRING(CVD.trans_id,-14,6) = '".str_replace("-","",$month_data)."' AND 
				CM.cust_type IN('Dealer','Sub Dealer','Non Star') 
				GROUP BY CM.customer_code ORDER BY CVD.emp_code, SUBSTRING(CVD.trans_id,-14,8) ASC";
		$res_customer = mysql_query($sql_customer);
	$total_customer = mysql_num_rows($res_customer);
	if($total_customer>0){
		$count = 1;
	$header ="Customer Code"."\t"."Customer Name"."\t"."Type"."\t"."Route"."\t"."No Of Visit"."\t"."Visit Dates"."\t"."Productive Call"."\t"."Non Productive Call";
		
		$res_customer = mysql_query($sql_customer);
		while($row_customer = mysql_fetch_array($res_customer)){
			$emp_code = $row_customer['emp_code'];
			$customer_code = $row_customer['customer_code'];
			$customer_name = $row_customer['customer_name'];
			$route_name = $row_customer['route_name'];
			$route_code = $row_customer['route_code'];
			$cust_type = $row_customer['cust_type'];
			
			//echo "<tr class=\"TDHEAD_SUB\"><td colspan=\"9\" align=\"center\">".$new_emp_name."</td></tr>";
			$sql_contact = "SELECT dns_customer_code FROM customer_master WHERE customer_code = '".$customer_code."'";
			$res_contact = mysql_query($sql_contact);
			$row_contact = mysql_fetch_array($res_contact);
			$dns_customer_code = $row_contact['dns_customer_code'];
			
			$productive_call = 0;
			$non_productive_call = 0;
			
			$date_array = array();
			//$customer_array_productive=array();
			//$customer_array_nonproductive=array();
			${customer_visit_unique_array.$customer_code}=array();
			$sql_transaction = "SELECT * FROM(SELECT trans_id, DATE_FORMAT(SUBSTRING(trans_id,-14,14),'%d-%m-%Y %H:%i:%s') AS date_select,DATE_FORMAT(SUBSTRING(trans_id,-14,14),'%d-%m-%Y') AS date_select_date FROM customer_visit_details WHERE emp_code IN(".$emp_hierarchy.")  AND customer_code = '".$customer_code."' 
						AND SUBSTRING(trans_id,-14,6) = '".str_replace("-","",$month_data)."'  
						ORDER BY DATE_FORMAT(SUBSTRING(trans_id,-14,14),'%Y-%m-%d %H:%i:%s') DESC) AS SAT GROUP BY 3";
			$res_transaction = mysql_query($sql_transaction);
			while($row_transaction = mysql_fetch_array($res_transaction)){
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
			
		  $table_data .= $dns_customer_code."\t".$customer_name."\t".$cust_type."\t".$route_name."\t".$total_visit."\t".$date_string."\t".$productive_call."\t".$non_productive_call."\n";
			$count++;
		}
	}
  }
  if($type=='nonvisited'){
	  $header_string = ucfirst($new_emp_name).' non visited customer list';
		$sqlcustomer="SELECT CM.dns_customer_code,CM.customer_name,CM.cust_type,
						GROUP_CONCAT(DISTINCT RM.route_name SEPARATOR ';') AS route_name,CM.branch_code
						FROM customer_master CM INNER JOIN customer_route_emp_relation CRR INNER JOIN route_master RM 
					WHERE CM.customer_code=CRR.customer_code AND CRR.route_code=RM.route_code AND 
					CRR.emp_code IN(".$emp_hierarchy.") AND 
					CM.cust_type IN('Dealer','Sub Dealer','Non Star') AND CM.customer_code NOT IN
(SELECT DISTINCT CVD.customer_code FROM customer_visit_details CVD,customer_master CM WHERE CM.customer_code=CVD.customer_code AND CVD.emp_code IN(".$emp_hierarchy.") AND CM.cust_type IN('Dealer','Sub Dealer','Non Star') AND SUBSTRING(CVD.trans_id,-14,6) = '".str_replace("-","",$_REQUEST['month_data'])."') GROUP BY CM.dns_customer_code ORDER BY CM.cust_type ASC";
			/*$sql_customer = "SELECT DISTINCT emp_code, customer_code, customer_name, route_name, route_code, cust_type FROM customer_visit_details WHERE emp_code IN('".$employee."') AND SUBSTRING(trans_id,-14,6) = '".str_replace("-","",$month_data)."' ORDER BY emp_code, SUBSTRING(trans_id,-14,8) ASC";*/
			//exit();
		$res_customer = mysql_query($sqlcustomer);
		$total_customer = mysql_num_rows($res_customer);
	  if($total_customer>0){
		$count = 1;
		$header ="Customer Code"."\t"."Customer Name"."\t"."Branch"."\t"."Route"."\t"."Category";
		while($row_customer = mysql_fetch_array($res_customer)){
			$customer_code = $row_customer['customer_code'];
			$customer_name = $row_customer['customer_name'];
			$route_name = $row_customer['route_name'];
			$branch_code = $row_customer['branch_code'];
			//$branch_name = $row_customer['branch_name'];
			$sqlbranch="SELECT branch_name FROM branch_master WHERE branch_code='".$branch_code."'";
			$rsbranch=mysql_query($sqlbranch);
			$rowbranch=mysql_fetch_array($rsbranch);
			$branch_name=$rowbranch['branch_name'];
			$cust_type = $row_customer['cust_type'];
			$dns_customer_code = $row_customer['dns_customer_code'];
		   $table_data .= $dns_customer_code."\t".$customer_name."\t".$branch_name."\t".$route_name."\t".$cust_type."\n";
			$count++;
		}
	}
  }
  	if($table_data !=''){	
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Disposition: attachment; filename=Customer_Visit_report.xls"); 
		header("Content-Transfer-Encoding: binary");
		header("Pragma: no-cache"); 
		header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 
		echo  $header_string."\n".ucwords($header)."\n".$table_data;
	}
	else
	{
		echo "<span style=\"font-weight:bold; color:red;\">No Records Found!</span>";
	}

?>
