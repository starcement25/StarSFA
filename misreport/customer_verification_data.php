<?php
ob_start();
session_start();
require("adminUtils.php");

$route = $_REQUEST['route'];
//$employee_arg = str_replace(",","#",$employee);
//$employee_arg = str_replace("'","^",$employee_arg);
?>
    <table class="border" width="70%" style="border-collapse:collapse;" border="1">
      <tr class="TDHEAD">
        <td>Customer Name</td>
        <td >Phone no</td>
      </tr>
    <?php
	$sqlquerycustomerroute="SELECT DISTINCT CM.customer_name,CM.phone_no FROM customer_route_emp_relation CRR,customer_master CM 
							WHERE CM.customer_code=CRR.customer_code AND CRR.route_code=".$route." AND CRR.acedns='Y'
						   ORDER BY CM.customer_name ASC";
	$resultcustomerroute = mysqli_query($link,$sqlquerycustomerroute);
	$countcustomerroute=mysqli_num_rows($resultcustomerroute);
	if($countcustomerroute>0){
		echo "<tr><td colspan = '5' class = 'TDHEAD_SUB' align = 'center'>Retailer CRM</td></tr>";
		while($row_customerroute = mysqli_fetch_assoc($resultcustomerroute)){
			$customer_name = $row_customerroute['customer_name'];
			$phone_no = $row_customerroute['phone_no'];
			$apikey='5b275fbea7392c7de8189d7459b019c1';
			if($phone_no!=''){
			echo "<tr>
					<td>".$customer_name."</td>
					<td><a href=\"javascript:void(0)\" style=\"color:#930;font-weight:bold;\" onClick=\"Javascript:call_verify('".$phone_no."','16.laranya_info','".$apikey."');\">".$phone_no."</a></td>
				  </tr>";
			}
		}
	?>
    </table>
    <br />
    <br>
<div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
}
else{
	echo "No Records";
}
mysqli_close($link);
?>


