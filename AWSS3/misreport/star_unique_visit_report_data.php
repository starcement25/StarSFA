<?php
ob_start();
session_start();
require("adminUtils.php");

$employee = $_REQUEST['employee'];
$type = $_REQUEST['type'];
$month_data = $_REQUEST['month_data'];
/*$emp_hierarchy = return_employee_hierarchy($employee);
$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$employee."'";
$res_emp_name = mysql_query($sql_emp_name);
$row_emp_name = mysql_fetch_array($res_emp_name);
$new_emp_name = $row_emp_name['emp_name'];*/
$monthNum=substr($month_data,5,2);
$year=substr($month_data,0,4);

$monthName = date('M', mktime(0, 0, 0, $monthNum, 10));
$yearstring=$monthName.'-'.$year;
	$sql_customer = "SELECT DISTINCT CVD.emp_code, CVD.customer_code, CVD.customer_name, CVD.route_name, CVD.route_code, CM.cust_type,
					EM.emp_name,BM.branch_name,CM.dns_customer_code
					FROM customer_visit_details CVD,customer_master CM,employee_master EM,branch_master BM WHERE CVD.customer_code=CM.customer_code
					AND CM.branch_code=BM.branch_code AND CVD.emp_code=EM.emp_code 
					AND CVD.emp_code IN(".$employee.") AND SUBSTRING(CVD.trans_id,-14,6) = '".str_replace("-","",$month_data)."' AND CVD.route_code!='RT/29674'
						ORDER BY EM.emp_name ASC,CM.customer_name ASC";
	$res_customer = mysql_query($sql_customer);
	$total_customer = mysql_num_rows($res_customer);
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
			<td>MONTH OF VISIT</td>
            <td>Customer Code</td>
			<td>Customer Name</td>
            <td>Route</td>
			<td>Type</td>
			<td>Branch</td>
			<td>Employee Name</td>
		  </tr>
		<?php
		$res_customer = mysql_query($sql_customer);
		while($row_customer = mysql_fetch_array($res_customer)){
			$emp_code = $row_customer['emp_code'];
			$customer_code = $row_customer['customer_code'];
			$customer_name = $row_customer['customer_name'];
			$route_name = $row_customer['route_name'];
			$route_code = $row_customer['route_code'];
			$cust_type = $row_customer['cust_type'];
			$branch_name = $row_customer['branch_name'];
			$emp_name = $row_customer['emp_name'];
			$dns_customer_code = $row_customer['dns_customer_code'];
			echo "<tr>
					<td>".$yearstring."</td>
					<td>".$dns_customer_code."</td>
					<td>".$customer_name."</td>
					<td>".$route_name."</td>
					<td>".$cust_type."</td>
					<td>".$branch_name."</td>
					<td>".$emp_name."</td>
				  </tr>";
			
			$count++;
		}
		?>
        </table>
        <br />
    <br>
<div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
        <?php
	}
	else{
		echo "<span style=\"color:red; font-weight:bold;\">No Record Found</span>";
	}
?>
