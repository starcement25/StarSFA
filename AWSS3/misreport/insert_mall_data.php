<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

	   $mall_name = $_REQUEST['mall_name'];
	   $street_number = $_REQUEST['street_number'];
  	     $address = $_REQUEST['address'];
 	    $landmark = $_REQUEST['landmark'];
	 	    $area = $_REQUEST['area'];
     	    $city = $_REQUEST['city'];
  	     $pincode = $_REQUEST['pincode'];
    	   $state = $_REQUEST['state'];
  	     $country = $_REQUEST['country'];
  	     $stdcode = $_REQUEST['stdcode'];
	   $emp_array = $_REQUEST['emparray'];
	   $closed_on = $_REQUEST['closed_on'];
  $upcoming_event = $_REQUEST['upcoming_event'];
$general_facility = $_REQUEST['general_facility'];

//print_r($emp_array);
/*--------------------------> Comma separated string formation of general facility<-------------------------------*/
foreach($general_facility as $value){
	$general_facility_data .= $value.",";
}
$general_facility_data = rtrim($general_facility_data,",");

/*----------------------------------> Check if any mall exist <------------------------------------*/
$sql_select_max_id = "SELECT MAX(mall_id) FROM mall_master WHERE mall_id LIKE 'M%'";
$res_select_max_id = mysql_query($sql_select_max_id);
$row_select_max_id = mysql_fetch_array($res_select_max_id);
$maxidcheck = $row_select_max_id['MAX(mall_id)'];
if($maxidcheck != NULL){
	$max_id = $row_select_max_id['MAX(mall_id)'];
	$max_id++;
	/*----------------------------------> Insert data in mall_master table <------------------------------------*/
	$sql_update_mall = "INSERT INTO mall_master SET 
										 mall_id = '".$max_id."', 
									   mall_name = '".$mall_name."',
									   street_number = '".$street_number."',  
										 address = '".$address."', 
										landmark = '".$landmark."', 
											area = '".$area."', 
											city = '".$city."', 
										 pincode = '".$pincode."', 
										   state = '".$state."', 
										 country = '".$country."', 
										std_code = '".$stdcode."', 
									   closed_on = '".$closed_on."', 
								  upcoming_event = '".$upcoming_event."', 
											type = 'mall', 
								general_facility = '".addslashes($general_facility_data)."',
									   date_time = current_timestamp";
	$res_update_mall = mysql_query($sql_update_mall);
	
	/*----------------------------------> Insert data in mall_emp_relation table<---------------------------------*/
	if(!empty($emp_array)){
		foreach($emp_array as $val){
			$sql_insert_emall_emp_relation_data = "INSERT INTO mall_emp_relation SET mall_id = '".$max_id."', emp_code = '".$val."'";
			$res_insert_emall_emp_relation_data = mysql_query($sql_insert_emall_emp_relation_data);
		}
	}
	echo "Data Inserted Succesfully";
}/*----------------------------------> If no mall exists <------------------------------------*/
else{
	$max_id = 'M0001';
	/*----------------------------------> Insert data in mall_master table <------------------------------------*/
	$sql_update_mall = "INSERT INTO mall_master SET 
										 mall_id = '".$max_id."', 
									   mall_name = '".$mall_name."', 
									   street_number = '".$street_number."',  
										 address = '".$address."', 
										landmark = '".$landmark."', 
											area = '".$area."', 
											city = '".$city."', 
										 pincode = '".$pincode."', 
										   state = '".$state."', 
										 country = '".$country."', 
										std_code = '".$stdcode."', 
									   closed_on = '".$closed_on."', 
								  upcoming_event = '".$upcoming_event."', 
											type = 'mall', 
								general_facility = '".addslashes($general_facility_data)."',
									   date_time = current_timestamp";
	$res_update_mall = mysql_query($sql_update_mall);
	
	/*----------------------------------> Insert data in mall_emp_relation table<---------------------------------*/
	if(!empty($emp_array)){
		foreach($emp_array as $val){
			$sql_insert_emall_emp_relation_data = "INSERT INTO mall_emp_relation SET mall_id = '".$max_id."', emp_code = '".$val."'";
			$res_insert_emall_emp_relation_data = mysql_query($sql_insert_emall_emp_relation_data);
		}
	}
	echo "Data Inserted Succesfully";
}
?>

