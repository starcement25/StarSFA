<?php
ob_start();
session_start();
require("adminUtils.php");

$customer_Array = $_REQUEST['customer_Array'];

	$cust_desc_string='';
	$datestring=gmdate('dmYHis',strtotime('+330 minute')).microtime();
	$map_id='ADE'.$datestring;
	$sever_url="http://".$_SERVER['SERVER_NAME']."/loadlinkedmap.php?mapid=".$map_id;
	foreach($customer_Array as $customer_id){
		$sqlsel="SELECT base_latt,base_longi,customer_name,cust_type,dns_customer_code,route_code,phone_no FROM 
					customer_master WHERE customer_code='".$customer_id."'";
		$rssel=mysqli_query($link,$sqlsel);
		$rowsel=mysqli_fetch_assoc($rssel);
		$customer_name=$rowsel['customer_name'];
		$cust_type=$rowsel['cust_type'];
		$dns_customer_code=$rowsel['dns_customer_code'];
 		$route_code=$rowsel['route_code'];
		$phone_no=$rowsel['phone_no'];
		$base_latt=$rowsel['base_latt'];
		$base_longi=$rowsel['base_longi'];
		$cust_desc_string .=$customer_name.';'.$base_latt.';'.$base_longi.';'.$dns_customer_code.';'.$route_code.';'.$phone_no.';'.$cust_type.'#';
	}
	$cust_desc_string=substr($cust_desc_string,0,-1);
		$sql_insert_new_map = "INSERT INTO market_survey_map_link SET map_id = '".$map_id."', map_link = '".$cust_desc_string."', 
								 create_date_time = current_timestamp";
		$res_insert_new_tagging = mysqli_query($link,$sql_insert_new_map);
	echo "<b>MAP Link creation done. Map link is - ".$sever_url.'</b>';
?>