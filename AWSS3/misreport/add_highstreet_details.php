<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>
<table border="1" style="border-collapse:collapse;" width="40%" cellpadding="5px" class="border">
	<tr class="TDHEAD">
    	<td align="center" colspan="2">High Street Details</td>
    </tr>
	<!--tr>
    	<td>High Street Name</td>
        <td><input name="input_highstreet_name" type="text" id="input_highstreet_name" /></td>
    </tr-->
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