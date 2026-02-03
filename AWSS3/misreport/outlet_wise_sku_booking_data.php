<?php
ob_start();
session_start();
require("adminUtils.php");

$employee_id = $_REQUEST['employee_id'];
$product_id = $_REQUEST['product_id'];
$date_array = array();
if($employee_id=='all')
{
if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy_value_condition = '';
	}
	else{
		$emp_hierarchy_value=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_value_condition = " AND SUBSTRING(POCM.order_no,2,5) IN(".$emp_hierarchy_value.")";
	}
}
else
{
	$emp_hierarchy_value_condition = " AND SUBSTRING(POCM.order_no,2,5)='".$employee_id."'";
}


/*$sql_order_date = "SELECT SUBSTRING(order_no,-14,8) AS no_order_date FROM order_header WHERE ".$order_condition." (SUBSTRING(order_no,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') AND order_no LIKE 'N%'";
$res_order_date = mysql_query($sql_order_date);
while($row_order_date = mysql_fetch_array($res_order_date)){
	$no_order_date = $row_order_date['no_order_date'];
	if(!in_array($no_order_date,$date_array))
		array_push($date_array,$no_order_date);
}

$sql_no_payment = "SELECT SUBSTRING(receipt_no,-14,8) AS no_payment_date FROM payment_header WHERE ".$payment_condition." (SUBSTRING(receipt_no,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') AND receipt_no LIKE 'N%'";
$res_no_payment = mysql_query($sql_no_payment);
while($row_no_payment = mysql_fetch_array($res_no_payment)){
	$no_payment_date = $row_no_payment['no_payment_date'];
	if(!in_array($no_payment_date,$date_array))
		array_push($date_array,$no_payment_date);
}

sort($date_array);*/

//if(!empty($date_array)){
	$sqlproddesc="SELECT prod_desc FROM product_master WHERE prod_code='".$product_id."'";
	$rsproddesc=mysql_query($sqlproddesc);
	$rowproddesc=mysql_fetch_array($rsproddesc);
	$prod_desc=$rowproddesc['prod_desc'];
	?>
    <table class="border" width="100%" style="border-collapse:collapse;" border="1" id="display_table">
    <tr class='TDHEAD'><td colspan='17' align='center'>Booking Details of <?php echo $prod_desc;?></td></tr>
      <tr class="TDHEAD_SUB">
      	<td>Employee</td>
      	<td>Beat</td>
        <td>Store</td>
        <td>Class</td>
        <td>Category</td>
     <?php 
	 $month_val_array=array();
	 $result = mysql_query("SELECT DISTINCT  MONTH(`visit_date`) AS month FROM prev_order_counting_master");
		while($row = mysql_fetch_array($result)) {
		   $monthval = date("F", mktime(0, 0, 0, $row['month']));
		   echo "<td>".$monthval . "</td>";
		   array_push($month_val_array,$row['month']);
		}
	?>	   
      </tr>
    <?
	$sql_outlet_sku = "SELECT DISTINCT EM.emp_name,CM.customer_name,RM.route_name,SUM(POCM.visit_qty) AS total_qty,
					MONTH(POCM.visit_date) AS month,POCM.customer_code
				FROM customer_master CM,employee_master EM,prev_order_counting_master POCM,route_master RM
				WHERE  POCM.customer_code=CM.customer_code AND CM.route_code=RM.route_code 
				AND SUBSTRING(POCM.order_no,2,5)=EM.emp_code AND  POCM.product_code='".$product_id."'
				 AND (POCM.order_no LIKE 'O%') 
				AND DATE_FORMAT(SUBSTRING(POCM.order_no,-14,8),'%Y-%m-%d') > '2020-12-01' ".$emp_hierarchy_value_condition." 
				GROUP BY SUBSTRING(POCM.order_no,2,5),POCM.customer_code,MONTH(POCM.visit_date) 
				ORDER BY EM.emp_name ASC,CM.customer_name ASC";
	$res_outlet_sku = mysql_query($sql_outlet_sku);
	$cnt_outlet_sku=mysql_num_rows($res_outlet_sku);
	if($cnt_outlet_sku >0){
		$count=1;
	$customer_code_array=array();
	$customer_name_array=array();
	$emp_name_array=array();	
	$route_name_array=array();		
	while($row_outlet_sku = mysql_fetch_array($res_outlet_sku)){
		$customer_name = $row_outlet_sku['customer_name'];
		$emp_name = $row_outlet_sku['emp_name'];
		$route_name = $row_outlet_sku['route_name'];
		$total_qty = $row_outlet_sku['total_qty'];
		$month=$row_outlet_sku['month'];
		$customer_code = $row_outlet_sku['customer_code'];
		if(!in_array($customer_code,$customer_code_array))
		{
			array_push($customer_code_array,$customer_code);
			array_push($customer_name_array,$customer_name);
			array_push($emp_name_array,$emp_name);
			array_push($route_name_array,$route_name);
		}
		${'visit_qty'.$customer_code.$month}=${'visit_qty'.$customer_code.$month}+$total_qty;
	}
	//echo ${'visit_qty'.'C/0000001'.'12'};
	
	for($i=0;$i< count($customer_code_array);$i++){
			echo "<tr>
				<td>".$emp_name_array[$i]."</td>
				<td>".$route_name_array[$i]."</td>
				<td>".$customer_name_array[$i]."</td>
				<td></td>
				<td></td>";
				
				foreach($month_val_array as $monthvalue)
				{
				echo "<td align=\"right\">".number_format((${'visit_qty'.$customer_code_array[$i].$monthvalue}),2)."</td>";
				}
			  echo "</tr>";
		}
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