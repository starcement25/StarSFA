<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

if($_GET['type'] == 'today'){
	$today = date('Y-m-d');
	$condition = " SUBSTRING(PD.datetime,1,10)='".$today."' ";
}
else if($_GET['type'] == 'mtd'){
	@$current_date = date('Y-m-d');
	$month = explode("-",$current_date);
	$year = $month[0];
	$month = $month[1];
	$condition = " SUBSTRING(PD.datetime,1,4)='".$year."' AND SUBSTRING(PD.datetime,6,2)='".$month."' ";
}
else if($_GET['type'] == 'custom'){
	$start_date = $_GET['start_date'];
	$end_date = $_GET['end_date'];
	$condition = " (SUBSTRING(PD.datetime,1,10) BETWEEN '".$start_date."' AND '".$end_date."') ";
}
$count = 1;

$plantnamearray = array();
$sql_check_record = "SELECT PD.plant_name, PD.loose_rate_ton, PGM.product_group_name, DATE_FORMAT(SUBSTRING(PD.datetime,1,10),'%d-%m-%Y') as pd_date, SUBSTRING(PD.datetime,12) as pd_time FROM pricing_detials PD, product_group_master PGM WHERE ".$condition." AND PD.product_group_code=PGM.product_group_code ORDER BY SUBSTRING(PD.datetime,1,10) DESC, PD.plant_name ASC, PGM.product_group_name ASC";
$res_check_record = mysqli_query($link,$sql_check_record);
$total_rows = mysqli_num_rows($res_check_record);

if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="100%" cellpadding="4" id="maintable" >
      <tr class="TDHEAD" align="center">
      	<td align="center"><div style="position:absolute; background:#A92A61; width:inherit;">SI</div></td>
        <td align="center"><div style="position:absolute; background:#A92A61; width:inherit;">Date</div></td>
        <td align="center"><div style="position:absolute; background:#A92A61; width:inherit;">Time</div></td>
        <td align="center"><div style="position:absolute; background:#A92A61; width:inherit;">Group Name</div></td>
        <td align="center"><div style="position:absolute; background:#A92A61; width:inherit;">Loose Rate</div></td>
      </tr>
      <tr class="TDHEAD" align="center">
      <td colspan="5">&nbsp;</td>
      </tr>
      <tr class="TDHEAD" align="center">
      <td colspan="5">&nbsp;</td>
      </tr>
      
    <?php
	$res_check_record = mysqli_query($link,$sql_check_record);
	while($row_check_record = mysqli_fetch_assoc($res_check_record)){
		$plant_name = $row_check_record['plant_name'];
		$loose_rate_ton = $row_check_record['loose_rate_ton'];
		$product_group_name = $row_check_record['product_group_name'];
		$pd_date = $row_check_record['pd_date'];
		$pd_time = $row_check_record['pd_time'];
		
		if(!in_array($plant_name,$plantnamearray)){
			array_push($plantnamearray,$plant_name);
			echo "<tr class=\"TDHEAD_SUB\"><td align=\"center\" colspan=\"5\">".$plant_name."</td></tr>";
		}
		echo "<tr id=\"tab".$count."\">
				<td>".$count."</td>
				<td>".$pd_date."</td>
				<td>".$pd_time."</td>
				<td>".$product_group_name."</td>
				<td align=\"right\">".$loose_rate_ton."</td>
			  </tr>";
	$count++;
	}
	echo "</table>";
}
else{
	echo "<strong><font color=\"red\">No records found</font></strong>";
}

?>