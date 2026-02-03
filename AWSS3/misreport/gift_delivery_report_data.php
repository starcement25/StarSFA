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
	$emp_condition = " AND SUBSTRING(GD.delivery_id,3,5) IN('".$employee."') ";
}
if($start_date!='' && $end_date!='')
{
	$date_condition=" AND SUBSTRING(GD.delivery_id,-14,8) >= '".str_replace("-","",$start_date)."' 
		AND SUBSTRING(GD.delivery_id,-14,8) <='".str_replace("-","",$end_date)."'";
}
else
{
	$date_condition='';
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
    <tr><td colspan = '14' align = 'center' class = 'TDHEAD_SUB'>Gift delivery report for the period 
	<?php echo date('d-m-Y',strtotime($start_date));?> to <?php echo date('d-m-Y',strtotime($end_date));?></td></tr>
      <tr class="TDHEAD">
      	<td>Delivery Date</td>
        <td>Delivery By</td>
        <td>Gift</td>
        <td>Broad option</td>
        <td>Customer Name</td>
        <td>Customer Type</td>
	  	<td>Gift Received By</td>
        <td>Owner Name</td>
        <td>Receiving Employee Name</td>
	    <td>Receiving Employee Mobile</td>
      	<td>Relation with owner</td> 
	    <td>Route</td>
	    <td>Image</td>
      </tr>
    <?
	$sql_gift = "SELECT GD.*,DATE_FORMAT(GD.delivery_date,'%d-%m-%Y') AS delivery_date,CM.customer_name,RM.route_name,EM.emp_name,CM.cust_type FROM gift_delivery_details GD,customer_master CM,route_master RM,employee_master EM WHERE GD.customer_code=CM.customer_code AND GD.route_code=RM.route_code AND SUBSTRING(GD.delivery_id,3,5)=EM.emp_code $emp_condition $date_condition ORDER BY GD.delivery_date DESC";
	$res_gift = mysql_query($sql_gift);
	$cnt_gift=mysql_num_rows($res_gift);
	if($cnt_gift >0){
	while($row_gift = mysql_fetch_array($res_gift)){
		$emp_name = $row_gift['emp_name'];
		$delivery_date = $row_gift['delivery_date'];
		$gift_name = $row_gift['gift_name'];
		$customer_broad_option = $row_gift['customer_broad_option'];
		$gift_delivery_option = $row_gift['gift_delivery_option'];
		$cust_type = $row_gift['cust_type'];
		$customer_name = $row_gift['customer_name'];
		$route_name = $row_gift['route_name'];
		$owner_name = $row_gift['owner_name'];
		$employee_name = $row_gift['employee_name'];
		$employee_mobile = $row_gift['employee_mobile'];
		$employee_relation_owner = $row_gift['employee_relation_owner'];
		$image_1 = $row_gift['image_1'];
		$image_2 = $row_gift['image_2'];
		if($gift_delivery_option=='employee/others') $gift_delivery_option='Employee';
		if($image_1!='')
		{
		$image_string_1 = "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$image_1."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
		}
		else
		{
			$image_string_1='';				
		}
		if($image_1!='')
		{
		$image_string_1 = "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$image_1."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
		}
		else
		{
			$image_string_1='';				
		}
		if($image_2!='')
		{
		$image_string_2 = "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$image_2."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
		}
		else
		{
			$image_string_2='';				
		}
		echo "<tr>
			<td>".$delivery_date."</td>
			<td>".$emp_name."</td>
			<td>".$gift_name."</td>
			<td>".$customer_broad_option."</td>
			<td>".$customer_name."</td>
			<td>".$cust_type."</td>
			<td>".$gift_delivery_option."</td>
			<td>".$owner_name."</td>
			<td>".$employee_name."</td>
			<td>".$employee_mobile."</td>
			<td>".$employee_relation_owner."</td>
			<td>".$route_name."</td>
			<td>".$image_string_1."<br />".$image_string_2."</td>
		  </tr>";
	  }
	}
	else{
	echo "<tr><td colspan='13' align='center'>No Records</td><tr>";
	}
	?>
    </table>
    <br />
    <br>
<div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
mysql_close($link);
?>


