<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$emp_code = $_REQUEST['emp_code'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];


/*$month = $_REQUEST['month'];
$month_year = explode("-",$month);
$monthvalue = date('m',strtotime($month_year[0]));
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
	$sql_empname = "SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
	$res_empname = mysql_query($sql_empname);
	$row_empname = mysql_fetch_array($res_empname);
	$emp_name = $row_empname['emp_name'];
	
	$emp_code = $_REQUEST['emp_code'];
	$emp_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=" AND EM.emp_code IN (".$emp_hierarchy.") AND EM.acedns!='N' ";
	
	$order_condition = " AND SUBSTRING(OH.order_no,-14,4)='".$year."' AND SUBSTRING(OH.order_no,-10,2)='".$monthvalue."' ";
	$order_check_condition = " AND SUBSTRING(OH.order_no,2,5)=EM.emp_code ";
	
	$employee_select_condition = " AND LO.emp_code = '".$emp_code."' ";
}*/

$current_date = date('Y-m-d');
$datecondition = " AND SUBSTRING(LO.date,1,10)='".$current_date."' ";
$primary_secondary_quantity_condition = " AND SUBSTRING(order_no,-14,8) = '".str_replace("-","",$current_date)."' ";

$count = 1;
/*----> Total Calls & Days Present <----*/
		$sql_total_calls = "SELECT LO.emp_code, COUNT(CASE WHEN LO.trans_id LIKE 'A%' THEN 1 END) AS days_present FROM location LO WHERE LO.emp_code IN(".$emp_code.") AND (DATE_FORMAT(LO.date,'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."') GROUP BY LO.emp_code";
		$res_total_calls = mysql_query($sql_total_calls);
		$total_rows = mysql_num_rows($res_total_calls);
		if($total_rows>0){
			?><table width='100%' class='border' border='1' style='border-collapse:collapse;' cellpadding='6px'>
              <tr class="TDHEAD">
                <td>SL No</td>
                <td>Emp Name</td>
                <td>Days Present</td>
                <td>Total Calls</td>
                <td>Productive Calls</td>
                <td>Productive Calls %</td>
                <td>LPPC</td>
                <td>Value</td>
              </tr>
            <?php
			$res_total_calls = mysql_query($sql_total_calls);
			while($row_total_calls = mysql_fetch_array($res_total_calls)){
				$emp_code = $row_total_calls['emp_code'];
				$days_present = $row_total_calls['days_present'];
				
				$sql_prod_call = "SELECT COUNT( DISTINCT CONCAT(customer_code,'^',SUBSTRING(order_no,-14,8)) ) AS productive_calls FROM `prev_order_counting_master` WHERE SUBSTRING(order_no,-19,5) = '".$emp_code."' AND order_no LIKE 'O%' AND SUBSTRING(order_no,-14,6)='".$year.$monthvalue."'";
				$res_prod_call = mysql_query($sql_prod_call);
				$row_prod_call = mysql_fetch_array($res_prod_call);
				$prod_call = $row_prod_call['productive_calls'];
				
				$sql_nonprod_call = "SELECT COUNT( DISTINCT CONCAT(customer_code,'^',SUBSTRING(order_no,-14,8)) ) AS non_productive_calls FROM `prev_order_counting_master` WHERE SUBSTRING(order_no,-19,5) = '".$emp_code."' AND order_no LIKE 'NO%' AND SUBSTRING(order_no,-14,6)='".$year.$monthvalue."'";
				$res_nonprod_call = mysql_query($sql_nonprod_call);
				$row_nonprod_call = mysql_fetch_array($res_nonprod_call);
				$non_prod_call = $row_nonprod_call['non_productive_calls'];
				
				$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
				$res_emp_name = mysql_query($sql_emp_name);
				$row_emp_name = mysql_fetch_array($res_emp_name);
				$emp_name = $row_emp_name['emp_name'];
				
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
				
				
				$sql_LPPC = "SELECT COUNT(DISTINCT POCM.product_code) AS sku_count, SUM(amount) FROM prev_order_counting_master POCM WHERE SUBSTRING(POCM.order_no,2,5) = '".$emp_code."' AND SUBSTRING(POCM.order_no,-14,6)='".$year.$monthvalue."'";
				$res_LPPC = mysql_query($sql_LPPC);
				$row_LPPC = mysql_fetch_array($res_LPPC);
				$lppc_count = $row_LPPC['sku_count'];
				$tot_amount = $row_LPPC['SUM(amount)'];
				$lppc = number_format($lppc_count/$prod_call,2);
								
				echo "<tr>
						<td>".$count."</td>
						<td>".$emp_name."</td>
						<td align=\"right\">".$days_present."</td>
						<td align=\"right\">".$total_calls."</td>
						<td align=\"right\">".$prod_call."</td>
						<td align=\"right\">".number_format($prod_call_percentage,2)."</td>
						<td align=\"right\">".$lppc."</td>
						<td align=\"right\">".number_format($tot_amount,2)."</td>
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
