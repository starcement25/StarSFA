<?php
ob_start();
session_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

if($_GET['type'] == 'today'){
	$today = date('Y-m-d');
	$payment_status = "Today's Collection Status";
	$payment_header_cond = " SUBSTRING(PH.receipt_id,-14,8) = '".str_replace("-","",$today)."' ";
	$value = 1;
}
else if($_GET['type'] == 'mtd'){
	@$current_date = date('Y-m-d');
	$month = explode("-",$current_date);
	$year = $month[0];
	$month = $month[1];
	$payment_status = "Collection Status MTD";
	$payment_header_cond = " SUBSTRING(PH.receipt_id,-14,4) =$year AND substring(PH.receipt_id,-10,2) =$month ";
	$value = 2;
}
else if($_GET['type'] == 'custom'){
	$start_date = str_replace("-","",$_GET['start_date']);
	$end_date = str_replace("-","",$_GET['end_date']);
	$value = 3;
	$payment_status = "Collection Status From ".date('d-m-Y',strtotime(''.$_GET['start_date'].''))." To ".date('d-m-Y',strtotime(''.$_GET['end_date'].''));
	$payment_header_cond = " (SUBSTRING(PH.receipt_id,-14,8) BETWEEN ".$start_date." AND ".$end_date.") ";
}
else{
	$today = date('Y-m-d');
	$payment_status = "Today's Collection Status";
	$payment_header_cond = " SUBSTRING(PH.receipt_id,-14,8) = '".str_replace("-","",$today)."' ";
	$value = 1;
}

if($_GET['emp_name'] == 'all'){
	$emp_cond = "";
}
else{
	$emp_cond = " AND SUBSTRING(PH.receipt_id,2,5)='".$_GET['emp_name']."' ";
}
$count = 1;
$emp_name_array = array();
$receipt_date_array = array();
$sql_collection = "SELECT PH.receipt_id, PH.customer_code, DATE_FORMAT(SUBSTRING(PH.receipt_id,-14,8),'%d-%m-%Y') as receipt_date FROM payment_header PH, employee_master EM WHERE ".$payment_header_cond." AND PH.receipt_id LIKE 'P%'".$emp_cond." AND EM.emp_code=SUBSTRING(PH.receipt_id,2,5) ORDER BY EM.emp_name ASC, receipt_date DESC";
$res_collection = mysqli_query($link,$sql_collection);
$total_rows = mysqli_num_rows($res_collection);
if($total_rows>0){
	?>
    <table border="1" width="100%" style="border-collapse:collapse;" cellpadding="8">
      <tr class="TDHEAD">
        <td align="center" colspan="4"><?php echo $payment_status; ?></td>
      </tr>
      <tr class="TDHEAD_SUB" align="center">
        <td>SI</td>
        <td width="20%">Date</td>
        <td>Customer Name</td>
        <td width="20%">Collected<br />Amount</td>
    <?php
	$res_collection = mysqli_query($link,$sql_collection);
	while($row_collection = mysqli_fetch_assoc($res_collection)){
		$receipt_id = $row_collection['receipt_id'];
		$customer_code = $row_collection['customer_code'];
		$receipt_date = $row_collection['receipt_date'];
		
		$sql_empname = "SELECT emp_name FROM employee_master WHERE emp_code='".substr($receipt_id,1,5)."'";
		$res_empname = mysqli_query($link,$sql_empname);
		$row_empname = mysqli_fetch_assoc($res_empname);
		$emp_name = $row_empname['emp_name'];
		if(!in_array($emp_name,$emp_name_array)){
			array_push($emp_name_array,$emp_name);
			$receipt_date_array = array();
			echo "<tr class=\"TDHEAD\"><td colspan='4' align='center'>$emp_name</td></tr>";
		}
		
		$sql_customer_name = "SELECT customer_name FROM customer_master WHERE customer_code = '".$customer_code."'";
		$res_customer_name = mysqli_query($link,$sql_customer_name);
		$row_customer_name = mysqli_fetch_assoc($res_customer_name);
		$customer_name = $row_customer_name['customer_name'];
		
		$sql_payment_details = "SELECT SUM(amount) as collected_amount FROM payment_details WHERE receipt_id='".$receipt_id."'";
		$res_payment_details = mysqli_query($link,$sql_payment_details);
		$row_payment_details = mysqli_fetch_assoc($res_payment_details);
		$collection_amount = $row_payment_details['collected_amount'];
		
		$total_collection_amount += $collection_amount;
		
		echo "<tr>
				<td>".$count."</td>";
				if(!in_array($receipt_date,$receipt_date_array)){
					array_push($receipt_date_array,$receipt_date);
					echo "<td bgcolor=\"gray\" style=\"font-weight:bold;\">".$receipt_date."</td>";
				}
				else{
				echo "<td></td>";
				}
				echo "<td><a href=\"javascript:void(0);\" style=\"color:blue; font-weight:bold;\" onclick=\"show_invoice('$receipt_id','$customer_name');\">".$customer_name."</a></td>
				<td align=\"right\">".number_format($collection_amount,2)."</td>
			  </tr>";
		$count++;
	}
	echo "<tr style=\"font-weight:bold;\">
			<td colspan='3'>Total</td>
			<td align=\"right\">".number_format($total_collection_amount,2)."</td>
		  </tr>";
	echo "</table>";
}
else{
	echo "<font color='red'><strong>No records</strong></font>";
}
mysqli_close($link);
?>