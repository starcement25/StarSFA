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
$cust_type=$_REQUEST['cust_type'];

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
$productgrouparray=array();
if($vertical=='all')
	{
		$sqldistinctproductgroup="SELECT DISTINCT PGM.product_group_name,PGM.product_group_code,PM.vertical_value FROM product_master PM,
							product_group_master PGM WHERE PM.acedns='Y' AND PM.product_group_code=PGM.product_group_code ORDER BY PGM.product_group_name ASC";
	}
	else
	{
	 //$colspan_vertical='2';
	   $vertical=str_replace("'","",$vertical);
	   $sqldistinctproductgroup="SELECT DISTINCT PGM.product_group_name,PGM.product_group_code,PM.vertical_value FROM product_master PM,
							product_group_master PGM WHERE PM.acedns='Y' AND PM.product_group_code=PGM.product_group_code 
							AND PM.vertical_value='".$vertical."' ORDER BY PGM.product_group_name ASC";
	}
	$rsdistinctproductgroup=mysqli_query($link,$sqldistinctproductgroup);
	$countdistinctproductgroup=mysqli_num_rows($rsdistinctproductgroup);
	while($rowdistinctproductgroup=mysqli_fetch_assoc($rsdistinctproductgroup))
	{
		$secondary_row.= "<td align=\"center\">$rowdistinctproductgroup[product_group_name]</td>";
		array_push($verticalarray,$rowdistinctproductgroup['vertical_value']);
		array_push($productgrouparray,$rowdistinctproductgroup['product_group_code']);
		$product_group_val.="'".$rowdistinctproductgroup['product_group_code']."'".",";
	}
	$product_group_val=substr($product_group_val,0,-1);
	$colspan_vertical=$countdistinctproductgroup+3;

