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
	if(strtoupper($_SESSION['admin_login'])=="ADMIN"){
		$emp_condition = '';
	}
	else{
		$emp_hierarchy_value=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_condition = " AND RP.emp_code IN(".$emp_hierarchy_value.") ";
	}
}
else{
	$emp_condition = " AND RP.emp_code IN('".$employee."') ";
}

/*$sql_order_date = "SELECT SUBSTRING(order_no,-14,8) AS no_order_date FROM order_header WHERE ".$order_condition." (SUBSTRING(order_no,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') AND order_no LIKE 'N%'";
$res_order_date = mysqli_query($link,$sql_order_date);
while($row_order_date = mysqli_fetch_assoc($res_order_date)){
	$no_order_date = $row_order_date['no_order_date'];
	if(!in_array($no_order_date,$date_array))
		array_push($date_array,$no_order_date);
}

$sql_no_payment = "SELECT SUBSTRING(receipt_no,-14,8) AS no_payment_date FROM payment_header WHERE ".$payment_condition." (SUBSTRING(receipt_no,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') AND receipt_no LIKE 'N%'";
$res_no_payment = mysqli_query($link,$sql_no_payment);
while($row_no_payment = mysqli_fetch_assoc($res_no_payment)){
	$no_payment_date = $row_no_payment['no_payment_date'];
	if(!in_array($no_payment_date,$date_array))
		array_push($date_array,$no_payment_date);
}

sort($date_array);*/

