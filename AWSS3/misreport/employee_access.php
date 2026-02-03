<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

?>

<?php
//require("adminUtils.php");
$db = "acedns_".strtoupper($_SESSION['nick_name']);
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","$db");

mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB);
?>

<?php
if($_GET)
	disphtml("getdata();");
else if($_POST)
	disphtml("postdata();");
else 
	disphtml("main();");
	
ob_end_flush();

function main()
{
?>

<center>
<div id="display" style="max-height: 350px; overflow-y: scroll; overflow-x: scroll; width:95%;">
<div style="position:fixed;  width:inherit;">
<div style="position:relative; width:100%;">
<table width="100%" border="1" style="border-collapse:collapse;">
  <tr style="font-weight:bold;">
  	<td align="center" class="TDHEAD">Employee Information</td>
  </tr>
</table>
</div>
<div style="position:relative; width:100%;">
<table width="100%" style="border-collapse:collapse;">
  <tr style="font-weight:bold;" class="TDHEAD_SUB">
    <td style="width:2%;" align="center">SI</td>
    <td style="width:13%;" align="center">SAP Emp Code</td>
    <td style="width:10%;" align="center">Emp Code</td>
    <td style="width:13%;" align="center">Emp Name</td>
    <td style="width:13%;" align="center">Headquarter</td>
    <td style="width:13%;" align="center">Location</td>
    <td style="width:13%;" align="center">Designation</td>
    <td style="width:13%; word-wrap:break-word;" align="center">Provide Access</td>
    <td style="width:13%;" align="center">Clear Allocation</td>
  </tr>
</table>
</div>
</div>
<br />
<br />
<br />
  

<?php
$count = 1;
$sql_employee_access = "SELECT EM.emp_code, EM.dns_emp_code, EM.emp_name, EM.HQ, EM.designation, EM.branch_code, CP.deviceid FROM employee_master EM, changepassword CP WHERE EM.emp_code = CP.emp_code ORDER BY EM.emp_name ASC";
$res_employee_access = mysql_query($sql_employee_access);
echo "<table width=\"100%\" border=\"1\" style=\"border-collapse:collapse;\" cellpadding=\"5px\">";
while($row_employee_access = mysql_fetch_array($res_employee_access))
{
	$branch_name = '';
	$branch_code_array = explode(",",$row_employee_access['branch_code']);
	//$pos = strpos($row_employee_access['branch_code'], ",");
	if(!empty($branch_code_array))
	{
		//$branch_code_array = explode(",",$row_employee_access['branch_code']);
		foreach($branch_code_array as $value)
		{
			$sql_branch_name = "SELECT branch_name FROM branch_master WHERE branch_code='$value'";
			$res_branch_name = mysql_query($sql_branch_name);
			$row_branch_name = mysql_fetch_array($res_branch_name);
			$branch_name .= $row_branch_name['branch_name'].",";
		}
		$branch_name = rtrim($branch_name,",");
	}
	else
	{
		$sql_branch_name = "SELECT branch_name FROM branch_master WHERE branch_code='$row_employee_access[branch_code]'";
		$res_branch_name = mysql_query($sql_branch_name);
		$row_branch_name = mysql_fetch_array($res_branch_name);
		$branch_name = $row_branch_name['branch_name'];
	}
	
	$sql_branch_name = "SELECT branch_name FROM branch_master WHERE branch_code='$row_employee_access[branch_code]'";
	$res_branch_name = mysql_query($sql_branch_name);
	$row_branch_name = mysql_fetch_array($res_branch_name);
	if($row_employee_access['deviceid'] != '')
		$deviceid = "<a href=\"employee_access.php?emp_code=$row_employee_access[emp_code]&allocation=clear\" style=\"color:blue;\">Clear Allocation</a>";
	else
		$deviceid = "--------";
		
	echo "
	<tr>
    <td style=\"width:2%\">$count</td>
    <td style=\"width:14%\">$row_employee_access[dns_emp_code]</td>
	<td style=\"width:14%\">$row_employee_access[emp_code]</td>
    <td style=\"width:14%\">$row_employee_access[emp_name]</td>
    <td style=\"width:14%\">$row_employee_access[HQ]</td>
    <td style=\"width:14%\">$branch_name</td>
    <td style=\"width:14%\">$row_employee_access[designation]</td>
	<td style=\"width:14%\"><a href=\"employee_access.php?emp_code=$row_employee_access[emp_code]&emp_name=$row_employee_access[emp_name]&provide=access\" style=\"color:blue;\">Provide Access</a></td>
    <td style=\"width:13%\">$deviceid</td>
  </tr>";
  
  $count++;
}

echo "</table>";

?>
</div>

<?php
}
function getdata()
{
	if($_GET['emp_code'] && $_GET['allocation'] == "clear")
	{
		$sql_clear_allocation = "UPDATE changepassword SET deviceid='',registrationid='' WHERE emp_code = '$_GET[emp_code]'";
		if(mysql_query($sql_clear_allocation))
		{
			main();
			echo "<font color='#00CC33'><strong>Allocation Cleared Successfully</strong></font>";
		}
	}
	
	else if($_GET['emp_code'] && $_GET['emp_name'] && $_GET['provide'] == 'access')
	{
		$sql_provideaccess = "SELECT * FROM changepassword WHERE emp_code = '$_GET[emp_code]'";
		$res_provideaccess = mysql_query($sql_provideaccess);
		$row_provideaccess = mysql_fetch_array($res_provideaccess);
		
		if($row_provideaccess['status'] == 'true')
			$unblocked = 'selected';
		else
			$unblocked = '';
		
		if($row_provideaccess['status'] == 'false')
			$blocked = 'selected';
		else
			$blocked = '';
		
		
		//echo "Hello";
		echo "<center>";
		echo "
		<form name=\"provide_access\" method=\"POST\" action=\"employee_access.php\" onsubmit=\"return validate();\">
		<input type=\"hidden\" name=\"emp_code\" value=\"$_GET[emp_code]\">
		<table width=\"500px\" style=\"border-collapse:collapse;\" border=\"1\" class=\"border\">
		  <tr class=\"TDHEAD\">
			<td colspan=\"2\" align=\"center\" >Provide Access To $_GET[emp_name]</td>
		  </tr>
		  <tr>
			<td colspan=\"2\">All <font color='red'><strong>*</strong></font> fields are mandatory</td>
		  </tr>
		  <tr>
			<td align=\"right\">Old Password<font color='red'><strong>*</strong></font>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
			<td><input type=\"text\" class=\"INPUT\" name=\"old_pass\" id=\"old_pass\" value=\"$row_provideaccess[oldpassword]\"></td>
		  </tr>
		  <tr>
			<td align=\"right\">New Password<font color='red'><strong>*</strong></font>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
			<td><input type=\"text\" class=\"INPUT\" name=\"new_pass\" id=\"new_pass\" value=\"\"></td>
		  </tr>
		  <tr>
			<td align=\"right\">Confirm New Password<font color='red'><strong>*</strong></font>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
			<td><input type=\"text\" class=\"INPUT\" name=\"confirm_new_pass\" id=\"confirm_new_pass\" value=\"\"></td>
		  </tr>
		  <tr>
			<td align=\"right\">Status&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
			<td>
				<select name=\"status\">
					<option value=\"false\" $blocked>Blocked</option>
					<option value=\"true\" $unblocked>Unblocked</option>
			</td>
		  </tr>
		  <tr>
			<td></td>
			<td align=\"left\">
			<input type=\"submit\" name=\"submit\" value=\"Change\" class=\"inplogin\" />&nbsp;&nbsp;&nbsp;
			<input type=\"button\" name=\"cancel\" id=\"cancel\" value=\"Cancel\" class=\"inplogin\" onclick=\"window.location='employee_access.php';\">
			</td>
		  </tr>
		</table>
		</form>
		";
		echo "</center>";
	}
?>
	<script>
	function validate()
	{
		var old_pass = document.getElementById("old_pass").value;
		if(document.getElementById("old_pass").value.search(/\S/) == -1)
		{
			alert("Enter old password");
			return false;
		}
		
		var new_pass = document.getElementById("new_pass").value;
		if(document.getElementById("new_pass").value.search(/\S/) == -1)
		{
			alert("Enter new password");
			return false;
		}
		
		var confirm_new_pass = document.getElementById("confirm_new_pass").value;
		if(document.getElementById("confirm_new_pass").value.search(/\S/) == -1)
		{
			alert("Confirm new password");
			return false;
		}
		
		if(new_pass != confirm_new_pass)
		{
			alert("Confirm password doesnot match new password");
			return false;
		}
		
		if(document.getElementById("cancel").value || document.getElementById("new_pass").value == '')
		return true;
	}
	</script>
<?php
}
?>

<?php
function postdata()
{
	if($_POST['submit'] == 'Change')
	{
		$sql_update_access = "UPDATE changepassword SET newpassword = '$_POST[new_pass]', oldpassword = '$_POST[new_pass]', status = '$_POST[status]' WHERE emp_code = '$_POST[emp_code]' AND oldpassword = '$_POST[old_pass]'";
		if(mysql_query($sql_update_access))
			{
				main();
				echo "<font color='#00CC33'><strong>Access successfully updated</strong></font>";
			}
		
	}
	else 
	{
		main();
		echo "<font color='#FF0000'><strong>Data update failure</strong></font>";
	}
	
	
}
?>
</center>


