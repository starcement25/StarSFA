<?php

/* ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);*/

ob_start();
session_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require ("attribute_selection.php");
require("include/dbcon.php");
if($_SESSION['admin_login']=="")  		header("product:index.php");

if($_SESSION['admin_login']=="admin"){
	$emp_hierarchy='';
	$emp_hierarchy_condition='';
	$emp_hierarchy_condition_one='';
}
else
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition_one=' AND EM.emp_code IN('.$emp_hierarchy.')';
}
$employee = $_REQUEST['employee'];
$employee_condition = " AND EM.emp_code IN(".$employee.") ";

$sql_emp = "SELECT EM.* FROM employee_master EM,changepassword CH WHERE CH.emp_code=EM.emp_code AND EM.acedns='Y' AND EM.app_access='Y' AND EM.emp_code<>'C0007'".$employee_condition." ORDER BY EM.emp_code ASC";
$res_emp = mysqli_query($link,$sql_emp);
$total_rows = mysqli_num_rows($res_emp);
$count = 1;
if($total_rows>0){
	?>
    <table class="border" width="100%" border="1" style="border-collapse:collapse; padding:6px;">
    <tr class="TDHEAD"> 
        <td>Sl</td>
        <td><?php echo strtoupper($_SESSION['nick_name']); ?> Emp code</td>
        <td>Emp code</td>
        <td>Employee</td>
        <td>Reporting To</td>
        <td>Phone</td>
        <td>Location</td>
        <td>App Version</td>
        <td>DB Version</td>
        <td>Access</td>
        <td>Device Clear</td>
    </tr>
    <?php
	$export_table_data = "<table class=\"border\" width=\"100%\" border=\"1\" style=\"border-collapse:collapse; padding:6px;\">
    <tr class=\"TDHEAD\"> 
        <td>Sl</td>
		<td>".strtoupper($_SESSION['nick_name'])." Emp code</td>
        <td>Emp code</td>
		<td>Employee</td>
		<td>Reporting To</td>
        <td>Phone</td>
        <td>Location</td>
        <td>State</td>
        <td>Zone</td>
		<td>App Version</td>
        <td>DB Version</td>
    </tr>";
	
	$res_emp = mysqli_query($link,$sql_emp);
	while($row_emp = mysqli_fetch_assoc($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$dns_emp_code = $row_emp['dns_emp_code'];
		$emp_name = $row_emp['emp_name'];
		$phone = $row_emp['phone_no'];
		$hq = $row_emp['HQ'];
		$state = $row_emp['state'];
		$zone = $row_emp['zone'];
		$reporting_to = $row_emp['reporting_to'];
		
		$emp_string = '';
		$emp_code_string = '';
		if($reporting_to == ''){
			$emp_string = '';
			$emp_code_string = '';
		}
		else{
			$emp_array = array();
			if(strpos($reporting_to,",") != TRUE){
				array_push($emp_array,$reporting_to);
			}
			else{
				$emp_array = explode(",",$reporting_to);
			}
			
			foreach($emp_array as $emp_val){
				$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$emp_val."'";
				$res_emp_name = mysqli_query($link,$sql_emp_name);
				while($row_emp_name = mysqli_fetch_assoc($res_emp_name)){
					$emp_string .= $row_emp_name['emp_name'].",";
					$emp_code_string .= $emp_val.",";
				}
			}
			$emp_string = rtrim($emp_string,",");
			$emp_code_string = rtrim($emp_code_string,",");
		}
		
		$sql_device_id = "SELECT deviceid FROM changepassword WHERE emp_code = '".$emp_code."'";
		$res_device_id = mysqli_query($link,$sql_device_id);
		$device_id_check = mysqli_num_rows($res_device_id);
		if($device_id_check>0)
			$clear_allocation = "<a href=\"#\" onclick=\"clear_allocation('$emp_code');\" style=\"color:green;\">Clear Allocation</a>";
		else
			$clear_allocation = "--------";
		
		$provide_access = "<a href=\"employee_provide_access.php?emp_code=$emp_code\" target=\"_blank\" style=\"color:red;\">Provide Access</a>";
		
		$res_device_id = mysqli_query($link,$sql_device_id);
		$row_device_id = mysqli_fetch_assoc($res_device_id);
		$device_id = $row_device_id['deviceid'];
		
		$sql_app_version = "SELECT version_code FROM app_updation WHERE device_id = '".$device_id."'";
		$res_app_version = mysqli_query($link,$sql_app_version);
		$row_app_version = mysqli_fetch_assoc($res_app_version);
		$app_version = $row_app_version['version_code'];
		
		$sql_db_version = "SELECT db_version_code FROM table_structure_updation WHERE device_id = '".$device_id."' AND emp_code='".$emp_code."'";
		$res_db_version = mysqli_query($link,$sql_db_version);
		$row_db_version = mysqli_fetch_assoc($res_db_version);
		$db_version = $row_db_version['db_version_code'];
			
		echo "<tr> 
				<td>".$count."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_code."</td>
				<td><a href='change_emp_name.php?emp_code=$emp_code' style='text-decoration: none'>&nbsp;&nbsp;<img src='images/edit_icon.gif'></a>&nbsp;".$emp_name."</td>
				<td>".$emp_string."</td>
				<td>".$phone."</td>
				<td>".$hq."</td>
				<td align=\"right\">".$app_version."</td>
				<td align=\"right\">".$db_version."</td>
				<td>".$provide_access."</td>
				<td>".$clear_allocation."</td>
			</tr>";
				
		$export_table_data .= "<tr> 
					<td>".$count."</td>
					<td>".$dns_emp_code."</td>
					<td>".$emp_code."</td>
					<td>".$emp_name."</td>
					<td>".$reporting_to."</td>
					<td>".$phone."</td>
					<td>".$hq."</td>
					<td>".$state."</td>
					<td>".$zone."</td>
					<td >".$app_version."</td>
					<td >".$db_version."</td>
				</tr>";
		$count++;
	}
	echo "</table>";
	$export_table_data .= "</table>";
	echo "<div id=\"display_details\" hidden>".$export_table_data."</div>";
}
else{
	echo "No records found";
}
mysqli_close($link);
?>