//if(!empty($date_array)){
	?>
    <table class="border" width="100%" style="border-collapse:collapse;" border="1">
      <tr class="TDHEAD">
      	<td>Sl No</td>
      	<td>SR Name</td>
        <td>SO Name</td>
      	<td>Date</td>
        <td>TC</td>
        <td>PC</td>
        <td>Beat</td>
        <td>DA</td>
        <td>TA</td>
        <td >Total</td>
      </tr>
    <?
	/*$sql_emp_beat = "SELECT * FROM(SELECT BTD.emp_code,BTD.emp_name,BTD.reporting_to_name,BTD.route_code,GROUP_CONCAT(BTD.route_name SEPARATOR ',') AS 	route_name,DATE_FORMAT(SUBSTRING(RP.visit_date,1,10),'%d-%m-%Y') AS visit_date,DATE_FORMAT(SUBSTRING(RP.visit_date,1,10),'%Y-%m-%d') AS visit_date_format,BTD.TA,BTD.DA,MAX(BTD.distance) as max_distance
				FROM beatwise_TA_DA BTD,route_plan RP WHERE RP.emp_code=BTD.emp_code AND RP.route_code=BTD.route_code 
				AND (SUBSTRING(RP.visit_date,1,10) BETWEEN '".$start_date."' AND '".$end_date."') ".$emp_condition." AND BTD.acedns='Y' 
				GROUP BY RP.emp_code,RP.visit_date ORDER BY max_distance ";*/
	/*$sql_emp_beat = "SELECT BTD.emp_code,BTD.emp_name,BTD.reporting_to_name,BTD.route_code,GROUP_CONCAT(BTD.route_name SEPARATOR ',') AS 	route_name,GROUP_CONCAT(BTD.route_code SEPARATOR ',') AS route_code,DATE_FORMAT(SUBSTRING(RP.visit_date,1,10),'%d-%m-%Y') AS visit_date,RP.visit_date AS visit_date_format,BTD.TA,BTD.DA,BTD.distance
				FROM beatwise_TA_DA BTD,route_plan RP WHERE RP.emp_code=BTD.emp_code AND RP.route_code=BTD.route_code 
				AND RP.visit_date >= '".$start_date."' AND RP.visit_date <='".$end_date."' ".$emp_condition." AND BTD.acedns='Y' 
				GROUP BY RP.emp_code,RP.visit_date HAVING BTD.distance=MAX(BTD.distance) 
				ORDER BY BTD.emp_name ASC ";*/
	$sql_emp_beat = "SELECT BTD.emp_code,BTD.emp_name,BTD.reporting_to_name,GROUP_CONCAT(BTD.route_name SEPARATOR ',') AS 	route_name,GROUP_CONCAT(BTD.route_code SEPARATOR ',') AS route_code,DATE_FORMAT(SUBSTRING(RP.visit_date,1,10),'%d-%m-%Y') AS visit_date,RP.visit_date AS visit_date_format,BTD.TA,BTD.DA,BTD.distance
				FROM beatwise_TA_DA BTD,route_plan RP WHERE RP.emp_code=BTD.emp_code AND RP.route_code=BTD.route_code 
				AND RP.visit_date >= '".$start_date."' AND RP.visit_date <='".$end_date."' ".$emp_condition." AND BTD.acedns='Y' 
				GROUP BY RP.emp_code,RP.visit_date ORDER BY BTD.emp_name ASC ";						
	$res_emp_beat = mysqli_query($link,$sql_emp_beat);
	$cnt_emp_beat=mysqli_num_rows($res_emp_beat);
	if($cnt_emp_beat >0){
		$count=1;
	while($row_emp_beat = mysqli_fetch_assoc($res_emp_beat)){
		$emp_code = $row_emp_beat['emp_code'];
		$emp_name = $row_emp_beat['emp_name'];
		$reporting_to_name = $row_emp_beat['reporting_to_name'];
		$route_code = $row_emp_beat['route_code'];
		$route_name = $row_emp_beat['route_name'];
		$visit_date = $row_emp_beat['visit_date'];
		$visit_date_format = $row_emp_beat['visit_date_format'];
		
		$sqlTADA="SELECT TA,DA,distance FROM beatwise_TA_DA WHERE emp_code='".$emp_code."' AND FIND_IN_SET(route_code,'".$route_code."') ORDER BY distance DESC LIMIT 0,1";
		$rsTADA=mysqli_query($link,$sqlTADA);
		$rowTADA=mysqli_fetch_assoc($rsTADA);

		
		$TA=$rowTADA['TA'];
		$DA=$rowTADA['DA'];
		$total=$TA+$DA;
		$total_TA=$total_TA+$TA;
		$total_DA=$total_DA+$DA;
		$total_all=$total_all+$total;
		
		$sql_total_calls = "SELECT COUNT( DISTINCT (CASE WHEN POCM.order_no LIKE 'O%' THEN CONCAT(POCM.customer_code,'^',SUBSTRING(POCM.order_no,-14,8)) END )) AS productive_calls, COUNT( DISTINCT (CASE WHEN POCM.order_no LIKE 'NO%' THEN CONCAT(POCM.customer_code,'^',SUBSTRING(POCM.order_no,-14,8)) END )) AS non_productive_calls FROM location LO LEFT JOIN `prev_order_counting_master` POCM 
	ON POCM.order_no=LO.trans_id  WHERE SUBSTRING(LO.trans_id,1,1) IN('A','N','O') 
	AND SUBSTRING(LO.date,1,10)='".$visit_date_format."' AND LO.emp_code='".$emp_code."'";
		$rs_total_calls=mysqli_query($link,$sql_total_calls);
		$row_total_calls=mysqli_fetch_assoc($rs_total_calls);
		$toatal_calls=$row_total_calls['productive_calls']+$row_total_calls['non_productive_calls'];
		$productive_calls=$row_total_calls['productive_calls'];
		$total_calls_all=$total_calls_all+$toatal_calls;
		$total_calls_productive=$total_calls_productive+$productive_calls;

		
		echo "<tr>
			<td>".$count."</td>
			<td>".$emp_name."</td>
			<td>".$reporting_to_name."</td>
			<td>".$visit_date."</td>
			<td>".$toatal_calls."</td>
			<td>".$productive_calls."</td>
			<td>".$route_name."</td>
			<td align=\"right\">".number_format($DA,2)."</td>
			<td align=\"right\">".number_format($TA,2)."</td>
			<td align=\"right\">".number_format($total,2)."</td>
		  </tr>";
		  $count++;
	  }
	  echo "<tr>
			<td colspan=\"4\" align=\"center\"><b>TOTAL</b></td>
			<td><b>".$total_calls_all."</b></td>
			<td><b>".$total_calls_productive."</b></td>
			<td></td>
			<td align=\"right\"><b>".number_format($total_DA,2)."</b></td>
			<td align=\"right\"><b>".number_format($total_TA,2)."</b></td>
			<td align=\"right\"><b>".number_format($total_all,2)."</b></td>
		  </tr>";
	  
	}
	else{
	echo "<tr><td colspan='8' align='center'>No Records</td></tr>";
}

	?>
    </table>
    <br />
    <br>
<div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
//}
mysqli_close($link);
?>


