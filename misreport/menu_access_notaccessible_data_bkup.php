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
  	<td class="TDHEAD" colspan="7" align="center">Not Accessible Menu</td>
  </tr>
  <tr align="center" class="TDHEAD_SUB">
  	<td>Serial</td>
    <td>Employee Name</td>
    <td>DNS Emp Code</td>
    <td>Designation</td>
    <td>Not Accessible Menu</td>
    <td>Update</td>
  </tr>
<?php
if(strtoupper($_SESSION['admin_login'])=='ADMIN')  $vertical_condition='';
if((strtoupper($_SESSION['admin_login'])=='E0042' || strtoupper($_SESSION['admin_login'])=='E0076' 
|| strtoupper($_SESSION['admin_login'])=='GMHBC' || strtoupper($_SESSION['admin_login'])=='GMSFATS' || strtoupper($_SESSION['admin_login'])=='SFATS')){
	if($_GET){
		$vertical_condition=" AND vertical_value='".$_SESSION['vertical_value']."'";
	}
	else
	{
		$vertical_condition=" WHERE vertical_value='".$_SESSION['vertical_value']."'";
	}
}
if(strtoupper($_SESSION['admin_login'])=='ADMIN' &&  strtoupper($_SESSION['nick_name'])!='STAR' && strtoupper($_SESSION['nick_name'])!='START'){	
if($_GET)	$condition = " WHERE designation LIKE '%$_GET[search_designation_name]%' ";
else	$condition = "";
}
else	$condition = "";
	
$count = 1;
$not_accessible_menu_array = array();
$sql_distinct_not_accessible_menu = "SELECT DISTINCT not_accessible_menu FROM menu_access ORDER BY not_accessible_menu ASC";
$res_distinct_not_accessible_menu = mysqli_query($link,$sql_distinct_not_accessible_menu);
while($row_distinct_not_accessible_menu = mysqli_fetch_assoc($res_distinct_not_accessible_menu))
{
	array_push($not_accessible_menu_array,$row_distinct_not_accessible_menu['not_accessible_menu']);
}


//$not_accessible_menu_array = array("product_promotion","market_feedback","activity_report","sauda","sauda_allocation_app");

