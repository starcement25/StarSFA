<?php
set_time_limit(1000);
ini_set('memory_limit', '-1');
error_reporting(E_ALL ^ E_NOTICE);
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$emp_code = $_REQUEST['emp_code'];
$month = $_REQUEST['month'];
$month_year = explode("-",$month);
$monthvalue = date('m',strtotime($month));
$year = $month_year[1];

if($emp_code == 'all'){
	if(strtoupper($_SESSION['admin_login']) != "ADMIN"){
		$emp_hierarchy = return_employee_hierarchy($_SESSION['admin_login']);
		$order_check_condition = " AND SUBSTRING(OH.order_no,2,5) IN (".$emp_hierarchy.") ";
		$employee_select_condition = " AND LO.emp_code IN (".$emp_hierarchy.") ";
	}
	else{
		$order_check_condition = "";
		$employee_select_condition = "";
	}
	$order_condition = " AND SUBSTRING(OH.order_no,-14,4)='".$year."' AND SUBSTRING(OH.order_no,-10,2)='".$monthvalue."' ";
}
else{
	/*$sql_empname = "SELECT emp_name,emp_code,state,designation FROM employee_master WHERE emp_code='".$emp_code."'";
	$res_empname = mysql_query($sql_empname);
	$row_empname = mysql_fetch_array($res_empname);
	$emp_name = $row_empname['emp_name'];
	$emp_code = $row_empname['emp_code'];
	$state = $row_empname['state'];
	$designation = $row_empname['designation'];*/
	
	$emp_code = $_REQUEST['emp_code'];
	$emp_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=" AND EM.emp_code IN (".$emp_hierarchy.") AND EM.acedns!='N' ";
	
	$order_condition = " AND SUBSTRING(OH.order_no,-14,4)='".$year."' AND SUBSTRING(OH.order_no,-10,2)='".$monthvalue."' ";
	$order_check_condition = " AND SUBSTRING(OH.order_no,2,5)=EM.emp_code ";
	
	$employee_select_condition = " AND LO.emp_code = '".$emp_code."' ";
}

$current_date = date('Y-m-d');
$datecondition = " AND SUBSTRING(LO.date,1,10)='".$current_date."' ";
$primary_secondary_quantity_condition = " AND SUBSTRING(order_no,-14,8) = '".str_replace("-","",$current_date)."' ";

