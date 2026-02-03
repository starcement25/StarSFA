<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
if($_GET['start_date'] != '' && $_GET['end_date'] != '')
{
	$start_date = $_GET['start_date'];
	$end_date = $_GET['end_date'];
	/*$table_columnname = 'No of days present';
	$condition = " AND (SUBSTRING(LO.date,1,10) BETWEEN '".$start_date."' AND '".$end_date."') ";
	$count_transid_condition = " ,COUNT(LO.trans_id) ";
	$primary_secondary_quantity_condition = "AND  (DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."')";
	$group_by = " GROUP BY EM.emp_code ";
	$attendance_condition = " COUNT(LO.trans_id) as attendance ";
	if($start_date == $end_date){
		$table_columnname = 'Attendance';
		$condition = " AND SUBSTRING(LO.date,1,10)='".$start_date."' ";
		$attendance_condition = " SUBSTRING(LO.date,12) as attendance ";
		$primary_secondary_quantity_condition = "AND  DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%Y-%m-%d')='".$start_date."'";
	}*/
	$date_condition=" AND SUBSTRING(visit_date,1,10) >='".$start_date."' AND SUBSTRING(visit_date,1,10) <='".$end_date."'";
}
else
{
	/*$start_date = date('Y-m-d');
	$table_columnname = 'Attendance';
	$condition = " AND SUBSTRING(LO.date,1,10)='".$start_date."' ";
	$attendance_condition = " SUBSTRING(LO.date,12) as attendance ";
	$primary_secondary_quantity_condition = "AND  DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%Y-%m-%d')='".$start_date."'";*/
	$date_condition='';
}

$count = 1;
$sql_get_alltrans = "SELECT customer_code,cust_type,product_code,visit_qty,SUBSTRING(visit_date,1,10) as visit_date,order_no,SUBSTRING(order_no,2,5) AS emp_code,remarks,hint_remarks,d_instruction,rate,amount,creation_type FROM prev_order_counting_master 
	WHERE (order_no LIKE 'O%' OR  order_no LIKE 'NO%' ) ".$date_condition." ORDER BY SUBSTRING(visit_date,1,10) DESC,customer_code ASC ";
$res_get_alltrans = mysqli_query($link,$sql_get_alltrans);
$total_rows = mysqli_fetch_assoc($res_get_alltrans);