/*------------------> Select details from employee_master table with optional condition <--------*/
$sql_select_emp = "SELECT * FROM employee_master".$condition.$vertical_condition.$employee_condition." ORDER BY emp_name ASC";
$res_select_emp = mysqli_query($link,$sql_select_emp);
while($row_select_emp = mysqli_fetch_assoc($res_select_emp))
{
	$menu_checked = array();
	$menu_not_checked = array();
	
	$emp_code = $row_select_emp['emp_code'];
	$dns_emp_code = $row_select_emp['dns_emp_code'];
	$emp_name = $row_select_emp['emp_name'];
	$designation = $row_select_emp['designation'];
	
	$sql_not_accessible_menu = "SELECT not_accessible_menu FROM menu_access WHERE emp_code = '".$emp_code."' ORDER BY not_accessible_menu ASC";
	$res_not_accessible_menu = mysqli_query($link,$sql_not_accessible_menu);
	while($row_not_accessible_menu = mysqli_fetch_assoc($res_not_accessible_menu))
	{
		$checked_menu = $row_not_accessible_menu['not_accessible_menu'];
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
if(strtoupper($_SESSION['nick_name'])=='ASL')
{
	$sql_select_emp = "SELECT * FROM customer_master WHERE customer_code IN(SELECT emp_code FROM changepassword) ORDER BY customer_name ASC";
$res_select_emp = mysqli_query($link,$sql_select_emp);
while($row_select_emp = mysqli_fetch_assoc($res_select_emp))
{
	$menu_checked = array();
	$menu_not_checked = array();
	
	$emp_code = $row_select_emp['customer_code'];
	$emp_code_fun=str_replace('/','_',$emp_code);
	$dns_emp_code = $row_select_emp['dns_customer_code'];
	$emp_name = $row_select_emp['customer_name'];
	$designation = '';
	
	$sql_not_accessible_menu = "SELECT not_accessible_menu FROM menu_access WHERE emp_code = '".$emp_code."' ORDER BY not_accessible_menu ASC";
	$res_not_accessible_menu = mysqli_query($link,$sql_not_accessible_menu);
	while($row_not_accessible_menu = mysqli_fetch_assoc($res_not_accessible_menu))
	{
		$checked_menu = $row_not_accessible_menu['not_accessible_menu'];
		$menu_checked[] = $checked_menu;
	}
	$menu_not_checked = array_diff($not_accessible_menu_array,$menu_checked);
	
	foreach($menu_checked as $menu_checked_value)
	{
		$string .= strtoupper(str_replace("_"," ",$menu_checked_value)).":"."<input name=\"menu_checked[]\" type=\"checkbox\" value=\"$menu_checked_value\" class=\"input_chk_".$emp_code_fun."\" checked />&nbsp;&nbsp;";
	}
	
	foreach($menu_not_checked as $menu_not_checked_value)
	{
		$string .= strtoupper(str_replace("_"," ",$menu_not_checked_value)).":"."<input name=\"menu_checked[]\" type=\"checkbox\" value=\"$menu_not_checked_value\" class=\"input_chk_".$emp_code_fun."\"  />&nbsp;&nbsp;";
	}
	
	echo "<tr>
			<td>".$count."</td>
			<td>".$emp_name."</td>
			<td>".$dns_emp_code."</td>
			<td>".$designation."</td>
			<td>".$string."</td>
			<td><input name=\"submit\" type=\"button\" value=\"UPDATE\" onclick=\"update_data_customer('$emp_code_fun');\" /></td>
		  </tr>";
	$string = '';
	$count++;
}
//For Broker
	$sql_select_emp = "SELECT * FROM broker_master WHERE broker_id IN(SELECT emp_code FROM changepassword) ORDER BY broker_name ASC";
$res_select_emp = mysqli_query($link,$sql_select_emp);
while($row_select_emp = mysqli_fetch_assoc($res_select_emp))
{
	$menu_checked = array();
	$menu_not_checked = array();
	
	$emp_code = $row_select_emp['broker_id'];
	$emp_code_fun=str_replace('/','_',$emp_code);
	$dns_emp_code = $row_select_emp['dns_broker_id'];
	$emp_name = $row_select_emp['broker_name'];
	$designation = '';
	
	$sql_not_accessible_menu = "SELECT not_accessible_menu FROM menu_access WHERE emp_code = '".$emp_code."' ORDER BY not_accessible_menu ASC";
	$res_not_accessible_menu = mysqli_query($link,$sql_not_accessible_menu);
	while($row_not_accessible_menu = mysqli_fetch_assoc($res_not_accessible_menu))
	{
		$checked_menu = $row_not_accessible_menu['not_accessible_menu'];
		$menu_checked[] = $checked_menu;
	}
	$menu_not_checked = array_diff($not_accessible_menu_array,$menu_checked);
	
	foreach($menu_checked as $menu_checked_value)
	{
		$string .= strtoupper(str_replace("_"," ",$menu_checked_value)).":"."<input name=\"menu_checked[]\" type=\"checkbox\" value=\"$menu_checked_value\" class=\"input_chk_".$emp_code_fun."\" checked />&nbsp;&nbsp;";
	}
	
	foreach($menu_not_checked as $menu_not_checked_value)
	{
		$string .= strtoupper(str_replace("_"," ",$menu_not_checked_value)).":"."<input name=\"menu_checked[]\" type=\"checkbox\" value=\"$menu_not_checked_value\" class=\"input_chk_".$emp_code_fun."\"  />&nbsp;&nbsp;";
	}
	
	echo "<tr>
			<td>".$count."</td>
			<td>".$emp_name."</td>
			<td>".$dns_emp_code."</td>
			<td>".$designation."</td>
			<td>".$string."</td>
			<td><input name=\"submit\" type=\"button\" value=\"UPDATE\" onclick=\"update_data_broker('$emp_code_fun');\" /></td>
		  </tr>";
	$string = '';
	$count++;
}

}
mysqli_close($link);
?>
</table>
</center>