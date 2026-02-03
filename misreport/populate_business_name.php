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

$sql_select_business_name = "SELECT LO.trans_id, SO.value FROM location LO, employee_master EM, survey_input SI, survey_output SO WHERE LO.trans_id = SO.survey_id AND SO.row_id = SI.row_id AND LO.emp_code = EM.emp_code AND (DATE_FORMAT(SUBSTRING(LO.date,1,10),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."') GROUP BY SO.survey_id ORDER BY SO.value ASC";
$res_select_business_name = mysqli_query($link,$sql_select_business_name);
while($row_select_business_name = mysqli_fetch_assoc($res_select_business_name))
{
	$survey_id = $row_select_business_name['trans_id'];
	$business_name = $row_select_business_name['value'];
	echo "<option value='".$survey_id."'>".$business_name."</option>";
}
mysqli_close($link);
?>