if($total_rows>0){
	echo "<table width='100%' border='1' style='border-collapse:collapse;' class='border' cellpadding='6px' align=\"center\">";
	echo "<tr class='TDHEAD'><td colspan='25' align='center'>Sales Dashboard</td></tr>";
	echo "<tr class='TDHEAD_SUB' align=\"center\">
			<td>SI</td>
			<td>Date of Reporting</td>
			<td>Cust ID</td>
			<td>Retailer Name</td>
			<td>Distributor Name</td>
			<td>Branch</td>
			<td>RSM Name</td>
			<td>SO Name</td>
			<td>LAS Name</td>			
			<td>Visit date</td>
			<td>Productive</td>
			<td>SKU</td>
			<td colspan=\"4\" align=\"center\">App</td>
			<td colspan=\"6\" align=\"center\">Callcentre</td>
			<td colspan=\"3\" align=\"center\">SFTP</td>
		  </tr>";
	echo "<tr class='TDHEAD_SUB'>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td>Qty</td>
			<td>Value</td>
			<td>Productive</td>
			<td>Feeddback</td>
			<td>Call Date</td>
			<td>Connected</td>
			<td>Qty</td>
			<td>Value</td>
			<td>Disposition</td>
			<td>Addn Remarks</td>
			<td>Trans Date</td>
			<td>Qty</td>
			<td>Value</td>
		  </tr>";
		  $customer_product_emp_date_array=array();
	while($row_get_alltrans = mysqli_fetch_assoc($res_get_alltrans)){
		$customer_code = $row_get_alltrans['customer_code'];
		$emp_code = $row_get_alltrans['emp_code'];
		$cust_type = $row_get_alltrans['cust_type'];
		$product_code = $row_get_alltrans['product_code'];
		$visit_qty = $row_get_alltrans['visit_qty'];
		$visit_date = $row_get_alltrans['visit_date'];
		$order_no = $row_get_alltrans['order_no'];
		$remarks = $row_get_alltrans['remarks'];
		$d_instruction = $row_get_alltrans['d_instruction'];
		$hint_remarks = $row_get_alltrans['hint_remarks'];
		$rate = $row_get_alltrans['rate'];
		$amount = $row_get_alltrans['amount'];
		$creation_type = $row_get_alltrans['creation_type'];
		if(substr($order_no,0,1)=='O'){
		
			$customer_product_emp_date_string=$customer_code.'#'.$product_code.'#'.$emp_code.'#'.$visit_date;
			if($creation_type=='')
			{
				${appqty.$customer_code.$product_code.$emp_code.$visit_date}=$visit_qty;
				${appvalue.$customer_code.$product_code.$emp_code.$visit_date}=$amount;
				${appfeedback.$customer_code.$product_code.$emp_code.$visit_date}=$d_instruction;
			}
			if($creation_type=='callcenter')
			{
				${callqty.$customer_code.$product_code.$emp_code.$visit_date}=$visit_qty;
				${callvalue.$customer_code.$product_code.$emp_code.$visit_date}=$amount;
				${callfeedback.$customer_code.$product_code.$emp_code.$visit_date}=$d_instruction;
				${calladdn.$customer_code.$product_code.$emp_code.$visit_date}=$remarks.'<br /><br /><br />'.$hint_remarks;
			}
			if($creation_type=='upload')
			{
				${sftpqty.$customer_code.$product_code.$emp_code.$visit_date}=$visit_qty;
				${sftpvalue.$customer_code.$product_code.$emp_code.$visit_date}=$amount;
			}
		}
		if(substr($order_no,0,1)=='N'){
			$product_code=$order_no;
			$customer_product_emp_date_string=$customer_code.'#'.$product_code.'#'.$emp_code.'#'.$visit_date;
			if($creation_type=='')
			{
				${appqty.$customer_code.$product_code.$emp_code.$visit_date}='';
				${appvalue.$customer_code.$product_code.$emp_code.$visit_date}='';
				${appfeedback.$customer_code.$product_code.$emp_code.$visit_date}=$d_instruction;
			}
			if($creation_type=='callcenter')
			{
				${callqty.$customer_code.$product_code.$emp_code.$visit_date}='';
				${callvalue.$customer_code.$product_code.$emp_code.$visit_date}='';
				${callfeedback.$customer_code.$product_code.$emp_code.$visit_date}=$d_instruction;
				${calladdn.$customer_code.$product_code.$emp_code.$visit_date}=$remarks.'<br /><br /><br />'.$hint_remarks;
			}
			if($creation_type=='upload')
			{
				${sftpqty.$customer_code.$product_code.$emp_code.$visit_date}='';
				${sftpvalue.$customer_code.$product_code.$emp_code.$visit_date}='';
			}
		}
		if(!in_array($customer_product_emp_date_string, $customer_product_emp_date_array))
		{
			array_push($customer_product_emp_date_array,$customer_product_emp_date_string);
		}
	}
		
		foreach($customer_product_emp_date_array as $customer_product_emp_date_val)
		{
			$customer_product_emp_date_val_array=explode('#',$customer_product_emp_date_val);
			$customer_code_fetched=$customer_product_emp_date_val_array[0];
			$product_code_fetched=$customer_product_emp_date_val_array[1];
			$emp_code_fetched=$customer_product_emp_date_val_array[2];
			$visit_date_fetched=$customer_product_emp_date_val_array[3];
			
			$sqlrdsname="SELECT CM.customer_name,(select customer_name FROM customer_master WHERE customer_code=CM.rds_tag) AS rds_name FROM customer_master CM WHERE 
						CM.customer_code='".$customer_code_fetched."'";
			$rsrdsname=mysqli_query($link,$sqlrdsname);
			$rowrdsname=mysqli_fetch_assoc($rsrdsname);
			$customer_name=$rowrdsname['customer_name'];
			$rds_name=$rowrdsname['rds_name'];
			
			if(substr($product_code_fetched,0,1)=='N'){
				
				$emp_code_fetched=substr($product_code_fetched,2,5);
				$sqlreportingname="SELECT EM.emp_name,(select emp_name FROM employee_master WHERE emp_code=EM.reporting_to) AS reporting_name,
								(select branch_name FROM branch_master WHERE branch_code=EM.branch_code) AS branch_name  
								FROM employee_master EM WHERE EM.emp_code='".$emp_code_fetched."'";
				
				$prod_desc='';
				$productive='NO';
			}
			else
			{
				$sqlreportingname="SELECT EM.emp_name,(select emp_name FROM employee_master WHERE emp_code=EM.reporting_to) AS reporting_name,
								(select branch_name FROM branch_master WHERE branch_code=EM.branch_code) AS branch_name  
								FROM employee_master EM WHERE EM.emp_code='".$emp_code_fetched."'";
								
				$sqlqueryprod="SELECT PM.prod_desc,PM.prod_code FROM product_master PM WHERE prod_code='".$product_code_fetched."'";
				$resultprod = mysqli_query($link,$sqlqueryprod);
				$rowprod=mysqli_fetch_assoc($resultprod);
				$prod_desc=$rowprod['prod_desc'];
				$productive='YES';
			}
			$rsreportingname=mysqli_query($link,$sqlreportingname);
			$rowreportingname=mysqli_fetch_assoc($rsreportingname);
			$reporting_name=$rowreportingname['reporting_name'];
			$emp_name=$rowreportingname['emp_name'];
			$branch_name=$rowreportingname['branch_name'];
				if(${appqty.$customer_code_fetched.$product_code_fetched.$emp_code_fetched.$visit_date_fetched} > 0 && ${appvalue.$customer_code_fetched.$product_code_fetched.$emp_code_fetched.$visit_date_fetched} >0)
				{
					${appproductive.$customer_code_fetched.$product_code_fetched.$emp_code_fetched.$visit_date_fetched}='YES';
				}
				else ${appproductive.$customer_code_fetched.$product_code_fetched.$emp_code_fetched.$visit_date_fetched}='';
				
				if(${callqty.$customer_code_fetched.$product_code_fetched.$emp_code_fetched.$visit_date_fetched} > 0 && ${callvalue.$customer_code_fetched.$product_code_fetched.$emp_code_fetched.$visit_date_fetched} >0)
				{
					${callconnected.$customer_code_fetched.$product_code_fetched.$emp_code_fetched.$visit_date_fetched}='YES';
					${calldate.$customer_code_fetched.$product_code_fetched.$emp_code_fetched.$visit_date_fetched}=date('d-m-Y',strtotime($visit_date_fetched));
				}
				else {
				${callconnected.$customer_code_fetched.$product_code_fetched.$emp_code_fetched.$visit_date_fetched}='';
				${calldate.$customer_code_fetched.$product_code_fetched.$emp_code_fetched.$visit_date_fetched}='';
				}
				if(${sftpqty.$customer_code_fetched.$product_code_fetched.$emp_code_fetched.$visit_date_fetched} > 0 && ${sftpvalue.$customer_code_fetched.$product_code_fetched.$emp_code_fetched.$visit_date_fetched} >0)
				{
				${sftptransdate.$customer_code_fetched.$product_code_fetched.$emp_code_fetched.$visit_date_fetched}=date('d-m-Y',strtotime($visit_date_fetched));
				}
				else
				{
					${sftptransdate.$customer_code_fetched.$product_code_fetched.$emp_code_fetched.$visit_date_fetched}='';
				}
		echo "<tr>
					<td>".$count."</td>
					<td >".date('d-m-Y',strtotime($visit_date_fetched))."</td>
					<td >".$customer_code_fetched."</td>
					<td >".$customer_name."</td>
					<td >".$rds_name."</td>
					<td >".$branch_name."</td>
					<td >".$reporting_name."</td>
					<td >".$emp_name."</td>
					<td ></td>
					<td >".date('d-m-Y',strtotime($visit_date_fetched))."</td>
					<td >".$productive."</td>
					<td >".$prod_desc."</td>
					<td >".${appqty.$customer_code_fetched.$product_code_fetched.$emp_code_fetched.$visit_date_fetched}."</td>
					<td >".${appvalue.$customer_code_fetched.$product_code_fetched.$emp_code_fetched.$visit_date_fetched}."</td>
					<td >".${appproductive.$customer_code_fetched.$product_code_fetched.$emp_code_fetched.$visit_date_fetched}."</td>
					<td >".${appfeedback.$customer_code_fetched.$product_code_fetched.$emp_code_fetched.$visit_date_fetched}."</td>
					<td >".${calldate.$customer_code_fetched.$product_code_fetched.$emp_code_fetched.$visit_date_fetched}."</td>
					<td >".${callconnected.$customer_code_fetched.$product_code_fetched.$emp_code_fetched.$visit_date_fetched}."</td>
					<td >".${callqty.$customer_code_fetched.$product_code_fetched.$emp_code_fetched.$visit_date_fetched}."</td>
					<td >".${callvalue.$customer_code_fetched.$product_code_fetched.$emp_code_fetched.$visit_date_fetched}."</td>
					<td >".${callfeedback.$customer_code_fetched.$product_code_fetched.$emp_code_fetched.$visit_date_fetched}."</td>
					<td >".${calladdn.$customer_code_fetched.$product_code_fetched.$emp_code_fetched.$visit_date_fetched}."</td>
					<td >".${sftptransdate.$customer_code_fetched.$product_code_fetched.$emp_code_fetched.$visit_date_fetched}."</td>
					<td >".${sftpqty.$customer_code_fetched.$product_code_fetched.$emp_code_fetched.$visit_date_fetched}."</td>
					<td >".${sftpvalue.$customer_code_fetched.$product_code_fetched.$emp_code_fetched.$visit_date_fetched}."</td>
				  </tr>";
				  
		$count++;
		}
	
	/*echo "<tr style='font-weight:bold;'>
			<td colspan='3' align='center'>Total</td>
			<td align='right'>".$total_productive_call."</td>
			<td align='right'>".$total_non_productive_call."</td>
			$table_column_data
		  </tr>";
	echo "</table>";*/
	echo "</table>";
	?>
    <br /><br />

    <p>

    <div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;

    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >

</div>

    <?php

}
else{
	echo "<table width='100%' border='1' style='border-collapse:collapse;' class='border' cellpadding='6px'>";
	echo "<tr class='TDHEAD'><td colspan='25' align='center'>Sales Dashboard</td></tr>";
	echo "<tr class='TDHEAD_SUB' align=\"center\">
	<td><font color='red'><strong>No records found</strong></font></td></tr></table>";
}
mysqli_close($link);
?>
