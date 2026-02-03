<?php
ob_start();
session_start();
require("adminUtils.php");

$employee = $_REQUEST['employee'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$date_array = array();
if($employee == 'all'){
	$order_condition = '';
	$payment_condition = '';
	$emp_condition = '';
}
else{
	$order_condition = " SUBSTRING(order_no,3,5) IN('".$employee."') AND ";
	$payment_condition = " SUBSTRING(receipt_no,3,5) IN('".$employee."') AND ";
	$emp_condition = " AND LO.emp_code IN('".$employee."') ";
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
	?>
    <table class="border" width="100%" style="border-collapse:collapse;" border="1">
      <tr class="TDHEAD">
      	<td width="10%">Date</td>
        <td width="20%">Emp Name</td>
        <td width="20%">Customer Name</td>
        <?php if(strtoupper($_SESSION['nick_name'])=='PALSONS'){
			?>
             <td width="15%">Route</td>
        <?php }?>
        <td >Remarks</td>
        <td width="10%">Type</td>
        <?php if(strtoupper($_SESSION['nick_name'])=='MAGIK'){
			$address_cond=',LO.address';
			?>
       	 <td width="20%">Location</td>
        <?php }
		else { $address_cond='';}
		?>
      </tr>
    <?
	$sql_emp = "SELECT EM.emp_code, EM.dns_emp_code, EM.emp_name,LO.trans_id,DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%d-%m-%Y') AS trans_date $address_cond
				FROM employee_master EM,location LO WHERE EM.emp_code=LO.emp_code AND (LO.trans_id LIKE 'NO%' OR LO.trans_id LIKE 'NC%') 
				AND (SUBSTRING(LO.date,1,10) BETWEEN '".$start_date."' AND '".$end_date."')".$emp_condition." AND EM.acedns='Y' 
				ORDER BY EM.emp_code ASC,SUBSTRING(LO.date,1,10) ASC";
	$res_emp = mysql_query($sql_emp);
	$cnt_emp=mysql_num_rows($res_emp);
	if($cnt_emp >0){
	while($row_emp = mysql_fetch_array($res_emp)){
		$dns_emp_code = $row_emp['dns_emp_code'];
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$trans_id=$row_emp['trans_id'];
		$trans_date=$row_emp['trans_date'];
		$trans_id_parts=substr($trans_id,0,2);
		
		/*$sql_location_check = "SELECT trans_id FROM location WHERE (trans_id LIKE 'NO%' OR trans_id LIKE 'NC%') AND 
		emp_code = '".$emp_code."' AND (SUBSTRING(date,1,10) BETWEEN '".$start_date."' AND '".$end_date."')";
		$res_location_check = mysql_query($sql_location_check);
		$location_row_check = mysql_num_rows($res_location_check);*/
		
		//if($location_row_check>0){
			//echo "<tr><td colspan = '5' class = 'TDHEAD_SUB' align = 'center'>$dns_emp_code - $emp_name</td></tr>";
			
			//foreach($date_array as $date_val){
				//$sql_no_order_details = "SELECT customer_code, d_instruction FROM order_header WHERE SUBSTRING(order_no,-14,8) = '".$date_val."' AND SUBSTRING(order_no,3,5) = '".$emp_code."' AND order_no LIKE 'NO%'";
				if($trans_id_parts=='NO'){
					if(strtoupper($_SESSION['nick_name'])=='MAGIK'){ 
							$address_val=$row_emp['address'];
							$address_TD='<td>'.$address_val.'</td>';
					}
					else $address_TD='';
				$sql_no_order_details = "SELECT OH.customer_code, OH.d_instruction,CM.dns_customer_code,CM.customer_name,RM.route_name
										FROM order_header OH,customer_master CM,route_master RM WHERE OH.customer_code=CM.customer_code AND 
										CM.route_code=RM.route_code AND   OH.order_no='".$trans_id."'";
				$res_no_order_details = mysql_query($sql_no_order_details);
				$no_order_rows = mysql_num_rows($res_no_order_details);
				if($no_order_rows>0){
					$res_no_order_details = mysql_query($sql_no_order_details);
					while($row_no_order_details = mysql_fetch_array($res_no_order_details)){
						$customer_code = $row_no_order_details['customer_code'];
						$d_instruction = $row_no_order_details['d_instruction'];
						
						//$sql_customer_name = "SELECT dns_customer_code, customer_name FROM customer_master WHERE customer_code = '".$customer_code."'";
						//$res_customer_name = mysql_query($sql_customer_name);
						//$row_customer_name = mysql_fetch_array($res_customer_name);
						$customer_name = $row_no_order_details['customer_name'];
						$route_name = $row_no_order_details['route_name'];
						$dns_customer_code = $row_no_order_details['dns_customer_code'];
						if($customer_name != ''){
							echo "<tr>
								<td>".$trans_date."</td>
								<td>".$emp_name."</td>
								<td>".$customer_name."</td>";
								if(strtoupper($_SESSION['nick_name'])=='PALSONS'){
									echo "<td>".$route_name."</td>";
								}
								echo "<td>".$d_instruction."</td>
								<td>No Order</td>".$address_TD."
							  </tr>";
						}
					}
				}
			}
				//$sql_no_payment_details = "SELECT customer_code, p_remark FROM payment_header WHERE SUBSTRING(receipt_id,-14,8) = '".$date_val."' AND SUBSTRING(receipt_id,3,5) = '".$emp_code."' AND receipt_id LIKE 'NC%'";
				if($trans_id_parts=='NC'){
					if(strtoupper($_SESSION['nick_name'])=='MAGIK'){ 
							$address_TD='<td></td>';
					}
					else $address_TD='';
				$sql_no_payment_details = "SELECT PH.customer_code, PH.p_remark,CM.dns_customer_code,CM.customer_name 
										FROM payment_header PH,customer_master CM WHERE PH.customer_code=CM.customer_code 
										AND  PH.receipt_id='".$trans_id."'";
				$res_no_payment_details = mysql_query($sql_no_payment_details);
				$no_payment_rows = mysql_num_rows($res_no_payment_details);
				if($no_payment_rows>0){
					$res_no_payment_details = mysql_query($sql_no_payment_details);
					while($row_no_payment_details = mysql_fetch_array($res_no_payment_details)){
						$customer_code = $row_no_payment_details['customer_code'];
						$p_remark = $row_no_payment_details['p_remark'];
						
						//$sql_customer_name = "SELECT customer_name FROM customer_master WHERE customer_code = '".$customer_code."'";
						//$res_customer_name = mysql_query($sql_customer_name);
						//$row_customer_name = mysql_fetch_array($res_customer_name);
						$customer_name = $row_no_payment_details['customer_name'];
						
						if($customer_name != ''){
							echo "<tr>
								<td>".$trans_date."</td>
								<td>".$emp_name."</td>
								<td>".$customer_name."</td>
								<td>".$p_remark."</td>
								<td>No Collection</td>".$address_TD."
							  </tr>";
						}
					}
				}
			}
			//}
		//}
	  }
	}
	else{
	echo "No Records";
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


