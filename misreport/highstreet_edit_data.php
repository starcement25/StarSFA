<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

$mall_id = $_REQUEST['mall_id'];

$sql_select_mall = "SELECT * FROM mall_master WHERE mall_id = '".$mall_id."'";
$res_select_mall = mysqli_query($link,$sql_select_mall);
$row_select_mall = mysqli_fetch_assoc($res_select_mall);


  	 $area = $row_select_mall['area'];
     $city = $row_select_mall['city'];
  $pincode = $row_select_mall['pincode'];
    $state = $row_select_mall['state'];
  $country = $row_select_mall['country'];
  $stdcode = $row_select_mall['std_code'];

$assigned_emp = array();
$sql_select_assigned_emp = "SELECT emp_code FROM mall_emp_relation WHERE mall_id = '".$mall_id."'";
$res_select_assigned_emp = mysqli_query($link,$sql_select_assigned_emp);
while($row_select_assigned_emp = mysqli_fetch_assoc($res_select_assigned_emp))
{
	$assigned_emp[ ] = $row_select_assigned_emp['emp_code'];
}

//print_r($assigned_emp);
?>

<table border="1" style="border-collapse:collapse;" width="40%" cellpadding="5px" class="border">
	<tr class="TDHEAD">
    	<td align="center" colspan="2">High Street Details</td>
    </tr>
	<!--tr>
    	<td>High Street Name</td>
        <td><input name="highstreet_name" type="text" id="highstreet_name" value="<?php //echo $mall_name; ?>" /></td>
    </tr-->
    <tr>
    	<td>Area</td>
        <td><input name="area" type="text" id="area" value="<?php echo $area; ?>" /></td>
    </tr>
    <tr>
    	<td>City</td>
        <td><input name="city" type="text" id="city" value="<?php echo $city; ?>" /></td>
    </tr>
    <tr>
    	<td>Pincode</td>
        <td><input name="pincode" type="text" id="pincode" value="<?php echo $pincode; ?>" /></td>
    </tr>
    <tr>
    	<td>State</td>
        <td><input name="state" type="text" id="state" value="<?php echo $state; ?>" /></td>
    </tr>
    <tr>
    	<td>Country</td>
        <td><input name="country" type="text" id="country" value="<?php echo $country; ?>" /></td>
    </tr>
    <tr>
    	<td>Area/STD Code</td>
        <td><input name="std_code" type="text" id="std_code" value="<?php echo $stdcode; ?>" /></td>
    </tr>
    <tr>
    	<td>Assigned To</td>
        <td><?php
				$sql_select_emp = "SELECT emp_code, emp_name FROM employee_master";
				$res_select_emp = mysqli_query($link,$sql_select_emp);
				while($row_select_emp = mysqli_fetch_assoc($res_select_emp))
				{
					$flag = 1;
					foreach($assigned_emp as $assigned_emp_value)
					{
						if($row_select_emp['emp_code'] == $assigned_emp_value)
						{
							echo $row_select_emp['emp_name'].":<input name=\"emp_checked[]\" type=\"checkbox\" value=\"$row_select_emp[emp_code]\" class=\"chk\" checked />";
							$flag = 0;
							break;
						}
					}
					if($flag == 1)
					{
						echo $row_select_emp['emp_name'].":<input name=\"emp_checked[]\" type=\"checkbox\" value=\"$row_select_emp[emp_code]\" class=\"chk\"  />";
					}
				}
        	?>
        </td>
    </tr>
    <tr>
    	<td><input type="hidden" name="mall_id" id="mall_id" value="<?php echo $mall_id; ?>" /></td>
        <td align="left"><input name="update" type="button" value="Update" onclick="return update_mall_data();" /></td>
    </tr>
</table>