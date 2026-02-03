<?php
ob_start();
session_start();
require("adminUtils.php");

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

if($_SESSION['admin_login']=="admin"){
	$emp_hierarchy = '';
	$emp_hierarchy_condition = '';
	$emp_hierarchy_condition_one='';
}
else{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition = " WHERE emp_code IN(".$emp_hierarchy.") ";
	$emp_hierarchy_condition_one = " AND SUBSTRING(survey_id,3,5) IN(".$emp_hierarchy.") ";
}

$zone = $_REQUEST['zone'];
$start_date = $_REQUEST['start_date'];
$start_date = date('Y-m-d',strtotime($_REQUEST['start_date']));
$end_date = $_REQUEST['end_date'];
$end_date  = date('Y-m-d',strtotime($_REQUEST['end_date']));
$type = $_REQUEST['type'];
if(isset($_REQUEST['division_id']))
{
	$division_id = $_REQUEST['division_id'];
}
if(isset($_REQUEST['region']))
{
	$region = $_REQUEST['region'];
}
if(isset($_REQUEST['CCC']))
{
	$CCC = $_REQUEST['CCC'];
}
if($zone != ''){
	if($zone == 'all')
		$zone_condition = "";
	else
		$zone_condition = " AND zone IN(".$zone.") ";
}
if($division_id != ''){
	if($division_id == 'all')
		$division_condition = "";
	else
		$division_condition = " AND division IN(".$division_id.") ";
}
if($region != ''){
	if($region == 'all')
		$region_condition = "";
	else
		$region_condition = " AND region IN(".$region.") ";
}
if($CCC != ''){
	if($CCC == 'all')
		$CCC_condition = "";
	else
		$CCC_condition = " AND CCC IN(".$CCC.") ";
}

$kiosk_id_array = array();

$sql_kiosk_details = "SELECT *,DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS kiosk_date FROM kiosk_transaction_details 
					WHERE type='".$type."' AND DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y%-%m-%d') <='".$end_date."' 
					AND DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y%-%m-%d') >='".$start_date."'".$emp_hierarchy_condition_one.$zone_condition.
					$division_condition.$region_condition.$CCC_condition." ORDER BY kiosk_id ASC";
$res_kiosk_details = mysql_query($sql_kiosk_details);
$count_kiosk_details=mysql_num_rows($res_kiosk_details);
if($count_kiosk_details >0)
{
/*while($row_order_date = mysql_fetch_array($res_order_date)){
	$no_order_date = $row_order_date['no_order_date'];
	if(!in_array($no_order_date,$date_array))
		array_push($date_array,$no_order_date);
}*/

//sort($date_array);

//if(!empty($date_array)){
	?>
    <table class="border" width="100%" style="border-collapse:collapse;" border="1">
      <tr class="TDHEAD"><td colspan="8" align="center">From Date: <?php echo date('d-m-Y',strtotime($start_date));?> - To Date: <?php echo date('d-m-Y',strtotime($end_date));?></td></tr>
       <tr class="TDHEAD_SUB">
            <td width="5%">Srl</td>
            <td width="8%" align="center">Date</td>
            <td width="10%" align="center">No. of <br /> Transactions</td>
            <td width="" align="center">Cheque Amount<br /> (INR)</td>
            <td width="16%">CCC</td>
            <td width="16%">Division</td>
            <td width="16%">Region</td>
            <td width="16%">Zone</td>
      </tr>
    <?php
	$count=1;
	while($row_kiosk_details = mysql_fetch_array($res_kiosk_details)){
		$kiosk_date = $row_kiosk_details['kiosk_date'];
		$kiosk_id = $row_kiosk_details['kiosk_id'];
		$no_of_transactions = $row_kiosk_details['no_of_transactions'];
		$amount = round($row_kiosk_details['amount'],2);
		$CCC = $row_kiosk_details['CCC'];
		$division = $row_kiosk_details['division'];
		$region = $row_kiosk_details['region'];
		$zone = $row_kiosk_details['zone'];
		${total_no_transactions.$kiosk_id}=${total_no_transactions.$kiosk_id}+$no_of_transactions;
		${total_amount.$kiosk_id}=${total_amount.$kiosk_id}+$amount;

		$current_kiosk=$kiosk_id;
			if(($current_kiosk !=$prev_kiosk) && $prev_kiosk!='')
			{
				
				echo "<tr ><td align='right' colspan='2'><b>KIOSK Total:</b></td>
				<td align='right'><b>".${total_no_transactions.$prev_kiosk}."</b></td>
				<td align='right'><b>".number_format(${total_amount.$prev_kiosk},2)."</b></td>
				<td colspan='4'></td>
				</tr>";
			}
		if(!in_array($kiosk_id,$kiosk_id_array))
		{
			echo "<tr ><td align='center' colspan='8'><b>***KIOSK-".$kiosk_id."***</b></td></tr>";
			array_push($kiosk_id_array,$kiosk_id);
		}
			$grantotal_no_transaction=$grantotal_no_transaction+$no_of_transactions;
			$grantotal_amount=$grantotal_amount+$amount;
			echo "<tr>
					<td>".$count."</td>
					<td>".$kiosk_date."</td>
					<td align='right'>".$no_of_transactions."</td>
					<td align='right'>".$amount."</td>
					<td>".$CCC ."</td>
					<td>".$division ."</td>
					<td>".$region ."</td>
					<td>".$zone ."</td>
				  </tr>";
			if($count==$count_kiosk_details)
			{
				echo "<tr ><td align='right' colspan='2'><b>KIOSK Total:</b></td>
					<td align='right'><b>".${total_no_transactions.$kiosk_id}."</b></td>
					<td align='right'><b>".number_format(${total_amount.$kiosk_id},2)."</b></td>
					<td colspan='4'></td>
				</tr>";
			}
			
			$prev_kiosk=$kiosk_id;
			$count++;
		}
		echo "<tr ><td align='right' colspan='2'><b>Grand Total:</b></td>
				<td align='right'><b>".$grantotal_no_transaction."</b></td>
				<td align='right'><b>".number_format($grantotal_amount,2)."</b></td>
				<td colspan='4'></td>
			</tr>";
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



