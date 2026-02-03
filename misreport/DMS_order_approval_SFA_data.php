<?php
ob_start();
session_start();
require("adminUtils.php");

//$employee = $_REQUEST['employee'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$date_array = array();
if($employee == 'all'){
	$order_condition = '';
	$payment_condition = '';
	$emp_condition = '';
}
else{
	$emp_condition = " AND SUBSTRING(TAA.approval_id,3,5) IN('".$employee."') ";
}
if($start_date!='' && $end_date!='')
{
	$date_condition=" AND SUBSTRING(TAA.order_date,1,10) >= '".$start_date."' 
		AND SUBSTRING(TAA.order_date,1,10) <='".$end_date."'";
}
else
{
	$date_condition='';
}
$emp_condition='';
	?>
    <table class="border" width="100%" style="border-collapse:collapse;" border="1">
    <tr><td colspan = '14' align = 'center' class = 'TDHEAD_SUB'>Order APproval report for the period 
	<?php echo date('d-m-Y',strtotime($start_date));?> to <?php echo date('d-m-Y',strtotime($end_date));?></td></tr>
      <tr class="TDHEAD">
      	<td>Order Date</td>
        <td>Order No</td>
        <td>Customer Name</td>
        <td>Customer Type</td>
	  	<td>Product Name</td>
        <td>Actual Qty</td>
        <td>Change Qty</td>
        <td>Destination</td>
	    <td>Approval Status</td>
        <td>Approved Date</td>
      	<td>Approved By</td>
        <td>Remarks</td>  
      </tr>
    <?
	$sql_approval = "SELECT TAA.*,DATE_FORMAT(TAA.order_date,'%d-%m-%Y') AS order_date,DATE_FORMAT(SUBSTRING(TAA.approval_id,-14,8),'%d-%m-%Y') AS approval_date,CM.customer_name,CM.cust_type FROM T_APPERPDO_APPROVAL TAA,customer_master CM WHERE TAA.customer_code=CM.customer_code  $emp_condition $date_condition ORDER BY order_date DESC";
	$res_approval = mysqli_query($link,$sql_approval);
	$cnt_approval=mysqli_num_rows($res_approval);
	if($cnt_approval >0){
	while($row_approval = mysqli_fetch_assoc($res_approval)){
		$order_date = $row_approval['order_date'];
		$approval_date = $row_approval['approval_date'];
		$customer_name = $row_approval['customer_name'];
		$cust_type = $row_approval['cust_type'];
		$APPORDERNO = $row_approval['APPORDERNO'];
		$cust_type = $row_approval['cust_type'];
		$prod_display_name = $row_approval['prod_display_name'];
		$QTY = $row_approval['QTY'];
		$QTY_CHANGED = $row_approval['QTY_CHANGED'];
		$approval_status = $row_approval['approval_status'];
		$destnation_name = $row_approval['destination_name'];
		$remarks = $row_approval['remarks'];
		$approval_done_by = $row_approval['approval_done_by'];
		
		$sqlempname="SELECT emp_name FROM employee_master WHERE emp_code='".$approval_done_by."'";
		$rsempname=mysqli_query($link,$sqlempname);
		$rowempname=mysqli_fetch_assoc($rsempname);
		$emp_name=$rowempname['emp_name'];
		
		echo "<tr>
			<td>".$order_date."</td>
			<td>".$APPORDERNO."</td>
			<td>".$customer_name."</td>
			<td>".$cust_type."</td>
			<td>".$prod_display_name."</td>
			<td>".number_format($QTY,2)."</td>
			<td>".number_format($QTY_CHANGED,2)."</td>
			<td>".$destnation_name."</td>
			<td>".$approval_status."</td>
			<td>".$approval_date."</td>
			<td>".$emp_name."</td>
			<td>".$remarks."</td>
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
mysqli_close($link);
?>


