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
	$emp_condition = " AND WP.receive_emp_code IN('".$employee."') ";
}
if($start_date!='' && $end_date!='')
{
	$date_condition=" AND SUBSTRING(WP.receive_date,1,10) >= '".$start_date."' 
		AND SUBSTRING(WP.receive_date,1,10) <='".$end_date."'";
}
else
{
	$date_condition='';
}
$curdate=date('Y-m-d');
	$sql_sis_visibility = "SELECT * FROM sis_app_visibility";
	$res_sis_visibility = mysql_query($sql_sis_visibility);
	$countsis=mysql_num_rows($res_sis_visibility);
	if($countsis >0){
		$count = 1;
		?>
		<table border="1" style="border-collapse:collapse;" class="border" width="70%" align="center">
          <tr class="TDHEAD_SUB">
          	<td colspan="10" align="center">APP Visibility</td>
          </tr>
		  <tr class="TDHEAD" align="center">
			<td width="8%">SI</td>
            <td width="">SIS Parameter</td>
			<td width="12%">ROE L2</td>
            <td width="12%">ROE L3 & ABOVE</td>
            <td width="12%">NE L2</td>
            <td width="12%">NE L3 & ABOVE</td>
		  </tr>
		<?php
		while($row_sis_visibility = mysql_fetch_array($res_sis_visibility)){
			$sis_parameter = $row_sis_visibility['sis_parameter'];
			$ROE_L2 = $row_sis_visibility['ROE_L2'];
			$ROE_L3_above = $row_sis_visibility['ROE_L3_above'];
			$NE_L2 = $row_sis_visibility['NE_L2'];
			$NE_L3_above = $row_sis_visibility['NE_L3_above'];
			echo "<tr>
					<td >".$count."</td>
					<td >".$sis_parameter."</td>
					<td >".$ROE_L2."</td>
					<td >".$ROE_L3_above."</td>
					<td >".$NE_L2."</td>
					<td >".$NE_L3_above."</td>
				  </tr>";
			$count++;
		}
		?>
        </table>
        <?php
	}
	else{
		echo "<span style=\"color:red; font-weight:bold;\">No Record Found</span>";
	}
?>