$count = 1;
/*----> Total Calls & Days Present <----*/
		/*$sql_total_calls = "SELECT EM.emp_name,LO.emp_code, COUNT(CASE WHEN LO.trans_id LIKE 'A%' THEN 1 END) AS days_present FROM location LO,employee_master EM WHERE SUBSTRING(LO.trans_id,-14,4)='".$year."' AND SUBSTRING(LO.trans_id,-10,2)='".$monthvalue."' AND LO.emp_code=EM.emp_code ".$employee_select_condition." GROUP BY LO.emp_code";*/
	/*$sql_total_calls = "SELECT LO.emp_code, COUNT(CASE WHEN LO.trans_id LIKE 'A%' THEN 1 END) AS days_present,COUNT( DISTINCT (CASE WHEN POCM.order_no LIKE 'O%' THEN CONCAT(POCM.customer_code,'^',SUBSTRING(POCM.order_no,-14,8)) END )) AS productive_calls, 
	COUNT( DISTINCT (CASE WHEN POCM.order_no LIKE 'NO%' THEN CONCAT(POCM.customer_code,'^',SUBSTRING(POCM.order_no,-14,8)) END )) AS non_productive_calls, 
	SUM(CASE WHEN POCM.order_no LIKE 'O%' AND POCM.cust_type='D' THEN POCM.amount ELSE 0 END) AS primary_amount,
	SUM(CASE WHEN POCM.order_no LIKE 'O%' AND POCM.cust_type='R' THEN POCM.amount ELSE 0 END) AS secondary_amount,EM.designation,EM.emp_name 
	FROM employee_master EM LEFT JOIN location LO ON LO.emp_code=EM.emp_code AND SUBSTRING(LO.trans_id,1,1) IN('A','N','O') 
	AND SUBSTRING(LO.date,1,10) BETWEEN '".$start_date."'  AND '".$end_date."' LEFT JOIN `prev_order_counting_master` POCM ON POCM.order_no=LO.trans_id 
	WHERE 
	".$employee_hierarchy_condition." GROUP BY EM.emp_code ORDER BY FIELD(EM.emp_code, $employee_hierarchy_val)";*/
	//exit();
	$emp_code_array=array();
	if($cust_type=='primary'){
		$cust_type_condion="SUM(CASE WHEN POCM.order_no LIKE 'O%' AND POCM.cust_type='D' THEN POCM.visit_qty ELSE 0 END) AS primary_val,0 AS secondary_val ";
	}
	else if($cust_type=='secondary'){
		if(strtoupper($_SESSION['nick_name'])=='ARCHITA')
		{
		 $cust_type_condion="SUM(CASE WHEN POCM.order_no LIKE 'O%' AND POCM.cust_type='R' THEN (POCM.visit_qty/PM.conversion_factor_two) ELSE 0 END) 
						AS secondary_val,0 AS primary_val ";
		}
		else
		{
		  $cust_type_condion="SUM(CASE WHEN POCM.order_no LIKE 'O%' AND POCM.cust_type='R' THEN POCM.visit_qty ELSE 0 END) 
						AS secondary_val,0 AS primary_val ";
		}
	}
	else if($cust_type=='both')
	{
		if(strtoupper($_SESSION['nick_name'])=='ARCHITA')
		{
		$cust_type_condion="SUM(CASE WHEN POCM.order_no LIKE 'O%' AND POCM.cust_type='D' THEN POCM.visit_qty ELSE 0 END) AS primary_val,
						SUM(CASE WHEN POCM.order_no LIKE 'O%' AND POCM.cust_type='R' THEN (POCM.visit_qty/PM.conversion_factor_two) ELSE 0 END) 
						AS secondary_val";
		}
		else
		{
			$cust_type_condion="SUM(CASE WHEN POCM.order_no LIKE 'O%' AND POCM.cust_type='D' THEN POCM.visit_qty ELSE 0 END) AS primary_val,
				SUM(CASE WHEN POCM.order_no LIKE 'O%' AND POCM.cust_type='R' THEN POCM.visit_qty ELSE 0 END) 
				AS secondary_val";
		}
	}

	$sql_total_calls = "SELECT  $cust_type_condion,EM.designation,EM.emp_name,PM.product_group_code,EM.emp_code
						FROM employee_master EM LEFT JOIN `prev_order_counting_master` POCM ON SUBSTRING(POCM.order_no,2,5)=EM.emp_code 
						AND SUBSTRING(POCM.visit_date,1,10) BETWEEN '".$start_date."'  AND '".$end_date."' 
						LEFT JOIN `product_master` PM ON POCM.product_code=PM.prod_code AND PM.product_group_code IN(".$product_group_val.")
					WHERE 
					".$employee_hierarchy_condition."  
					GROUP BY EM.emp_code,PM.product_group_code ORDER BY FIELD(EM.emp_code, $employee_hierarchy_val),
					FIELD(PM.product_group_code, $product_group_val)";
		$res_total_calls = mysqli_query($link,$sql_total_calls);
		$total_rows = mysqli_num_rows($res_total_calls);
		if($total_rows>0){
			?><table width='100%' class='border' border='1' style='border-collapse:collapse;' cellpadding='6px'>
             <tr class='TDHEAD'><td colspan='30' align='center'>SKU Wise Report - From <?php echo date('d-m-Y',strtotime($start_date));?> To <?php echo date('d-m-Y',strtotime($end_date));?></td></tr>
              <tr class="TDHEAD_SUB">
                <td>SL No</td>
                <td>Emp Name</td>
                <td>Designation</td>
                <?php echo $secondary_row;?><td>Total</td>
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
				$emp_name = $row_total_calls['emp_name'];
				$designation = $row_total_calls['designation'];
				$product_group_code = $row_total_calls['product_group_code'];
				
				$primary_val = $row_total_calls['primary_val'];
				$secondary_val = $row_total_calls['secondary_val'];
				${emp_name.$emp_code}=$emp_name;
				${designation.$emp_code}=$designation;
				${primary_val.$emp_code.$product_group_code}=$primary_val;
				${secondary_val.$emp_code.$product_group_code}=$secondary_val;
				
				if(!in_array($emp_code,$emp_code_array))
				{
					array_push($emp_code_array,$emp_code);
				}
			}
			for($k=0;$k < count($emp_code_array);$k++)
			{
				$each_row_total_val='';
				if(${designation.$emp_code_array[$k]}=='RSM')
				{
					$rsm_emp_code=$emp_code_array[$k];
				}
				//For RSM total except last one
				if((${designation.$emp_code_array[$k]}=='RSM' && $count > 1))
				{
					echo "<tr style='font-weight:bold;'>
					<td align='center' colspan='3'>$emp_name_previous(RSM) Total</td>";
					foreach($productgrouparray as $productgroupcodeval)
					 {
					   echo "<td align='right'>".number_format(${each_row_val_groupwise_RSM.$previous_rsm_emp_code.$productgroupcodeval},2)."</td>";
					 }
					 echo "<td align='right'>".number_format(${each_row_total_val_groupwise_RSM.$previous_rsm_emp_code},2)."</td></tr>";				
				}
				if(substr(strtoupper(${emp_name.$emp_code_array[$k]}),0,6)=='VACANT')
				{
					$color_name='red';
				}
				else $color_name='';
				echo "<tr>
						<td>".$count."</td>
						<td style='background:$color_name'>".${emp_name.$emp_code_array[$k]}."</td>
						<td>".${designation.$emp_code_array[$k]}."</td>";
				foreach($productgrouparray as $productgroupcodeval)
				{
					$each_row_col_val = ${primary_val.$emp_code_array[$k].$productgroupcodeval}+${secondary_val.$emp_code_array[$k].$productgroupcodeval};
					$each_row_col_val=round($each_row_col_val,2);
					echo "<td align=\"right\">".number_format($each_row_col_val,2)."</td>";
					$each_row_total_val +=$each_row_col_val;
					${each_row_total_val_groupwise.$productgroupcodeval}+=$each_row_col_val;
					${each_row_val_groupwise_RSM.$rsm_emp_code.$productgroupcodeval} +=$each_row_col_val;
				}
				echo "<td align=\"right\">".number_format($each_row_total_val,2)."</td></tr>";
				${each_row_total_val_groupwise_RSM.$rsm_emp_code} +=$each_row_total_val;

				
				//For RSM total last one
				if(($loop_count+1)==count($emp_code_array))
				{
					echo "<tr style='font-weight:bold;'>
					<td align='center' colspan='3'>$emp_name_previous(RSM) Total</td>";
					foreach($productgrouparray as $productgroupcodeval)
					 {
					   echo "<td align='right'>".number_format(${each_row_val_groupwise_RSM.$previous_rsm_emp_code.$productgroupcodeval},2)."</td>";
					 }
					 echo "<td align='right'>".number_format(${each_row_total_val_groupwise_RSM.$previous_rsm_emp_code},2)."</td></tr>";
				}
				
				$each_row_grand_total_val +=$each_row_total_val;
				
				if(${designation.$emp_code_array[$k]}=='RSM')
				{	  
					$emp_name_previous = ${emp_name.$emp_code_array[$k]};
					$previous_rsm_emp_code= $emp_code_array[$k];
				}
				$count++;
				$loop_count++;
			}
			echo "<tr style='font-weight:bold;'>
			<td align='center' colspan='3'>Grand Total</td>";
			foreach($productgrouparray as $productgroupcodeval)
			 {
			echo "<td align='right'>".number_format(${each_row_total_val_groupwise.$productgroupcodeval},2)."</td>";
			 }
			echo "<td align='right'>".number_format($each_row_grand_total_val,2)."</td></tr>";

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
