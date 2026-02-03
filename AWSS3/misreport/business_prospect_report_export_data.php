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
	$emp_condition = " AND SUBSTRING(PCH.trans_id,3,5) IN('".$employee."') ";
}
if($start_date!='' && $end_date!='')
{
	$date_condition=" AND SUBSTRING(PCH.trans_id,-14,8) >= '".str_replace("-","",$start_date)."' 
		AND SUBSTRING(PCH.trans_id,-14,8) <='".str_replace("-","",$end_date)."'";
}
else
{
	$date_condition='';
}

	?>
    <table class="border" width="100%" style="border-collapse:collapse;" border="1">
    <tr><td colspan = '14' align = 'center' class = 'TDHEAD_SUB'>Business Prospect report for the period 
	<?php echo date('d-m-Y',strtotime($start_date));?> to <?php echo date('d-m-Y',strtotime($end_date));?></td></tr>
      <tr class="TDHEAD">
      	<td>Create Date</td>
        <td>Emp Name</td>
        <td>Prospect Name</td>
        <td>Address</td>
        <td>Pin</td>
        <td>Area</td>
	  	<td>Phone No</td>
        <td>Cust type</td>
        <td>Remarks</td>
        <?php if(strtoupper($_SESSION['nick_name'])=='ILS'){?>
        <td>Category of Stores</td>
        <?php }?>
        <td>Discussed Product</td>
      </tr>
    <?
	$sql_prospect = "SELECT EM.emp_name,DATE_FORMAT(SUBSTRING(PCH.trans_id,-14,14),'%d-%m-%Y %H:%i:%s') AS create_date,PCH.trans_id,PCH.customer_name, PCH.phone_no, PCH.customer_code,PCH.cust_type,PCH.remarks, PCH.area,PCH.pin,PCH.address,PCH.category_of_store FROM employee_master EM, prospective_customer_header PCH WHERE  EM.emp_code=substring(PCH.trans_id,3,5) 
				 ".$emp_condition.$date_condition." ORDER BY EM.emp_name ASC,DATE_FORMAT(SUBSTRING(PCH.trans_id,-14,14),'%Y-%m-%d %H:%i:%s') DESC";
	$res_prospect = mysql_query($sql_prospect);
	$cnt_prospect=mysql_num_rows($res_prospect);
	if($cnt_prospect >0){
	while($row_prospect = mysql_fetch_array($res_prospect))
	{	
		$route_code = $row_prospect['area'];
		$sql_route_name = "SELECT route_name FROM route_master WHERE route_code = '".$route_code."'";
		$res_route_name = mysql_query($sql_route_name);
		$row_route_name = mysql_fetch_array($res_route_name);
		$route_name = $row_route_name['route_name'];
		if($route_name=='') $route_name=$row_prospect['area'];
		$pin = $row_prospect['pin'];
		$emp_name = $row_prospect['emp_name'];
		$create_date = $row_prospect['create_date'];
		$customer_name = $row_prospect['customer_name'];
		$phone_no = $row_prospect['phone_no'];
		$address = $row_prospect['address'];
		$cust_type = $row_prospect['cust_type'];
		$remarks = $row_prospect['remarks'];
		$trans_id=$row_prospect['trans_id'];
		$category_of_store=$row_prospect['category_of_store'];
	  $SQLproddiscussed="SELECT GROUP_CONCAT(PM.prod_desc SEPARATOR ',') as prod_discussed FROM prospective_customer_details PCD,product_master PM WHERE 
						PM.prod_code=PCD.product_code AND PCD.trans_id='".$trans_id."' ORDER BY PM.prod_desc ASC";
	  $rsproddiscussed=mysql_query($SQLproddiscussed);
	  $rowproddiscussed=mysql_fetch_array($rsproddiscussed);
	  $prod_discussed=$rowproddiscussed['prod_discussed'];
		echo "<tr>
			<td>".$create_date."</td>
			<td>".$emp_name."</td>
			<td>".$customer_name."</td>
			<td>".$address."</td>
			<td>".$pin."</td>
			<td>".$route_name."</td>
			<td>".$phone_no."</td>
			<td>".$cust_type."</td>
			<td>".$remarks."</td>";
			if(strtoupper($_SESSION['nick_name'])=='ILS'){
				echo "<td>".$category_of_store."</td>";
			}
			echo "<td>".$prod_discussed."</td>
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