$count = 1;
/*----> Total Calls & Days Present <----*/
		/*$sql_total_calls = "SELECT EM.emp_name,LO.emp_code, COUNT(CASE WHEN LO.trans_id LIKE 'A%' THEN 1 END) AS days_present FROM location LO,employee_master EM WHERE SUBSTRING(LO.trans_id,-14,4)='".$year."' AND SUBSTRING(LO.trans_id,-10,2)='".$monthvalue."' AND LO.emp_code=EM.emp_code ".$employee_select_condition." GROUP BY LO.emp_code";*/
		if(strtoupper($_SESSION['nick_name'])=='SKIPPER' || strtoupper($_SESSION['nick_name'])=='NHPL' || strtoupper($_SESSION['nick_name'])=='PRAKASH'){
			$sql_total_calls = "SELECT LO.emp_code, COUNT(CASE WHEN LO.trans_id LIKE 'A%' THEN 1 END) AS days_present,COUNT( DISTINCT (CASE WHEN OH.order_no LIKE 'O%' THEN CONCAT(OH.customer_code,'^',SUBSTRING(OH.order_no,-14,8)) END )) AS productive_calls, COUNT( DISTINCT (CASE WHEN OH.order_no LIKE 'NO%' THEN CONCAT(OH.customer_code,'^',SUBSTRING(OH.order_no,-14,8)) END )) AS non_productive_calls, COUNT( DISTINCT (CASE WHEN OD.order_no LIKE 'O%' THEN OD.sku_code END )) AS sku_count, SUM(CASE WHEN OD.order_no LIKE 'O%' THEN OD.amount ELSE 0 END) AS total_amount FROM location LO LEFT JOIN `order_header` OH ON OH.order_no=LO.trans_id LEFT JOIN order_details OD ON OD.order_no=LO.trans_id WHERE SUBSTRING(LO.trans_id,1,1) IN('A','N','O') AND SUBSTRING(LO.trans_id,-14,4)='".$year."' AND SUBSTRING(LO.trans_id,-10,2)='".$monthvalue."' ".$employee_select_condition." GROUP BY LO.emp_code";
		}
		else
		{
			//For transaction type Telephonic excluding
	/*$sql_total_calls = "SELECT LO.emp_code, COUNT(CASE WHEN LO.trans_id LIKE 'A%' THEN 1 END) AS days_present,COUNT( DISTINCT (CASE WHEN POCM.order_no LIKE 'O%' THEN CONCAT(POCM.customer_code,'^',SUBSTRING(POCM.order_no,-14,8)) END )) AS productive_calls, COUNT( DISTINCT (CASE WHEN POCM.order_no LIKE 'NO%' THEN CONCAT(POCM.customer_code,'^',SUBSTRING(POCM.order_no,-14,8)) END )) AS non_productive_calls, COUNT( DISTINCT (CASE WHEN POCM.order_no LIKE 'O%' THEN POCM.product_code END )) AS sku_count, 
	SUM(CASE WHEN POCM.order_no LIKE 'O%' THEN POCM.amount ELSE 0 END) AS total_amount FROM location LO LEFT JOIN `prev_order_counting_master` POCM 
	ON POCM.order_no=LO.trans_id LEFT JOIN order_header OH ON POCM.order_no=OH.order_no WHERE SUBSTRING(LO.trans_id,1,1) IN('A','N','O') AND SUBSTRING(LO.trans_id,-14,4)='".$year."' AND 
	SUBSTRING(LO.trans_id,-10,2)='".$monthvalue."' ".$employee_select_condition." AND OH.transaction_type!='TO' GROUP BY LO.emp_code";*/
	
		$sql_total_calls = "SELECT LO.emp_code, COUNT(CASE WHEN LO.trans_id LIKE 'A%' THEN 1 END) AS days_present,COUNT( DISTINCT (CASE WHEN POCM.order_no LIKE 'O%' THEN CONCAT(POCM.customer_code,'^',SUBSTRING(POCM.order_no,-14,8)) END )) AS productive_calls, COUNT( DISTINCT (CASE WHEN POCM.order_no LIKE 'NO%' THEN CONCAT(POCM.customer_code,'^',SUBSTRING(POCM.order_no,-14,8)) END )) AS non_productive_calls, COUNT( DISTINCT (CASE WHEN POCM.order_no LIKE 'O%' THEN POCM.product_code END )) AS sku_count, 
	SUM(CASE WHEN POCM.order_no LIKE 'O%' THEN POCM.amount ELSE 0 END) AS total_amount FROM location LO LEFT JOIN `prev_order_counting_master` POCM 
	ON POCM.order_no=LO.trans_id  WHERE SUBSTRING(LO.trans_id,1,1) IN('A','N','O') AND SUBSTRING(LO.trans_id,-14,4)='".$year."' AND 
	SUBSTRING(LO.trans_id,-10,2)='".$monthvalue."' ".$employee_select_condition."  GROUP BY LO.emp_code";

		}
		//exit();
		$res_total_calls = mysql_query($sql_total_calls);
		$total_rows = mysql_num_rows($res_total_calls);
		if($total_rows>0){
			if(stk_audit=='yes' || retailer_care=='yes'){
				$stk_audit_customer_TR='<td>No of<br /> Stock Audit</td>';
			}
			else
			{
				$stk_audit_customer_TR='';
				$stk_audit_customer_TD='';
			}
			?><table width='100%' class='border' border='1' style='border-collapse:collapse;' cellpadding='6px'>
              <tr class="TDHEAD">
                <td>SL No</td>
                <td>Emp Code</td>
                <td>Emp Name</td>
                <td>Designation</td>
                <td>State</td>
                <td>Days Present</td>
                <td>Total Calls</td>
                <td>Productive <br />Calls</td>
                <td>Productive <br />Calls %</td>
                <td>LPPC</td>
                <td>Value</td><?php echo $stk_audit_customer_TR;?>
              </tr>
            <?php
			$res_total_calls = mysql_query($sql_total_calls);
			while($row_total_calls = mysql_fetch_array($res_total_calls)){
				$emp_code = $row_total_calls['emp_code'];
				$days_present = $row_total_calls['days_present'];
				
				/*$sql_call_details="SELECT COUNT( DISTINCT (CASE WHEN order_no LIKE 'O%' THEN CONCAT(customer_code,'^',SUBSTRING(order_no,-14,8)) END )) 
									AS productive_calls,
									COUNT( DISTINCT (CASE WHEN order_no LIKE 'NO%' THEN CONCAT(customer_code,'^',SUBSTRING(order_no,-14,8)) END )) 
									AS non_productive_calls,
									COUNT( DISTINCT (CASE WHEN order_no LIKE 'O%' THEN product_code END )) AS sku_count,
									SUM(CASE WHEN order_no LIKE 'O%' THEN amount ELSE 0 END) 
									AS total_amount
									FROM `prev_order_counting_master`
									WHERE 
									SUBSTRING(order_no,-19,5) = '".$emp_code."'AND SUBSTRING(order_no,-14,6)='".$year.$monthvalue."'";*/
				/*$sql_prod_call = "SELECT COUNT( DISTINCT CONCAT(customer_code,'^',SUBSTRING(order_no,-14,8)) ) AS productive_calls,COUNT(DISTINCT product_code) AS sku_count, SUM(amount) FROM `prev_order_counting_master` WHERE SUBSTRING(order_no,-19,5) = '".$emp_code."' AND order_no LIKE 'O%' AND SUBSTRING(order_no,-14,6)='".$year.$monthvalue."'";
				$res_prod_call = mysql_query($sql_prod_call);
				$row_prod_call = mysql_fetch_array($res_prod_call);
				$prod_call = $row_prod_call['productive_calls'];
				
				$sql_nonprod_call = "SELECT COUNT( DISTINCT CONCAT(customer_code,'^',SUBSTRING(order_no,-14,8)) ) AS non_productive_calls FROM `prev_order_counting_master` WHERE SUBSTRING(order_no,-19,5) = '".$emp_code."' AND order_no LIKE 'NO%' AND SUBSTRING(order_no,-14,6)='".$year.$monthvalue."'";
				$res_nonprod_call = mysql_query($sql_nonprod_call);
				$row_nonprod_call = mysql_fetch_array($res_nonprod_call);
				$non_prod_call = $row_nonprod_call['non_productive_calls'];*/
				
				$sql_emp_name = "SELECT emp_name,state,designation FROM employee_master WHERE emp_code = '".$emp_code."'";
				$res_emp_name = mysql_query($sql_emp_name);
				$row_emp_name = mysql_fetch_array($res_emp_name);
				$emp_name = $row_emp_name['emp_name'];
				$state = $row_emp_name['state'];
				$designation = $row_emp_name['designation'];
				//$rs_call_details=mysql_query($sql_call_details);
				//$row_call_details=mysql_fetch_array($rs_call_details);
				$prod_call=$row_total_calls['productive_calls'];
				$non_prod_call=$row_total_calls['non_productive_calls'];
				$total_calls = $prod_call + $non_prod_call;
				
				/*----> Productive Calls % <----*/
				$prod_call_percentage = ($prod_call/$total_calls)*100;
				/*---------------------------------> Count of LPPC <--------------------------------
				$lppc_count = 0;
				$lppc_array = array();
				$tot_amount = '';
				
				$sql_LPPC = "SELECT sku_code, amount FROM order_details OD WHERE SUBSTRING(order_no,2,5) = '".$emp_code."' AND SUBSTRING(order_no,-14,4)='".$year."' AND SUBSTRING(order_no,-10,2)='".$monthvalue."'";
				$res_LPPC = mysql_query($sql_LPPC);
				while($row_LPPC = mysql_fetch_array($res_LPPC)){
					$sku_code = $row_LPPC['sku_code'];
					$amount = $row_LPPC['amount'];
					$tot_amount += $amount;
					$lppc_array[$sku_code] = 1;
				}
				$lppc_count = count($lppc_array);
				$lppc = number_format($lppc_count/$prod_call,2);
				$total_lppc += $lppc;*/
				
				
				/*$sql_LPPC = "SELECT COUNT(DISTINCT POCM.product_code) AS sku_count, SUM(amount) FROM prev_order_counting_master POCM WHERE SUBSTRING(POCM.order_no,2,5) = '".$emp_code."' AND SUBSTRING(POCM.order_no,-14,6)='".$year.$monthvalue."'";
				$res_LPPC = mysql_query($sql_LPPC);
				$row_LPPC = mysql_fetch_array($res_LPPC);
				$lppc_count = $row_LPPC['sku_count'];
				$tot_amount = $row_LPPC['SUM(amount)'];
				$lppc = number_format($lppc_count/$prod_call,2);
				$lppc_count = $row_prod_call['sku_count'];
				$tot_amount = $row_prod_call['SUM(amount)'];*/
				$lppc_count = $row_total_calls['sku_count'];
				$tot_amount = $row_total_calls['total_amount'];
				$lppc = number_format($lppc_count/$prod_call,2);
				$sqlstockaudit="SELECT COUNT(LO.trans_id)AS total_stk_audit FROM location LO WHERE  LO.emp_code='".$emp_code."' 
						AND (LO.trans_id LIKE 'SE%') AND SUBSTRING(LO.trans_id,-14,4)='".$year."' AND SUBSTRING(LO.trans_id,-10,2)='".$monthvalue."'";
				$rsstockaudit=mysql_query($sqlstockaudit) or die(mysql_error()." Error in total stock audit: ".$sqlstockaudit);
				$rowstockaudit=mysql_fetch_array($rsstockaudit);
				if(stk_audit=='yes' || retailer_care=='yes'){
					$stk_audit_customer_TD="<td align=\"right\" >".$rowstockaudit['total_stk_audit']."</td>";
				}
				echo "<tr>
						<td>".$count."</td>
						<td>".$emp_code."</td>
						<td>".$emp_name."</td>
						<td>".$designation."</td>
						<td>".$state."</td>
						<td align=\"right\">".$days_present."</td>
						<td align=\"right\">".$total_calls."</td>
						<td align=\"right\">".$prod_call."</td>
						<td align=\"right\">".number_format($prod_call_percentage,2)."</td>
						<td align=\"right\">".$lppc."</td>
						<td align=\"right\">".number_format($tot_amount,2)."</td>".$stk_audit_customer_TD."
					  </tr>";
				$count++;
			}
			?>
            </table>
<br>
<div style="width:70%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
       <?php		
		}
		else{
			echo "<div style=\"font-weight:bold; color:red;\">No Records Found</div>";
		}
		mysql_close($link);
?>
