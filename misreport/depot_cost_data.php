<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

if($_GET['type'] == 'today'){
	$today = date('Y-m-d');
	$condition = " SUBSTRING(DC.datetime,1,10)='".$today."' ";
}
else if($_GET['type'] == 'mtd'){
	@$current_date = date('Y-m-d');
	$month = explode("-",$current_date);
	$year = $month[0];
	$month = $month[1];
	$condition = " SUBSTRING(DC.datetime,1,4)='".$year."' AND SUBSTRING(DC.datetime,6,2)='".$month."' ";
}
else if($_GET['type'] == 'custom'){
	$start_date = $_GET['start_date'];
	$end_date = $_GET['end_date'];
	$condition = " (SUBSTRING(DC.datetime,1,10) BETWEEN '".$start_date."' AND '".$end_date."') ";
}
$count = 1;
$plantnamearray = array();
$sql_check_record = "SELECT PM.prod_desc,PM.dns_prod_code,BM.branch_name,PGM.product_group_name,DC.depot_cost,DATE_FORMAT(SUBSTRING(DC.datetime,1,10),'%d-%m-%Y') as dc_date, SUBSTRING(DC.datetime,12) as dc_time FROM depot_cost DC, product_group_master PGM,product_master PM,branch_master BM WHERE ".$condition." AND PM.product_group_code=PGM.product_group_code AND PM.dns_prod_code=DC.dns_prod_code AND PM.branch_code=DC.branch_code AND BM.branch_code=PM.branch_code ORDER BY SUBSTRING(DC.datetime,1,19) DESC";
$res_check_record = mysqli_query($link,$sql_check_record);
$total_rows = mysqli_num_rows($res_check_record);

if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="100%" cellpadding="4" >
      <tr class="TDHEAD" align="center" id="head_main">
      	<td>SI</td>
        <td>Date</td>
        <td>Time</td>
        <td>Depot</td>
        <td>Group Name</td>
        <td>Product Code</td>
        <td>Product</td>
        <td>Depot cost</td>
      </tr>
    <?php
	$res_check_record = mysqli_query($link,$sql_check_record);
	
	while($row_check_record = mysqli_fetch_assoc($res_check_record)){
		$prod_desc = $row_check_record['prod_desc'];
		$dns_prod_code=$row_check_record['dns_prod_code'];
		$branch_name = $row_check_record['branch_name'];
		$product_group_name = $row_check_record['product_group_name'];
		$depot_cost = $row_check_record['depot_cost'];
		$dc_date = $row_check_record['dc_date'];
		$dc_time = $row_check_record['dc_time'];
		
		/*if(!in_array($plant_name,$plantnamearray)){
			array_push($plantnamearray,$plant_name);
			echo "<tr class=\"TDHEAD_SUB\"><td align=\"center\" colspan=\"5\">".$plant_name."</td></tr>";
		}*/
		echo "<tr id=\"tab\">
				<td>".$count."</td>
				<td>".$dc_date."</td>
				<td>".$dc_time."</td>
				<td>".$branch_name."</td>
				<td>".$product_group_name."</td>
				<td>".$dns_prod_code."</td>
				<td>".$prod_desc."</td>
				<td align=\"right\">".number_format($depot_cost,2)."</td>
			  </tr>";
	$count++;
	}
	echo "</table>";
}
else{
	echo "<strong><font color=\"red\">No records found</font></strong>";
}
mysqli_close($link);
?>