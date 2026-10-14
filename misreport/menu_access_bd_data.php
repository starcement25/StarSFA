<?php
	session_start();
require_once("include/config.php");
require_once("include/config-setup.php");
require_once("include/dbcon.php");
require_once("menu_access_bd_schema.php");

	if($_SESSION['admin_login']=="")  		header("location:index.php");

/* The page has no employee search: every BD employee is always listed. */
?>
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
</head>

<center>
<table class="border" border="1" width="100%" style="border-collapse:collapse;" cellpadding="6px">
 
  <tr>
  	<td class="TDHEAD" colspan="7" align="center">BD Menu Permission (ticked = not permitted)</td>
  </tr>
  <tr align="center" class="TDHEAD_SUB">
  	<td>Serial</td>
    <td>Employee Name</td>
    <td>DNS Emp Code</td>
    <td>Designation</td>
    <td>Not Permitted BD Menu</td>
    <td>Update</td>
  </tr>
<?php
/*------------------> Only BD employees can hold BD menu permissions <--------*/
$where_condition_array = array("sale_access = 'BD'");

if((strtoupper($_SESSION['admin_login'])=='E0042' || strtoupper($_SESSION['admin_login'])=='E0076'
|| strtoupper($_SESSION['admin_login'])=='GMHBC' || strtoupper($_SESSION['admin_login'])=='GMSFATS' || strtoupper($_SESSION['admin_login'])=='SFATS')){
	$where_condition_array[] = "vertical_value = '".mysqli_real_escape_string($link, $_SESSION['vertical_value'])."'";
}

$where_condition = " WHERE ".implode(" AND ", $where_condition_array);

$count = 1;
$bd_menu_array = array();

ensure_menu_access_bd_schema($link);

// 1. Get all survey types
$sql_types = "SELECT DISTINCT survey_sub_menu FROM survey_input_mle WHERE survey_sub_menu != '' ORDER BY survey_sub_menu ASC";
$res_types = mysqli_query($link, $sql_types);
while ($row_types = mysqli_fetch_assoc($res_types)) {
    $sub_menu = $row_types['survey_sub_menu'];
    $bd_menu_array[$sub_menu] = $sub_menu;
}

// 2. Get all technical meet types
$sql_tech = "SELECT display_name, row_id FROM survey_input_mle WHERE type='menu' and survey_sub_menu='Technical Meets' ORDER BY display_order ASC";
$res_tech = mysqli_query($link, $sql_tech);
while ($row_tech = mysqli_fetch_assoc($res_tech)) {
    $tech_name = $row_tech['display_name'];
    $row_id = $row_tech['row_id'];
    if ($tech_name != '') {
        $bd_menu_array["Tech_Meet_" . $row_id] = "Technical Meet - " . $tech_name;
    }
}

/*------------------> Select details from employee_master table <--------*/
$sql_select_emp = "SELECT * FROM employee_master".$where_condition." ORDER BY emp_name ASC";
$res_select_emp = mysqli_query($link,$sql_select_emp);
while($row_select_emp = mysqli_fetch_assoc($res_select_emp))
{
	$emp_code = $row_select_emp['emp_code'];
	$dns_emp_code = $row_select_emp['dns_emp_code'];
	$emp_name = $row_select_emp['emp_name'];
	$designation = $row_select_emp['designation'];

	$menu_checked = get_restricted_bd_menus($link,$emp_code);

	$string = '';
	foreach ($bd_menu_array as $key => $display_name) {
		$checked = bd_menu_is_restricted($key, $menu_checked) ? "checked" : "";
		$string .= htmlspecialchars($display_name) . ": " .
		           "<input name=\"input_chk_" . $emp_code . "\" type=\"checkbox\" value=\"" . htmlspecialchars($key) . "\" " . $checked . " />&nbsp;&nbsp;&nbsp;&nbsp;";
	}
	
	echo "<tr>
			<td>".$count."</td>
			<td>".$emp_name."</td>
			<td>".$dns_emp_code."</td>
			<td>".$designation."</td>
			<td>".$string."</td>
			<td><input name=\"submit\" type=\"button\" value=\"UPDATE\" onclick=\"update_data('$emp_code');\"/></td>
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
		$emp_code = $row_select_emp['customer_code'];
		$emp_code_fun=str_replace('/','_',$emp_code);
		$dns_emp_code = $row_select_emp['dns_customer_code'];
		$emp_name = $row_select_emp['customer_name'];
		$designation = '';

		$menu_checked = get_restricted_bd_menus($link,$emp_code);

		$string = '';
		foreach ($bd_menu_array as $key => $display_name) {
			$checked = bd_menu_is_restricted($key, $menu_checked) ? "checked" : "";
			$string .= htmlspecialchars($display_name) . ": " . 
			           "<input name=\"menu_checked[]\" type=\"checkbox\" value=\"" . htmlspecialchars($key) . "\" class=\"input_chk_".$emp_code_fun."\" " . $checked . " />&nbsp;&nbsp;&nbsp;&nbsp;";
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
		$emp_code = $row_select_emp['broker_id'];
		$emp_code_fun=str_replace('/','_',$emp_code);
		$dns_emp_code = $row_select_emp['dns_broker_id'];
		$emp_name = $row_select_emp['broker_name'];
		$designation = '';

		$menu_checked = get_restricted_bd_menus($link,$emp_code);

		$string = '';
		foreach ($bd_menu_array as $key => $display_name) {
			$checked = bd_menu_is_restricted($key, $menu_checked) ? "checked" : "";
			$string .= htmlspecialchars($display_name) . ": " . 
			           "<input name=\"menu_checked[]\" type=\"checkbox\" value=\"" . htmlspecialchars($key) . "\" class=\"input_chk_".$emp_code_fun."\" " . $checked . " />&nbsp;&nbsp;&nbsp;&nbsp;";
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
}
mysqli_close($link);
?>
</table>
</center>
