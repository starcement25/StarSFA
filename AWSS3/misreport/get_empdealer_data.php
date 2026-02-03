<?php
ob_start();
session_start();
require("adminUtils.php");

/*--------> Employee Hierarchy Condition <--------*/

$employee = $_REQUEST['employee'];
$type=$_REQUEST['type'];

/*if($_SESSION['admin_login']=="admin"){
	$emp_hierarchy = '';
	$emp_hierarchy_condition = '';
	$branch_condition = " WHERE branch_code != '' ";
	$sale_access_condition = " WHERE sale_access != '' ";
	$hq_condition = " WHERE hq != '' ";
	$designation_condition = " WHERE designation != '' ";
}
else{*/
	$emp_hierarchy=return_employee_hierarchy(str_replace("'","",$employee));
	$emp_hierarchy_condition = " WHERE emp_code IN(".$emp_hierarchy.") ";
	$emp_hierarchy_condition_one = " AND emp_code IN(".$emp_hierarchy.") ";
//}

if($type=='stockanalysis')
{
	$month=$_REQUEST['month'];
	$month_year = explode("-",$month);
	$monthvalue = date('m',strtotime($month_year[0]));
	$year = $month_year[1];
	$current_month_first_date=$year.'-'.$monthvalue.'-01';
	$previous_month_first_date=date('Y-m-d',strtotime($current_month_first_date.' -1 MONTH'));
	$previous_month=substr($previous_month_first_date,5,2);
	$previous_year=substr($previous_month_first_date,0,4);
	
	$sql_dealer = "SELECT DISTINCT CM.customer_code,CM.customer_name  FROM customer_master CM,customer_route_emp_relation CRER WHERE 
	CRER.customer_code=CM.customer_code AND CM.cust_type <>'R' AND CRER.acedns='Y' AND CRER.emp_code  IN(".$emp_hierarchy.") ORDER BY CM.customer_name ASC"; 
	//$sql_dealer = "SELECT DISTINCT CM.rds_tag  FROM customer_master CM, yellow_card_details YCD WHERE YCD.customer_code = CM.customer_code ";
	$res_dealer = mysql_query($sql_dealer);
	$total_rows = mysql_num_rows($res_dealer);
	if($total_rows>0){
		$dealer_select_control = "<select name=\"dealercontrol\" id=\"dealercontrol\" >";
		$dealer_select_control .= "<option value=\"\">Select</option>";

		$res_dealer = mysql_query($sql_dealer);
		while($row_dealer = mysql_fetch_array($res_dealer)){
			$dealer_code = $row_dealer['customer_code'];
			$dealername=$row_dealer['customer_name'];
			if($dealername !=''){
				$sqllaststkaudit="SELECT DATE_FORMAT(SUBSTRING(transaction_id,-14,14),'%Y-%m-%d %H:%i:%s') AS last_stock_audit_date 
				 				FROM stock_audit WHERE customer_code='".$dealer_code."' AND 
				 				SUBSTRING(transaction_id,-14,4)='".$year."' AND SUBSTRING(transaction_id,-10,2)='".$monthvalue."' 
								ORDER BY DATE_FORMAT(SUBSTRING(transaction_id,-14,14),'%Y-%m-%d %H:%i:%s') DESC LIMIT 0,1";
				$rslaststkaudit=mysql_query($sqllaststkaudit);	
				$last_stock_audit_cnt=mysql_num_rows($rslaststkaudit);
				$sqllaststkauditprevmonth="SELECT DATE_FORMAT(SUBSTRING(transaction_id,-14,14),'%Y-%m-%d %H:%i:%s') AS last_stock_audit_date_prev_month 
										FROM stock_audit WHERE customer_code='".$dealer_code."' AND 
										SUBSTRING(transaction_id,-14,4)='".$previous_year."' AND SUBSTRING(transaction_id,-10,2)='".$previous_month."' 
										ORDER BY DATE_FORMAT(SUBSTRING(transaction_id,-14,14),'%Y-%m-%d %H:%i:%s') DESC LIMIT 0,1";
				$rslaststkauditprevmonth=mysql_query($sqllaststkauditprevmonth);	
				$last_stock_audit_prev_month_cnt=mysql_num_rows($rslaststkauditprevmonth);
				
				$sql_purchase = "SELECT PD.invoice_no FROM purchase_details PD WHERE 
					PD.distributor_code='".$dealer_code."' AND SUBSTRING(PD.invoice_date,-10,4)='".$year."' 
					AND SUBSTRING(PD.invoice_date,-5,2)='".$monthvalue."' ORDER BY PD.invoice_date ASC";
				$rs_purchase=mysql_query($sql_purchase);
				$purchase_cnt=mysql_num_rows($rs_purchase);
						
				$sql_last_month_purchase = "SELECT PD.qty,PD.invoice_date  FROM purchase_details PD
						WHERE PD.distributor_code='".$dealer_code."' AND SUBSTRING(PD.invoice_date,-10,4)='".$previous_year."' 
						AND SUBSTRING(PD.invoice_date,-5,2)='".$previous_month."'  GROUP BY PD.prod_code";
				$rs_last_month_purchase=mysql_query($sql_last_month_purchase);	
				$prev_month_purchase_cnt=mysql_num_rows($rs_last_month_purchase);
				
				if($last_stock_audit_cnt > 0 || $last_stock_audit_prev_month_cnt > 0 || $purchase_cnt > 0 || $prev_month_purchase_cnt >0){
				$dealer_code_string .= "'".$dealer_code."',";
				$dealer_select_control_option .= "<option value=\"'".$dealer_code."'\">".$dealername."</option>";
					}
				}
			}
			if($dealer_select_control_option!=''){
			$dealer_code_string = rtrim($dealer_code_string,",");
			//$dealer_select_control .= "<option value=\"".$dealer_code_string."\">All</option>";
			$dealer_select_control .= $dealer_select_control_option;
			$dealer_select_control .= "</select>";
			}
			else $dealer_select_control="No dealer found";
			echo $dealer_select_control;
		}
	else{
		echo "No dealer found";
	}	
}
else
{
$dealer_select_control = "<select name=\"dealercontrol\" id=\"dealercontrol\" >";
$dealer_select_control .= "<option value=\"\">Select</option>";

$sql_dealer = "SELECT DISTINCT CM.customer_code,CM.customer_name  FROM customer_master CM,customer_route_emp_relation CRER WHERE CRER.customer_code=CM.customer_code AND CM.cust_type='D' AND CRER.acedns='Y' AND CRER.emp_code  IN(".$emp_hierarchy.") ORDER BY CM.customer_name ASC"; 
//$sql_dealer = "SELECT DISTINCT CM.rds_tag  FROM customer_master CM, yellow_card_details YCD WHERE YCD.customer_code = CM.customer_code ";
$res_dealer = mysql_query($sql_dealer);
$total_rows = mysql_num_rows($res_dealer);
if($total_rows>0){
	$res_dealer = mysql_query($sql_dealer);
	while($row_dealer = mysql_fetch_array($res_dealer)){
		$dealer_code = $row_dealer['customer_code'];
		$dealername=$row_dealer['customer_name'];
		if($dealername !=''){
			$dealer_code_string .= "'".$dealer_code."',";
			$dealer_select_control_option .= "<option value=\"'".$dealer_code."'\">".$dealername."</option>";
		}
	}
	$dealer_code_string = rtrim($dealer_code_string,",");
	//$dealer_select_control .= "<option value=\"".$dealer_code_string."\">All</option>";
	$dealer_select_control .= $dealer_select_control_option;
	$dealer_select_control .= "</select>";
	echo $dealer_select_control;
}
else{
	echo "No dealer found";
}
}
?>
