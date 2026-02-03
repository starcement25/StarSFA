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
$employee_hierarchy_condition= " CRR.emp_code IN (".$employee_hierarchy_val.")";
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
	$sql_closing_stock = "SELECT CM.customer_name,CM.dns_customer_code,PD.product_group_code AS product_group_purchase,SAD.product_group_code AS product_group_audit,
							PD.total_purchase_amount,
							PD.total_purchase_qty,SUM(SAD.total_audit_qty) AS total_audit_qty
							FROM   
							(SELECT CMA.customer_name,CMA.dns_customer_code,CMA.customer_code         
       						FROM   customer_master CMA LEFT JOIN customer_route_emp_relation CRR 
        					ON CMA.customer_code=CRR.customer_code AND CMA.customer_name IS NOT NULL AND CMA.cust_type!='R' AND 
							".$employee_hierarchy_condition." GROUP  BY CRR.customer_code) CM
							LEFT JOIN 
							(SELECT Sum(PUD.amount) as total_purchase_amount,SUM(PUD.qty) as total_purchase_qty,PUD.distributor_code,PUD.invoice_date,
								PM.product_group_code 
                  			 FROM   purchase_details PUD,product_master PM where SUBSTRING(PUD.invoice_date,1,10) >='".$start_date."' 
							AND PM.prod_code=PUD.prod_code   AND PM.product_group_code IN(".$product_group_val.")
                  			GROUP  BY PUD.distributor_code,PM.product_group_code) PD
							ON CM.customer_code = PD.distributor_code  
							LEFT JOIN (SELECT Sum(SA.quantity) as total_audit_qty,SA.customer_code,PM.product_group_code FROM stock_audit SA,product_master PM 
							where 
          				 	DATE_FORMAT(SUBSTRING(SA.transaction_id,-14,8),'%Y-%m-%d') <='".$start_date."' AND PM.prod_code=SA.product_code 
							AND 
           					PM.product_group_code IN(".$product_group_val.") GROUP BY SA.customer_code,PM.product_group_code) SAD 
							ON CM.customer_code = SAD.customer_code 
 							GROUP BY CM.customer_code,PD.product_group_code,SAD.product_group_code ORDER BY CM.customer_name ASC";
		$res_closing_stock = mysqli_query($link,$sql_closing_stock);
		$total_rows = mysqli_num_rows($res_closing_stock);
		if($total_rows>0){
			?><table width='90%' class='border' border='1' style='border-collapse:collapse;' cellpadding='6px'>
             <tr class='TDHEAD'><td colspan='10' align='center'>Closing Stock Report - Date <?php echo date('d-m-Y',strtotime($start_date));?> </td></tr>
              <tr class="TDHEAD_SUB">
                <td>SL No</td>
                <td>Customer Name</td>
                <?php echo $secondary_row;?>
                <td>Total(Volume)</td>
                <!--td>Total(Value)</td-->
              </tr>
            <?php
			$res_closing_stock = mysqli_query($link,$sql_closing_stock);
			$loop_count=0;
			$customer_code_array=array();
			while($row_closing_stock = mysqli_fetch_assoc($res_closing_stock)){
				$customer_name=$row_closing_stock['customer_name'];
				$customer_code=$row_closing_stock['dns_customer_code'];
				$product_group_purchase=$row_closing_stock['product_group_purchase'];
				$product_group_audit=$row_closing_stock['product_group_audit'];
				$total_purchase_amount=$row_closing_stock['total_purchase_amount'];
				$total_purchase_qty=$row_closing_stock['total_purchase_qty'];
				$total_audit_qty=$row_closing_stock['total_audit_qty'];
				foreach($productgrouparray as $productgroupcodeval)
				{
					if($productgroupcodeval==$product_group_purchase)
					{
						${total_purchase_qty.$customer_code.$productgroupcodeval}=$total_purchase_qty;
						${total_purchase_amount.$customer_code.$productgroupcodeval}=$total_purchase_amount;
					}
					else
					{
						if(!isset(${total_purchase_qty.$customer_code.$productgroupcodeval}) || ${total_purchase_qty.$customer_code.$productgroupcodeval}==0)
						{
							${total_purchase_qty.$customer_code.$productgroupcodeval}=0;
						}
						if(!isset(${total_purchase_amount.$customer_code.$productgroupcodeval}) || ${total_purchase_amount.$customer_code.$productgroupcodeval}==0)
						{
							${total_purchase_amount.$customer_code.$productgroupcodeval}=0;
						}
					}
					if($productgroupcodeval==$product_group_audit)
					{
						${total_audit_qty.$customer_code.$productgroupcodeval}=$total_audit_qty;
						//${total_purchase_amount.$customer_code.$productgroupcodeval}=$total_purchase_amount;
					}
					else
					{
						if(!isset(${total_audit_qty.$customer_code.$productgroupcodeval}) || ${total_audit_qty.$customer_code.$productgroupcodeval}==0)
						{
							${total_audit_qty.$customer_code.$productgroupcodeval}=0;
						}
						/*if(!isset(${total_purchase_amount.$customer_code.$productgroupcodeval}) || ${total_purchase_amount.$customer_code.$productgroupcodeval}==0)
						{
							${total_purchase_amount.$customer_code.$productgroupcodeval}=0;
						}*/
					}
					${closingstockval.$customer_code.$productgroupcodeval}=${total_purchase_qty.$customer_code.$productgroupcodeval}+${total_audit_qty.$customer_code.$productgroupcodeval};
				}
				if(!in_array($customer_code,$customer_code_array))
				{
					array_push($customer_code_array,$customer_code);
					${customer_name.$customer_code}=$customer_name;
				}
				/*echo "<tr>
						<td>".$count."</td>
						<td >".$customer_name."</td>";
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
				$count++;
				$loop_count++;*/
			}
			foreach($customer_code_array as $customer_code_val)
			{
				echo "<tr>
						<td>".$count."</td>
						<td >".${customer_name.$customer_code_val}."</td>";
				foreach($productgrouparray as $productgroupcodeval)
				{
					if(${closingstockval.$customer_code_val.$productgroupcodeval}==0)
					{
						echo "<td align=\"right\">--</td>";
					}
					else
					{
					  echo "<td align=\"right\">".number_format(${closingstockval.$customer_code_val.$productgroupcodeval},2)."</td>";
					}
					${each_row_total_val.$customer_code_val} +=${closingstockval.$customer_code_val.$productgroupcodeval};
					${product_groupwisetotal.$productgroupcodeval}+=${closingstockval.$customer_code_val.$productgroupcodeval};
					//$total_value_overall +=${each_row_total_val.$customer_code_val};
				}
				if(${each_row_total_val.$customer_code_val}==0)
				{
					echo "<td align=\"right\">--</td>";
				}
				else
				{
					echo "<td align=\"right\">".number_format(${each_row_total_val.$customer_code_val},2)."</td>";
				}
				//echo "<td align=\"right\"></td>";	
				echo "</tr>";
				$count++;
				$loop_count++;		
			}
			echo "<tr style='font-weight:bold;'>
			<td align='center' colspan='2'>Grand Total</td>";
			foreach($productgrouparray as $productgroupcodeval)
			 {
			echo "<td align='right'>".number_format(${product_groupwisetotal.$productgroupcodeval},2)."</td>";
			$total_value_overall +=${product_groupwisetotal.$productgroupcodeval};
			 }
			echo "<td align='right'>".number_format($total_value_overall,2)."</td></tr>";
			//echo "<td align='right'></td></tr>";
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
