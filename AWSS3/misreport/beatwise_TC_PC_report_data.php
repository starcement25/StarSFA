<?php
ob_start();
session_start();
require("adminUtils.php");

$employee_id = $_REQUEST['employee_id'];
$route = $_REQUEST['route'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$date_array = array();
/*if($employee_id=='all')
{
if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy_value_condition = '';
	}
	else{
		$emp_hierarchy_value=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_value_condition = " AND SUBSTRING(POCM.order_no,-19,5) IN(".$emp_hierarchy_value.")";
	}
}
else
{
	$emp_hierarchy_value_condition = " AND SUBSTRING(POCM.order_no,-19,5)='".$employee_id."'";
}*/
if($route=='all')
{
$sqlqueryemproute="SELECT DISTINCT RM.route_code,RM.route_name FROM customer_route_emp_relation CM,route_master RM 
							WHERE CM.route_code=RM.route_code AND RM.route_name!='' AND CM.acedns='Y' AND 
								CM.emp_code IN(".$employee_id.") ORDER BY RM.route_name ASC";
    $resultqueryemproute = mysql_query($sqlqueryemproute);
	$countqueryemproute=mysql_num_rows($resultqueryemproute);
	if($countqueryemproute>0){
		while($rowqueryemproute = mysql_fetch_array($resultqueryemproute))
		{
			$route_name=$rowqueryemproute['route_name'];
			$route_code=$rowqueryemproute['route_code'];
			$route_code_string .= "'".$route_code."',";
		}
		$route_code_string = rtrim($route_code_string,",");
	}
}
else $route_code_string =$route;

/*---------------------------------> Count of productive call <--------------------------------*/
		$sql_productive_call = "SELECT COUNT(OH.order_no),SUBSTRING(OH.order_no,-19,5) AS emp_code,CM.route_code  FROM order_header OH, customer_master CM WHERE OH.customer_code=CM.customer_code 
								AND OH.order_no LIKE 'O%' AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."' GROUP BY SUBSTRING(OH.order_no,-19,5),CM.route_code ORDER BY SUBSTRING(OH.order_no,-19,5) ASC";
		$res_productive_call = mysql_query($sql_productive_call);
		while($row_productive_call = mysql_fetch_array($res_productive_call))
		{
			$emp_code_productive=$row_productive_call['emp_code'];
			$route_code_productive=$row_productive_call['route_code'];
			${'productive_call'.$emp_code_productive.$route_code_productive}=$row_productive_call['COUNT(OH.order_no)'];
		}
		
	/*---------------------------------> Count of non-productive call <--------------------------------*/
		$sql_non_productive_call = "SELECT COUNT(OH.order_no),SUBSTRING(OH.order_no,-19,5) AS emp_code,CM.route_code  FROM order_header OH, customer_master CM WHERE OH.customer_code=CM.customer_code 
									AND OH.order_no LIKE 'NO%' AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."' GROUP BY SUBSTRING(OH.order_no,-19,5),CM.route_code ORDER BY SUBSTRING(OH.order_no,-19,5) ASC";
		$res_non_productive_call = mysql_query($sql_non_productive_call);
		while($row_non_productive_call = mysql_fetch_array($res_non_productive_call))
		{
			$emp_code_non_productive=$row_non_productive_call['emp_code'];
			$route_code_non_productive=$row_non_productive_call['route_code'];
			${'non_productive_call'.$emp_code_non_productive.$route_code_non_productive}=$row_non_productive_call['COUNT(OH.order_no)'];
		}
		
			
		$sql_order_value = "SELECT SUM(OD.qty) AS order_value,SUM(OD.amount) as tot_amount,SUBSTRING(OH.order_no,-19,5) AS emp_code,CM.route_code FROM order_details OD, customer_master CM, order_header OH 
							WHERE OH.order_no=OD.order_no  AND OH.customer_code=CM.customer_code 
							AND OH.order_no LIKE 'O%' AND DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."' GROUP BY SUBSTRING(OD.order_no,-19,5),CM.route_code ORDER BY SUBSTRING(OD.order_no,-19,5) ASC";
		$res_order_value = mysql_query($sql_order_value);
		while($row_order_value = mysql_fetch_array($res_order_value))
		{
			$emp_code_value=$row_order_value['emp_code'];
			$route_code_value=$row_order_value['route_code'];
			$order_value = $row_order_value['order_value'];
			$order_amount = $row_order_value['tot_amount'];
			${'order_value'.$emp_code_value.$route_code_value}=$order_value;
			${'order_amount'.$emp_code_value.$route_code_value}=$order_amount;
		}
	?>
    <table class="border" width="100%" style="border-collapse:collapse;" border="1" id="display_table">
    <tr class='TDHEAD'><td colspan='17' align='center'>Beat Wise TC PC report Details</td></tr>
      <tr class="TDHEAD_SUB">
      	<td>Employee</td>
      	<td>Beat Name</td>
        <td>Visit Date</td>
        <td>TC</td>
        <td>PC</td>
        <td>Order Amount</td>
      </tr>
    <?
	$sql_TC_PC="SELECT EM.emp_name,RM.route_name,SUBSTRING(OH.order_no,-19,5) AS emp_code,CM.route_code,GROUP_CONCAT(DISTINCT DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%d-%m-%Y') SEPARATOR ',') AS visit_date  FROM order_header OH, customer_master CM,route_master RM,employee_master EM 
				WHERE OH.customer_code=CM.customer_code AND SUBSTRING(OH.order_no,-19,5)=EM.emp_code AND CM.route_code=RM.route_code AND 
				(OH.order_no LIKE 'O%' OR OH.order_no LIKE 'NO%') AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') 
				BETWEEN '".$start_date."' AND '".$end_date."' AND CM.route_code IN(".$route_code_string.") AND SUBSTRING(OH.order_no,-19,5) IN(".$employee_id.") GROUP BY SUBSTRING(OH.order_no,-19,5),CM.route_code ORDER BY EM.emp_name ASC";
	$res_TC_PC = mysql_query($sql_TC_PC);
	$cnt_TC_PC=mysql_num_rows($res_TC_PC);
	if($cnt_TC_PC >0){
		$count=1;
	while($row_TC_PC = mysql_fetch_array($res_TC_PC)){
		
		$emp_name = $row_TC_PC['emp_name'];
		$emp_code = $row_TC_PC['emp_code'];
		$route_name = $row_TC_PC['route_name'];
		$route_code = $row_TC_PC['route_code'];
		$visit_date = $row_TC_PC['visit_date'];
		$customer_code = $row_TC_PC['customer_code'];
		$total_call=${'productive_call'.$emp_code.$route_code}+${'non_productive_call'.$emp_code.$route_code};
		echo "<tr>
				<td>".$emp_name."</td>
				<td>".$route_name."</td>
				<td>".$visit_date."</td>
				<td>".$total_call."</td>
				<td>".${'productive_call'.$emp_code.$route_code}."</td>
				<td>".number_format(${'order_amount'.$emp_code.$route_code},2)."</td></tr>";
				
		
		/*if($visit_date!='')
		{
			${'visit_date'.$emp_code.$route_code}=${'visit_date'.$emp_code.$route_code.$month}.$visit_date.",";
		}
		if($productive_date!='')
		{
			${'productive_date'.$emp_code.$customer_code.$month}=${'productive_date'.$emp_code.$customer_code.$month}.$productive_date.",";
		}*/
	}
	//echo ${'visit_qty'.'C/0000001'.'12'};
	
	/*for($i=0;$i< count($customer_code_array);$i++){
			$customer_code_val=explode('-',$customer_code_array[$i]);
			$customer_code=$customer_code_val[0];
			echo "<tr>
				<td>".$emp_name_array[$i]."</td>
				<td>".$route_name_array[$i]."</td>
				<td>".$customer_name_array[$i]."</td>
				<td>".$customer_class_array[$i]."</td>
				<td>".$customer_category_array[$i]."</td>";
				
				foreach($month_val_array as $monthvalue)
				{
				echo "<td align=\"right\">".substr(${'visit_date'.$emp_code_array[$i].$customer_code.$monthvalue},0,-1)."</td>";
				echo "<td align=\"right\">".substr(${'productive_date'.$emp_code_array[$i].$customer_code.$monthvalue},0,-1)."</td>";
				}
			  echo "</tr>";
		}
	}*/
	}
	else{
	echo "<tr>
				<td colspan='17'>No Records</td></tr>";
}
	?>
    </table>
    <br />
    <br>
<div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
//}
mysql_close($link);
?>