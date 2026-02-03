<?php
ob_start();
session_start();
require("adminUtils_CRM_CRE.php");

$route = $_REQUEST['route'];
//$employee_arg = str_replace(",","#",$employee);
//$employee_arg = str_replace("'","^",$employee_arg);
$emp_code=$_SESSION['admin_login'];
$current_date=date('Y-m-d');
?>
    <table class="border" width="75%" style="border-collapse:collapse;" border="1" height="177px">
      <tr class="TDHEAD">
        <td width="">Customer Name</td>
        <td width="15%">Phone no</td>
         <td width="15%">Last Call Date Time</td>
        <td width="15%">Responded Date Time</td>
        <td width="15%">Status</td>
        <td width="15%">Complain Closer</td>
      </tr>
    <?php
	$sqlquerycustomerroute="SELECT DISTINCT CM.customer_name,CM.phone_no,CM.customer_code FROM customer_route_emp_relation CRR,customer_master CM,
							emp_datewise_route_allocation  EDRA
							WHERE EDRA.route_code=CRR.route_code AND CM.customer_code=CRR.customer_code AND 
							CRR.route_code='".$route."' AND CM.cust_type='R' AND CRR.acedns='Y' AND EDRA.distributor_code=CM.rds_tag AND 
							EDRA.allocation_date='".$current_date."' AND EDRA.emp_code='".$emp_code."' ORDER BY CM.customer_name ASC";
	$resultcustomerroute = mysqli_query($link,$sqlquerycustomerroute);
	$countcustomerroute=mysqli_num_rows($resultcustomerroute);
	$today=date("Ymd");
	if($countcustomerroute>0){
		echo "<tr><td colspan = '6' class = 'TDHEAD_SUB' align = 'center'>Retailer CRE</td></tr>";
		while($row_customerroute = mysqli_fetch_assoc($resultcustomerroute)){
			$sqltrans=mysqli_fetch_assoc(mysqli_query($link,"SELECT *,DATE_FORMAT(SUBSTRING(call_id,-14,14),'%d-%m-%Y %H:%i:%s') AS last_call_date_time FROM CRM_transaction WHERE customer_code='".$row_customerroute['customer_code']."' AND response_type='Responded' ORDER BY respond_date_time DESC LIMIT 0,1"));
			$customer_name = $row_customerroute['customer_name'];
			$customer_code = $row_customerroute['customer_code'];
			$phone_no = $row_customerroute['phone_no'];
			//if($phone_no!=''){
			echo "<tr>
					<td><a href=\"javascript:void(0);\" style=\"color:blue;\" onclick=\"javascript:return update_crm_details('".$customer_code."')\">".$customer_name."</td>
					<td>".$phone_no."</td>
					<td>".(($sqltrans['last_call_date_time']!='')?$sqltrans['last_call_date_time']:'')."</td>
					<td>".(($sqltrans['respond_date_time']!="0000-00-00 00:00:00" && $sqltrans['respond_date_time']!='')?date('d-m-Y H:i:s',strtotime($sqltrans['respond_date_time'])):'')."</td>
					<td>".$sqltrans['response_type']."</td>
					<td>".$sqltrans['complain_closer']."</td>
				  </tr>";
			//}
		}
	?>
    </table>
    <br />
    <br>
<!--<div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>-->
    <?php
}
else{
	echo "No Records";
}
mysqli_close($link);
?>


