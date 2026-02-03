<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

$mall_id = $_REQUEST['mall_id'];

$sql_select_mall = "SELECT * FROM mall_master WHERE mall_id = '".$mall_id."'";
$res_select_mall = mysqli_query($link,$sql_select_mall);
$row_select_mall = mysqli_fetch_assoc($res_select_mall);

	   $mall_name = $row_select_mall['mall_name'];
  	     $address = $row_select_mall['address'];
		 $street_number = $row_select_mall['street_number'];
 	    $landmark = $row_select_mall['landmark'];
	 	    $area = $row_select_mall['area'];
     	    $city = $row_select_mall['city'];
  	     $pincode = $row_select_mall['pincode'];
    	   $state = $row_select_mall['state'];
  	     $country = $row_select_mall['country'];
  	     $stdcode = $row_select_mall['std_code'];
	   $closed_on = $row_select_mall['closed_on'];
  $upcoming_event = $row_select_mall['upcoming_event'];
$general_facility = $row_select_mall['general_facility'];

$general_facility_array = explode(",",$general_facility);

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
    	<td align="center" colspan="2">Mall Details</td>
    </tr>
	<tr>
    	<td>Mall Name</td>
        <td><input name="mall_name" type="text" id="mall_name" value="<?php echo $mall_name; ?>" /></td>
    </tr>
    <tr>
    	<td>Street Number</td>
        <td><input name="street_number" type="text" id="street_number"  value="<?php echo $street_number; ?>" /></td>
    </tr>
    <tr>
    	<td>Address</td>
        <td><input name="address" type="text" id="address" value="<?php echo $address; ?>" /></td>
    </tr>
    <tr>
    	<td>Landmark</td>
        <td><input name="landmark" type="text" id="landmark" value="<?php echo $landmark; ?>" /></td>
    </tr>
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
    	<td>Closed on</td>
        <td><input name="closed_on" type="text" id="closed_on" value="<?php echo $closed_on; ?>" /></td>
    </tr>
    <tr>
    	<td>Any upcoming/current event in the mall</td>
        <td><input type="text" name="upcoming_event" id="upcoming_event" value="<?php echo $upcoming_event; ?>" /></td>
    </tr>
    <tr>
    	<td>General Facility</td>
        <td>
        <?php
		$sql_select_general_facility = "SELECT facility FROM facility ORDER BY facility ASC"; 
		$res_select_general_facility = mysqli_query($link,$sql_select_general_facility);
		while($row_general_faclity = mysqli_fetch_assoc($res_select_general_facility)){
			$gen_flag = 1;
			foreach($general_facility_array as $value){
				if($row_general_faclity['facility'] == $value){
					echo $row_general_faclity['facility'].":<input name=\"generalfacility_checked[]\" type=\"checkbox\" value=\"$row_general_faclity[facility]\" class=\"generalfacility_chk\" checked />";
							$gen_flag = 0;
							break;
				}
			}
			if($gen_flag == 1){
				echo $row_general_faclity['facility'].":<input name=\"generalfacility_checked[]\" type=\"checkbox\" value=\"$row_general_faclity[facility]\" class=\"generalfacility_chk\" />";
			}
		}
		?>
        </td>
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