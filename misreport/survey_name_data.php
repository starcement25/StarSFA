<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

if($_REQUEST['type'] == 'today')
{
	$today = date('Y-m-d');
	$today1 = str_replace("-","",$today);
	$condition = "SUBSTRING(LO.date,1,10) LIKE '%".$today."%'";
	$output_condition = "SUBSTRING(survey_id,-14,8) LIKE '%".$today1."%'";
}
else if($_REQUEST['type'] == 'mtd')
{
	@$current_date = date('Y-m-d');
	$month = explode("-",$current_date);
	$year = $month[0];
	$month = $month[1];
	$condition = "SUBSTRING(LO.date,1,4) =$year AND SUBSTRING(LO.date,6,2) =$month";
	$output_condition = "SUBSTRING(survey_id,-14,4)=$year AND SUBSTRING(survey_id,-10,2) =$month";
}
else if($_REQUEST['type'] == 'custom')
{
	$start_date = str_replace("-","",$_GET['start_date']);
	$strt = date('d-m-Y',strtotime($start_date));
	$end_date = str_replace("-","",$_GET['end_date']);
	$endt = date('d-m-Y',strtotime($end_date));
	$condition = "(SUBSTRING(LO.date,1,10) BETWEEN '".$_GET['start_date']."' AND '".$_GET['end_date']."')";
	$output_condition = "(SUBSTRING(survey_id,-14,8) BETWEEN '".$start_date."' AND '".$end_date."')";
}
else
{
	$today = date('Y-m-d');
	$today1 = str_replace("-","",$today);
	$condition = "SUBSTRING(SO.survey_id,-14,8) LIKE '%".$today1."%'";
	$output_condition = "SUBSTRING(survey_id,-14,8) LIKE '%".$today1."%'";
}

if($_GET['emp_code'] != '')
{
	if($_GET['emp_code'] == 'all')
	{
		$condition_emp = '';
		$condition_one_emp = '';
	}
	else
	{
		$condition_emp = " AND SUBSTRING(LO.trans_id,3,5) LIKE '%".$_GET['emp_code']."%' ";
		$condition_one_emp = " AND SUBSTRING(survey_id,3,5) LIKE '%".$_GET['emp_code']."%'";
	}
}

$count = 1;
$sql_menu_id = "SELECT menu_id,layout_name,survey_type FROM survey_input WHERE type='menu' AND menu_id  IN(SELECT distinct  SI.menu_id FROM survey_input SI,survey_output SO,location LO WHERE SI.row_id=SO.row_id AND LO.trans_id=SO.survey_id ".$condition_emp." AND ".$condition.") ORDER BY display_order ASC";
$res_menu_id = mysqli_query($link,$sql_menu_id);
$total_row = mysqli_num_rows($res_menu_id);

if($total_row>0)
{
	echo "<table border=\"1\" class=\"border\" style=\"border-collapse:collapse;\" width=\"50%\">
			<tr class=\"TDHEAD\">
				<td colspan=\"4\" align=\"center\">Survey Details</td>
			</tr>
			<tr class=\"TDHEAD_SUB\" align=\"center\">
				<td>Count</td>
				<td>Type</td>
				<td>Survey Name</td>
				<td>No of Survey</td>
			</tr>";
	$res_menu_id = mysqli_query($link,$sql_menu_id);
	while($row_menu_id = mysqli_fetch_assoc($res_menu_id))
	{
		$menu_id = $row_menu_id['menu_id'];
		$layout_name = $row_menu_id['layout_name'];
		$survey_type = ucfirst($row_menu_id['survey_type']);
		
		$sql_distinct_survey = "SELECT COUNT(DISTINCT survey_id) as survey_count  FROM survey_output WHERE ".$output_condition.$condition_one_emp." AND row_id IN(SELECT DISTINCT row_id FROM survey_input WHERE menu_id='".$menu_id."')";
		$res_distint_survey = mysqli_query($link,$sql_distinct_survey);
		while($row_distinct_survey = mysqli_fetch_assoc($res_distint_survey))
		{
			$survey_count = $row_distinct_survey['survey_count'];
			
			echo "<tr>
					<td>".$count."</td>
					<td>".$survey_type."</td>
					<td>".$layout_name."</td>
					<td align=\"right\">".$survey_count."</td>
				  </tr>";
			$count++;
		}
	}
	echo "</table>";
}
else
{
	echo "<strong><font color='red'>No records</font></strong>";
}
mysqli_close($link);
?>