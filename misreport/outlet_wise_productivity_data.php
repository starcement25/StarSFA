<?php
ob_start();
session_start();
require("adminUtils.php");

$employee_id = $_REQUEST['employee_id'];
$date_array = array();
if($employee_id=='all')
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
}


/*$sql_order_date = "SELECT SUBSTRING(order_no,-14,8) AS no_order_date FROM order_header WHERE ".$order_condition." (SUBSTRING(order_no,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') AND order_no LIKE 'N%'";
$res_order_date = mysqli_query($link,$sql_order_date);
while($row_order_date = mysqli_fetch_assoc($res_order_date)){
	$no_order_date = $row_order_date['no_order_date'];
	if(!in_array($no_order_date,$date_array))
		array_push($date_array,$no_order_date);
}

$sql_no_payment = "SELECT SUBSTRING(receipt_no,-14,8) AS no_payment_date FROM payment_header WHERE ".$payment_condition." (SUBSTRING(receipt_no,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') AND receipt_no LIKE 'N%'";
$res_no_payment = mysqli_query($link,$sql_no_payment);
while($row_no_payment = mysqli_fetch_assoc($res_no_payment)){
	$no_payment_date = $row_no_payment['no_payment_date'];
	if(!in_array($no_payment_date,$date_array))
		array_push($date_array,$no_payment_date);
}

sort($date_array);*/

//if(!empty($date_array)){
	$sqlproddesc="SELECT prod_desc FROM product_master WHERE prod_code='".$product_id."'";
	$rsproddesc=mysqli_query($link,$sqlproddesc);
	$rowproddesc=mysqli_fetch_assoc($rsproddesc);
	$prod_desc=$rowproddesc['prod_desc'];
	?>
    <table class="border" width="100%" style="border-collapse:collapse;" border="1" id="display_table">
    <tr class='TDHEAD'><td colspan='17' align='center'>Customer Visit & Productivity report Details</td></tr>
      <tr class="TDHEAD_SUB">
      	<td>Employee</td>
      	<td>Beat</td>
        <td>Store</td>
        <td>Class</td>
        <td>Category</td>
     <?php 
	 $month_val_array=array();
	 $result = mysqli_query($link,"SELECT DISTINCT  MONTH(`visit_date`) AS month FROM prev_order_counting_master");
		while($row = mysqli_fetch_assoc($result)) {
		   $monthval = date("F", mktime(0, 0, 0, $row['month']));
		   echo "<td>".$monthval . " Visit Date</td>";
		    echo "<td>".$monthval . " Productivity Date</td>";
		   array_push($month_val_array,$row['month']);
		}
	?>	   
      </tr>
    <?
	/*$sql_outlet_sku="SELECT DISTINCT EM.emp_name,CM.customer_name,RM.route_name,DATE_FORMAT(SUBSTRING(POCM.order_no,-14,8),'%d') AS visit_date ,  IF( POCM.order_no LIKE 'O%', DATE_FORMAT(SUBSTRING(POCM.order_no,-14,8),'%d'), NULL ) AS productive_date, MONTH(POCM.visit_date)  AS month,POCM.customer_code FROM customer_master CM,employee_master EM,prev_order_counting_master POCM,route_master RM WHERE POCM.customer_code=CM.customer_code AND CM.route_code=RM.route_code AND SUBSTRING(POCM.order_no,-19,5)=EM.emp_code AND DATE_FORMAT(SUBSTRING(POCM.order_no,-14,8),'%Y-%m-%d') > '2020-12-01'  ORDER BY EM.emp_name ASC,CM.customer_name ASC,MONTH(POCM.visit_date) ASC";*/
	$sql_outlet_sku = "SELECT DISTINCT EM.emp_name,CM.customer_name,RM.route_name,
					GROUP_CONCAT(DISTINCT (DATE_FORMAT(SUBSTRING(POCM.order_no,-14,8),'%d')) SEPARATOR ',') AS visit_date ,
					  GROUP_CONCAT( DISTINCT IF( POCM.order_no LIKE 'O%', DATE_FORMAT(SUBSTRING(POCM.order_no,-14,8),'%d'), NULL ) SEPARATOR ',')  AS productive_date,
					MONTH(POCM.visit_date) AS month,POCM.customer_code
				FROM customer_master CM,employee_master EM,prev_order_counting_master POCM,route_master RM
				WHERE  POCM.customer_code=CM.customer_code AND CM.route_code=RM.route_code 
				AND SUBSTRING(POCM.order_no,-19,5)=EM.emp_code
				AND DATE_FORMAT(SUBSTRING(POCM.order_no,-14,8),'%Y-%m-%d') > '2020-12-01' ".$emp_hierarchy_value_condition." 
				GROUP BY SUBSTRING(POCM.order_no,-19,5),POCM.customer_code,MONTH(POCM.visit_date) 
				ORDER BY EM.emp_name ASC,CM.customer_name ASC";
	$res_outlet_sku = mysqli_query($link,$sql_outlet_sku);
	$cnt_outlet_sku=mysqli_num_rows($res_outlet_sku);
	if($cnt_outlet_sku >0){
		$count=1;
	$customer_code_array=array();
	$customer_name_array=array();
	$emp_name_array=array();	
	$route_name_array=array();
	$visit_date_array=array();			
	while($row_outlet_sku = mysqli_fetch_assoc($res_outlet_sku)){
		$customer_name = $row_outlet_sku['customer_name'];
		$emp_name = $row_outlet_sku['emp_name'];
		$route_name = $row_outlet_sku['route_name'];
		$visit_date = $row_outlet_sku['visit_date'];
		$productive_date = $row_outlet_sku['productive_date'];
		$month=$row_outlet_sku['month'];
		$customer_code = $row_outlet_sku['customer_code'];
		if(!in_array($customer_code,$customer_code_array))
		{
			array_push($customer_code_array,$customer_code);
			array_push($customer_name_array,$customer_name);
			array_push($emp_name_array,$emp_name);
			array_push($route_name_array,$route_name);
		}
		${'visit_date'.$customer_code.$month}=$visit_date;
		${'productive_date'.$customer_code.$month}=$productive_date;
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
				echo "<td align=\"right\">".${'visit_date'.$customer_code_array[$i].$monthvalue}."</td>";
				echo "<td align=\"right\">".${'productive_date'.$customer_code_array[$i].$monthvalue}."</td>";
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
mysqli_close($link);
?>