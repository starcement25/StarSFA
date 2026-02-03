<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

	 $area = $_REQUEST['area'];
     $city = $_REQUEST['city'];
  $pincode = $_REQUEST['pincode'];
  	$state = $_REQUEST['state'];
  $country = $_REQUEST['country'];
  $stdcode = $_REQUEST['stdcode'];
$emp_array = $_REQUEST['emparray'];

//print_r($emp_array);

$sql_select_max_id = "SELECT MAX(mall_id) FROM mall_master WHERE mall_id LIKE 'H%'";
$res_select_max_id = mysql_query($sql_select_max_id);
$row_select_max_id = mysql_fetch_array($res_select_max_id);
$maxidcheck = $row_select_max_id['MAX(mall_id)'];
if($maxidcheck != NULL)
{
	$res_select_max_id = mysql_query($sql_select_max_id);
	$row_select_max_id = mysql_fetch_array($res_select_max_id);
	$max_id = $row_select_max_id['MAX(mall_id)'];
	$max_id++;
	$sql_update_mall = "INSERT INTO mall_master SET mall_id = '".$max_id."', address = '".$address."', landmark = '".$landmark."', area = '".$area."', city = '".$city."', pincode = '".$pincode."', state = '".$state."', country = '".$country."', std_code = '".$stdcode."', closed_on = '".$closed_on."', type = 'hi-street', date_time = current_timestamp";
	$res_update_mall = mysql_query($sql_update_mall);
	
	if(!empty($emp_array))
	{
		foreach($emp_array as $val)
		{
			$sql_insert_emall_emp_relation_data = "INSERT INTO mall_emp_relation SET mall_id = '".$max_id."', emp_code = '".$val."'";
			$res_insert_emall_emp_relation_data = mysql_query($sql_insert_emall_emp_relation_data);
		}
	}
	
	echo "Data Inserted Succesfully";
}
else
{
	$max_id = 'H0001';
	
	$sql_update_mall = "INSERT INTO mall_master SET mall_id = '".$max_id."', address = '".$address."', landmark = '".$landmark."', area = '".$area."', city = '".$city."', pincode = '".$pincode."', state = '".$state."', country = '".$country."', std_code = '".$stdcode."', closed_on = '".$closed_on."', type = 'hi-street', date_time = current_timestamp";
	$res_update_mall = mysql_query($sql_update_mall);
	
	if(!empty($emp_array))
	{
		foreach($emp_array as $val)
		{
			$sql_insert_emall_emp_relation_data = "INSERT INTO mall_emp_relation SET mall_id = '".$max_id."', emp_code = '".$val."'";
			$res_insert_emall_emp_relation_data = mysql_query($sql_insert_emall_emp_relation_data);
		}
	}
	
	echo "Data Inserted Succesfully";
}
?>

