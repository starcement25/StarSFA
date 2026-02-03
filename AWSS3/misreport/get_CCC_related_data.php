<?php
ob_start();
session_start();
require("adminUtils.php");

/*--------> Employee Hierarchy Condition <--------*/
if($_SESSION['admin_login']=="admin"){
	$emp_hierarchy = '';
	$emp_hierarchy_condition = '';
	$emp_hierarchy_condition_one='';
}
else{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition = " WHERE emp_code IN(".$emp_hierarchy.") ";
	$emp_hierarchy_condition_one = " AND SUBSTRING(survey_id,3,5) IN(".$emp_hierarchy.") ";
}

$zone = $_REQUEST['zone'];
$start_date = $_REQUEST['start_date'];
$start_date = date('Y-m-d',strtotime($_REQUEST['start_date']));
$end_date = $_REQUEST['end_date'];
$end_date  = date('Y-m-d',strtotime($_REQUEST['end_date']));
$type = $_REQUEST['type'];
if(isset($_REQUEST['division_id']))
{
	$division_id = $_REQUEST['division_id'];
}
if(isset($_REQUEST['region']))
{
	$region = $_REQUEST['region'];
}

if($zone != ''){
	if($zone == 'all')
		$zone_condition = "";
	else
		$zone_condition = " AND zone IN(".$zone.") ";
}
if($division_id != ''){
	if($division_id == 'all')
		$division_condition = "";
	else
		$division_condition = " AND division IN(".$division_id.") ";
}
if($region != ''){
	if($region == 'all')
		$region_condition = "";
	else
		$region_condition = " AND region IN(".$region.") ";
}

	if(isset($_REQUEST['division_id']) || isset($_REQUEST['region']))
	{
		$sql_CCC = "SELECT DISTINCT CCC FROM kiosk_transaction_details WHERE type='".$type."' AND DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y%-%m-%d') <='".$end_date."' 
					AND DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y%-%m-%d') >='".$start_date."'".$emp_hierarchy_condition_one.$zone_condition.
					$division_condition.$region_condition;
		$res_CCC = mysql_query($sql_CCC);
		$count_CCC=mysql_num_rows($res_CCC);
		if($count_CCC >0)
		{
			$CCC_string='';
			echo "<select name=\"CCC\" id=\"CCC\">";
			echo "<option value=\"\">Select</option>";
			echo "<option value=\"all\">All</option>";
			while($row_CCC = mysql_fetch_array($res_CCC)){
				//$division_id = $row_division['division_id'];
				$CCC = $row_CCC['CCC'];
				echo "<option value=\"'".$CCC."'\">".$CCC."</option>";
				$CCC_string .= "'".$CCC."',";
			}
			echo "</select>";
		}
		else
		{
			echo "<font color='#FF0000'>No CCC found</font>";
		}
	}
	else
	{
		echo "<font color='#FF0000'>No CCC found</font>";
	}
mysql_close($link);
?>