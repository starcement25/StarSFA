<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

  $mall_id = $_REQUEST['mall_id'];	
//$mall_name = $_REQUEST['mall_name'];
	 $area = $_REQUEST['area'];
     $city = $_REQUEST['city'];
  $pincode = $_REQUEST['pincode'];
    $state = $_REQUEST['state'];
  $country = $_REQUEST['country'];
  $stdcode = $_REQUEST['stdcode'];
$emp_array = $_REQUEST['emparray'];

//print_r($emp_array);

$sql_delete_mall_emp_relation_data = "DELETE FROM mall_emp_relation WHERE mall_id = '".$mall_id."'";
$res_delete_mall_emp_relation_data = mysql_query($sql_delete_mall_emp_relation_data);

if(!empty($emp_array))
{
	foreach($emp_array as $val)
	{
		$sql_insert_emall_emp_relation_data = "INSERT INTO mall_emp_relation SET mall_id = '".$mall_id."', emp_code = '".$val."'";
		$res_insert_emall_emp_relation_data = mysql_query($sql_insert_emall_emp_relation_data);
	}
}

$sql_update_mall = "UPDATE mall_master SET  address = '".$address."', landmark = '".$landmark."', area = '".$area."', city = '".$city."', pincode = '".$pincode."', state = '".$state."', country = '".$country."', std_code = '".$stdcode."', closed_on = '".$closed_on."', type = 'hi-street' WHERE mall_id = '".$mall_id."'";
$res_update_mall = mysql_query($sql_update_mall);
echo "Data Updated Succesfully";
?>

