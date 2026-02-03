<?php
ob_start();
	session_start();
	require("adminUtils.php");
	
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
if(strtoupper($_SESSION['admin_login'])=='ADMIN' && strtoupper($_SESSION['nick_name'])=='STAR'){	
	$employee = $_REQUEST['employee'];
	$employee_condition = " WHERE emp_code IN(".$employee.") ";
}
else
{
	$employee_condition = "";
}
?><head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
</head>

<center>
<table class="border" border="1" width="100%" style="border-collapse:collapse;" cellpadding="6px">
 
  <tr>
  	<td class="TDHEAD" colspan="7" align="center">OTP Menu Access</td>
  </tr>
  <tr align="center" class="TDHEAD_SUB">
  	<td>Serial</td>
    <td>Employee Name</td>
    <td>DNS Emp Code</td>
    <td>Designation</td>
    <td>OTP Menu</td>
    <td>Update</td>
  </tr>
<?php
	
$count = 1;
$not_accessible_menu_array = array();
$sql_distinct_not_accessible_menu = "SELECT DISTINCT accessible_menu FROM OTP_menu_access ORDER BY accessible_menu ASC";
$res_distinct_not_accessible_menu = mysql_query($sql_distinct_not_accessible_menu);
while($row_distinct_not_accessible_menu = mysql_fetch_array($res_distinct_not_accessible_menu))
{
	array_push($not_accessible_menu_array,$row_distinct_not_accessible_menu['accessible_menu']);
}


//$not_accessible_menu_array = array("product_promotion","market_feedback","activity_report","sauda","sauda_allocation_app");

/*------------------> Select details from employee_master table with optional condition <--------*/
$sql_select_emp = "SELECT * FROM employee_master ORDER BY emp_name ASC";
$res_select_emp = mysql_query($sql_select_emp);
while($row_select_emp = mysql_fetch_array($res_select_emp))
{
	$menu_checked = array();
	$menu_not_checked = array();
	
	$emp_code = $row_select_emp['emp_code'];
	$dns_emp_code = $row_select_emp['dns_emp_code'];
	$emp_name = $row_select_emp['emp_name'];
	$designation = $row_select_emp['designation'];
	
	$sql_not_accessible_menu = "SELECT accessible_menu FROM OTP_menu_access WHERE emp_code = '".$emp_code."' ORDER BY accessible_menu ASC";
	$res_not_accessible_menu = mysql_query($sql_not_accessible_menu);
	while($row_not_accessible_menu = mysql_fetch_array($res_not_accessible_menu))
	{
		$checked_menu = $row_not_accessible_menu['accessible_menu'];
		$menu_checked[] = $checked_menu;
	}
	$menu_not_checked = array_diff($not_accessible_menu_array,$menu_checked);
	
	foreach($menu_checked as $menu_checked_value)
	{
		$string .= strtoupper(str_replace("_"," ",$menu_checked_value)).":"."<input name=\"menu_checked[]\" type=\"checkbox\" value=\"$menu_checked_value\" class=\"input_chk_".$emp_code."\" checked />&nbsp;&nbsp;";
	}
	
	foreach($menu_not_checked as $menu_not_checked_value)
	{
		$string .= strtoupper(str_replace("_"," ",$menu_not_checked_value)).":"."<input name=\"menu_checked[]\" type=\"checkbox\" value=\"$menu_not_checked_value\" class=\"input_chk_".$emp_code."\"  />&nbsp;&nbsp;";
	}
	
	echo "<tr>
			<td>".$count."</td>
			<td>".$emp_name."</td>
			<td>".$dns_emp_code."</td>
			<td>".$designation."</td>
			<td>".$string."</td>
			<td><input name=\"submit\" type=\"button\" value=\"UPDATE\" onclick=\"update_data('$emp_code');\" /></td>
		  </tr>";
	$string = '';
	$count++;
}
mysql_close($link);
?>
</table>
</center>