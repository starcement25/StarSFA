<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");
?>

<?php
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

echo "<option value=\"\">Select</option>";

$sql_select_business_name = "SELECT DISTINCT survey_id, value FROM survey_output WHERE (SUBSTRING(survey_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') AND row_id = 'RA013'";
$res_select_business_name = mysqli_query($link,$sql_select_business_name);
while($row_select_business_name = mysqli_fetch_assoc($res_select_business_name))
{
	$survey_id = $row_select_business_name['survey_id'];
	$business_name = $row_select_business_name['value'];
	echo "<option value='".$survey_id."'>".$business_name."</option>";
}
?>