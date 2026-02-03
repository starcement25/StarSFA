<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$dns_code = $_REQUEST['dns_code'];
$branch = $_REQUEST['branch'];
$route_name = $_REQUEST['route_name'];
$emp_code_array = $_REQUEST['emp_array'];

$sqlmaxroutecode="SELECT MAX( CAST( SUBSTRING( route_code, 4, length( route_code ) -3 ) AS UNSIGNED ) ) AS new_route_code FROM route_master WHERE route_code NOT LIKE '%N%'";
$rsmaxroutecode=mysqli_query($link,$sqlmaxroutecode);
$rowmaxroutecode=mysqli_fetch_assoc($rsmaxroutecode);
$new_route_code=$rowmaxroutecode['new_route_code'];

$max_route_code='RT/'.($new_route_code+1);

foreach($emp_code_array as $val){
$sql_insert = "INSERT INTO route_master SET 
								`route_code` = '".$max_route_code."', 
							`dns_route_code` = '".$dns_code."', 
								`route_name` = '".$route_name."', 
								  `emp_code` = '".$val."',
							 `download_time` = current_timestamp";
$res_insert = mysqli_query($link,$sql_insert);
}
echo "Data inserted successfully";

mysqli_close($link);
?>