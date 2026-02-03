<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>
<table border="1" style="border-collapse:collapse;" width="40%" cellpadding="5px" class="border">
	<tr class="TDHEAD">
    	<td align="center" colspan="2">Mall Details</td>
    </tr>
	<tr>
    	<td>Mall Name</td>
        <td><input name="input_mall_name" type="text" id="input_mall_name" /></td>
    </tr>
    <tr>
    	<td>Street Number</td>
        <td><input name="input_street_number" type="text" id="input_street_number" /></td>
    </tr>
    <tr>
    	<td>Address</td>
        <td><input name="input_address" type="text" id="input_address" /></td>
    </tr>
    <tr>
    	<td>Landmark</td>
        <td><input name="input_landmark" type="text" id="input_landmark"  /></td>
    </tr>
    <tr>
    	<td>Area</td>
        <td><input name="input_area" type="text" id="input_area"  /></td>
    </tr>
    <tr>
    	<td>City</td>
        <td><input name="input_city" type="text" id="input_city"  /></td>
    </tr>
    <tr>
    	<td>Pincode</td>
        <td><input name="input_pincode" type="text" id="input_pincode"  /></td>
    </tr>
    <tr>
    	<td>State</td>
        <td><input name="input_state" type="text" id="input_state"  /></td>
    </tr>
    <tr>
    	<td>Country</td>
        <td><input name="input_country" type="text" id="input_country"  /></td>
    </tr>
    <tr>
    	<td>Area/STD Code</td>
        <td><input name="input_stdcode" type="text" id="input_stdcode"  /></td>
    </tr>
    <tr>
    	<td>Closed on</td>
        <td><input name="input_closed_on" type="text" id="input_closed_on"  /></td>
    </tr>
    <tr>
    	<td>Any upcoming/current event in the mall</td>
        <td><input type="text" name="upcoming_event" id="upcoming_event" /></td>
    </tr>
    <tr>
    	<td>General Facility</td>
        <td><?php $sql_select_general_facility = "SELECT facility FROM facility ORDER BY facility ASC"; 
				  $res_select_general_facility = mysql_query($sql_select_general_facility);
				  while($row_general_faclity = mysql_fetch_array($res_select_general_facility)){
					  echo $row_general_faclity['facility'].":<input name=\"genralfacility_checked[]\" type=\"checkbox\" value=\"$row_general_faclity[facility]\" class=\"generalfacility_chk\"  />";
				  }
		?></td>
    </tr>
    <tr>
    	<td>Assigned To</td>
        <td><?php
				$sql_select_emp = "SELECT emp_code, emp_name FROM employee_master";
				$res_select_emp = mysql_query($sql_select_emp);
				while($row_select_emp = mysql_fetch_array($res_select_emp))
				{
					echo $row_select_emp['emp_name'].":<input name=\"emp_checked[]\" type=\"checkbox\" value=\"$row_select_emp[emp_code]\" class=\"input_chk\"  />";
				}
			?>
        </td>
    </tr>
    <tr>
    	<td></td>
        <td align="left"><input name="add" type="button" value="Add" onclick="return insert_mall_data();" /></td>
    </tr>
</table>