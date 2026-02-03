<?php
ob_start();
	session_start();
	error_reporting(0);
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

  	     $mall_id = $_REQUEST['mall_id'];	
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

/*--------------------------> Delete existing data from mall_emp_relation table <----------------------------------*/
$sql_delete_mall_emp_relation_data = "DELETE FROM mall_emp_relation WHERE mall_id = '".$mall_id."'";
$res_delete_mall_emp_relation_data = mysqli_query($link,$sql_delete_mall_emp_relation_data);

/*--------------------------> Insert data in mall_emp_relation table <----------------------------------*/
if(!empty($emp_array))
{
	foreach($emp_array as $val)
	{
		$sql_insert_emall_emp_relation_data = "INSERT INTO mall_emp_relation SET mall_id = '".$mall_id."', emp_code = '".$val."'";
		$res_insert_emall_emp_relation_data = mysqli_query($link,$sql_insert_emall_emp_relation_data);
	}
}
/*--------------------------> Mall Update Query <----------------------------------*/
$sql_update_mall = "UPDATE mall_master SET 
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
							general_facility = '".addslashes($general_facility_data)."' 
					WHERE mall_id = '".$mall_id."'";
$res_update_mall = mysqli_query($link,$sql_update_mall);
echo "Data Updated Succesfully";
?>

