<?php
set_time_limit(1000);
ini_set('memory_limit', '-1');
error_reporting(E_ALL ^ E_NOTICE);
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$emp_code = $_REQUEST['emp_code'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$vertical=$_REQUEST['vertical'];


$current_date = date('Y-m-d');
$employeeval=str_replace("'","",$emp_code);
$employeevalarray=explode(",",$employeeval);
$employee_hierarchy_val='';
foreach($employeevalarray as $employeevalfinal)
{
$employee_hierarchy=return_employee_hierarchy($employeevalfinal);
$employee_hierarchy_val.="'".$employeevalfinal."'".",".$employee_hierarchy.",";
}
$employee_hierarchy_val=substr($employee_hierarchy_val,0,-1);
//echo $employee_upper_hierarchy_val;
//exit();
$employee_hierarchy_condition= " EM.emp_code IN (".$employee_hierarchy_val.")";
$verticalarray=array();
if($vertical=='all')
	{
		$sqldistinctvertical="SELECT DISTINCT vertical_value FROM product_master WHERE acedns='Y'";
		$rsdistinctvertical=mysqli_query($link,$sqldistinctvertical);
		$countdistinctvertical=mysqli_num_rows($rsdistinctvertical);
		while($rowdistinctvertical=mysqli_fetch_assoc($rsdistinctvertical))
		{
			$secondary_row.= "<td align=\"center\">$rowdistinctvertical[vertical_value]</td>";
			array_push($verticalarray,$rowdistinctvertical['vertical_value']);
		}
		$colspan_vertical=$countdistinctvertical+1;
	}
	else
	{
	 $colspan_vertical='2';
	 $vertical=str_replace("'","",$vertical);
	 $secondary_row= "<td align=\"center\">$vertical</td>";
	 array_push($verticalarray,$vertical);
	}
$table_column = "<td colspan=\"$colspan_vertical\" width=\"15%\" align=\"center\">Primary</td>
					 <td colspan=\"$colspan_vertical\" width=\"15%\" align=\"center\">Secondary</td>";
$secondary_row.="<td align=\"center\">Total</td>";
$colspan_two = '3';
$colspan = '16';
					 
$count = 1;
/*----> Total Calls & Days Present <----*/
		/*$sql_total_calls = "SELECT EM.emp_name,LO.emp_code, COUNT(CASE WHEN LO.trans_id LIKE 'A%' THEN 1 END) AS days_present FROM location LO,employee_master EM WHERE SUBSTRING(LO.trans_id,-14,4)='".$year."' AND SUBSTRING(LO.trans_id,-10,2)='".$monthvalue."' AND LO.emp_code=EM.emp_code ".$employee_select_condition." GROUP BY LO.emp_code";*/
		if(strtoupper($_SESSION['nick_name'])=='SKIPPER' || strtoupper($_SESSION['nick_name'])=='NHPL'){
			$sql_total_calls = "SELECT LO.emp_code, COUNT(CASE WHEN LO.trans_id LIKE 'A%' THEN 1 END) AS days_present,
			COUNT( DISTINCT (CASE WHEN OH.order_no LIKE 'O%' THEN CONCAT(OH.customer_code,'^',SUBSTRING(OH.order_no,-14,8)) END )) AS productive_calls, 
			COUNT( DISTINCT (CASE WHEN OH.order_no LIKE 'NO%' THEN CONCAT(OH.customer_code,'^',SUBSTRING(OH.order_no,-14,8)) END )) AS non_productive_calls, 
			COUNT( DISTINCT (CASE WHEN OD.order_no LIKE 'O%' THEN OD.sku_code END )) AS sku_count, 
			SUM(CASE WHEN POCM.order_no LIKE 'O%' AND POCM.cust_type='D' THEN POCM.amount ELSE 0 END) AS primary_amount,
	SUM(CASE WHEN POCM.order_no LIKE 'O%' AND POCM.cust_type='R' THEN POCM.amount ELSE 0 END) AS secondary_amount FROM location LO LEFT JOIN `order_header` OH ON OH.order_no=LO.trans_id LEFT JOIN order_details OD ON OD.order_no=LO.trans_id WHERE SUBSTRING(LO.trans_id,1,1) IN('A','N','O') AND SUBSTRING(LO.trans_id,-14,4)='".$year."' AND SUBSTRING(LO.trans_id,-10,2)='".$monthvalue."' ".$employee_select_condition." GROUP BY LO.emp_code";
		}
		else
		{
	$sql_total_calls = "SELECT EM.emp_code, COUNT(CASE WHEN LO.trans_id LIKE 'A%' THEN 1 END) AS days_present,COUNT( DISTINCT (CASE WHEN POCM.order_no LIKE 'O%' THEN CONCAT(POCM.customer_code,'^',SUBSTRING(POCM.order_no,-14,8)) END )) AS productive_calls, 
	COUNT( DISTINCT (CASE WHEN POCM.order_no LIKE 'NO%' THEN CONCAT(POCM.customer_code,'^',SUBSTRING(POCM.order_no,-14,8)) END )) AS non_productive_calls, 
	SUM(CASE WHEN POCM.order_no LIKE 'O%' AND POCM.cust_type='D' THEN POCM.amount ELSE 0 END) AS primary_amount,
	SUM(CASE WHEN POCM.order_no LIKE 'O%' AND POCM.cust_type='R' THEN POCM.amount ELSE 0 END) AS secondary_amount,EM.designation,EM.emp_name 
	FROM employee_master EM LEFT JOIN location LO ON LO.emp_code=EM.emp_code AND SUBSTRING(LO.trans_id,1,1) IN('A','N','O') 
	AND SUBSTRING(LO.date,1,10) BETWEEN '".$start_date."'  AND '".$end_date."' LEFT JOIN `prev_order_counting_master` POCM ON POCM.order_no=LO.trans_id 
	WHERE 
	".$employee_hierarchy_condition." GROUP BY EM.emp_code ORDER BY FIELD(EM.emp_code, $employee_hierarchy_val)";
		}
		//exit();
		$res_total_calls = mysqli_query($link,$sql_total_calls);
		$total_rows = mysqli_num_rows($res_total_calls);
		if($total_rows>0){
			?><table width='100%' class='border' border='1' style='border-collapse:collapse;' cellpadding='6px'>
             <tr class='TDHEAD'><td colspan='20' align='center'>Monthly Activity Report - From <?php echo date('d-m-Y',strtotime($start_date));?> To <?php echo date('d-m-Y',strtotime($end_date));?></td></tr>
              <tr class="TDHEAD_SUB">
                <td>SL No</td>
                <td>Emp Name</td>
                <td>Designation</td>
                <td>W.Days</td>
                <td>TC</td>
                <td>PC</td>
                <td>Strike Rate(%)</td>
                <?php echo $table_column;?><td>AVG</td>
              </tr>
              <tr class="TDHEAD_SUB">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <?php echo $secondary_row.$secondary_row;?><td>S.Sale</td>
              </tr>
            <?php
			$res_total_calls = mysqli_query($link,$sql_total_calls);
			$loop_count=0;
			while($row_total_calls = mysqli_fetch_assoc($res_total_calls)){
				$table_column_data_primary='';
			    $table_column_data_secondary='';
				$sub_total_primary_amount='';
			    $sub_total_secondary_amount='';

				$emp_code = $row_total_calls['emp_code'];
				$days_present = $row_total_calls['days_present'];
				$emp_name = $row_total_calls['emp_name'];
				$designation = $row_total_calls['designation'];
				
			    $prod_call=$row_total_calls['productive_calls'];
				$non_prod_call=$row_total_calls['non_productive_calls'];
				$total_calls = $prod_call + $non_prod_call;
				
				/*----> Productive Calls % <----*/
				$prod_call_percentage = ($prod_call/$total_calls)*100;
				$primary_amount = $row_total_calls['primary_amount'];
				$secondary_amount = $row_total_calls['secondary_amount'];
				$avg_secondary_amount=round(($secondary_amount/$days_present),2);
				//$lppc = number_format($lppc_count/$prod_call,2);
				/*$sqlstockaudit="SELECT COUNT(LO.trans_id)AS total_stk_audit FROM location LO WHERE  LO.emp_code='".$emp_code."' 
						AND (LO.trans_id LIKE 'SE%') AND SUBSTRING(LO.trans_id,-14,4)='".$year."' AND SUBSTRING(LO.trans_id,-10,2)='".$monthvalue."'";
				$rsstockaudit=mysqli_query($link,$sqlstockaudit) or die(mysqli_error()." Error in total stock audit: ".$sqlstockaudit);
				$rowstockaudit=mysqli_fetch_assoc($rsstockaudit);
				if(stk_audit=='yes' || retailer_care=='yes'){
					$stk_audit_customer_TD="<td align=\"right\" >".$rowstockaudit['total_stk_audit']."</td>";
				}*/
				if($designation=='RSM')
				{
					$rsm_emp_code=$emp_code;
				}
				
				$total_productive_call += $prod_call;
				$total_non_productive_call += $non_prod_call;
				$total_call +=$total_calls;
				$total_avg_secondary_amount +=$avg_secondary_amount;
				$total_days_present +=$days_present;
				$total_secondary_amount += $secondary_amount;
				//echo $primary_amount.'<br />';
				$total_primary_amount += $primary_amount;
				
				${total_productive_call_RSM.$rsm_emp_code} += $prod_call;
				${total_call_RSM.$rsm_emp_code} +=$total_calls;
				${total_avg_secondary_amount_RSM.$rsm_emp_code} +=$avg_secondary_amount;
				${total_days_present_RSM.$rsm_emp_code} +=$days_present;
				$strike_rate=round((($prod_call/$total_calls)*100),2);

				for($i=0;$i<2;$i++)
				{
					foreach($verticalarray as $verticalvalfinal)
					{
						$sqlverticaltotal="SELECT SUM(CASE WHEN POCM.cust_type='D' AND POCM.order_no LIKE 'O%' THEN POCM.amount ELSE 0 END ) AS primary_amount, 
											SUM(CASE WHEN POCM.cust_type='R' AND POCM.order_no LIKE 'O%' THEN POCM.amount ELSE 0 END ) AS secondary_amount
											FROM `prev_order_counting_master` POCM,product_master PM
											WHERE POCM.product_code=PM.prod_code AND SUBSTRING(POCM.visit_date,1,10) BETWEEN '".$start_date."'  AND '".$end_date."' 
											AND SUBSTRING(POCM.order_no,2,5)='".$emp_code."' AND PM.vertical_value='".$verticalvalfinal."'";
						$rsverticaltotal=mysqli_query($link,$sqlverticaltotal);
						$rowverticaltotal=mysqli_fetch_assoc($rsverticaltotal);
						${primary_amount.$verticalvalfinal}=$rowverticaltotal['primary_amount'];
						${secondary_amount.$verticalvalfinal}=$rowverticaltotal['secondary_amount'];
						if($i==0)
					    {
						 $sub_total_primary_amount=$sub_total_primary_amount+${primary_amount.$verticalvalfinal};
						 ${total_primary_amount.$verticalvalfinal}=${total_primary_amount.$verticalvalfinal}+${primary_amount.$verticalvalfinal};
						 //For RSM total
						 ${sub_total_primary_amount.$rsm_emp_code}=${sub_total_primary_amount.$rsm_emp_code}+${primary_amount.$verticalvalfinal};
						 ${total_primary_amount.$verticalvalfinal.$rsm_emp_code}=${total_primary_amount.$verticalvalfinal.$rsm_emp_code}+${primary_amount.$verticalvalfinal};
					    }
					   if($i==1)
					   {
						$sub_total_secondary_amount=$sub_total_secondary_amount+${secondary_amount.$verticalvalfinal};
						${total_secondary_amount.$verticalvalfinal}=${total_secondary_amount.$verticalvalfinal}+${secondary_amount.$verticalvalfinal};
						 //For RSM total
						${sub_total_secondary_amount.$rsm_emp_code}=${sub_total_secondary_amount.$rsm_emp_code}+${secondary_amount.$verticalvalfinal};
						${total_secondary_amount.$verticalvalfinal.$rsm_emp_code}=${total_secondary_amount.$verticalvalfinal.$rsm_emp_code}+${secondary_amount.$verticalvalfinal};
					   }
					   if($i==0)
					   {
					     $table_column_data_primary.= "<td align='right'>".number_format(${primary_amount.$verticalvalfinal},2)."</td>";
						 if(($designation=='RSM' && $count > 1) || (($loop_count+1)==$total_rows))
						 {
						 	${table_column_data_primary.$previous_rsm_emp_code}.= "<td align='right'>".number_format(${total_primary_amount.$verticalvalfinal.$previous_rsm_emp_code},2)."</td>";
						 }
					   }
					   if($i==1)
					   {
						  $table_column_data_secondary.= "<td align='right'>".number_format(${secondary_amount.$verticalvalfinal},2)."</td>";
						   if(($designation=='RSM' && $count > 1) || (($loop_count+1)==$total_rows))
						  {
						 	${table_column_data_secondary.$previous_rsm_emp_code}.= "<td align='right'>".number_format(${total_secondary_amount.$verticalvalfinal.$previous_rsm_emp_code},2)."</td>";
						  }
					   }
					}
				   if($i==0)
				   {
					 $table_column_data_primary.= "<td align='right'>".number_format($sub_total_primary_amount,2)."</td>";
					  if(($designation=='RSM' && $count > 1) || (($loop_count+1)==$total_rows))
					  {
						 ${table_column_data_primary.$previous_rsm_emp_code}.= "<td align='right'>".number_format(${sub_total_primary_amount.$previous_rsm_emp_code},2)."</td>";
					  }
				   }
				   if($i==1)
				   {
					  $table_column_data_secondary.= "<td align='right'>".number_format($sub_total_secondary_amount,2)."</td>";
					  if(($designation=='RSM' && $count > 1) || (($loop_count+1)==$total_rows))
					  {
						 ${table_column_data_secondary.$previous_rsm_emp_code}.= "<td align='right'>".number_format(${sub_total_secondary_amount.$previous_rsm_emp_code},2)."</td>";
					  }
				   }
				}
				//echo $loop_count;
				if(($designation=='RSM' && $count > 1))
				{
					
					echo "<tr style='font-weight:bold;'>
					<td align='center' colspan='3'>$emp_name_previous(RSM) Total-$previous_rsm_emp_code</td>
					<td align='right'>".${total_days_present_RSM.$previous_rsm_emp_code}."</td>
					<td align='right'>".${total_call_RSM.$previous_rsm_emp_code}."</td>
					<td align='right'>".${total_productive_call_RSM.$previous_rsm_emp_code}."</td>
					<td align='right'>".round(((${total_productive_call_RSM.$previous_rsm_emp_code}/${total_call_RSM.$previous_rsm_emp_code})*100),2)."</td>".
					${table_column_data_primary.$previous_rsm_emp_code}.${table_column_data_secondary.$previous_rsm_emp_code}.
					"<td align=\"right\" >".${total_avg_secondary_amount_RSM.$previous_rsm_emp_code}."</td>
				  </tr>";
				}
				/*if($designation=='RSM')
				{
					$rsm_emp_code=$emp_code;
				}*/
				echo "<tr>
						<td>".$count."</td>
						<td>".$emp_name."</td>
						<td>".$designation."</td>
						<td align=\"right\">".$days_present."</td>
						<td align=\"right\">".$total_calls."</td>
						<td align=\"right\">".$prod_call."</td>
						<td align=\"right\">".$strike_rate."</td>".$table_column_data_primary.$table_column_data_secondary.
						"<td align=\"right\" >".$avg_secondary_amount."</td>
					  </tr>";
				if(($loop_count+1)==$total_rows)
				{
					echo "<tr style='font-weight:bold;'>
					<td align='center' colspan='3'>$emp_name_previous(RSM) Total</td>
					<td align='right'>".${total_days_present_RSM.$previous_rsm_emp_code}."</td>
					<td align='right'>".${total_call_RSM.$previous_rsm_emp_code}."</td>
					<td align='right'>".${total_productive_call_RSM.$previous_rsm_emp_code}."</td>
					<td align='right'>".round(((${total_productive_call_RSM.$previous_rsm_emp_code}/${total_call_RSM.$previous_rsm_emp_code})*100),2)."</td>".
					${table_column_data_primary.$previous_rsm_emp_code}.${table_column_data_secondary.$previous_rsm_emp_code}.
					"<td align=\"right\" >".${total_avg_secondary_amount_RSM.$previous_rsm_emp_code}."</td>
				  </tr>";
				}	  
				if($designation=='RSM')
				{	  
					$emp_name_previous = $row_total_calls['emp_name'];
					$previous_rsm_emp_code= $row_total_calls['emp_code'];
				}
				$count++;
				$loop_count++;
			}
			for($i=0;$i<2;$i++)
			 {
				foreach($verticalarray as $verticalvalfinal)
				{
					 if($i==0)
					 {
						$table_column_data_primary_total.= "<td align='right'>".number_format(${total_primary_amount.$verticalvalfinal},2)."</td>";
					 }
					 else
					 {
						$table_column_data_secondary_total.= "<td align='right'>".number_format(${total_secondary_amount.$verticalvalfinal},2)."</td>";
					 }
				}
			  if($i==0)
			   {
				   if(count($verticalarray) > 1)
				   {
					 $table_column_data_primary_total.= "<td align='right'>".number_format($total_primary_amount,2)."</td>";
				   }
				   else $table_column_data_primary_total.= "<td align='right'>".number_format(${total_primary_amount.$vertical},2)."</td>";
			   }
			   else
			   {
				   if(count($verticalarray) > 1)
				   {
					$table_column_data_secondary_total.= "<td align='right'>".number_format($total_secondary_amount,2)."</td>";
				   }
				   else $table_column_data_secondary_total.= "<td align='right'>".number_format(${total_secondary_amount.$vertical},2)."</td>";
			   }
			 }
			echo "<tr style='font-weight:bold;'>
			<td align='center' colspan='3'>Grand Total</td>
			<td align='right'>".$total_days_present."</td>
			<td align='right'>".$total_call."</td>
			<td align='right'>".$total_productive_call."</td>
			<td align='right'>".round((($total_productive_call/$total_call)*100),2)."</td>
			$table_column_data_primary_total".$table_column_data_secondary_total.
			"<td align=\"right\" >".$total_avg_secondary_amount."</td>
		  </tr>";
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
		mysqli_close($link);
?>
