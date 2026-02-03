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
	$emp_condition = " AND SUBSTRING(BP.prospect_id,2,5) IN('".$employee."') ";
}
if($start_date!='' && $end_date!='')
{
	$date_condition=" AND SUBSTRING(BP.prospect_id,-14,8) >= '".str_replace("-","",$start_date)."' 
		AND SUBSTRING(BP.prospect_id,-14,8) <='".str_replace("-","",$end_date)."'";
}
else
{
	$date_condition='';
}

	?>
    <table class="border" width="100%" style="border-collapse:collapse;" border="1">
    <tr><td colspan = '8' align = 'center' class = 'TDHEAD_SUB'>Business Prospect report for the period 
	<?php echo date('d-m-Y',strtotime($start_date));?> to <?php echo date('d-m-Y',strtotime($end_date));?></td></tr>
      <tr class="TDHEAD">
      	<td>Create Date</td>
        <td>Emp Name</td>
        <td>Refered Person Name</td>
        <td>Refered Person Profession</td>
        <td>Refered Person Dealer</td>
        <td>Refered Person Phone</td>
        <td>Refered Person Email</td>
        <td>Refered Person Firm</td>
      </tr>
    <?
	$sql_prospect = "SELECT BP.*,EM.emp_name,DATE_FORMAT(SUBSTRING(BP.prospect_id,-14,14),'%d-%m-%Y %H:%i:%s') AS create_date FROM employee_master EM, business_prospect_details BP WHERE  
				EM.emp_code=substring(BP.prospect_id,2,5) 
				 ".$emp_condition.$date_condition." ORDER BY EM.emp_name ASC,DATE_FORMAT(SUBSTRING(BP.prospect_id,-14,14),'%Y-%m-%d %H:%i:%s') DESC";
	$res_prospect = mysql_query($sql_prospect);
	$cnt_prospect=mysql_num_rows($res_prospect);
	if($cnt_prospect >0){
	while($row_prospect = mysql_fetch_array($res_prospect))
	{	
		$dealer_code = $row_prospect['referred_person_dealer'];
		$sql_cust_name = "SELECT customer_name FROM customer_master WHERE customer_code = '".$dealer_code."'";
		$res_cust_name = mysql_query($sql_cust_name);
		$row_cust_name = mysql_fetch_array($res_cust_name);
		$customer_name = $row_cust_name['customer_name'];
		$referred_person_name = $row_prospect['referred_person_name'];
		$emp_name = $row_prospect['emp_name'];
		$create_date = $row_prospect['create_date'];
		$referred_person_profession = $row_prospect['referred_person_profession'];
		$referred_person_phone = $row_prospect['referred_person_phone'];
		$referred_person_email = $row_prospect['referred_person_email'];
		$referred_person_firm = $row_prospect['referred_person_firm'];
		$prospect_id=$row_prospect['prospect_id'];
		echo "<tr>
			<td>".$create_date."</td>
			<td>".$emp_name."</td>
			<td>".$referred_person_name."</td>
			<td>".$referred_person_profession."</td>
			<td>".$customer_name."</td>
			<td>".$referred_person_phone."</td>
			<td>".$referred_person_email."</td>
			<td>".$referred_person_firm."</td>
		  </tr>";
	}
	}
	else{
	echo "<tr><td colspan='10' align='center'>No Records</td><tr>";
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