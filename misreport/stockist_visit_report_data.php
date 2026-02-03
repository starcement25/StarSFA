<?php
ob_start();
session_start();
require("adminUtils.php");

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$employee = $_REQUEST['employee'];
//$employee_arg = str_replace("#",",",$employee);
//$employee_arg = str_replace("^","'",$employee_arg);
if($employee == 'all'){
	$emp_condition = '';
}
else{
	$emp_condition = " AND SUBSTRING(SV.visit_trans_id,3,5) IN ('".$employee."')";
}
?>
<table border="1" style="border-collapse:collapse;" class="border" width="100%">
<tr class="TDHEAD"><td colspan="6" align="center">Stockist Visit Report</td></tr>
  <tr class="TDHEAD_SUB">
  	<td>Date</td>
    <td>Visit Done By</td>
    <td>Stockist</td>
    <td>Retailer</td>
    <td>Sale</td>
    <td>Folder</td>
  </tr>
<?php 
$count=1;
$sql_stockist = "SELECT DISTINCT CM.customer_name,DATE_FORMAT(SUBSTRING(SV.visit_trans_id,-14,8),'%d-%m-%Y') AS visit_date,EM.emp_name,
				SV.stockist_code,SV.customer_code,SUBSTRING(SV.visit_trans_id,3,5) AS emp_code,SV.sale,SV.folder
				FROM stockist_visit SV,customer_master CM,employee_master EM
				WHERE SV.stockist_code=CM.customer_code AND SUBSTRING(SV.visit_trans_id,3,5)=EM.emp_code ".$emp_condition." AND 
				(DATE_FORMAT(SUBSTRING(SV.visit_trans_id,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."') ORDER BY 
				EM.emp_name ASC,DATE_FORMAT(SUBSTRING(SV.visit_trans_id,-14,8),'%Y-%m-%d') DESC";
$res_stockist = mysqli_query($link,$sql_stockist);
$count_stockist=mysqli_num_rows($res_stockist);
if($count_stockist >0){
while($row_stockist = mysqli_fetch_assoc($res_stockist)){
	$visit_date = $row_stockist['visit_date'];
	$emp_name = $row_stockist['emp_name'];
	$customer_code= $row_stockist['customer_code'];
	$customer_name_stockist= $row_stockist['customer_name'];
	$sale= $row_stockist['sale'];
	$folder= $row_stockist['folder'];
	
	$sqlretailername="SELECT customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
	$rsretailername=mysqli_query($link,$sqlretailername);
	$rowretailername=mysqli_fetch_assoc($rsretailername);
	$retailer_name=$rowretailername['customer_name'];
	
		echo "<tr>
			<td>".$visit_date."</td>
			<td>".$emp_name."</td>
			<td>".$customer_name_stockist."</td>
			<td>".$retailer_name."</td>
			<td>".$sale."</td>
			<td>".$folder."</td>
			</tr>";	
	}
}
else
{
	echo "<tr><td colspan='6'>No Records found</td></tr>";
}
mysqli_close($link);
?>
</table>
<br />
    <br>
<div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>